<?php

namespace App\Modules\Perizinan\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Kbli;
use App\Modules\Perizinan\Services\KbliService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class KbliController extends Controller
{
    use ApiResponse;

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
            return $this->error('Keyword minimal 2 karakter', 200, null);
        }

        $results = $this->kbliService->search($keyword);

        // Limit results to 20 for performance
        $results = array_slice($results, 0, 20);

        return $this->success(array_values($results), 'OK', 200, ['count' => count($results)]);
    }

    /**
     * Get KBLI by code
     * GET /api/kbli/{code}
     */
    public function show(string $code)
    {
        $kbli = $this->kbliService->getByCode($code);

        if (! $kbli) {
            return $this->notFound('KBLI tidak ditemukan');
        }

        return $this->success($kbli);
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

        return $this->success(array_values($data), 'OK', 200, ['count' => count($data)]);
    }

    /**
     * Get most popular KBLI codes (by usage)
     * GET /api/kbli/popular?limit={limit}
     */
    public function popular(Request $request)
    {
        $validated = $request->validate([
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $limit = $validated['limit'] ?? 10;

        try {
            // Cache for 1 hour
            $popular = Cache::remember("kbli_popular_{$limit}", 3600, function () use ($limit) {
                return Kbli::getPopular($limit)
                    ->map(function ($kbli) {
                        return [
                            'code' => $kbli->code,
                            'description' => $kbli->description,
                            'category' => $kbli->category,
                            'usage_count' => $kbli->usage_count,
                            'complexity_level' => $kbli->complexity_level,
                        ];
                    })
                    ->toArray();
            });

            return $this->success(array_values($popular), 'OK', 200, ['count' => count($popular)]);
        } catch (\Exception $e) {
            Log::error('KBLI popular error', ['error' => $e->getMessage()]);

            return $this->serverError('Gagal mengambil data KBLI populer');
        }
    }
}
