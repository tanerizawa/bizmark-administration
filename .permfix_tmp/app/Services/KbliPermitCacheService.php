<?php

namespace App\Services;

use App\Models\Kbli;
use App\Models\KbliPermitRecommendation;
use Illuminate\Support\Facades\Log;

class KbliPermitCacheService
{
    protected OpenRouterService $aiService;
    const CACHE_TTL_DAYS = 30;
    
    public function __construct(OpenRouterService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Get or generate permit recommendations for a KBLI code
     */
    public function getRecommendations(
        string $kbliCode,
        ?string $businessScale = null,
        ?string $locationType = null,
        ?int $clientId = null
    ): ?KbliPermitRecommendation {
        // Try to get from cache first
        $cached = $this->getCachedRecommendation($kbliCode, $businessScale, $locationType);
        
        if ($cached && !$cached->needsRefresh()) {
            $cached->incrementCacheHits();
            Log::info('Using cached KBLI recommendation', [
                'kbli_code' => $kbliCode,
                'cache_hits' => $cached->cache_hits + 1,
            ]);
            return $cached;
        }

        // Generate new recommendation
        return $this->generateAndCache($kbliCode, $businessScale, $locationType, $clientId);
    }

    /**
     * Get cached recommendation from database
     */
    protected function getCachedRecommendation(
        string $kbliCode,
        ?string $businessScale,
        ?string $locationType
    ): ?KbliPermitRecommendation {
        return KbliPermitRecommendation::active()
            ->forContext($kbliCode, $businessScale, $locationType)
            ->first();
    }

    /**
     * Generate new recommendation and cache it
     */
    protected function generateAndCache(
        string $kbliCode,
        ?string $businessScale,
        ?string $locationType,
        ?int $clientId
    ): ?KbliPermitRecommendation {
        // Get KBLI details
        $kbli = Kbli::where('code', $kbliCode)->first();
        
        if (!$kbli) {
            Log::error('KBLI not found', ['kbli_code' => $kbliCode]);
            return null;
        }

        Log::info('Generating new AI recommendation', [
            'kbli_code' => $kbliCode,
            'scale' => $businessScale,
            'location' => $locationType,
        ]);

        // Call AI service
        $aiData = $this->aiService->generatePermitRecommendations(
            $kbliCode,
            $kbli->description,
            $kbli->sector,
            $businessScale,
            $locationType,
            $clientId
        );

        if (!$aiData) {
            Log::error('Failed to generate AI recommendation', ['kbli_code' => $kbliCode]);
            return null;
        }

        // Save to database
        try {
            $recommendation = KbliPermitRecommendation::updateOrCreate(
                [
                    'kbli_code' => $kbliCode,
                    'business_scale' => $businessScale,
                    'location_type' => $locationType,
                ],
                array_merge($aiData, [
                    'cache_hits' => 0,
                    'last_used_at' => now(),
                    'expires_at' => now()->addDays(self::CACHE_TTL_DAYS),
                ])
            );

            Log::info('Cached new AI recommendation', [
                'id' => $recommendation->id,
                'kbli_code' => $kbliCode,
                'confidence' => $recommendation->confidence_score,
            ]);

            return $recommendation;
        } catch (\Exception $e) {
            Log::error('Failed to cache recommendation', [
                'kbli_code' => $kbliCode,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Force refresh a recommendation
     */
    public function refreshRecommendation(
        string $kbliCode,
        ?string $businessScale = null,
        ?string $locationType = null,
        ?int $clientId = null
    ): ?KbliPermitRecommendation {
        // Delete existing cache
        KbliPermitRecommendation::where('kbli_code', $kbliCode)
            ->where('business_scale', $businessScale)
            ->where('location_type', $locationType)
            ->delete();

        // Generate new one
        return $this->generateAndCache($kbliCode, $businessScale, $locationType, $clientId);
    }

    /**
     * Clear expired recommendations
     */
    public function clearExpired(): int
    {
        $deleted = KbliPermitRecommendation::where('expires_at', '<', now())->delete();
        
        Log::info('Cleared expired recommendations', ['count' => $deleted]);
        
        return $deleted;
    }

    /**
     * Clear all cache for a specific KBLI
     */
    public function clearKbliCache(string $kbliCode): int
    {
        $deleted = KbliPermitRecommendation::where('kbli_code', $kbliCode)->delete();
        
        Log::info('Cleared KBLI cache', ['kbli_code' => $kbliCode, 'count' => $deleted]);
        
        return $deleted;
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        return [
            'total_cached' => KbliPermitRecommendation::count(),
            'active_cached' => KbliPermitRecommendation::active()->count(),
            'expired' => KbliPermitRecommendation::where('expires_at', '<', now())->count(),
            'total_hits' => KbliPermitRecommendation::sum('cache_hits'),
            'avg_confidence' => KbliPermitRecommendation::avg('confidence_score'),
            'low_confidence' => KbliPermitRecommendation::where('confidence_score', '<', 0.7)->count(),
        ];
    }
}
