# RAG Integration Plan - Bizmark.ID + Perizinan AI

**Date:** December 4, 2025  
**Objective:** Integrate RAG (Retrieval-Augmented Generation) from https://api.bizmark.id/ into consultation cost estimation system at https://bizmark.id/

---

## Current System Analysis

### 1. Bizmark.ID (Laravel - Main Website)
**Location:** `/home/bizmark/bizmark.id/`

**Current Cost Estimation System:**
- **Endpoint:** `/estimasi-biaya` (formerly `/konsultasi-gratis`)
- **Controller:** `app/Http/Controllers/Api/ConsultationController.php`
- **View:** `resources/views/consultation/index.blade.php`
- **Model:** `app/Models/ConsultRequest.php`

**Current Flow:**
```
User Input → ConsultationController 
          → ConsultationPricingEngine (Rule-based)
          → Database Storage (consult_requests table)
          → Return estimation to user
```

**Key Features:**
- ✅ Captures: business type, location, employee count, business activities
- ✅ AI-powered cost calculation using GPT-4
- ✅ Stores leads in PostgreSQL
- ✅ Maps location types for database compliance
- ✅ Validates business activities (KBLI codes)

**Limitations:**
- ❌ No context from Indonesian regulations
- ❌ Generic AI responses without regulatory knowledge
- ❌ Cannot cite specific regulation articles
- ❌ No versioning of regulation sources
- ❌ Limited to pre-defined business types

---

### 2. Perizinan AI API (FastAPI - RAG System)
**Location:** `/opt/perizinan-ai/`  
**URL:** https://api.bizmark.id/

**Architecture:**
```
FastAPI (Port 8000) ← Nginx reverse proxy
├── Next.js Admin Panel (Port 3000, path: /admin)
├── PostgreSQL (Structured data)
├── Qdrant Vector Store (Document embeddings)
└── Python RAG Engine
```

**Key Components:**

#### A. API Endpoints
- `POST /api/queries` - Main RAG query processing
- `POST /api/auth/login` - JWT authentication
- `GET /api/queries/history` - User query history
- `GET /api/documents` - Document management
- `GET /api/health` - System health check

#### B. RAG Capabilities
- ✅ Vector search using Qdrant
- ✅ Context-aware AI responses
- ✅ Source citation from regulations
- ✅ Confidence scoring
- ✅ Query history tracking
- ✅ Rate limiting (100/hour per IP)
- ✅ JWT authentication

#### C. Data Structure
**Query Request:**
```json
{
  "question": "string",
  "context": {},
  "filters": {},
  "include_sources": true
}
```

**Query Response:**
```json
{
  "id": "uuid",
  "question": "string",
  "answer": "string",
  "sources": [
    {
      "id": "uuid",
      "title": "string",
      "type": "regulation",
      "section": "string",
      "confidence_score": 0.95,
      "url": "string",
      "snippet": "string"
    }
  ],
  "confidence_score": 0.92,
  "processing_time_ms": 850
}
```

#### D. Authentication
- JWT tokens (access + refresh)
- User roles: user, admin
- Rate limiting per user/IP

---

## Integration Strategy

### Phase 1: API Integration (Week 1)

