# Sistem Konsultasi Gratis dengan KBLI 5-Digit & AI OpenRouter

## 📋 Overview

Sistem konsultasi gratis yang **komprehensif** dan **akurat** menggunakan:
- **KBLI 5-digit** (Kelompok Kegiatan - klasifikasi paling spesifik)
- **OpenRouter AI** (Claude 3.5 Sonnet / Gemini Pro 1.5) untuk analisis real-time
- **Multiple input variables** untuk estimasi biaya yang mendekati real
- **Separation of concerns**: Biaya Pokok vs Biaya Jasa

## 🎯 Fitur Utama

### 1. KBLI 5-Digit System
- **1,789 kode KBLI 5-digit** tersedia di database
- **12 kode top business** sudah di-seed dengan pricing template
- Search/autocomplete **hanya 5-digit codes**
- Auto-ranking berdasarkan `usage_count`

### 2. AI-Powered Cost Estimation
**ConsultationPricingEngine** dengan OpenRouter AI:

```php
// Input Variables
[
    'kbli_code' => '41011',              // 5-digit KBLI (WAJIB)
    'business_size' => 'small',          // micro|small|medium|large
    'location' => 'Jakarta',             // Province/city name
    'location_type' => 'commercial',     // industrial|commercial|residential|rural
    'investment_level' => '100m_500m',   // under_100m|100m_500m|500m_2b|over_2b
    'employee_count' => 25,              // Number of employees
    'project_description' => '...',      // Detailed description
    'deliverables_requested' => []       // Array of services
]

// Output
[
    'cost_breakdown' => [
        'biaya_pokok' => [
            'printing' => 300000,
            'permits' => 1000000,
            'lab_tests' => 2500000,
            'field_equipment' => 500000,
            'total' => 4300000
        ],
        'biaya_jasa' => [
            'admin' => ['hours' => 4, 'rate' => 125000, 'cost' => 500000],
            'technical' => ['hours' => 24, 'rate' => 250000, 'cost' => 6000000],
            'review' => ['hours' => 8, 'rate' => 200000, 'cost' => 1600000],
            'field' => ['hours' => 16, 'rate' => 200000, 'cost' => 3200000],
            'total' => 11300000
        ],
        'overhead' => ['percentage' => 10, 'amount' => 1560000]
    ],
    'cost_summary' => [
        'subtotal' => 15600000,
        'grand_total' => 17160000,
        'cost_range' => ['min' => 14586000, 'max' => 19734000],
        'formatted' => [
            'grand_total' => 'Rp 17.160.000',
            'range' => 'Rp 14.586.000 - Rp 19.734.000'
        ]
    ],
    'ai_analysis' => [
        'permits' => [...],        // Detailed permit list from AI
        'documents' => [...],      // Required documents
        'risk_assessment' => [...], // Risk level & factors
        'timeline' => [...],       // Estimated timeline
        'cost_adjustment' => [...]  // AI-based adjustment
    ],
    'confidence_score' => 0.85
]
```

### 3. Multiplier System

**Business Size Multipliers:**
- Micro: 1.0x (base)
- Small: 1.3x
- Medium: 1.8x
- Large: 2.5x

**Location Type Multipliers:**
- Rural: 0.8x (simplest)
- Residential: 0.9x
- Commercial: 1.0x (standard)
- Industrial: 1.2x (most complex)

**AI Adjustment Factors:**
- Many mandatory permits (>8): +20%
- Multiple permits (5-8): +10%
- High complexity business: +15%

### 4. Biaya Pokok vs Biaya Jasa

**Biaya Pokok (Direct Costs):**
- Printing & documentation
- Government permits (PNBP)
- Lab tests & assessments
- Field equipment & surveys

**Biaya Jasa (Service Fees):**
- Admin hours × Admin rate
- Technical hours × Technical rate
- Review hours × Review rate
- Field hours × Field rate

**Overhead:** 10% of subtotal (admin + project management)

## 🗄️ Database Structure

### Table: `kbli`
```sql
-- Existing fields
id, code, description, sector, notes, created_at, updated_at

-- New pricing fields (added via migration)
category VARCHAR
activities TEXT
examples TEXT
complexity_level ENUM('low','medium','high')
default_direct_costs JSONB
default_hours_estimate JSONB
default_hourly_rates JSONB
regulatory_flags JSONB
recommended_services JSONB
is_active BOOLEAN
usage_count INTEGER
deleted_at TIMESTAMP

-- Full-text search index (GIN)
CREATE INDEX idx_kbli_search ON kbli USING GIN(...)
```

### Table: `consult_requests`
```sql
-- Contact info
name, email, phone, company_name

-- Business info
kbli_code (FK to kbli.code)
business_size ENUM
location VARCHAR
location_type ENUM
investment_level ENUM
employee_count INTEGER

-- Project details
project_description TEXT
deliverables_requested JSONB

-- Estimate data
estimate_status ENUM
auto_estimate JSONB          -- Full AI estimate
final_quote JSONB            -- Admin-adjusted
confidence_score DECIMAL(3,2)

-- Admin review
admin_notes TEXT
reviewed_by (FK to users.id)
reviewed_at TIMESTAMP

-- Follow-up
contacted BOOLEAN
contacted_at TIMESTAMP
converted_to_client BOOLEAN
client_id (FK to clients.id)

-- Tracking
ip_address, user_agent, referrer_url, utm_params JSONB
created_at, updated_at, deleted_at
```

