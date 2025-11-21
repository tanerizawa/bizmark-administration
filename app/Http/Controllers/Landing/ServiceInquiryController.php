<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\ServiceInquiry;
use App\Services\FreeAIAnalysisService;
use App\Services\ServiceInquiryRateLimiter;
use App\Jobs\AnalyzeServiceInquiryJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ServiceInquiryController extends Controller
{
    public function __construct(
        private FreeAIAnalysisService $aiService,
        private ServiceInquiryRateLimiter $rateLimiter
    ) {}

    /**
     * Show inquiry form
     */
    public function create()
    {
        return view('landing.service-inquiry.create', [
            'provinces' => $this->getProvinces(),
        ]);
    }

    /**
     * Store inquiry and trigger AI analysis
     */
    public function store(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            // Step 1: Contact & Company
            'email' => 'required|email|max:255',
            'company_name' => 'required|string|max:255',
            'company_type' => 'nullable|string|max:100',
            'phone' => 'required|string|max:50',
            'contact_person' => 'required|string|max:255',
            'position' => 'nullable|string|max:100',
            
            // Step 2: Business Info
            'business_activity' => 'required|string|max:1000',
            'kbli_code' => 'nullable|string|max:10',
            'business_scale' => 'required|in:micro,small,medium,large',
            'location_province' => 'required|string|max:100',
            'location_city' => 'required|string|max:100',
            'location_category' => 'required|in:industrial,commercial,residential,rural',
            'estimated_investment' => 'required|in:under_100m,100m_500m,500m_2b,over_2b',
            'timeline' => 'nullable|in:urgent,1-3_months,3-6_months,6plus_months,not_sure',
            'additional_notes' => 'nullable|string|max:2000',
            
            // UTM tracking
            'utm_source' => 'nullable|string|max:100',
            'utm_medium' => 'nullable|string|max:100',
            'utm_campaign' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $email = strtolower($data['email']);
        $ip = $request->ip();

        // Check rate limit
        $limitCheck = $this->rateLimiter->check($email, $ip);
        if (!$limitCheck['allowed']) {
            return response()->json([
                'success' => false,
                'error' => 'rate_limit',
                'message' => $limitCheck['message'],
                'retry_after' => $limitCheck['retry_after'],
                'retry_after_formatted' => $limitCheck['retry_after_formatted']
            ], 429);
        }

        try {
            // Create inquiry record
            $inquiry = ServiceInquiry::create([
                'email' => $email,
                'company_name' => $data['company_name'],
                'company_type' => $data['company_type'] ?? null,
                'phone' => $data['phone'],
                'contact_person' => $data['contact_person'],
                'position' => $data['position'] ?? null,
                'business_activity' => $data['business_activity'],
                'kbli_code' => $data['kbli_code'] ?? null,
                'form_data' => [
                    'business_scale' => $data['business_scale'],
                    'location_province' => $data['location_province'],
                    'location_city' => $data['location_city'],
                    'location_category' => $data['location_category'],
                    'estimated_investment' => $data['estimated_investment'],
                    'timeline' => $data['timeline'] ?? null,
                    'additional_notes' => $data['additional_notes'] ?? null,
                ],
                'status' => 'processing',
                'source' => 'landing_page',
                'utm_source' => $data['utm_source'] ?? null,
                'utm_medium' => $data['utm_medium'] ?? null,
                'utm_campaign' => $data['utm_campaign'] ?? null,
                'ip_address' => $ip,
                'user_agent' => $request->userAgent(),
                'session_id' => $request->session()->getId(),
            ]);

            // Increment rate limiter
            $this->rateLimiter->increment($email, $ip);

            // Dispatch async job for AI analysis
            AnalyzeServiceInquiryJob::dispatch($inquiry->id);

            Log::info('ServiceInquiry created', [
                'inquiry_number' => $inquiry->inquiry_number,
                'email' => $email,
                'company' => $data['company_name']
            ]);

            return response()->json([
                'success' => true,
                'inquiry_number' => $inquiry->inquiry_number,
                'message' => 'Inquiry berhasil dikirim. AI sedang menganalisis...',
                'remaining_today' => $limitCheck['remaining_today'] ?? 0
            ]);

        } catch (\Exception $e) {
            Log::error('ServiceInquiry creation failed', [
                'error' => $e->getMessage(),
                'email' => $email
            ]);

            return response()->json([
                'success' => false,
                'error' => 'server_error',
                'message' => 'Terjadi kesalahan. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Get inquiry result by number
     */
    public function show(string $inquiryNumber)
    {
        $inquiry = ServiceInquiry::where('inquiry_number', $inquiryNumber)->first();

        if (!$inquiry) {
            return response()->json([
                'success' => false,
                'error' => 'not_found',
                'message' => 'Inquiry tidak ditemukan'
            ], 404);
        }

        // Check if still processing
        if ($inquiry->status === 'processing' && !$inquiry->ai_analysis) {
            return response()->json([
                'success' => false,
                'status' => 'processing',
                'message' => 'AI masih memproses analisis Anda. Mohon tunggu...'
            ]);
        }

        // Return result
        return response()->json([
            'success' => true,
            'status' => 'completed',
            'inquiry' => [
                'inquiry_number' => $inquiry->inquiry_number,
                'company_name' => $inquiry->company_name,
                'email' => $inquiry->email,
                'business_activity' => $inquiry->business_activity,
                'created_at' => $inquiry->created_at->toIso8601String(),
            ],
            'analysis' => $inquiry->ai_analysis,
            'processing_time' => $inquiry->ai_processing_time,
        ]);
    }

    /**
     * Show result page
     */
    public function result(string $inquiryNumber)
    {
        $inquiry = ServiceInquiry::where('inquiry_number', $inquiryNumber)->firstOrFail();

        return view('landing.service-inquiry.result', [
            'inquiry' => $inquiry
        ]);
    }

    /**
     * Check rate limit status
     */
    public function checkRateLimit(Request $request)
    {
        $email = strtolower($request->input('email'));
        $ip = $request->ip();

        $stats = $this->rateLimiter->getStats($email, $ip);
        $limitCheck = $this->rateLimiter->check($email, $ip);

        return response()->json([
            'allowed' => $limitCheck['allowed'],
            'stats' => $stats,
            'limit_info' => $limitCheck
        ]);
    }

    /**
     * Get provinces list
     */
    private function getProvinces(): array
    {
        return [
            'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Kepulauan Riau',
            'Jambi', 'Sumatera Selatan', 'Kepulauan Bangka Belitung', 'Bengkulu', 'Lampung',
            'DKI Jakarta', 'Jawa Barat', 'Banten', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur',
            'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur',
            'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara',
            'Sulawesi Utara', 'Gorontalo', 'Sulawesi Tengah', 'Sulawesi Barat', 'Sulawesi Selatan', 'Sulawesi Tenggara',
            'Maluku', 'Maluku Utara', 'Papua', 'Papua Barat', 'Papua Tengah', 'Papua Pegunungan', 'Papua Selatan', 'Papua Barat Daya'
        ];
    }
}

