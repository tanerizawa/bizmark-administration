<?php

namespace App\Http\Controllers\Api;

use App\Services\CivicStackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CivicStackController — proxies requests to the civic_stack microservice.
 *
 * All endpoints are public (throttled by route middleware).
 * Returns 200 with {"found": false} when civic_stack is down or returns no data,
 * so the frontend can degrade gracefully without showing an error.
 */
class CivicStackController
{
    public function __construct(private readonly CivicStackService $civic) {}

    /**
     * GET /api/civic/simbg-hints?q={city}
     *
     * Building permit (PBG/IMB) hints for a given city.
     * Used in Step 2 (Lokasi Proyek) of the context form.
     */
    public function simbgHints(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['required', 'string', 'min:3', 'max:100'],
        ]);

        $data = $this->civic->simbgSearch($request->string('q')->trim()->toString());

        return response()->json($data ?? ['found' => false]);
    }

    /**
     * GET /api/civic/bpjph-check?q={company_name}
     *
     * Halal certification lookup for a company/product name.
     * Used in Step 3 (Detail Bisnis) — only for F&B KBLI sectors.
     */
    public function bpjphCheck(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['required', 'string', 'min:3', 'max:200'],
        ]);

        $data = $this->civic->bpjphSearch($request->string('q')->trim()->toString());

        return response()->json($data ?? ['found' => false]);
    }

    /**
     * GET /api/civic/nib-lookup?q={nib_or_company}
     *
     * NIB (business registration) lookup via OSS portal.
     * Used above Step 1 to auto-fill business_scale, province, city.
     */
    public function nibLookup(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['required', 'string', 'min:3', 'max:200'],
        ]);

        $data = $this->civic->nibLookup($request->string('q')->trim()->toString());

        return response()->json($data ?? ['found' => false]);
    }

    /**
     * GET /api/civic/jdih-search?q={keyword}&type={pp|uu|perpres|permen}
     *
     * Indonesian legal regulations search.
     * Used in Step 4 (Konfirmasi) to display relevant laws for the KBLI.
     */
    public function jdihSearch(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:200'],
            'type' => ['sometimes', 'string', 'in:uu,pp,perpres,permen,perda,kepmen'],
        ]);

        $data = $this->civic->jdihSearch(
            keyword: $request->string('q')->trim()->toString(),
            type: $request->string('type', 'pp')->toString(),
            limit: 5,
        );

        return response()->json($data ?? ['found' => false]);
    }
}
