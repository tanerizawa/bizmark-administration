<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\PermitApplication;
use App\Models\Quotation;
use App\Models\ApplicationStatusLog;
use App\Models\User;
use App\Notifications\AdminClientActionNotification;
use App\Services\PermitApplicationWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientQuotationController extends Controller
{
    /**
     * Display the quotation for a permit application.
     */
    public function show($applicationId)
    {
        $application = PermitApplication::with([
            'client',
            'permitType',
            'quotation.creator'
        ])
        ->where('client_id', Auth::guard('client')->id())
        ->findOrFail($applicationId);
        
        // Check if quotation exists
        if (!$application->quotation) {
            return redirect()
                ->route('client.applications.show', $applicationId)
                ->with('error', 'Quotation belum tersedia untuk aplikasi ini');
        }
        
        $quotation = $application->quotation;
        
        // Check if quotation is expired
        if ($quotation->isExpired() && $quotation->status !== 'accepted') {
            $quotation->update(['status' => 'expired']);
        }
        
        return view('client.applications.quotation', compact('application', 'quotation'));
    }
    
    /**
     * Accept the quotation.
     */
    public function accept($applicationId)
    {
        $application = PermitApplication::with('quotation')
            ->where('client_id', Auth::guard('client')->id())
            ->findOrFail($applicationId);
        
        $quotation = $application->quotation;
        
        if (!$quotation) {
            return back()->with('error', 'Quotation tidak ditemukan');
        }
        
        if ($quotation->status === 'accepted') {
            return back()->with('info', 'Quotation sudah diterima sebelumnya');
        }
        
        if ($quotation->isExpired()) {
            return back()->with('error', 'Quotation sudah kadaluarsa. Silakan hubungi admin.');
        }
        
        DB::beginTransaction();
        try {
            $application = PermitApplication::with('quotation')
                ->where('client_id', Auth::guard('client')->id())
                ->whereKey($applicationId)
                ->lockForUpdate()
                ->firstOrFail();

            $quotation = $application->quotation;
            if (!$quotation) {
                DB::rollBack();
                return back()->with('error', 'Quotation tidak ditemukan');
            }

            // Update quotation status
            $quotation->update([
                'status' => 'accepted',
                'accepted_at' => now(),
            ]);
            
            app(PermitApplicationWorkflowService::class)->transition(
                $application,
                'quotation_accepted',
                'Client menerima quotation',
                'client',
                Auth::guard('client')->id()
            );
            
            $notification = new AdminClientActionNotification(
                'quotation_accepted',
                $application->application_number,
                $application->id,
                Auth::guard('client')->user()->name
            );
            User::where('is_active', true)
                ->whereHas('role', fn ($q) => $q->where('name', 'admin'))
                ->get()
                ->each->notify($notification);

            DB::commit();

            return redirect()
                ->route('client.applications.show', $applicationId)
                ->with('success', 'Quotation berhasil diterima. Silakan lakukan pembayaran.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menerima quotation: ' . $e->getMessage());
        }
    }
    
    /**
     * Reject the quotation.
     */
    public function reject(Request $request, $applicationId)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);
        
        $application = PermitApplication::with('quotation')
            ->where('client_id', Auth::guard('client')->id())
            ->findOrFail($applicationId);
        
        $quotation = $application->quotation;
        
        if (!$quotation) {
            return back()->with('error', 'Quotation tidak ditemukan');
        }
        
        if ($quotation->status === 'rejected') {
            return back()->with('info', 'Quotation sudah ditolak sebelumnya');
        }
        
        if ($quotation->status === 'accepted') {
            return back()->with('error', 'Quotation yang sudah diterima tidak bisa ditolak');
        }
        
        DB::beginTransaction();
        try {
            $application = PermitApplication::with('quotation')
                ->where('client_id', Auth::guard('client')->id())
                ->whereKey($applicationId)
                ->lockForUpdate()
                ->firstOrFail();

            $quotation = $application->quotation;
            if (!$quotation) {
                DB::rollBack();
                return back()->with('error', 'Quotation tidak ditemukan');
            }

            // Update quotation status
            $quotation->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);
            
            $application->update([
                'admin_notes' => 'Quotation ditolak oleh client. Alasan: ' . $request->rejection_reason,
            ]);

            app(PermitApplicationWorkflowService::class)->transition(
                $application,
                'under_review',
                'Client menolak quotation: ' . $request->rejection_reason,
                'client',
                Auth::guard('client')->id()
            );
            
            $notification = new AdminClientActionNotification(
                'quotation_rejected',
                $application->application_number,
                $application->id,
                Auth::guard('client')->user()->name,
                $request->rejection_reason
            );
            User::where('is_active', true)
                ->whereHas('role', fn ($q) => $q->where('name', 'admin'))
                ->get()
                ->each->notify($notification);

            DB::commit();

            return redirect()
                ->route('client.applications.show', $applicationId)
                ->with('success', 'Quotation ditolak. Admin akan menghubungi Anda.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menolak quotation: ' . $e->getMessage());
        }
    }
}
