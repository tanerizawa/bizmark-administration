<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = config('services_data');
        
        // Detect mobile
        $isMobile = $request->header('User-Agent') && 
                   (preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $request->header('User-Agent')));
        
        $view = $isMobile ? 'services.mobile-index' : 'services.index';
        
        return view($view, [
            'services' => $services,
            'title' => 'Layanan Kami - Bizmark.ID',
            'meta_description' => 'Layanan lengkap perizinan industri: Limbah B3, AMDAL, UKL-UPL, OSS NIB, PBG/SLF, Izin Operasional, Konsultan Lingkungan, dan Monitoring Digital'
        ]);
    }

    public function show(Request $request, $slug)
    {
        $services = config('services_data');
        
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
        
        $view = $isMobile ? 'services.mobile-show' : 'services.show';
        
        return view($view, [
            'service' => $service,
            'relatedServices' => $relatedServices,
            'title' => $service['title'] . ' - Bizmark.ID',
            'meta_description' => $service['short_description']
        ]);
    }
}
