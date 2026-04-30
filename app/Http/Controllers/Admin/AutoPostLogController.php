<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutoPostLog;
use App\Models\AutoPostSchedule;
use Illuminate\Http\Request;

class AutoPostLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AutoPostSchedule::with(['article', 'topic'])
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date') && $request->from_date != '') {
            $query->whereDate('completed_at', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date != '') {
            $query->whereDate('completed_at', '<=', $request->to_date);
        }

        $logs = $query->paginate(20);

        $stats = [
            'total' => AutoPostSchedule::whereNotNull('completed_at')->count(),
            'completed' => AutoPostSchedule::where('status', 'completed')->count(),
            'failed' => AutoPostSchedule::where('status', 'failed')->count(),
            'processing' => AutoPostSchedule::where('status', 'processing')->count(),
        ];

        return view('admin.auto-post.logs', compact('logs', 'stats'));
    }

    public function recent(Request $request)
    {
        $limit = $request->input('limit', 50);

        $logs = AutoPostLog::with(['schedule.topic'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'schedule_id' => $log->schedule_id,
                    'level' => $log->level,
                    'event' => $log->event,
                    'message' => $log->message,
                    'context' => $log->context,
                    'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                    'topic' => $log->schedule ? [
                        'id' => $log->schedule->topic_id,
                        'title' => $log->schedule->topic?->title ?? 'N/A',
                    ] : null,
                ];
            });

        return response()->json([
            'success' => true,
            'logs' => $logs,
            'count' => $logs->count(),
        ]);
    }
}
