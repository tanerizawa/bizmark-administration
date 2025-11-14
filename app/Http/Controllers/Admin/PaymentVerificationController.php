<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PermitApplication;
use App\Models\ApplicationStatusLog;
use App\Services\ProjectConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\PaymentVerifiedNotification;
use App\Notifications\PaymentRejectedNotification;

class PaymentVerificationController extends Controller
{
    /**
     * Display list of payments pending verification
     */
    public function index()
    {
        $payments = Payment::with(['quotation.application', 'client'])
            ->where('payment_method', 'manual')
            ->whereIn('status', ['processing', 'pending'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Display payment detail for verification
     */
    public function show($id)
    {
        $payment = Payment::with(['quotation.application.client', 'client'])
            ->findOrFail($id);

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Verify (approve) payment
     */
    public function verify(Request $request, $id)
    {
        \Log::info('Payment verification started', ['payment_id' => $id, 'user_id' => Auth::id()]);
        
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $payment = Payment::with('quotation.application')->findOrFail($id);

        if ($payment->status !== 'processing') {
            \Log::warning('Payment not in processing status', ['payment_id' => $id, 'status' => $payment->status]);
            return back()->with('error', 'Payment tidak dalam status menunggu verifikasi (Status: ' . $payment->status . ')');
        }

        DB::beginTransaction();
        try {
            // Update payment status
            $payment->update([
                'status' => 'success',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'verification_notes' => $request->notes,
                'paid_at' => now(),
            ]);
            
            \Log::info('Payment status updated', ['payment_id' => $id, 'status' => 'success']);

            // Update application status
            $application = $payment->quotation->application;
            $previousStatus = $application->status;
            
            // Determine payment status based on payment type
            $paymentStatus = $payment->payment_type === 'down_payment' ? 'down_paid' : 'fully_paid';
            
            $application->update([
                'status' => 'payment_verified',
                'payment_status' => $paymentStatus,
            ]);
            
            \Log::info('Application status updated', [
                'application_id' => $application->id,
                'from' => $previousStatus,
                'to' => 'payment_verified',
                'payment_status' => $paymentStatus
            ]);

            // Log status change
            ApplicationStatusLog::create([
                'application_id' => $application->id,
                'from_status' => $previousStatus,
                'to_status' => 'payment_verified',
                'changed_by_type' => 'user',
                'changed_by_id' => Auth::id(),
                'notes' => 'Pembayaran terverifikasi: ' . $payment->payment_number,
            ]);

            // Auto-convert to project
            $project = null;
            try {
                $conversionService = new ProjectConversionService();
                if ($conversionService->canConvert($application)) {
                    $project = $conversionService->convertToProject($application);
                    \Log::info('Application converted to project', [
                        'application_id' => $application->id,
                        'project_id' => $project->id
                    ]);
                    $successMessage = 'Pembayaran berhasil diverifikasi dan aplikasi telah dikonversi ke project: ' . $project->name;
                } else {
                    \Log::info('Application cannot be converted', ['application_id' => $application->id]);
                    $successMessage = 'Pembayaran berhasil diverifikasi';
                }
            } catch (\Exception $e) {
                // Log error but don't fail the payment verification
                \Log::error("Payment verified but project conversion failed", [
                    'application_id' => $application->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $successMessage = 'Pembayaran berhasil diverifikasi (konversi project gagal, coba manual)';
            }

            DB::commit();
            
            // Send notification to client
            $client = $application->client;
            $client->notify(new PaymentVerifiedNotification($payment, $project));
            
            \Log::info('Payment verification completed successfully', ['payment_id' => $id]);

            return redirect()
                ->route('admin.payments.index')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payment verification failed', [
                'payment_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal verifikasi pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Reject payment
     */
    public function reject(Request $request, $id)
    {
        \Log::info('Payment rejection started', ['payment_id' => $id, 'user_id' => Auth::id()]);
        
        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $payment = Payment::with('quotation.application')->findOrFail($id);

        if ($payment->status !== 'processing') {
            \Log::warning('Payment not in processing status for rejection', ['payment_id' => $id, 'status' => $payment->status]);
            return back()->with('error', 'Payment tidak dalam status menunggu verifikasi (Status: ' . $payment->status . ')');
        }

        DB::beginTransaction();
        try {
            // Update payment status
            $payment->update([
                'status' => 'failed',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'verification_notes' => $request->notes,
            ]);
            
            \Log::info('Payment status updated to failed', ['payment_id' => $id]);

            // Log status change for application
            $application = $payment->quotation->application;
            ApplicationStatusLog::create([
                'application_id' => $application->id,
                'from_status' => $application->status,
                'to_status' => $application->status,
                'changed_by_type' => 'user',
                'changed_by_id' => Auth::id(),
                'notes' => 'Pembayaran ditolak: ' . $request->notes,
            ]);

            DB::commit();
            
            // Send notification to client
            $client = $application->client;
            $client->notify(new PaymentRejectedNotification($payment, $request->notes));
            
            \Log::info('Payment rejection completed successfully', ['payment_id' => $id]);

            return redirect()
                ->route('admin.payments.index')
                ->with('success', 'Pembayaran ditolak. Client dapat mengunggah ulang bukti pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payment rejection failed', [
                'payment_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal menolak pembayaran: ' . $e->getMessage());
        }
    }
}
