# 🔧 Stuck Processing Fix Plan

## Root Cause Analysis

### Primary Issue: Job Crashes After Status Update
**Problem:** Job updates status to "processing" → crashes di API call → status stuck forever

**Evidence:**
- Schedule #16: processing since 02:44:22, no article created
- OpenRouter API errors: "Input required: specify 'prompt' or 'messages'"
- Similarity calculation failures: Undefined array key "choices"
- No detailed auto_post_logs (job crashed before logging)

### Secondary Issue: Race Condition
**Problem:** Queue retry dispatched before first job finishes crashing

**Evidence:**
- [02:44:22] Job #1 started (attempt 1)
- [02:45:23] Job #2 started (attempt 2) - only 61 seconds later
- Job #2 sees status=processing, exits immediately

## Fix Strategy

### FIX 1: Wrap Status Update in Transaction with Error Handling ✅
**Goal:** Jika job crash, rollback status ke pending

**Implementation:**
```php
// Use DB transaction untuk atomic status update
DB::transaction(function() use ($schedule) {
    $schedule->update(['status' => 'processing', 'started_at' => now()]);
    
    try {
        // Execute article generation
        $article = $service->executeScheduledPost($schedule);
        $schedule->update(['status' => 'completed', 'completed_at' => now()]);
    } catch (\Exception $e) {
        // Rollback status on crash
        $schedule->update(['status' => 'pending', 'started_at' => null]);
        throw $e;
    }
});
```

**Files to Modify:**
- `app/Jobs/GenerateAutoPostArticle.php` - handle() method

### FIX 2: Add Timeout Detection in Health Check ✅
**Goal:** Auto-detect jobs running > 5 minutes (timeout exceeded)

**Implementation:**
```php
// In AutoPostHealthCheck
$timeout = now()->subMinutes(5);
$timedOut = AutoPostSchedule::where('status', 'processing')
    ->where('started_at', '<', $timeout)
    ->get();

foreach ($timedOut as $schedule) {
    echo "⏱️  Timed out: #{$schedule->id} (running for " . 
         $schedule->started_at->diffInMinutes(now()) . " minutes)\n";
    
    if ($fix) {
        $schedule->update([
            'status' => 'pending',
            'started_at' => null,
            'error_message' => 'Timeout exceeded (>5 minutes)',
        ]);
    }
}
```

**Files to Modify:**
- `app/Console/Commands/AutoPostHealthCheck.php`

### FIX 3: Fix TopicSimilarityService API Calls ✅
**Goal:** Fix OpenRouter API errors causing crashes

**Problem:** API request tidak menyertakan "messages" parameter

**Implementation:**
- Review TopicSimilarityService::calculateSimilarity()
- Ensure proper API request format
- Add error handling untuk API failures

**Files to Check:**
- `app/Services/TopicSimilarityService.php`

### FIX 4: Improve Queue Job Resilience ✅
**Goal:** Prevent duplicate processing, better error recovery

**Implementation:**
```php
// Add pessimistic locking
$schedule = AutoPostSchedule::lockForUpdate()->find($id);

if ($schedule->status !== 'pending') {
    return; // Already being processed
}

// Proceed with processing...
```

**Files to Modify:**
- `app/Jobs/GenerateAutoPostArticle.php`

### FIX 5: Add Detailed Logging at Each Step ✅
**Goal:** Identify exact crash point

**Implementation:**
```php
\Log::info('Step 1: Loading schedule');
\Log::info('Step 2: Checking similarity');
\Log::info('Step 3: Generating content');
// etc...
```

## Implementation Priority

1. **CRITICAL:** Fix stuck schedule #16 (manual reset)
2. **HIGH:** Fix TopicSimilarityService API errors
3. **HIGH:** Add timeout detection to health check
4. **MEDIUM:** Wrap job in transaction with rollback
5. **LOW:** Add detailed step logging

## Immediate Actions

### Action 1: Reset Stuck Schedule #16
```bash
php artisan tinker --execute="
\$s = App\Models\AutoPostSchedule::find(16);
\$s->update(['status' => 'pending', 'started_at' => null, 'error_message' => 'Reset from stuck processing']);
\$s->topic->clearScheduling();
echo 'Schedule #16 reset to pending';
"
```

### Action 2: Test Similarity Service
```bash
php artisan tinker --execute="
\$service = app(App\Services\TopicSimilarityService::class);
\$topic1 = App\Models\ArticleTopic::first();
\$topic2 = App\Models\ArticleTopic::skip(1)->first();
try {
    \$similarity = \$service->calculateSimilarity(\$topic1->title, \$topic2->title);
    echo 'Similarity: ' . \$similarity;
} catch (\Exception \$e) {
    echo 'ERROR: ' . \$e->getMessage();
}
"
```

### Action 3: Check OpenRouter API Config
```bash
grep -r "OPENROUTER" .env
```

## Success Criteria

- [ ] Schedule #16 successfully processes
- [ ] No API errors in logs
- [ ] Health check detects timeouts
- [ ] Jobs complete within 5 minutes
- [ ] No stuck processing status

## Monitoring

```bash
# Watch for stuck processing
watch -n 30 "php artisan tinker --execute=\"
echo 'Processing: ' . App\Models\AutoPostSchedule::where('status', 'processing')->count();
echo 'Pending: ' . App\Models\AutoPostSchedule::where('status', 'pending')->count();
\""

# Watch logs
tail -f storage/logs/laravel.log | grep -i "schedule_id\":16"
```

---

**Created:** 2026-01-04 02:49
**Status:** 🚧 In Progress
**Priority:** 🔴 CRITICAL
