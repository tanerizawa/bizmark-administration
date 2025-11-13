<?php

namespace App\Exports;

use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Generator;

class FinancialReportExport
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Generate Overview Sheet
     */
    public function overviewGenerator(): Generator
    {
        yield ['LAPORAN KEUANGAN - OVERVIEW'];
        yield ['Periode: ' . ($this->startDate ? date('d/m/Y', strtotime($this->startDate)) : 'Semua') . ' - ' . ($this->endDate ? date('d/m/Y', strtotime($this->endDate)) : 'Semua')];
        yield [];

        // Build queries with date filters
        $invoiceQuery = Invoice::query();
        $expenseQuery = DB::table('project_expenses');
        $paymentQuery = DB::table('project_payments');

        if ($this->startDate) {
            $invoiceQuery->whereDate('invoice_date', '>=', $this->startDate);
            $expenseQuery->whereDate('expense_date', '>=', $this->startDate);
            $paymentQuery->whereDate('payment_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $invoiceQuery->whereDate('invoice_date', '<=', $this->endDate);
            $expenseQuery->whereDate('expense_date', '<=', $this->endDate);
            $paymentQuery->whereDate('payment_date', '<=', $this->endDate);
        }

        // Calculate metrics
        $totalRevenue = $invoiceQuery->sum('total_amount');
        $totalPaid = (clone $invoiceQuery)->sum('paid_amount');
        $totalUnpaid = $totalRevenue - $totalPaid;
        $totalExpenses = $expenseQuery->sum('amount');
        $totalProfit = $totalRevenue - $totalExpenses;
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        // Invoice Status Breakdown
        $paidInvoices = (clone $invoiceQuery)->where('status', 'paid')->count();
        $partialInvoices = (clone $invoiceQuery)->where('status', 'partial')->count();
        $unpaidInvoices = (clone $invoiceQuery)->where('status', 'unpaid')->count();
        $overdueInvoices = (clone $invoiceQuery)->where('status', 'overdue')->count();

        // Key Metrics
        yield ['METRIK UTAMA'];
        yield ['Total Revenue', 'Rp ' . number_format($totalRevenue, 0, ',', '.')];
        yield ['Total Terbayar', 'Rp ' . number_format($totalPaid, 0, ',', '.')];
        yield ['Total Piutang', 'Rp ' . number_format($totalUnpaid, 0, ',', '.')];
        yield ['Total Pengeluaran', 'Rp ' . number_format($totalExpenses, 0, ',', '.')];
        yield ['Profit Bersih', 'Rp ' . number_format($totalProfit, 0, ',', '.')];
        yield ['Margin Profit', number_format($profitMargin, 2) . '%'];
        yield [];

        // Invoice Status
        yield ['STATUS INVOICE'];
        yield ['Lunas (Paid)', $paidInvoices . ' invoice'];
        yield ['Sebagian (Partial)', $partialInvoices . ' invoice'];
        yield ['Belum Bayar (Unpaid)', $unpaidInvoices . ' invoice'];
        yield ['Terlambat (Overdue)', $overdueInvoices . ' invoice'];
        yield [];

        // Top Projects by Revenue
        yield ['TOP 5 PROYEK - REVENUE TERTINGGI'];
        yield ['Proyek', 'Total Revenue'];

        $topProjects = DB::table('invoices')
            ->join('projects', 'invoices.project_id', '=', 'projects.id')
            ->select('projects.name', DB::raw('SUM(invoices.total_amount) as total'))
            ->when($this->startDate, function ($query) {
                $query->whereDate('invoices.invoice_date', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->whereDate('invoices.invoice_date', '<=', $this->endDate);
            })
            ->groupBy('projects.id', 'projects.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        foreach ($topProjects as $project) {
            yield [$project->name, 'Rp ' . number_format($project->total, 0, ',', '.')];
        }

        yield [];
        yield ['Diekspor pada: ' . now()->format('d/m/Y H:i:s')];
    }

    /**
     * Generate Invoices Sheet
     */
    public function invoicesGenerator(): Generator
    {
        yield ['DAFTAR INVOICE'];
        yield [];
        yield ['No Invoice', 'Tanggal', 'Proyek', 'Klien', 'Total', 'Terbayar', 'Status'];

        $query = Invoice::with('project');

        if ($this->startDate) {
            $query->whereDate('invoice_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('invoice_date', '<=', $this->endDate);
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->get();

        foreach ($invoices as $invoice) {
            yield [
                $invoice->invoice_number,
                $invoice->invoice_date->format('d/m/Y'),
                $invoice->project->name ?? '-',
                $invoice->project->client_name ?? '-',
                'Rp ' . number_format($invoice->total_amount, 0, ',', '.'),
                'Rp ' . number_format($invoice->paid_amount, 0, ',', '.'),
                $invoice->status
            ];
        }
    }

    /**
     * Generate Expenses Sheet
     */
    public function expensesGenerator(): Generator
    {
        yield ['DAFTAR PENGELUARAN'];
        yield [];
        yield ['Tanggal', 'Proyek', 'Kategori', 'Deskripsi', 'Jumlah'];

        $query = DB::table('project_expenses')
            ->join('projects', 'project_expenses.project_id', '=', 'projects.id')
            ->select(
                'project_expenses.expense_date',
                'projects.name as project_name',
                'project_expenses.category',
                'project_expenses.description',
                'project_expenses.amount'
            );

        if ($this->startDate) {
            $query->whereDate('project_expenses.expense_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('project_expenses.expense_date', '<=', $this->endDate);
        }

        $expenses = $query->orderBy('project_expenses.expense_date', 'desc')->get();

        foreach ($expenses as $expense) {
            yield [
                date('d/m/Y', strtotime($expense->expense_date)),
                $expense->project_name,
                $expense->category,
                $expense->description,
                'Rp ' . number_format($expense->amount, 0, ',', '.')
            ];
        }
    }

    /**
     * Generate Payments Sheet
     */
    public function paymentsGenerator(): Generator
    {
        yield ['DAFTAR PEMBAYARAN'];
        yield [];
        yield ['Tanggal', 'Proyek', 'Jumlah', 'Tipe', 'Metode', 'Referensi', 'Deskripsi'];

        $query = DB::table('project_payments')
            ->join('projects', 'project_payments.project_id', '=', 'projects.id')
            ->select(
                'project_payments.payment_date',
                'projects.name as project_name',
                'project_payments.amount',
                'project_payments.payment_type',
                'project_payments.payment_method',
                'project_payments.reference_number',
                'project_payments.description'
            );

        if ($this->startDate) {
            $query->whereDate('project_payments.payment_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('project_payments.payment_date', '<=', $this->endDate);
        }

        $payments = $query->orderBy('project_payments.payment_date', 'desc')->get();

        foreach ($payments as $payment) {
            yield [
                date('d/m/Y', strtotime($payment->payment_date)),
                $payment->project_name,
                'Rp ' . number_format($payment->amount, 0, ',', '.'),
                $payment->payment_type ?? '-',
                $payment->payment_method ?? '-',
                $payment->reference_number ?? '-',
                $payment->description ?? '-'
            ];
        }
    }
}
