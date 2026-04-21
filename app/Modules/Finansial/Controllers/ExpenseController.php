<?php

namespace App\Modules\Finansial\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\Project;
use App\Models\ProjectExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    /**
     * Rule set yang sama untuk store & update.
     */
    private function rules(): array
    {
        return [
            'description' => 'nullable|string|max:500',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'category' => ['required', Rule::in(ProjectExpense::categoryKeys())],
            'vendor_name' => 'nullable|string|max:255',
            'payment_method' => ['required', Rule::in(PaymentMethod::activeCodes())],
            'bank_account_id' => 'nullable|exists:cash_accounts,id',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'is_receivable' => 'nullable|boolean',
            'receivable_from' => 'nullable|string|max:255',
            'receivable_status' => 'nullable|in:pending,partial,paid',
            'receivable_paid_amount' => 'nullable|numeric|min:0',
            'receivable_notes' => 'nullable|string',
        ];
    }

    /**
     * Normalize receivable-related fields so kolom NOT NULL tetap terisi.
     */
    private function normalizeReceivableFields(array $validated, Request $request, ?ProjectExpense $existing = null): array
    {
        $validated['is_billable'] = false;
        $validated['is_receivable'] = $request->has('is_receivable') ? (bool) $request->is_receivable : false;

        if (!isset($validated['receivable_paid_amount'])) {
            $validated['receivable_paid_amount'] = $existing->receivable_paid_amount ?? 0;
        }

        if (!empty($validated['is_receivable'])) {
            if (empty($validated['receivable_status'])) {
                $validated['receivable_status'] = 'pending';
            }
        } else {
            $validated['receivable_status'] = 'paid';
        }

        return $validated;
    }

    public function store(Request $request, Project $project)
    {
        try {
            $validated = $request->validate($this->rules());
            $validated = $this->normalizeReceivableFields($validated, $request);

            if ($request->hasFile('receipt_file')) {
                $validated['receipt_file'] = $request->file('receipt_file')->store('receipts', 'public');
            }

            $validated['created_by'] = auth()->id();

            $expense = $project->expenses()->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Pengeluaran berhasil disimpan',
                'expense' => $expense,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error storing expense: ' . $e->getMessage(), [
                'project_id' => $project->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get expense data for edit form.
     */
    public function show(ProjectExpense $expense)
    {
        return response()->json([
            'id' => $expense->id,
            'expense_date' => $expense->expense_date->format('Y-m-d'),
            'amount' => $expense->amount,
            'category' => $expense->category,
            'vendor_name' => $expense->vendor_name,
            'payment_method' => $expense->payment_method,
            'bank_account_id' => $expense->bank_account_id,
            'description' => $expense->description,
            'is_billable' => $expense->is_billable,
            'is_receivable' => $expense->is_receivable,
            'receivable_from' => $expense->receivable_from,
            'receivable_status' => $expense->receivable_status,
            'receivable_notes' => $expense->receivable_notes,
            'receivable_paid_amount' => $expense->receivable_paid_amount,
            'receipt_file' => $expense->receipt_file,
            'invoiced' => false,
        ]);
    }

    public function update(Request $request, ProjectExpense $expense)
    {
        try {
            $validated = $request->validate($this->rules());
            $validated = $this->normalizeReceivableFields($validated, $request, $expense);

            if ($request->hasFile('receipt_file')) {
                if ($expense->receipt_file) {
                    Storage::disk('public')->delete($expense->receipt_file);
                }
                $validated['receipt_file'] = $request->file('receipt_file')->store('receipts', 'public');
            }

            $expense->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Pengeluaran berhasil diupdate',
                'expense' => $expense,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating expense: ' . $e->getMessage(), [
                'expense_id' => $expense->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(ProjectExpense $expense)
    {
        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted',
        ]);
    }

    public function deleteReceipt(ProjectExpense $expense)
    {
        try {
            if ($expense->receipt_file) {
                Storage::disk('public')->delete($expense->receipt_file);
                $expense->receipt_file = null;
                $expense->save();

                return response()->json([
                    'success' => true,
                    'message' => 'File berhasil dihapus',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file untuk dihapus',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting receipt file: ' . $e->getMessage(), [
                'expense_id' => $expense->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus file: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function markInvoiced(Request $request, ProjectExpense $expense)
    {
        try {
            $expense->update([
                'notes' => $expense->notes . ($request->invoice_note ? "\nInvoice: " . $request->invoice_note : "\nMarked as invoiced"),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Expense marked as invoiced',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark expense as invoiced: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function recordReceivablePayment(Request $request, ProjectExpense $expense)
    {
        try {
            $request->validate([
                'payment_amount' => 'required|numeric|min:0',
            ]);

            $paymentAmount = $request->payment_amount;
            $newPaidAmount = $expense->receivable_paid_amount + $paymentAmount;

            if ($newPaidAmount > $expense->amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount exceeds remaining balance',
                ], 400);
            }

            $status = 'pending';
            if ($newPaidAmount >= $expense->amount) {
                $status = 'paid';
            } elseif ($newPaidAmount > 0) {
                $status = 'partial';
            }

            $notes = $expense->receivable_notes ?? '';
            $notes .= "\n" . now()->format('d M Y H:i') . ' - Pembayaran: Rp ' . number_format($paymentAmount, 0, ',', '.');
            if ($request->payment_notes) {
                $notes .= ' (' . $request->payment_notes . ')';
            }

            $expense->update([
                'receivable_paid_amount' => $newPaidAmount,
                'receivable_status' => $status,
                'receivable_notes' => trim($notes),
                'updated_at' => now(),
            ]);

            $remaining = $expense->amount - $newPaidAmount;
            $message = $status === 'paid'
                ? 'Piutang lunas!'
                : 'Pembayaran tercatat. Sisa: Rp ' . number_format($remaining, 0, ',', '.');

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function removeReceivable(ProjectExpense $expense)
    {
        try {
            $expense->update([
                'is_receivable' => false,
                'receivable_from' => null,
                'receivable_status' => 'pending',
                'receivable_paid_amount' => 0,
                'receivable_notes' => $expense->receivable_notes . "\n[" . now()->format('d M Y H:i') . '] Status kasbon dihapus, dikembalikan ke pengeluaran biasa',
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status kasbon dihapus. Pengeluaran tetap tercatat sebagai pengeluaran biasa.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove receivable: ' . $e->getMessage(),
            ], 500);
        }
    }
}
