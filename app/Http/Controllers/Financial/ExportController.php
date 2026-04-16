<?php

namespace App\Http\Controllers\Financial;

use App\Exports\FinancialReportExport;
use App\Exports\InvoiceDetailExport;
use App\Exports\InvoicesExport;
use App\Exports\ProjectExpensesExport;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use Rap2hpoutre\FastExcel\SheetCollection;

class ExportController extends Controller
{
    public function invoices(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $export = new InvoicesExport(
            $request->start_date,
            $request->end_date,
            $request->project_id
        );

        $filename = 'invoices_' . now()->format('Y-m-d_His') . '.xlsx';

        return (new FastExcel($export->generator()))->download($filename);
    }

    public function invoiceDetail(Invoice $invoice)
    {
        $export = new InvoiceDetailExport($invoice);

        $filename = 'invoice_' . $invoice->invoice_number . '_' . now()->format('Y-m-d') . '.xlsx';

        return (new FastExcel($export->generator()))->download($filename);
    }

    public function expenses(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $export = new ProjectExpensesExport(
            $request->start_date,
            $request->end_date,
            $request->project_id
        );

        $filename = 'expenses_' . now()->format('Y-m-d_His') . '.xlsx';

        return (new FastExcel($export->generator()))->download($filename);
    }

    public function financialReport(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $export = new FinancialReportExport(
            $request->start_date,
            $request->end_date
        );

        $filename = 'financial_report_' . now()->format('Y-m-d_His') . '.xlsx';

        $sheets = new SheetCollection([
            'Overview' => $export->overviewGenerator(),
            'Invoices' => $export->invoicesGenerator(),
            'Expenses' => $export->expensesGenerator(),
            'Payments' => $export->paymentsGenerator(),
        ]);

        return (new FastExcel($sheets))->download($filename);
    }
}
