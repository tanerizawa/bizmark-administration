<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::query()->with('project');
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
            });
        }
        
        // Filter by category
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $documents = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('mobile.documents.index', compact('documents'));
    }
    
    public function show(Document $document)
    {
        return view('mobile.documents.show', compact('document'));
    }
    
    public function uploadForm()
    {
        // Get projects for dropdown
        $projects = \App\Models\Project::where('status_id', '!=', 8)
            ->orderBy('name')
            ->get(['id', 'name']);
            
        return view('mobile.documents.upload', compact('projects'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'category' => 'required|in:permit,proposal,agreement,report,other',
            'file' => 'required|file|max:10240', // 10MB
            'description' => 'nullable|string|max:500'
        ]);
        
        try {
            // Store file
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('documents', $filename, 'public');
            
            // Create document record
            $document = Document::create([
                'project_id' => $request->project_id,
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType(),
                'uploaded_by' => auth()->id(),
                'status' => 'active'
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dokumen berhasil diupload',
                    'document' => $document
                ]);
            }
            
            return redirect()->route('mobile.documents.index')
                ->with('success', 'Dokumen berhasil diupload');
                
        } catch (\Exception $e) {
            \Log::error('Document upload error: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupload dokumen: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Gagal mengupload dokumen');
        }
    }
    
    public function download(Document $document)
    {
        if (!Storage::exists($document->file_path)) {
            return back()->with('error', 'File tidak ditemukan');
        }
        
        return Storage::download($document->file_path, $document->title);
    }
}
