<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConsultRequest;
use App\Models\Kbli;
use App\Services\ConsultationPricingEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ConsultationController extends Controller
{
    protected ConsultationPricingEngine $pricingEngine;
    
    public function __construct(ConsultationPricingEngine $pricingEngine)
    {
        $this->pricingEngine = $pricingEngine;
    }
    
    /**
     * Submit free consultation request with AI cost estimation
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submit(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                // Contact information (simplified - phone only for now)
                'contact_phone' => 'required|string|max:20',
                
                // Business information (5-digit KBLI required)
                'kbli_code' => [
                    'required',
                    'string',
                    'regex:/^\d{5}$/',
                    'exists:kbli,code',
                ],
                'business_size' => 'required|in:micro,small,medium,large',
                'location' => 'required|string|max:255',
                'location_type' => 'required|in:jakarta,jabodetabek,jawa_bali,luar_jawa',
                'investment_level' => 'required|in:under_1b,1b_5b,5b_10b,above_10b',
                'employee_count' => 'nullable|integer|min:0|max:100000',
                
                // Project details (optional - AI will recommend if not provided)
                'deliverables' => 'nullable|string|max:5000',
            ], [
                'kbli_code.required' => 'KBLI code is required',
                'kbli_code.regex' => 'KBLI code must be exactly 5 digits',
                'kbli_code.exists' => 'Invalid KBLI code',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }
            
            $validated = $validator->validated();
            
            // Verify KBLI code is 5-digit and active
            $kbli = Kbli::findByCode($validated['kbli_code']);
            if (!$kbli || strlen($kbli->code) !== 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a valid 5-digit KBLI code',
                ], 422);
            }
            
            // Calculate AI-enhanced estimate
            Log::info('Calculating consultation estimate', [
                'kbli_code' => $validated['kbli_code'],
                'business_size' => $validated['business_size'],
                'location' => $validated['location'],
            ]);
            
            $startTime = microtime(true);
            
            $estimate = $this->pricingEngine->calculateEstimate([
                'kbli_code' => $validated['kbli_code'],
                'business_size' => $validated['business_size'],
                'location' => $validated['location'],
                'location_type' => $validated['location_type'],
                'investment_level' => $validated['investment_level'],
                'employee_count' => $validated['employee_count'] ?? 5,
                'project_description' => $validated['deliverables'] ?? 'Konsultasi perizinan usaha',
                'deliverables_requested' => [],
            ]);
            
            $estimateTime = (int) ((microtime(true) - $startTime) * 1000);
            
            // Map form investment_level to database enum
            $investmentLevelMap = [
                'under_1b' => 'under_100m',
                '1b_5b' => '100m_500m',
                '5b_10b' => '500m_2b',
                'above_10b' => 'over_2b',
            ];
            $dbInvestmentLevel = $investmentLevelMap[$validated['investment_level']] ?? 'under_100m';
            
            // Map form location_type to database enum
            $locationTypeMap = [
                'jakarta' => 'commercial',
                'jabodetabek' => 'commercial',
                'jawa_bali' => 'commercial',
                'luar_jawa' => 'industrial',
            ];
            $dbLocationType = $locationTypeMap[$validated['location_type']] ?? 'commercial';
            
            // Create consultation request record
            $consultRequest = ConsultRequest::create([
                'name' => 'Guest User', // Temporary placeholder
                'email' => 'guest-' . time() . '@bizmark.id', // Temporary email
                'phone' => $validated['contact_phone'],
                'company_name' => null,
                'kbli_code' => $validated['kbli_code'],
                'business_size' => $validated['business_size'],
                'location' => $validated['location'],
                'location_type' => $dbLocationType, // Use mapped value
                'investment_level' => $dbInvestmentLevel, // Use mapped value
                'employee_count' => $validated['employee_count'] ?? null,
                'project_description' => $validated['deliverables'] ?? 'Konsultasi perizinan usaha',
                'deliverables_requested' => [],
                'estimate_status' => 'auto_estimated',
                'auto_estimate' => $estimate,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referrer_url' => $request->headers->get('referer'),
            ]);
            
            Log::info('Consultation request created', [
                'request_id' => $consultRequest->id,
                'kbli_code' => $validated['kbli_code'],
                'estimate_total' => $estimate['cost_summary']['grand_total'] ?? 0,
                'confidence' => $estimate['confidence_score'] ?? 0,
                'processing_time_ms' => $estimateTime,
            ]);
            
            // Increment KBLI usage counter
            $kbli->incrementUsage();
            
            // Return response with estimate
            return response()->json([
                'success' => true,
                'message' => 'Consultation request submitted successfully',
                'data' => [
                    'request_id' => $consultRequest->id,
                    'kbli' => [
                        'code' => $kbli->code,
                        'description' => $kbli->description,
                        'category' => $kbli->category,
                        'complexity_level' => $kbli->complexity_level,
                    ],
                    'estimate' => [
                        'cost_summary' => $estimate['cost_summary'] ?? null,
                        'cost_breakdown' => $estimate['cost_breakdown'] ?? null,
                        'confidence_score' => $estimate['confidence_score'] ?? 0.5,
                        'ai_analysis' => isset($estimate['ai_analysis']) ? [
                            'permits_count' => count($estimate['ai_analysis']['permits'] ?? []),
                            'model_used' => $estimate['ai_analysis']['model_used'] ?? null,
                            'timeline' => $estimate['ai_analysis']['timeline'] ?? null,
                        ] : null,
                    ],
                    'next_steps' => [
                        'We will review your request and contact you within 24 hours',
                        'Check your email for detailed consultation report',
                        'Register in our client portal for full project management access',
                    ],
                ],
                'meta' => [
                    'processing_time_ms' => $estimateTime,
                    'created_at' => $consultRequest->created_at->toIso8601String(),
                ],
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Consultation submission error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except(['password', 'token']),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit consultation request. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
    
    /**
     * Get quick estimate without saving (preview only)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function quickEstimate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kbli_code' => [
                    'required',
                    'string',
                    'regex:/^\d{5}$/',
                    'exists:kbli,code',
                ],
                'business_size' => 'required|in:micro,small,medium,large',
                'location_type' => 'required|in:jakarta,jabodetabek,jawa_bali,luar_jawa',
                'investment_level' => 'nullable|in:under_1b,1b_5b,5b_10b,above_10b',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
            
            $validated = $validator->validated();
            
            // Quick estimate without full AI analysis
            $kbli = Kbli::findByCode($validated['kbli_code']);
            
            if (!$kbli || strlen($kbli->code) !== 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid KBLI code',
                ], 422);
            }
            
            // Use base pricing with multipliers only (no AI call)
            $estimate = $this->pricingEngine->calculateEstimate([
                'kbli_code' => $validated['kbli_code'],
                'business_size' => $validated['business_size'],
                'location' => 'Indonesia',
                'location_type' => $validated['location_type'],
                'investment_level' => $validated['investment_level'] ?? 'under_100m',
                'employee_count' => 5,
                'project_description' => 'Quick estimate preview',
                'deliverables_requested' => [],
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'kbli' => [
                        'code' => $kbli->code,
                        'description' => $kbli->description,
                        'complexity_level' => $kbli->complexity_level,
                    ],
                    'estimate' => [
                        'formatted' => $estimate['cost_summary']['formatted'] ?? null,
                        'cost_range' => $estimate['cost_summary']['cost_range'] ?? null,
                        'confidence_score' => $estimate['confidence_score'] ?? 0.5,
                    ],
                    'note' => 'This is a quick estimate. Submit full form for detailed AI-powered analysis.',
                ],
            ]);
            
        } catch (\Exception $e) {
            Log::error('Quick estimate error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate estimate',
            ], 500);
        }
    }
    
    /**
     * Extract UTM parameters from request
     * 
     * @param Request $request
     * @return array|null
     */
    protected function extractUtmParams(Request $request): ?array
    {
        $utmParams = [];
        
        $utmKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
        
        foreach ($utmKeys as $key) {
            if ($request->has($key)) {
                $utmParams[$key] = $request->input($key);
            }
        }
        
        return empty($utmParams) ? null : $utmParams;
    }
}
