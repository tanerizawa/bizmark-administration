<?php

namespace App\Http\Controllers;

use App\Models\PermitType;
use App\Models\Institution;
use Illuminate\Http\Request;

class PermitTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PermitType::with('institution');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by institution
        if ($request->filled('institution')) {
            $query->where('institution_id', $request->institution);
        }

        // Filter active/inactive
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $permitTypes = $query->paginate(20)->withQueryString();

        // Get filter options
        $institutions = Institution::orderBy('name')->get();
        $categories = ['environmental', 'land', 'building', 'transportation', 'business', 'other'];

        return view('permit-types.index', compact('permitTypes', 'institutions', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $institutions = Institution::orderBy('name')->get();
        $categories = ['environmental', 'land', 'building', 'transportation', 'business', 'other'];
        
        return view('permit-types.create', compact('institutions', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:permit_types,code',
            'category' => 'required|in:environmental,land,building,transportation,business,other',
            'institution_id' => 'nullable|exists:institutions,id',
            'avg_processing_days' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'required_documents' => 'nullable|array',
            'required_documents.*' => 'string',
            'estimated_cost_min' => 'nullable|numeric|min:0',
            'estimated_cost_max' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Convert required_documents array to proper format
        if ($request->has('required_documents')) {
            $validated['required_documents'] = array_filter($request->required_documents);
        }

        $validated['is_active'] = $request->has('is_active');

        PermitType::create($validated);

        return redirect()
            ->route('permit-types.index')
            ->with('success', 'Jenis izin berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(PermitType $permitType)
    {
        $permitType->load('institution', 'templateItems', 'projectPermits');
        
        return view('permit-types.show', compact('permitType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PermitType $permitType)
    {
        $institutions = Institution::orderBy('name')->get();
        $categories = ['environmental', 'land', 'building', 'transportation', 'business', 'other'];
        
        return view('permit-types.edit', compact('permitType', 'institutions', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PermitType $permitType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:permit_types,code,' . $permitType->id,
            'category' => 'required|in:environmental,land,building,transportation,business,other',
            'institution_id' => 'nullable|exists:institutions,id',
            'avg_processing_days' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'required_documents' => 'nullable|array',
            'required_documents.*' => 'string',
            'estimated_cost_min' => 'nullable|numeric|min:0',
            'estimated_cost_max' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Convert required_documents array to proper format
        if ($request->has('required_documents')) {
            $validated['required_documents'] = array_filter($request->required_documents);
        }

        $validated['is_active'] = $request->has('is_active');

        $permitType->update($validated);

        return redirect()
            ->route('permit-types.index')
            ->with('success', 'Jenis izin berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PermitType $permitType)
    {
        // Check if permit type is being used
        $usageCount = $permitType->templateItems()->count() + $permitType->projectPermits()->count();
        
        if ($usageCount > 0) {
            return redirect()
                ->route('permit-types.index')
                ->with('error', "Tidak dapat menghapus jenis izin ini karena sedang digunakan di {$usageCount} tempat.");
        }

        $permitType->delete();

        return redirect()
            ->route('permit-types.index')
            ->with('success', 'Jenis izin berhasil dihapus!');
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(PermitType $permitType)
    {
        $permitType->update(['is_active' => !$permitType->is_active]);

        $status = $permitType->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->back()
            ->with('success', "Jenis izin berhasil {$status}!");
    }
}
