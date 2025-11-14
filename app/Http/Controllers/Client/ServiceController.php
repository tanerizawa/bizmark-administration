<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\PermitType;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:client');
    }

    /**
     * Display a listing of available permit services.
     */
    public function index(Request $request)
    {
        $query = PermitType::where('is_active', true);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%")
                  ->orWhere('code', 'ilike', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Sort
        $sortBy = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        
        if (in_array($sortBy, ['name', 'estimated_cost_min', 'avg_processing_days'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $services = $query->paginate(12)->withQueryString();
        
        // Get categories for filter
        $categories = PermitType::select('category')
            ->where('is_active', true)
            ->groupBy('category')
            ->pluck('category');

        return view('client.services.index', compact('services', 'categories'));
    }

    /**
     * Display the specified permit service.
     */
    public function show(string $code)
    {
        $service = PermitType::where('code', $code)
            ->where('is_active', true)
            ->firstOrFail();

        // Get related services in the same category
        $relatedServices = PermitType::where('category', $service->category)
            ->where('id', '!=', $service->id)
            ->where('is_active', true)
            ->limit(3)
            ->get();

        return view('client.services.show', compact('service', 'relatedServices'));
    }
}
