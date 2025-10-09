 # ANALISIS BISNIS - KONSULTAN LINGKUNGAN

**Tanggal Analisis:** 2 Oktober 2025  
**Jenis Usaha:** Jasa Konsultan Lingkungan & Perizinan  
**Status Sistem:** Fase Implementasi Bertahap

---

## 1. PROFIL BISNIS

### Layanan Utama
- ✅ UKL-UPL (Upaya Kelola & Pemantauan Lingkungan)
- ✅ Perizinan Limbah B3 (Penyimpanan & Pemanfaatan)
- ✅ Kartu Pengawasan Kendaraan Limbah B3
- ✅ AMDAL & Sidang Lingkungan
- ✅ Uji Laboratorium (Udara & Air)
- ✅ Pembuatan Sistem Administrasi

### Karakteristik Workflow
1. **Multi-Stage Process:**
   - Survey & Data Collection
   - Uji Laboratorium (Udara, Air, Tanah)
   - Pembuatan Gambar/Site Plan
   - Penyusunan Proposal/Dokumen
   - Pertimbangan Teknis (Pertek)
   - Submission ke Instansi (BPN, KLHK, Dinas LH)
   - Approval & Revisi

2. **Strong Dependencies:**
   - PKKPR requires Site Plan completed
   - Proposal submission blocked by Pertek
   - UKL-UPL needs Lab results

3. **Timeline Critical:**
   - Deadline ketat dari klien & instansi
   - Multi-project concurrent (5-7 proyek paralel)
   - Deliverable dependencies

4. **External Dependencies:**
   - Vendor drafter untuk gambar teknik
   - Laboratorium untuk sampling & testing
   - Instansi pemerintah untuk approval
   - Klien untuk data & approval

---

## 2. PORTFOLIO AKTIF (Per 2 Oktober 2025)

### Summary Keuangan

| Metrik | Nilai | Persentase |
|--------|-------|------------|
| **Total Kontrak** | Rp 370.000.000 | 100% |
| **DP Diterima** | Rp 147.000.000 | 40% |
| **Piutang Outstanding** | Rp 223.000.000 | 60% |
| **Total Pengeluaran** | Rp 73.000.000 | 20% |
| **Saldo Tersedia** | Rp 74.000.000 | 20% |

### Detail Per Proyek

#### 🔴 PT ASIA CON - URGENT (Kontrak Terbesar)
- **Nilai Kontrak:** Rp 180.000.000
- **DP Diterima:** Rp 90.000.000 (50%)
- **Sisa Piutang:** Rp 90.000.000
- **Status:** In Progress - Multiple deadlines imminent
- **Jenis:** UKL-UPL
- **Timeline Kritis:**
  - ⚠️ **Pertimbangan Teknis BPN:** 4 Oktober 2025 (H-2)
  - ⚠️ **Gambar Site Plan:** 8 Oktober 2025 (H-6)
  - ⚠️ **Uji Lab Udara & Air:** 8 Oktober 2025 (H-6)
  - ⚠️ **PKKPR:** 15 Oktober 2025 (H-13)
  - ⚠️ **Penyusunan Proposal:** 21 Oktober 2025 (H-19)

#### 🟡 PT PUTRA JAYA LAKSANA - URGENT
- **Nilai Kontrak:** Rp 45.000.000
- **DP Diterima:** Rp 30.000.000 (67%)
- **Sisa Piutang:** Rp 15.000.000
- **Status:** Revisi Minor
- **Jenis:** UKL-UPL
- **Timeline:** Target selesai 4 Oktober 2025 (H-2)

#### 🟡 PT MAULIDA
- **Nilai Kontrak:** Rp 40.000.000
- **DP Diterima:** Rp 10.000.000 (25%)
- **Sisa Piutang:** Rp 30.000.000
- **Status:** In Progress
- **Jenis:** UKL-UPL
- **Timeline:**
  - Uji Lab Udara & Air: 8 Oktober 2025
  - Penyusunan Proposal: 11 Oktober 2025

#### 🔴 PT MEGA CORPORINDO MANDIRI - BLOCKED
- **DP Diterima:** Rp 15.000.000
- **Status:** **BLOCKED** - Proposal ready tapi waiting Pertek Limbah B3
- **Jenis:** UKL-UPL + Perizinan Limbah B3
- **Blocker:** Pertek Limbah B3 belum selesai
- **Note:** Sudah uji lab, proposal sudah, tapi tidak bisa submit

