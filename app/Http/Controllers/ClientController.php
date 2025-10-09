<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the clients.
     */
    public function index(Request $request)
    {
        $query = Client::query()->with('projects');

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by client type
        if ($request->has('client_type') && $request->client_type != '') {
            $query->where('client_type', $request->client_type);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $clients = $query->paginate(15);

        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new client.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'npwp' => 'nullable|string|max:50',
            'tax_name' => 'nullable|string|max:255',
            'tax_address' => 'nullable|string',
            'client_type' => 'required|in:individual,company,government',
            'status' => 'required|in:active,inactive,potential',
            'notes' => 'nullable|string',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Klien berhasil ditambahkan!');
    }

    /**
     * Display the specified client.
     */
    public function show(Client $client)
    {
        $client->load(['projects.status', 'projects.institution']);
        
        // Statistics
        $stats = [
            'total_projects' => $client->projects->count(),
            'active_projects' => $client->projects->filter(function ($project) {
                return $project->status->name != 'Selesai';
            })->count(),
            'total_value' => $client->projects->sum('contract_value'),
            'total_paid' => $client->projects->sum('down_payment'),
        ];

        return view('clients.show', compact('client', 'stats'));
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified client in storage.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'npwp' => 'nullable|string|max:50',
            'tax_name' => 'nullable|string|max:255',
            'tax_address' => 'nullable|string',
            'client_type' => 'required|in:individual,company,government',
            'status' => 'required|in:active,inactive,potential',
            'notes' => 'nullable|string',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Klien berhasil diupdate!');
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(Client $client)
    {
        // Check if client has projects
        if ($client->projects()->count() > 0) {
            return redirect()->route('clients.index')
                ->with('error', 'Tidak dapat menghapus klien yang memiliki project aktif!');
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Klien berhasil dihapus!');
    }

    /**
     * Get clients for API/Select2
     */
    public function apiIndex(Request $request)
    {
        $query = Client::query()->where('status', 'active');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $clients = $query->select('id', 'name', 'company_name', 'email', 'phone')
            ->limit(20)
            ->get();

        return response()->json($clients);
    }
}
