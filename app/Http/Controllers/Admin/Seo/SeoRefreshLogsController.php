<?php

namespace App\Http\Controllers\Admin\Seo;

use App\Http\Controllers\Controller;
use App\Models\ContentRefreshLog;
use App\Services\ContentRefreshService;
use Illuminate\Http\Request;

class SeoRefreshLogsController extends Controller
{

    /**
     * Content Refresh audit logs
     */
    public function refreshLogs(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $query = ContentRefreshLog::with('article:id,title,slug');

        if ($filter === 'refreshed') {
            $query->where('status', 'refreshed');
        } elseif ($filter === 'error') {
            $query->where('status', 'error');
        }

        $logs = $query->orderByDesc('created_at')->paginate(25);

        $stats = ContentRefreshLog::selectRaw("COUNT(*) as total, SUM(CASE WHEN status = 'refreshed' THEN 1 ELSE 0 END) as refreshed, SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) as errors, MAX(created_at) as last_run, COALESCE(SUM(ai_tokens_used), 0) as total_tokens")->first();

        $summary = [
            'total' => (int) $stats->total,
            'refreshed' => (int) $stats->refreshed,
            'errors' => (int) $stats->errors,
            'last_run' => $stats->last_run ? \Carbon\Carbon::parse($stats->last_run) : null,
            'total_tokens' => (int) $stats->total_tokens,
        ];

        return view('admin.seo.refresh-logs', compact('logs', 'summary', 'filter'));
    }

    /**
     * Run content refresh on stale articles
     */
    public function runContentRefresh(Request $request, ContentRefreshService $service)
    {
        $limit = min((int) $request->input('limit', 2), 5);
        $stale = $service->getStaleArticles(90, $limit);

        if ($stale->isEmpty()) {
            return redirect()->route('admin.seo.refresh-logs')->with('success', 'Tidak ada artikel stale yang perlu di-refresh (semua sudah diperbarui dalam 90 hari terakhir).');
        }

        $results = ['refreshed' => 0, 'errors' => 0];
        foreach ($stale as $article) {
            $r = $service->refreshArticle($article, 'manual');
            $r['status'] === 'refreshed' ? $results['refreshed']++ : $results['errors']++;
        }

        return redirect()->route('admin.seo.refresh-logs')->with('success', "Content refresh selesai: {$results['refreshed']} berhasil, {$results['errors']} error dari {$stale->count()} artikel.");
    }

    /**
     * Retry refresh for a specific failed log entry
     */
    public function retryRefresh(int $id, ContentRefreshService $service)
    {
        $log = ContentRefreshLog::with('article')->findOrFail($id);

        if (!$log->article) {
            return redirect()->route('admin.seo.refresh-logs')->with('error', 'Artikel sudah dihapus, tidak bisa retry.');
        }

        $result = $service->refreshArticle($log->article, 'manual');

        return redirect()->route('admin.seo.refresh-logs')->with('success', "Retry untuk \"{$log->article->title}\": status {$result['status']}.");
    }

    /**
     * Delete a refresh log entry
     */
    public function deleteRefreshLog(int $id)
    {
        $log = ContentRefreshLog::findOrFail($id);
        $log->delete();

        return redirect()->route('admin.seo.refresh-logs')->with('success', 'Log entry berhasil dihapus.');
    }

    /**
     * Show detail of a refresh log (for before/after snapshots)
     */
    public function showRefreshLog(int $id)
    {
        $log = ContentRefreshLog::with('article:id,title,slug')->findOrFail($id);

        return response()->json([
            'id' => $log->id,
            'article_title' => $log->article->title ?? 'Deleted',
            'status' => $log->status,
            'triggered_by' => $log->triggered_by,
            'ai_tokens_used' => $log->ai_tokens_used,
            'changes' => $log->changes,
            'before_snapshot' => $log->before_snapshot,
            'after_snapshot' => $log->after_snapshot,
            'error_message' => $log->error_message,
            'created_at' => $log->created_at->format('d M Y H:i:s'),
        ]);
    }
}

