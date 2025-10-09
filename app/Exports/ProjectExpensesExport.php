<?php

namespace App\Exports;

use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Generator;

class ProjectExpensesExport
{
    protected $startDate;
    protected $endDate;
    protected $projectId;

    public function __construct($startDate = null, $endDate = null, $projectId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->projectId = $projectId;
    }

    public function generator(): Generator
    {
        // Header
        yield ['LAPORAN PENGELUARAN PROYEK'];
        yield ['Periode: ' . ($this->startDate ? date('d/m/Y', strtotime($this->startDate)) : 'Semua') . ' - ' . ($this->endDate ? date('d/m/Y', strtotime($this->endDate)) : 'Semua')];
        yield [];

        // Column Headers
        yield [
            'Tanggal',
            'Proyek',
            'Kategori',
            'Deskripsi',
            'Jumlah',
            'Metode Pembayaran',
            'Referensi'
        ];

        // Build Query
        $query = DB::table('project_expenses')
            ->join('projects', 'project_expenses.project_id', '=', 'projects.id')
            ->select(
                'project_expenses.expense_date',
                'projects.name as project_name',
                'project_expenses.category',
                'project_expenses.description',
                'project_expenses.amount',
                'project_expenses.payment_method',
                'project_expenses.reference_number'
            );

        if ($this->startDate) {
            $query->whereDate('project_expenses.expense_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('project_expenses.expense_date', '<=', $this->endDate);
        }

        if ($this->projectId) {
            $query->where('project_expenses.project_id', $this->projectId);
        }

        $expenses = $query->orderBy('project_expenses.expense_date', 'desc')->get();

        $totalExpenses = 0;
        $expensesByCategory = [];
        $expensesByProject = [];

        // Data Rows
        foreach ($expenses as $expense) {
            $totalExpenses += $expense->amount;
            
            // Group by category
            if (!isset($expensesByCategory[$expense->category])) {
                $expensesByCategory[$expense->category] = 0;
            }
            $expensesByCategory[$expense->category] += $expense->amount;

            // Group by project
            if (!isset($expensesByProject[$expense->project_name])) {
                $expensesByProject[$expense->project_name] = 0;
            }
            $expensesByProject[$expense->project_name] += $expense->amount;

            yield [
                date('d/m/Y', strtotime($expense->expense_date)),
                $expense->project_name,
                $expense->category,
                $expense->description,
                'Rp ' . number_format($expense->amount, 0, ',', '.'),
                $expense->payment_method ?? '-',
                $expense->reference_number ?? '-'
            ];
        }

        // Summary Section
        yield [];
        yield [];
        yield ['RINGKASAN PER KATEGORI'];
        yield ['Kategori', 'Total'];
        
        foreach ($expensesByCategory as $category => $amount) {
            yield [$category, 'Rp ' . number_format($amount, 0, ',', '.')];
        }

        yield [];
        yield [];
        yield ['RINGKASAN PER PROYEK'];
        yield ['Proyek', 'Total'];
        
        foreach ($expensesByProject as $project => $amount) {
            yield [$project, 'Rp ' . number_format($amount, 0, ',', '.')];
        }

        // Grand Total
        yield [];
        yield [];
        yield ['TOTAL KESELURUHAN PENGELUARAN', 'Rp ' . number_format($totalExpenses, 0, ',', '.')];
        
        yield [];
        yield ['Diekspor pada: ' . now()->format('d/m/Y H:i:s')];
    }
}
