# Sprint 8: Dynamic Permit Dependency System - Implementation Plan

**Sprint Goal**: Implement flexible permit management system with templates and dependency tracking
**Duration**: 3-4 days
**Priority**: HIGH (Last sprint of Phase 2A)
**Start Date**: October 2, 2025

---

## 🎯 Sprint Objectives

### Primary Goals
1. ✅ Master data permit types management
2. ✅ Permit templates creation and management
3. ✅ Project permit assignment with dependencies
4. ✅ Visual dependency flow/timeline
5. ✅ Permit status tracking and workflow

### Success Criteria
- User can browse and select permits from master data
- User can create custom permits not in master
- User can use templates to quickly setup permits
- User can define dependencies between permits
- System validates dependency logic
- Visual representation of permit flow
- Permit status updates trigger notifications
- Full audit trail of permit changes

---

## 📋 Sprint Backlog

### Day 1: Master Data & Templates (Setup Foundation)

#### Morning Session (4 hours)
**Task 1.1: Permit Types Management**
- [ ] Create PermitType model with relationships
- [ ] Migration already exists, verify structure
- [ ] Create PermitTypeController with CRUD
- [ ] Seed 20 default permit types (UKL/UPL, IMB, etc.)
- [ ] Create permit types index view
- [ ] Create permit type form (create/edit)

**Task 1.2: Permit Template System**
- [ ] Create PermitTemplate model
- [ ] Create PermitTemplateItem model
- [ ] Create PermitTemplateDependency model
- [ ] Setup relationships between models
- [ ] Create controller for template management

#### Afternoon Session (4 hours)
**Task 1.3: Template UI**
- [ ] Template list view
- [ ] Template builder interface
- [ ] Add permit items to template
- [ ] Define dependencies in template
- [ ] Save/update template functionality

**Task 1.4: Seed Demo Templates**
- [ ] Create 3 demo templates:
  - UKL/UPL Package (Environmental permits)
  - Land Acquisition Package (BPN permits)
  - Building Permit Package (IMB/PBG permits)

---

### Day 2: Project Permit Implementation

#### Morning Session (4 hours)
**Task 2.1: Project Permit Models**
- [ ] Create ProjectPermit model
- [ ] Create ProjectPermitDependency model  
- [ ] Define relationships (project, permit_type, dependencies)
- [ ] Add computed properties (is_ready, is_blocked)
- [ ] Create observers for status changes

**Task 2.2: Permit Assignment**
- [ ] Create route for permit management
- [ ] PermitController for project permits
- [ ] Method: Apply template to project
- [ ] Method: Add individual permit
- [ ] Method: Add custom permit (not in master)

#### Afternoon Session (4 hours)
**Task 2.3: Dependency Management**
- [ ] Create dependency validator
- [ ] Prevent circular dependencies
- [ ] Calculate permit readiness status
- [ ] Method: Get dependency chain
- [ ] Method: Get blocked by permits

**Task 2.4: Permit UI - Tab Integration**
- [ ] Add "Permits" tab to project detail
- [ ] Display current project permits
- [ ] Show permit status with icons
- [ ] Show dependency relationships
- [ ] Quick actions (update status, add note)

---

### Day 3: Workflow & Visualization

#### Morning Session (4 hours)
**Task 3.1: Permit Workflow**
- [ ] Status transition rules
- [ ] Auto-update dependent permit status
- [ ] Notification on status change
- [ ] Document requirements per status
- [ ] Cost tracking (estimated vs actual)

**Task 3.2: Permit Timeline View**
- [ ] Gantt-style timeline component
- [ ] Show permits with start/end dates
- [ ] Color-code by status
- [ ] Show dependency arrows
- [ ] Interactive (click to see details)

#### Afternoon Session (4 hours)
**Task 3.3: Permit Detail Modal**
- [ ] Modal for permit details
- [ ] Show all permit information
- [ ] Edit permit data inline
- [ ] Update status dropdown
- [ ] Add documents related to permit
- [ ] Add notes/comments

