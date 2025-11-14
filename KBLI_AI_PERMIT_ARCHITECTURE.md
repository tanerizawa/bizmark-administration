# KBLI-Based AI Permit System Architecture

## 🎯 Objective
Transform the permit catalog from manual permit types to an intelligent KBLI-based system that uses AI to generate required permits and documents automatically.

## 📊 Current Architecture

### Existing System:
```
Client → Browse Permit Types (Manual) → Select → Fill Form → Submit
```

**Problems:**
- Manual permit type management
- Static document requirements
- Limited to predefined permit types
- No industry-specific recommendations

## 🚀 New Architecture

### KBLI-Based AI System:
```
Client → Select KBLI → AI Analysis → Generated Permits & Docs → Review → Submit
```

### Flow Diagram:
```
┌─────────────────────────────────────────────────────────────┐
│                    CLIENT INTERFACE                          │
├─────────────────────────────────────────────────────────────┤
│  1. KBLI Selection (Autocomplete from our database)         │
│     ↓                                                        │
│  2. Business Profile Input (optional context)               │
│     ↓                                                        │
│  3. AI Processing (OpenRouter API)                          │
│     ↓                                                        │
│  4. Display Results:                                        │
│     - Required Permits List                                 │
│     - Document Requirements per Permit                      │
│     - Estimated Processing Time                             │
│     - Risk Level Assessment                                 │
│     ↓                                                        │
│  5. Client Reviews & Customizes                             │
│     ↓                                                        │
│  6. Submit Application(s)                                   │
└─────────────────────────────────────────────────────────────┘
```

## 🗄️ Database Schema Updates

### New Tables:

#### 1. `kbli_permit_recommendations` (Caching AI Results)
```sql
CREATE TABLE kbli_permit_recommendations (
    id BIGSERIAL PRIMARY KEY,
    kbli_code VARCHAR(10) NOT NULL REFERENCES kbli(code),
    business_scale VARCHAR(50), -- micro, small, medium, large
    location_type VARCHAR(50), -- urban, rural, industrial_zone
    
    -- AI Generated Data
    recommended_permits JSONB NOT NULL, -- Array of permit objects
    required_documents JSONB NOT NULL,  -- Documents per permit
    risk_assessment JSONB,              -- Risk level & considerations
    estimated_timeline JSONB,           -- Processing time per permit
    additional_notes TEXT,
    
    -- AI Metadata
    ai_model VARCHAR(100),
    ai_prompt_hash VARCHAR(64), -- To track if prompt changed
    confidence_score DECIMAL(3,2),
    
    -- Caching
    cache_hits INTEGER DEFAULT 0,
    last_used_at TIMESTAMP,
    expires_at TIMESTAMP,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE(kbli_code, business_scale, location_type)
);

CREATE INDEX idx_kbli_recommendations_code ON kbli_permit_recommendations(kbli_code);
CREATE INDEX idx_kbli_recommendations_expires ON kbli_permit_recommendations(expires_at);
```

#### 2. `ai_query_logs` (Analytics & Debugging)
```sql
CREATE TABLE ai_query_logs (
    id BIGSERIAL PRIMARY KEY,
    client_id BIGINT REFERENCES clients(id),
    kbli_code VARCHAR(10),
    business_context JSONB,
    
    -- Request/Response
    prompt_text TEXT,
    response_text TEXT,
    tokens_used INTEGER,
    response_time_ms INTEGER,
    
    -- Status
    status VARCHAR(20), -- success, error, timeout
    error_message TEXT,
    
    -- Metadata
    ai_model VARCHAR(100),
    api_cost DECIMAL(10,6),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_ai_logs_client ON ai_query_logs(client_id);
CREATE INDEX idx_ai_logs_kbli ON ai_query_logs(kbli_code);
CREATE INDEX idx_ai_logs_created ON ai_query_logs(created_at);
```

#### 3. Update `permit_applications` table
```sql
ALTER TABLE permit_applications ADD COLUMN IF NOT EXISTS ai_recommendation_id BIGINT REFERENCES kbli_permit_recommendations(id);
ALTER TABLE permit_applications ADD COLUMN IF NOT EXISTS business_context JSONB;
```

## 🤖 AI Integration

### OpenRouter API Configuration

#### Model Selection:
```php
// Recommended models (sorted by cost-effectiveness):
'models' => [
    'primary' => 'anthropic/claude-3.5-sonnet',     // Best quality
    'fallback' => 'google/gemini-pro-1.5',          // Cost-effective
    'budget' => 'meta-llama/llama-3.1-70b-instruct' // Budget option
]
```

#### Prompt Engineering:

