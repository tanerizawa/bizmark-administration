<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use App\Models\PaymentMethod;
use App\Models\Project;
use App\Models\ProjectPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DirectIncomeController extends Controller
{
    /**
     * Rules yang dipakai store() & update().
     */
    private function rules(): array
    {
        return [
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'cash_account_id' => 'nullable|exists:cash_accounts,id',
            'description' => 'required|string|max:1000',
            'reference' => 'nullable|string|max:255',
        ];
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate($this->rules());

        try {
            DB::beginTransaction();

            $paymentMethod = PaymentMethod::findOrFail($validated['payment_method_id']);

            if ($paymentMethod->requires_cash_account && empty($validated['cash_account_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Metode pembayaran ini memerlukan rekening/kas tujuan',
                ], 422);
            }

            $payment = new ProjectPayment();
            $payment->project_id = $project->id;
            $payment->invoice_id = null;
            $payment->payment_date = $validated['payment_date'];
            $payment->amount = $validated['amount'];
            $payment->payment_method = $paymentMethod->code;
            $payment->bank_account_id = $validated['cash_account_id'] ?? null;
            $payment->reference_number = $validated['reference'] ?? null;
            $payment->description = $validated['description'];
            $payment->payment_type = 'other';
            $payment->created_by = auth()->id();
            $payment->save();

            // Cash account balance update ditangani ProjectPayment observer.
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pemasukan berhasil dicatat! Jumlah: Rp ' . number_format($validated['amount'], 0, ',', '.'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing direct income: ' . $e->getMessage(), [
                'project_id' => $project->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat pemasukan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit(Project $project, ProjectPayment $payment)
    {
        if (!$this->belongsToProject($payment, $project)) {
            return response()->json(['success' => false, 'message' => 'Invalid payment'], 403);
        }

        return response()->json([
            'success' => true,
            'payment' => [
                'id' => $payment->id,
                'payment_date' => $payment->payment_date->format('Y-m-d'),
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'cash_account_id' => $payment->bank_account_id,
                'reference_number' => $payment->reference_number,
                'description' => $payment->description,
            ],
        ]);
    }

    public function update(Request $request, Project $project, ProjectPayment $payment)
    {
        if (!$this->belongsToProject($payment, $project)) {
            return response()->json(['success' => false, 'message' => 'Invalid payment'], 403);
        }

        $validated = $request->validate($this->rules());

        try {
            DB::beginTransaction();

            $paymentMethod = PaymentMethod::findOrFail($validated['payment_method_id']);

            if ($paymentMethod->requires_cash_account && empty($validated['cash_account_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Metode pembayaran ini memerlukan rekening/kas tujuan',
                ], 422);
            }

            $oldAmount = $payment->amount;
            $oldBankAccountId = $payment->bank_account_id;

            $payment->payment_date = $validated['payment_date'];
            $payment->amount = $validated['amount'];
            $payment->payment_method = $paymentMethod->code;
            $payment->bank_account_id = $validated['cash_account_id'] ?? null;
            $payment->reference_number = $validated['reference'] ?? null;
            $payment->description = $validated['description'];
            $payment->save();

            // Manual balance adjust karena update bypass observer.
            if ($oldBankAccountId && ($oldAccount = CashAccount::find($oldBankAccountId))) {
                $oldAccount->current_balance -= $oldAmount;
                $oldAccount->save();
            }

            if ($payment->bank_account_id && ($newAccount = CashAccount::find($payment->bank_account_id))) {
                $newAccount->current_balance += $payment->amount;
                $newAccount->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pemasukan berhasil diperbarui! Jumlah: Rp ' . number_format($validated['amount'], 0, ',', '.'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating direct income: ' . $e->getMessage(), [
                'project_id' => $project->id,
                'payment_id' => $payment->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pemasukan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Project $project, ProjectPayment $payment)
    {
        if (!$this->belongsToProject($payment, $project)) {
            return response()->json(['success' => false, 'message' => 'Invalid payment'], 403);
        }

        try {
            DB::beginTransaction();

            $amount = $payment->amount;
            $payment->delete();  // observer handles cash balance

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pemasukan berhasil dihapus. Jumlah: Rp ' . number_format($amount, 0, ',', '.'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting direct income: ' . $e->getMessage(), [
                'project_id' => $project->id,
                'payment_id' => $payment->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pemasukan: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function belongsToProject(ProjectPayment $payment, Project $project): bool
    {
        return $payment->project_id === $project->id && $payment->invoice_id === null;
    }
}
