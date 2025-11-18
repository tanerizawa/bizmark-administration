<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentMethod;
use App\Models\PaymentSchedule;
use App\Models\ProjectExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;

class FinancialController extends Controller
{
    /**
     * Show financial tab for a project.
     */
    public function index(Project $project)
    {
        $project->load([
            'invoices.items',
            'paymentSchedules',
            'expenses',
        ]);

        // Calculate financial overview
        $totalBudget = $project->contract_value ?? 0;
        $totalInvoiced = $project->invoices()->sum('total_amount');
        $totalReceived = $project->invoices()->sum('paid_amount');
        $totalExpenses = $project->expenses()->sum('amount');
        $totalScheduled = $project->paymentSchedules()->where('status', 'pending')->sum('amount');
        
        $budgetRemaining = $totalBudget - $totalInvoiced;
        
        // Calculate outstanding receivables (kasbon yang belum lunas)
        $receivableOutstanding = $project->expenses()
            ->where('is_receivable', true)
            ->whereIn('receivable_status', ['pending', 'partial'])
            ->sum('amount');
        
        $profitMargin = $totalReceived - $totalExpenses;

        // Get monthly data for chart (last 6 months)
        $monthlyData = $this->getMonthlyFinancialData($project);
        
        // Debug: Log the monthly data
        \Log::info('Monthly chart data for project ' . $project->id, $monthlyData);

        return view('projects.partials.financial-tab', compact(
            'project',
            'totalBudget',
            'totalInvoiced',
            'totalReceived',
            'totalExpenses',
            'totalScheduled',
            'budgetRemaining',
            'receivableOutstanding',
            'profitMargin',
            'monthlyData'
        ));
    }