```
System Role: You are an expert in Indonesian business licensing and regulations, 
specializing in matching KBLI codes to required permits.

Context:
- KBLI Code: {code}
- Business Activity: {description}
- Business Sector: {sector}
- Business Scale: {scale}
- Location Type: {location}

Task: Generate a comprehensive permit and document requirements analysis.

Required Output Format (JSON):
{
  "permits": [
    {
      "name": "string",
      "type": "mandatory|recommended|optional",
      "issuing_authority": "string",
      "estimated_cost_range": {"min": number, "max": number},
      "estimated_days": number,
      "priority": number (1-5),
      "description": "string",
      "legal_basis": "string",
      "prerequisites": ["string"]
    }
  ],
  "documents": [
    {
      "name": "string",
      "type": "identity|company|technical|financial|other",
      "required_for_permits": ["permit_names"],
      "format": "string",
      "notes": "string",
      "sample_available": boolean
    }
  ],
  "risk_assessment": {
    "level": "low|medium|high",
    "factors": ["string"],
    "mitigation": ["string"]
  },
  "timeline": {
    "minimum_days": number,
    "maximum_days": number,
    "critical_path": ["step descriptions"]
  },
  "additional_considerations": ["string"],
  "regional_variations": "string"
}

Rules:
1. Focus on Indonesian regulations (OSS, BKPM, sectoral ministries)
2. Consider business scale impact on requirements
3. Include both national and potential regional permits
4. Prioritize mandatory permits
5. Be specific with document requirements
6. Provide realistic cost and time estimates
```

### Caching Strategy:

```php
class KbliPermitCacheService
{
    // Cache TTL: 30 days for static KBLI
    const CACHE_TTL = 2592000;
    
    // Invalidation triggers:
    // 1. Manual admin invalidation
    // 2. Regulation updates
    // 3. Low confidence score (<0.7)
    // 4. Cache older than 90 days with low hit rate
    
    public function getCacheKey($kbli, $scale, $location) {
        return "kbli_permit:{$kbli}:{$scale}:{$location}";
    }
    
    public function shouldRefresh($recommendation) {
        return $recommendation->confidence_score < 0.7 ||
               $recommendation->cache_hits < 5 &&
               $recommendation->created_at < now()->subDays(90);
    }
}
```

## 🎨 User Interface Flow

### Page 1: KBLI Selection
```
┌─────────────────────────────────────────────┐
│  Katalog Layanan Perizinan Berbasis KBLI   │
├─────────────────────────────────────────────┤
│                                             │
│  [🔍 Cari KBLI Anda...]                    │
│  Contoh: "01111" atau "Pertanian Padi"     │
│                                             │
│  💡 Tips: Pilih KBLI yang sesuai dengan    │
│     bidang usaha utama Anda                │
│                                             │
│  Atau pilih berdasarkan sektor:            │
│  [Pertanian] [Manufaktur] [Perdagangan]    │
│  [Jasa] [Konstruksi] [Lainnya]             │
└─────────────────────────────────────────────┘
```

### Page 2: Business Context (Optional)
```
┌─────────────────────────────────────────────┐
│  Informasi Tambahan (Opsional)             │
├─────────────────────────────────────────────┤
│                                             │
│  Skala Usaha:                              │
│  ○ Mikro  ○ Kecil  ○ Menengah  ○ Besar    │
│                                             │
│  Lokasi Usaha:                             │
│  ○ Perkotaan  ○ Perdesaan  ○ Kawasan       │
│                                             │
│  Informasi ini membantu kami memberikan     │
│  rekomendasi yang lebih akurat              │
│                                             │
│  [Lewati] [Lanjutkan dengan Rekomendasi]   │
└─────────────────────────────────────────────┘
```

### Page 3: AI Processing
```
┌─────────────────────────────────────────────┐
│  🤖 Menganalisis Kebutuhan Perizinan...    │
├─────────────────────────────────────────────┤
│                                             │
│     ⚡ Memproses KBLI Anda                 │
│     📋 Mengidentifikasi izin wajib         │
│     📄 Menyiapkan daftar dokumen           │
│     ⏱️  Mengestimasi waktu proses          │
│                                             │
│  [████████████░░░] 85%                     │
└─────────────────────────────────────────────┘
```

