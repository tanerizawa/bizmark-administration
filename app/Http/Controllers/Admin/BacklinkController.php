<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Backlink;
use App\Models\BacklinkTarget;
use App\Models\BacklinkOutreach;
use App\Models\ContentSyndication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BacklinkController extends Controller
{
    public function index()
    {
        // Dashboard overview
        $stats = [
            'total_targets' => BacklinkTarget::count(),
            'pending_targets' => BacklinkTarget::where('status', 'pending')->count(),
            'contacted_targets' => BacklinkTarget::where('status', 'contacted')->count(),
            'acquired_backlinks' => Backlink::count(),
            'indexed_backlinks' => Backlink::where('status', 'indexed')->count(),
            'dofollow_backlinks' => Backlink::where('type', 'dofollow')->count(),
            'total_da' => Backlink::sum('domain_authority'),
            'avg_da' => Backlink::avg('domain_authority'),
            'outreach_sent' => BacklinkOutreach::where('status', 'sent')->count(),
            'outreach_responded' => BacklinkOutreach::where('status', 'responded')->count(),
            'response_rate' => BacklinkOutreach::where('status', 'sent')->count() > 0 
                ? round((BacklinkOutreach::where('status', 'responded')->count() / BacklinkOutreach::where('status', 'sent')->count()) * 100, 2)
                : 0,
            'syndicated_content' => ContentSyndication::where('status', 'published')->count(),
        ];

        // Recent backlinks
        $recentBacklinks = Backlink::with('target')
            ->latest()
            ->take(10)
            ->get();

        // Pending outreach
        $pendingOutreach = BacklinkOutreach::with('target')
            ->where('status', 'pending')
            ->latest()
            ->take(10)
            ->get();

        // High priority targets
        $highPriorityTargets = BacklinkTarget::where('priority', 'high')
            ->where('status', 'pending')
            ->orderBy('domain_authority', 'desc')
            ->take(10)
            ->get();

        // Monthly backlink chart
        $monthlyBacklinks = Backlink::select(
                DB::raw('DATE(acquired_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->whereNotNull('acquired_at')
            ->where('acquired_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(acquired_at)'))
            ->orderBy('date')
            ->get();

        return view('admin.backlinks.index', compact(
            'stats',
            'recentBacklinks',
            'pendingOutreach',
            'highPriorityTargets',
            'monthlyBacklinks'
        ));
    }

    public function targets(Request $request)
    {
        $query = BacklinkTarget::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('website_name', 'like', '%' . $request->search . '%')
                  ->orWhere('website_url', 'like', '%' . $request->search . '%')
                  ->orWhere('contact_email', 'like', '%' . $request->search . '%');
            });
        }

        $targets = $query->withCount('outreaches', 'backlinks')
            ->orderBy('priority', 'desc')
            ->orderBy('domain_authority', 'desc')
            ->paginate(20);

        return view('admin.backlinks.targets', compact('targets'));
    }

    public function createTarget()
    {
        return view('admin.backlinks.create-target');
    }

    public function storeTarget(Request $request)
    {
        $validated = $request->validate([
            'website_name' => 'required|string|max:255',
            'website_url' => 'required|url|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_name' => 'nullable|string|max:255',
            'domain_authority' => 'nullable|integer|min:0|max:100',
            'category' => 'required|string|max:255',
            'type' => 'required|in:guest_post,resource_link,partnership,directory,syndication',
            'priority' => 'required|in:high,medium,low',
            'notes' => 'nullable|string',
        ]);

        BacklinkTarget::create($validated);

        return redirect()->route('admin.backlinks.targets')
            ->with('success', 'Target website added successfully!');
    }

    public function editTarget(BacklinkTarget $target)
    {
        return view('admin.backlinks.edit-target', compact('target'));
    }

    public function updateTarget(Request $request, BacklinkTarget $target)
    {
        $validated = $request->validate([
            'website_name' => 'required|string|max:255',
            'website_url' => 'required|url|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_name' => 'nullable|string|max:255',
            'domain_authority' => 'nullable|integer|min:0|max:100',
            'category' => 'required|string|max:255',
            'type' => 'required|in:guest_post,resource_link,partnership,directory,syndication',
            'priority' => 'required|in:high,medium,low',
            'status' => 'required|in:pending,contacted,responded,accepted,rejected,acquired',
            'notes' => 'nullable|string',
        ]);

        $target->update($validated);

        return redirect()->route('admin.backlinks.targets')
            ->with('success', 'Target website updated successfully!');
    }

    public function deleteTarget(BacklinkTarget $target)
    {
        $target->delete();

        return redirect()->route('admin.backlinks.targets')
            ->with('success', 'Target website deleted successfully!');
    }

    public function backlinks(Request $request)
    {
        $query = Backlink::with('target');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('source_url', 'like', '%' . $request->search . '%')
                  ->orWhere('target_url', 'like', '%' . $request->search . '%')
                  ->orWhere('anchor_text', 'like', '%' . $request->search . '%');
            });
        }

        $backlinks = $query->orderBy('acquired_at', 'desc')
            ->paginate(20);

        return view('admin.backlinks.backlinks', compact('backlinks'));
    }

    public function createBacklink()
    {
        $targets = BacklinkTarget::orderBy('website_name')->get();
        return view('admin.backlinks.create-backlink', compact('targets'));
    }

    public function storeBacklink(Request $request)
    {
        $validated = $request->validate([
            'backlink_target_id' => 'nullable|exists:backlink_targets,id',
            'source_url' => 'required|url|max:255',
            'target_url' => 'required|url|max:255',
            'anchor_text' => 'required|string|max:255',
            'type' => 'required|in:dofollow,nofollow',
            'domain_authority' => 'nullable|integer|min:0|max:100',
            'acquired_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        Backlink::create($validated);

        return redirect()->route('admin.backlinks.list')
            ->with('success', 'Backlink added successfully!');
    }

    public function editBacklink(Backlink $backlink)
    {
        $targets = BacklinkTarget::orderBy('website_name')->get();
        return view('admin.backlinks.edit-backlink', compact('backlink', 'targets'));
    }

    public function updateBacklink(Request $request, Backlink $backlink)
    {
        $validated = $request->validate([
            'backlink_target_id' => 'nullable|exists:backlink_targets,id',
            'source_url' => 'required|url|max:255',
            'target_url' => 'required|url|max:255',
            'anchor_text' => 'required|string|max:255',
            'type' => 'required|in:dofollow,nofollow',
            'status' => 'required|in:active,indexed,broken,removed',
            'domain_authority' => 'nullable|integer|min:0|max:100',
            'acquired_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $backlink->update($validated);

        return redirect()->route('admin.backlinks.list')
            ->with('success', 'Backlink updated successfully!');
    }

    public function deleteBacklink(Backlink $backlink)
    {
        $backlink->delete();

        return redirect()->route('admin.backlinks.list')
            ->with('success', 'Backlink deleted successfully!');
    }

    public function syndication(Request $request)
    {
        $query = ContentSyndication::with('article');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('platform')) {
            $query->where('platform', $request->platform);
        }

        $syndications = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.backlinks.syndication', compact('syndications'));
    }

    public function analytics()
    {
        // Detailed analytics
        $analytics = [
            // Overview metrics
            'total_backlinks' => Backlink::count(),
            'indexed_backlinks' => Backlink::where('status', 'indexed')->count(),
            'pending_backlinks' => Backlink::where('status', 'pending')->count(),
            'broken_backlinks' => Backlink::where('status', 'broken')->count(),
            
            // Quality metrics
            'dofollow_count' => Backlink::where('type', 'dofollow')->count(),
            'nofollow_count' => Backlink::where('type', 'nofollow')->count(),
            'avg_da' => round(Backlink::avg('domain_authority'), 2),
            'total_da' => Backlink::sum('domain_authority'),
            
            // Outreach metrics
            'total_targets' => BacklinkTarget::count(),
            'contacted' => BacklinkTarget::where('status', 'contacted')->count(),
            'responded' => BacklinkTarget::whereIn('status', ['responded', 'accepted'])->count(),
            'acquired' => BacklinkTarget::where('status', 'acquired')->count(),
            'success_rate' => BacklinkTarget::where('status', 'contacted')->count() > 0
                ? round((BacklinkTarget::where('status', 'acquired')->count() / BacklinkTarget::where('status', 'contacted')->count()) * 100, 2)
                : 0,
            
            // Syndication metrics
            'syndicated_articles' => ContentSyndication::where('status', 'published')->count(),
            'pending_syndication' => ContentSyndication::where('status', 'pending')->count(),
            'failed_syndication' => ContentSyndication::where('status', 'failed')->count(),
        ];

        // Backlinks by category
        $byCategory = BacklinkTarget::select('category', DB::raw('COUNT(*) as count'))
            ->whereHas('backlinks')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get();

        // Monthly acquisition trend
        $monthlyTrend = Backlink::select(
                DB::raw("TO_CHAR(acquired_at, 'YYYY-MM') as month"),
                DB::raw('COUNT(*) as count'),
                DB::raw('AVG(domain_authority) as avg_da')
            )
            ->whereNotNull('acquired_at')
            ->where('acquired_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.backlinks.analytics', compact('analytics', 'byCategory', 'monthlyTrend'));
    }

    /**
     * Execute automation command
     */
    public function executeCommand(Request $request)
    {
        $request->validate([
            'command' => 'required|string',
            'args' => 'nullable|array',
        ]);

        $command = $request->input('command');
        $args = $request->input('args', []);

        // Security: Whitelist allowed commands
        $allowedCommands = [
            'backlink:outreach',
            'backlink:crawl',
            'backlink:monitor',
            'content:syndicate',
        ];

        if (!in_array($command, $allowedCommands)) {
            return response()->json([
                'success' => false,
                'message' => 'Command not allowed',
            ], 403);
        }

        try {
            // Build command with arguments
            $commandString = $command;
            foreach ($args as $key => $value) {
                if (is_bool($value)) {
                    if ($value) {
                        $commandString .= " --{$key}";
                    }
                } else {
                    $commandString .= " --{$key}={$value}";
                }
            }

            // Execute command and capture output
            $output = [];
            $exitCode = 0;

            \Artisan::call($commandString, [], new \Symfony\Component\Console\Output\BufferedOutput());
            $output = \Artisan::output();

            return response()->json([
                'success' => true,
                'message' => 'Command executed successfully',
                'output' => $output,
                'exit_code' => $exitCode,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Command execution failed: ' . $e->getMessage(),
                'output' => '',
            ], 500);
        }
    }
}
