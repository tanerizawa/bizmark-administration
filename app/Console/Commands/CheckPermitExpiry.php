<?php

namespace App\Console\Commands;

use App\Models\PermitExpiryMonitor;
use App\Notifications\PermitExpiryNotification;
use Illuminate\Console\Command;

class CheckPermitExpiry extends Command
{
    protected $signature = 'permits:check-expiry {--dry-run : Preview without sending notifications}';

    protected $description = 'Check permits expiring in 90/30/7 days and send notifications to clients';

    public function handle(): int
    {
        $isDry = $this->option('dry-run');
        $thresholds = [90, 30, 7];
        $total = 0;

        foreach ($thresholds as $days) {
            $column = "notified_{$days}";
            $monitors = PermitExpiryMonitor::where('status', '!=', 'expired')
                ->where('status', '!=', 'renewed')
                ->where($column, false)
                ->whereDate('expires_at', '<=', now()->addDays($days))
                ->whereDate('expires_at', '>', now())
                ->with('client')
                ->get();

            foreach ($monitors as $monitor) {
                if ($isDry) {
                    $this->line("  [DRY] {$monitor->permit_type} — client_id={$monitor->client_id}, days={$days}");

                    continue;
                }

                if ($monitor->client) {
                    $monitor->client->notify(new PermitExpiryNotification($monitor, $days));
                }

                $monitor->update([
                    $column => true,
                    'last_notified_at' => now(),
                    'status' => 'expiring_soon',
                ]);
            }

            $count = $monitors->count();
            $total += $count;
            $this->info("H-{$days}: {$count} permit(s) notified".($isDry ? ' (dry-run)' : ''));
        }

        // Mark expired permits
        if (! $isDry) {
            $expired = PermitExpiryMonitor::where('status', '!=', 'expired')
                ->where('status', '!=', 'renewed')
                ->whereDate('expires_at', '<', now())
                ->update(['status' => 'expired']);

            $this->info("Marked {$expired} permit(s) as expired.");
        }

        $this->info("Done. Total notified: {$total}.");

        return self::SUCCESS;
    }
}
