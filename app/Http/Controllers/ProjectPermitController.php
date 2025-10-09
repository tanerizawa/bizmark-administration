<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectPermit;
use App\Models\PermitType;
use App\Models\PermitTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectPermitController extends Controller
{
    /**
     * Store a new permit for the project.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'permit_type_id' => 'required|exists:permit_types,id',
            'is_goal_permit' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Get next sequence order
        $maxSequence = $project->permits()->max('sequence_order') ?? 0;

        $permit = $project->permits()->create([
            'permit_type_id' => $validated['permit_type_id'],
            'sequence_order' => $maxSequence + 1,
            'is_goal_permit' => $validated['is_goal_permit'] ?? false,
            'status' => ProjectPermit::STATUS_NOT_STARTED,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Izin berhasil ditambahkan ke proyek!');
    }

    /**
     * Update permit status.
     */
    public function updateStatus(Request $request, ProjectPermit $permit)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', ProjectPermit::STATUSES),
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
            'override_reason' => 'nullable|string',
        ]);

        $validated['status'] = Str::upper($validated['status']);

        // Check if trying to start without meeting dependencies
        if ($validated['status'] === ProjectPermit::STATUS_IN_PROGRESS && !$permit->canStart()) {
            // Allow override if reason provided
            if (empty($validated['override_reason'])) {
                return redirect()
                    ->back()
                    ->with('error', 'Tidak dapat memulai izin ini. Prasyarat belum terpenuhi!');
            }

            // Log override
            $permit->update([
                'override_dependencies' => true,
                'override_reason' => $validated['override_reason'],
                'override_by' => auth()->id() ?? 1, // TODO: Add auth
                'override_at' => now(),
            ]);
        }

        $permit->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Status izin berhasil diperbarui!');
    }

    /**
     * Add dependency to a permit.
     */
    public function addDependency(Request $request, ProjectPermit $permit)
    {
        $validated = $request->validate([
            'depends_on_permit_id' => 'required|exists:project_permits,id',
            'can_proceed_without' => 'boolean',
        ]);

        // Check if dependency already exists
        $exists = $permit->dependencies()
            ->where('depends_on_permit_id', $validated['depends_on_permit_id'])
            ->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->with('error', 'Dependensi sudah ada!');
        }

        $permit->dependencies()->create($validated);

        return redirect()
            ->back()
            ->with('success', 'Dependensi berhasil ditambahkan!');
    }

    /**
     * Remove dependency from a permit.
     */
    public function removeDependency(ProjectPermit $permit, $dependencyId)
    {
        $permit->dependencies()->where('id', $dependencyId)->delete();

        return redirect()
            ->back()
            ->with('success', 'Dependensi berhasil dihapus!');
    }

    /**
     * Apply template to project.
     */
    public function applyTemplate(Request $request, Project $project)
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:permit_templates,id',
        ]);

        $template = PermitTemplate::with(['items.permitType', 'items.dependencies'])->findOrFail($validated['template_id']);

        // Delete existing permits if requested
        if ($request->boolean('replace_existing')) {
            $project->permits()->delete();
        }

        // Get starting sequence
        $startSequence = $project->permits()->max('sequence_order') ?? 0;

        // Copy template items to project permits
        $itemsMap = []; // Maps template item id => project permit id

        foreach ($template->items as $item) {
            $projectPermit = $project->permits()->create([
                'permit_type_id' => $item->permit_type_id,
                'sequence_order' => $startSequence + $item->sequence_order,
                'is_goal_permit' => $item->is_goal_permit,
                'status' => ProjectPermit::STATUS_NOT_STARTED,
            ]);

            $itemsMap[$item->id] = $projectPermit->id;
        }

        // Copy dependencies
        foreach ($template->items as $item) {
            if ($item->dependencies->count() > 0) {
                $projectPermit = ProjectPermit::find($itemsMap[$item->id]);

                foreach ($item->dependencies as $dependency) {
                    $dependsOnPermitId = $itemsMap[$dependency->depends_on_item_id] ?? null;

                    if ($dependsOnPermitId) {
                        $projectPermit->dependencies()->create([
                            'depends_on_permit_id' => $dependsOnPermitId,
                            'can_proceed_without' => $dependency->can_proceed_without,
                        ]);
                    }
                }
            }
        }

        return redirect()
            ->back()
            ->with('success', "Template '{$template->name}' berhasil diterapkan ke proyek!");
    }

    /**
     * Delete a permit from project.
     */
    public function destroy(ProjectPermit $permit)
    {
        // Check if other permits depend on this
        $dependents = ProjectPermit::whereHas('dependencies', function($query) use ($permit) {
            $query->where('depends_on_permit_id', $permit->id);
        })->get();

        if ($dependents->count() > 0) {
            $redirectUrl = route('projects.show', $permit->project_id);
            
            // If request has redirect_to_tab parameter, add it to URL
            if (request()->has('redirect_to_tab')) {
                $redirectUrl .= '?tab=' . request('redirect_to_tab');
            }
            
            return redirect($redirectUrl)
                ->with('error', 'Tidak dapat menghapus izin ini karena masih menjadi prasyarat izin lain!');
        }

        $permit->delete();

        $redirectUrl = route('projects.show', $permit->project_id);
        
        // If request has redirect_to_tab parameter, add it to URL
        if (request()->has('redirect_to_tab')) {
            $redirectUrl .= '?tab=' . request('redirect_to_tab');
        }

        return redirect($redirectUrl)
            ->with('success', 'Izin berhasil dihapus dari proyek!');
    }

    /**
     * Reorder permits in project.
     */
    public function reorder(Request $request, Project $project)
    {
        $validated = $request->validate([
            'permits' => 'required|array',
            'permits.*.id' => 'required|exists:project_permits,id',
            'permits.*.sequence_order' => 'required|integer|min:1',
        ]);

        foreach ($validated['permits'] as $permitData) {
            ProjectPermit::where('id', $permitData['id'])
                ->update(['sequence_order' => $permitData['sequence_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan izin berhasil diperbarui!'
        ]);
    }
}
