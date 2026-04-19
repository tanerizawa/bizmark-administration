<?php

namespace App\Http\Controllers;

use App\Models\PermitTemplate;
use App\Models\Project;
use Illuminate\Http\Request;

class PermitTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $templates = PermitTemplate::with([
                'items.permitType.institution',
                'dependencies'
            ])
            ->withCount(['items', 'dependencies'])
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        // Calculate estimated days and cost for each template
        foreach ($templates as $template) {
            $template->total_estimated_days = $template->items->sum('estimated_days') ?? 0;
            $template->total_estimated_cost = $template->items->sum('estimated_cost') ?? 0;
        }

        return view('permit-templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('permit-templates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'estimated_days' => 'nullable|integer|min:1',
            'estimated_cost' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.permit_type_id' => 'required|exists:permit_types,id',
            'items.*.sequence_order' => 'required|integer|min:1',
            'items.*.is_goal_permit' => 'nullable|boolean',
        ]);

        // Create template
        $template = PermitTemplate::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'estimated_days' => $validated['estimated_days'] ?? null,
            'estimated_cost' => $validated['estimated_cost'] ?? null,
        ]);

        // Create items and store them for dependency reference
        $itemsMap = []; // Maps sequence_order => item_id
        
        foreach ($validated['items'] as $itemData) {
            $item = $template->items()->create([
                'permit_type_id' => $itemData['permit_type_id'],
                'sequence_order' => $itemData['sequence_order'],
                'is_goal_permit' => $itemData['is_goal_permit'] ?? false,
            ]);
            
            $itemsMap[$itemData['sequence_order']] = $item->id;
        }

        // Create dependencies
        foreach ($validated['items'] as $index => $itemData) {
            if (!empty($itemData['dependencies'])) {
                $currentItemId = $itemsMap[$itemData['sequence_order']];
                
                foreach ($itemData['dependencies'] as $depSequence) {
                    $dependsOnItemId = $itemsMap[$depSequence] ?? null;
                    
                    if ($dependsOnItemId) {
                        $dependencyType = $itemData['dependency_types'][$depSequence] ?? 'MANDATORY';
                        
                        \App\Models\PermitTemplateDependency::create([
                            'template_id' => $template->id,
                            'permit_item_id' => $currentItemId,
                            'depends_on_item_id' => $dependsOnItemId,
                            'dependency_type' => $dependencyType,
                        ]);
                    }
                }
            }
        }

        return redirect()
            ->route('permit-templates.show', $template)
            ->with('success', 'Template berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(PermitTemplate $permitTemplate)
    {
        $permitTemplate->load([
            'items.permitType.institution',
            'items.dependencies.dependsOnItem.permitType'
        ]);

        return view('permit-templates.show', compact('permitTemplate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PermitTemplate $permitTemplate)
    {
        return view('permit-templates.edit', compact('permitTemplate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PermitTemplate $permitTemplate)
    {
        // TODO: Implement update
        return redirect()->route('permit-templates.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PermitTemplate $permitTemplate)
    {
        $permitTemplate->delete();

        return redirect()
            ->route('permit-templates.index')
            ->with('success', 'Template berhasil dihapus!');
    }

    /**
     * Apply template to a project.
     */
    public function applyToProject(Request $request, PermitTemplate $template)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
        ]);

        $project = Project::findOrFail($validated['project_id']);

        // Copy template items to project permits
        foreach ($template->items as $item) {
            $projectPermit = $project->permits()->create([
                'permit_type_id' => $item->permit_type_id,
                'sequence_order' => $item->sequence_order,
                'is_goal_permit' => $item->is_goal_permit,
                'status' => 'not_started',
            ]);

            // Copy dependencies
            foreach ($item->dependencies as $dependency) {
                $dependsOnPermit = $project->permits()
                    ->where('permit_type_id', $dependency->depends_on_permit_type_id)
                    ->first();

                if ($dependsOnPermit) {
                    $projectPermit->dependencies()->create([
                        'depends_on_permit_id' => $dependsOnPermit->id,
                        'can_proceed_without' => $dependency->can_proceed_without,
                    ]);
                }
            }
        }

        return redirect()
            ->route('projects.show', $project)
            ->with('success', "Template '{$template->name}' berhasil diterapkan ke proyek!");
    }
}
