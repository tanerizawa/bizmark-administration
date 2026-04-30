<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with('role');
        
        // Count active projects for each user
        $query->withCount(['assignedTasks as active_projects_count' => function($q) {
            $q->whereHas('project', function($pq) {
                $pq->whereIn('status_id', [2, 3, 4, 5, 6, 7, 8]); // Kontrak through Menunggu Persetujuan
            });
        }]);
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('phone', 'ilike', "%{$search}%");
            });
        }
        
        // Filter by role
        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        $users = $query->orderBy('name', 'asc')->paginate(20);
        
        // Calculate stats
        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'on_project' => User::whereHas('assignedTasks.project', function($q) {
                $q->whereIn('status_id', [2, 3, 4, 5, 6, 7, 8]); // Kontrak through Menunggu Persetujuan
            })->count(),
            'available' => User::where('is_active', true)
                ->whereDoesntHave('assignedTasks.project', function($q) {
                    $q->whereIn('status_id', [2, 3, 4, 5, 6, 7, 8]); // Kontrak through Menunggu Persetujuan
                })->count(),
        ];
        
        return view('mobile.team.index', compact('users', 'stats'));
    }
    
    public function show(User $user)
    {
        $user->load(['assignedTasks' => function($query) {
            $query->latest()->limit(10);
        }]);
        
        return view('mobile.team.show', compact('user'));
    }
}
