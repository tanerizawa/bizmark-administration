# RAG Integration - Quick Start Guide

**Estimated Time:** 2-3 hours for basic integration  
**Difficulty:** Intermediate

## Prerequisites Checklist

- [  ] API credentials for https://api.bizmark.id/
- [  ] Laravel 12.x environment running
- [  ] Database backup completed
- [  ] Composer installed
- [  ] Git for version control

---

## Step-by-Step Implementation

### Step 1: Test API Connection (15 minutes)

**1.1 Create test script:**

```bash
cd /home/bizmark/bizmark.id
php artisan tinker
```

**1.2 Test API manually:**

```php
use Illuminate\Support\Facades\Http;

$response = Http::post('https://api.bizmark.id/api/auth/login', [
    'username' => 'your_username',
    'password' => 'your_password',
]);

$token = $response->json('access_token');

// Test query
$query = Http::withToken($token)->post('https://api.bizmark.id/api/queries', [
    'question' => 'Apa persyaratan untuk mendirikan PT di Jakarta?',
    'include_sources' => true,
]);

dump($query->json());
```

**Expected Output:**
```json
{
  "id": "uuid-here",
  "answer": "Untuk mendirikan PT di Jakarta...",
  "sources": [...],
  "confidence_score": 0.87
}
```

---

### Step 2: Create Service Class (30 minutes)

**2.1 Create service file:**

```bash
mkdir -p app/Services
touch app/Services/PerizinanAIService.php
```

**2.2 Add service code:**
(See full code in RAG_INTEGRATION_PLAN.md - Section 1.1)

**2.3 Register service provider:**

```bash
php artisan make:provider PerizinanAIServiceProvider
```

Edit `app/Providers/PerizinanAIServiceProvider.php`:

```php
public function register()
{
    $this->app->singleton(PerizinanAIService::class, function ($app) {
        return new PerizinanAIService();
    });
}
```

Add to `config/app.php`:

```php
'providers' => [
    // ...
    App\Providers\PerizinanAIServiceProvider::class,
],
```

---

### Step 3: Add Configuration (10 minutes)

**3.1 Update config/services.php:**

```php
'perizinan_ai' => [
    'url' => env('PERIZINAN_AI_URL', 'https://api.bizmark.id'),
    'username' => env('PERIZINAN_AI_USERNAME'),
    'password' => env('PERIZINAN_AI_PASSWORD'),
],
```

**3.2 Update .env:**

```bash
PERIZINAN_AI_URL=https://api.bizmark.id
PERIZINAN_AI_USERNAME=your_username
PERIZINAN_AI_PASSWORD=your_secure_password
```

**3.3 Clear config cache:**

```bash
php artisan config:clear
php artisan cache:clear
```

---

### Step 4: Database Migration (15 minutes)

**4.1 Create migration:**

```bash
php artisan make:migration add_rag_insights_to_consult_requests
```

**4.2 Edit migration file:**
(See full code in RAG_INTEGRATION_PLAN.md - Section 1.4)

**4.3 Run migration:**

```bash
php artisan migrate
```

**4.4 Verify:**

```bash
psql -U postgres -d bizmark_db -c "\d consult_requests"
```

Should show: `rag_insights`, `rag_confidence` columns

---

### Step 5: Update Controller (45 minutes)

**5.1 Backup current controller:**

```bash
cp app/Http/Controllers/Api/ConsultationController.php \
   app/Http/Controllers/Api/ConsultationController.php.backup
```

**5.2 Update ConsultationController:**
(See full enhanced code in RAG_INTEGRATION_PLAN.md - Section 1.3)

**Key changes:**
- Inject `PerizinanAIService` in constructor
- Call RAG before pricing engine
- Store RAG insights in database
- Handle errors gracefully

**5.3 Test manually:**

```bash
curl -X POST https://bizmark.id/api/consultation/estimate \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your-csrf-token" \
  -d '{
    "business_type": "PT",
    "location_type": "jakarta",
    "employee_count": 25,
    "business_activities": [...]
  }'
```

---

### Step 6: Update Frontend (30 minutes)

**6.1 Test current form:**

Visit: https://bizmark.id/estimasi-biaya

**6.2 Add result display component:**

```bash
mkdir -p resources/views/consultation/partials
touch resources/views/consultation/partials/rag-insights.blade.php
```

**6.3 Add component code:**
(See full code in RAG_INTEGRATION_PLAN.md - Section 2.1)

**6.4 Include in main view:**

Edit `resources/views/consultation/index.blade.php`:

```blade
<!-- After cost estimation section -->
@include('consultation.partials.rag-insights', ['ragContext' => $result['rag_context'] ?? null])
```

**6.5 Clear view cache:**

```bash
php artisan view:clear
```

---

### Step 7: Test Integration (30 minutes)

**7.1 Unit Test:**