#### 🟢 NUSANTARA GRUP
- **Nilai Kontrak:** Rp 25.000.000
- **DP Diterima:** Rp 15.000.000 (60%)
- **Sisa Piutang:** Rp 10.000.000
- **Jenis:** Pembuatan Sistem Administrasi (proyek ini!)
- **Status:** In Development

#### 🟠 PT RAS - Multiple Sub-Projects
- **DP Diterima:** Rp 2.000.000
- **Status:** Proposal stage untuk multiple services
- **Services:**
  1. **Kartu Pengawasan Kendaraan Limbah**
     - Status: Kelengkapan dokumen (Buku Uji, STNK, MSDS)
     - Revisi foto simbol limbah
  2. **Penyimpanan Limbah B3**
     - Status: Butuh penawaran & proposal
  3. **Pemanfaatan Limbah B3**
     - Status: Butuh penawaran & proposal
  4. **UKL-UPL**
     - Status: Butuh penawaran & proposal

### Breakdown Pengeluaran (Rp 73.000.000)

| Kategori | Vendor | Jumlah | Proyek |
|----------|--------|--------|--------|
| Gambar Teknik | - | Rp 15.000.000 | PT MCM |
| Vendor | Big MAN | Rp 5.000.000 | - |
| Vendor | Mr. Gobs | Rp 14.000.000 | - |
| Sidang AMDAL | - | Rp 10.000.000 | - |
| Pajak (Piutang) | - | Rp 14.000.000 | - |
| Operasional | - | Rp 6.000.000 | - |
| Vendor | Odang | Rp 9.000.000 | - |

### Posisi Kas (Rp 74.000.000)

| Akun | Saldo |
|------|-------|
| Bank BTN | Rp 46.000.000 |
| Mr. Gobs (Piutang) | Rp 20.000.000 |
| Cash | Rp 8.000.000 |

---

## 3. GAP ANALYSIS

### ✅ FITUR YANG SUDAH ADA (Coverage: 80%)

| Modul | Fitur | Use Case Bisnis |
|-------|-------|-----------------|
| **Projects** | CRUD, Status tracking | Track PT Asia Con, PT Maulida, dll |
| **Tasks** | CRUD, Dependencies, Deadlines | Sub-pekerjaan: Uji Lab, PKKPR, Gambar |
| **Task Dependencies** | Depends on task | PKKPR depends on Site Plan ✅ |
| **Institutions** | Client database | PT RAS, PT Asia Con, dll |
| **Documents** | Upload, Categorize, Version | Proposal, Pertek, PKKPR, MSDS |
| **Users** | Multi-user, Assignment | Tim internal assignment |
| **Status Tracking** | Todo → In Progress → Done → Blocked | Track progress per task |

### ❌ CRITICAL GAPS (Coverage: 0%)

#### 1. Financial Management 🚨 PRIORITY 1
**Missing Features:**
- [ ] Contract value tracking
- [ ] Down payment (DP) recording
- [ ] Payment terms (termin pembayaran)
- [ ] Payment received tracking
- [ ] Outstanding receivables (piutang)
- [ ] Project expenses logging
- [ ] Vendor payments tracking
- [ ] Cash position dashboard
- [ ] Profit margin calculation
- [ ] Budget vs Actual comparison

**Business Impact:**
- ❌ Tidak bisa track piutang Rp 223 juta
- ❌ Tidak tahu profit real per proyek
- ❌ Sulit forecast cash flow
- ❌ Manual calculate margin

**Real Case:**
PT Asia Con - Kontrak Rp 180 juta, DP Rp 90 juta masuk, sisa Rp 90 juta kapan masuk? Expense berapa? Profit berapa?
→ **Sistem sekarang tidak bisa jawab!**

#### 2. Document Workflow 🚨 PRIORITY 2
**Missing Features:**
- [ ] Document submission status
- [ ] Submission tracking (submitted to BPN, KLHK, etc)
- [ ] Approval status & dates
- [ ] Revision tracking & numbering
- [ ] Document expiry dates
- [ ] Approval chain/workflow
- [ ] Response tracking from agencies

**Business Impact:**
- ❌ Tidak tahu Pertek sudah submit atau belum
- ❌ Tidak ada alert Pertek mau expired
- ❌ Revisi PT Putra Jaya versi berapa?
- ❌ Manual track submission ke instansi

**Real Case:**
PT MCM - "Proposal sudah ready tapi blocked karena Pertek Limbah B3 belum"
→ **Sistem sekarang tidak bisa enforce dependency dokumen!**

