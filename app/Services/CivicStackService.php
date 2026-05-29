<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * CivicStackService — Laravel wrapper for the indonesia-civic-stack microservice.
 *
 * The civic_stack Docker container runs FastAPI on the bizmark_network.
 * This service calls it via internal HTTP and caches responses in Redis.
 *
 * Modules covered:
 *   - simbg   : Building permit (PBG/IMB) search — cache 15 min
 *   - bpjph   : Halal certification lookup — cache 5 min
 *   - oss_nib : Business NIB lookup via OSS portal — cache 5 min
 *   - jdih    : Indonesian legal regulations search — cache 60 min
 *
 * All methods return null on failure (graceful degradation).
 */
class CivicStackService
{
    private string $baseUrl;

    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.civic_stack.url', 'http://bizmark_civic_stack:8000'), '/');
        $this->timeout = (int) config('services.civic_stack.timeout', 15);
    }

    /**
     * Search SIMBG for building permit hints in a city.
     *
     * @param  string  $city  City/kabupaten name (e.g., "Jakarta Selatan")
     * @return array|null SIMBG response data or null on failure
     */
    public function simbgSearch(string $city): ?array
    {
        $city = trim($city);
        if (empty($city)) {
            return null;
        }

        $cacheKey = 'civic_stack.simbg.'.md5(strtolower($city));

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($city) {
            return $this->get('/simbg/search', ['q' => $city]);
        });
    }

    /**
     * Search BPJPH for halal certification status.
     *
     * @param  string  $company  Company/product name
     */
    public function bpjphSearch(string $company): ?array
    {
        $company = trim($company);
        if (empty($company)) {
            return null;
        }

        $cacheKey = 'civic_stack.bpjph.'.md5(strtolower($company));

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($company) {
            return $this->get('/bpjph/search', ['q' => $company]);
        });
    }

    /**
     * Look up NIB (business registration number) via OSS portal.
     *
     * @param  string  $query  NIB number (13-digit) or company name
     */
    public function nibLookup(string $query): ?array
    {
        $query = trim($query);
        if (empty($query)) {
            return null;
        }

        $cacheKey = 'civic_stack.nib.'.md5(strtolower($query));

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($query) {
            return $this->get('/oss-nib/nib/'.urlencode($query));
        });
    }

    /**
     * Search JDIH for Indonesian legal regulations.
     *
     * @param  string  $keyword  Search keyword (KBLI description or topic)
     * @param  string  $type  Regulation type: uu, pp, perpres, permen, perda
     * @param  int  $limit  Max results (1–50)
     * @return array|null Array of regulation items or null on failure
     */
    public function jdihSearch(string $keyword, string $type = 'pp', int $limit = 5): ?array
    {
        $keyword = trim($keyword);
        if (empty($keyword)) {
            return null;
        }

        $cacheKey = 'civic_stack.jdih.'.md5(strtolower("{$keyword}|{$type}|{$limit}"));

        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($keyword, $type, $limit) {
            return $this->get('/jdih/search', ['q' => $keyword, 'type' => $type, 'limit' => $limit]);
        });
    }

    /**
     * Make an internal HTTP GET request to the civic_stack microservice.
     *
     * Returns null instead of throwing on any connection/HTTP error
     * so that the Laravel app degrades gracefully when civic_stack is down.
     *
     * @param  string  $path  API path (e.g., "/simbg/search")
     * @param  array  $query  Query parameters
     */
    private function get(string $path, array $query = []): ?array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get($this->baseUrl.$path, $query);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('CivicStack non-2xx response', [
                'path' => $path,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // civic_stack container is down — degrade gracefully
            Log::warning('CivicStack connection failed (container may be down)', [
                'path' => $path,
                'message' => $e->getMessage(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('CivicStack unexpected error', [
                'path' => $path,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
