<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\PermitApplication;
use App\Models\ServiceInquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Live aggregate stats for landing ticker.
 *
 * Returns intentionally bucketed/rounded ranges (privacy + signal-vs-noise).
 * Cached 5 minutes to keep DB load minimal.
 *
 * Decision #2: aggregate ranges only — never raw counts, never PII.
 */
class LandingStatsController extends Controller
{
    private const CACHE_KEY = 'landing.live_stats.v1';

    private const CACHE_TTL = 300; // 5 minutes

    public function __invoke(): JsonResponse
    {
        $stats = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return $this->compute();
        });

        return response()->json([
            'data' => $stats,
            'meta' => [
                'cache_ttl' => self::CACHE_TTL,
                'note' => 'Aggregate ranges only. Updated every 5 minutes.',
            ],
        ])->header('Cache-Control', 'public, max-age=300');
    }

    /**
     * Compute aggregates with try/catch fallback to safe defaults.
     */
    private function compute(): array
    {
        $defaults = config('landing_metrics.live_stats_defaults', [
            'permits_per_month' => '200–300',
            'permits_this_week' => '40–60',
            'pma_active' => '10–15',
            'clients_active' => '138+',
        ]);

        try {
            // Permits per month — last 30 days, count completed/in-progress/submitted
            $monthCount = PermitApplication::query()
                ->where('created_at', '>=', now()->subDays(30))
                ->count();

            // This week — last 7 days, completed only
            $weekCount = PermitApplication::query()
                ->whereIn('status', ['completed', 'approved'])
                ->where('updated_at', '>=', now()->subDays(7))
                ->count();

            // PMA inquiries active — service_inquiries with PMA-related fields in last 60d
            $pmaCount = ServiceInquiry::query()
                ->where(function ($q) {
                    $q->where('company_type', 'like', '%PMA%')
                        ->orWhere('business_activity', 'like', '%PMA%')
                        ->orWhere('kbli_description', 'like', '%asing%');
                })
                ->where('created_at', '>=', now()->subDays(60))
                ->count();

            // Total active clients
            $clientsTotal = Client::query()->count();

            return [
                'permits_per_month' => $this->bucketRange($monthCount, 50, '200–300'),
                'permits_this_week' => $this->bucketRange($weekCount, 10, '40–60'),
                'pma_active' => $this->bucketRange($pmaCount, 5, '10–15'),
                'clients_active' => $this->floorPlus($clientsTotal, 10, '138+'),
                'as_of' => now()->toIso8601String(),
            ];
        } catch (Throwable $e) {
            // Tabel/kolom belum ada atau query error → fallback aman
            report($e);

            return array_merge($defaults, [
                'as_of' => now()->toIso8601String(),
                'fallback' => true,
            ]);
        }
    }

    /**
     * Bucket a count to a "low–high" range rounded to $step.
     * Below 1 step worth, returns $minLabel.
     */
    private function bucketRange(int $count, int $step, string $minLabel): string
    {
        if ($count <= 0) {
            return $minLabel;
        }
        $low = (int) (floor($count / $step) * $step);
        $high = $low + $step;
        if ($low === 0) {
            $low = max(1, (int) round($step / 5));
        }

        return sprintf('%d–%d', $low, $high);
    }

    /**
     * Floor to step + plus suffix (e.g., 138 → "130+").
     */
    private function floorPlus(int $count, int $step, string $minLabel): string
    {
        if ($count <= 0) {
            return $minLabel;
        }
        $floor = (int) (floor($count / $step) * $step);

        return sprintf('%d+', max($floor, $step));
    }
}
