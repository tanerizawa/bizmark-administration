<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\KbliService;
use Illuminate\Http\Request;

class KbliController extends Controller
{
    protected $kbliService;
    
    public function __construct(KbliService $kbliService)
    {
        $this->kbliService = $kbliService;
    }
    
    /**
     * Search KBLI
     * GET /api/kbli/search?q={keyword}
     */
    public function search(Request $request)
    {
        $keyword = $request->get('q', '');
        
        if (strlen($keyword) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Keyword minimal 2 karakter',
                'data' => []
            ]);
        }
        
        $results = $this->kbliService->search($keyword);
        
        // Limit results to 20 for performance
        $results = array_slice($results, 0, 20);
        
        return response()->json([
            'success' => true,
            'data' => array_values($results),
            'count' => count($results)
        ]);
    }
    
    /**
     * Get KBLI by code
     * GET /api/kbli/{code}
     */
    public function show(string $code)
    {
        $kbli = $this->kbliService->getByCode($code);
        
        if (!$kbli) {
            return response()->json([
                'success' => false,
                'message' => 'KBLI tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $kbli
        ]);
    }
    
    /**
     * Get all KBLI (with pagination)
     * GET /api/kbli
     */
    public function index(Request $request)
    {
        $category = $request->get('category');
        $sector = $request->get('sector');
        
        if ($category) {
            $data = $this->kbliService->getByCategory($category);
        } elseif ($sector) {
            $data = $this->kbliService->getBySector($sector);
        } else {
            $data = $this->kbliService->getAll();
        }
        
        return response()->json([
            'success' => true,
            'data' => array_values($data),
            'count' => count($data)
        ]);
    }
}

