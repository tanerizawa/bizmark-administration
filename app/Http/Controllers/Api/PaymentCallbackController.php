<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\ApplicationStatusLog;
use App\Services\ProjectConversionService;
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
            $notification = new Notification();

            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? null;
            $paymentNumber = $notification->order_id;

            Log::info('Midtrans Callback Received', [
                'order_id' => $paymentNumber,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
            ]);

            $payment = Payment::where('payment_number', $paymentNumber)->first();

            if (!$payment) {
                Log::error('Payment not found', ['order_id' => $paymentNumber]);
                return response()->json(['message' => 'Payment not found'], 404);
            }

            DB::beginTransaction();
            try {
                // Update payment with gateway response
                $payment->update([
                    'gateway_response' => json_encode($notification->getResponse()),
                ]);

                // Handle transaction status
                if ($transactionStatus == 'capture') {
                    if ($fraudStatus == 'accept') {
                        $this->handleSuccessfulPayment($payment);
                    }
                } elseif ($transactionStatus == 'settlement') {
                    $this->handleSuccessfulPayment($payment);
                } elseif ($transactionStatus == 'pending') {
                    $payment->update(['status' => 'pending']);
                } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
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
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Error processing callback: ' . $e->getMessage()
            ], 500);
        }
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

        // Update application status
        $application = $payment->quotation->application;
        $previousStatus = $application->status;
        
        $application->update([
            'status' => 'payment_verified',
            'payment_status' => 'paid',
        ]);

        // Log status change
        ApplicationStatusLog::create([
            'application_id' => $application->id,
            'from_status' => $previousStatus,
            'to_status' => 'payment_verified',
            'changed_by_type' => 'system',
            'changed_by_id' => null,
            'notes' => 'Pembayaran berhasil melalui ' . $payment->gateway_provider . ': ' . $payment->payment_number,
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
