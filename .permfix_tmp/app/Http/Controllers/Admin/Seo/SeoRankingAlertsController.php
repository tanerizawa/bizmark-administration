<?php

namespace App\Http\Controllers\Admin\Seo;

use App\Http\Controllers\Admin\Seo\Concerns\SeoAdminFlashRedirect;
use App\Http\Controllers\Controller;
use App\Models\RankingAlert;
use Illuminate\Http\Request;

class SeoRankingAlertsController extends Controller
{
    use SeoAdminFlashRedirect;

    /**
     * Ranking alerts page.
     */
    public function rankingAlerts(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $severity = $request->get('severity', 'all');

        $query = RankingAlert::with('positionHistory');

        if ($filter === 'unread') {
            $query->unread();
        } elseif ($filter === 'pending') {
            $query->pending();
        } elseif ($filter === 'drops') {
            $query->drops();
        } elseif ($filter === 'gains') {
            $query->gains();
        }

        if ($severity === 'critical') {
            $query->critical();
        } elseif ($severity === 'warning') {
            $query->warnings();
        }

        $alerts = $query->orderByDesc('created_at')->paginate(25);

        // Summary
        $summary = RankingAlert::getDashboardSummary();

        return view('admin.seo.alerts', compact('alerts', 'summary', 'filter', 'severity'));
    }

    /**
     * Mark alert as read.
     */
    public function markAlertRead(int $id)
    {
        $alert = RankingAlert::findOrFail($id);
        $alert->markAsRead();

        return $this->seoBackFlash('success', 'Alert marked as read.');
    }

    /**
     * Mark all alerts as read.
     */
    public function markAllAlertsRead()
    {
        RankingAlert::unread()->update(['is_read' => true]);

        return $this->seoBackFlash('success', 'All alerts marked as read.');
    }
}

