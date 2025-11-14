<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Display a listing of client's documents.
     */
    public function index(Request $request)
    {
        $client = Auth::guard('client')->user();
        
        // Get all documents from client's projects
        $query = Document::whereHas('project', function ($q) use ($client) {
            $q->where('client_id', $client->id);
        })->with(['project']);
        
        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'ILIKE', '%' . $request->search . '%');
        }
        
        // Filter by document category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $documents = $query->paginate(15);
        
        // Get client's projects for filter
        $projects = $client->projects()->get();
        
        // Get unique document categories
        $documentTypes = Document::whereHas('project', function ($q) use ($client) {
            $q->where('client_id', $client->id);
        })->distinct()->pluck('category')->filter()->values();
        
        // Statistics
        $stats = [
            'total' => Document::whereHas('project', function ($q) use ($client) {
                $q->where('client_id', $client->id);
            })->count(),
            'this_month' => Document::whereHas('project', function ($q) use ($client) {
                $q->where('client_id', $client->id);
            })->whereMonth('created_at', now()->month)->count(),
        ];
        
        return view('client.documents.index', compact('documents', 'projects', 'documentTypes', 'stats'));
    }
    
    /**
     * Download a document.
     */
    public function download($id)
    {
        $client = Auth::guard('client')->user();
        $document = Document::findOrFail($id);
        
        // Authorize: Check if document belongs to client's project
        if ($document->project->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this document');
        }
        
        // Check if file exists
        if (!Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File tidak ditemukan');
        }
        
        // Download file
        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }
}
