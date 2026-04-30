<?php

namespace App\Http\Controllers;

use App\Mail\ServiceCostRequestAdminNotificationMail;
use App\Mail\ServiceCostRequestUserConfirmationMail;
use App\Models\ServiceCostRequest;
use App\Services\OpenRouterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ServiceCostRequestController extends Controller
{
    public function __construct(private OpenRouterService $openRouterService) {}

    /**
     * Display the service cost request form
     */
    public function index()
    {
        $serviceCategories = ServiceCostRequest::getServiceCategories();
        $servicesByCategory = ServiceCostRequest::getServicesByCategory();

        return view('permohonan.index', compact('serviceCategories', 'servicesByCategory'));
    }

    /**
     * Store a new service cost request
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->getValidationRules($request->input('applicant_type')), $this->getValidationMessages());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $validator->validated();

            // Process services requested
            $servicesRequested = $request->input('services_requested', []);
            if (is_string($servicesRequested)) {
                $servicesRequested = json_decode($servicesRequested, true) ?: [];
            }
            $data['services_requested'] = $servicesRequested;

            // Add metadata
            $data['ip_address'] = $request->ip();
            $data['user_agent'] = $request->userAgent();
            $data['source'] = 'website';
            $data['status'] = 'pending';

            // Handle file uploads if any
            if ($request->hasFile('documents')) {
                $documents = [];
                foreach ($request->file('documents') as $file) {
                    $path = $file->store('service-cost-requests/'.date('Y/m'), 'public');
                    $documents[] = [
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType(),
                    ];
                }
                $data['documents'] = $documents;
            }

            $serviceRequest = ServiceCostRequest::create($data);

            // Log the request
            Log::info('Service cost request created', [
                'request_number' => $serviceRequest->request_number,
                'applicant_type' => $serviceRequest->applicant_type,
                'email' => $serviceRequest->email,
            ]);

            // Send confirmation email to requester (non-blocking)
            try {
                Mail::to($serviceRequest->email)
                    ->send(new ServiceCostRequestUserConfirmationMail($serviceRequest));
            } catch (\Throwable $mailException) {
                Log::warning('Failed to send requester confirmation email for service cost request', [
                    'request_number' => $serviceRequest->request_number,
                    'email' => $serviceRequest->email,
                    'error' => $mailException->getMessage(),
                ]);
            }

            // Send notification email to internal admin inbox (non-blocking)
            try {
                $adminEmail = config('mail.admin_address')
                    ?: config('landing_metrics.contact.email')
                    ?: config('mail.from.address');

                if (! empty($adminEmail)) {
                    Mail::to($adminEmail)
                        ->send(new ServiceCostRequestAdminNotificationMail($serviceRequest));
                }
            } catch (\Throwable $mailException) {
                Log::warning('Failed to send admin notification email for service cost request', [
                    'request_number' => $serviceRequest->request_number,
                    'error' => $mailException->getMessage(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Permohonan berhasil dikirim!',
                'request_number' => $serviceRequest->request_number,
                'redirect_url' => route('permohonan.result', $serviceRequest->request_number),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create service cost request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.',
            ], 500);
        }
    }

    /**
     * Display the result page after submission
     */
    public function result(string $requestNumber)
    {
        $serviceRequest = ServiceCostRequest::where('request_number', $requestNumber)->firstOrFail();

        return view('permohonan.result', compact('serviceRequest'));
    }

    /**
     * Check request status via API
     */
    public function checkStatus(string $requestNumber)
    {
        $serviceRequest = ServiceCostRequest::where('request_number', $requestNumber)
            ->select(['request_number', 'status', 'quoted_price', 'quoted_at', 'created_at'])
            ->first();

        if (! $serviceRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Permohonan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'request_number' => $serviceRequest->request_number,
                'status' => $serviceRequest->status,
                'status_label' => $serviceRequest->status_label,
                'quoted_price' => $serviceRequest->formatted_quoted_price,
                'quoted_at' => $serviceRequest->quoted_at?->format('d M Y H:i'),
                'created_at' => $serviceRequest->created_at->format('d M Y H:i'),
            ],
        ]);
    }

    /**
     * Generate AI-assisted official request letter draft.
     */
    public function generateLetterDraft(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'applicant_type' => 'required|in:perorangan,badan',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'service_category' => 'required|string|max:50',
            'services_requested' => 'required',
            'project_description' => 'nullable|string|max:2000',
            'project_location' => 'nullable|string|max:255',
            'estimated_budget' => 'nullable|numeric|min:0',
            'timeline_expectation' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:255',
            'business_type' => 'nullable|in:pt,cv,ud,yayasan,koperasi,lainnya',
            'business_sector' => 'nullable|string|max:100',
            'pic_name' => 'nullable|string|max:255',
            'pic_position' => 'nullable|string|max:100',
            'nib' => 'nullable|string|max:30',
            'npwp' => 'nullable|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Data belum lengkap untuk generate surat AI.',
            ], 422);
        }

        $payload = $validator->validated();
        $servicesRequested = $request->input('services_requested', []);
        if (is_string($servicesRequested)) {
            $servicesRequested = json_decode($servicesRequested, true) ?: [];
        }
        $payload['services_requested'] = is_array($servicesRequested) ? $servicesRequested : [];

        $serviceCategories = ServiceCostRequest::getServiceCategories();
        $servicesByCategory = ServiceCostRequest::getServicesByCategory();
        $serviceCategoryLabel = $serviceCategories[$payload['service_category']] ?? $payload['service_category'];

        $serviceLabels = [];
        foreach ($payload['services_requested'] as $serviceKey) {
            $serviceLabels[] = $servicesByCategory[$payload['service_category']][$serviceKey] ?? $serviceKey;
        }

        $systemPrompt = "Anda adalah konsultan legal bisnis Indonesia senior. Tugas Anda menulis surat resmi Bahasa Indonesia yang formal, natural, jelas, dan meyakinkan. Hindari bahasa robotik. Jangan gunakan markdown.\n\nOutput WAJIB JSON valid dengan struktur:\n{\n  \"ai_letter_body\": \"teks surat lengkap setelah kalimat 'Dengan hormat,' hingga sebelum penutup 'Hormat kami'\"\n}";

        $userPrompt = "Tulis draf isi surat permohonan resmi untuk penghitungan biaya jasa yang ditujukan kepada PT Cangah Pajaratan Mandiri (Bizmark.ID) dengan data berikut:\n".
            '- Jenis Pemohon: '.($payload['applicant_type'] ?? '-')."\n".
            '- Nama Pemohon/Kontak: '.($payload['name'] ?? '-')."\n".
            '- Email: '.($payload['email'] ?? '-')."\n".
            '- Telepon: '.($payload['phone'] ?? '-')."\n".
            '- Alamat: '.($payload['address'] ?? '-')."\n".
            '- Kota: '.($payload['city'] ?? '-')."\n".
            '- Provinsi: '.($payload['province'] ?? '-')."\n".
            '- Occupation (jika perorangan): '.($payload['occupation'] ?? '-')."\n".
            '- Nama Perusahaan (jika badan): '.($payload['company_name'] ?? '-')."\n".
            '- Jenis Badan (jika badan): '.($payload['business_type'] ?? '-')."\n".
            '- Bidang Usaha: '.($payload['business_sector'] ?? '-')."\n".
            '- PIC: '.($payload['pic_name'] ?? '-')."\n".
            '- Jabatan PIC: '.($payload['pic_position'] ?? '-')."\n".
            '- NIB: '.($payload['nib'] ?? '-')."\n".
            '- NPWP: '.($payload['npwp'] ?? '-')."\n".
            '- Kategori Layanan: '.$serviceCategoryLabel."\n".
            '- Layanan Dipilih: '.(empty($serviceLabels) ? '-' : implode('; ', $serviceLabels))."\n".
            '- Deskripsi Kebutuhan: '.($payload['project_description'] ?? '-')."\n".
            '- Lokasi Proyek: '.($payload['project_location'] ?? '-')."\n".
            '- Estimasi Budget: '.(! empty($payload['estimated_budget']) ? 'Rp '.number_format((float) $payload['estimated_budget'], 0, ',', '.') : '-')."\n".
            '- Ekspektasi Waktu: '.($payload['timeline_expectation'] ?? '-')."\n\n".
            "Ketentuan output isi surat:\n".
            "1. Formal dan siap kirim.\n".
            "2. Panjang 3-6 paragraf.\n".
            "3. Sertakan tujuan, kebutuhan layanan, konteks proyek, dan kontak lanjutan.\n".
            "4. Jangan buat data baru yang tidak ada pada input.\n".
            '5. Kembalikan JSON valid saja sesuai format.';

        try {
            $aiResponse = $this->openRouterService->chat([
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ], [
                'model' => config('services.openrouter.free_primary_model', 'google/gemini-2.5-flash'),
                'temperature' => 0.4,
                'max_tokens' => 1200,
            ]);

            if (! $aiResponse['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI belum dapat memproses permintaan saat ini.',
                    'error' => $aiResponse['error'] ?? 'AI request failed',
                ], 503);
            }

            $content = trim($aiResponse['content'] ?? '');
            $content = preg_replace('/^```json\s*/i', '', $content);
            $content = preg_replace('/```$/', '', $content);

            $decoded = json_decode(trim($content), true);
            if (! is_array($decoded)) {
                if (preg_match('/\{[\s\S]*\}/', $content, $matches)) {
                    $decoded = json_decode($matches[0], true);
                }
            }

            if (! is_array($decoded) || empty($decoded['ai_letter_body'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format respons AI tidak sesuai.',
                ], 422);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'ai_letter_body' => trim((string) $decoded['ai_letter_body']),
                    'model' => $aiResponse['model'] ?? null,
                    'tokens_used' => $aiResponse['tokens_used'] ?? null,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('AI letter draft generation failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat generate surat AI.',
            ], 500);
        }
    }

    /**
     * Get validation rules based on applicant type
     */
    private function getValidationRules(string $applicantType = 'perorangan'): array
    {
        $rules = [
            'applicant_type' => 'required|in:perorangan,badan',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'service_category' => 'required|string|max:50',
            'services_requested' => 'required',
            'project_description' => 'nullable|string|max:2000',
            'project_location' => 'nullable|string|max:255',
            'estimated_budget' => 'nullable|numeric|min:0',
            'timeline_expectation' => 'nullable|string|max:100',
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ];

        if ($applicantType === 'perorangan') {
            $rules['nik'] = 'nullable|string|max:20';
            $rules['occupation'] = 'nullable|string|max:100';
        } else {
            $rules['company_name'] = 'required|string|max:255';
            $rules['npwp'] = 'nullable|string|max:30';
            $rules['nib'] = 'nullable|string|max:30';
            $rules['business_type'] = 'required|in:pt,cv,ud,yayasan,koperasi,lainnya';
            $rules['business_sector'] = 'nullable|string|max:100';
            $rules['pic_name'] = 'nullable|string|max:255';
            $rules['pic_position'] = 'nullable|string|max:100';
        }

        return $rules;
    }

    /**
     * Get custom validation messages
     */
    private function getValidationMessages(): array
    {
        return [
            'applicant_type.required' => 'Pilih jenis pemohon.',
            'applicant_type.in' => 'Jenis pemohon tidak valid.',
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'service_category.required' => 'Pilih kategori layanan.',
            'services_requested.required' => 'Pilih minimal satu layanan.',
            'company_name.required' => 'Nama perusahaan wajib diisi untuk pemohon badan.',
            'business_type.required' => 'Pilih jenis badan usaha.',
            'business_type.in' => 'Jenis badan usaha tidak valid.',
            'documents.*.max' => 'Ukuran file maksimal 5MB.',
            'documents.*.mimes' => 'Format file harus PDF, DOC, DOCX, JPG, atau PNG.',
        ];
    }
}
