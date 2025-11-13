<?php

// Script untuk link payment ke invoice
// Usage: docker exec bizmark_app php link-payment.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Project;
use App\Models\ProjectPayment;
use App\Models\Invoice;

// =====================================
// STEP 1: Cek Data Saat Ini
// =====================================

echo "\n";
echo "===========================================\n";
echo "  🔍 ANALISA DATA PROJECT #42\n";
echo "===========================================\n\n";

$project = Project::find(42);

if (!$project) {
    echo "❌ Project 42 tidak ditemukan!\n";
    exit(1);
}

echo "📁 Project: {$project->name}\n";
echo "   ID: {$project->id}\n\n";

// Cek Invoice
echo "===========================================\n";
echo "📋 INVOICE\n";
echo "===========================================\n\n";

$invoices = $project->invoices;

if ($invoices->isEmpty()) {
    echo "❌ Tidak ada invoice!\n\n";
} else {
    foreach ($invoices as $inv) {
        echo "Invoice #{$inv->id}\n";
        echo "  Nomor       : {$inv->invoice_number}\n";
        echo "  Status      : {$inv->status}\n";
        echo "  Total       : Rp " . number_format($inv->total_amount, 0, ',', '.') . "\n";
        echo "  Terbayar    : Rp " . number_format($inv->paid_amount, 0, ',', '.') . "\n";
        echo "  Sisa        : Rp " . number_format($inv->remaining_amount, 0, ',', '.') . "\n";
        echo "\n";
    }
}

// Cek Pembayaran
echo "===========================================\n";
echo "💰 PEMBAYARAN\n";
echo "===========================================\n\n";

$payments = $project->payments;

if ($payments->isEmpty()) {
    echo "❌ Tidak ada pembayaran!\n\n";
} else {
    foreach ($payments as $pay) {
        echo "Payment #{$pay->id}\n";
        echo "  Tanggal     : {$pay->payment_date->format('d M Y')}\n";
        echo "  Jumlah      : Rp " . number_format($pay->amount, 0, ',', '.') . "\n";
        echo "  Tipe        : {$pay->payment_type}\n";
        echo "  Invoice     : " . ($pay->invoice_id ? "Linked ke Invoice #{$pay->invoice_id}" : "❌ BELUM LINK") . "\n";
        echo "  Deskripsi   : " . ($pay->description ?? '-') . "\n";
        echo "\n";
    }
}

// =====================================
// STEP 2: Identifikasi yang Perlu Link
// =====================================

echo "===========================================\n";
echo "🔗 ANALISA LINKING\n";
echo "===========================================\n\n";

$unlinkedPayments = $project->payments()->whereNull('invoice_id')->get();
$unpaidInvoices = $project->invoices()
    ->whereIn('status', ['sent', 'partial', 'overdue', 'draft'])
    ->where('remaining_amount', '>', 0)
    ->get();

echo "Pembayaran belum ter-link: {$unlinkedPayments->count()}\n";
echo "Invoice belum lunas      : {$unpaidInvoices->count()}\n\n";

if ($unlinkedPayments->isEmpty()) {
    echo "✅ Semua pembayaran sudah ter-link!\n\n";
    exit(0);
}

if ($unpaidInvoices->isEmpty()) {
    echo "⚠️  Tidak ada invoice yang perlu dibayar.\n";
    echo "   Pembayaran mungkin advance payment atau sudah semua lunas.\n\n";
    exit(0);
}

// =====================================
// STEP 3: Saran Linking
// =====================================

echo "===========================================\n";
echo "💡 SARAN LINKING\n";
echo "===========================================\n\n";

echo "Saya sarankan linking berikut:\n\n";

$suggestions = [];
foreach ($unlinkedPayments as $payment) {
    // Cari invoice yang cocok (amount matching atau oldest unpaid)
    $matchedInvoice = null;
    
    // Strategy 1: Exact amount match
    foreach ($unpaidInvoices as $inv) {
        if (abs($inv->remaining_amount - $payment->amount) < 1) {
            $matchedInvoice = $inv;
            break;
        }
    }
    
    // Strategy 2: Partial payment to oldest invoice
    if (!$matchedInvoice) {
        $matchedInvoice = $unpaidInvoices->sortBy('invoice_date')->first();
    }
    
    if ($matchedInvoice) {
        $suggestions[] = [
            'payment' => $payment,
            'invoice' => $matchedInvoice,
        ];
        
        echo "💰 Payment #{$payment->id} (Rp " . number_format($payment->amount, 0, ',', '.') . ")\n";
        echo "   → Link ke Invoice #{$matchedInvoice->id} ({$matchedInvoice->invoice_number})\n";
        echo "      Sisa invoice: Rp " . number_format($matchedInvoice->remaining_amount, 0, ',', '.') . "\n";
        
        if (abs($matchedInvoice->remaining_amount - $payment->amount) < 1) {
            echo "      ✅ EXACT MATCH - Invoice akan lunas!\n";
        } else if ($payment->amount < $matchedInvoice->remaining_amount) {
            echo "      ⚠️  PARTIAL - Invoice akan menjadi status 'partial'\n";
        } else {
            echo "      ⚠️  OVERPAYMENT - Payment lebih besar dari sisa invoice!\n";
        }
        echo "\n";
    }
}

if (empty($suggestions)) {
    echo "❌ Tidak ada saran linking yang cocok.\n\n";
    exit(0);
}

// =====================================
// STEP 4: Konfirmasi dan Eksekusi
// =====================================

echo "===========================================\n";
echo "⚙️  EKSEKUSI LINKING\n";
echo "===========================================\n\n";

echo "Apakah Anda ingin melanjutkan linking di atas?\n";
echo "Ketik 'yes' untuk melanjutkan, atau 'no' untuk membatalkan: ";

$handle = fopen("php://stdin", "r");
$confirmation = trim(fgets($handle));
fclose($handle);

if (strtolower($confirmation) !== 'yes') {
    echo "\n❌ Linking dibatalkan.\n\n";
    exit(0);
}

echo "\n🔄 Memproses linking...\n\n";

$successCount = 0;
$errorCount = 0;

foreach ($suggestions as $suggestion) {
    $payment = $suggestion['payment'];
    $invoice = $suggestion['invoice'];
    
    try {
        echo "Processing Payment #{$payment->id} → Invoice #{$invoice->id}... ";
        
        // Link payment to invoice
        $payment->invoice_id = $invoice->id;
        $payment->save(); // This triggers model event to auto-update invoice
        
        // Refresh invoice to get updated values
        $invoice->refresh();
        
        echo "✅ BERHASIL!\n";
        echo "   New status: {$invoice->status}\n";
        echo "   Terbayar: Rp " . number_format($invoice->paid_amount, 0, ',', '.') . "\n";
        echo "   Sisa: Rp " . number_format($invoice->remaining_amount, 0, ',', '.') . "\n\n";
        
        $successCount++;
        
    } catch (\Exception $e) {
        echo "❌ GAGAL!\n";
        echo "   Error: {$e->getMessage()}\n\n";
        $errorCount++;
    }
}

// =====================================
// STEP 5: Summary
// =====================================

echo "===========================================\n";
echo "📊 SUMMARY\n";
echo "===========================================\n\n";

echo "Berhasil di-link: {$successCount}\n";
echo "Gagal: {$errorCount}\n\n";

if ($successCount > 0) {
    echo "✅ Linking selesai! Silakan cek di browser untuk melihat hasilnya.\n";
    echo "   URL: https://bizmark.id/projects/42#financial\n\n";
}

exit(0);
