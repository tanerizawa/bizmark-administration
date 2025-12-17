<?php

namespace App\Console\Commands\AutoPost;

use App\Models\AutoPostSchedule;
use App\Models\AutoPostLog;
use Illuminate\Console\Command;

class FixStuckSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autopost:fix-stuck {--timeout=10 : Minutes before considering a schedule stuck}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix schedules stuck in processing status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $timeout = (int) $this->option('timeout');
        
        $this->info("Checking for stuck schedules...");
        $this->newLine();
        
        // 1. Fix schedules stuck in processing
        $this->info("1. Checking schedules stuck in processing for more than {$timeout} minutes...");
        
        $stuck = AutoPostSchedule::where('status', 'processing')
            ->where('started_at', '<', now()->subMinutes($timeout))
            ->get();
        
        $fixed = 0;
        $reset = 0;
        
        foreach ($stuck as $schedule) {
            $this->line("Processing Schedule #{$schedule->id}...");
            
            // Check if article was actually created
            $publishLog = AutoPostLog::where('schedule_id', $schedule->id)
                ->where('event', 'article_published')
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($publishLog) {
                // Article was published, mark as completed
                $createLog = AutoPostLog::where('schedule_id', $schedule->id)
                    ->where('event', 'article_created')
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                $articleId = null;
                if ($createLog && isset($createLog->context['article_id'])) {
                    $articleId = $createLog->context['article_id'];
                }
                
                $schedule->update([
                    'status' => 'completed',
                    'article_id' => $articleId,
                    'completed_at' => $publishLog->created_at,
                ]);
                
                $this->info("  ✓ Schedule #{$schedule->id} marked as completed (Article ID: {$articleId})");
                $fixed++;
            } else {
                // No article created, reset to pending
                $schedule->update([
                    'status' => 'pending',
                    'started_at' => null,
                    'attempts' => 0,
                    'error_message' => 'Auto-reset from stuck processing state',
                ]);
                
                $this->warn("  ↻ Schedule #{$schedule->id} reset to pending (no article found)");
                $reset++;
            }
        }
        
        if ($stuck->isEmpty()) {
            $this->info('  ✓ No stuck processing schedules found.');
        }
        
        $this->newLine();
        
        // 2. Fix schedules with articles but wrong status
        $this->info("2. Checking schedules with articles but not marked as completed...");
        
        $broken = AutoPostSchedule::whereNotNull('article_id')
            ->where('status', '!=', 'completed')
            ->with('article')
            ->get();
        
        $fixedBroken = 0;
        
        foreach ($broken as $schedule) {
            $this->line("Processing Schedule #{$schedule->id}...");
            
            if ($schedule->article) {
                $schedule->update([
                    'status' => 'completed',
                    'completed_at' => $schedule->article->created_at,
                ]);
                
                $this->info("  ✓ Schedule #{$schedule->id} marked as completed (Article ID: {$schedule->article_id})");
                $fixedBroken++;
            } else {
                // Article_id exists but article was deleted
                $this->warn("  ⚠ Schedule #{$schedule->id} has article_id but article not found - resetting");
                $schedule->update([
                    'status' => 'pending',
                    'article_id' => null,
                    'started_at' => null,
                ]);
            }
        }
        
        if ($broken->isEmpty()) {
            $this->info('  ✓ No broken schedules found.');
        }
        
        $this->newLine();
        
        // 3. Find orphaned articles (articles created but not linked to schedule)
        $this->info("3. Checking for orphaned articles (created but not linked)...");
        
        $processing = AutoPostSchedule::where('status', 'processing')
            ->whereNull('article_id')
            ->with('topic')
            ->get();
        
        $linked = 0;
        
        foreach ($processing as $schedule) {
            $this->line("Checking Schedule #{$schedule->id}...");
            
            // Try to find article by checking logs first
            $createLog = AutoPostLog::where('schedule_id', $schedule->id)
                ->where('event', 'article_created')
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($createLog && isset($createLog->context['article_id'])) {
                $articleId = $createLog->context['article_id'];
                $article = \App\Models\Article::find($articleId);
                
                if ($article) {
                    $schedule->update([
                        'status' => 'completed',
                        'article_id' => $articleId,
                        'completed_at' => $article->created_at,
                    ]);
                    
                    $this->info("  ✓ Linked to Article #{$articleId} (from logs): {$article->title}");
                    $linked++;
                    continue;
                }
            }
            
            // Try to find article by matching title with topic
            if ($schedule->topic) {
                $article = \App\Models\Article::where('title', 'LIKE', '%' . substr($schedule->topic->title, 0, 30) . '%')
                    ->where('created_at', '>', $schedule->started_at ?? $schedule->scheduled_at)
                    ->where('created_at', '<', now())
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if ($article) {
                    $schedule->update([
                        'status' => 'completed',
                        'article_id' => $article->id,
                        'completed_at' => $article->created_at,
                    ]);
                    
                    $this->info("  ✓ Linked to Article #{$article->id} (by title match): {$article->title}");
                    $linked++;
                    continue;
                }
            }
        }
        
        if ($processing->isEmpty() || $linked === 0) {
            $this->info('  ✓ No orphaned articles found.');
        }
        
        $this->newLine();
        $this->info("Summary:");
        $this->info("  Stuck Processing Fixed: {$fixed}");
        $this->info("  Stuck Processing Reset: {$reset}");
        $this->info("  Wrong Status Fixed: {$fixedBroken}");
        $this->info("  Orphaned Articles Linked: {$linked}");
        $this->info("  Total Fixed: " . ($fixed + $fixedBroken + $linked));
        
        return 0;
    }
}