**Task 3.4: Dependency Visualization**
- [ ] Flow diagram component
- [ ] Show permits as nodes
- [ ] Show dependencies as arrows
- [ ] Highlight critical path
- [ ] Show blocked permits in red
- [ ] Interactive navigation

---

### Day 4: Polish & Integration

#### Morning Session (4 hours)
**Task 4.1: Permit Actions**
- [ ] Bulk status update
- [ ] Clone permit from another project
- [ ] Export permit list (Excel)
- [ ] Print permit roadmap (PDF)
- [ ] Archive completed permits

**Task 4.2: Advanced Features**
- [ ] Auto-calculate target dates based on dependencies
- [ ] Send reminders for due permits
- [ ] Flag permits nearing deadline
- [ ] Show permit health score
- [ ] Suggest next action per permit

#### Afternoon Session (4 hours)
**Task 4.3: Testing & Bug Fixes**
- [ ] Test all CRUD operations
- [ ] Test dependency validation
- [ ] Test status transitions
- [ ] Test with multiple projects
- [ ] Fix any bugs found

**Task 4.4: Documentation**
- [ ] Update ROADMAP.md
- [ ] Create SPRINT_8_COMPLETION_REPORT.md
- [ ] Document permit workflow
- [ ] Create user guide for permits
- [ ] API documentation

---

## 🗄️ Database Schema Review

### Existing Tables (Verify)
- ✅ `permit_types` - Master data
- ✅ `permit_templates` - Templates
- ✅ `permit_template_items` - Template contents
- ✅ `permit_template_dependencies` - Template relationships
- ✅ `project_permits` - Actual permits in projects
- ✅ `project_permit_dependencies` - Actual dependencies

### Key Relationships
```
PermitType (Master)
  ↓ (reference)
PermitTemplate → PermitTemplateItem → PermitTemplateDependency
  ↓ (apply to)
Project → ProjectPermit → ProjectPermitDependency
```

---

## 🎨 UI/UX Design

### Permits Tab Layout
```
┌─────────────────────────────────────────────────┐
│ Permits                          [+ Add Permit] │
│                      [Use Template ▼] [Timeline]│
├─────────────────────────────────────────────────┤
│                                                 │
│  ┌───────────────────────────────────────────┐ │
│  │ 📄 UKL/UPL                        ✅ DONE │ │
│  │ Target: 2024-12-15 | Cost: Rp 5,000,000  │ │
│  │ Assigned: Siti     | SK: 123/UKL/2024   │ │
│  └───────────────────────────────────────────┘ │
│        ↓ depends on                            │
│  ┌───────────────────────────────────────────┐ │
│  │ 🏗️ Pertek BPN              🔄 IN PROGRESS │ │
│  │ Target: 2024-11-30 | Cost: Rp 3,000,000  │ │
│  │ Assigned: Rudi     | Days left: 12       │ │
│  └───────────────────────────────────────────┘ │
│        ↓ depends on                            │
│  ┌───────────────────────────────────────────┐ │
│  │ 📋 IMB/PBG                      ⏸️ WAITING│ │
│  │ Blocked by: Pertek BPN                    │ │
│  └───────────────────────────────────────────┘ │
│                                                 │
└─────────────────────────────────────────────────┘
```

### Timeline View (Gantt)
```
┌───────────────────────────────────────────────────┐
│ Permit Timeline                [Export] [Print]  │
├───────────────────────────────────────────────────┤
│                Nov        Dec        Jan          │
│ UKL/UPL    ████████████████░░░                   │
│ Pertek     ░░░░████████████░░░░░                 │
│ IMB/PBG    ░░░░░░░░░░░░░░░░████████░░            │
│                                                   │
│ Legend: ████ Completed  ████ In Progress         │
│         ░░░░ Not Started  ⚠️ Delayed             │
└───────────────────────────────────────────────────┘
```

