<?php

namespace App\Exports;

use App\Models\Invoice;
use Generator;

class InvoiceDetailExport
{
    protected $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice->load(['project', 'items', 'paymentSchedules']);
    }

    public function generator(): Generator
    {
        // Company Header
        yield ['BIZMARK PERIZINAN'];
        yield ['Jl. Contoh No. 123, Jakarta'];
        yield ['Telp: (021) 1234567 | Email: cs@bizmark.id'];
        yield [];

        // Invoice Header
        yield ['INVOICE'];
        yield [];
        yield ['No. Invoice:', $this->invoice->invoice_number];
        yield ['Tanggal:', $this->invoice->invoice_date->format('d/m/Y')];
        yield ['Jatuh Tempo:', $this->invoice->due_date ? $this->invoice->due_date->format('d/m/Y') : '-'];
        yield [];

        // Client Info
        yield ['KLIEN:'];
        yield ['Nama:', $this->invoice->project->client_name ?? '-'];
        yield ['Proyek:', $this->invoice->project->name ?? '-'];
        yield [];

        // Items Header
        yield ['DETAIL ITEM'];
        yield ['No', 'Deskripsi', 'Qty', 'Harga Satuan', 'Total'];

        // Items
        $no = 1;
        foreach ($this->invoice->items as $item) {
            yield [
                $no++,
                $item->description,
                $item->quantity,
                'Rp ' . number_format($item->unit_price, 0, ',', '.'),
                'Rp ' . number_format($item->amount, 0, ',', '.')
            ];
        }

        yield [];

        // Totals
        yield ['', '', '', 'Subtotal:', 'Rp ' . number_format($this->invoice->subtotal, 0, ',', '.')];
        yield ['', '', '', 'PPN (' . $this->invoice->tax_rate . '%):', 'Rp ' . number_format($this->invoice->tax_amount, 0, ',', '.')];
        yield ['', '', '', 'TOTAL:', 'Rp ' . number_format($this->invoice->total_amount, 0, ',', '.')];
        yield [];

        // Payment Status
        yield ['STATUS PEMBAYARAN'];
        yield ['Total Invoice:', 'Rp ' . number_format($this->invoice->total_amount, 0, ',', '.')];
        yield ['Terbayar:', 'Rp ' . number_format($this->invoice->paid_amount, 0, ',', '.')];
        yield ['Sisa:', 'Rp ' . number_format($this->invoice->total_amount - $this->invoice->paid_amount, 0, ',', '.')];
        yield ['Status:', $this->invoice->status];
        yield [];

        // Payment Schedule if exists
        if ($this->invoice->paymentSchedules->count() > 0) {
            yield ['JADWAL PEMBAYARAN'];
            yield ['No', 'Tanggal', 'Jumlah', 'Status'];
            
            $scheduleNo = 1;
            foreach ($this->invoice->paymentSchedules as $schedule) {
                yield [
                    $scheduleNo++,
                    $schedule->due_date->format('d/m/Y'),
                    'Rp ' . number_format($schedule->amount, 0, ',', '.'),
                    $schedule->status
                ];
            }
            yield [];
        }

        // Notes
        if ($this->invoice->notes) {
            yield ['CATATAN'];
            yield [$this->invoice->notes];
            yield [];
        }

        // Footer
        yield [];
        yield ['Diekspor pada: ' . now()->format('d/m/Y H:i:s')];
    }
}
