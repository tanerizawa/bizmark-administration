# Phase 2A: Dynamic Permit Dependency System

## 📋 Overview
Sistem manajemen izin dengan dependency dinamis yang memungkinkan user mengkonfigurasi alur perizinan secara fleksibel untuk setiap proyek.

## 🎯 Goals
1. User dapat memilih izin mana yang dibutuhkan untuk setiap proyek
2. User dapat mengatur dependency/urutan izin secara manual
3. User dapat menambah izin custom yang tidak ada di master
4. User dapat override dependency jika ada kasus khusus
5. Sistem menyediakan template untuk mempercepat setup

## 🗄️ Database Structure

### 1. permit_types (Master Data - Referensi)
```sql
- id: bigint (PK)
- name: varchar(100) - "UKL/UPL", "Pertek BPN", "IMB/PBG"
- code: varchar(50) - "UKL_UPL", "PERTEK_BPN"
- category: enum - environmental, land, building, transportation, business
- institution_id: FK to institutions (nullable)
- avg_processing_days: int
- description: text
- required_documents: json - ["KTP", "NPWP", "Siteplan"]
- estimated_cost_min: decimal
- estimated_cost_max: decimal
- is_active: boolean
- created_at, updated_at
```

### 2. permit_templates (Optional - Untuk Mempercepat Setup)
```sql
- id: bigint (PK)
- name: varchar(150) - "Template UKL/UPL Pabrik Kelapa Sawit"
- description: text
- use_case: text - "Untuk proyek pabrik dengan lahan > 1 ha"
- category: varchar(50) - sama dengan permit_types.category
- created_by_user_id: FK to users
- is_public: boolean - bisa dipakai team lain?
- usage_count: int - track popularitas
- created_at, updated_at
```

### 3. permit_template_items (Isi Template)
```sql
- id: bigint (PK)
- template_id: FK to permit_templates
- permit_type_id: FK to permit_types (nullable - bisa custom)
- custom_permit_name: varchar(100) - jika permit_type_id null
- sequence_order: int
- is_goal_permit: boolean - apakah ini izin target akhir?
- estimated_days: int
- estimated_cost: decimal
- notes: text
- created_at, updated_at
```

### 4. permit_template_dependencies (Relasi dalam Template)
```sql
- id: bigint (PK)
- template_id: FK to permit_templates
- permit_item_id: FK to permit_template_items
- depends_on_item_id: FK to permit_template_items
- dependency_type: enum - MANDATORY, OPTIONAL, RECOMMENDED
- notes: text
- created_at, updated_at
```

### 5. project_permits (Actual Working Data - FULLY EDITABLE)
```sql
- id: bigint (PK)
- project_id: FK to projects
- permit_type_id: FK to permit_types (nullable)
- custom_permit_name: varchar(100) - jika tidak ada di master
- custom_institution_name: varchar(100)
- sequence_order: int - user dapat ubah
- is_goal_permit: boolean
- status: enum - NOT_STARTED, IN_PROGRESS, WAITING_DOC, SUBMITTED, 
                 UNDER_REVIEW, APPROVED, REJECTED, EXISTING, CANCELLED
- has_existing_document: boolean
- existing_document_id: FK to documents (nullable)
- assigned_to_user_id: FK to users (nullable)
- started_at: datetime
- submitted_at: datetime
- approved_at: datetime
- rejected_at: datetime
- target_date: date
- estimated_cost: decimal
- actual_cost: decimal
- permit_number: varchar(100) - nomor SK/izin yang terbit
- valid_until: date - masa berlaku izin
- notes: text
- created_at, updated_at
```

### 6. project_permit_dependencies (Relasi Antar Izin di Proyek)
```sql
- id: bigint (PK)
- project_permit_id: FK to project_permits
- depends_on_permit_id: FK to project_permits
- dependency_type: enum - MANDATORY, OPTIONAL
- can_proceed_without: boolean - untuk override
- override_reason: text - wajib jika can_proceed_without = true
- override_document_path: varchar(255) - bukti pendukung
- overridden_by_user_id: FK to users (nullable)
- overridden_at: datetime (nullable)
- created_by_user_id: FK to users
- created_at, updated_at
```

## 📊 Relationships

```
permit_types (Master)
    ↓ (many-to-many via project_permits)
projects ←→ project_permits ←→ project_permit_dependencies
                ↓
            documents (existing permits)
            
permit_templates → permit_template_items → permit_template_dependencies
                ↓ (copy to)
            project_permits
```

## 🎨 UI Components

