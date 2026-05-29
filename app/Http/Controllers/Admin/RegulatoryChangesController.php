<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\CrawlRegulatorySourcesJob;
use App\Models\RegulatoryChange;
use Illuminate\Http\Request;

/**
 * P7 — Admin panel for viewing and managing regulatory change alerts.
 */
class RegulatoryChangesController extends Controller
{
    public function index(Request $request)
    {
        $query = RegulatoryChange::query()->orderByDesc('published_at');

        if ($request->filled('relevance')) {
            $query->where('relevance_score', '>=', (float) $request->relevance);
        }

        if ($request->filled('notified')) {
            $query->where('notified', (bool) $request->notified);
        }

        $changes = $query->paginate(20)->withQueryString();

        return view('admin.regulatory-changes.index', compact('changes'));
    }

    public function triggerCrawl()
    {
        CrawlRegulatorySourcesJob::dispatch();

        return back()->with('success', 'Crawling dijadwalkan. Periksa hasilnya dalam beberapa menit.');
    }

    public function destroy(RegulatoryChange $change)
    {
        $change->delete();

        return back()->with('success', 'Entri dihapus.');
    }
}
