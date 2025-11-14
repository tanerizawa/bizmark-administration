<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PermitApplication;
use App\Models\Quotation;
use App\Models\ApplicationStatusLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Midtrans\Config;
use Midtrans\Snap;
use App\Notifications\PaymentUploadedNotification;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Configure Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Display payment selection page
     */
    public function show($applicationId)
    {
        $application = PermitApplication::with(['quotation', 'client'])
            ->where('client_id', Auth::guard('client')->id())
            ->findOrFail($applicationId);

        if (!$application->quotation || $application->quotation->status !== 'accepted') {
            return redirect()
                ->route('client.applications.show', $applicationId)
                ->with('error', 'Quotation belum diterima atau tidak tersedia');
        }

        if (!in_array($application->status, ['quotation_accepted', 'payment_pending'])) {
            return redirect()
                ->route('client.applications.show', $applicationId)
                ->with('error', 'Status aplikasi tidak valid untuk pembayaran');
        }

        $quotation = $application->quotation;
        $payments = Payment::where('quotation_id', $quotation->id)->get();
        
        // Check for pending payment verification
        $pendingPayment = Payment::where('quotation_id', $quotation->id)
            ->where('status', 'processing')
            ->latest()
            ->first();

        return view('client.payments.show', compact('application', 'quotation', 'payments', 'pendingPayment'));
    }

    /**
     * Initiate Midtrans payment
     */
    public function initiate(Request $request, $applicationId)
    {
        $request->validate([
            'payment_type' => 'required|in:down_payment,full_payment',
        ]);

        $application = PermitApplication::with(['quotation', 'client'])
            ->where('client_id', Auth::guard('client')->id())
            ->findOrFail($applicationId);

        $quotation = $application->quotation;

        if (!$quotation || $quotation->status !== 'accepted') {
            return back()->with('error', 'Quotation tidak valid');
        }

        // Check if there's already a pending payment
        $pendingPayment = Payment::where('quotation_id', $quotation->id)
            ->where('status', 'processing')
            ->exists();

        if ($pendingPayment) {
            return response()->json([
                'success' => false,
                'message' => 'Masih ada pembayaran yang sedang diverifikasi. Harap tunggu hingga proses verifikasi selesai.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Determine payment amount
            $amount = $request->payment_type === 'down_payment' 
                ? $quotation->down_payment_amount 
                : $quotation->total_amount;

            // Create payment record
            $payment = Payment::create([
                'payment_number' => $this->generatePaymentNumber(),
                'payable_type' => PermitApplication::class,
                'payable_id' => $application->id,
                'client_id' => $application->client_id,
                'quotation_id' => $quotation->id,
                'amount' => $amount,
                'payment_type' => $request->payment_type === 'full_payment' ? 'final_payment' : 'down_payment',
                'payment_method' => 'virtual_account',
                'gateway_provider' => 'midtrans',
                'status' => 'pending',
            ]);

            // Prepare transaction details for Midtrans
            $transactionDetails = [
                'order_id' => $payment->payment_number,
                'gross_amount' => (int) $amount,
            ];

            $itemDetails = [
                [
                    'id' => $quotation->id,
                    'price' => (int) $amount,
                    'quantity' => 1,
                    'name' => 'Payment for ' . $application->application_number,
                ]
            ];

            $customerDetails = [
                'first_name' => $application->client->contact_person,
                'email' => $application->client->email,
                'phone' => $application->client->phone,
            ];

            $transactionData = [
                'transaction_details' => $transactionDetails,
                'item_details' => $itemDetails,
                'customer_details' => $customerDetails,
                'enabled_payments' => config('midtrans.enabled_payments'),
                'expiry' => [
                    'duration' => config('midtrans.expiry_duration'),
                    'unit' => 'minutes'
                ],
            ];

            // Get Snap token from Midtrans
            $snapToken = Snap::getSnapToken($transactionData);
            
            // Store snap token in payment
            $payment->update([
                'gateway_transaction_id' => $snapToken,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'payment_id' => $payment->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle manual payment (bank transfer)
     */
    public function manual(Request $request, $applicationId)
    {
        $request->validate([
            'payment_type' => 'required|in:down_payment,full_payment',
            'bank_name' => 'required|string|max:100',
            'account_holder' => 'required|string|max:255',
            'transfer_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $application = PermitApplication::with(['quotation', 'client'])
            ->where('client_id', Auth::guard('client')->id())
            ->findOrFail($applicationId);

        $quotation = $application->quotation;

        if (!$quotation || $quotation->status !== 'accepted') {
            return back()->with('error', 'Quotation tidak valid');
        }

        if (!in_array($application->status, ['quotation_accepted', 'payment_pending'])) {
            return back()->with('error', 'Status aplikasi tidak valid untuk pembayaran');
        }

        // Check if there's already a pending payment
        $pendingPayment = Payment::where('quotation_id', $quotation->id)
            ->where('status', 'processing')
            ->exists();

        if ($pendingPayment) {
            return back()->with('error', 'Masih ada pembayaran yang sedang diverifikasi. Harap tunggu hingga proses verifikasi selesai.');
        }

        DB::beginTransaction();
        try {
            // Determine payment amount
            $amount = $request->payment_type === 'down_payment' 
                ? $quotation->down_payment_amount 
                : $quotation->total_amount;

            // Upload transfer proof
            $proofPath = $request->file('transfer_proof')->store('payment-proofs', 'public');

            // Create payment record
            $payment = Payment::create([
                'payment_number' => $this->generatePaymentNumber(),
                'payable_type' => PermitApplication::class,
                'payable_id' => $application->id,
                'client_id' => $application->client_id,
                'quotation_id' => $quotation->id,
                'amount' => $amount,
                'payment_type' => $request->payment_type === 'full_payment' ? 'final_payment' : 'down_payment',
                'payment_method' => 'manual',
                'bank_name' => $request->bank_name,
                'account_holder' => $request->account_holder,
                'transfer_proof_path' => $proofPath,
                'status' => 'processing',
            ]);

            // Update application status
            if ($application->status === 'quotation_accepted') {
                $previousStatus = $application->status;
                $application->update(['status' => 'payment_pending']);

                // Log status change
                ApplicationStatusLog::create([
                    'application_id' => $application->id,
                    'from_status' => $previousStatus,
                    'to_status' => 'payment_pending',
                    'changed_by_type' => 'client',
                    'changed_by_id' => Auth::guard('client')->id(),
                    'notes' => 'Client mengunggah bukti pembayaran manual',
                ]);
            }

            DB::commit();

            // Notify all admins about new payment upload
            $admins = User::where('guard', 'web')->get();
            Notification::send($admins, new PaymentUploadedNotification($payment));

            return redirect()
                ->route('client.payments.success', ['id' => $applicationId, 'paymentId' => $payment->id])
                ->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengunggah bukti pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Payment success page
     */
    public function success($applicationId, $paymentId)
    {
        $application = PermitApplication::with(['quotation'])
            ->where('client_id', Auth::guard('client')->id())
            ->findOrFail($applicationId);

        $payment = Payment::where('quotation_id', $application->quotation->id)
            ->findOrFail($paymentId);

        return view('client.payments.success', compact('application', 'payment'));
    }

    /**
     * Generate payment number
     */
    private function generatePaymentNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        
        $lastPayment = Payment::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastPayment ? 
            intval(substr($lastPayment->payment_number, -4)) + 1 : 1;
        
        return sprintf('PAY-%s%s-%04d', $year, $month, $nextNumber);
    }
}
