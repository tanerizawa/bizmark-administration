<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\ArticleTopic;
use App\Models\AutoPostLog;
use App\Models\AutoPostSchedule;
use Illuminate\Console\Command;

class AutoPostHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:health-check {--fix : Automatically fix stuck schedules}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check auto-post system health and detect stuck schedules';

    /**
     * Resolve article ID from schedule direct link, topic link, or logs.
     */
    protected function resolveArticleId(AutoPostSchedule $schedule): ?int
    {
        if (! empty($schedule->article_id) && Article::find($schedule->article_id)) {
            return (int) $schedule->article_id;
        }

        $topic = ArticleTopic::withTrashed()->find($schedule->topic_id);
        if ($topic && ! empty($topic->article_id) && Article::find($topic->article_id)) {
            return (int) $topic->article_id;
        }

        $log = AutoPostLog::where('schedule_id', $schedule->id)
            ->whereIn('event', ['article_created', 'article_published'])
            ->orderByDesc('created_at')
            ->first();

        if ($log) {
            $candidateId = $log->article_id ?? data_get($log->context, 'article_id');
            if (! empty($candidateId) && Article::find($candidateId)) {
                return (int) $candidateId;
            }
        }

        return null;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🏥 AUTO-POST HEALTH CHECK');
        $this->info('═══════════════════════════════════════════');
        $this->newLine();

        $issues = [];
        $fixed = 0;

        // 1. Check stuck processing (>5 minutes - job timeout)
        $stuckProcessing = AutoPostSchedule::where('status', 'processing')
            ->where(function ($q) {
                $q->where('started_at', '<', now()->subMinutes(5))
                    ->orWhereNull('started_at');
            })
            ->with('topic')
            ->get();

        if ($stuckProcessing->count() > 0) {
            $this->warn("⚠️  Found {$stuckProcessing->count()} schedule(s) stuck in processing:");
            foreach ($stuckProcessing as $schedule) {
                $duration = $schedule->started_at ? $schedule->started_at->diffForHumans() : 'never started';
                $topicTitle = $schedule->topic?->title ?? "[Topic unavailable #{$schedule->topic_id}]";
                $this->line("   #{$schedule->id} - {$topicTitle}");
                $this->line("   Scheduled: {$schedule->scheduled_at->format('Y-m-d H:i')} | Processing: {$duration}");

                if ($this->option('fix')) {
                    $this->fixStuckSchedule($schedule, 'Job timeout exceeded (>5 minutes)');
                    $fixed++;
                }
            }
            $issues[] = 'Stuck processing';
            $this->newLine();
        }

        // 2. Check missed schedules (pending but past due >2 hours)
        $missedSchedules = AutoPostSchedule::where('status', 'pending')
            ->where('scheduled_at', '<', now()->subHours(2))
            ->with('topic')
            ->get();

        if ($missedSchedules->count() > 0) {
            $this->warn("⏰ Found {$missedSchedules->count()} missed schedule(s):");
            foreach ($missedSchedules as $schedule) {
                $delay = $schedule->scheduled_at->diffForHumans();
                $topicTitle = $schedule->topic?->title ?? "[Topic unavailable #{$schedule->topic_id}]";
                $this->line("   #{$schedule->id} - {$topicTitle}");
                $this->line("   Scheduled: {$schedule->scheduled_at->format('Y-m-d H:i')} | Missed: {$delay}");

                if ($this->option('fix')) {
                    // Reschedule to next available slot
                    $this->rescheduleToNextSlot($schedule);
                    $fixed++;
                }
            }
            $issues[] = 'Missed schedules';
            $this->newLine();
        }

        // 3. Check schedules with high attempt count
        $highAttempts = AutoPostSchedule::where('status', 'pending')
            ->where('attempts', '>=', 3)
            ->with('topic')
            ->get();

        if ($highAttempts->count() > 0) {
            $this->warn("🔄 Found {$highAttempts->count()} schedule(s) with multiple failed attempts:");
            foreach ($highAttempts as $schedule) {
                $topicTitle = $schedule->topic?->title ?? "[Topic unavailable #{$schedule->topic_id}]";
                $this->line("   #{$schedule->id} - {$topicTitle}");
                $this->line("   Attempts: {$schedule->attempts} | Last error: ".substr($schedule->error_message ?? 'N/A', 0, 50));

                if ($this->option('fix') && $schedule->attempts >= 5) {
                    // Mark as failed after 5 attempts
                    $schedule->update([
                        'status' => 'failed',
                        'error_message' => 'Max retry attempts exceeded (5)',
                    ]);
                    if ($schedule->topic) {
                        $schedule->topic->markAsFailed();
                    }
                    $this->line('   ❌ Marked as failed (max attempts exceeded)');
                    $fixed++;
                }
            }
            $issues[] = 'High retry attempts';
            $this->newLine();
        }

        // 4. Summary
        // 4. Reconcile inconsistent status: article exists but schedule still pending/processing
        $inconsistent = AutoPostSchedule::whereIn('status', ['pending', 'processing'])->get();
        $reconciled = 0;

        foreach ($inconsistent as $schedule) {
            $articleId = $this->resolveArticleId($schedule);
            if (! $articleId) {
                continue;
            }

            $this->warn("🔁 Found inconsistent schedule #{$schedule->id}: status={$schedule->status}, article_id={$articleId}");
            $issues[] = 'Inconsistent schedule status';

            if ($this->option('fix')) {
                $schedule->update([
                    'status' => 'completed',
                    'article_id' => $articleId,
                    'completed_at' => $schedule->completed_at ?? now(),
                    'error_message' => null,
                ]);
                $this->line('   🔧 Reconciled to completed');
                $reconciled++;
                $fixed++;
            }
        }

        if ($reconciled > 0) {
            $this->newLine();
        }

        // 5. Summary
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        if (empty($issues)) {
            $this->info('✅ System healthy - no issues detected');
        } else {
            $this->warn('⚠️  Issues found: '.implode(', ', $issues));

            if ($this->option('fix')) {
                $this->info("🔧 Fixed {$fixed} issue(s)");
            } else {
                $this->line('');
                $this->comment('Run with --fix flag to automatically resolve issues:');
                $this->comment('  php artisan articles:health-check --fix');
            }
        }

        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // For automated healing runs, consider command successful when issues are fixed.
        if ($this->option('fix')) {
            return 0;
        }

        return empty($issues) ? 0 : 1;
    }

    /**
     * Fix stuck schedule by resetting to pending
     */
    protected function fixStuckSchedule(AutoPostSchedule $schedule, string $reason)
    {
        $schedule->update([
            'status' => 'pending',
            'started_at' => null,
            'completed_at' => null,
            'error_message' => "Reset by health check: {$reason}",
            'attempts' => $schedule->attempts + 1,
        ]);

        if ($schedule->topic) {
            $schedule->topic->clearScheduling();
        }

        $this->line("   🔧 Reset to pending (attempt #{$schedule->attempts})");
    }

    /**
     * Reschedule missed schedule to next available slot
     */
    protected function rescheduleToNextSlot(AutoPostSchedule $schedule)
    {
        // Find next available time slot (avoid conflicts)
        $nextSlot = now()->addHours(2)->startOfHour();

        while (AutoPostSchedule::where('scheduled_at', $nextSlot)->exists()) {
            $nextSlot->addHour();
        }

        $schedule->update([
            'scheduled_at' => $nextSlot,
            'attempts' => $schedule->attempts + 1,
            'error_message' => 'Rescheduled from '.$schedule->scheduled_at->format('Y-m-d H:i'),
        ]);

        if ($schedule->topic) {
            $schedule->topic->update(['scheduled_for' => $nextSlot]);
        }

        $this->line("   🔧 Rescheduled to {$nextSlot->format('Y-m-d H:i')}");
    }
}