## 🔧 Services Architecture

### 1. OpenRouterService
**File:** `app/Services/OpenRouterService.php`

**Purpose:** AI permit analysis using Claude/Gemini

**Key Methods:**
```php
generatePermitRecommendations(
    $kbliCode,
    $kbliDescription,
    $sector,
    $businessScale,
    $locationType,
    $clientId
)
```

**Returns:**
- Recommended permits (mandatory/recommended/conditional)
- Required documents
- Risk assessment
- Timeline estimation
- Cost breakdown (government fees + consultant fees)

### 2. ConsultationPricingEngine
**File:** `app/Services/ConsultationPricingEngine.php`

**Purpose:** Comprehensive cost calculation with AI enhancement

**Key Methods:**
```php
calculateEstimate(array $params): array
```

**Process Flow:**
1. Validate input (5-digit KBLI required)
2. Get base estimate from KBLI template
3. Apply business size multiplier
4. Apply location type multiplier
5. Call OpenRouter AI for permit analysis
6. Apply AI cost adjustment
7. Calculate final estimate with range
8. Return comprehensive breakdown

### 3. Kbli Model
**File:** `app/Models/Kbli.php`

**Key Methods:**
```php
// Search only 5-digit codes
Kbli::search('konstruksi', 20)

// Find by code (5-digit preferred)
Kbli::findByCode('41011')

// Get popular codes (usage-based)
Kbli::getPopular(10)

// Increment usage counter
$kbli->incrementUsage()
```

### 4. ConsultRequest Model
**File:** `app/Models/ConsultRequest.php`

**Key Features:**
- Store all consultation requests
- Auto-estimate via AI
- Admin review workflow
- Conversion tracking
- UTM tracking

**Scopes:**
```php
ConsultRequest::pending()        // Need review
ConsultRequest::notContacted()   // Follow-up needed
ConsultRequest::highPotential()  // High confidence + large business
```

## 📊 KBLI 5-Digit Codes (Seeded)

### Construction (41xxx)
- **41011**: Konstruksi Gedung Hunian
- **41012**: Konstruksi Gedung Perkantoran
- **41013**: Konstruksi Gedung Industri

### Food Industry (10xxx)
- **10110**: Rumah Potong Dan Pengepakan Daging

### Trade (46xxx)
- **46100**: Perdagangan Besar Atas Dasar Balas Jasa

### Food & Beverage (56xxx)
- **56101**: Restoran
- **56102**: Rumah/Warung Makan

### IT & Software (62xxx)
- **62011**: Pengembangan Video Game
- **62012**: Pengembangan E-Commerce

### Professional Services (71xxx)
- **71101**: Aktivitas Arsitektur
- **71102**: Keinsinyuran dan Konsultasi Teknis

### Real Estate (68xxx)
- **68111**: Real Estat Residensial

## 🔌 API Endpoints (Next Steps)

### 1. KBLI Autocomplete
```http
GET /api/kbli/search?q=konstruksi&limit=20

Response:
{
  "success": true,
  "data": [
    {
      "code": "41011",
      "description": "Konstruksi Gedung Hunian",
      "category": "Konstruksi Gedung Hunian",
      "usage_count": 15
    }
  ]
}
```

### 2. Free Consultation Submission
```http
POST /api/consultation/submit

Body:
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "08123456789",
  "company_name": "PT Example",
  "kbli_code": "41011",
  "business_size": "small",
  "location": "Jakarta",
  "location_type": "commercial",
  "investment_level": "100m_500m",
  "employee_count": 25,
  "project_description": "...",
  "deliverables_requested": ["ukl_upl", "imb"]
}

Response:
{
  "success": true,
  "request_id": 123,
  "estimate": {
    "cost_summary": {...},
    "ai_analysis": {...},
    "confidence_score": 0.85
  }
}
```

## 🎨 Frontend Form (Next Steps)

### Required Form Fields:

**1. Contact Information:**
- Name (required)
- Email (required)
- Phone (required)
- Company Name (optional)

**2. Business Information:**
- **KBLI Code** (autocomplete, 5-digit only, required)
  - Search as-you-type
  - Show: code + description
  - Filter: only 5-digit codes
  - Sort: by usage_count DESC
  
- **Business Size** (radio, required)
  - Micro (< 10 employees)
  - Small (10-50 employees)
  - Medium (50-100 employees)
  - Large (> 100 employees)
  
- **Location** (text, required)
  - Province/city input
  
- **Location Type** (select, required)
  - Industrial area
  - Commercial area
  - Residential area
  - Rural area
  
- **Investment Level** (select, required)
  - < Rp 100 million
  - Rp 100 - 500 million
  - Rp 500 million - 2 billion
  - > Rp 2 billion
  
- **Employee Count** (number, optional)

