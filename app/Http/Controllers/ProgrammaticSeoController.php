<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\SchemaMarkupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProgrammaticSeoController extends Controller
{
    /**
     * Location + Service landing page
     * URL: /layanan/{service-slug}/{city-slug}
     * e.g. /layanan/perizinan-lb3/karawang
     */
    public function serviceLocation(string $serviceSlug, string $citySlug)
    {
        $config = config('programmatic_seo');
        if (!$config['enabled']) {
            abort(404);
        }

        $cities = $config['cities'] ?? [];
        $serviceKeys = $config['services'] ?? [];

        if (!isset($cities[$citySlug]) || !in_array($serviceSlug, $serviceKeys)) {
            abort(404);
        }

        $city = $cities[$citySlug];
        $allServices = config('services_data');

        if (!isset($allServices[$serviceSlug])) {
            abort(404);
        }

        $service = $allServices[$serviceSlug];
        $year = $config['current_year'];
        $whatsappLink = data_get(config('landing_metrics'), 'contact.whatsapp_link', 'https://wa.me/6283879602855');

        // Build unique page data
        $pageData = $this->buildPageData($service, $city, $year, $whatsappLink);

        // Get related articles for this service+city combo
        $relatedArticles = Cache::remember(
            "pseo.articles.{$serviceSlug}.{$citySlug}",
            $config['cache_ttl'] * 60,
            function () use ($service) {
                $keywords = explode(',', $service['meta_keywords'] ?? '');
                $keywords = array_map('trim', $keywords);

                return Article::published()
                    ->byLanguage('id')
                    ->where(function ($q) use ($keywords, $service) {
                        foreach (array_slice($keywords, 0, 3) as $kw) {
                            $q->orWhere('title', 'LIKE', "%{$kw}%");
                        }
                        $q->orWhere('title', 'LIKE', '%' . ($service['title'] ?? '') . '%');
                    })
                    ->orderBy('views_count', 'desc')
                    ->take(4)
                    ->get();
            }
        );

        // Get other cities for cross-linking
        $otherCities = collect($cities)
            ->except($citySlug)
            ->take(6);

        // Get related services for cross-linking
        $relatedServices = collect($allServices)
            ->except($serviceSlug)
            ->filter(fn($s, $k) => in_array($k, $serviceKeys))
            ->take(4);

        return view('programmatic.service-location', compact(
            'service', 'city', 'pageData', 'year',
            'relatedArticles', 'otherCities', 'relatedServices',
            'serviceSlug', 'citySlug', 'whatsappLink'
        ));
    }

    /**
     * City index page listing all services available
     * URL: /layanan/kota/{city-slug}
     */
    public function cityIndex(string $citySlug)
    {
        $config = config('programmatic_seo');
        if (!$config['enabled']) {
            abort(404);
        }

        $cities = $config['cities'] ?? [];
        if (!isset($cities[$citySlug])) {
            abort(404);
        }

        $city = $cities[$citySlug];
        $allServices = config('services_data');
        $serviceKeys = $config['services'] ?? [];
        $year = $config['current_year'];
        $whatsappLink = data_get(config('landing_metrics'), 'contact.whatsapp_link', 'https://wa.me/6283879602855');

        $services = collect($allServices)
            ->filter(fn($s, $k) => in_array($k, $serviceKeys));

        $otherCities = collect($cities)->except($citySlug)->take(8);

        return view('programmatic.city-index', compact(
            'city', 'services', 'year', 'otherCities', 'citySlug', 'whatsappLink'
        ));
    }

    /**
     * Build unique, valuable page content for service+city
     */
    protected function buildPageData(array $service, array $city, int $year, string $whatsappLink): array
    {
        $cityName = $city['name'];
        $serviceTitle = $service['title'];
        $industries = $city['industries'] ?? [];
        $zones = $city['industrial_zones'] ?? [];

        return [
            'title' => "Jasa {$serviceTitle} {$cityName} {$year} - Konsultan Berpengalaman",
            'meta_title' => "Jasa {$serviceTitle} {$cityName} {$year} | Bizmark.ID",
            'meta_description' => "Butuh jasa {$serviceTitle} di {$cityName}? Bizmark.ID adalah konsultan perizinan berpengalaman melayani " . implode(', ', array_slice($industries, 0, 3)) . " dan lainnya. Proses cepat & transparan.",
            'meta_keywords' => "jasa {$serviceTitle} {$cityName}, konsultan {$serviceTitle} {$cityName}, " . ($service['meta_keywords'] ?? '') . ", perizinan {$cityName}",
            'h1' => "Jasa {$serviceTitle} di {$cityName} {$year}",
            'intro' => "Mencari jasa profesional {$serviceTitle} di {$cityName}, {$city['province']}? Bizmark.ID menyediakan layanan konsultasi dan pengurusan {$serviceTitle} untuk perusahaan industri di {$cityName} dan sekitarnya. " . $city['description'],
            'industries_text' => "Kami melayani berbagai sektor industri di {$cityName}, termasuk: " . implode(', ', $industries) . ".",
            'zones_text' => count($zones) > 0
                ? "Layanan kami mencakup perusahaan di kawasan industri " . implode(', ', array_slice($zones, 0, 4)) . " dan area industri lainnya di {$cityName}."
                : '',
            'cta_whatsapp' => $whatsappLink . '?text=' . urlencode($city['whatsapp_text'] ?? "Halo Bizmark, saya butuh {$serviceTitle} di {$cityName}"),
            'government_office' => $city['government_office'] ?? '',

            // FAQ items (unique per city+service)
            'faqs' => $this->generateFaqs($service, $city, $year),

            // Schema data
            'schema_service' => [
                '@context' => 'https://schema.org',
                '@type' => 'Service',
                'name' => "Jasa {$serviceTitle} {$cityName}",
                'description' => $service['long_description'] ?? $service['short_description'] ?? '',
                'provider' => [
                    '@type' => 'Organization',
                    'name' => 'Bizmark.ID',
                    'url' => config('app.url'),
                ],
                'areaServed' => [
                    '@type' => 'City',
                    'name' => $cityName,
                    'containedInPlace' => [
                        '@type' => 'State',
                        'name' => $city['province'],
                    ],
                ],
                'serviceType' => $serviceTitle,
            ],
        ];
    }

    /**
     * Generate unique FAQ items per city+service
     */
    protected function generateFaqs(array $service, array $city, int $year): array
    {
        $cityName = $city['name'];
        $serviceTitle = $service['title'];
        $province = $city['province'];
        $govOffice = $city['government_office'] ?? "Dinas terkait di {$cityName}";

        return [
            [
                'question' => "Berapa biaya jasa {$serviceTitle} di {$cityName}?",
                'answer' => "Biaya jasa {$serviceTitle} di {$cityName} bervariasi tergantung skala usaha, jenis industri, dan kompleksitas perizinan. Hubungi kami untuk konsultasi gratis dan penawaran yang disesuaikan dengan kebutuhan perusahaan Anda.",
            ],
            [
                'question' => "Berapa lama proses {$serviceTitle} di {$cityName}?",
                'answer' => "Waktu proses {$serviceTitle} di {$cityName} umumnya berkisar 1-3 bulan, tergantung kelengkapan dokumen dan koordinasi dengan {$govOffice}. Bizmark.ID membantu mempercepat proses dengan pendampingan intensif.",
            ],
            [
                'question' => "Apa saja dokumen yang dibutuhkan untuk {$serviceTitle} di {$cityName}?",
                'answer' => "Dokumen yang dibutuhkan termasuk NIB, akta perusahaan, NPWP, dokumen teknis sesuai jenis izin, serta dokumen pendukung lainnya. Tim kami akan memberikan checklist lengkap sesuai kebutuhan spesifik Anda di {$cityName}.",
            ],
            [
                'question' => "Apakah Bizmark.ID melayani {$serviceTitle} untuk kawasan industri di {$cityName}?",
                'answer' => "Ya, kami melayani seluruh kawasan industri di {$cityName}" . (count($city['industrial_zones'] ?? []) > 0 ? " termasuk " . implode(', ', array_slice($city['industrial_zones'], 0, 3)) : '') . ". Tim kami berpengalaman menangani berbagai jenis industri.",
            ],
            [
                'question' => "Bagaimana cara memulai pengurusan {$serviceTitle} di {$cityName}?",
                'answer' => "Hubungi Bizmark.ID melalui WhatsApp atau formulir kontak untuk konsultasi awal. Kami akan menganalisis kebutuhan perizinan Anda, menyiapkan dokumen, dan mendampingi seluruh proses hingga izin diterbitkan oleh {$govOffice}.",
            ],
        ];
    }
}
