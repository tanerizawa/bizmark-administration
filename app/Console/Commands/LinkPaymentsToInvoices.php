<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\ProjectPayment;
use App\Models\Invoice;

class LinkPaymentsToInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:link-to-invoices 
                            {--project= : Specific project ID to process}
                            {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Link unlinked payments to their corresponding invoices';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $projectId = $this->option('project');
        
        $this->info('🔗 Linking Payments to Invoices...');
        $this->info('Mode: ' . ($isDryRun ? '🔍 DRY RUN (no changes)' : '💾 LIVE (will make changes)'));
        $this->newLine();
        
        // Get projects to process
        $projects = $projectId 
            ? Project::where('id', $projectId)->get() 
            : Project::has('payments')->get();
        
        if ($projects->isEmpty()) {
            $this->error('No projects found!');
            return 1;
        }
        
        $totalLinked = 0;
        $totalSkipped = 0;
        
        foreach ($projects as $project) {
            $this->info("📁 Project: {$project->name} (ID: {$project->id})");
            
            // Get unlinked payments
            $unlinkedPayments = $project->payments()
                ->whereNull('invoice_id')
                ->orderBy('payment_date')
                ->get();
            
            if ($unlinkedPayments->isEmpty()) {
                $this->line("   ✅ All payments already linked");
                $this->newLine();
                continue;
            }
            
            // Get unpaid/partial invoices
            $invoices = $project->invoices()
                ->whereIn('status', ['sent', 'partial', 'overdue'])
                ->orderBy('invoice_date')
                ->get();
            
            if ($invoices->isEmpty()) {
                $this->warn("   ⚠️  No unpaid invoices found. Skipping {$unlinkedPayments->count()} payments.");
                $totalSkipped += $unlinkedPayments->count();
                $this->newLine();
                continue;
            }
            
            $this->line("   📋 Found {$unlinkedPayments->count()} unlinked payments");
            $this->line("   🧾 Found {$invoices->count()} unpaid invoices");
            $this->newLine();
            
            // Strategy: Link each payment to oldest unpaid invoice
            foreach ($unlinkedPayments as $payment) {
                // Find invoice that still needs payment
                $invoice = $invoices->first(function($inv) {
                    return $inv->remaining_amount > 0;
                });
                
                if (!$invoice) {
                    $this->warn("   ⚠️  No more invoices need payment. Skipping payment #{$payment->id}");
                    $totalSkipped++;
                    continue;
                }
                
                $this->line("   💰 Payment #{$payment->id} (Rp " . number_format($payment->amount, 0, ',', '.') . ")");
                $this->line("      → Invoice: {$invoice->invoice_number}");
                $this->line("      → Remaining: Rp " . number_format($invoice->remaining_amount, 0, ',', '.'));
                
                if (!$isDryRun) {
                    // Link payment to invoice
                    $payment->invoice_id = $invoice->id;
                    $payment->save(); // This triggers auto-update via model event
                    
                    // Refresh invoice to show updated values
                    $invoice->refresh();
                    
                    $this->info("      ✅ Linked! New status: {$invoice->status} (Paid: Rp " . number_format($invoice->paid_amount, 0, ',', '.') . ")");
                } else {
                    $this->comment("      🔍 [DRY RUN] Would link payment to invoice");
                }
                
                $totalLinked++;
            }
            
            $this->newLine();
        }
        
        // Summary
        $this->info('=================================');
        $this->info('📊 SUMMARY');
        $this->info('=================================');
        $this->line("Payments linked: {$totalLinked}");
        $this->line("Payments skipped: {$totalSkipped}");
        
        if ($isDryRun) {
            $this->newLine();
            $this->comment('🔍 This was a DRY RUN - no changes were made');
            $this->comment('Run without --dry-run to apply changes');
        } else {
            $this->newLine();
            $this->info('✅ Process completed!');
        }
        
        return 0;
    }
}