```bash
php artisan make:test PerizinanAIServiceTest --unit
```

```php
public function test_can_query_rag_system()
{
    $service = app(PerizinanAIService::class);
    
    $result = $service->query('Test question');
    
    $this->assertArrayHasKey('answer', $result);
    $this->assertArrayHasKey('sources', $result);
    $this->assertArrayHasKey('confidence_score', $result);
}
```

**7.2 Integration Test:**

```bash
# Submit form via browser
open https://bizmark.id/estimasi-biaya

# Fill form and submit
# Check console for errors
# Verify RAG insights appear
```

**7.3 Database Check:**

```sql
SELECT 
  name,
  email,
  rag_confidence,
  rag_insights->>'answer' as rag_answer,
  created_at
FROM consult_requests
WHERE rag_insights IS NOT NULL
ORDER BY created_at DESC
LIMIT 5;
```

---

## Verification Checklist

After implementation, verify:

- [ ] API authentication works
- [ ] Token caching functioning (check Redis/cache)
- [ ] RAG queries return results
- [ ] Database stores rag_insights
- [ ] Frontend displays regulation context
- [ ] Error handling works (test with invalid credentials)
- [ ] Performance acceptable (< 5 seconds total)
- [ ] No console errors
- [ ] Mobile responsive
- [ ] Dark mode works

---

## Troubleshooting

### Issue: "Failed to authenticate"

**Solution:**
```bash
# Test credentials manually
php artisan tinker
>>> Http::post('https://api.bizmark.id/api/auth/login', ['username' => 'test', 'password' => 'test'])->json();
```

### Issue: "RAG query timeout"

**Solution:**
```php
// Increase timeout in PerizinanAIService
Http::withToken($token)
    ->timeout(60)  // Increase from 30 to 60
    ->post(...);
```

### Issue: "Column rag_insights does not exist"

**Solution:**
```bash
php artisan migrate:refresh --path=database/migrations/2025_12_04_add_rag_insights_to_consult_requests.php
```

### Issue: "Sources not displaying"

**Solution:**
```blade
{{-- Debug in Blade template --}}
@if($ragContext)
  <pre>{{ json_encode($ragContext, JSON_PRETTY_PRINT) }}</pre>
@endif
```

---

## Performance Optimization

### 1. Cache RAG Responses

```php
public function getBusinessTypeRegulations(string $businessType, string $location): array
{
    $cacheKey = "rag:{$businessType}:{$location}";
    
    return Cache::remember($cacheKey, 3600, function () use ($businessType, $location) {
        return $this->query("...");
    });
}
```

### 2. Async Processing (Optional)

```php
use Illuminate\Support\Facades\Queue;

Queue::push(function () use ($consultationId) {
    $ragContext = $this->ragService->query(...);
    
    ConsultRequest::find($consultationId)->update([
        'rag_insights' => $ragContext,
    ]);
});
```

### 3. Batch Requests

For multiple KBLI codes, batch them:

```php
public function batchQuery(array $questions): array
{
    // Implement batching logic
}
```

---

## Monitoring

### Add Logging

```php
// In PerizinanAIService
Log::channel('rag')->info('RAG Query', [
    'question' => $question,
    'response_time_ms' => $responseTime,
    'confidence' => $result['confidence_score'],
    'sources_count' => count($result['sources']),
]);
```

### Create Log Channel

In `config/logging.php`:

```php
'rag' => [
    'driver' => 'daily',
    'path' => storage_path('logs/rag.log'),
    'level' => 'debug',
    'days' => 14,
],
```

---

## Rollback Plan

If integration causes issues:

```bash
# 1. Restore controller backup
cp app/Http/Controllers/Api/ConsultationController.php.backup \
   app/Http/Controllers/Api/ConsultationController.php

# 2. Rollback migration (optional)
php artisan migrate:rollback --step=1

# 3. Clear caches
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# 4. Remove service (optional)
# Comment out in config/app.php providers array
```

---

## Next Steps

After basic integration works:

1. **Monitor for 1 week:**
   - Check error logs
   - Monitor response times
   - Gather user feedback

2. **Iterate:**
   - Improve UI based on feedback
   - Optimize cache strategy
   - Add more regulation sources

3. **Expand:**
   - Admin dashboard for RAG insights
   - KBLI-specific queries
   - Real-time regulation updates

---

## Support

**Documentation:** `/docs/RAG_INTEGRATION_PLAN.md`  
**API Docs:** https://api.bizmark.id/docs  
**Issues:** Check logs at `storage/logs/rag.log`

**Quick Health Check:**

```bash
php artisan tinker
>>> app(App\Services\PerizinanAIService::class)->query('test');
```

---

**Last Updated:** December 4, 2025  
**Estimated Total Time:** 2-3 hours  
**Difficulty:** ⭐⭐⭐ (Intermediate)
