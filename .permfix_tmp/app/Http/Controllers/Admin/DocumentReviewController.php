<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\ApplicationStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DocumentReviewController extends Controller
{
    /**
     * Approve a document
     */
    public function approve(Request $request, $documentId)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $document = ApplicationDocument::with('application')->findOrFail($documentId);

        if ($document->status === 'approved') {
            return back()->with('info', 'Dokumen sudah diapprove sebelumnya');
        }

        DB::beginTransaction();
        try {
            $document->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'review_notes' => $request->notes,
            ]);

            // Log activity
            ApplicationStatusLog::create([
                'application_id' => $document->application_id,
                'from_status' => $document->application->status,
                'to_status' => $document->application->status,
                'changed_by_type' => 'user',
                'changed_by_id' => Auth::id(),
                'notes' => "Dokumen '{$document->document_type}' diapprove",
            ]);

            DB::commit();

            return back()->with('success', 'Dokumen berhasil diapprove');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal approve dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Reject a document
     */
    public function reject(Request $request, $documentId)
    {
        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $document = ApplicationDocument::with('application')->findOrFail($documentId);

        if ($document->status === 'rejected') {
            return back()->with('info', 'Dokumen sudah direject sebelumnya');
        }

        DB::beginTransaction();
        try {
            $document->update([
                'status' => 'rejected',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'review_notes' => $request->notes,
            ]);

            // Log activity
            ApplicationStatusLog::create([
                'application_id' => $document->application_id,
                'from_status' => $document->application->status,
                'to_status' => $document->application->status,
                'changed_by_type' => 'user',
                'changed_by_id' => Auth::id(),
                'notes' => "Dokumen '{$document->document_type}' ditolak: {$request->notes}",
            ]);

            DB::commit();

            // TODO: Send notification to client about rejected document

            return back()->with('success', 'Dokumen berhasil ditolak. Client akan diberitahu untuk upload ulang.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal reject dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Bulk approve documents
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:application_documents,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $documents = ApplicationDocument::with('application')
                ->whereIn('id', $request->document_ids)
                ->where('status', 'pending')
                ->get();

            foreach ($documents as $document) {
                $document->update([
                    'status' => 'approved',
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => now(),
                    'review_notes' => $request->notes,
                ]);

                ApplicationStatusLog::create([
                    'application_id' => $document->application_id,
                    'from_status' => $document->application->status,
                    'to_status' => $document->application->status,
                    'changed_by_type' => 'user',
                    'changed_by_id' => Auth::id(),
                    'notes' => "Dokumen '{$document->document_type}' diapprove (bulk)",
                ]);
            }

            DB::commit();

            return back()->with('success', count($documents) . ' dokumen berhasil diapprove');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal bulk approve: ' . $e->getMessage());
        }
    }

    /**
     * Approve all documents in an application
     */
    public function approveAll($applicationId)
    {
        DB::beginTransaction();
        try {
            $documents = ApplicationDocument::where('application_id', $applicationId)
                ->where('status', 'pending')
                ->get();

            if ($documents->isEmpty()) {
                return back()->with('info', 'Tidak ada dokumen pending untuk diapprove');
            }

            foreach ($documents as $document) {
                $document->update([
                    'status' => 'approved',
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => now(),
                ]);
            }

            ApplicationStatusLog::create([
                'application_id' => $applicationId,
                'from_status' => $documents->first()->application->status,
                'to_status' => $documents->first()->application->status,
                'changed_by_type' => 'user',
                'changed_by_id' => Auth::id(),
                'notes' => "Semua dokumen (" . count($documents) . ") diapprove",
            ]);

            DB::commit();

            return back()->with('success', count($documents) . ' dokumen berhasil diapprove');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal approve semua dokumen: ' . $e->getMessage());
        }
    }
}