### Dependency Flow Diagram
```
       ┌─────────┐
       │UKL/UPL  │
       │✅ Done  │
       └────┬────┘
            │
            ↓
       ┌─────────┐     ┌──────────┐
       │Pertek   │ ──→ │Siteplan  │
       │🔄Active │     │🔄Active  │
       └────┬────┘     └──────────┘
            │               │
            ↓               ↓
       ┌─────────┐     ┌──────────┐
       │IMB/PBG  │ ←── │PBB Tax   │
       │⏸️Wait  │     │✅ Done   │
       └─────────┘     └──────────┘
```

---

## 🔧 Technical Implementation

### Models Structure

**PermitType.php**
```php
class PermitType extends Model {
    // Relationships
    public function institution()
    public function templates()
    public function projectPermits()
    
    // Scopes
    public function scopeActive()
    public function scopeByCategory()
    
    // Accessors
    public function getDisplayNameAttribute()
}
```

**PermitTemplate.php**
```php
class PermitTemplate extends Model {
    // Relationships
    public function items()
    public function dependencies()
    public function creator()
    
    // Methods
    public function applyToProject(Project $project)
    public function clone()
    public function incrementUsage()
}
```

**ProjectPermit.php**
```php
class ProjectPermit extends Model {
    // Relationships
    public function project()
    public function permitType()
    public function assignedTo()
    public function dependencies()
    public function dependents()
    public function documents()
    
    // Status Methods
    public function isReady()
    public function isBlocked()
    public function getBlockingPermits()
    public function canStart()
    
    // Progress
    public function calculateProgress()
    public function getDaysRemaining()
    public function isOverdue()
}
```

---

## 🧪 Testing Checklist

### Unit Tests
- [ ] PermitType CRUD
- [ ] Template creation and application
- [ ] Dependency validation (no circular)
- [ ] Status transition rules
- [ ] Permit readiness calculation

### Integration Tests
- [ ] Apply template to project
- [ ] Add custom permit
- [ ] Update permit status
- [ ] Calculate dependency chain
- [ ] Generate timeline data

### UI Tests
- [ ] Permit list displays correctly
- [ ] Timeline renders properly
- [ ] Dependency diagram loads
- [ ] Modal interactions work
- [ ] Status updates reflect immediately

---

## 📊 Success Metrics

### Functional
- User can setup 10 permits in < 5 minutes using template
- Dependency validation prevents all circular references
- Status updates propagate correctly to dependents
- Timeline accurately reflects project schedule

### Performance
- Permit list loads in < 500ms
- Timeline renders in < 1s
- Dependency calculation < 100ms
- No N+1 query issues

### UX
- Intuitive permit selection
- Clear dependency visualization
- Easy status updates
- Helpful error messages

---

## 🚀 Deployment Strategy

### Phase 1: Models & Backend (Day 1-2)
- Deploy models and migrations
- Test in staging environment
- Verify data integrity

### Phase 2: UI Components (Day 3)
- Deploy permit tab
- Deploy timeline view
- User acceptance testing

### Phase 3: Polish & Go-Live (Day 4)
- Fix bugs from testing
- Performance optimization
- Production deployment

---

## 📝 Documentation Required

1. **Technical Docs**
   - Model relationships diagram
   - API endpoints documentation
   - Database schema with indexes

2. **User Docs**
   - How to use permit templates
   - How to add custom permits
   - Understanding dependency flow
   - Status workflow guide

3. **Admin Docs**
   - How to add new permit types
   - How to create templates
   - Maintenance procedures

---

## 🎯 Definition of Done

- [ ] All models created with relationships
- [ ] CRUD operations working for all entities
- [ ] Permits tab integrated in project detail
- [ ] Template system functional
- [ ] Dependency system validates correctly
- [ ] Timeline view displays correctly
- [ ] Status workflow implemented
- [ ] No critical bugs
- [ ] Documentation complete
- [ ] Code reviewed and merged

---

**Ready to start Sprint 8?** This will complete Phase 2A! 🚀
