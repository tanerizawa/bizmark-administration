<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\ConsultRequest;
use App\Models\Kbli;
use App\Services\ConsultationPricingEngine;
use App\Services\PerizinanAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ConsultationController extends Controller
{
    use ApiResponse;

    protected ConsultationPricingEngine $pricingEngine;

    protected PerizinanAIService $ragService;

    public function __construct(
        ConsultationPricingEngine $pricingEngine,
        PerizinanAIService $ragService
    ) {
        $this->pricingEngine = $pricingEngine;
        $this->ragService = $ragService;
    }

    /**
     * Submit free consultation request with AI cost estimation
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function submit(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                // Contact information
                'applicant_name' => 'required|string|max:255',
                'applicant_email' => 'nullable|email|max:255',
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
                'location_type' => 'required|in:commercial,industrial,residential,mixed_use,special_economic,rural_agricultural,tourism,educational',
                'geographic_region' => 'required|in:jakarta_capital,java_major_cities,java_medium_cities,java_small_cities,bali_lombok,sumatra_major,sumatra_others,kalimantan_major,kalimantan_others,sulawesi_major,sulawesi_others,eastern_indonesia,border_areas',
                'entity_type' => 'required|in:individual,cv,firma,pt,pt_pma,persero,perum,koperasi,yayasan,perkumpulan,bumn,foreign_rep',
                'investment_level' => 'required|in:under_100m,100m_500m,500m_2b,2b_10b,10b_50b,above_50b',
                'employee_count' => 'nullable|integer|min:0|max:100000',
                'target_timeline' => 'nullable|in:urgent,fast,normal,planned,flexible',
                'business_nature' => 'nullable|in:local_market,export_oriented,import_dependent,b2b_services,b2c_retail,online_marketplace,franchise,government_contractor,high_risk',
                'additional_activities' => 'nullable|array|max:3',
                'additional_activities.*.kbli_code' => [
                    'nullable',
                    'string',
                    'regex:/^\d{5}$/',
                    'exists:kbli,code',
                ],
                'additional_activities.*.description' => 'nullable|string|max:255',

                // Project details (optional - AI will recommend if not provided)
                'deliverables' => 'nullable|string|max:5000',
            ], [
                'applicant_name.required' => 'Nama pengusul wajib diisi.',
                'contact_phone.required' => 'Nomor WhatsApp wajib diisi.',
                'contact_phone.max' => 'Nomor WhatsApp maksimal 20 karakter.',
                'applicant_email.email' => 'Format email tidak valid.',
                'kbli_code.required' => 'Kode KBLI wajib dipilih.',
                'kbli_code.regex' => 'Kode KBLI harus terdiri dari 5 digit.',
                'kbli_code.exists' => 'Kode KBLI tidak ditemukan atau tidak aktif.',
                'business_size.required' => 'Skala bisnis wajib dipilih.',
                'location.required' => 'Kota/kabupaten wajib diisi.',
                'location_type.required' => 'Zona/kawasan lokasi wajib dipilih.',
                'geographic_region.required' => 'Wilayah geografis wajib dipilih.',
                'entity_type.required' => 'Jenis badan usaha wajib dipilih.',
                'investment_level.required' => 'Level investasi wajib dipilih.',
                'additional_activities.max' => 'Maksimal 3 aktivitas tambahan.',
                'additional_activities.*.kbli_code.regex' => 'KBLI aktivitas tambahan harus 5 digit.',
                'additional_activities.*.kbli_code.exists' => 'KBLI aktivitas tambahan tidak valid.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal. Mohon periksa data Anda.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $validated = $validator->validated();

            // Verify KBLI code is 5-digit and active
            $kbli = Kbli::findByCode($validated['kbli_code']);
            if (! $kbli || strlen($kbli->code) !== 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan pilih KBLI 5 digit yang valid.',
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
                'geographic_region' => $validated['geographic_region'] ?? null,
                'entity_type' => $validated['entity_type'] ?? null,
                'investment_level' => $validated['investment_level'],
                'employee_count' => $validated['employee_count'] ?? 5,
                'target_timeline' => $validated['target_timeline'] ?? null,
                'business_nature' => $validated['business_nature'] ?? null,
                'project_description' => $validated['deliverables'] ?? 'Konsultasi perizinan usaha',
                'deliverables_requested' => [],
            ]);

            $estimateTime = (int) ((microtime(true) - $startTime) * 1000);

            // Map form investment_level to database enum (now consistent)
            $investmentLevelMap = [
                'under_100m' => 'under_100m',
                '100m_500m' => '100m_500m',
                '500m_2b' => '500m_2b',
                '2b_10b' => 'over_2b',       // Map to closest DB enum
                '10b_50b' => 'over_2b',      // Large investments
                'above_50b' => 'over_2b',    // Very large investments
            ];
            $dbInvestmentLevel = $investmentLevelMap[$validated['investment_level']] ?? 'under_100m';

            // Map form location_type to database enum values
            $locationTypeMap = [
                'commercial' => 'commercial',
                'industrial' => 'industrial',
                'residential' => 'residential',
                'mixed_use' => 'commercial',        // Mixed-use treated as commercial
                'special_economic' => 'industrial', // KEK treated as industrial
                'rural_agricultural' => 'rural',   // Rural/agricultural
                'tourism' => 'commercial',          // Tourism as commercial
                'educational' => 'residential',     // Educational as residential-like
            ];
            $dbLocationType = $locationTypeMap[$validated['location_type']] ?? 'commercial';

            // Get RAG regulation context
            [$ragInsights, $ragConfidence] = $this->buildStructuredRagInsights($validated, $kbli);

            // Use actual applicant data or fallback to descriptive placeholder
            $applicantName = $validated['applicant_name'];
            $applicantEmail = $validated['applicant_email'] ?? ('guest-'.time().'@bizmark.id');

            // Create consultation request record with accurate data
            $consultRequest = ConsultRequest::create([
                'name' => $applicantName, // Real applicant name
                'email' => $applicantEmail, // Real email or temporary
                'phone' => $validated['contact_phone'],
                'company_name' => null,
                'kbli_code' => $validated['kbli_code'],
                'business_size' => $validated['business_size'],
                'location' => $validated['location'],
                'location_type' => $dbLocationType,
                'investment_level' => $dbInvestmentLevel,
                'employee_count' => (int) ($validated['employee_count'] ?? 0), // Ensure integer
                'project_description' => $validated['deliverables'] ?? 'Konsultasi perizinan usaha',
                'deliverables_requested' => [],
                'estimate_status' => 'auto_estimated',
                'auto_estimate' => array_merge($estimate, [
                    'form_data' => [
                        'geographic_region' => $validated['geographic_region'] ?? null,
                        'entity_type' => $validated['entity_type'] ?? null,
                        'target_timeline' => $validated['target_timeline'] ?? null,
                        'business_nature' => $validated['business_nature'] ?? null,
                        'original_investment_level' => $validated['investment_level'], // Keep original for reference
                        'original_location_type' => $validated['location_type'], // Keep original for reference
                    ],
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referrer_url' => $request->headers->get('referer'),
                'rag_insights' => $ragInsights,
                'rag_confidence' => $ragConfidence,
                'rag_processed_at' => $ragInsights ? now() : null,
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
                'message' => 'Permintaan estimasi berhasil dikirim.',
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
                    'rag' => $ragInsights ? [
                        'confidence' => $ragConfidence,
                        'activity_requirements_count' => count($ragInsights['activity_requirements'] ?? []),
                    ] : null,
                    'next_steps' => [
                        'Tim kami akan meninjau permintaan Anda dan menghubungi dalam 24 jam.',
                        'Periksa email Anda untuk laporan estimasi detail.',
                        'Daftar ke client portal untuk manajemen proyek lengkap.',
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
                'message' => 'Validasi gagal. Mohon periksa data Anda.',
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
                'message' => 'Gagal mengirim permintaan estimasi. Silakan coba lagi.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get quick estimate without saving (preview only)
     *
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
                'location_type' => 'required|in:commercial,industrial,residential,mixed_use,special_economic,rural_agricultural,tourism,educational',
                'geographic_region' => 'nullable|in:jakarta_capital,java_major_cities,java_medium_cities,java_small_cities,bali_lombok,sumatra_major,sumatra_others,kalimantan_major,kalimantan_others,sulawesi_major,sulawesi_others,eastern_indonesia,border_areas',
                'investment_level' => 'nullable|in:under_100m,100m_500m,500m_2b,2b_10b,10b_50b,above_50b',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi estimasi cepat gagal.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $validated = $validator->validated();

            // Quick estimate without full AI analysis
            $kbli = Kbli::findByCode($validated['kbli_code']);

            if (! $kbli || strlen($kbli->code) !== 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode KBLI tidak valid.',
                ], 422);
            }

            // Use base pricing with multipliers only (no AI call)
            $estimate = $this->pricingEngine->calculateEstimate([
                'kbli_code' => $validated['kbli_code'],
                'business_size' => $validated['business_size'],
                'location' => 'Indonesia',
                'location_type' => $validated['location_type'],
                'geographic_region' => $validated['geographic_region'] ?? null,
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
                    'note' => 'Ini adalah estimasi cepat. Kirim formulir penuh untuk analisis AI yang lebih detail.',
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Quick estimate error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung estimasi cepat.',
            ], 500);
        }
    }

    /**
     * Extract UTM parameters from request
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

    /**
     * Convert entity type code to readable label for RAG query
     */
    protected function getEntityTypeLabel(string $entityType): string
    {
        $labels = [
            'individual' => 'Perorangan',
            'cv' => 'CV (Commanditaire Vennootschap)',
            'firma' => 'Firma',
            'pt' => 'PT (Perseroan Terbatas)',
            'pt_pma' => 'PT PMA (Penanaman Modal Asing)',
            'persero' => 'Persero',
            'perum' => 'Perum (Perusahaan Umum)',
            'koperasi' => 'Koperasi',
            'yayasan' => 'Yayasan',
            'perkumpulan' => 'Perkumpulan',
            'bumn' => 'BUMN (Badan Usaha Milik Negara)',
            'foreign_rep' => 'Kantor Perwakilan Perusahaan Asing',
        ];

        return $labels[$entityType] ?? 'PT';
    }

    protected function buildStructuredRagInsights(array $validated, Kbli $primaryKbli): array
    {
        try {
            Log::info('Fetching structured RAG regulation context', [
                'entity_type' => $validated['entity_type'],
                'location' => $validated['location'],
                'kbli_code' => $validated['kbli_code'],
            ]);

            $ragStartTime = microtime(true);
            $confidenceScores = [];

            $overviewContext = $this->ragService->getBusinessTypeRegulations(
                $this->getEntityTypeLabel($validated['entity_type']),
                $validated['location']
            );

            if (isset($overviewContext['confidence_score'])) {
                $confidenceScores[] = $overviewContext['confidence_score'];
            }

            $activityRequirements = [];
            foreach ($this->collectActivityContexts($validated, $primaryKbli) as $activity) {
                try {
                    $kbliContext = $this->ragService->getKBLIRequirements(
                        $activity['kbli_code'],
                        $activity['description']
                    );

                    $activityConfidence = $kbliContext['confidence_score'] ?? 0;
                    if ($activityConfidence > 0) {
                        $confidenceScores[] = $activityConfidence;
                    }

                    $activityRequirements[] = [
                        'label' => $activity['label'],
                        'kbli_code' => $activity['kbli_code'],
                        'description' => $activity['description'],
                        'answer' => $kbliContext['answer'] ?? null,
                        'sources' => array_slice($kbliContext['sources'] ?? [], 0, 3),
                        'confidence' => $activityConfidence,
                    ];
                } catch (\Exception $e) {
                    Log::warning('KBLI-specific RAG query failed during consultation', [
                        'kbli_code' => $activity['kbli_code'],
                        'description' => $activity['description'],
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $ragDuration = round((microtime(true) - $ragStartTime) * 1000, 2);
            $aggregateConfidence = count($confidenceScores) > 0
                ? round(array_sum($confidenceScores) / count($confidenceScores), 4)
                : ($overviewContext['confidence_score'] ?? 0);

            $ragInsights = [
                'answer' => $overviewContext['answer'] ?? null,
                'sources' => array_slice($overviewContext['sources'] ?? [], 0, 5),
                'confidence' => $overviewContext['confidence_score'] ?? 0,
                'query_type' => 'business_type_with_kbli_requirements',
                'query_params' => [
                    'entity_type' => $validated['entity_type'],
                    'location' => $validated['location'],
                    'primary_kbli' => $primaryKbli->code,
                    'activities_count' => count($activityRequirements),
                ],
                'activity_requirements' => $activityRequirements,
            ];

            Log::info('Structured RAG query successful', [
                'confidence' => $aggregateConfidence,
                'overview_sources_count' => count($overviewContext['sources'] ?? []),
                'activities_count' => count($activityRequirements),
                'duration_ms' => $ragDuration,
            ]);

            return [$ragInsights, $aggregateConfidence];
        } catch (\Exception $e) {
            Log::warning('RAG query failed during consultation', [
                'error' => $e->getMessage(),
                'entity_type' => $validated['entity_type'],
                'location' => $validated['location'],
            ]);

            return [null, null];
        }
    }

    protected function collectActivityContexts(array $validated, Kbli $primaryKbli): array
    {
        $activities = [[
            'label' => 'Aktivitas Utama',
            'kbli_code' => $primaryKbli->code,
            'description' => $primaryKbli->description,
        ]];

        foreach ($validated['additional_activities'] ?? [] as $index => $activity) {
            $kbliCode = $activity['kbli_code'] ?? null;
            $description = $activity['description'] ?? null;

            if (! $kbliCode && ! $description) {
                continue;
            }

            $resolvedKbli = $kbliCode ? Kbli::findByCode($kbliCode) : null;

            $activities[] = [
                'label' => 'Aktivitas Tambahan '.($index + 1),
                'kbli_code' => $resolvedKbli?->code ?? $kbliCode,
                'description' => $resolvedKbli?->description ?? $description ?? 'Aktivitas usaha tambahan',
            ];
        }

        return array_values(array_filter($activities, fn (array $activity) => ! empty($activity['kbli_code']) && ! empty($activity['description'])));
    }
}