#### 1.1 Setup Authentication
**File:** `app/Services/PerizinanAIService.php` (NEW)

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PerizinanAIService
{
    private string $baseUrl;
    private string $username;
    private string $password;
    
    public function __construct()
    {
        $this->baseUrl = config('services.perizinan_ai.url');
        $this->username = config('services.perizinan_ai.username');
        $this->password = config('services.perizinan_ai.password');
    }
    
    /**
     * Get JWT access token (cached for 20 minutes)
     */
    private function getAccessToken(): string
    {
        return Cache::remember('perizinan_ai_token', 1200, function () {
            $response = Http::post("{$this->baseUrl}/api/auth/login", [
                'username' => $this->username,
                'password' => $this->password,
            ]);
            
            if ($response->failed()) {
                throw new \Exception('Failed to authenticate with Perizinan AI API');
            }
            
            return $response->json('access_token');
        });
    }
    
    /**
     * Query RAG system for regulation context
     */
    public function query(string $question, array $context = [], array $filters = []): array
    {
        $token = $this->getAccessToken();
        
        $response = Http::withToken($token)
            ->timeout(30)
            ->retry(3, 1000)
            ->post("{$this->baseUrl}/api/queries", [
                'question' => $question,
                'context' => $context,
                'filters' => $filters,
                'include_sources' => true,
            ]);
        
        if ($response->failed()) {
            Log::error('Perizinan AI query failed', [
                'question' => $question,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            
            throw new \Exception('RAG query failed');
        }
        
        return $response->json();
    }
    
    /**
     * Get regulation context for business type
     */
    public function getBusinessTypeRegulations(string $businessType, string $location): array
    {
        $question = "Apa saja persyaratan perizinan untuk bisnis {$businessType} di {$location}?";
        
        return $this->query($question, [
            'business_type' => $businessType,
            'location' => $location,
        ], [
            'document_type' => 'regulation',
        ]);
    }
    
    /**
     * Get KBLI-specific requirements
     */
    public function getKBLIRequirements(string $kbliCode, string $description): array
    {
        $question = "Apa persyaratan perizinan untuk KBLI {$kbliCode} ({$description})?";
        
        return $this->query($question, [
            'kbli_code' => $kbliCode,
        ], [
            'document_type' => 'regulation',
            'regulation_type' => 'business_permit',
        ]);
    }
}
```

#### 1.2 Add Configuration
**File:** `config/services.php`

```php
'perizinan_ai' => [
    'url' => env('PERIZINAN_AI_URL', 'https://api.bizmark.id'),
    'username' => env('PERIZINAN_AI_USERNAME'),
    'password' => env('PERIZINAN_AI_PASSWORD'),
    'timeout' => env('PERIZINAN_AI_TIMEOUT', 30),
],
```

**File:** `.env`

```env
PERIZINAN_AI_URL=https://api.bizmark.id
PERIZINAN_AI_USERNAME=bizmark_user
PERIZINAN_AI_PASSWORD=secure_password_here
PERIZINAN_AI_TIMEOUT=30
```

#### 1.3 Enhance ConsultationController
**File:** `app/Http/Controllers/Api/ConsultationController.php`

```php
use App\Services\PerizinanAIService;

class ConsultationController extends Controller
{
    private PerizinanAIService $ragService;
    
    public function __construct(PerizinanAIService $ragService)
    {
        $this->ragService = $ragService;
    }
    
    public function estimate(Request $request)
    {
        // ... existing validation ...
        
        // NEW: Get RAG context for better estimation
        try {
            $ragContext = $this->ragService->getBusinessTypeRegulations(
                $validated['business_type'],
                $this->getLocationName($validated['location_type'])
            );
            
            // Store RAG insights
            $validated['rag_insights'] = [
                'answer' => $ragContext['answer'],
                'sources' => array_map(fn($s) => [
                    'title' => $s['title'],
                    'section' => $s['section'],
                    'confidence' => $s['confidence_score'],
                ], $ragContext['sources'] ?? []),
                'confidence' => $ragContext['confidence_score'],
            ];
            
            Log::info('RAG context retrieved', [
                'sources_count' => count($ragContext['sources'] ?? []),
                'confidence' => $ragContext['confidence_score'],
            ]);
        } catch (\Exception $e) {
            // Graceful degradation - continue without RAG if it fails
            Log::warning('RAG query failed, continuing with standard estimation', [
                'error' => $e->getMessage(),
            ]);
            
            $validated['rag_insights'] = null;
        }
        
        // Continue with existing pricing engine...
        $pricing = new ConsultationPricingEngine();
        $estimate = $pricing->calculate($validated);
        
        // Save to database with RAG insights
        $consultation = ConsultRequest::create([
            // ... existing fields ...
            'rag_insights' => json_encode($validated['rag_insights']),
            'rag_confidence' => $validated['rag_insights']['confidence'] ?? null,
        ]);
        
        return response()->json([
            'success' => true,
            'data' => [
                'estimate' => $estimate,
                'rag_context' => $validated['rag_insights'],
            ],
        ]);
    }
}
```

#### 1.4 Database Migration
**File:** `database/migrations/2025_12_04_add_rag_insights_to_consult_requests.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('consult_requests', function (Blueprint $table) {
            $table->jsonb('rag_insights')->nullable()->after('auto_estimate');
            $table->decimal('rag_confidence', 3, 2)->nullable()->after('rag_insights');
            $table->index('rag_confidence');
        });
    }

    public function down()
    {
        Schema::table('consult_requests', function (Blueprint $table) {
            $table->dropColumn(['rag_insights', 'rag_confidence']);
        });
    }
};
```

---

### Phase 2: Enhanced UI (Week 2)

#### 2.1 Display RAG Insights in Form Response
**File:** `resources/views/consultation/partials/result-modal.blade.php` (NEW)

```blade
<!-- RAG Context Section -->
@if(isset($result['rag_context']) && $result['rag_context'])
<div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
    <div class="flex items-start">
        <i class="fas fa-book text-blue-600 dark:text-blue-400 mt-1 mr-3"></i>
        <div class="flex-1">
            <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">
                Konteks Regulasi
            </h4>
            <div class="text-sm text-blue-800 dark:text-blue-200 mb-3">
                {{ $result['rag_context']['answer'] }}
            </div>
            
            @if(count($result['rag_context']['sources']) > 0)
            <div class="mt-3">
                <p class="text-xs font-medium text-blue-700 dark:text-blue-300 mb-2">
                    Sumber Regulasi:
                </p>
                <ul class="space-y-1">
                    @foreach($result['rag_context']['sources'] as $source)
                    <li class="flex items-start text-xs">
                        <i class="fas fa-check-circle text-blue-500 mt-0.5 mr-2"></i>
                        <span class="text-blue-700 dark:text-blue-300">
                            {{ $source['title'] }}
                            @if($source['section'])
                                <span class="text-blue-600 dark:text-blue-400">
                                    ({{ $source['section'] }})
                                </span>
                            @endif
                            <span class="inline-block ml-1 px-1.5 py-0.5 bg-blue-100 dark:bg-blue-800 text-blue-700 dark:text-blue-200 rounded text-[10px]">
                                {{ number_format($source['confidence'] * 100, 0) }}% relevan
                            </span>
                        </span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <div class="mt-3 flex items-center text-xs text-blue-600 dark:text-blue-400">
                <i class="fas fa-info-circle mr-1"></i>
                <span>
                    Tingkat kepercayaan: 
                    <strong>{{ number_format($result['rag_context']['confidence'] * 100, 0) }}%</strong>
                </span>
            </div>
        </div>
    </div>
