<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Jobs\ParaphraseDocumentJob;
use App\Jobs\ComplianceCheckJob;
use App\Models\AIProcessingLog;
use App\Models\DocumentDraft;
use App\Models\DocumentTemplate;
use App\Models\Project;
use App\Services\TemplateExtractor;
use App\Services\UKLUPLComplianceService;
use App\Services\ComplianceReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentAIController extends Controller
{
    /**
     * Show paraphrase form
     */
    public function create(int $projectId)
    {
        $project = Project::with('client')->findOrFail($projectId);
        $templates = DocumentTemplate::active()->get();

        return view('ai.paraphrase-form', compact('project', 'templates'));
    }

    /**
     * Process paraphrase request (dispatch job)
     */
    public function store(Request $request, int $projectId)
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:document_templates,id',
            'additional_context' => 'nullable|array',
        ]);

        $project = Project::findOrFail($projectId);
        $template = DocumentTemplate::findOrFail($validated['template_id']);

        // Dispatch background job
        ParaphraseDocumentJob::dispatch(
            $projectId,
            $template->id,
            Auth::id(),
            $validated['additional_context'] ?? []
        );

        return redirect()
            ->route('ai.drafts.index', $projectId)
            ->with('success', 'Dokumen sedang diproses oleh AI. Anda akan menerima notifikasi setelah selesai.');
    }

    /**
     * List all drafts for project
     */
    public function index(int $projectId)
    {
        $project = Project::findOrFail($projectId);
        
        $drafts = DocumentDraft::with(['template', 'aiLog', 'creator', 'approver', 'complianceChecks'])
            ->forProject($projectId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('ai.drafts-index', compact('project', 'drafts'));
    }

    /**
     * Show draft details
     */
    public function show(int $projectId, int $draftId)
    {
        $project = Project::findOrFail($projectId);
        
        $draft = DocumentDraft::with(['template', 'aiLog', 'creator', 'approver'])
            ->where('project_id', $projectId)
            ->findOrFail($draftId);

        return view('ai.draft-show', compact('project', 'draft'));
    }

    /**
     * Update draft content (after manual editing)
     */
    public function update(Request $request, int $projectId, int $draftId)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'nullable|in:draft,reviewed,approved,rejected',
        ]);

        $draft = DocumentDraft::where('project_id', $projectId)->findOrFail($draftId);

        $draft->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'status' => $validated['status'] ?? 'reviewed',
        ]);

        // Auto-trigger compliance check after significant update
        if ($request->has('content')) {
            ComplianceCheckJob::dispatch($draft->id);
        }

        // Support AJAX auto-save with JSON response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'id' => $draft->id,
                'status' => $draft->status,
                'updated_at' => $draft->updated_at?->toIso8601String(),
                'compliance_check_triggered' => true,
            ]);
        }

        return redirect()
            ->route('ai.drafts.show', [$projectId, $draftId])
            ->with('success', 'Draft berhasil diperbarui. Compliance check sedang berjalan...');
    }

    /**
     * Approve draft
     */
    public function approve(int $projectId, int $draftId, UKLUPLComplianceService $complianceService)
    {
        $draft = DocumentDraft::where('project_id', $projectId)
            ->with('complianceChecks')
            ->findOrFail($draftId);
        
        // Check compliance score before approval
        $latestCheck = $draft->complianceChecks()->latest()->first();
        
        if ($latestCheck && $latestCheck->overall_score < 80) {
            return redirect()
                ->route('ai.drafts.show', [$projectId, $draftId])
                ->with('warning', sprintf(
                    'Compliance score saat ini adalah %.1f/100 (threshold 80). Apakah Anda yakin ingin approve? %s',
                    $latestCheck->overall_score,
                    $complianceService->getComplianceSummary($latestCheck)
                ));
        }
        
        $draft->approve(Auth::id());

        return redirect()
            ->route('ai.drafts.show', [$projectId, $draftId])
            ->with('success', 'Draft telah disetujui');
    }

    /**
     * Reject draft
     */
    public function reject(int $projectId, int $draftId)
    {
        $draft = DocumentDraft::where('project_id', $projectId)->findOrFail($draftId);
        
        $draft->reject();

        return redirect()
            ->route('ai.drafts.index', $projectId)
            ->with('info', 'Draft ditolak');
    }

    /**
     * Delete draft (soft delete with permission check)
     */
    public function destroy(int $projectId, int $draftId)
    {
        $draft = DocumentDraft::where('project_id', $projectId)->findOrFail($draftId);
        
        // Permission check: only creator can delete (simplified - extend with role later)
        if ($draft->created_by !== Auth::id()) {
            return redirect()
                ->route('ai.drafts.show', [$projectId, $draftId])
                ->with('error', 'Anda tidak memiliki izin untuk menghapus draft ini');
        }
        
        // Cannot delete approved drafts (business rule)
        if ($draft->status === 'approved') {
            return redirect()
                ->route('ai.drafts.show', [$projectId, $draftId])
                ->with('error', 'Draft yang sudah disetujui tidak dapat dihapus');
        }
        
        // Soft delete for audit trail
        $draft->delete();

        return redirect()
            ->route('ai.drafts.index', $projectId)
            ->with('success', 'Draft berhasil dihapus');
    }

    /**
     * Export draft to PDF or DOCX
     */
    public function export(Request $request, int $projectId, int $draftId)
    {
        $draft = DocumentDraft::with(['project', 'template', 'creator', 'approver'])
            ->where('project_id', $projectId)
            ->findOrFail($draftId);

        $format = $request->get('format', 'docx'); // default DOCX

        if ($format === 'docx') {
            // Export to DOCX with proper formatting
            $docxService = app(\App\Services\DocxExportService::class);
            $filePath = $docxService->exportToDocx($draft);
            
            return $docxService->downloadDocx($filePath);
        } else {
            // Export to PDF (legacy support)
            $pdf = Pdf::loadView('ai.draft-pdf', compact('draft'));
            $filename = str_replace(' ', '_', $draft->title) . '.pdf';
            return $pdf->download($filename);
        }
    }

    /**
     * Check processing status via AJAX
     */
    public function status(int $projectId)
    {
        $logs = AIProcessingLog::where('project_id', $projectId)
            ->whereIn('status', ['pending', 'processing'])
            ->with('template')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'processing' => $logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'template_name' => $log->template->name,
                    'status' => $log->status,
                    'started_at' => $log->created_at->diffForHumans(),
                ];
            }),
        ]);
    }

    /**
     * Upload new template (admin only)
     */
    public function uploadTemplate(Request $request)
    {
        $this->authorize('admin'); // Or create policy

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permit_type' => 'required|in:pertek_bpn,ukl_upl,amdal,imb,pbg,slf,siup,tdp,npwp,other',
            'description' => 'nullable|string',
            'template_file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240', // 10MB
            'required_fields' => 'nullable|array',
        ]);

        // Store file
        $file = $request->file('template_file');
        $filePath = $file->store('templates', 'local');

        // Extract metadata
        $extractor = app(TemplateExtractor::class);
        $extraction = $extractor->extractFromFile(storage_path('app/' . $filePath));

        // Create template record
        $template = DocumentTemplate::create([
            'name' => $validated['name'],
            'permit_type' => $validated['permit_type'],
            'description' => $validated['description'],
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'page_count' => $extraction['page_count'] ?? null,
            'required_fields' => $validated['required_fields'] ?? null,
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Template berhasil diunggah');
    }

    /**
     * Run compliance check on a draft
     */
    public function checkCompliance(Request $request, int $projectId, int $draftId)
    {
        $draft = DocumentDraft::where('project_id', $projectId)->findOrFail($draftId);

        // Check if user has permission (creator or admin)
        if ($draft->created_by !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk melakukan compliance check',
            ], 403);
        }

        // Dispatch compliance check job
        ComplianceCheckJob::dispatch($draftId);

        return response()->json([
            'success' => true,
            'message' => 'Compliance check dimulai. Hasil akan tersedia dalam beberapa saat.',
        ]);
    }

    /**
     * Get compliance check results
     */
    public function getComplianceResults(int $projectId, int $draftId, UKLUPLComplianceService $complianceService)
    {
        $draft = DocumentDraft::where('project_id', $projectId)
            ->with('complianceChecks')
            ->findOrFail($draftId);

        $latestCheck = $draft->complianceChecks()->latest()->first();

        if (!$latestCheck) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada compliance check untuk draft ini',
                'has_check' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'has_check' => true,
            'check' => [
                'overall_score' => $latestCheck->overall_score,
                'structure_score' => $latestCheck->structure_score,
                'compliance_score' => $latestCheck->compliance_score,
                'formatting_score' => $latestCheck->formatting_score,
                'completeness_score' => $latestCheck->completeness_score,
                'status' => $latestCheck->status,
                'status_label' => $latestCheck->status_label,
                'status_color' => $latestCheck->status_color,
                'total_issues' => $latestCheck->total_issues,
                'critical_issues' => $latestCheck->critical_issues,
                'warning_issues' => $latestCheck->warning_issues,
                'info_issues' => $latestCheck->info_issues,
                'issues' => $latestCheck->issues,
                'issues_by_category' => $latestCheck->issues_by_category,
                'summary' => $complianceService->getComplianceSummary($latestCheck),
                'checked_at' => $latestCheck->checked_at?->diffForHumans(),
                'needs_recheck' => $latestCheck->needsRecheck(),
            ],
        ]);
    }

    /**
     * Export compliance report as DOCX
     */
    public function exportComplianceReport(int $projectId, int $draftId, ComplianceReportService $reportService)
    {
        $draft = DocumentDraft::where('project_id', $projectId)
            ->with(['complianceChecks', 'project', 'creator'])
            ->findOrFail($draftId);

        $latestCheck = $draft->complianceChecks()->latest()->first();

        if (!$latestCheck) {
            return redirect()
                ->route('ai.drafts.show', [$projectId, $draftId])
                ->with('error', 'Belum ada compliance check. Jalankan compliance check terlebih dahulu.');
        }

        try {
            return $reportService->downloadReport($draft);
        } catch (\Exception $e) {
            return redirect()
                ->route('ai.drafts.show', [$projectId, $draftId])
                ->with('error', 'Gagal generate compliance report: ' . $e->getMessage());
        }
    }
}
