<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\ApplicationStatusLog;
use App\Services\ProjectConversionService;
use App\Services\PermitApplicationWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;

class PaymentCallbackController extends Controller
{
    public function __construct()
    {
        // Configure Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
    }

    /**
     * Handle Midtrans payment notification callback
     */
    public function callback(Request $request)
    {
        try {
            if (!$this->isValidMidtransSignature($request)) {
                Log::warning('Midtrans callback rejected: invalid signature', [
                    'ip' => $request->ip(),
                    'order_id' => $request->input('order_id'),
                ]);

                return response()->json(['message' => 'Invalid callback signature'], 403);
            }

            $notification = new Notification();

            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? null;
            $paymentNumber = $notification->order_id;

            Log::info('Midtrans Callback Received', [
                'order_id' => $paymentNumber,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
            ]);

            DB::beginTransaction();
            try {
                $payment = Payment::where('payment_number', $paymentNumber)->lockForUpdate()->first();

                if (!$payment) {
                    DB::rollBack();
                    Log::error('Payment not found', ['order_id' => $paymentNumber]);
                    return response()->json(['message' => 'Payment not found'], 404);
                }

                if (in_array($payment->status, ['success', 'failed'], true)) {
                    DB::commit();
                    return response()->json(['message' => 'Callback already processed']);
                }

                $payment->update([
                    'gateway_response' => json_encode($notification->getResponse()),
                ]);

                if ($transactionStatus == 'capture') {
                    if ($fraudStatus == 'accept') {
                        $this->handleSuccessfulPayment($payment);
                    }
                } elseif ($transactionStatus == 'settlement') {
                    $this->handleSuccessfulPayment($payment);
                } elseif ($transactionStatus == 'pending') {
                    $payment->update(['status' => 'pending']);
                } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'], true)) {
                    $payment->update(['status' => 'failed']);
                }

                DB::commit();

                return response()->json(['message' => 'Callback processed successfully']);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error processing callback', [
                    'order_id' => $paymentNumber,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Midtrans Callback Error', [
                'error' => $e->getMessage(),
                'order_id' => $request->input('order_id'),
            ]);

            return response()->json([
                'message' => 'Error processing callback: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate Midtrans callback signature.
     */
    private function isValidMidtransSignature(Request $request): bool
    {
        $serverKey = (string) config('midtrans.server_key');
        $signature = (string) $request->input('signature_key', '');
        $orderId = (string) $request->input('order_id', '');
        $statusCode = (string) $request->input('status_code', '');
        $grossAmount = (string) $request->input('gross_amount', '');

        if ($serverKey === '' || $signature === '' || $orderId === '' || $statusCode === '' || $grossAmount === '') {
            return false;
        }

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Handle successful payment
     */
    private function handleSuccessfulPayment(Payment $payment)
    {
        $payment->update([
            'status' => 'success',
            'paid_at' => now(),
        ]);

        $quotation = $payment->quotation;
        $application = $quotation?->application;
        if (!$application) {
            throw new \RuntimeException('Application not found for payment.');
        }

        $previousStatus = $application->status;

        $paymentStatus = $payment->payment_type === 'down_payment' ? 'down_paid' : 'fully_paid';

        $workflow = app(PermitApplicationWorkflowService::class);
        $workflow->transition(
            $application,
            'payment_verified',
            'Pembayaran berhasil melalui ' . $payment->gateway_provider . ': ' . $payment->payment_number,
            'system',
            null
        );

        $application->update([
            'payment_status' => $paymentStatus,
        ]);

        // Auto-convert to project
        try {
            $conversionService = new ProjectConversionService();
            if ($conversionService->canConvert($application)) {
                $project = $conversionService->convertToProject($application);
                Log::info('Application auto-converted to project', [
                    'application_id' => $application->id,
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                ]);
            }
        } catch (\Exception $e) {
            // Log error but don't fail the payment process
            Log::error("Payment successful but project conversion failed", [
                'application_id' => $application->id,
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }

        // TODO: Send email notification to client and admin
        
        Log::info('Payment successful', [
            'payment_number' => $payment->payment_number,
            'application_number' => $application->application_number,
        ]);
    }
}
