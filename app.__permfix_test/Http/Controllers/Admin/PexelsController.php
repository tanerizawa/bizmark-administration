<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PexelsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PexelsController extends Controller
{
    protected $pexelsService;

    public function __construct(PexelsService $pexelsService)
    {
        $this->pexelsService = $pexelsService;
    }

    /**
     * Search photos on Pexels
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:80',
            'orientation' => 'nullable|in:landscape,portrait,square',
            'size' => 'nullable|in:large,medium,small',
            'color' => 'nullable|string',
        ]);

        $query = $request->input('query');
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 15);

        $filters = [];
        if ($request->filled('orientation')) {
            $filters['orientation'] = $request->input('orientation');
        }
        if ($request->filled('size')) {
            $filters['size'] = $request->input('size');
        }
        if ($request->filled('color')) {
            $filters['color'] = $request->input('color');
        }

        $result = $this->pexelsService->searchPhotos($query, $perPage, $page, $filters);

        if ($result === null) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari foto dari Pexels. Silakan coba lagi.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Get curated photos
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function curated(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 15);

        $result = $this->pexelsService->getCuratedPhotos($perPage, $page);

        if ($result === null) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil foto curated dari Pexels.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Download photo from Pexels and save to storage
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function download(Request $request)
    {
        $request->validate([
            'photo_id' => 'required|integer',
            'photo_url' => 'required|url',
            'photographer_name' => 'required|string',
            'photographer_url' => 'required|url',
            'pexels_url' => 'required|url',
        ]);

        try {
            $photoId = $request->input('photo_id');
            $photoUrl = $request->input('photo_url');
            $photographerName = $request->input('photographer_name');
            $photographerUrl = $request->input('photographer_url');
            $pexelsUrl = $request->input('pexels_url');

            // Download and save photo
            $path = $this->pexelsService->downloadAndSavePhoto(
                $photoUrl,
                $photographerName,
                $photoId
            );

            if ($path === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengunduh foto dari Pexels.',
                ], 500);
            }

            // Get attribution text
            $attribution = $this->pexelsService->getAttributionText(
                $photographerName,
                $photographerUrl,
                $pexelsUrl
            );

            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => asset('storage/'.$path),
                'attribution' => $attribution,
                'message' => 'Foto berhasil diunduh dari Pexels.',
            ]);
        } catch (\Exception $e) {
            Log::error('Pexels download controller exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengunduh foto.',
            ], 500);
        }
    }
}