### Phase 2A.1 - Master Data (Week 1)
1. **Permit Types Management** (`/admin/permit-types`)
   - CRUD jenis izin
   - Kategorisasi
   - Set institution default
   - Set estimasi waktu & biaya

### Phase 2A.2 - Template System (Week 1)
2. **Template Builder** (`/admin/permit-templates`)
   - Create template
   - Add permits to template
   - Define dependencies (drag-and-drop)
   - Preview template tree
   - Publish/unpublish

### Phase 2A.3 - Project Integration (Week 2)
3. **Project Permit Wizard** (saat create/edit project)
   - Step: Choose template or start from scratch
   - Step: Configure permit tree
   - Step: Set initial status for each permit
   - Step: Review & save

4. **Permit Roadmap Tab** (`/projects/{id}/permits`)
   - Visual tree/flowchart
   - Status indicators
   - Progress tracking
   - Edit tree anytime
   - Add/remove permits
   - Override dependencies

### Phase 2A.4 - Operations (Week 2)
5. **Permit Detail Modal**
   - View permit info
   - Upload existing document
   - Start processing
   - Update status
   - Add notes

## 🔄 User Workflows

### Workflow 1: Setup Project Baru dengan Template
```
1. User create project baru
2. Setelah isi info dasar, pilih "Use Template"
3. Pilih template "UKL/UPL Pabrik Kelapa Sawit"
4. System copy template items & dependencies ke project
5. User review tree, bisa edit/hapus/tambah
6. User set status awal (existing/need processing)
7. Save project
```

### Workflow 2: Setup Project Baru Custom
```
1. User create project baru
2. Pilih "Configure Manually"
3. Drag permits dari sidebar ke tree builder
4. Set dependency untuk setiap permit
5. Mark goal permit (izin target akhir)
6. Set status awal
7. Save project
```

### Workflow 3: Start Processing Permit
```
1. User buka project → Tab "Izin & Prasyarat"
2. Klik permit yang ready (dependencies terpenuhi)
3. Modal muncul:
   - Option A: Upload existing document → Status = EXISTING
   - Option B: Start processing → Status = IN_PROGRESS
4. Jika pilih B:
   - Assign to user
   - Set target date
   - Set budget
   - System auto create task
5. Save
```

### Workflow 4: Override Dependency
```
1. User ingin start permit yang masih blocked
2. Klik "Override Dependency"
3. Modal konfirmasi:
   - List dependencies yang belum selesai
   - Input alasan override (wajib)
   - Upload dokumen pendukung (optional)
4. Confirm override
5. System log activity untuk audit
6. Permit unlocked, bisa diproses
```

## 🚀 Implementation Order

### Sprint 1 (3-4 hari): Foundation
- [ ] Create migrations (6 tables)
- [ ] Create models with relationships
- [ ] Seed sample permit types (15-20 jenis izin umum)
- [ ] Seed 2-3 sample templates

### Sprint 2 (3-4 hari): Master Data UI
- [ ] Permit Types CRUD
- [ ] Template Builder (basic version)
- [ ] Template Preview

### Sprint 3 (4-5 hari): Project Integration
- [ ] Project creation wizard (add permit setup step)
- [ ] Permit Roadmap tab in project detail
- [ ] Permit tree visualization
- [ ] Add/remove permit functionality

### Sprint 4 (3-4 hari): Operations
- [ ] Start permit modal
- [ ] Upload existing document
- [ ] Update permit status
- [ ] Override dependency modal
- [ ] Activity logging

### Sprint 5 (2-3 hari): Polish & Testing
- [ ] Drag-and-drop tree builder
- [ ] Visual flowchart (using library)
- [ ] Responsive design
- [ ] Integration testing
- [ ] Documentation

## 📈 Success Metrics

1. User dapat setup permit tree untuk proyek baru dalam < 5 menit (dengan template)
2. User dapat add/edit permit tree kapan saja
3. System dapat handle 20+ permits per project
4. Dependency blocking/unlocking works correctly
5. Override mechanism provides audit trail

## 🔗 Integration Points

- **Documents**: Link existing permits
- **Tasks**: Auto-create task when start processing
- **Timeline**: Show permit deadlines in gantt
- **Financial**: Track actual cost vs estimate
- **Notifications**: Alert when dependency completed

## 📝 Notes

- Prioritas: Fleksibilitas > Automation
- User control > System rules
- Template mempercepat, bukan membatasi
- Audit trail penting untuk compliance
- Mobile-friendly untuk field operations

---

**Status**: 📝 Design Complete - Ready for Implementation
**Estimated Total Time**: 15-20 hari (120-160 jam)
**Start Date**: October 2, 2025
