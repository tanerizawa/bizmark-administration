<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BetaTester;
use App\Models\BetaTesterDocument;
use App\Notifications\BetaTesterDocumentLinkNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BetaTesterManagementController extends Controller
{
    /**
     * Display dashboard with statistics
     */
    public function dashboard()
    {
        $stats = [
            'total' => BetaTester::count(),
            'pending_documents' => BetaTester::where('status', 'documents_pending')->count(),
            'active' => BetaTester::where('status', 'active')->count(),
            'completed' => BetaTester::where('status', 'completed')->count(),
        ];

        // Registration trend (last 30 days)
        $registrationTrend = BetaTester::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Recent registrations
        $recentRegistrations = BetaTester::with('documents')
            ->latest()
            ->take(10)
            ->get();

        // Document signing status
        $documentStats = [
            'signed_both' => BetaTester::whereHas('documents', function ($q) {
                $q->where('is_signed', true);
            }, '>=', 2)->count(),
            'signed_partial' => BetaTester::whereHas('documents', function ($q) {
                $q->where('is_signed', true);
            }, '=', 1)->count(),
            'unsigned' => BetaTester::whereDoesntHave('documents', function ($q) {
                $q->where('is_signed', true);
            })->count(),
        ];

        // University distribution
        $universityStats = BetaTester::select('university', DB::raw('COUNT(*) as count'))
            ->groupBy('university')
            ->orderByDesc('count')
            ->take(10)
            ->get();

        return view('admin.beta-tester.dashboard', compact(
            'stats',
            'registrationTrend',
            'recentRegistrations',
            'documentStats',
            'universityStats'
        ));
    }

    /**
     * Display a listing of beta testers
     */
    public function index(Request $request)
    {
        $query = BetaTester::with(['documents', 'activities' => function ($q) {
            $q->latest()->take(1);
        }]);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'ILIKE', "%{$search}%")
                    ->orWhere('email', 'ILIKE', "%{$search}%")
                    ->orWhere('registration_number', 'ILIKE', "%{$search}%")
                    ->orWhere('university', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by document status
        if ($request->filled('document_status')) {
            if ($request->document_status === 'all_signed') {
                $query->whereHas('documents', function ($q) {
                    $q->where('is_signed', true);
                }, '>=', 2);
            } elseif ($request->document_status === 'none_signed') {
                $query->whereDoesntHave('documents', function ($q) {
                    $q->where('is_signed', true);
                });
            } elseif ($request->document_status === 'partial_signed') {
                $query->whereHas('documents', function ($q) {
                    $q->where('is_signed', true);
                }, '=', 1);
            }
        }

        // Filter by university
        if ($request->filled('university')) {
            $query->where('university', $request->university);
        }

        // Sort
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $betaTesters = $query->paginate(20)->withQueryString();

        // Get universities for filter
        $universities = BetaTester::select('university')
            ->distinct()
            ->orderBy('university')
            ->pluck('university');

        return view('admin.beta-tester.index', compact('betaTesters', 'universities'));
    }

    /**
     * Display the specified beta tester
     */
    public function show(BetaTester $betaTester)
    {
        $betaTester->load(['documents', 'activities' => function ($q) {
            $q->latest();
        }]);

        return view('admin.beta-tester.show', compact('betaTester'));
    }

    /**
     * Update the specified beta tester
     */
    public function update(Request $request, BetaTester $betaTester)
    {
        $validated = $request->validate([
            'status' => 'required|in:registered,documents_pending,documents_signed,active,inactive,completed,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        $oldStatus = $betaTester->status;
        $betaTester->update($validated);

        // Log activity
        $betaTester->logActivity(
            'status_changed',
            "Status diubah dari {$oldStatus} ke {$validated['status']} oleh admin",
            [
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
            ]
        );

        return redirect()
            ->route('admin.beta-tester.show', $betaTester)
            ->with('success', 'Status beta tester berhasil diperbarui');
    }

    /**
     * Verify document
     */
    public function verifyDocument(Request $request, BetaTesterDocument $document)
    {
        if (!$document->is_signed) {
            return back()->with('error', 'Dokumen belum ditandatangani');
        }

        $document->verify(auth()->id());

        return back()->with('success', 'Dokumen berhasil diverifikasi');
    }

    /**
     * Change beta tester status
     */
    public function changeStatus(Request $request, BetaTester $betaTester)
    {
        $validated = $request->validate([
            'status' => 'required|in:registered,documents_pending,documents_signed,active,inactive,completed,rejected',
            'reason' => 'nullable|string',
        ]);

        $oldStatus = $betaTester->status;
        $betaTester->status = $validated['status'];
        $betaTester->save();

        // Log activity
        $betaTester->logActivity(
            'status_changed',
            "Status diubah dari {$oldStatus} ke {$validated['status']}" . ($validated['reason'] ? ": {$validated['reason']}" : ''),
            [
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'reason' => $validated['reason'] ?? null,
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
            ]
        );

        // Send notification to beta tester
        // TODO: Implement email notification

        return back()->with('success', 'Status berhasil diubah');
    }

    /**
     * Resend document links
     */
    public function resendDocuments(BetaTester $betaTester)
    {
        try {
            // Send notification with dashboard link
            $betaTester->notify(new BetaTesterDocumentLinkNotification($betaTester, true));
            
            $betaTester->logActivity(
                'documents_resent',
                'Link dokumen dikirim ulang oleh admin',
                ['admin_id' => auth()->id(), 'admin_name' => auth()->user()->name]
            );

            return back()->with('success', 'Link dokumen berhasil dikirim ulang ke ' . $betaTester->email);
        } catch (\Exception $e) {
            \Log::error('Failed to resend document link', [
                'beta_tester_id' => $betaTester->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Gagal mengirim email. Silakan coba lagi.');
        }
    }

    /**
     * Add admin note
     */
    public function addNote(Request $request, BetaTester $betaTester)
    {
        $validated = $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $betaTester->logActivity(
            'admin_note_added',
            $validated['note'],
            [
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
            ]
        );

        return back()->with('success', 'Catatan berhasil ditambahkan');
    }

    /**
     * Remove the specified beta tester
     */
    public function destroy(BetaTester $betaTester)
    {
        $registrationNumber = $betaTester->registration_number;
        
        DB::beginTransaction();
        try {
            // Delete documents
            $betaTester->documents()->delete();
            
            // Delete activities
            $betaTester->activities()->delete();
            
            // Delete beta tester
            $betaTester->delete();
            
            DB::commit();
            
            return redirect()
                ->route('admin.beta-tester.index')
                ->with('success', "Beta tester {$registrationNumber} berhasil dihapus");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to delete beta tester: ' . $e->getMessage());
            
            return back()->with('error', 'Gagal menghapus beta tester');
        }
    }

    /**
     * Export beta testers to CSV
     */
    public function export(Request $request)
    {
        $query = BetaTester::with('documents');

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $betaTesters = $query->get();

        $filename = 'beta-testers-' . now()->format('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($betaTesters) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'Registration Number',
                'Full Name',
                'Email',
                'Phone',
                'University',
                'Faculty',
                'Major',
                'Semester',
                'Status',
                'Pakta Integritas Signed',
                'NDA Signed',
                'Registration Date',
            ]);

            // Data
            foreach ($betaTesters as $tester) {
                $paktaSigned = $tester->documents->where('document_type', 'pakta_integritas')->first()?->is_signed ? 'Yes' : 'No';
                $ndaSigned = $tester->documents->where('document_type', 'nda')->first()?->is_signed ? 'Yes' : 'No';
                
                fputcsv($file, [
                    $tester->registration_number,
                    $tester->full_name,
                    $tester->email,
                    $tester->phone,
                    $tester->university,
                    $tester->faculty,
                    $tester->major,
                    $tester->semester,
                    $tester->status,
                    $paktaSigned,
                    $ndaSigned,
                    $tester->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