### Page 4: Results Display
```
┌─────────────────────────────────────────────────────────────┐
│  Rekomendasi Perizinan untuk:                               │
│  KBLI 01111 - Pertanian Padi & Serealia Lainnya           │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  📊 Ringkasan                                              │
│  • 5 Izin Wajib                                            │
│  • 2 Izin Rekomendasi                                      │
│  • 12 Dokumen Dibutuhkan                                   │
│  • Estimasi Waktu: 45-60 hari kerja                        │
│  • Estimasi Biaya: Rp 5.000.000 - Rp 15.000.000           │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│  ⚠️ IZIN WAJIB                                             │
│                                                             │
│  1. [🔴 Priority 1] NIB (Nomor Induk Berusaha)            │
│     Instansi: OSS (Online Single Submission)               │
│     Waktu: 1-3 hari | Biaya: Gratis                       │
│     Dokumen: KTP, NPWP, Akta Pendirian                     │
│     [Lihat Detail] [Ajukan Sekarang]                       │
│                                                             │
│  2. [🔴 Priority 1] Izin Usaha Pertanian                  │
│     Instansi: Dinas Pertanian Kab/Kota                    │
│     Waktu: 7-14 hari | Biaya: Rp 500.000 - Rp 2.000.000  │
│     Dokumen: NIB, Surat Tanah, Kajian Lingkungan          │
│     [Lihat Detail] [Ajukan Sekarang]                       │
│                                                             │
│  [...more permits...]                                       │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│  💡 IZIN REKOMENDASI                                       │
│  [Collapsed by default - Click to expand]                  │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│  📋 DOKUMEN YANG DIBUTUHKAN                                │
│                                                             │
│  Identitas & Legal:                                        │
│  ✓ KTP Direktur                                            │
│  ✓ NPWP Perusahaan                                         │
│  ✓ Akta Pendirian & SK Kemenkumham                         │
│                                                             │
│  Teknis:                                                   │
│  ✓ Surat Kepemilikan/Sewa Lahan                           │
│  ✓ Peta Lokasi Usaha                                       │
│  ✓ Dokumen Lingkungan (AMDAL/UKL-UPL)                     │
│                                                             │
│  [Download Checklist] [Lihat Contoh Dokumen]              │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│  ⏱️ TIMELINE PROSES                                        │
│                                                             │
│  [Gantt Chart Visualization]                                │
│  Week 1-2: NIB + Dokumen Dasar                             │
│  Week 3-6: Izin Usaha Pertanian                            │
│  Week 7-8: Izin Lingkungan                                 │
│  ...                                                        │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│  [📥 Download Full Report PDF]                             │
│  [💬 Konsultasi dengan Tim]                               │
│  [✅ Mulai Proses Permohonan]                              │
└─────────────────────────────────────────────────────────────┘
```

## 🔧 Implementation Plan

### Phase 1: Database & Models (Day 1-2)
- [ ] Create migrations for new tables
- [ ] Create Eloquent models
- [ ] Set up relationships

### Phase 2: AI Service Layer (Day 3-5)
- [ ] OpenRouter API integration service
- [ ] Prompt engineering & testing
- [ ] Response parsing & validation
- [ ] Caching mechanism
- [ ] Error handling & fallbacks

### Phase 3: Backend Logic (Day 6-8)
- [ ] KBLI search API endpoint
- [ ] AI recommendation controller
- [ ] Cache management service
- [ ] Analytics & logging
- [ ] Admin tools for cache management

### Phase 4: Frontend (Day 9-12)
- [ ] KBLI selection interface
- [ ] Business context form
- [ ] Loading states & animations
- [ ] Results display page
- [ ] Interactive permit cards
- [ ] Document checklist
- [ ] Timeline visualization
- [ ] PDF export functionality

### Phase 5: Integration & Testing (Day 13-15)
- [ ] Connect to existing application flow
- [ ] End-to-end testing
- [ ] Performance optimization
- [ ] Cost monitoring
- [ ] User acceptance testing

## 💰 Cost Estimation

### OpenRouter API Costs:
- Claude 3.5 Sonnet: ~$0.015 per request (4K tokens avg)
- Gemini Pro 1.5: ~$0.005 per request
- With caching: 1 AI call serves 100+ users = $0.00015 per user

### Monthly Operating Cost (1000 queries):
- Without cache: $15 - $50
- With 95% cache hit rate: $0.75 - $2.50

## 🎯 Success Metrics

1. **User Experience:**
   - Permit discovery time: < 2 minutes
   - Recommendation accuracy: > 90%
   - User satisfaction: > 4.5/5

2. **System Performance:**
   - Cache hit rate: > 95%
   - API response time: < 3 seconds
   - AI response time: < 10 seconds

3. **Business Value:**
   - Permit application completion rate: +30%
   - Support ticket reduction: -40%
   - Client satisfaction increase: +25%

## 🔐 Security & Compliance

- API key management (Laravel secrets)
- Rate limiting (10 AI requests per user per hour)
- Input validation & sanitization
- Audit logging for all AI queries
- GDPR-compliant data handling
- Regular prompt injection testing

## 🚀 Future Enhancements

1. **Multi-language support** (English, Chinese)
2. **Export location** integration for export permits
3. **Industry-specific templates** (F&B, Manufacturing, etc.)
4. **Permit status tracking** integration with OSS
5. **Document OCR** for auto-filling requirements
6. **Chatbot assistant** for permit questions
7. **Permit calendar** with deadline reminders

---

## 📝 Next Steps

1. Review and approve architecture
2. Set up OpenRouter API account
3. Start Phase 1 implementation
4. Weekly progress reviews

**Estimated Total Implementation Time:** 15-20 working days
**Estimated Total Cost:** $50-100 (API costs for development + first month)