**3. Project Details:**
- Project Description (textarea, required)
- Deliverables Requested (checkboxes, optional)

**4. Cost Estimate Preview:**
- Real-time estimate update on KBLI selection
- Show cost range
- Show complexity level
- Show confidence score

## ⚙️ Configuration

### Environment Variables:
```env
# OpenRouter AI
OPENROUTER_API_KEY=sk-or-v1-...
OPENROUTER_BASE_URL=https://openrouter.ai/api/v1
OPENROUTER_MODEL=anthropic/claude-3.5-sonnet
OPENROUTER_FALLBACK_MODEL=google/gemini-pro-1.5
OPENROUTER_TIMEOUT=60
OPENROUTER_MAX_TOKENS=4000
```

### Service Provider:
```php
// config/services.php
'openrouter' => [
    'api_key' => env('OPENROUTER_API_KEY'),
    'base_url' => env('OPENROUTER_BASE_URL'),
    'model' => env('OPENROUTER_MODEL'),
    'fallback_model' => env('OPENROUTER_FALLBACK_MODEL'),
],
```

## 🧪 Testing

### Test KBLI Search (5-digit only):
```php
php artisan tinker

// Search for construction-related codes
$results = App\Models\Kbli::search('konstruksi', 10);
foreach($results as $r) {
    echo "{$r->code} - {$r->description}\n";
}

// Output:
// 41011 - Konstruksi Gedung Hunian
// 41012 - Konstruksi Gedung Perkantoran
// 41013 - Konstruksi Gedung Industri
```

### Test Pricing Engine:
```php
php artisan tinker

$engine = app(App\Services\ConsultationPricingEngine::class);

$estimate = $engine->calculateEstimate([
    'kbli_code' => '41011',
    'business_size' => 'small',
    'location' => 'Jakarta',
    'location_type' => 'commercial',
    'investment_level' => '100m_500m',
    'employee_count' => 25,
    'project_description' => 'Pembangunan 10 unit rumah 2 lantai',
    'deliverables_requested' => ['ukl_upl', 'imb', 'slf'],
]);

print_r($estimate['cost_summary']);
```

### Test ConsultRequest Creation:
```php
php artisan tinker

$request = App\Models\ConsultRequest::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'phone' => '08123456789',
    'kbli_code' => '41011',
    'business_size' => 'small',
    'location' => 'Jakarta',
    'location_type' => 'commercial',
    'investment_level' => '100m_500m',
    'employee_count' => 25,
    'project_description' => 'Test project',
    'auto_estimate' => $estimate,
    'confidence_score' => 0.85,
]);

echo "Request created with ID: {$request->id}\n";
echo "Estimated cost: {$request->formatted_cost_range}\n";
```

## 📈 Next Steps

### Phase 1 (Backend API):
- [x] ~~KBLI 5-digit data seeding~~
- [x] ~~ConsultationPricingEngine with OpenRouter~~
- [x] ~~ConsultRequest model & migration~~
- [ ] **API endpoint: KBLI autocomplete**
- [ ] **API endpoint: Consultation submission**
- [ ] **Rate limiting & validation**

### Phase 2 (Frontend):
- [ ] **Update konsultasi-gratis form**
  - Add KBLI autocomplete component
  - Add business size radios
  - Add location & type inputs
  - Add investment level select
  - Add real-time estimate preview
- [ ] **Form validation (client-side)**
- [ ] **Loading states & error handling**

### Phase 3 (Admin Panel):
- [ ] **Consultation requests dashboard**
- [ ] **Review & adjust estimates**
- [ ] **Contact tracking**
- [ ] **Conversion to client workflow**
- [ ] **Analytics & reporting**

### Phase 4 (Testing & Optimization):
- [ ] **Unit tests for PricingEngine**
- [ ] **Integration tests for API**
- [ ] **E2E tests for form flow**
- [ ] **Performance optimization**
- [ ] **AI prompt refinement**

## 💡 Key Differences from Previous System

### ❌ OLD System:
- Used 2-digit KBLI codes (too broad)
- Simple deterministic formula
- No AI enhancement
- Fixed pricing templates
- No business size consideration
- Basic multipliers only

### ✅ NEW System:
- **5-digit KBLI codes** (most specific)
- **OpenRouter AI** for real analysis
- **Multiple input variables** (size, location, investment)
- **Dynamic pricing** with AI adjustments
- **Permit-based cost refinement**
- **Confidence scoring**
- **Comprehensive tracking** (UTM, conversion)

## 🚀 Production Checklist

- [x] Database migrations completed
- [x] Models created with relationships
- [x] Services implemented (OpenRouter + PricingEngine)
- [x] KBLI 5-digit data seeded
- [ ] API routes defined
- [ ] Controllers implemented
- [ ] Frontend form updated
- [ ] Rate limiting configured
- [ ] Email notifications set up
- [ ] Admin panel updated
- [ ] Testing completed
- [ ] Documentation updated
- [ ] Monitoring & logging configured

---

**Created:** November 27, 2025
**Version:** 1.0
**Status:** Phase 1 Backend Complete ✅
