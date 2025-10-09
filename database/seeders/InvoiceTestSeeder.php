<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentSchedule;
use Carbon\Carbon;

class InvoiceTestSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=== CREATING INVOICE & PAYMENT FOR TESTING ===');
        
        // Get project UKL-UPL Pabrik Industri - PT Asiacon (latest)
        $project = Project::where('name', 'LIKE', '%Asiacon%')->orderBy('id', 'desc')->first();
        
        if (!$project) {
            $this->command->error('❌ Project not found');
            return;
        }
        
        $this->command->info("📁 Project: {$project->name}");
        $this->command->info("   Client: {$project->client_name}");
        $this->command->info("   Budget: Rp " . number_format($project->contract_value, 0, ',', '.'));
        
        // Generate unique invoice number
        $invoiceCount = Invoice::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count() + 1;
        $invoiceNumber = 'INV-' . date('Ym') . '-' . str_pad($invoiceCount, 4, '0', STR_PAD_LEFT);
        
        // Create Invoice
        $invoice = Invoice::create([
            'project_id' => $project->id,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => now()->subDays(15),
            'due_date' => now()->addDays(15),
            'subtotal' => 180000000,
            'tax_rate' => 11,
            'tax_amount' => 19800000,
            'total_amount' => 199800000,
            'paid_amount' => 100000000,
            'remaining_amount' => 99800000,
            'status' => 'partial',
            'client_name' => $project->client_name,
            'client_address' => $project->client_address,
            'notes' => 'Invoice untuk UKL-UPL Pabrik Industri - Termin 1'
        ]);
        
        $this->command->info("✅ Invoice created: {$invoice->invoice_number}");
        $this->command->info("   Total: Rp " . number_format($invoice->total_amount, 0, ',', '.'));
        $this->command->info("   Paid: Rp " . number_format($invoice->paid_amount, 0, ',', '.'));
        
        // Create invoice items
        $items = [
            ['description' => 'Penyusunan Dokumen UKL-UPL', 'quantity' => 1, 'unit_price' => 80000000],
            ['description' => 'Survey Lapangan & Pengambilan Sample', 'quantity' => 1, 'unit_price' => 40000000],
            ['description' => 'Konsultasi & Koordinasi dengan Instansi', 'quantity' => 1, 'unit_price' => 30000000],
            ['description' => 'Pengurusan Administrasi & Legalisasi', 'quantity' => 1, 'unit_price' => 30000000],
        ];
        
        foreach ($items as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'amount' => $item['quantity'] * $item['unit_price']
            ]);
        }
        
        $this->command->info('✅ Created ' . count($items) . ' invoice items');
        
        // Create payment schedule (termin 1 - sudah dibayar)
        $payment1 = PaymentSchedule::create([
            'project_id' => $project->id,
            'invoice_id' => $invoice->id,
            'description' => 'Termin 1 - Down Payment (50%)',
            'amount' => 100000000,
            'due_date' => now()->subDays(15),
            'paid_date' => now()->subDays(10),
            'status' => 'paid',
            'payment_method' => 'bank_transfer',
            'reference_number' => 'TRF-' . date('Ymd') . '-001',
            'notes' => 'Pembayaran termin 1 via BCA'
        ]);
        
        $this->command->info('✅ Payment Schedule created (PAID): Rp ' . number_format($payment1->amount, 0, ',', '.'));
        $this->command->info('   Paid Date: ' . $payment1->paid_date->format('d M Y'));
        
        // Create payment schedule (termin 2 - belum dibayar)
        $payment2 = PaymentSchedule::create([
            'project_id' => $project->id,
            'invoice_id' => $invoice->id,
            'description' => 'Termin 2 - Final Payment (50%)',
            'amount' => 99800000,
            'due_date' => now()->addDays(15),
            'status' => 'pending',
            'notes' => 'Pembayaran setelah dokumen selesai'
        ]);
        
        $this->command->info('✅ Payment Schedule created (PENDING): Rp ' . number_format($payment2->amount, 0, ',', '.'));
        $this->command->info('   Due Date: ' . $payment2->due_date->format('d M Y'));
        
        $this->command->info("\n🎉 Invoice & Payment data created successfully!");
    }
}
