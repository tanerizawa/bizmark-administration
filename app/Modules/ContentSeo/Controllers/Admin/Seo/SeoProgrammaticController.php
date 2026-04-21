<?php

namespace App\Modules\ContentSeo\Controllers\Admin\Seo;

use App\Modules\ContentSeo\Controllers\Admin\Concerns\SeoAdminFlashRedirect;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SeoProgrammaticController extends Controller
{
    use SeoAdminFlashRedirect;

    /**
     * Programmatic SEO stats page
     */
    public function programmatic(Request $request)
    {
        $config = config('programmatic_seo');
        $cities = $config['cities'] ?? [];
        $serviceSlugs = $config['services'] ?? [];
        $servicesData = config('services_data', []);

        // Enrich services with name/icon from services_data
        $services = collect($serviceSlugs)->map(function ($slug) use ($servicesData) {
            $data = $servicesData[$slug] ?? [];
            return [
                'slug' => $slug,
                'title' => $data['title'] ?? ucfirst(str_replace('-', ' ', $slug)),
                'icon' => $data['icon'] ?? 'fa-cog',
                'color' => $data['color'] ?? '#6B7280',
                'category' => $data['category'] ?? '-',
            ];
        });

        // Filter by province if requested
        $provinceFilter = $request->get('province');
        $filteredCities = $cities;
        if ($provinceFilter) {
            $filteredCities = collect($cities)->filter(fn($c) => ($c['province'] ?? '') === $provinceFilter)->all();
        }

        $stats = [
            'total_cities' => count($cities),
            'total_services' => $services->count(),
            'total_pages' => count($cities) * $services->count() + count($cities),
            'service_location_pages' => count($cities) * $services->count(),
            'city_index_pages' => count($cities),
        ];

        // Group cities by province
        $byProvince = collect($cities)->groupBy('province')->map->count()->sortDesc();

        return view('admin.seo.programmatic', compact('stats', 'cities', 'filteredCities', 'services', 'byProvince', 'provinceFilter'));
    }

    /**
     * Clear programmatic SEO cache
     */
    public function clearProgrammaticCache()
    {
        $cleared = 0;
        $config = config('programmatic_seo');
        $cities = $config['cities'] ?? [];
        $services = $config['services'] ?? [];

        foreach ($cities as $city) {
            $slug = $city['slug'] ?? '';
            if ($slug && \Illuminate\Support\Facades\Cache::forget("programmatic_city_{$slug}")) {
                $cleared++;
            }
            foreach ($services as $svc) {
                if (\Illuminate\Support\Facades\Cache::forget("programmatic_{$svc}_{$slug}")) {
                    $cleared++;
                }
            }
        }

        return $this->seoRouteFlash('admin.seo.programmatic', 'success', "Cache programmatic SEO di-clear. {$cleared} entri dihapus.");
    }
}

