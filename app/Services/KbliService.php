<?php

namespace App\Services;

use App\Models\Kbli;
use Illuminate\Support\Facades\Cache;

class KbliService
{
    /**
     * Search KBLI by keyword (code or description)
     * Using database with caching for better performance
     */
    public function search(string $keyword, int $limit = 20): array
    {
        // Return empty if keyword too short
        if (strlen($keyword) < 2) {
            return [];
        }

        // Cache key based on keyword and limit
        $cacheKey = 'kbli_search_' . md5(strtolower($keyword)) . '_' . $limit;

        return Cache::remember($cacheKey, 3600, function () use ($keyword, $limit) {
            $results = Kbli::search($keyword, $limit);
            
            // Convert to array format for compatibility
            return $results->map(function ($kbli) {
                return [
                    'code' => $kbli->code,
                    'description' => $kbli->description,
                    'category' => $kbli->category,
                    'sector' => $kbli->sector,
                    'notes' => $kbli->notes,
                ];
            })->toArray();
        });
    }

    /**
     * Get KBLI by specific code
     */
    public function getByCode(string $code): ?array
    {
        $cacheKey = 'kbli_code_' . $code;

        return Cache::remember($cacheKey, 86400, function () use ($code) {
            $kbli = Kbli::findByCode($code);
            
            if (!$kbli) {
                return null;
            }

            return [
                'code' => $kbli->code,
                'description' => $kbli->description,
                'category' => $kbli->category,
                'sector' => $kbli->sector,
                'notes' => $kbli->notes,
            ];
        });
    }

    /**
     * Get all KBLI data
     */
    public function getAll(): array
    {
        return Cache::remember('kbli_all', 86400, function () {
            $results = Kbli::orderBy('code')->get();
            
            return $results->map(function ($kbli) {
                return [
                    'code' => $kbli->code,
                    'description' => $kbli->description,
                    'category' => $kbli->category,
                    'sector' => $kbli->sector,
                    'notes' => $kbli->notes,
                ];
            })->toArray();
        });
    }

    /**
     * Get KBLI by risk category
     */
    public function getByCategory(string $category): array
    {
        $cacheKey = 'kbli_category_' . md5($category);

        return Cache::remember($cacheKey, 86400, function () use ($category) {
            $results = Kbli::getByCategory($category);
            
            return $results->map(function ($kbli) {
                return [
                    'code' => $kbli->code,
                    'description' => $kbli->description,
                    'category' => $kbli->category,
                    'sector' => $kbli->sector,
                    'notes' => $kbli->notes,
                ];
            })->toArray();
        });
    }

    /**
     * Get KBLI by business sector
     */
    public function getBySector(string $sector): array
    {
        $cacheKey = 'kbli_sector_' . $sector;

        return Cache::remember($cacheKey, 86400, function () use ($sector) {
            $results = Kbli::getBySector($sector);
            
            return $results->map(function ($kbli) {
                return [
                    'code' => $kbli->code,
                    'description' => $kbli->description,
                    'category' => $kbli->category,
                    'sector' => $kbli->sector,
                    'notes' => $kbli->notes,
                ];
            })->toArray();
        });
    }

    /**
     * Clear all KBLI cache
     */
    public function clearCache(): void
    {
        Cache::forget('kbli_all');
        // Cache with patterns will expire naturally
    }
}
