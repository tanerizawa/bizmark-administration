<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationStatusLog;
use App\Models\Payment;
use App\Notifications\PaymentRejectedNotification;
use App\Notifications\PaymentVerifiedNotification;
use App\Services\PermitApplicationWorkflowService;
use App\Services\ProjectConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        Log::info('Payment verification started', ['payment_id' => $id, 'user_id' => Auth::id()]);

        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $payment = Payment::with('quotation.application')->whereKey($id)->lockForUpdate()->firstOrFail();

            if ($payment->status !== 'processing') {
                Log::warning('Payment not in processing status', ['payment_id' => $id, 'status' => $payment->status]);
                DB::rollBack();

                return back()->with('error', 'Payment tidak dalam status menunggu verifikasi (Status: '.$payment->status.')');
            }

            // FIX (BUG-03): Guard against null quotation/application chain
            if (! $payment->quotation || ! $payment->quotation->application) {
                Log::error('Payment references missing quotation or application', [
                    'payment_id' => $id,
                    'has_quotation' => ! is_null($payment->quotation),
                ]);
                DB::rollBack();

                return back()->with('error', 'Data pembayaran tidak lengkap (quotation/aplikasi tidak ditemukan)');
            }

            // Update payment status
            $payment->update([
                'status' => 'success',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'verification_notes' => $request->notes,
                'paid_at' => now(),
            ]);

            Log::info('Payment status updated', ['payment_id' => $id, 'status' => 'success']);

            // Update application status
            $application = $payment->quotation->application;
            $previousStatus = $application->status;

            // Determine payment status based on payment type
            $paymentStatus = $payment->payment_type === 'down_payment' ? 'down_paid' : 'fully_paid';

            $workflow = app(PermitApplicationWorkflowService::class);
            $workflow->transition(
                $application,
                'payment_verified',
                'Pembayaran terverifikasi: '.$payment->payment_number,
                'user',
                Auth::id()
            );

            $application->update([
                'payment_status' => $paymentStatus,
            ]);

            Log::info('Application status updated', [
                'application_id' => $application->id,
                'from' => $previousStatus,
                'to' => 'payment_verified',
                'payment_status' => $paymentStatus,
            ]);

            // Auto-convert to project
            $project = null;
            try {
                $conversionService = new ProjectConversionService;
                if ($conversionService->canConvert($application)) {
                    $project = $conversionService->convertToProject($application);
                    Log::info('Application converted to project', [
                        'application_id' => $application->id,
                        'project_id' => $project->id,
                    ]);
                    $successMessage = 'Pembayaran berhasil diverifikasi dan aplikasi telah dikonversi ke project: '.$project->name;
                } else {
                    Log::info('Application cannot be converted', ['application_id' => $application->id]);
                    $successMessage = 'Pembayaran berhasil diverifikasi';
                }
            } catch (\Exception $e) {
                // Log error but don't fail the payment verification
                Log::error('Payment verified but project conversion failed', [
                    'application_id' => $application->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $successMessage = 'Pembayaran berhasil diverifikasi (konversi project gagal, coba manual)';
            }

            DB::commit();

            // FIX (BUG-10): Send notification with error resilience — don't show false error if notification fails
            try {
                $client = $application->client;
                if ($client) {
                    $client->notify(new PaymentVerifiedNotification($payment, $project));
                } else {
                    Log::warning('No client found for payment notification', ['payment_id' => $id, 'application_id' => $application->id]);
                }
            } catch (\Exception $e) {
                Log::error('Notification sending failed after payment verification (payment already confirmed)', [
                    'payment_id' => $id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            Log::info('Payment verification completed successfully', ['payment_id' => $id]);

            return redirect()
                ->route('admin.payments.index')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment verification failed', [
                'payment_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Gagal verifikasi pembayaran: '.$e->getMessage());
        }
    }

    /**
     * Reject payment
     */
    public function reject(Request $request, $id)
    {
        Log::info('Payment rejection started', ['payment_id' => $id, 'user_id' => Auth::id()]);

        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $payment = Payment::with('quotation.application')->whereKey($id)->lockForUpdate()->firstOrFail();

            if ($payment->status !== 'processing') {
                Log::warning('Payment not in processing status for rejection', ['payment_id' => $id, 'status' => $payment->status]);
                DB::rollBack();

                return back()->with('error', 'Payment tidak dalam status menunggu verifikasi (Status: '.$payment->status.')');
            }

            // FIX (BUG-03): Guard against null quotation/application chain
            if (! $payment->quotation || ! $payment->quotation->application) {
                Log::error('Payment rejection references missing quotation or application', [
                    'payment_id' => $id,
                    'has_quotation' => ! is_null($payment->quotation),
                ]);
                DB::rollBack();

                return back()->with('error', 'Data pembayaran tidak lengkap (quotation/aplikasi tidak ditemukan)');
            }

            // Update payment status
            $payment->update([
                'status' => 'failed',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'verification_notes' => $request->notes,
            ]);

            Log::info('Payment status updated to failed', ['payment_id' => $id]);

            // Log status change for application
            $application = $payment->quotation->application;
            ApplicationStatusLog::create([
                'application_id' => $application->id,
                'from_status' => $application->status,
                'to_status' => $application->status,
                'changed_by_type' => 'user',
                'changed_by_id' => Auth::id(),
                'notes' => 'Pembayaran ditolak: '.$request->notes,
            ]);

            DB::commit();

            // FIX (BUG-10): Send notification with error resilience
            try {
                $client = $application->client;
                if ($client) {
                    $client->notify(new PaymentRejectedNotification($payment, $request->notes));
                } else {
                    Log::warning('No client found for payment rejection notification', ['payment_id' => $id, 'application_id' => $application->id]);
                }
            } catch (\Exception $e) {
                Log::error('Notification sending failed after payment rejection (rejection already processed)', [
                    'payment_id' => $id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            Log::info('Payment rejection completed successfully', ['payment_id' => $id]);

            return redirect()
                ->route('admin.payments.index')
                ->with('success', 'Pembayaran ditolak. Client dapat mengunggah ulang bukti pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment rejection failed', [
                'payment_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Gagal menolak pembayaran: '.$e->getMessage());
        }
    }

    /**
     * Stream or download transfer proof securely.
     */
    public function proof(Request $request, $id)
    {
        $payment = Payment::with(['quotation.application.client', 'client'])->findOrFail($id);

        $location = $payment->resolveTransferProofLocation();
        if (! $location) {
            abort(404, 'Bukti transfer tidak ditemukan.');
        }

        [$disk, $path] = $location;
        $storage = Storage::disk($disk);
        $mimeType = $storage->mimeType($path) ?: 'application/octet-stream';

        if ($request->boolean('download')) {
            return $storage->download($path, basename($path), [
                'Content-Type' => $mimeType,
            ]);
        }

        return $storage->response($path, null, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.basename($path).'"',
        ]);
    }
}
