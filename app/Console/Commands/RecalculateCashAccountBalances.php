<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CashAccount;

class RecalculateCashAccountBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cash:recalculate-balances 
                            {--account= : Specific account ID to recalculate}
                            {--force : Force recalculation even if balance seems correct}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate all cash account balances from transactions (fix balance discrepancies)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Starting Cash Account Balance Recalculation...');
        $this->newLine();

        // Get accounts to process
        if ($accountId = $this->option('account')) {
            $accounts = CashAccount::where('id', $accountId)->get();
            if ($accounts->isEmpty()) {
                $this->error("❌ Account ID {$accountId} not found!");
                return 1;
            }
        } else {
            $accounts = CashAccount::all();
        }

        if ($accounts->isEmpty()) {
            $this->warn('⚠️  No cash accounts found in database.');
            return 0;
        }

        $this->info("Processing {$accounts->count()} account(s)...");
        $this->newLine();

        $fixed = 0;
        $unchanged = 0;
        $errors = 0;

        foreach ($accounts as $account) {
            try {
                $oldBalance = $account->current_balance;
                
                // Calculate expected balance
                $totalIncome = $account->payments()->sum('amount');
                $totalExpense = $account->expenses()->sum('amount');
                $expectedBalance = $account->initial_balance + $totalIncome - $totalExpense;
                
                $difference = $expectedBalance - $oldBalance;
                
                if ($difference == 0 && !$this->option('force')) {
                    $this->line("✅ [{$account->id}] {$account->account_name}: Balance OK (Rp " . number_format($oldBalance, 0) . ")");
                    $unchanged++;
                    continue;
                }
                
                // Recalculate
                $account->recalculateBalance(
                    changeType: 'recalculation',
                    description: "Manual recalculation via artisan command"
                );
                
                $newBalance = $account->fresh()->current_balance;
                
                if ($difference != 0) {
                    $this->warn("🔧 [{$account->id}] {$account->account_name}:");
                    $this->line("   Old Balance: Rp " . number_format($oldBalance, 0));
                    $this->line("   New Balance: Rp " . number_format($newBalance, 0));
                    $this->line("   Difference:  Rp " . number_format($difference, 0) . " (" . ($difference > 0 ? '+' : '') . number_format($difference, 0) . ")");
                    $this->line("   Income:      Rp " . number_format($totalIncome, 0));
                    $this->line("   Expense:     Rp " . number_format($totalExpense, 0));
                    $fixed++;
                } else {
                    $this->info("✓ [{$account->id}] {$account->account_name}: Recalculated (Rp " . number_format($newBalance, 0) . ")");
                    $unchanged++;
                }
                
            } catch (\Exception $e) {
                $this->error("❌ [{$account->id}] {$account->account_name}: Error - {$e->getMessage()}");
                $errors++;
            }
        }

        $this->newLine();
        $this->info('📊 Summary:');
        $this->table(
            ['Status', 'Count'],
            [
                ['Fixed (had discrepancies)', $fixed],
                ['Unchanged (already correct)', $unchanged],
                ['Errors', $errors],
                ['Total Processed', $accounts->count()],
            ]
        );

        if ($errors > 0) {
            return 1;
        }

        $this->newLine();
        $this->info('✅ Balance recalculation completed!');
        return 0;
    }
}
