<?php

namespace App\Console\Commands;

use App\Jobs\GenerateAutoPostArticle;
use App\Models\Article;
use App\Models\ArticleTopic;
use App\Models\AutoPostSchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoPostDiagnose extends Command
{
    protected $signature = 'articles:diagnose 
                            {--fix : Automatically fix issues found}
                            {--test : Run comprehensive tests}
                            {--dispatch : Dispatch a test job after fixing}';

    protected $description = 'Diagnose and fix auto-post system issues comprehensively';

    public function handle(): int
    {
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('        AUTO-POST SYSTEM DIAGNOSTIC & REPAIR TOOL');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->newLine();

        $issues = [];

        // 1. Check orphan schedules (processing but no article)
        $issues = array_merge($issues, $this->checkOrphanSchedules());

        // 2. Check schedules that should be linked to existing articles
        $issues = array_merge($issues, $this->checkUnlinkedSchedules());

        // 3. Check stuck processing schedules
        $issues = array_merge($issues, $this->checkStuckSchedules());

        // 4. Check queue health
        $issues = array_merge($issues, $this->checkQueueHealth());

        // 5. Check log permissions
        $issues = array_merge($issues, $this->checkPermissions());

        // Summary
        $this->newLine();
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('                         SUMMARY');
        $this->info('═══════════════════════════════════════════════════════════════');

        if (empty($issues)) {
            $this->info('✅ No issues found! System is healthy.');
        } else {
            $this->warn('Found '.count($issues).' issue(s):');
            foreach ($issues as $issue) {
                $this->line("  • {$issue['type']}: {$issue['message']}");
            }

            if ($this->option('fix')) {
                $this->newLine();
                $this->info('🔧 Applying fixes...');
                $this->applyFixes($issues);
            } else {
                $this->newLine();
                $this->comment('Run with --fix to automatically repair issues.');
            }
        }

        // Run tests if requested
        if ($this->option('test')) {
            $this->newLine();
            $this->runComprehensiveTests();
        }

        // Dispatch test job if requested
        if ($this->option('dispatch')) {
            $this->newLine();
            $this->dispatchTestJob();
        }

        return Command::SUCCESS;
    }

    protected function checkOrphanSchedules(): array
    {
        $this->info('🔍 Checking for orphan schedules...');
        $issues = [];

        // Schedules stuck in processing without article for more than 10 minutes
        $orphans = AutoPostSchedule::where('status', 'processing')
            ->whereNull('article_id')
            ->where('updated_at', '<', now()->subMinutes(10))
            ->get();

        foreach ($orphans as $schedule) {
            $issues[] = [
                'type' => 'orphan_schedule',
                'message' => "Schedule #{$schedule->id} stuck in processing (no article)",
                'schedule_id' => $schedule->id,
                'fix' => 'reset_to_pending',
            ];
            $this->warn("  ⚠️  Schedule #{$schedule->id} is orphaned (processing > 10min, no article)");
        }

        if (empty($orphans)) {
            $this->line('  ✓ No orphan schedules found');
        }

        return $issues;
    }

    protected function checkUnlinkedSchedules(): array
    {
        $this->info('🔍 Checking for unlinked schedules with matching articles...');
        $issues = [];

        // Get pending/processing schedules
        $schedules = AutoPostSchedule::whereIn('status', ['pending', 'processing', 'failed'])
            ->whereNull('article_id')
            ->with('topic')
            ->get();

        foreach ($schedules as $schedule) {
            if (! $schedule->topic) {
                continue;
            }

            // Check if article with same title exists
            $existingArticle = Article::where('title', $schedule->topic->title)->first();

            if ($existingArticle) {
                $issues[] = [
                    'type' => 'unlinked_schedule',
                    'message' => "Schedule #{$schedule->id} has existing article #{$existingArticle->id} but not linked",
                    'schedule_id' => $schedule->id,
                    'article_id' => $existingArticle->id,
                    'fix' => 'link_and_complete',
                ];
                $this->warn("  ⚠️  Schedule #{$schedule->id} can be linked to Article #{$existingArticle->id}");
            }
        }

        if (empty($issues)) {
            $this->line('  ✓ No unlinked schedules found');
        }

        return $issues;
    }

    protected function checkStuckSchedules(): array
    {
        $this->info('🔍 Checking for stuck schedules...');
        $issues = [];

        // Schedules in processing for more than 5 minutes
        $stuck = AutoPostSchedule::where('status', 'processing')
            ->where('updated_at', '<', now()->subMinutes(5))
            ->get();

        foreach ($stuck as $schedule) {
            // If has article, mark as completed
            if ($schedule->article_id) {
                $issues[] = [
                    'type' => 'stuck_with_article',
                    'message' => "Schedule #{$schedule->id} stuck processing but has article",
                    'schedule_id' => $schedule->id,
                    'fix' => 'mark_completed',
                ];
            } else {
                $issues[] = [
                    'type' => 'stuck_no_article',
                    'message' => "Schedule #{$schedule->id} stuck processing without article",
                    'schedule_id' => $schedule->id,
                    'fix' => 'reset_to_pending',
                ];
            }
            $this->warn("  ⚠️  Schedule #{$schedule->id} is stuck (processing > 5min)");
        }

        if (empty($stuck)) {
            $this->line('  ✓ No stuck schedules found');
        }

        return $issues;
    }

    protected function checkQueueHealth(): array
    {
        $this->info('🔍 Checking queue health...');
        $issues = [];

        // Check jobs table
        $pendingJobs = DB::table('jobs')->count();
        $failedJobs = DB::table('failed_jobs')->count();

        $this->line("  Jobs in queue: {$pendingJobs}");
        $this->line("  Failed jobs: {$failedJobs}");

        if ($failedJobs > 0) {
            $issues[] = [
                'type' => 'failed_jobs',
                'message' => "{$failedJobs} failed jobs in queue",
                'fix' => 'flush_failed',
            ];
            $this->warn("  ⚠️  {$failedJobs} failed jobs need attention");
        }

        // Check if supervisor is running
        $supervisorStatus = shell_exec('supervisorctl status bizmark-worker:* 2>&1');
        if (strpos($supervisorStatus, 'RUNNING') === false) {
            $issues[] = [
                'type' => 'supervisor_down',
                'message' => 'Supervisor workers not running',
                'fix' => 'manual',
            ];
            $this->error('  ✗ Supervisor workers not running!');
        } else {
            $this->line('  ✓ Supervisor workers are running');
        }

        return $issues;
    }

    protected function checkPermissions(): array
    {
        $this->info('🔍 Checking log permissions...');
        $issues = [];

        $logPath = storage_path('logs/laravel.log');

        if (file_exists($logPath)) {
            $perms = substr(sprintf('%o', fileperms($logPath)), -4);
            $owner = posix_getpwuid(fileowner($logPath))['name'] ?? 'unknown';
            $group = posix_getgrgid(filegroup($logPath))['name'] ?? 'unknown';

            $this->line("  Log file: {$owner}:{$group} ({$perms})");

            if (! is_writable($logPath)) {
                $issues[] = [
                    'type' => 'log_permission',
                    'message' => 'Laravel log file not writable',
                    'fix' => 'fix_permissions',
                ];
                $this->warn('  ⚠️  Log file not writable!');
            } else {
                $this->line('  ✓ Log file is writable');
            }
        }

        return $issues;
    }

    protected function applyFixes(array $issues): void
    {
        foreach ($issues as $issue) {
            switch ($issue['fix']) {
                case 'reset_to_pending':
                    $schedule = AutoPostSchedule::find($issue['schedule_id']);
                    if ($schedule) {
                        $schedule->update([
                            'status' => 'pending',
                            'started_at' => null,
                            'error_message' => 'Auto-reset by diagnose command',
                        ]);
                        $this->info("  ✓ Reset Schedule #{$schedule->id} to pending");
                    }
                    break;

                case 'link_and_complete':
                    $schedule = AutoPostSchedule::find($issue['schedule_id']);
                    if ($schedule) {
                        $schedule->update([
                            'status' => 'completed',
                            'article_id' => $issue['article_id'],
                            'completed_at' => now(),
                        ]);

                        // Also update topic
                        if ($schedule->topic) {
                            $schedule->topic->update([
                                'status' => 'published',
                                'article_id' => $issue['article_id'],
                            ]);
                        }

                        $this->info("  ✓ Linked Schedule #{$schedule->id} to Article #{$issue['article_id']}");
                    }
                    break;

                case 'mark_completed':
                    $schedule = AutoPostSchedule::find($issue['schedule_id']);
                    if ($schedule && $schedule->article_id) {
                        $schedule->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                        ]);
                        $this->info("  ✓ Marked Schedule #{$schedule->id} as completed");
                    }
                    break;

                case 'flush_failed':
                    $this->call('queue:flush');
                    $this->info('  ✓ Flushed failed jobs');
                    break;

                case 'fix_permissions':
                    $logPath = storage_path('logs');
                    @chmod($logPath, 0775);
                    foreach (glob("{$logPath}/*.log") as $logFile) {
                        @chmod($logFile, 0664);
                    }
                    $this->info('  ✓ Fixed log permissions');
                    break;

                case 'manual':
                    $this->comment("  ⏭️  Issue requires manual intervention: {$issue['message']}");
                    break;
            }
        }
    }

    protected function runComprehensiveTests(): void
    {
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('                   COMPREHENSIVE TESTS');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->newLine();

        $passed = 0;
        $failed = 0;

        // Test 1: Unique Slug Generation
        $this->info('Test 1: Unique Slug Generation');
        try {
            $title = 'Test Slug Generation '.time();
            $a1 = Article::create([
                'title' => $title,
                'content' => '<p>Test</p>',
                'excerpt' => 'Test',
                'category' => 'tips',
                'language' => 'id',
                'tags' => ['test'],
                'status' => 'draft',
                'author_id' => 6,
                'meta_title' => $title,
                'meta_description' => 'Test',
                'meta_keywords' => 'test',
                'reading_time' => 1,
                'is_featured' => false,
            ]);

            $a2 = Article::create([
                'title' => $title,
                'content' => '<p>Test 2</p>',
                'excerpt' => 'Test 2',
                'category' => 'tips',
                'language' => 'id',
                'tags' => ['test'],
                'status' => 'draft',
                'author_id' => 6,
                'meta_title' => $title,
                'meta_description' => 'Test 2',
                'meta_keywords' => 'test',
                'reading_time' => 1,
                'is_featured' => false,
            ]);

            if ($a1->slug !== $a2->slug) {
                $this->info("  ✓ PASSED: Unique slugs generated ({$a1->slug} vs {$a2->slug})");
                $passed++;
            } else {
                $this->error('  ✗ FAILED: Duplicate slugs!');
                $failed++;
            }

            // Cleanup
            $a1->forceDelete();
            $a2->forceDelete();
        } catch (\Exception $e) {
            $this->error('  ✗ FAILED: '.$e->getMessage());
            $failed++;
        }

        // Test 2: Queue Dispatch
        $this->info('Test 2: Queue Job Dispatch');
        try {
            $beforeCount = DB::table('jobs')->count();

            // Create test schedule
            $testTopic = ArticleTopic::first();
            if (! $testTopic) {
                $this->warn('  ⏭️  SKIPPED: No topics available');
            } else {
                $testSchedule = AutoPostSchedule::create([
                    'topic_id' => $testTopic->id,
                    'scheduled_at' => now()->addDays(30),
                    'status' => 'pending',
                ]);

                GenerateAutoPostArticle::dispatch($testSchedule);

                $afterCount = DB::table('jobs')->count();

                if ($afterCount > $beforeCount) {
                    $this->info('  ✓ PASSED: Job dispatched successfully');
                    $passed++;
                } else {
                    $this->error('  ✗ FAILED: Job not added to queue');
                    $failed++;
                }

                // Cleanup - delete test schedule and job
                $testSchedule->forceDelete();
                DB::table('jobs')->whereRaw('created_at >= ?', [now()->subMinute()])->delete();
            }
        } catch (\Exception $e) {
            $this->error('  ✗ FAILED: '.$e->getMessage());
            $failed++;
        }

        // Test 3: Log Writing
        $this->info('Test 3: Log Writing');
        try {
            $testMessage = 'DIAGNOSTIC_TEST_'.time();
            \Log::info($testMessage);

            $logContent = file_get_contents(storage_path('logs/laravel.log'));
            if (strpos($logContent, $testMessage) !== false) {
                $this->info('  ✓ PASSED: Log writing works');
                $passed++;
            } else {
                $this->error('  ✗ FAILED: Log message not found');
                $failed++;
            }
        } catch (\Exception $e) {
            $this->error('  ✗ FAILED: '.$e->getMessage());
            $failed++;
        }

        // Test 4: Database Connection
        $this->info('Test 4: Database Connection');
        try {
            $count = AutoPostSchedule::count();
            $this->info("  ✓ PASSED: Database connected ({$count} schedules)");
            $passed++;
        } catch (\Exception $e) {
            $this->error('  ✗ FAILED: '.$e->getMessage());
            $failed++;
        }

        // Test 5: Article Service Instantiation
        $this->info('Test 5: Service Container');
        try {
            $service = app(\App\Services\ArticleAutoPostService::class);
            $this->info('  ✓ PASSED: ArticleAutoPostService resolved');
            $passed++;
        } catch (\Exception $e) {
            $this->error('  ✗ FAILED: '.$e->getMessage());
            $failed++;
        }

        // Summary
        $this->newLine();
        $total = $passed + $failed;
        $this->info("Tests completed: {$passed}/{$total} passed");

        if ($failed > 0) {
            $this->error("{$failed} test(s) failed!");
        }
    }

    protected function dispatchTestJob(): void
    {
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('                   DISPATCH TEST JOB');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->newLine();

        $schedule = AutoPostSchedule::where('status', 'pending')
            ->orderBy('id')
            ->first();

        if (! $schedule) {
            $this->warn('No pending schedules available for testing.');

            return;
        }

        $this->info("Dispatching Schedule #{$schedule->id}...");
        $this->line('Topic: '.($schedule->topic->title ?? 'N/A'));

        GenerateAutoPostArticle::dispatch($schedule);

        $this->info('Job dispatched! Monitoring for 60 seconds...');
        $this->newLine();

        // Monitor progress
        $bar = $this->output->createProgressBar(12);
        $bar->start();

        for ($i = 0; $i < 12; $i++) {
            sleep(5);
            $bar->advance();

            $schedule->refresh();

            if ($schedule->status === 'completed') {
                $bar->finish();
                $this->newLine(2);
                $this->info('✅ SUCCESS! Schedule completed.');
                $this->line("  Article ID: #{$schedule->article_id}");
                $this->line("  Generation Time: {$schedule->generation_time_seconds}s");

                return;
            }

            if ($schedule->status === 'failed') {
                $bar->finish();
                $this->newLine(2);
                $this->error('❌ FAILED!');
                $this->line("  Error: {$schedule->error_message}");

                return;
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->warn("⏱️  Timeout - Schedule still {$schedule->status} after 60s");
    }
}