    /**
     * Store a new invoice.
     */
    public function storeInvoice(Request $request, Project $project)
    {
        // Validate that project has a client
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
            // Get client data from project relationship
            $client = $project->client;

            // Create invoice with auto-filled client data
            $invoice = $project->invoices()->create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'tax_rate' => $validated['tax_rate'],
                'notes' => $validated['notes'] ?? null,
                // Auto-fill client data from project->client
                'client_name' => $client->name,
                'client_address' => $client->address ?? $client->company ?? '',
                'client_tax_id' => $validated['client_tax_id'] ?? null,
                'status' => 'draft',
            ]);

            // Create invoice items
            foreach ($validated['items'] as $index => $item) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'order' => $index,
                ]);
            }

            // Calculate totals (auto-triggered by InvoiceItem observer)
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

    /**
     * Update invoice status.
     */
    public function updateInvoiceStatus(Request $request, Invoice $invoice)
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

    /**
     * Record a payment for invoice.
     */
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
            // Automatically select cash account based on payment method
            $cashAccount = null;
            $accountType = PaymentMethod::accountTypeFor($validated['payment_method']);
            $cashAccount = \App\Models\CashAccount::active()
                ->where('account_type', $accountType)
                ->orderBy('id', 'asc')
                ->first();

            if (!$cashAccount) {
                throw new \Exception('No active cash account found for payment method: ' . $validated['payment_method']);
            }

            // Record payment on invoice
            $invoice->recordPayment($validated['amount']);

            // Create payment schedule record
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

            // Update cash account balance (increase)
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

    /**
     * Store a payment schedule.
     */
    public function storePaymentSchedule(Request $request, Project $project)
    {
        $validated = $request->validate([
            'invoice_id' => 'nullable|exists:invoices,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $schedule = $project->paymentSchedules()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Payment schedule created',
            'schedule' => $schedule,
        ]);
    }

    /**
     * Mark payment schedule as paid.
     */
    public function markSchedulePaid(Request $request, PaymentSchedule $schedule)
    {
        $validated = $request->validate([
            'payment_method' => ['required', Rule::in(PaymentMethod::activeCodes())],
            'reference_number' => 'nullable|string|max:255',
        ]);

        $schedule->markAsPaid(
            $validated['payment_method'],
            $validated['reference_number'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Payment marked as paid',
            'schedule' => $schedule->fresh(),
        ]);
    }

    /**
     * Store a project expense.
     */
    public function storeExpense(Request $request, Project $project)
    {
        try {
            $validated = $request->validate([
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
            ]);

            // Semua pengeluaran adalah tanggung jawab perusahaan (NOT billable to client)
            $validated['is_billable'] = false;
            
            // Ensure boolean values are set correctly (checkbox tidak terkirim jika tidak dicentang)
            $validated['is_receivable'] = $request->has('is_receivable') ? (bool) $request->is_receivable : false;
            
            // Set default for receivable_paid_amount if not set (kolom NOT NULL di database)
            if (!isset($validated['receivable_paid_amount'])) {
                $validated['receivable_paid_amount'] = 0;
            }
            
            // Set default receivable status (kolom NOT NULL enum di database)
            // Jika is_receivable true, set status berdasarkan input atau default 'pending'
            // Jika is_receivable false, set status 'paid' (karena bukan piutang)
            if (!empty($validated['is_receivable'])) {
                if (empty($validated['receivable_status'])) {
                    $validated['receivable_status'] = 'pending';
                }
            } else {
                // Bukan piutang, set status 'paid' agar kolom NOT NULL terisi
                $validated['receivable_status'] = 'paid';
            }

            // Handle file upload
            if ($request->hasFile('receipt_file')) {
                $validated['receipt_file'] = $request->file('receipt_file')->store('receipts', 'public');
            }

            // Set created_by
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
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error storing expense: ' . $e->getMessage(), [
                'project_id' => $project->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get expense details for editing
     */
    public function getExpense(ProjectExpense $expense)
    {
        // Format data untuk form edit
        return response()->json([
            'id' => $expense->id,
            'expense_date' => $expense->expense_date->format('Y-m-d'), // Format untuk input[type="date"]
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
            // For now, assume billable expenses might be invoiced
            // TODO: Add actual invoice_items relationship check in future
            'invoiced' => false, // Can be enhanced with actual invoice check
        ]);
    }

    /**
     * Update expense
     */
    public function updateExpense(Request $request, ProjectExpense $expense)
    {
        try {
            $validated = $request->validate([
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
            ]);

            // Semua pengeluaran adalah tanggung jawab perusahaan (NOT billable to client)
            $validated['is_billable'] = false;
            
            // Ensure boolean values are set correctly
            $validated['is_receivable'] = $request->has('is_receivable') ? (bool) $request->is_receivable : false;
            
            // Set default for receivable_paid_amount if not set (kolom NOT NULL di database)
            if (!isset($validated['receivable_paid_amount'])) {
                $validated['receivable_paid_amount'] = $expense->receivable_paid_amount ?? 0;
            }
            
            // Set default receivable status (kolom NOT NULL enum di database)
            // Jika is_receivable true, set status berdasarkan input atau default 'pending'
            // Jika is_receivable false, set status 'paid' (karena bukan piutang)
            if (!empty($validated['is_receivable'])) {
                if (empty($validated['receivable_status'])) {
                    $validated['receivable_status'] = 'pending';
                }
            } else {
                // Bukan piutang, set status 'paid' agar kolom NOT NULL terisi
                $validated['receivable_status'] = 'paid';
            }

            // Handle file upload
            if ($request->hasFile('receipt_file')) {
                // Delete old file if exists
                if ($expense->receipt_file) {
                    \Storage::disk('public')->delete($expense->receipt_file);
                }
                $validated['receipt_file'] = $request->file('receipt_file')->store('receipts', 'public');
            }

            $expense->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Pengeluaran berhasil diupdate',
                'expense' => $expense
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating expense: ' . $e->getMessage(), [
                'expense_id' => $expense->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete receipt file from expense
     */
    public function deleteReceipt(ProjectExpense $expense)
    {
        try {
            if ($expense->receipt_file) {
                // Delete file from storage
                \Storage::disk('public')->delete($expense->receipt_file);
                
                // Update database
                $expense->receipt_file = null;
                $expense->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'File berhasil dihapus'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file untuk dihapus'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error deleting receipt file: ' . $e->getMessage(), [
                'expense_id' => $expense->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get monthly financial data for charts.
     */
    private function getMonthlyFinancialData(Project $project)
    {
        $months = [];
        $income = [];
        $expenses = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');

            // Calculate income from multiple sources:
            // 1. Invoice payments (from payment_schedules with paid status)
            $invoiceIncome = $project->paymentSchedules()
                ->where('status', 'paid')
                ->whereNotNull('paid_date')
                ->whereMonth('paid_date', $date->month)
                ->whereYear('paid_date', $date->year)
                ->sum('amount');
            
            // 2. Direct project payments (legacy system - if any)
            $directIncome = $project->payments()
                ->whereMonth('payment_date', $date->month)
                ->whereYear('payment_date', $date->year)
                ->sum('amount');
            
            $totalIncome = (float) ($invoiceIncome + $directIncome);
            $income[] = $totalIncome;
            
            // Debug logging
            \Log::info("Chart data for {$date->format('M Y')}: Income={$totalIncome}, InvoiceIncome={$invoiceIncome}, DirectIncome={$directIncome}");

            // Calculate expenses
            $monthExpense = $project->expenses()
                ->whereMonth('expense_date', $date->month)
                ->whereYear('expense_date', $date->year)
                ->sum('amount');
            $expenses[] = (float) $monthExpense;
        }
        
        // Final debug
        \Log::info('Final chart data', [
            'labels' => $months,
            'income' => $income,
            'expenses' => $expenses
        ]);

        return [
            'labels' => $months,
            'income' => $income,
            'expenses' => $expenses,
        ];
    }

    /**
     * Delete an invoice with validation.
     */
    public function destroyInvoice(Invoice $invoice)
    {
        try {
            // Validation 1: Check if invoice has payments
            $hasPayments = $invoice->payments()->exists();
            if ($hasPayments) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus invoice yang sudah memiliki pembayaran. Hapus pembayaran terlebih dahulu atau gunakan fitur "Batalkan Invoice".',
                ], 422);
            }
            
            // Validation 2: Check if invoice is not draft (only draft can be deleted)
            if ($invoice->status !== 'draft' && $invoice->status !== 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya invoice dengan status Draft atau Cancelled yang dapat dihapus. Invoice dengan status lain harus dibatalkan terlebih dahulu.',
                ], 422);
            }
            
            $projectId = $invoice->project_id;
            $invoiceNumber = $invoice->invoice_number;
            
            // Delete related data
            $invoice->items()->delete();
            $invoice->paymentSchedules()->delete();
            
            // Delete the invoice
            $invoice->delete();
            
            // Log activity
            \App\Models\ProjectLog::create([
                'project_id' => $projectId,
                'user_id' => auth()->id(),
                'description' => "Invoice {$invoiceNumber} dihapus",
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice berhasil dihapus',
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error deleting invoice: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus invoice',
            ], 500);
        }
    }

    /**
     * Delete a payment schedule.
     */
    public function destroySchedule(PaymentSchedule $schedule)
    {
        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment schedule deleted',
        ]);
    }

    /**
     * Delete an expense.
     */
    public function destroyExpense(ProjectExpense $expense)
    {
        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted',
        ]);
    }

    /**
     * Mark expense as invoiced
     */
    public function markExpenseInvoiced(Request $request, ProjectExpense $expense)
    {
        try {
            // Update expense with invoice note if provided
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

    /**
     * Record receivable payment
     */
    public function recordReceivablePayment(Request $request, ProjectExpense $expense)
    {
        try {
            $request->validate([
                'payment_amount' => 'required|numeric|min:0',
            ]);

            $paymentAmount = $request->payment_amount;
            $newPaidAmount = $expense->receivable_paid_amount + $paymentAmount;
            
            // Validate payment doesn't exceed total
            if ($newPaidAmount > $expense->amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount exceeds remaining balance',
                ], 400);
            }

            // Determine new status
            $status = 'pending';
            if ($newPaidAmount >= $expense->amount) {
                $status = 'paid';
            } elseif ($newPaidAmount > 0) {
                $status = 'partial';
            }

            // Update expense
            $notes = $expense->receivable_notes ?? '';
            $notes .= "\n" . now()->format('d M Y H:i') . " - Pembayaran: Rp " . number_format($paymentAmount, 0, ',', '.');
            if ($request->payment_notes) {
                $notes .= " (" . $request->payment_notes . ")";
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

    /**
     * Remove receivable flag (convert back to regular expense)
     */
    public function removeReceivable(ProjectExpense $expense)
    {
        try {
            // Reset all receivable-related fields
            $expense->update([
                'is_receivable' => false,
                'receivable_from' => null,
                'receivable_status' => 'pending',
                'receivable_paid_amount' => 0,
                'receivable_notes' => $expense->receivable_notes . "\n[" . now()->format('d M Y H:i') . "] Status kasbon dihapus, dikembalikan ke pengeluaran biasa",
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

    /**
     * Download invoice as PDF (Sprint 6 - Day 3)
     */
    public function downloadInvoicePDF(Invoice $invoice)
    {
        $invoice->load(['items', 'project', 'paymentSchedules' => function($query) {
            $query->where('status', 'paid')->orderBy('paid_date', 'desc');
        }]);
        
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        
        // Stream PDF (preview in browser) instead of direct download
        return $pdf->stream('Invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * View invoice details (Sprint 6 - Day 3)
     */
    public function showInvoice(Invoice $invoice)
    {
        $invoice->load(['items', 'project', 'paymentSchedules']);
        
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Export invoices to Excel (Sprint 7)
     */
    public function exportInvoices(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'project_id' => 'nullable|exists:projects,id'
        ]);

        $export = new \App\Exports\InvoicesExport(
            $request->start_date,
            $request->end_date,
            $request->project_id
        );

        $filename = 'invoices_' . now()->format('Y-m-d_His') . '.xlsx';

        return (new \Rap2hpoutre\FastExcel\FastExcel($export->generator()))
            ->download($filename);
    }

    /**
     * Export single invoice detail to Excel (Sprint 7)
     */
    public function exportInvoiceDetail(Invoice $invoice)
    {
        $export = new \App\Exports\InvoiceDetailExport($invoice);

        $filename = 'invoice_' . $invoice->invoice_number . '_' . now()->format('Y-m-d') . '.xlsx';

        return (new \Rap2hpoutre\FastExcel\FastExcel($export->generator()))
            ->download($filename);
    }

    /**
     * Export project expenses to Excel (Sprint 7)
     */
    public function exportExpenses(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'project_id' => 'nullable|exists:projects,id'
        ]);

        $export = new \App\Exports\ProjectExpensesExport(
            $request->start_date,
            $request->end_date,
            $request->project_id
        );

        $filename = 'expenses_' . now()->format('Y-m-d_His') . '.xlsx';

        return (new \Rap2hpoutre\FastExcel\FastExcel($export->generator()))
            ->download($filename);
    }

    /**
     * Export comprehensive financial report to Excel (Sprint 7)
     */
    public function exportFinancialReport(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        $export = new \App\Exports\FinancialReportExport(
            $request->start_date,
            $request->end_date
        );

        $filename = 'financial_report_' . now()->format('Y-m-d_His') . '.xlsx';

        // Multi-sheet export
        $sheets = new \Rap2hpoutre\FastExcel\SheetCollection([
            'Overview' => $export->overviewGenerator(),
            'Invoices' => $export->invoicesGenerator(),
            'Expenses' => $export->expensesGenerator(),
            'Payments' => $export->paymentsGenerator(),
        ]);

        return (new \Rap2hpoutre\FastExcel\FastExcel($sheets))
            ->download($filename);
    }

    /**
     * Store direct income (pemasukan tanpa invoice).
     */
    public function storeDirectIncome(Request $request, Project $project)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'cash_account_id' => 'nullable|exists:cash_accounts,id',
            'description' => 'required|string|max:1000',
            'reference' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Get payment method to check if it requires cash account
            $paymentMethod = PaymentMethod::findOrFail($validated['payment_method_id']);
            
            if ($paymentMethod->requires_cash_account && empty($validated['cash_account_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Metode pembayaran ini memerlukan rekening/kas tujuan'
                ], 422);
            }

            // Create a dummy invoice for direct income (invoice_id = null means direct income)
            // Or we can use ProjectPayment model directly if it exists
            // For now, let's create an invoice with special flag or use existing ProjectPayment model
            
            // Check if ProjectPayment model exists
            if (class_exists(\App\Models\ProjectPayment::class)) {
                $payment = new \App\Models\ProjectPayment();
                $payment->project_id = $project->id;
                $payment->invoice_id = null; // No invoice - direct income
                $payment->payment_date = $validated['payment_date'];
                $payment->amount = $validated['amount'];
                $payment->payment_method_id = $validated['payment_method_id'];
                $payment->cash_account_id = $validated['cash_account_id'] ?? null;
                $payment->description = $validated['description'];
                $payment->reference = $validated['reference'] ?? null;
                $payment->save();
            } else {
                // Fallback: Create invoice with special type "direct_income"
                $invoice = new Invoice();
                $invoice->project_id = $project->id;
                $invoice->invoice_number = 'DI-' . now()->format('Ymd') . '-' . strtoupper(substr(md5(time()), 0, 6));
                $invoice->invoice_date = $validated['payment_date'];
                $invoice->due_date = $validated['payment_date']; // Same date for direct income
                $invoice->subtotal = $validated['amount'];
                $invoice->tax_rate = 0;
                $invoice->tax_amount = 0;
                $invoice->total_amount = $validated['amount'];
                $invoice->paid_amount = $validated['amount']; // Fully paid immediately
                $invoice->remaining_amount = 0;
                $invoice->status = 'paid';
                $invoice->notes = 'PEMASUKAN LANGSUNG (Tanpa Invoice Formal): ' . $validated['description'];
                $invoice->payment_terms = $validated['reference'] ?? 'Direct Income';
                $invoice->save();

                // Create invoice item
                $item = new InvoiceItem();
                $item->invoice_id = $invoice->id;
                $item->description = $validated['description'];
                $item->quantity = 1;
                $item->unit_price = $validated['amount'];
                $item->total = $validated['amount'];
                $item->save();
            }

            // Update cash account balance if applicable
            if (!empty($validated['cash_account_id'])) {
                $cashAccount = \App\Models\CashAccount::findOrFail($validated['cash_account_id']);
                $cashAccount->balance += $validated['amount'];
                $cashAccount->save();

                // Record transaction
                \App\Models\CashAccountTransaction::create([
                    'cash_account_id' => $cashAccount->id,
                    'project_id' => $project->id,
                    'type' => 'income',
                    'amount' => $validated['amount'],
                    'description' => 'Pemasukan Langsung: ' . $validated['description'],
                    'reference' => $validated['reference'] ?? null,
                    'transaction_date' => $validated['payment_date'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pemasukan berhasil dicatat! Jumlah: Rp ' . number_format($validated['amount'], 0, ',', '.')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error storing direct income: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat pemasukan: ' . $e->getMessage()
            ], 500);
        }
    }
}
