<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query()->withCount('projects');
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('company_name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $clients = $query->orderBy('name', 'asc')->paginate(20);
        
        // Calculate stats
        $stats = [
            'total' => Client::count(),
            'active' => Client::where('status', 'active')->count(),
            'inactive' => Client::where('status', 'inactive')->count(),
        ];
        
        return view('mobile.clients.index', compact('clients', 'stats'));
    }
    
    public function show(Client $client)
    {
        $client->load(['projects' => function($query) {
            $query->latest()->limit(10);
        }]);
        
        return view('mobile.clients.show', compact('client'));
    }
}
