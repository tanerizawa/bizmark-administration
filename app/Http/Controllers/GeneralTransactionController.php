<?php

namespace App\Http\Controllers;

use App\Models\ProjectPayment;
use App\Models\ProjectExpense;
use App\Models\CashAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GeneralTransactionController extends Controller
{
    /**
     * Store a general income (non-project payment)
     */
    public function storeIncome(Request $request)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:transfer,cash,check,other',
            'bank_account_id' => 'required|exists:cash_accounts,id',
            'description' => 'nullable|string|max:1000',
            'reference_number' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $income = ProjectPayment::create([
                'project_id' => null, // General income (not project-related)
                'invoice_id' => null, // Not invoice-related
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_type' => 'other',
                'payment_method' => $validated['payment_method'],
                'bank_account_id' => $validated['bank_account_id'],
                'description' => $validated['description'] ?? 'Pemasukan Umum',
                'reference_number' => $validated['reference_number'] ?? null,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pemasukan umum berhasil ditambahkan',
                'data' => $income->load(['bankAccount', 'createdBy'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create general income: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan pemasukan umum: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a general income
     */
    public function updateIncome(Request $request, $id)
    {
        $income = ProjectPayment::whereNull('project_id')
                                ->whereNull('invoice_id')
                                ->findOrFail($id);

        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:transfer,cash,check,other',
            'bank_account_id' => 'required|exists:cash_accounts,id',
            'description' => 'nullable|string|max:1000',
            'reference_number' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $income->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pemasukan umum berhasil diperbarui',
                'data' => $income->fresh(['bankAccount', 'createdBy'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to update general income: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pemasukan umum: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a general income
     */
    public function destroyIncome($id)
    {
        $income = ProjectPayment::whereNull('project_id')
                                ->whereNull('invoice_id')
                                ->findOrFail($id);

        DB::beginTransaction();
        try {
            $income->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pemasukan umum berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to delete general income: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pemasukan umum: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a general expense (non-project expense)
     */
    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|string',
            'payment_method' => 'required|in:transfer,cash,check,other',
            'bank_account_id' => 'required|exists:cash_accounts,id',
            'vendor_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $expense = ProjectExpense::create([
                'project_id' => null, // General expense (not project-related)
                'expense_date' => $validated['expense_date'],
                'amount' => $validated['amount'],
                'category' => $validated['category'],
                'payment_method' => $validated['payment_method'],
                'bank_account_id' => $validated['bank_account_id'],
                'vendor_name' => $validated['vendor_name'] ?? null,
                'description' => $validated['description'] ?? 'Pengeluaran Umum',
                'is_billable' => false, // General expenses are not billable
                'is_receivable' => false,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengeluaran umum berhasil ditambahkan',
                'data' => $expense->load(['bankAccount', 'createdBy'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create general expense: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan pengeluaran umum: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a general expense
     */
    public function updateExpense(Request $request, $id)
    {
        $expense = ProjectExpense::whereNull('project_id')
                                 ->findOrFail($id);

        $validated = $request->validate([
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|string',
            'payment_method' => 'required|in:transfer,cash,check,other',
            'bank_account_id' => 'required|exists:cash_accounts,id',
            'vendor_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $expense->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengeluaran umum berhasil diperbarui',
                'data' => $expense->fresh(['bankAccount', 'createdBy'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to update general expense: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pengeluaran umum: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a general expense
     */
    public function destroyExpense($id)
    {
        $expense = ProjectExpense::whereNull('project_id')
                                 ->findOrFail($id);

        DB::beginTransaction();
        try {
            $expense->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengeluaran umum berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to delete general expense: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pengeluaran umum: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a single general income for editing
     */
    public function getIncome($id)
    {
        $income = ProjectPayment::whereNull('project_id')
                                ->whereNull('invoice_id')
                                ->with(['bankAccount', 'createdBy'])
                                ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $income
        ]);
    }

    /**
     * Get a single general expense for editing
     */
    public function getExpense($id)
    {
        $expense = ProjectExpense::whereNull('project_id')
                                 ->with(['bankAccount', 'createdBy'])
                                 ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $expense
        ]);
    }
}