</div>
@endif
```

#### 2.2 Add Loading State for RAG
**File:** `resources/views/consultation/index.blade.php`

```blade
<!-- In Alpine.js data -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('consultationForm', () => ({
        loading: false,
        ragLoading: false,  // NEW
        
        async submitForm() {
            this.loading = true;
            this.ragLoading = true;  // NEW
            
            try {
                const response = await fetch('/api/consultation/estimate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.formData),
                });
                
                const result = await response.json();
                
                this.ragLoading = false;  // NEW
                
                if (result.success) {
                    this.showResult(result.data);
                }
            } catch (error) {
                this.ragLoading = false;  // NEW
                this.showError(error.message);
            } finally {
                this.loading = false;
            }
        },
    }));
});
</script>

<!-- Loading overlay with RAG status -->
<div x-show="loading" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-sm mx-4">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-center text-gray-700 dark:text-gray-300 mb-2">
            Menghitung estimasi biaya...
        </p>
        <div x-show="ragLoading" class="text-center text-sm text-gray-500 dark:text-gray-400">
            <i class="fas fa-book animate-pulse mr-1"></i>
            Menganalisis konteks regulasi...
        </div>
    </div>
</div>
```

---

### Phase 3: Admin Panel Enhancement (Week 3)

#### 3.1 Show RAG Insights in Lead Detail
**File:** `resources/views/admin/consultation-leads/show.blade.php`

```blade
<!-- RAG Insights Card -->
@if($consultation->rag_insights)
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4 flex items-center">
        <i class="fas fa-brain text-purple-600 mr-2"></i>
        AI Regulation Insights
    </h3>
    
    <div class="space-y-4">
        <!-- Confidence Score -->
        <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                Confidence Score
            </label>
            <div class="flex items-center mt-1">
                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-purple-600 h-2 rounded-full" 
                         style="width: {{ $consultation->rag_confidence * 100 }}%">
                    </div>
                </div>
                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ number_format($consultation->rag_confidence * 100, 0) }}%
                </span>
            </div>
        </div>
        
        <!-- AI Answer -->
        <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                Regulatory Context
            </label>
            <div class="mt-1 p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg text-sm">
                {{ $consultation->rag_insights['answer'] ?? 'N/A' }}
            </div>
        </div>
        
        <!-- Sources -->
        @if(isset($consultation->rag_insights['sources']) && count($consultation->rag_insights['sources']) > 0)
        <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                Source Documents ({{ count($consultation->rag_insights['sources']) }})
            </label>
            <ul class="mt-2 space-y-2">
                @foreach($consultation->rag_insights['sources'] as $source)
                <li class="flex items-start p-2 bg-gray-50 dark:bg-gray-700 rounded">
                    <i class="fas fa-file-alt text-purple-500 mt-1 mr-2"></i>
                    <div class="flex-1">
                        <div class="font-medium text-sm">{{ $source['title'] }}</div>
                        @if($source['section'])
                        <div class="text-xs text-gray-600 dark:text-gray-400">
                            Section: {{ $source['section'] }}
                        </div>
                        @endif
                        <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                            Relevance: {{ number_format($source['confidence'] * 100, 0) }}%
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>
@endif
```

#### 3.2 RAG Quality Metrics Dashboard
**File:** `app/Http/Controllers/Admin/DashboardController.php`

```php
public function index()
{
    $ragMetrics = ConsultRequest::whereNotNull('rag_insights')
        ->selectRaw('
            COUNT(*) as total_with_rag,
            AVG(rag_confidence) as avg_confidence,
            COUNT(CASE WHEN rag_confidence >= 0.8 THEN 1 END) as high_confidence,
            COUNT(CASE WHEN rag_confidence < 0.6 THEN 1 END) as low_confidence
        ')
        ->first();
    
    return view('admin.dashboard', [
        'ragMetrics' => $ragMetrics,
        // ... other data ...
    ]);
}
```

---

### Phase 4: Advanced Features (Week 4)

#### 4.1 KBLI-Specific RAG Queries
**Enhancement:** Query RAG for each selected business activity

```php
// In ConsultationController
foreach ($validated['business_activities'] as $activity) {
    try {
        $kbliContext = $this->ragService->getKBLIRequirements(
            $activity['kbli_code'],
            $activity['description']
        );
        
        $activity['rag_requirements'] = $kbliContext['answer'];
        $activity['rag_sources'] = array_slice($kbliContext['sources'], 0, 3);
    } catch (\Exception $e) {
        Log::warning("Failed to get RAG context for KBLI {$activity['kbli_code']}");
    }
}
```

#### 4.2 Real-time Regulation Updates
**New Feature:** Subscribe to regulation changes from Perizinan AI

```php
// app/Console/Commands/SyncRegulations.php
class SyncRegulations extends Command
{
    protected $signature = 'rag:sync-regulations';
    
    public function handle(PerizinanAIService $ragService)
    {
        $latestRegulations = Http::withToken($ragService->getAccessToken())
            ->get('https://api.bizmark.id/api/documents', [
                'type' => 'regulation',
                'updated_after' => now()->subDay()->toIso8601String(),
            ])
            ->json();
        
        foreach ($latestRegulations['data'] as $regulation) {
            // Update local cache or notify admins
            $this->info("New regulation: {$regulation['title']}");
        }
    }
}
```

#### 4.3 A/B Testing RAG Impact
**Track conversion rates with vs without RAG context**

```php
// Add tracking column
Schema::table('consult_requests', function (Blueprint $table) {
    $table->boolean('converted_to_client')->default(false);
    $table->timestamp('converted_at')->nullable();
});

// Analytics query
$ragImpact = DB::table('consult_requests')
    ->selectRaw('
        CASE WHEN rag_insights IS NOT NULL THEN "With RAG" ELSE "Without RAG" END as group,
        COUNT(*) as total,
        SUM(CASE WHEN converted_to_client THEN 1 ELSE 0 END) as converted,
        AVG(CASE WHEN converted_to_client THEN 1 ELSE 0 END) * 100 as conversion_rate
    ')
    ->groupBy('group')
    ->get();
```

---

## Technical Specifications

### API Rate Limiting
- Perizinan AI: 100 requests/hour per IP
- Strategy: Cache responses by business_type + location for 1 hour
- Fallback: Continue without RAG if limit exceeded

### Performance Targets
- RAG query: < 2 seconds
- Total form submission: < 5 seconds
- Fallback gracefully if RAG fails

### Error Handling
```php
try {
    $ragContext = $this->ragService->query($question);
} catch (RateLimitException $e) {
    Log::warning('RAG rate limit exceeded');
    return $this->estimateWithoutRAG($data);
} catch (TimeoutException $e) {
    Log::error('RAG timeout');
    return $this->estimateWithoutRAG($data);
} catch (\Exception $e) {
    Log::error('RAG error: ' . $e->getMessage());
    return $this->estimateWithoutRAG($data);
}
```

### Monitoring
- Track RAG success/failure rate
- Monitor response times
- Alert on confidence score drops
- Log source citations for audit

---

## Implementation Checklist

### Phase 1: Foundation (Week 1)
- [ ] Create `PerizinanAIService.php`
- [ ] Add config to `services.php`
- [ ] Set environment variables
- [ ] Create JWT authentication flow
- [ ] Add rate limiting middleware
- [ ] Implement token caching
- [ ] Create database migration
- [ ] Update `ConsultationController`
- [ ] Add error handling
- [ ] Write unit tests

### Phase 2: UI Enhancement (Week 2)
- [ ] Create result modal partial
- [ ] Add RAG loading states
- [ ] Style regulation sources display
- [ ] Add confidence indicators
- [ ] Mobile responsive design
- [ ] Dark mode support
- [ ] Add tooltips for regulations
- [ ] User testing

### Phase 3: Admin Features (Week 3)
- [ ] Update admin lead detail view
- [ ] Create RAG metrics dashboard
- [ ] Add source document links
- [ ] Implement quality filtering
- [ ] Export with RAG insights
- [ ] Admin notifications for low confidence
- [ ] Bulk re-process with RAG

### Phase 4: Advanced Features (Week 4)
- [ ] KBLI-specific queries
- [ ] Regulation sync command
- [ ] A/B testing implementation
- [ ] Performance optimization
- [ ] Cache warming
- [ ] Analytics dashboard
- [ ] Documentation
- [ ] Training materials

---

## Success Metrics

### KPIs to Track
1. **RAG Adoption Rate**: % of estimates using RAG
2. **Confidence Score Distribution**: Average and median
3. **Lead Quality**: Conversion rate with vs without RAG
4. **User Satisfaction**: Feedback on regulation insights
5. **Performance**: P95 response time
6. **Reliability**: RAG success rate

### Target Goals
- RAG success rate: > 95%
- Average confidence: > 0.75
- Response time: < 3 seconds
- Conversion uplift: +15%
- User satisfaction: > 4.5/5

---

## Risk Mitigation

### Technical Risks
1. **API Downtime**: Implement graceful degradation
2. **Rate Limits**: Aggressive caching strategy
3. **Slow Responses**: Async processing option
4. **Data Quality**: Validation & confidence thresholds

### Business Risks
1. **Outdated Regulations**: Daily sync schedule
2. **Incorrect Citations**: Human review for high-value leads
3. **User Confusion**: Clear UI labeling
4. **Privacy**: No PII sent to RAG

---

## Next Steps

1. **Immediate (Today):**
   - Review this plan with team
   - Set up API credentials
   - Test connection to api.bizmark.id
   
2. **This Week:**
   - Implement Phase 1
   - Create test environment
   - Initial UAT
   
3. **Next Week:**
   - Deploy to production
   - Monitor metrics
   - Gather feedback

---

**Prepared by:** AI Assistant  
**Review Required:** Development Team  
**Timeline:** 4 weeks (can be compressed to 2 weeks if urgent)
