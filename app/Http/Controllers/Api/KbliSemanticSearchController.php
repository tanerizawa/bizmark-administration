<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kbli;
use App\Services\KbliSemanticSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KbliSemanticSearchController extends Controller
{
    public function __construct(private readonly KbliSemanticSearchService $searchService) {}

    /**
     * POST /api/kbli/semantic-search
     *
     * Body: { "query": "usaha laundry pakaian", "limit": 5 }
     * Returns top-N KBLI matches with similarity scores + AI explanation.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'min:3', 'max:500'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:10'],
        ]);

        $result = $this->searchService->search(
            query: $validated['query'],
            limit: $validated['limit'] ?? 5,
            ip: $request->ip(),
        );

        return response()->json([
            'success' => true,
            'query' => $result['query'],
            'results' => $result['results'],
            'explanation' => $result['explanation'],
            'latency_ms' => $result['latency_ms'],
            'source' => $result['source'],
        ]);
    }

    /**
     * GET /api/v2/kbli/{code}
     * Returns KBLI detail by 5-digit code.
     */
    public function show(string $code): JsonResponse
    {
        $kbli = Kbli::where('code', $code)->where('is_active', true)->first();

        if (! $kbli) {
            return response()->json(['error' => 'KBLI not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => $kbli]);
    }
}
