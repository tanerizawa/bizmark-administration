<?php

namespace App\Http\Controllers;

use App\Models\PermitType;
use App\Services\TimelineSimulatorService;
use Illuminate\Http\Request;

/**
 * P9 — Permit Timeline Simulator (public tool at /simulasi-timeline).
 */
class TimelineSimulatorController extends Controller
{
    public function index()
    {
        $permitTypes = PermitType::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'typical_duration_days', 'category']);

        return view('tools.timeline-simulator.index', compact('permitTypes'));
    }

    public function simulate(Request $request, TimelineSimulatorService $simulator)
    {
        $request->validate([
            'permit_ids' => 'required|array|min:1|max:20',
            'permit_ids.*' => 'integer|exists:permit_types,id',
        ]);

        $result = $simulator->simulate($request->permit_ids);

        return response()->json($result);
    }
}
