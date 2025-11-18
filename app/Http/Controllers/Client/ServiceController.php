<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Kbli;
use App\Models\BusinessContext;
use App\Services\KbliPermitCacheService;
use App\Services\ConsultantFeeCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class ServiceController extends Controller
{
    protected KbliPermitCacheService $cacheService;
    protected ConsultantFeeCalculatorService $feeCalculator;

    public function __construct(
        KbliPermitCacheService $cacheService,
        ConsultantFeeCalculatorService $feeCalculator
    ) {
        $this->middleware('auth:client');
        $this->cacheService = $cacheService;
        $this->feeCalculator = $feeCalculator;
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

        // Get total KBLI count
        $totalKbli = Kbli::count();

        return view('client.services.index', compact('sectors', 'popularKbli', 'totalKbli'));
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

        // Check if we have stored context in session
        $contextData = session('business_context');
        $contextArray = null;

        if ($contextData) {
            $businessScale = $contextData['business_scale'] ?? $businessScale;
            $locationType = $contextData['location_category'] ?? $locationType;
            $contextArray = $contextData;
        }

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

        // Calculate consultant fees if we have context data
        $costBreakdown = null;
        if ($contextArray && $recommendation->recommended_permits) {
            try {
                $costBreakdown = $this->feeCalculator->generateCostBreakdown(
                    $recommendation->recommended_permits,
                    $contextArray
                );
                
                Log::info('Cost breakdown calculated', [
                    'kbli_code' => $kbliCode,
                    'consultant_fees' => $costBreakdown['consultant_fees'],
                    'government_fees' => $costBreakdown['government_fees'],
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to calculate consultant fees', [
                    'kbli_code' => $kbliCode,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Format cost breakdown for display
        $formattedCosts = null;
        if ($costBreakdown) {
            $formattedCosts = $this->feeCalculator->formatCostBreakdown($costBreakdown);
        }

        return view('client.services.show', compact(
            'kbli',
            'recommendation',
            'businessScale',
            'locationType',
            'costBreakdown',
            'formattedCosts',
            'contextArray'
        ));
    }

    /**
     * Show business context form (optional step)
     */
    public function context(string $kbliCode)
    {
        $kbli = Kbli::where('code', $kbliCode)->firstOrFail();
        
        return view('client.services.context', compact('kbli'));
    }

    /**
     * Store business context and redirect to recommendations
     */
    public function storeContext(Request $request, string $kbliCode)
    {
        $kbli = Kbli::where('code', $kbliCode)->firstOrFail();
        $clientId = auth('client')->id();

        // Validate comprehensive context data
        $validated = $request->validate([
            // Project Scale
            'land_area' => 'required|numeric|min:0',
            'building_area' => 'nullable|numeric|min:0',
            'number_of_floors' => 'nullable|integer|min:1|max:100',
            'investment_value' => 'nullable|numeric|min:0',
            
            // Location Details
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'zone_type' => 'nullable|in:residential,commercial,industrial,mixed,special',
            'location_category' => 'required|in:perkotaan,pedesaan,kawasan_industri',
            
            // Business Details
            'business_scale' => 'nullable|in:mikro,kecil,menengah,besar',
            'number_of_employees' => 'nullable|integer|min:0',
            'production_capacity' => 'nullable|string|max:100',
            'annual_revenue_target' => 'nullable|numeric|min:0',
            
            // Environmental Factors
            'environmental_impact' => 'required|in:low,medium,high',
            'waste_management' => 'nullable|in:minimal,standard,complex',
            'near_protected_area' => 'nullable|boolean',
            'environmental_notes' => 'nullable|string|max:1000',
            
            // Legal Status
            'ownership_status' => 'nullable|in:owned,leased,partnership',
            'urgency_level' => 'nullable|in:standard,rush',
            
            // Additional
            'additional_notes' => 'nullable|string|max:2000',
        ]);

        try {
            // Store in database for future reference
            $context = BusinessContext::create(array_merge($validated, [
                'client_id' => $clientId,
                'kbli_code' => $kbliCode,
                'near_protected_area' => $request->has('near_protected_area'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'submitted_at' => now(),
            ]));

            // Also store in session for immediate use
            session(['business_context' => $validated]);

            Log::info('Business context stored', [
                'context_id' => $context->id,
                'client_id' => $clientId,
                'kbli_code' => $kbliCode,
            ]);

            return redirect()->route('client.services.show', $kbliCode)
                ->with('success', 'Data konteks bisnis berhasil disimpan. Estimasi biaya yang lebih akurat sedang dihitung.');
                
        } catch (\Exception $e) {
            Log::error('Failed to store business context', [
                'kbli_code' => $kbliCode,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data konteks. Silakan coba lagi.');
        }
    }

    /**
     * Download PDF summary of permit recommendations
     */
    public function downloadSummary(Request $request, string $kbliCode)
    {
        $kbli = Kbli::where('code', $kbliCode)->firstOrFail();
        
        $businessScale = $request->get('scale');
        $locationType = $request->get('location');
        $clientId = auth('client')->id();
        $client = auth('client')->user();

        // Check session context
        $contextData = session('business_context');
        $contextArray = null;

        if ($contextData) {
            $businessScale = $contextData['business_scale'] ?? $businessScale;
            $locationType = $contextData['location_category'] ?? $locationType;
            $contextArray = $contextData;
        }

        // Get recommendations
        $recommendation = $this->cacheService->getRecommendations(
            $kbliCode,
            $businessScale,
            $locationType,
            $clientId
        );

        if (!$recommendation) {
            return back()->with('error', 'Gagal menghasilkan rekomendasi perizinan.');
        }

        // Calculate costs if context available
        $costBreakdown = null;
        $formattedCosts = null;
        
        if ($contextArray && $recommendation->recommended_permits) {
            try {
                $costBreakdown = $this->feeCalculator->generateCostBreakdown(
                    $recommendation->recommended_permits,
                    $contextArray
                );
                $formattedCosts = $this->feeCalculator->formatCostBreakdown($costBreakdown);
            } catch (\Exception $e) {
                Log::error('Failed to calculate fees for PDF', [
                    'kbli_code' => $kbliCode,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Generate metadata
        $metadata = [
            'document_number' => 'BIZ/ANALISIS/' . strtoupper($kbliCode) . '/' . now()->format('Ymd') . '/' . str_pad($clientId, 5, '0', STR_PAD_LEFT),
            'generated_date' => now()->locale('id')->isoFormat('D MMMM YYYY'),
            'generated_time' => now()->format('H:i:s'),
            'validity_period' => now()->addMonths(3)->locale('id')->isoFormat('D MMMM YYYY'),
        ];

        // Prepare data
        $data = compact(
            'client',
            'kbli',
            'recommendation',
            'businessScale',
            'locationType',
            'costBreakdown',
            'formattedCosts',
            'contextArray',
            'metadata'
        );

        // Generate PDF
        $pdf = Pdf::loadView('client.services.summary-pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', '10mm')
            ->setOption('margin-bottom', '10mm')
            ->setOption('margin-left', '10mm')
            ->setOption('margin-right', '10mm');

        // Filename
        $filename = 'Analisis_Perizinan_KBLI_' . $kbliCode . '_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }
}

