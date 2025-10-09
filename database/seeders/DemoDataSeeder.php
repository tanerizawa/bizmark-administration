<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentSchedule;
use App\Models\ProjectExpense;
use App\Models\ProjectPayment;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating demo projects...');

        // Create 3 demo projects
        $projects = [];
        
        $projects[] = Project::create([
            'name' => 'Perizinan AMDAL PT. Maju Jaya',
            'client_name' => 'PT. Maju Jaya Indonesia',
            'client_contact' => 'Budi Santoso',
            'client_address' => 'Jl. Sudirman No. 123, Jakarta Selatan',
            'description' => 'Pengurusan izin AMDAL untuk pabrik tekstil',
            'status_id' => 2, // In Progress
            'institution_id' => 1,
            'start_date' => Carbon::now()->subMonths(3),
            'deadline' => Carbon::now()->addMonths(3),
            'contract_value' => 75000000,
        ]);

        $projects[] = Project::create([
            'name' => 'IMB Gedung Perkantoran PT. Sentosa',
            'client_name' => 'PT. Sentosa Property',
            'client_contact' => 'Dewi Lestari',
            'client_address' => 'Jl. HR Rasuna Said Kav. 5, Jakarta',
            'description' => 'Pengurusan IMB gedung perkantoran 15 lantai',
            'status_id' => 3, // Under Review
            'institution_id' => 2,
            'start_date' => Carbon::now()->subMonths(2),
            'deadline' => Carbon::now()->addMonths(4),
            'contract_value' => 120000000,
        ]);

        $projects[] = Project::create([
            'name' => 'SIUP & TDP CV. Berkah Sejahtera',
            'client_name' => 'CV. Berkah Sejahtera',
            'client_contact' => 'Ahmad Hidayat',
            'client_address' => 'Jl. Gatot Subroto No. 45, Bandung',
            'description' => 'Pengurusan SIUP dan TDP baru',
            'status_id' => 5, // Approved
            'institution_id' => 3,
            'start_date' => Carbon::now()->subMonths(1),
            'deadline' => Carbon::now()->addMonth(),
            'contract_value' => 25000000,
        ]);

        $this->command->info('Creating invoices with items...');

        // Create invoices for each project
        foreach ($projects as $index => $project) {
            // Invoice 1 (Paid)
            $invoice1 = Invoice::create([
                'project_id' => $project->id,
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'invoice_date' => Carbon::now()->subMonths(2),
                'due_date' => Carbon::now()->subMonth(),
                'subtotal' => 30000000,
                'tax_rate' => 11,
                'tax_amount' => 3300000,
                'total_amount' => 33300000,
                'paid_amount' => 33300000,
                'status' => 'paid',
                'notes' => 'Pembayaran DP 50%',
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice1->id,
                'description' => 'Down Payment - ' . $project->name,
                'quantity' => 1,
                'unit_price' => 30000000,
                'amount' => 30000000,
            ]);

            // Invoice 2 (Partial/Unpaid)
            $invoice2 = Invoice::create([
                'project_id' => $project->id,
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'invoice_date' => Carbon::now()->subWeeks(2),
                'due_date' => Carbon::now()->addWeek(),
                'subtotal' => $project->contract_value - 30000000,
                'tax_rate' => 11,
                'tax_amount' => ($project->contract_value - 30000000) * 0.11,
                'total_amount' => ($project->contract_value - 30000000) * 1.11,
                'paid_amount' => $index === 0 ? 20000000 : 0, // First project has partial payment
                'status' => $index === 0 ? 'partial' : 'sent',
                'notes' => 'Pelunasan - ' . $project->name,
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice2->id,
                'description' => 'Sisa Pembayaran',
                'quantity' => 1,
                'unit_price' => $project->contract_value - 30000000,
                'amount' => $project->contract_value - 30000000,
            ]);

            // Payment Schedule
            PaymentSchedule::create([
                'project_id' => $project->id,
                'invoice_id' => $invoice2->id,
                'description' => 'Termin 1',
                'due_date' => Carbon::now()->addWeek(),
                'amount' => $invoice2->total_amount / 2,
                'status' => 'pending',
                'notes' => 'Pembayaran termin pertama',
            ]);

            PaymentSchedule::create([
                'project_id' => $project->id,
                'invoice_id' => $invoice2->id,
                'description' => 'Termin 2',
                'due_date' => Carbon::now()->addWeeks(2),
                'amount' => $invoice2->total_amount / 2,
                'status' => 'pending',
                'notes' => 'Pembayaran termin kedua',
            ]);
        }

        $this->command->info('Creating project expenses...');

        // Create expenses
        foreach ($projects as $project) {
            ProjectExpense::create([
                'project_id' => $project->id,
                'expense_date' => Carbon::now()->subWeeks(3),
                'category' => 'operational',
                'description' => 'Biaya pengurusan dokumen administrasi',
                'amount' => 2500000,
                'payment_method' => 'bank_transfer',
            ]);

            ProjectExpense::create([
                'project_id' => $project->id,
                'expense_date' => Carbon::now()->subWeeks(2),
                'category' => 'vendor',
                'vendor_name' => 'Konsultan Perizinan ABC',
                'description' => 'Fee konsultan perizinan',
                'amount' => 5000000,
                'payment_method' => 'cash',
            ]);

            ProjectExpense::create([
                'project_id' => $project->id,
                'expense_date' => Carbon::now()->subWeek(),
                'category' => 'travel',
                'description' => 'Biaya perjalanan ke instansi terkait',
                'amount' => 750000,
                'payment_method' => 'bank_transfer',
            ]);
        }

        $this->command->info('Creating project payments...');

        // Create payments
        foreach ($projects as $project) {
            ProjectPayment::create([
                'project_id' => $project->id,
                'payment_date' => Carbon::now()->subMonths(2),
                'amount' => 33300000,
                'payment_type' => 'dp',
                'payment_method' => 'bank_transfer',
                'reference_number' => 'PAY-' . rand(10000, 99999),
                'description' => 'Pembayaran Down Payment invoice pertama',
            ]);

            if ($project->id === $projects[0]->id) {
                ProjectPayment::create([
                    'project_id' => $project->id,
                    'payment_date' => Carbon::now()->subWeek(),
                    'amount' => 20000000,
                    'payment_type' => 'progress',
                    'payment_method' => 'bank_transfer',
                    'reference_number' => 'PAY-' . rand(10000, 99999),
                    'description' => 'Pembayaran parsial invoice kedua',
                ]);
            }
        }

        $this->command->info('✅ Demo data seeded successfully!');
        $this->command->info('📊 Created:');
        $this->command->info('   - 3 Projects');
        $this->command->info('   - 6 Invoices with 6 Items');
        $this->command->info('   - 6 Payment Schedules');
        $this->command->info('   - 9 Project Expenses');
        $this->command->info('   - 4 Project Payments');
    }
}
