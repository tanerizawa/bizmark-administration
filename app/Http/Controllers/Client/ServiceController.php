<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Kbli;
use App\Services\KbliPermitCacheService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    protected KbliPermitCacheService $cacheService;

    public function __construct(KbliPermitCacheService $cacheService)
    {
        $this->middleware('auth:client');
        $this->cacheService = $cacheService;
    }

    /**
     * Display KBLI selection page (new approach)
     */
    public function index(Request $request)
    {
        // Get sectors with KBLI counts for filtering/UI
        $sectors = Kbli::select('sector')
            ->selectRaw('COUNT(*) as total_kbli')
            ->groupBy('sector')
            ->orderBy('sector')
            ->get();

        // Get popular KBLI (most used in recommendations)
        // If no recommendations yet, show random sample
        $popularKbli = Kbli::select('kbli.code', 'kbli.description', 'kbli.sector')
            ->leftJoin('kbli_permit_recommendations', 'kbli.code', '=', 'kbli_permit_recommendations.kbli_code')
            ->selectRaw('COALESCE(SUM(kbli_permit_recommendations.cache_hits), 0) as cache_hits')
            ->groupBy('kbli.code', 'kbli.description', 'kbli.sector')
            ->orderByDesc('cache_hits')
            ->orderBy('kbli.code')
            ->limit(6)
            ->get();

        return view('client.services.index', compact('sectors', 'popularKbli'));
    }

    /**
     * Show permit recommendations for selected KBLI
     */
    public function show(Request $request, string $kbliCode)
    {
        $kbli = Kbli::where('code', $kbliCode)->firstOrFail();
        
        $businessScale = $request->get('scale');
        $locationType = $request->get('location');
        $clientId = auth('client')->id();

        // Get or generate recommendations
        $recommendation = $this->cacheService->getRecommendations(
            $kbliCode,
            $businessScale,
            $locationType,
            $clientId
        );

        if (!$recommendation) {
            return back()->with('error', 'Gagal menghasilkan rekomendasi perizinan. Silakan coba lagi.');
        }

        return view('client.services.show', compact('kbli', 'recommendation', 'businessScale', 'locationType'));
    }

    /**
     * Show business context form (optional step)
     */
    public function context(string $kbliCode)
    {
        $kbli = Kbli::where('code', $kbliCode)->firstOrFail();
        
        return view('client.services.context', compact('kbli'));
    }
}
