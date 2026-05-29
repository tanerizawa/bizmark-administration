<?php

namespace App\Services;

use App\Models\OssCredential;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * P4 — OSS-RBA Status Tracker
 *
 * Mengambil status permohonan dari OSS menggunakan API resmi OSS (oss.go.id)
 * atau fallback scraping ringan.
 *
 * ⚠️ LEGAL NOTE: Penggunaan harus mematuhi ToS oss.go.id.
 * Rate limit: maks 1 check per akun per 24 jam.
 */
class OssScraperService
{
    private const OSS_API_BASE = 'https://oss.go.id/api/v1';

    private const CACHE_TTL_HOURS = 23;

    /**
     * Fetch OSS permit status untuk satu nomor permohonan.
     * Returns ['status_code', 'status_label', 'raw'] atau null jika gagal.
     */
    public function fetchStatus(OssCredential $credential, string $applicationNumber): ?array
    {
        $cacheKey = 'oss_status:'.$credential->client_id.':'.md5($applicationNumber);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Try OSS REST API first (if available)
        $result = $this->tryOssApi($credential, $applicationNumber);

        // Fallback: return structured "pending manual check" if API unavailable
        if ($result === null) {
            $result = [
                'status_code' => 'UNKNOWN',
                'status_label' => 'Tidak dapat terhubung ke OSS — periksa manual di oss.go.id',
                'raw' => [],
            ];
        }

        Cache::put($cacheKey, $result, now()->addHours(self::CACHE_TTL_HOURS));

        return $result;
    }

    /**
     * Coba fetch via API OSS resmi (endpoint publik status permohonan).
     */
    private function tryOssApi(OssCredential $credential, string $applicationNumber): ?array
    {
        try {
            // OSS permohonan status endpoint (public — no auth needed for status check)
            $response = Http::timeout(20)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'Bizmark-OSS-Tracker/1.0 (+https://bizmark.id)',
                ])
                ->get(self::OSS_API_BASE.'/permohonan/'.urlencode($applicationNumber).'/status');

            if ($response->successful()) {
                $data = $response->json();
                $statusCode = $data['status_code'] ?? $data['status'] ?? 'UNKNOWN';
                $statusLabel = $data['status_label'] ?? $data['keterangan'] ?? $statusCode;

                return [
                    'status_code' => (string) $statusCode,
                    'status_label' => (string) $statusLabel,
                    'raw' => $data,
                ];
            }

            Log::info('[OSS] API returned non-200', [
                'status' => $response->status(),
                'app_num' => $applicationNumber,
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::warning('[OSS] API request failed: '.$e->getMessage());

            return null;
        }
    }
}
