<?php

namespace App\Exports;

use App\Models\Invoice;
use Generator;

class InvoicesExport
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
        // Header Row
        yield [
            'No Invoice',
            'Tanggal',
            'Proyek',
            'Klien',
            'Jatuh Tempo',
            'Subtotal',
            'PPN',
            'Total',
            'Status',
            'Terbayar',
            'Sisa'
        ];

        // Build Query
        $query = Invoice::with(['project', 'items']);

        if ($this->startDate) {
            $query->whereDate('invoice_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('invoice_date', '<=', $this->endDate);
        }

        if ($this->projectId) {
            $query->where('project_id', $this->projectId);
        }

        $invoices = $query->orderBy('issue_date', 'desc')->get();

        $total = 0;
        $totalPaid = 0;

        // Data Rows
        foreach ($invoices as $invoice) {
            $total += $invoice->total_amount;
            $totalPaid += $invoice->paid_amount;

            yield [
                $invoice->invoice_number,
                $invoice->invoice_date->format('d/m/Y'),
                $invoice->project->name ?? '-',
                $invoice->project->client_name ?? '-',
                $invoice->due_date ? $invoice->due_date->format('d/m/Y') : '-',
                number_format($invoice->subtotal, 0, ',', '.'),
                number_format($invoice->tax_amount, 0, ',', '.'),
                number_format($invoice->total_amount, 0, ',', '.'),
                $invoice->status,
                number_format($invoice->paid_amount, 0, ',', '.'),
                number_format($invoice->total_amount - $invoice->paid_amount, 0, ',', '.')
            ];
        }

        // Summary Row
        yield [];
        yield [
            'TOTAL',
            '',
            '',
            '',
            '',
            '',
            '',
            number_format($total, 0, ',', '.'),
            '',
            number_format($totalPaid, 0, ',', '.'),
            number_format($total - $totalPaid, 0, ',', '.')
        ];

        // Info Row
        yield [];
        yield [
            'Diekspor pada: ' . now()->format('d/m/Y H:i:s'),
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ];
    }
}
