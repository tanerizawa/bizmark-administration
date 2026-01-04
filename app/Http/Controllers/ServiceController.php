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
            ? config('services_data')
            : ($marketSegment === 'pma' ? config('services_pma') : config('services_data'));
        
        // Detect mobile
        $isMobile = $request->header('User-Agent') && 
                   (preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $request->header('User-Agent')));
        
        // Select view based on locale and device
        if ($locale === 'en') {
            $view = 'services.en.index';  // English always uses desktop view
        } else {
            $view = $isMobile ? 'services.mobile-index' : 'services.index';
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
            ? config('services_data')
            : ($marketSegment === 'pma' ? config('services_pma') : config('services_data'));
        
        if (!isset($services[$slug])) {
            abort(404);
        }
        
        $service = $services[$slug];
        $relatedServices = array_filter($services, function($key) use ($slug) {
            return $key !== $slug;
        }, ARRAY_FILTER_USE_KEY);
        $relatedServices = array_slice($relatedServices, 0, 3);
        
        // Detect mobile
        $isMobile = $request->header('User-Agent') && 
                   (preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $request->header('User-Agent')));
        
        // Select view based on locale and device
        if ($locale === 'en') {
            $view = 'services.en.show';  // English always uses desktop view
        } else {
            $view = $isMobile ? 'services.mobile-show' : 'services.show';
        }
        
        return view($view, [
            'service' => $service,
            'relatedServices' => $relatedServices,
            'title' => $service['title'] . ' - Bizmark.ID',
            'meta_description' => $service['short_description'],
            'locale' => $locale,
            'marketSegment' => $marketSegment,
        ]);
    }
}
