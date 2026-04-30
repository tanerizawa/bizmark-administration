<?php

namespace App\Modules\Proyek\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Project;
use App\Models\ProjectLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            $query->where('title', 'LIKE', '%'.$request->search.'%');
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
        $allowedSortBy = ['created_at', 'title', 'category'];
        $sortBy = $request->get('sort_by', 'created_at');
        if (! in_array($sortBy, $allowedSortBy, true)) {
            $sortBy = 'created_at';
        }

        $sortOrder = strtolower((string) $request->get('sort_order', 'desc'));
        if (! in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $documents = $query->paginate(15);

        // Get client's projects for filter
        $projects = Project::where('client_id', $client->id)->get();

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
     * Store a newly created document.
     */
    public function store(Request $request)
    {
        $client = Auth::guard('client')->user();

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'category' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        // Verify project belongs to client
        $project = Project::where('id', $validated['project_id'])
            ->where('client_id', $client->id)
            ->firstOrFail();

        // Upload file
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $safeFileName = (string) Str::uuid().'.'.$file->extension();
            $filePath = $file->storeAs('documents/'.$project->id, $safeFileName, 'private');

            // Create document record
            $document = Document::create([
                'project_id' => $project->id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'category' => $validated['category'],
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'document_type' => $file->getClientOriginalExtension(),
                'uploaded_by' => null,
                'is_latest_version' => true,
                'status' => 'final',
            ]);

            // Log activity
            ProjectLog::create([
                'project_id' => $project->id,
                'user_id' => null,
                'action' => 'document_uploaded',
                'entity_type' => 'Document',
                'entity_id' => $document->id,
                'description' => "Client uploaded document: {$document->title}",
            ]);

            return back()->with('success', 'Dokumen berhasil diupload');
        }

        return back()->with('error', 'File tidak ditemukan');
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
        if (Storage::disk('private')->exists($document->file_path)) {
            return response()->download(Storage::disk('private')->path($document->file_path), $document->file_name);
        }

        if (Storage::disk('public')->exists($document->file_path)) {
            return response()->download(Storage::disk('public')->path($document->file_path), $document->file_name);
        }

        return back()->with('error', 'File tidak ditemukan');
    }
}
