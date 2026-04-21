<?php

namespace App\Modules\Perizinan\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RtrwProxyController extends Controller
{
    /**
     * Query zona RTRW at a given coordinate (point-in-polygon).
     *
     * Queries all layers in the province's MapServer to find which
     * RTRW zones contain the given point. Returns zone name, type,
     * kabupaten/kota, kecamatan, perda info, etc.
     */
    public function zona(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'province_code' => 'required|string|size:2',
        ]);

        if (!config('rtrw.enabled')) {
            return response()->json(['error' => 'RTRW service is disabled'], 503);
        }

        $provinceConfig = config("rtrw.provinces.{$validated['province_code']}");
        if (!$provinceConfig) {
            return response()->json([
                'error' => 'Data RTRW belum tersedia untuk provinsi ini',
                'available' => false,
            ], 404);
        }

        $cacheKey = sprintf(
            'rtrw:zona:%s:%.6f:%.6f',
            $validated['province_code'],
            $validated['lat'],
            $validated['lng']
        );

        if (config('rtrw.cache.enabled')) {
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                return response()->json($cached);
            }
        }

        try {
            $result = $this->queryZona(
                $provinceConfig['path'],
                $validated['lat'],
                $validated['lng']
            );

            $response = [
                'province' => $provinceConfig['name'],
                'province_code' => $validated['province_code'],
                'lat' => (float) $validated['lat'],
                'lng' => (float) $validated['lng'],
                'zones' => $result,
                'available' => true,
                'source' => 'GISTARU ATR/BPN',
                'disclaimer' => 'Data bersifat indikatif. Untuk kepastian hukum, gunakan data resmi dari instansi berwenang.',
            ];

            if (config('rtrw.cache.enabled')) {
                Cache::put($cacheKey, $response, config('rtrw.cache.ttl_zone_query'));
            }

            return response()->json($response);
        } catch (\Exception $e) {
            Log::warning('RTRW zona query failed', [
                'province' => $validated['province_code'],
                'lat' => $validated['lat'],
                'lng' => $validated['lng'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Gagal mengambil data zona RTRW. Silakan coba lagi.',
                'available' => false,
            ], 502);
        }
    }

    /**
     * Get available layers for a province.
     */
    public function layers(Request $request, string $provinceCode): JsonResponse
    {
        if (!config('rtrw.enabled')) {
            return response()->json(['error' => 'RTRW service is disabled'], 503);
        }

        $provinceConfig = config("rtrw.provinces.{$provinceCode}");
        if (!$provinceConfig) {
            return response()->json(['error' => 'Province not found'], 404);
        }

        $cacheKey = "rtrw:layers:{$provinceCode}";

        if (config('rtrw.cache.enabled')) {
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                return response()->json($cached);
            }
        }

        try {
            $url = $this->buildProxiedUrl($provinceConfig['path'] . '/MapServer', ['f' => 'json']);
            $response = Http::timeout(config('rtrw.http.timeout'))
                ->connectTimeout(config('rtrw.http.connect_timeout'))
                ->withHeaders(['User-Agent' => config('rtrw.http.user_agent')])
                ->get($url);

            if (!$response->successful()) {
                throw new \RuntimeException('GISTARU returned HTTP ' . $response->status());
            }

            $data = $response->json();
            $layers = collect($data['layers'] ?? [])->map(fn ($l) => [
                'id' => $l['id'],
                'name' => $l['name'],
            ])->values()->all();

            $result = [
                'province' => $provinceConfig['name'],
                'province_code' => $provinceCode,
                'layers' => $layers,
                'total' => count($layers),
            ];

            if (config('rtrw.cache.enabled')) {
                Cache::put($cacheKey, $result, config('rtrw.cache.ttl_layers'));
            }

            return response()->json($result);
        } catch (\Exception $e) {
            Log::warning('RTRW layers fetch failed', [
                'province' => $provinceCode,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Gagal mengambil daftar layer RTRW'], 502);
        }
    }

    /**
     * Get legend items for a province MapServer.
     */
    public function legend(Request $request, string $provinceCode): JsonResponse
    {
        if (!config('rtrw.enabled')) {
            return response()->json(['error' => 'RTRW service is disabled'], 503);
        }

        $provinceConfig = config("rtrw.provinces.{$provinceCode}");
        if (!$provinceConfig) {
            return response()->json(['error' => 'Province not found'], 404);
        }

        $cacheKey = "rtrw:legend:{$provinceCode}";

        if (config('rtrw.cache.enabled')) {
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                return response()->json($cached);
            }
        }

        try {
            $url = $this->buildProxiedUrl($provinceConfig['path'] . '/MapServer/legend', ['f' => 'json']);
            $response = Http::timeout(config('rtrw.http.timeout'))
                ->connectTimeout(config('rtrw.http.connect_timeout'))
                ->withHeaders(['User-Agent' => config('rtrw.http.user_agent')])
                ->get($url);

            if (!$response->successful()) {
                throw new \RuntimeException('GISTARU legend returned HTTP ' . $response->status());
            }

            $data = $response->json();
            $legendItems = collect($data['layers'] ?? [])
                ->flatMap(function (array $layer) {
                    return collect($layer['legend'] ?? [])->map(function (array $item) use ($layer) {
                        return [
                            'layer_id' => $layer['layerId'] ?? null,
                            'layer_name' => $layer['layerName'] ?? null,
                            'label' => $item['label'] ?? 'Tanpa label',
                            'content_type' => $item['contentType'] ?? 'image/png',
                            'image_base64' => $item['imageData'] ?? null,
                            'width' => $item['width'] ?? null,
                            'height' => $item['height'] ?? null,
                        ];
                    });
                })
                ->filter(fn (array $item) => !empty($item['label']) || !empty($item['image_base64']))
                ->values()
                ->all();

            $result = [
                'province' => $provinceConfig['name'],
                'province_code' => $provinceCode,
                'items' => $legendItems,
                'total' => count($legendItems),
            ];

            if (config('rtrw.cache.enabled')) {
                Cache::put($cacheKey, $result, config('rtrw.cache.ttl_layers'));
            }

            return response()->json($result);
        } catch (\Exception $e) {
            Log::warning('RTRW legend fetch failed', [
                'province' => $provinceCode,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Gagal mengambil legenda RTRW'], 502);
        }
    }

    /**
     * Analyze a polygon using multiple sample points and aggregate coverage.
     */
    public function analyze(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'province_code' => 'required|string|size:2',
            'points' => 'required|array|min:1|max:12',
            'points.*.lat' => 'required|numeric|between:-90,90',
            'points.*.lng' => 'required|numeric|between:-180,180',
            'points.*.label' => 'nullable|string|max:80',
        ]);

        if (!config('rtrw.enabled')) {
            return response()->json(['error' => 'RTRW service is disabled'], 503);
        }

        $provinceConfig = config("rtrw.provinces.{$validated['province_code']}");
        if (!$provinceConfig) {
            return response()->json([
                'error' => 'Data RTRW belum tersedia untuk provinsi ini',
                'available' => false,
            ], 404);
        }

        try {
            $samples = [];
            $aggregated = [];
            $sampleCount = count($validated['points']);

            foreach ($validated['points'] as $index => $point) {
                $zones = $this->queryZona($provinceConfig['path'], (float) $point['lat'], (float) $point['lng']);
                $label = $point['label'] ?? ('Titik ' . ($index + 1));

                $samples[] = [
                    'label' => $label,
                    'lat' => (float) $point['lat'],
                    'lng' => (float) $point['lng'],
                    'zones' => $zones,
                ];

                $seenKeys = [];
                foreach ($zones as $zone) {
                    $zoneKey = md5(json_encode([
                        $zone['zona'] ?? null,
                        $zone['jenis_zona'] ?? null,
                        $zone['layer_name'] ?? null,
                        $zone['no_perda'] ?? null,
                    ]));

                    if (isset($seenKeys[$zoneKey])) {
                        continue;
                    }
                    $seenKeys[$zoneKey] = true;

                    if (!isset($aggregated[$zoneKey])) {
                        $aggregated[$zoneKey] = array_merge($zone, [
                            'hits' => 0,
                            'sample_labels' => [],
                        ]);
                    }

                    $aggregated[$zoneKey]['hits']++;
                    $aggregated[$zoneKey]['sample_labels'][] = $label;
                }
            }

            $aggregatedZones = collect($aggregated)
                ->map(function (array $zone) use ($sampleCount) {
                    $zone['coverage_percent'] = $sampleCount > 0
                        ? round(($zone['hits'] / $sampleCount) * 100, 1)
                        : 0;
                    return $zone;
                })
                ->sortByDesc('hits')
                ->values()
                ->all();

            return response()->json([
                'province' => $provinceConfig['name'],
                'province_code' => $validated['province_code'],
                'available' => true,
                'source' => 'GISTARU ATR/BPN',
                'disclaimer' => 'Data bersifat indikatif. Untuk kepastian hukum, gunakan data resmi dari instansi berwenang.',
                'sample_count' => $sampleCount,
                'samples' => $samples,
                'aggregated_zones' => $aggregatedZones,
            ]);
        } catch (\Exception $e) {
            Log::warning('RTRW polygon analysis failed', [
                'province' => $validated['province_code'],
                'points' => count($validated['points']),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Gagal menganalisis zona RTRW untuk poligon.',
                'available' => false,
            ], 502);
        }
    }

    /**
     * List all available provinces for RTRW data.
     */
    public function provinces(): JsonResponse
    {
        $provinces = collect(config('rtrw.provinces', []))->map(fn ($p, $code) => [
            'code' => $code,
            'name' => $p['name'],
        ])->values()->all();

        return response()->json([
            'provinces' => $provinces,
            'total' => count($provinces),
            'disclaimer' => 'Data RTRW bersifat indikatif dari GISTARU ATR/BPN.',
        ]);
    }

    /**
     * Proxy MapServer export as image tile for Leaflet overlay.
     */
    public function mapExport(Request $request, string $provinceCode)
    {
        $validated = $request->validate([
            'bbox' => 'required|string',
            'width' => 'required|integer|min:1|max:1024',
            'height' => 'required|integer|min:1|max:1024',
        ]);

        if (!config('rtrw.enabled')) {
            abort(503, 'RTRW service is disabled');
        }

        $provinceConfig = config("rtrw.provinces.{$provinceCode}");
        if (!$provinceConfig) {
            abort(404, 'Province not found');
        }

        $cacheKey = sprintf('rtrw:tile:%s:%s:%dx%d', $provinceCode, md5($validated['bbox']), $validated['width'], $validated['height']);

        if (config('rtrw.cache.enabled')) {
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                return response($cached, 200)->header('Content-Type', 'image/png')->header('Cache-Control', 'public, max-age=3600');
            }
        }

        try {
            $url = $this->buildProxiedUrl($provinceConfig['path'] . '/MapServer/export', [
                'bbox' => $validated['bbox'],
                'bboxSR' => config('rtrw.spatial_reference'),
                'imageSR' => config('rtrw.spatial_reference'),
                'size' => $validated['width'] . ',' . $validated['height'],
                'dpi' => 96,
                'format' => 'png32',
                'transparent' => 'true',
                'f' => 'image',
            ]);

            $response = Http::timeout(config('rtrw.http.timeout'))
                ->connectTimeout(config('rtrw.http.connect_timeout'))
                ->withHeaders(['User-Agent' => config('rtrw.http.user_agent')])
                ->get($url);

            if (!$response->successful()) {
                abort(502, 'GISTARU export failed');
            }

            $imageData = $response->body();

            if (config('rtrw.cache.enabled')) {
                Cache::put($cacheKey, $imageData, config('rtrw.cache.ttl_zone_query'));
            }

            return response($imageData, 200)
                ->header('Content-Type', 'image/png')
                ->header('Cache-Control', 'public, max-age=3600');

        } catch (\Exception $e) {
            Log::warning('RTRW map export failed', [
                'province' => $provinceCode,
                'error' => $e->getMessage(),
            ]);
            abort(502, 'Map export failed');
        }
    }

    /**
     * Query all layers in a MapServer for a given point.
     */
    private function queryZona(string $servicePath, float $lat, float $lng): array
    {
        $url = $this->buildProxiedUrl($servicePath . '/MapServer/identify', [
                'geometry' => "{$lng},{$lat}",
                'geometryType' => 'esriGeometryPoint',
                'sr' => config('rtrw.spatial_reference'),
                'layers' => 'all',
                'tolerance' => 1,
                'mapExtent' => sprintf('%.6f,%.6f,%.6f,%.6f', $lng - 0.01, $lat - 0.01, $lng + 0.01, $lat + 0.01),
                'imageDisplay' => '800,800,96',
                'returnGeometry' => 'false',
                'f' => 'json',
            ]);

        $response = Http::timeout(config('rtrw.http.timeout'))
            ->connectTimeout(config('rtrw.http.connect_timeout'))
            ->withHeaders(['User-Agent' => config('rtrw.http.user_agent')])
            ->get($url);

        if (!$response->successful()) {
            throw new \RuntimeException('GISTARU identify returned HTTP ' . $response->status());
        }

        $data = $response->json();

        if (isset($data['error']) && empty($data['results'])) {
            throw new \RuntimeException($data['error']['message'] ?? 'GISTARU error');
        }

        $zones = [];
        foreach ($data['results'] ?? [] as $result) {
            $attrs = $result['attributes'] ?? [];
            $zones[] = [
                'layer_id' => $result['layerId'] ?? null,
                'layer_name' => $result['layerName'] ?? null,
                'zona' => $attrs['Nama Objek'] ?? $attrs['NAMOBJ'] ?? $attrs['NAMZON'] ?? null,
                'jenis_zona' => $attrs['Jenis Rencana Pola Ruang'] ?? null,
                'kabupaten_kota' => $attrs['Wilayah Administrasi Kabupaten/Kota'] ?? $attrs['WADMKK'] ?? null,
                'kecamatan' => $attrs['Wilayah Administrasi Kecamatan'] ?? $attrs['WADMKC'] ?? null,
                'provinsi' => $attrs['Wilayah Administrasi Provinsi'] ?? $attrs['WADMPR'] ?? null,
                'no_perda' => $attrs['Nomor dan Tahun Peraturan'] ?? $attrs['NOTHPR'] ?? null,
                'remark' => $attrs['Catatan'] ?? $attrs['REMARK'] ?? null,
            ];
        }

        return $zones;
    }

    /**
     * Build a proxied URL for GISTARU with query params correctly placed.
     *
     * GISTARU proxy format: run.ashx?{inner_arcgis_url_with_params}
     * The inner URL must have its own ?key=value params — they cannot be
     * appended with & to the outer proxy URL.
     */
    private function buildProxiedUrl(string $servicePath, array $params = []): string
    {
        $innerUrl = config('rtrw.arcgis_base') . $servicePath;

        if (!empty($params)) {
            $innerUrl .= '?' . http_build_query($params);
        }

        return config('rtrw.proxy_base') . $innerUrl;
    }
}
