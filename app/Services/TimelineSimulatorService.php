<?php

namespace App\Services;

use App\Models\PermitType;

/**
 * P9 — Calculates permit acquisition timeline with parallelism analysis.
 *
 * Given a list of permit_type IDs, returns:
 * - Total estimated days (critical path)
 * - Gantt chart data (JSON-ready)
 * - AI tips for acceleration
 */
class TimelineSimulatorService
{
    public function __construct(private OpenRouterService $ai) {}

    /**
     * Simulate timeline for selected permit types.
     *
     * @param  int[]  $permitTypeIds
     * @return array { total_days, optimistic_days, pessimistic_days, phases, gantt, ai_tips }
     */
    public function simulate(array $permitTypeIds): array
    {
        $permits = PermitType::whereIn('id', $permitTypeIds)
            ->where('is_active', true)
            ->get();

        if ($permits->isEmpty()) {
            return $this->emptyResult();
        }

        $phases = $this->buildPhases($permits);
        $gantt = $this->buildGantt($phases);

        $totalDays = $this->criticalPath($phases);
        $optimisticDays = (int) ($totalDays * 0.75);
        $pessimisticDays = (int) ($totalDays * 1.4);

        $aiTips = $this->generateAiTips($permits->pluck('name')->toArray(), $totalDays);

        return [
            'total_days' => $totalDays,
            'optimistic_days' => $optimisticDays,
            'pessimistic_days' => $pessimisticDays,
            'phases' => $phases,
            'gantt' => $gantt,
            'ai_tips' => $aiTips,
            'permit_count' => $permits->count(),
        ];
    }

    private function buildPhases($permits): array
    {
        $phases = [];
        $position = 0;

        foreach ($permits as $permit) {
            $duration = $permit->typical_duration_days ?? 30;
            $min = $permit->min_duration_days ?? (int) ($duration * 0.7);
            $max = $permit->max_duration_days ?? (int) ($duration * 1.5);

            $requires = $permit->requires_before ?? [];
            $canParallel = $permit->can_parallel_with ?? [];

            // Calculate start based on dependencies
            $startDay = 0;
            if (! empty($requires)) {
                foreach ($phases as $phase) {
                    if (in_array($phase['code'], $requires)) {
                        $startDay = max($startDay, $phase['start_day'] + $phase['duration']);
                    }
                }
            } elseif (! empty($phases) && empty($canParallel)) {
                // Default: sequential after all previous
                $lastPhase = end($phases);
                $startDay = $lastPhase['start_day'] + $lastPhase['duration'];
            } elseif (! empty($canParallel)) {
                // Can run in parallel with some — start at position of the most recent dependency
                $startDay = $position > 0 ? $phases[0]['start_day'] ?? 0 : 0;
            }

            $phase = [
                'id' => $permit->id,
                'code' => $permit->code ?? 'P'.$permit->id,
                'name' => $permit->name,
                'start_day' => $startDay,
                'duration' => $duration,
                'min_duration' => $min,
                'max_duration' => $max,
                'parallel' => ! empty($canParallel),
                'requires' => $requires,
            ];

            $phases[] = $phase;
            $position++;
        }

        return $phases;
    }

    private function buildGantt(array $phases): array
    {
        $colors = ['#6366f1', '#10b981', '#f59e0b', '#3b82f6', '#ec4899', '#14b8a6', '#8b5cf6', '#f97316'];

        return array_map(function ($phase, $idx) use ($colors) {
            return [
                'label' => $phase['name'],
                'start' => $phase['start_day'],
                'end' => $phase['start_day'] + $phase['duration'],
                'duration' => $phase['duration'],
                'color' => $colors[$idx % count($colors)],
                'parallel' => $phase['parallel'],
            ];
        }, $phases, array_keys($phases));
    }

    private function criticalPath(array $phases): int
    {
        if (empty($phases)) {
            return 0;
        }

        return max(array_map(fn ($p) => $p['start_day'] + $p['duration'], $phases));
    }

    private function generateAiTips(array $permitNames, int $totalDays): array
    {
        $list = implode(', ', $permitNames);
        $result = $this->ai->chat([
            ['role' => 'system', 'content' => 'You are an Indonesian business licensing expert. Be concise and practical. Respond in Bahasa Indonesia.'],
            ['role' => 'user', 'content' => "Izin yang dibutuhkan: $list. Total estimasi: $totalDays hari. Berikan 3-4 tips singkat untuk mempercepat proses perizinan ini. Format: array JSON dengan objek {tip, impact} di mana impact adalah 'tinggi', 'sedang', atau 'rendah'."],
        ]);

        if (! $result['success']) {
            return [];
        }

        $cleaned = preg_replace('/^```(?:json)?\s*/m', '', $result['content']);
        $cleaned = preg_replace('/\s*```$/m', '', $cleaned);
        $decoded = json_decode(trim($cleaned), true);

        return is_array($decoded) ? $decoded : [];
    }

    private function emptyResult(): array
    {
        return [
            'total_days' => 0,
            'optimistic_days' => 0,
            'pessimistic_days' => 0,
            'phases' => [],
            'gantt' => [],
            'ai_tips' => [],
            'permit_count' => 0,
        ];
    }
}
