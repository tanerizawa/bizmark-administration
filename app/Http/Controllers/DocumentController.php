<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Document::with(['project', 'task', 'uploader']);

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by document type
        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        // Search by title or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        // Only show latest versions by default
        if (!$request->filled('show_all_versions')) {
            $query->where('is_latest_version', true);
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $projects = Project::orderBy('name')->get();
        $categories = Document::select('category')->distinct()->whereNotNull('category')->pluck('category');
        $documentTypes = Document::select('document_type')->distinct()->whereNotNull('document_type')->pluck('document_type');

        return view('documents.index', compact('documents', 'projects', 'categories', 'documentTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $projects = Project::orderBy('name')->get();
        $tasks = collect();
        
        // Pre-select project and load tasks if coming from project or task page
        $selectedProject = $request->filled('project_id') ? 
            Project::find($request->project_id) : null;
        
        if ($selectedProject) {
            $tasks = Task::where('project_id', $selectedProject->id)->orderBy('title')->get();
        }
        
        $selectedTask = $request->filled('task_id') ? 
            Task::find($request->task_id) : null;
        
        return view('documents.create', compact('projects', 'tasks', 'selectedProject', 'selectedTask'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'task_id' => 'nullable|exists:tasks,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'document_file' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,rar',
            'document_date' => 'nullable|date',
            'status' => 'required|in:draft,review,approved,submitted,final',
            'is_confidential' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('documents', $fileName, 'public');

            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_path'] = $filePath;
            $validated['file_size'] = $file->getSize();
            $validated['mime_type'] = $file->getMimeType();
            $validated['document_type'] = strtoupper($file->getClientOriginalExtension());
        }

        $validated['uploaded_by'] = Auth::id() ?? 1; // Default to admin if not authenticated
        $validated['version'] = '1.0';
        $validated['is_latest_version'] = true;

        $document = Document::create($validated);

        return redirect()->route('documents.show', $document)
            ->with('success', 'Dokumen berhasil diunggah!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        $document->load(['project', 'task', 'uploader', 'parentDocument', 'childDocuments']);
        
        // Update last accessed and download count
        $document->update([
            'download_count' => $document->download_count + 1,
            'last_accessed_at' => now()
        ]);
        
        // Get related documents
        $relatedDocuments = Document::where('project_id', $document->project_id)
            ->where('id', '!=', $document->id)
            ->where('is_latest_version', true)
            ->limit(5)
            ->get();
            
        return view('documents.show', compact('document', 'relatedDocuments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        $projects = Project::orderBy('name')->get();
        $tasks = Task::where('project_id', $document->project_id)->orderBy('title')->get();
        
        return view('documents.edit', compact('document', 'projects', 'tasks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'task_id' => 'nullable|exists:tasks,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'document_date' => 'nullable|date',
            'status' => 'required|in:draft,review,approved,submitted,final',
            'is_confidential' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Handle file replacement
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            
            // Validation for new file
            $request->validate([
                'document_file' => 'file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,rar',
            ]);

            // Delete old file
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Store new file
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('documents', $fileName, 'public');

            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_path'] = $filePath;
            $validated['file_size'] = $file->getSize();
            $validated['mime_type'] = $file->getMimeType();
            $validated['document_type'] = strtoupper($file->getClientOriginalExtension());
        }

        $document->update($validated);

        return redirect()->route('documents.show', $document)
            ->with('success', 'Dokumen berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        // Delete file from storage
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $projectId = $document->project_id;
        $document->delete();

        return redirect()->route('documents.index', ['project_id' => $projectId])
            ->with('success', 'Dokumen berhasil dihapus!');
    }

    /**
     * Download document file
     */
    public function download(Document $document)
    {
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File tidak ditemukan!');
        }

        // Update download count
        $document->increment('download_count');
        $document->update(['last_accessed_at' => now()]);

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    /**
     * Get tasks by project (AJAX)
     */
    public function getTasksByProject(Request $request)
    {
        $tasks = Task::where('project_id', $request->project_id)
            ->orderBy('title')
            ->get(['id', 'title']);

        return response()->json($tasks);
    }
}
