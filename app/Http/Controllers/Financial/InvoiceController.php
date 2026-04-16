<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\Project;
use App\Models\ProjectLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    /**
     * Store a new invoice untuk sebuah project.
     */
    public function store(Request $request, Project $project)
    {
        if (!$project->client_id || !$project->client) {
            return response()->json([
                'success' => false,
                'message' => 'Proyek ini belum memiliki klien. Silakan tambahkan klien terlebih dahulu.',
            ], 422);
        }

        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'client_tax_id' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $client = $project->client;

            $invoice = $project->invoices()->create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'tax_rate' => $validated['tax_rate'],
                'notes' => $validated['notes'] ?? null,
                'client_name' => $client->name,
                'client_address' => $client->address ?? $client->company ?? '',
                'client_tax_id' => $validated['client_tax_id'] ?? null,
                'status' => 'draft',
            ]);

            foreach ($validated['items'] as $index => $item) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'order' => $index,
                ]);
            }

            // Totals dihitung otomatis oleh InvoiceItem observer
            $invoice->refresh();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully',
                'invoice' => $invoice->load('items'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['items', 'project', 'paymentSchedules']);

        return view('invoices.show', compact('invoice'));
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,sent,partial,paid,overdue,cancelled',
        ]);

        $invoice->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Invoice status updated',
            'invoice' => $invoice,
        ]);
    }

    public function recordPayment(Request $request, Invoice $invoice)
    {
        $allowedPaymentMethods = PaymentMethod::activeCodesRequiringAccount();

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => ['required', Rule::in($allowedPaymentMethods)],
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $accountType = PaymentMethod::accountTypeFor($validated['payment_method']);
            $cashAccount = CashAccount::active()
                ->where('account_type', $accountType)
                ->orderBy('id', 'asc')
                ->first();

            if (!$cashAccount) {
                throw new \Exception('No active cash account found for payment method: ' . $validated['payment_method']);
            }

            $invoice->recordPayment($validated['amount']);

            $invoice->project->paymentSchedules()->create([
                'invoice_id' => $invoice->id,
                'description' => "Payment for Invoice {$invoice->invoice_number}",
                'amount' => $validated['amount'],
                'due_date' => $validated['payment_date'],
                'paid_date' => $validated['payment_date'],
                'status' => 'paid',
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $cashAccount->current_balance += $validated['amount'];
            $cashAccount->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dicatat dan saldo kas diperbarui',
                'invoice' => $invoice->fresh(),
                'cash_account' => $cashAccount->account_name,
                'new_balance' => $cashAccount->current_balance,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Invoice $invoice)
    {
        try {
            if ($invoice->payments()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus invoice yang sudah memiliki pembayaran. Hapus pembayaran terlebih dahulu atau gunakan fitur "Batalkan Invoice".',
                ], 422);
            }

            if ($invoice->status !== 'draft' && $invoice->status !== 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya invoice dengan status Draft atau Cancelled yang dapat dihapus. Invoice dengan status lain harus dibatalkan terlebih dahulu.',
                ], 422);
            }

            $projectId = $invoice->project_id;
            $invoiceNumber = $invoice->invoice_number;

            $invoice->items()->delete();
            $invoice->paymentSchedules()->delete();
            $invoice->delete();

            ProjectLog::create([
                'project_id' => $projectId,
                'user_id' => auth()->id(),
                'description' => "Invoice {$invoiceNumber} dihapus",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting invoice: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus invoice',
            ], 500);
        }
    }

    /**
     * Stream invoice PDF (preview in browser).
     */
    public function downloadPDF(Invoice $invoice)
    {
        $invoice->load(['items', 'project', 'paymentSchedules' => function ($query) {
            $query->where('status', 'paid')->orderBy('paid_date', 'desc');
        }]);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        return $pdf->stream('Invoice-' . $invoice->invoice_number . '.pdf');
    }
}
