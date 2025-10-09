<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InstitutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Institution::withCount(['projects', 'permitTypes']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%");
            });
        }
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }
        
        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', (bool) $request->get('is_active'));
        }
        
        // Sorting
        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');
        $query->orderBy($sortBy, $sortOrder);
        
        $institutions = $query->paginate(15)->withQueryString();
        
        // Get filter options
        $types = Institution::select('type')->distinct()->whereNotNull('type')->pluck('type');
        
        return view('institutions.index', compact('institutions', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('institutions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:institutions,name',
            'type' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_position' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        // Set default active status if not provided
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }
        
        $institution = Institution::create($validated);
        
        return redirect()->route('institutions.show', $institution)
            ->with('success', 'Institusi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Institution $institution)
    {
        $institution->load(['projects' => function($query) {
            $query->with(['status'])->latest();
        }]);
        
        // Get statistics
        $stats = [
            'total_projects' => $institution->projects->count(),
            'active_projects' => $institution->projects->where('status.name', 'Aktif')->count(),
            'completed_projects' => $institution->projects->where('status.name', 'Selesai')->count(),
            'total_budget' => $institution->projects->sum('budget'),
        ];
        
        // Recent projects
        $recentProjects = $institution->projects->take(5);
        
        return view('institutions.show', compact('institution', 'stats', 'recentProjects'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Institution $institution)
    {
        return view('institutions.edit', compact('institution'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Institution $institution)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('institutions', 'name')->ignore($institution->id)],
            'type' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_position' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $institution->update($validated);
        
        return redirect()->route('institutions.show', $institution)
            ->with('success', 'Institusi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Institution $institution)
    {
        // Check if institution has active projects
        if ($institution->projects()->count() > 0) {
            return redirect()->route('institutions.show', $institution)
                ->with('error', 'Tidak dapat menghapus institusi yang masih memiliki proyek.');
        }
        
        $institution->delete();
        
        return redirect()->route('institutions.index')
            ->with('success', 'Institusi berhasil dihapus.');
    }

    /**
     * Get institutions for API/AJAX requests
     */
    public function apiIndex(Request $request)
    {
        $query = Institution::where('status', 'active');
        
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }
        
        $institutions = $query->orderBy('name')->limit(20)->get(['id', 'name', 'type']);
        
        return response()->json($institutions);
    }
}
