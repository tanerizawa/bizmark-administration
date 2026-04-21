<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $marketSegment = session('market_segment', 'local');

        // Load appropriate services config based on locale first, then market segment
        // Indonesian locale always uses services_data
        $services = $locale === 'id'
            ? config('services_data', [])
            : ($marketSegment === 'pma' ? config('services_pma', []) : config('services_data', []));

        // Group services by category (locale-aware fallback label)
        $defaultCategory = $locale === 'en' ? 'Other' : 'Lainnya';
        $groupedServices = [];
        foreach ($services as $slug => $service) {
            $category = $service['category'] ?? $defaultCategory;
            $groupedServices[$category][$slug] = $service;
        }

        // Select view based on locale and device
        if ($locale === 'en') {
            $view = 'services.en.index';  // English always uses desktop view
        } else {
            $view = 'services.index';
        }

        // Locale-aware metadata
        $title = $locale === 'en'
            ? 'Our Services - Bizmark.ID'
            : 'Layanan Kami - Bizmark.ID';

        $meta_description = $locale === 'en'
            ? 'Complete investment and compliance services for foreign investors in Indonesia: BKPM approval, company establishment, work permits, environmental permits, and ongoing support'
            : 'Layanan lengkap perizinan industri: Limbah B3, AMDAL, UKL-UPL, OSS NIB, PBG/SLF, Izin Operasional, Konsultan Lingkungan, dan Monitoring Digital';

        return view($view, [
            'services' => $services,
            'groupedServices' => $groupedServices,
            'title' => $title,
            'meta_description' => $meta_description,
            'locale' => $locale,
            'marketSegment' => $marketSegment,
        ]);
    }

    public function show(Request $request, $slug)
    {
        $locale = app()->getLocale();
        $marketSegment = session('market_segment', 'local');

        // Load appropriate services config based on locale first
        // Indonesian locale always uses services_data
        $services = $locale === 'id'
            ? config('services_data', [])
            : ($marketSegment === 'pma' ? config('services_pma', []) : config('services_data', []));

        if (! isset($services[$slug])) {
            abort(404);
        }

        $service = $services[$slug];

        // Category-based related services: same category first, then others
        $sameCategory = array_filter($services, function ($s, $key) use ($slug, $service) {
            return $key !== $slug && ($s['category'] ?? '') === ($service['category'] ?? '');
        }, ARRAY_FILTER_USE_BOTH);
        $otherServices = array_filter($services, function ($s, $key) use ($slug, $service) {
            return $key !== $slug && ($s['category'] ?? '') !== ($service['category'] ?? '');
        }, ARRAY_FILTER_USE_BOTH);
        $relatedServices = array_slice($sameCategory + $otherServices, 0, 3, true);

        // Testimonial mapping by category
        $testimonials = config('landing.testimonials', []);
        $categoryTestimonialMap = [
            // Indonesian categories
            'BANGUNAN' => 0,
            'INDUSTRI' => 1,
            'LINGKUNGAN' => 2,
            'PERIZINAN USAHA' => 3,
            'TEKNOLOGI' => 4,
            'K3' => 1,
            // English (PMA) categories
            'INVESTMENT' => 0,
            'LEGAL' => 0,
            'TAX' => 1,
            'IMMIGRATION' => 3,
            'ENVIRONMENT' => 2,
            'PROPERTY' => 3,
            'OPERATIONS' => 1,
            'COMPLIANCE' => 4,
        ];
        $testimonialIndex = $categoryTestimonialMap[$service['category'] ?? ''] ?? 0;
        $testimonial = $testimonials[$testimonialIndex] ?? ($testimonials[0] ?? null);

        // Select view based on locale and device
        if ($locale === 'en') {
            $view = 'services.en.show';  // English always uses desktop view
        } else {
            $view = 'services.show';
        }

        // Enhanced SEO meta for specific high-value services
        $metaOverrides = [
            'amdal' => [
                'title' => 'Jasa Pengurusan AMDAL — Tenaga Ahli Bersertifikat, Standar Teknis Terjamin | Bizmark.ID',
                'meta_description' => 'Jasa pengurusan AMDAL oleh tenaga ahli bersertifikat KTPA dengan metodologi analisis terstandar. Pengurangan risiko penolakan 95% — dari KA-ANDAL, ANDAL, RKL-RPL hingga persetujuan lingkungan. Konsultasi langsung dengan ahli gratis!',
            ],
        ];

        $meta = $metaOverrides[$slug] ?? [];

        return view($view, [
            'service' => $service,
            'relatedServices' => $relatedServices,
            'testimonial' => $testimonial,
            'title' => $meta['title'] ?? ($service['title'].' - Bizmark.ID'),
            'meta_description' => $meta['meta_description'] ?? $service['short_description'],
            'locale' => $locale,
            'marketSegment' => $marketSegment,
        ]);
    }

    public function showSub(Request $request, $serviceSlug, $subSlug)
    {
        $locale = app()->getLocale();
        $marketSegment = session('market_segment', 'local');

        // EN services have no sub_services — redirect to parent service
        if ($locale === 'en') {
            return redirect()->route('services.show.en', $serviceSlug);
        }

        $services = config('services_data');

        if (! isset($services[$serviceSlug])) {
            abort(404);
        }

        $parentService = $services[$serviceSlug];
        $subServices = $parentService['sub_services'] ?? [];

        if (! isset($subServices[$subSlug])) {
            abort(404);
        }

        $subService = $subServices[$subSlug];

        // Build related sub-services (siblings)
        $relatedSubs = array_filter($subServices, fn ($key) => $key !== $subSlug, ARRAY_FILTER_USE_KEY);
        $relatedSubs = array_slice($relatedSubs, 0, 4, true);

        // Related parent services (same category first, then others)
        $sameCategory = array_filter($services, function ($s, $key) use ($serviceSlug, $parentService) {
            return $key !== $serviceSlug && ($s['category'] ?? '') === ($parentService['category'] ?? '');
        }, ARRAY_FILTER_USE_BOTH);
        $otherServices = array_filter($services, function ($s, $key) use ($serviceSlug, $parentService) {
            return $key !== $serviceSlug && ($s['category'] ?? '') !== ($parentService['category'] ?? '');
        }, ARRAY_FILTER_USE_BOTH);
        $relatedServices = array_slice($sameCategory + $otherServices, 0, 3, true);

        $view = 'services.sub-show';

        $title = $subService['title'].' | '.$parentService['title'].' - Bizmark.ID';
        $metaDesc = $subService['short_description'];

        return view($view, [
            'parentService' => $parentService,
            'parentSlug' => $serviceSlug,
            'subService' => $subService,
            'subSlug' => $subSlug,
            'relatedSubs' => $relatedSubs,
            'relatedServices' => $relatedServices,
            'title' => $title,
            'meta_description' => $metaDesc,
            'locale' => $locale,
            'marketSegment' => $marketSegment,
        ]);
    }
}
