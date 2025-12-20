<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BacklinkTarget;
use App\Models\BacklinkOutreach;

class FixBacklinkTargetStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backlink:fix-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix backlink target statuses based on outreach history';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Fixing Backlink Target Statuses...');
        $this->newLine();

        // Find targets with status 'pending' but have sent outreaches
        $pendingWithOutreach = BacklinkTarget::where('status', 'pending')
            ->whereHas('outreaches', function($q) {
                $q->where('status', 'sent');
            })
            ->with('outreaches')
            ->get();

        if ($pendingWithOutreach->isEmpty()) {
            $this->info('✅ No targets need status fixing.');
            return 0;
        }

        $this->info("Found {$pendingWithOutreach->count()} target(s) with incorrect status");
        $this->newLine();

        $fixed = 0;
        $progressBar = $this->output->createProgressBar($pendingWithOutreach->count());
        $progressBar->start();

        foreach ($pendingWithOutreach as $target) {
            // Update status to 'contacted' since they have sent outreaches
            $target->update(['status' => 'contacted']);
            $fixed++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ Fixed {$fixed} target status(es)");
        
        // Show summary
        $this->newLine();
        $this->info('📊 Current Status Distribution:');
        $statusCounts = BacklinkTarget::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->orderBy('count', 'desc')
            ->get();

        $this->table(
            ['Status', 'Count'],
            $statusCounts->map(fn($s) => [$s->status, $s->count])
        );

        return 0;
    }
}
