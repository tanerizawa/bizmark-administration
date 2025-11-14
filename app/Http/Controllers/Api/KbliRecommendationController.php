<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\KbliPermitCacheService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class KbliRecommendationController extends Controller
{
    protected KbliPermitCacheService $cacheService;

    public function __construct(KbliPermitCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Get permit recommendations for a KBLI code
     */
    public function getRecommendations(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kbli_code' => 'required|string|exists:kbli,code',
            'business_scale' => 'nullable|in:micro,small,medium,large',
            'location_type' => 'nullable|in:urban,rural,industrial_zone',
        ]);

        $clientId = auth('client')->check() ? auth('client')->id() : null;

        $recommendation = $this->cacheService->getRecommendations(
            $validated['kbli_code'],
            $validated['business_scale'] ?? null,
            $validated['location_type'] ?? null,
            $clientId
        );

        if (!$recommendation) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghasilkan rekomendasi. Silakan coba lagi.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kbli_code' => $recommendation->kbli_code,
                'kbli' => $recommendation->kbli,
                'business_context' => [
                    'scale' => $recommendation->business_scale,
                    'location' => $recommendation->location_type,
                ],
                'permits' => $recommendation->recommended_permits,
                'documents' => $recommendation->required_documents,
                'risk_assessment' => $recommendation->risk_assessment,
                'timeline' => $recommendation->estimated_timeline,
                'additional_notes' => $recommendation->additional_notes 
                    ? json_decode($recommendation->additional_notes, true) 
                    : null,
                'summary' => [
                    'mandatory_permits_count' => $recommendation->mandatory_permits_count,
                    'total_documents' => count($recommendation->required_documents ?? []),
                    'estimated_cost' => $recommendation->total_cost_range,
                    'estimated_days' => $recommendation->estimated_timeline 
                        ? [
                            'min' => $recommendation->estimated_timeline['minimum_days'] ?? 0,
                            'max' => $recommendation->estimated_timeline['maximum_days'] ?? 0,
                        ] 
                        : null,
                ],
                'metadata' => [
                    'ai_model' => $recommendation->ai_model,
                    'confidence_score' => $recommendation->confidence_score,
                    'generated_at' => $recommendation->created_at,
                    'cache_hits' => $recommendation->cache_hits,
                ],
            ],
        ]);
    }

    /**
     * Force refresh recommendations
     */
    public function refresh(Request $request): JsonResponse
    {
        $this->authorize('admin'); // Only admins can force refresh

        $validated = $request->validate([
            'kbli_code' => 'required|string|exists:kbli,code',
            'business_scale' => 'nullable|in:micro,small,medium,large',
            'location_type' => 'nullable|in:urban,rural,industrial_zone',
        ]);

        $recommendation = $this->cacheService->refreshRecommendation(
            $validated['kbli_code'],
            $validated['business_scale'] ?? null,
            $validated['location_type'] ?? null
        );

        if (!$recommendation) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal me-refresh rekomendasi.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Rekomendasi berhasil di-refresh.',
            'data' => $recommendation,
        ]);
    }

    /**
     * Get cache statistics
     */
    public function stats(): JsonResponse
    {
        $this->authorize('admin');

        $stats = $this->cacheService->getCacheStats();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