#### 3. Vendor Management 🚨 PRIORITY 3
**Missing Features:**
- [ ] Vendor master data
- [ ] Vendor categorization (drafter, lab, surveyor)
- [ ] Vendor contact & bank info
- [ ] Purchase Order (PO) tracking
- [ ] Vendor invoice management
- [ ] Invoice due dates & aging
- [ ] Vendor performance rating
- [ ] Cost allocation to projects

**Business Impact:**
- ❌ Expense Rp 15 juta gambar, vendor siapa? Invoice mana?
- ❌ Tidak ada reminder invoice due date
- ❌ Tidak bisa compare vendor performance
- ❌ Sulit audit trail expense

**Real Case:**
Pengeluaran Rp 73 juta, tapi tidak jelas:
- Rp 15 juta gambar untuk proyek mana?
- Mr. Gobs Rp 14 juta, invoice kapan?
- Big MAN Rp 5 juta, untuk apa?

#### 4. Project Costing & Analytics 📊 PRIORITY 4
**Missing Features:**
- [ ] Real-time profitability dashboard
- [ ] Cash flow projection (30-60-90 days)
- [ ] Project cost breakdown
- [ ] Resource utilization report
- [ ] Timeline Gantt chart
- [ ] Aging analysis (piutang/hutang)
- [ ] Budget deviation alerts

#### 5. Automation & Alerts 🤖 PRIORITY 5
**Missing Features:**
- [ ] Payment reminders
- [ ] Deadline alerts (3 days before)
- [ ] Document expiry notifications
- [ ] Task auto-block based on dependencies
- [ ] Auto-calculate profit margins
- [ ] WhatsApp/Email integration
- [ ] Weekly summary reports

---

## 4. IMPLEMENTATION ROADMAP

### 🚀 PHASE 1: FINANCIAL BASIC (Week 1-2) - URGENT

**Tujuan:** Track uang masuk & keluar per proyek dengan akurat

**Deliverables:**
1. **Database Schema:**
   - Extend `projects` table (contract_value, down_payment, payment_received)
   - New table: `project_payments`
   - New table: `project_expenses`
   - New table: `cash_accounts`

2. **UI Components:**
   - Tab "Keuangan" di project show page
   - Form input payment received
   - Form input project expenses
   - Dashboard: Income vs Expense
   - Project profitability indicator

3. **Business Rules:**
   - Auto-calculate outstanding receivables
   - Simple profit = (payment_received - expenses)
   - Multi-account support (BTN, Cash, Piutang)

**Metrics Success:**
- ✅ Bisa input DP Rp 90 juta PT Asia Con
- ✅ Bisa log expense Rp 15 juta gambar
- ✅ Dashboard show total piutang Rp 223 juta
- ✅ Bisa lihat profit per proyek

**Timeline:** 7-10 hari kerja

---

### 🚀 PHASE 2: DOCUMENT WORKFLOW (Week 3-4) - CRITICAL

**Tujuan:** Track status dokumen submission & approval

**Deliverables:**
1. **Database Schema:**
   - Extend `documents` table (submission_status, submission_date, approval_date, expiry_date)
   - New table: `document_approvals`
   - New table: `document_submissions`

2. **UI Components:**
   - Document submission form
   - Status badges (Draft → Submitted → Approved)
   - Submission timeline per document
   - Expiry date alerts

3. **Business Rules:**
   - Enforce document dependencies
   - Auto-alert 30 days before expiry
   - Track revision numbers

**Metrics Success:**
- ✅ PT MCM proposal blocked sampai Pertek approved
- ✅ Track "Pertek submitted to BPN on 4 Oct"
- ✅ Alert "Pertek will expire in 30 days"

**Timeline:** 7-10 hari kerja

---

### 🚀 PHASE 3: VENDOR MANAGEMENT (Week 5-6) - IMPORTANT

**Tujuan:** Database vendor & cost control

**Deliverables:**
1. **Database Schema:**
   - New table: `vendors`
   - New table: `vendor_invoices`
   - Link expenses to vendors

2. **UI Components:**
   - Vendor master data CRUD
   - Invoice tracking UI
   - Vendor performance rating
   - Due date reminders

3. **Business Rules:**
   - Invoice aging calculation
   - Auto-reminder 3 days before due
   - Vendor category filtering

**Metrics Success:**
- ✅ Database Big MAN, Mr. Gobs, Lab
- ✅ Track invoice Rp 14 juta Mr. Gobs, due date
- ✅ Compare vendor performance

**Timeline:** 7-10 hari kerja

---

### 🚀 PHASE 4: ANALYTICS & REPORTING (Week 7-8) - ENHANCEMENT

**Deliverables:**
- Profitability report per project
- Cash flow projection
- Timeline Gantt chart
- Resource utilization
- Aging analysis

