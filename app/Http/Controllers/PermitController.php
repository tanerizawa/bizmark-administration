<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectPermit;
use App\Models\PermitType;
use App\Models\PermitTemplate;
use App\Models\PermitDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PermitController extends Controller
{
    /**
     * Show permits tab for a project
     */
    public function index(Project $project)
    {
        $project->load([
            'permits.permitType',
            'permits.dependencies.dependsOnPermit',
            'permits.dependents',
            'permits.assignedTo',
            'permits.documents.uploader',
        ]);

        // Calculate statistics
        $permits = $project->permits;
        $statistics = [
            'total' => $permits->count(),
            'completed' => $permits->where('status', 'APPROVED')->count(),
            'in_progress' => $permits->whereIn('status', ['IN_PROGRESS', 'SUBMITTED'])->count(),
            'not_started' => $permits->where('status', 'NOT_STARTED')->count(),
            'blocked' => $permits->filter(function($permit) {
                return $permit->status === 'NOT_STARTED' && 
                       $permit->dependencies->where('dependsOnPermit.status', '!=', 'APPROVED')->count() > 0;
            })->count(),
            'estimated_cost' => $permits->sum('estimated_cost'),
            'actual_cost' => $permits->sum('actual_cost'),
            'completion_rate' => $permits->count() > 0 ? round(($permits->where('status', 'APPROVED')->count() / $permits->count()) * 100, 1) : 0,
        ];

        // Get available permit types for adding
        $availablePermitTypes = PermitType::active()
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        // Get available templates
        $templates = PermitTemplate::with('items.permitType')
            ->where('is_public', true)
            ->orWhere('created_by_user_id', auth()->id())
            ->orderByDesc('usage_count')
            ->get();

        return view('projects.partials.permits-tab', compact(
            'project',
            'statistics',
            'availablePermitTypes',
            'templates'
        ));
    }

    /**
     * Store a new permit for the project
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'permit_type_id' => 'nullable|exists:permit_types,id',
            'custom_permit_name' => 'required_if:permit_type_id,null|string|max:100',
            'custom_institution_name' => 'nullable|string|max:100',
            'sequence_order' => 'nullable|integer',
            'is_goal_permit' => 'boolean',
            'estimated_cost' => 'nullable|numeric|min:0',
            'target_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'assigned_to_user_id' => 'nullable|exists:users,id',
        ]);

        // Get the next sequence order if not provided
        if (!isset($validated['sequence_order'])) {
            $validated['sequence_order'] = $project->permits()->max('sequence_order') + 1;
        }

        $permit = $project->permits()->create($validated);

        return redirect()
            ->route('projects.show', ['project' => $project->id, 'tab' => 'permits'])
            ->with('success', 'Permit berhasil ditambahkan');
    }

    /**
     * Update permit details
     */
    public function update(Request $request, ProjectPermit $permit)
    {
        $validated = $request->validate([
            'status' => 'required|in:NOT_STARTED,IN_PROGRESS,WAITING_DOC,SUBMITTED,UNDER_REVIEW,APPROVED,REJECTED,EXISTING,CANCELLED',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'target_date' => 'nullable|date',
            'permit_number' => 'nullable|string|max:100',
            'valid_until' => 'nullable|date',
            'notes' => 'nullable|string',
            'assigned_to_user_id' => 'nullable|exists:users,id',
        ]);

        // Update status timestamps
        if ($validated['status'] === 'IN_PROGRESS' && !$permit->started_at) {
            $validated['started_at'] = now();
        } elseif ($validated['status'] === 'SUBMITTED' && !$permit->submitted_at) {
            $validated['submitted_at'] = now();
        } elseif ($validated['status'] === 'APPROVED' && !$permit->approved_at) {
            $validated['approved_at'] = now();
        } elseif ($validated['status'] === 'REJECTED' && !$permit->rejected_at) {
            $validated['rejected_at'] = now();
        }

        $permit->update($validated);

        return redirect()
            ->route('projects.show', ['project' => $permit->project_id, 'tab' => 'permits'])
            ->with('success', 'Status permit berhasil diupdate');
    }

    /**
     * Delete a permit
     */
    public function destroy(ProjectPermit $permit)
    {
        $projectId = $permit->project_id;
        
        // Check if other permits depend on this one
        if ($permit->dependents()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus permit yang menjadi dependency permit lain');
        }

        $permit->delete();

        return redirect()
            ->route('projects.show', ['project' => $projectId, 'tab' => 'permits'])
            ->with('success', 'Permit berhasil dihapus');
    }

    /**
     * Apply a template to the project
     */
    public function applyTemplate(Request $request, Project $project)
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:permit_templates,id',
        ]);

        $template = PermitTemplate::with([
            'items.permitType',
            'dependencies'
        ])->findOrFail($validated['template_id']);

        DB::beginTransaction();
        try {
            // Create permits from template items
            $permitMapping = [];
            
            foreach ($template->items as $item) {
                $permit = $project->permits()->create([
                    'permit_type_id' => $item->permit_type_id,
                    'custom_permit_name' => $item->custom_permit_name,
                    'sequence_order' => $item->sequence_order,
                    'is_goal_permit' => $item->is_goal_permit,
                    'estimated_cost' => $item->estimated_cost,
                    'notes' => $item->notes,
                    'status' => 'NOT_STARTED',
                ]);

                $permitMapping[$item->id] = $permit->id;
            }

            // Create dependencies
            foreach ($template->dependencies as $dependency) {
                if (isset($permitMapping[$dependency->permit_item_id]) && 
                    isset($permitMapping[$dependency->depends_on_item_id])) {
                    
                    DB::table('project_permit_dependencies')->insert([
                        'project_permit_id' => $permitMapping[$dependency->permit_item_id],
                        'depends_on_permit_id' => $permitMapping[$dependency->depends_on_item_id],
                        'dependency_type' => $dependency->dependency_type,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Increment template usage
            $template->increment('usage_count');

            DB::commit();

            return redirect()
                ->route('projects.show', ['project' => $project->id, 'tab' => 'permits'])
                ->with('success', 'Template berhasil diterapkan ke proyek');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menerapkan template: ' . $e->getMessage());
        }
    }

    /**
     * Add a dependency between permits
     */
    public function addDependency(Request $request, ProjectPermit $permit)
    {
        $validated = $request->validate([
            'depends_on_permit_id' => 'required|exists:project_permits,id',
            'dependency_type' => 'required|in:MANDATORY,OPTIONAL',
        ]);

        // Prevent self-dependency
        if ($permit->id == $validated['depends_on_permit_id']) {
            return back()->with('error', 'Permit tidak dapat bergantung pada dirinya sendiri');
        }

        // Prevent circular dependency
        if ($this->wouldCreateCircularDependency($permit->id, $validated['depends_on_permit_id'])) {
            return back()->with('error', 'Dependency ini akan menciptakan circular dependency');
        }

        // Check if dependency already exists
        $exists = DB::table('project_permit_dependencies')
            ->where('project_permit_id', $permit->id)
            ->where('depends_on_permit_id', $validated['depends_on_permit_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Dependency sudah ada');
        }

        DB::table('project_permit_dependencies')->insert([
            'project_permit_id' => $permit->id,
            'depends_on_permit_id' => $validated['depends_on_permit_id'],
            'dependency_type' => $validated['dependency_type'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Dependency berhasil ditambahkan');
    }

    /**
     * Check if adding a dependency would create a circular reference
     */
    private function wouldCreateCircularDependency($permitId, $dependsOnPermitId)
    {
        $visited = [];
        $queue = [$dependsOnPermitId];

        while (!empty($queue)) {
            $currentId = array_shift($queue);
            
            if ($currentId == $permitId) {
                return true; // Circular dependency found
            }

            if (in_array($currentId, $visited)) {
                continue;
            }

            $visited[] = $currentId;

            // Get all dependencies of current permit
            $dependencies = DB::table('project_permit_dependencies')
                ->where('project_permit_id', $currentId)
                ->pluck('depends_on_permit_id')
                ->toArray();

            $queue = array_merge($queue, $dependencies);
        }

        return false;
    }

    /**
     * Reorder permits
     */
    public function reorder(Request $request, Project $project)
    {
        $validated = $request->validate([
            'permits' => 'required|array',
            'permits.*.id' => 'required|exists:project_permits,id',
            'permits.*.sequence_order' => 'required|integer',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['permits'] as $permitData) {
                ProjectPermit::where('id', $permitData['id'])
                    ->where('project_id', $project->id)
                    ->update(['sequence_order' => $permitData['sequence_order']]);
            }

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Bulk update permit statuses
     */
    public function bulkUpdateStatus(Request $request, Project $project)
    {
        $validated = $request->validate([
            'permit_ids' => 'required|array',
            'permit_ids.*' => 'required|exists:project_permits,id',
            'status' => 'required|in:NOT_STARTED,IN_PROGRESS,SUBMITTED,APPROVED,REJECTED,ON_HOLD',
        ]);

        DB::beginTransaction();
        try {
            $timestamp = now();
            $updateData = ['status' => $validated['status']];

            // Set appropriate timestamp based on status
            if ($validated['status'] === 'IN_PROGRESS') {
                $updateData['started_at'] = $timestamp;
            } elseif ($validated['status'] === 'SUBMITTED') {
                $updateData['submitted_at'] = $timestamp;
            } elseif ($validated['status'] === 'APPROVED') {
                $updateData['approved_at'] = $timestamp;
            } elseif ($validated['status'] === 'REJECTED') {
                $updateData['rejected_at'] = $timestamp;
            }

            $updated = ProjectPermit::whereIn('id', $validated['permit_ids'])
                ->where('project_id', $project->id)
                ->update($updateData);

            DB::commit();

            return response()->json([
                'success' => true,
                'updated_count' => $updated,
                'message' => "Successfully updated {$updated} permits"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete permits
     */
    public function bulkDelete(Request $request, Project $project)
    {
        $validated = $request->validate([
            'permit_ids' => 'required|array',
            'permit_ids.*' => 'required|exists:project_permits,id',
        ]);

        DB::beginTransaction();
        try {
            $deleted = 0;
            $skipped = 0;

            foreach ($validated['permit_ids'] as $permitId) {
                $permit = ProjectPermit::where('id', $permitId)
                    ->where('project_id', $project->id)
                    ->first();

                if (!$permit) {
                    continue;
                }

                // Check if other permits depend on this one
                if ($permit->dependents()->count() > 0) {
                    $skipped++;
                    continue;
                }

                $permit->delete();
                $deleted++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'deleted_count' => $deleted,
                'skipped_count' => $skipped,
                'message' => "Deleted {$deleted} permits" . ($skipped > 0 ? ", skipped {$skipped} with dependencies" : "")
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload document for a permit
     */
    public function uploadDocument(Request $request, Project $project, ProjectPermit $permit)
    {
        $validated = $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120', // 5MB max
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $file = $request->file('document');
            $originalFilename = $file->getClientOriginalName();
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalFilename);
            
            // Store file in storage/app/permits_documents
            $path = $file->storeAs('permits_documents', $filename);

            $document = PermitDocument::create([
                'project_permit_id' => $permit->id,
                'filename' => $filename,
                'original_filename' => $originalFilename,
                'file_path' => $path,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'description' => $validated['description'] ?? null,
                'uploaded_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully',
                'document' => $document
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download document
     */
    public function downloadDocument(Project $project, PermitDocument $document)
    {
        if (!Storage::exists($document->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::download($document->file_path, $document->original_filename);
    }

    /**
     * Delete document
     */
    public function deleteDocument(Project $project, PermitDocument $document)
    {
        try {
            // Delete file from storage
            if (Storage::exists($document->file_path)) {
                Storage::delete($document->file_path);
            }

            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete document (POST method for JavaScript compatibility)
     */
    public function deleteDocumentPost(PermitDocument $document)
    {
        try {
            // Delete file from storage
            if (Storage::exists($document->file_path)) {
                Storage::delete($document->file_path);
            }

            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