**Timeline:** 7-10 hari kerja

---

### 🚀 PHASE 5: AUTOMATION (Week 9-10) - OPTIMIZATION

**Deliverables:**
- Payment reminders (WhatsApp/Email)
- Deadline alerts
- Document expiry notifications
- Auto-block tasks
- Weekly reports

**Timeline:** 7-10 hari kerja

---

## 5. COMPLIANCE & REGULATIONS

### Perpajakan Indonesia
1. **PPh 23 (Jasa Konsultan):** 2% dari gross invoice
2. **PPN:** 11% jika PKP (Pengusaha Kena Pajak)
3. **PPh Badan:** 22% dari net profit (untuk PT)
4. **PPh Final UMKM:** 0.5% dari omzet (jika omzet < 4.8M/tahun)

**System Requirements:**
- [ ] Auto-calculate PPh 23 di invoice
- [ ] PPN calculation toggle
- [ ] Tax report per period

### Perizinan Usaha Konsultan
1. **NIB (Nomor Induk Berusaha)** - via OSS
2. **Sertifikat Standar Konsultan Lingkungan** - LPJK
3. **SIUJK (Surat Izin Usaha Jasa Konstruksi)** - jika termasuk konstruksi
4. **ISO 9001** (optional, nilai tambah)

### Standar Akuntansi
- **SAK EMKM** untuk UMKM
- **Basis Akrual:** Recognize revenue saat kontrak signed, bukan saat terima uang
- **Laporan Wajib:** Neraca, Laba Rugi, Arus Kas

---

## 6. RISK ASSESSMENT

### Financial Risks
| Risk | Impact | Mitigation |
|------|--------|------------|
| Piutang macet (Rp 223M outstanding) | HIGH | Payment terms di contract, DP minimal 30% |
| Cash flow negatif | MEDIUM | Maintain buffer 3 months operational |
| Vendor payment delay | MEDIUM | Track invoice due dates strictly |

### Operational Risks
| Risk | Impact | Mitigation |
|------|--------|------------|
| Miss deadline (PT Asia Con 4 Oct) | HIGH | Alert system, buffer time |
| Document rejection by agencies | MEDIUM | Quality review before submit |
| Vendor quality issues (lab, drafter) | MEDIUM | Vendor rating & backup vendors |

### Compliance Risks
| Risk | Impact | Mitigation |
|------|--------|------------|
| Pertek expired | HIGH | Auto-alert 30 days before |
| Tax compliance | MEDIUM | Auto-calculate tax, monthly review |
| Missing documentation | MEDIUM | Mandatory fields, checklist |

---

## 7. SUCCESS METRICS (KPI)

### Financial KPIs
- **Revenue per Month:** Target Rp 100-150 juta
- **Profit Margin:** Target 60-70%
- **Collection Period:** Target < 30 hari setelah deliverable
- **Cash Position:** Minimum Rp 50 juta buffer

### Operational KPIs
- **On-Time Delivery:** Target 95%
- **Document Approval Rate:** Target 90% first submission
- **Client Satisfaction:** Target 4.5/5 stars
- **Vendor Quality:** Target 4.0/5 stars

### System KPIs (Post-Implementation)
- **Data Entry Time:** Reduce 50%
- **Report Generation:** Real-time vs 2-3 hours manual
- **Error Rate:** Reduce 80%
- **User Adoption:** 100% team usage within 2 weeks

---

## 8. NEXT ACTIONS

### Immediate (This Week)
1. ✅ Review & approve this analysis
2. ⏳ Input 6 existing projects into system
3. ⏳ Input tasks for PT Asia Con (deadline 4 Oct)
4. ⏳ Upload existing documents (proposals, pertek)

### Week 1-2 (Phase 1 Development)
1. ⏳ Database migration for financial tables
2. ⏳ Build payment tracking UI
3. ⏳ Build expense logging UI
4. ⏳ Create financial dashboard
5. ⏳ Testing & UAT

### Week 3-4 (Phase 2 Development)
1. ⏳ Document workflow implementation
2. ⏳ Submission tracking
3. ⏳ Testing & UAT

---

## 9. STAKEHOLDER SIGN-OFF

| Role | Name | Sign-off Date | Status |
|------|------|---------------|--------|
| Business Owner | [Name] | [Date] | ⏳ Pending |
| Project Manager | [Name] | [Date] | ⏳ Pending |
| Technical Lead | [Name] | [Date] | ⏳ Pending |

---

**Document Version:** 1.0  
**Last Updated:** 2 Oktober 2025  
**Next Review:** Post Phase 1 completion
