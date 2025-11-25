# 🎯 Recruitment Advanced System - Comprehensive Analysis & Roadmap

**Project**: Advanced Interview & Testing System for Bizmark.ID  
**Date**: November 23, 2025  
**Document Version**: 1.0  

---

## 📊 Executive Summary

### Current State
- ✅ Basic recruitment system operational (job posting, application form)
- ✅ Application status tracking (pending, reviewed, interview, accepted, rejected)
- ✅ Admin panel untuk review applications
- ✅ Email notifications untuk status changes
- ✅ File upload (CV, portfolio)

### Proposed Enhancement
**Multi-stage recruitment process** dengan:
1. **Automated Testing** (Psikologi, Psikotest, Technical Skills)
2. **Interview Scheduling & Management**
3. **Video Conference Integration**
4. **Comprehensive Candidate Assessment**
5. **Advanced Analytics & Reporting**

---

## 🖥️ Server Specification Analysis

### Current Infrastructure
```
CPU: AMD EPYC 9354P 32-Core Processor
RAM: 15 GB (11 GB available)
Storage: 197 GB total (186 GB available, 3.2 GB used)
Usage: 2% disk utilization
```

### Video Conference Feasibility Assessment

#### Option 1: Self-Hosted (Jitsi Meet) ⭐ **RECOMMENDED**
**Pros:**
- ✅ Full control & customization
- ✅ No per-user licensing cost
- ✅ GDPR compliant (data stays on server)
- ✅ Adequate resources: 32-core CPU + 15GB RAM dapat handle 10-15 concurrent video calls
- ✅ Seamless integration dengan Laravel

**Cons:**
- ⚠️ Bandwidth requirement: ~2-4 Mbps per participant
- ⚠️ Maintenance overhead (updates, security patches)
- ⚠️ Need TURN/STUN server untuk NAT traversal

**Resource Requirements:**
- RAM: ~500MB per concurrent room (dapat support 20+ rooms)
- CPU: Minimal impact dengan WebRTC peer-to-peer
- Storage: Logs ~100MB per month

**Estimated Cost:** $0/month (only bandwidth)

#### Option 2: Third-party API (Zoom/Google Meet)
**Pros:**
- ✅ Zero server load
- ✅ Superior reliability & features
- ✅ Familiar interface untuk candidates

**Cons:**
- ❌ Monthly cost: ~$15-20 per host
- ❌ API rate limits
- ❌ Data privacy concerns
- ❌ Less customization

**Estimated Cost:** $150-300/month (10 recruiters)

#### Option 3: Hybrid Approach ⚡ **OPTIMAL**
- Use **Zoom API** untuk final interviews (important)
- Use **Jitsi** untuk preliminary screening calls
- Fallback mechanism jika salah satu down

**Estimated Cost:** $50/month (3 Zoom licenses for managers)

---

## 🗄️ Database Architecture Design

### New Tables Required

#### 1. `interview_schedules`
```sql
CREATE TABLE interview_schedules (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    job_application_id BIGINT NOT NULL,
    interview_type ENUM('preliminary', 'technical', 'hr', 'final') NOT NULL,
    interview_stage TINYINT NOT NULL DEFAULT 1,
    scheduled_at TIMESTAMP NOT NULL,
    duration_minutes INT NOT NULL DEFAULT 60,
    location VARCHAR(255), -- 'online', 'office', atau alamat spesifik
    meeting_type ENUM('in-person', 'video-call', 'phone') NOT NULL,
    meeting_link TEXT, -- Zoom/Jitsi link
    meeting_password VARCHAR(255),
    interviewer_ids JSON, -- Array user IDs
    status ENUM('scheduled', 'confirmed', 'rescheduled', 'completed', 'cancelled', 'no-show') DEFAULT 'scheduled',
    notes TEXT,
    reminder_sent_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    created_by BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (job_application_id) REFERENCES job_applications(id) ON DELETE CASCADE,
    INDEX idx_scheduled_at (scheduled_at),
    INDEX idx_status (status),
    INDEX idx_application_stage (job_application_id, interview_stage)
);
```

#### 2. `interview_feedback`
```sql
CREATE TABLE interview_feedback (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    interview_schedule_id BIGINT NOT NULL,
    interviewer_id BIGINT NOT NULL,
    
    -- Scoring (1-10 scale)
    technical_skills TINYINT,
    communication TINYINT,
    problem_solving TINYINT,
    cultural_fit TINYINT,
    motivation TINYINT,
    overall_rating DECIMAL(3,1), -- Average score
    
    -- Qualitative feedback
    strengths TEXT,
    weaknesses TEXT,
    detailed_notes TEXT,
    
    recommendation ENUM('strong-hire', 'hire', 'maybe', 'no-hire') NOT NULL,
    
    submitted_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (interview_schedule_id) REFERENCES interview_schedules(id) ON DELETE CASCADE,
    FOREIGN KEY (interviewer_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_feedback (interview_schedule_id, interviewer_id)
);
```

#### 3. `test_templates`
```sql
CREATE TABLE test_templates (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    test_type ENUM('psychology', 'psychometric', 'technical', 'aptitude', 'personality') NOT NULL,
    description TEXT,
    duration_minutes INT NOT NULL,
    passing_score INT NOT NULL, -- Percentage
    
    -- Configuration
    questions_data JSON, -- Question bank
    instructions TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    
    -- Metadata
    created_by BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_type (test_type, is_active)
);
```

#### 4. `test_sessions`
```sql
CREATE TABLE test_sessions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    job_application_id BIGINT NOT NULL,
    test_template_id BIGINT NOT NULL,
    
    -- Session control
    session_token VARCHAR(64) UNIQUE NOT NULL,
    status ENUM('pending', 'in-progress', 'completed', 'expired', 'cancelled') DEFAULT 'pending',
    
    -- Timing
    starts_at TIMESTAMP NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    
    -- Results
    score DECIMAL(5,2), -- 0-100 percentage
    passed BOOLEAN DEFAULT FALSE,
    time_taken_minutes INT,
    
    -- Security & integrity
    ip_address VARCHAR(45),
    user_agent TEXT,
    tab_switches INT DEFAULT 0, -- Anti-cheat
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (job_application_id) REFERENCES job_applications(id) ON DELETE CASCADE,
    FOREIGN KEY (test_template_id) REFERENCES test_templates(id) ON DELETE CASCADE,
    INDEX idx_session_token (session_token),
    INDEX idx_status_expires (status, expires_at)
);
```

#### 5. `test_answers`
```sql
CREATE TABLE test_answers (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    test_session_id BIGINT NOT NULL,
    question_id VARCHAR(100) NOT NULL, -- References questions_data in template
    answer_data JSON NOT NULL, -- Flexible: multiple choice, text, file path
    is_correct BOOLEAN,
    points_earned DECIMAL(5,2),
    time_spent_seconds INT,
    answered_at TIMESTAMP,
    
    FOREIGN KEY (test_session_id) REFERENCES test_sessions(id) ON DELETE CASCADE,
    INDEX idx_session (test_session_id)
);
```

#### 6. `technical_test_submissions`
```sql
CREATE TABLE technical_test_submissions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    job_application_id BIGINT NOT NULL,
    test_title VARCHAR(255) NOT NULL,
    test_description TEXT,
    
    -- File upload
    original_file_path VARCHAR(500), -- File sebelum dikerjakan
    submission_file_path VARCHAR(500) NOT NULL, -- File hasil kandidat
    file_type VARCHAR(50), -- docx, xlsx, pdf, etc
    
    -- Auto-check results (for formatted documents)
    format_score DECIMAL(5,2), -- Auto-scoring format compliance
    format_issues JSON, -- Array of detected issues
    
    -- Manual review
    reviewer_id BIGINT,
    review_score DECIMAL(5,2),
    review_notes TEXT,
    reviewed_at TIMESTAMP NULL,
    
    status ENUM('submitted', 'under-review', 'reviewed') DEFAULT 'submitted',
    submitted_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (job_application_id) REFERENCES job_applications(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES users(id),
    INDEX idx_status (status)
);
```

#### 7. `recruitment_stages` (Tracking overall progress)
```sql
CREATE TABLE recruitment_stages (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    job_application_id BIGINT NOT NULL,
    stage_name VARCHAR(100) NOT NULL, -- 'screening', 'psych-test', 'technical-test', 'interview-1', etc
    stage_order INT NOT NULL,
    status ENUM('pending', 'in-progress', 'passed', 'failed', 'skipped') DEFAULT 'pending',
    
    score DECIMAL(5,2),
    notes TEXT,
    
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (job_application_id) REFERENCES job_applications(id) ON DELETE CASCADE,
    UNIQUE KEY unique_stage (job_application_id, stage_name),
    INDEX idx_application_order (job_application_id, stage_order)
);
```

---

## 🎨 Feature Specifications

### 1. Interview Management System

#### Admin Features:
- **Scheduling Interface**
  - Calendar view (FullCalendar.js integration)
  - Drag & drop scheduling
  - Conflict detection (double-booking prevention)
  - Bulk scheduling for multiple candidates
  
- **Interview Types**
  - Preliminary Screening (15-30 min)
  - Technical Interview (60-90 min)
  - HR Interview (45-60 min)
  - Final Interview with Leadership (60 min)

- **Automated Notifications**
  - Email + WhatsApp notifications 24 hours before
  - Calendar invite (.ics file)
  - SMS reminder 2 hours before
  - Automatic rescheduling requests

- **Interviewer Dashboard**
  - Upcoming interviews list
  - Candidate profile preview
  - Feedback form (submit during/after interview)
  - Historical feedback access

#### Candidate Features:
- **My Interview Portal** (`/karir/my-interviews/{token}`)
  - View scheduled interviews
  - One-click join meeting (for video calls)
  - Request reschedule (with reason)
  - Pre-interview checklist & tips
  
- **Video Call Interface**
  - Test audio/video before join
  - Screen sharing capability
  - Real-time connection quality indicator
  - Recording option (with consent)

---

### 2. Psychology & Psychometric Testing

#### Test Types:
1. **DISC Personality Assessment**
   - 24 questions
   - 15 minutes duration
   - Automatic scoring & interpretation
   - Generate PDF report

2. **Cognitive Ability Test**
   - Logical reasoning (10 questions)
   - Numerical reasoning (10 questions)
   - Verbal reasoning (10 questions)
   - 45 minutes total

3. **Situational Judgment Test**
   - 15 workplace scenarios
   - Multiple choice responses
   - 30 minutes

#### Admin Features:
- **Test Builder**
  - Question bank management
  - Drag & drop question ordering
  - Multiple question types: MCQ, True/False, Likert scale
  - Import questions from CSV/Excel
  
- **Test Assignment**
  - Assign specific tests to job positions
  - Set passing threshold per test
  - Configure test sequence

- **Results Dashboard**
  - Individual candidate results
  - Comparative analysis (vs other candidates)
  - Export to Excel/PDF
  - Filter by job position, date range

#### Candidate Experience:
- **Test Portal** (`/karir/test/{token}`)
  - Clear instructions page
  - Timer countdown (persistent across page refresh)
  - Progress indicator
  - Auto-save answers every 30 seconds
  - Submit confirmation dialog
  
- **Anti-Cheat Measures**
  - Full-screen mode enforcement
  - Tab switch detection (logged & flagged)
  - Randomized question order
  - Disable copy-paste
  - Proctor view (optional webcam recording)

---

### 3. Technical Test System

#### Document Format Test (Word/Excel)
**Use Case**: Test kandidat untuk memperbaiki format dokumen sesuai standard perusahaan

**Workflow:**
1. Admin upload template dokumen dengan format issues
2. Kandidat download file
3. Kandidat perbaiki & upload kembali
4. System auto-check format compliance
5. Reviewer verify & score manual aspects

**Auto-Check Capabilities:**
- Font consistency (family, size)
- Heading hierarchy (H1, H2, H3)
- Margin & spacing
- Table formatting
- Image alignment & sizing
- Page numbering
- Header/footer presence

**Implementation:**
- Use **PHPWord** library untuk parse DOCX
- Use **PhpSpreadsheet** untuk XLSX
- Compare dengan template menggunakan rule engine
- Generate compliance score (0-100%)

#### Code Challenge (for technical positions)
- Online code editor (Monaco Editor)
- Support multiple languages (PHP, JavaScript, Python)
- Unit test validation
- Plagiarism detection

#### CAD/Design Test (for drafter positions)
- Upload DWG/DXF files
- Checklist-based manual review
- File size & complexity metrics

---

### 4. Video Conference Integration

#### Jitsi Meet Implementation (Self-Hosted)

**Installation Steps:**
```bash
# Install Jitsi Meet on Ubuntu
apt update
apt install -y jitsi-meet

# SSL Setup dengan Let's Encrypt
/usr/share/jitsi-meet/scripts/install-letsencrypt-cert.sh

# Configure JWT authentication
apt install -y jitsi-meet-tokens

# Laravel Integration
composer require firebase/php-jwt
```

**Features:**
- Generate secure meeting rooms
- Moderator controls (mute, kick, lock room)
- Screen sharing
- Recording (stored locally)
- Waiting room
- Breakout rooms (for panel interviews)

**Laravel Integration:**
```php
// Generate meeting token
$jwt = JWT::encode([
    'context' => [
        'user' => [
            'name' => $candidate->full_name,
            'email' => $candidate->email,
            'avatar' => $candidate->avatar_url,
        ],
    ],
    'aud' => 'jitsi',
    'iss' => config('jitsi.app_id'),
    'sub' => config('jitsi.domain'),
    'room' => $roomName,
    'exp' => time() + 3600, // 1 hour
], config('jitsi.secret'), 'HS256');

$meetingUrl = "https://meet.bizmark.id/{$roomName}?jwt={$jwt}";
```

#### Zoom Integration (Fallback)
```php
// Composer package
composer require zoom/zoom-php-sdk

// Create meeting
$zoom = new ZoomClient(config('services.zoom.client_id'), config('services.zoom.client_secret'));
$meeting = $zoom->createMeeting([
    'topic' => "Interview with {$candidate->full_name}",
    'type' => 2, // Scheduled
    'start_time' => $schedule->scheduled_at->toIso8601String(),
    'duration' => $schedule->duration_minutes,
    'settings' => [
        'waiting_room' => true,
        'join_before_host' => false,
        'auto_recording' => 'cloud',
    ],
]);
```

---

## 📱 User Interface Design

### Candidate Portal (`/karir/candidate-portal/{token}`)

**Dashboard Sections:**
1. **Application Status Timeline**
   - Visual progress tracker
   - Current stage highlighted
   - Estimated timeline

2. **Pending Tasks**
   - Tests to complete (with deadlines)
   - Interview confirmations needed
   - Documents to upload

3. **Upcoming Interviews**
   - Date, time, type
   - Interviewer names
   - Join meeting button (30 min before)

4. **Test Results** (after completion)
   - Score summary
   - Strengths & areas to improve
   - Downloadable certificate

### Admin Panel Enhancements

#### New Menu Structure:
```
Recruitment
├── Dashboard (Overview metrics)
├── Job Vacancies
├── Applications
│   ├── All Applications
│   ├── Screening Queue
│   ├── Test Results
│   └── Interview Schedule
├── Testing
│   ├── Test Templates
│   ├── Active Sessions
│   └── Results & Analytics
├── Interviews
│   ├── Calendar View
│   ├── Feedback Review
│   └── Video Recordings
└── Reports
    ├── Funnel Analysis
    ├── Time-to-Hire
    └── Source Effectiveness
```

#### Dashboard Metrics:
- Active candidates by stage (funnel chart)
- Average time per stage
- Test completion rates
- Interview no-show rates
- Offer acceptance rate
- Source of hire (job boards, referrals, etc.)

---

## 🔧 Technical Implementation Plan

### Phase 1: Foundation (Week 1-2) - 2 weeks
**Goal:** Database setup & core models

**Tasks:**
- [x] Create 7 new migration files
- [x] Build Eloquent models with relationships
- [x] Seed sample data untuk testing
- [x] API documentation (OpenAPI/Swagger)

**Deliverables:**
- Migration files executed
- Models tested in Tinker
- Seeder dengan realistic data

---

### Phase 2: Interview Management (Week 3-4) - 2 weeks
**Goal:** Functional interview scheduling system

**Tasks:**
- [x] Admin controller untuk CRUD interview schedules
- [x] Calendar view dengan FullCalendar.js
- [x] Email notifications with .ics attachments
- [x] Interviewer feedback form
- [x] Candidate interview portal

**Deliverables:**
- Admin dapat schedule interviews
- Candidates receive notifications
- Feedback collection workflow works

---

### Phase 3: Psychology Testing (Week 5-6) - 2 weeks
**Goal:** Online testing platform

**Tasks:**
- [x] Test template builder (admin)
- [x] Test assignment logic
- [x] Candidate test portal with timer
- [x] Auto-scoring engine
- [x] Results dashboard
- [x] Anti-cheat mechanisms

**Deliverables:**
- 3 test templates created (DISC, Cognitive, SJT)
- Candidates can take tests
- Automatic scoring works
- PDF report generation

---

### Phase 4: Technical Testing (Week 7-8) - 2 weeks
**Goal:** Document format checker & submission system

**Tasks:**
- [x] File upload interface
- [x] PHPWord/PhpSpreadsheet integration
- [x] Format compliance checker algorithm
- [x] Manual review interface
- [x] Scoring rubric system

**Deliverables:**
- Upload Word/Excel test files
- Auto-check detects 10+ format issues
- Manual review workflow functional

---

### Phase 5: Video Conference (Week 9-10) - 2 weeks
**Goal:** Integrated video calling

**Decision Point:** Evaluate Phase 1-4 server performance

**Option A: Jitsi (if server stable)**
- [x] Install Jitsi Meet on server
- [x] JWT authentication setup
- [x] Laravel wrapper service
- [x] Embed iframe in portal
- [x] Recording management

**Option B: Zoom API (if server issues)**
- [x] Zoom SDK integration
- [x] Meeting creation automation
- [x] Webhook handlers (participant joined/left)

**Deliverables:**
- Working video call system
- Meeting links auto-generated
- Recordings accessible to admin

---

### Phase 6: Advanced Features (Week 11-12) - 2 weeks
**Goal:** Polish & enhancements

**Tasks:**
- [x] Advanced analytics dashboard
- [x] Export reports (Excel, PDF)
- [x] WhatsApp integration untuk reminders
- [x] SMS notifications (Twilio/Nexmo)
- [x] Candidate portal mobile optimization
- [x] Performance optimization
- [x] Security audit

**Deliverables:**
- Analytics dashboard dengan 10+ metrics
- Mobile-responsive candidate portal
- Comprehensive test coverage (PHPUnit)

---

## 📈 Success Metrics & KPIs

### Efficiency Metrics:
- **Time-to-Hire**: Target 30 days (from application to offer)
- **Interview Scheduling Time**: < 24 hours
- **Test Completion Rate**: > 80%
- **Interview No-Show Rate**: < 10%

### Quality Metrics:
- **Candidate Satisfaction**: Survey score > 4/5
- **Hiring Manager Satisfaction**: > 4/5
- **New Hire Retention (90 days)**: > 85%

### System Performance:
- **Portal Load Time**: < 2 seconds
- **Test Platform Uptime**: 99.5%
- **Video Call Quality**: < 5% connection issues

---

## 💰 Cost Estimation

### Development Costs (Internal):
- **Developer Time**: 12 weeks × 40 hours = 480 hours
- **Rate**: (Internal resource, $0 external cost)

### Infrastructure Costs:
| Item | Monthly Cost | Annual Cost |
|------|--------------|-------------|
| Jitsi Server (included) | $0 | $0 |
| Zoom Pro (3 licenses, backup) | $45 | $540 |
| SMS Notifications (Twilio) | $20 | $240 |
| SSL Certificate | $0 (Let's Encrypt) | $0 |
| Additional Storage (if needed) | $0 | $0 |
| **TOTAL** | **$65/month** | **$780/year** |

### Third-party Services (Optional):
- **Anti-cheat Webcam Proctoring**: $1-2 per test session
- **Advanced Analytics (Mixpanel)**: $89/month
- **Video Recording Storage (AWS S3)**: ~$10/month

### ROI Calculation:
**Savings from automation:**
- HR time saved: 10 hours/week × $30/hour = $300/week = $1,200/month
- Improved hire quality: Reduced turnover saves ~$5,000 per prevented bad hire

**Payback Period**: < 1 month

---

## ⚠️ Risk Assessment & Mitigation

### Technical Risks:

| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| Server overload during video calls | High | Medium | Implement queue system, use Zoom fallback |
| Test cheating | Medium | High | Anti-cheat measures, manual review |
| Data breach (candidate info) | High | Low | Encryption at rest/transit, security audit |
| Jitsi downtime | Medium | Low | Automatic failover to Zoom |
| Browser compatibility issues | Low | Medium | Polyfill support, user agent detection |

### Business Risks:

| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| Low candidate adoption | High | Medium | User testing, training videos, support |
| Interviewer resistance | Medium | Medium | Change management, training sessions |
| Legal compliance (data protection) | High | Low | GDPR compliance review, consent forms |

---

## 🔒 Security & Compliance

### Data Protection:
- **Encryption**: TLS 1.3 for data in transit, AES-256 for data at rest
- **Access Control**: Role-based permissions (RBAC)
- **Audit Logs**: Track all access to candidate data
- **Data Retention**: Auto-delete after 2 years (configurable)
- **Consent Management**: Explicit opt-ins for recording, data processing

### GDPR Compliance:
- Right to access (candidate can download their data)
- Right to erasure (delete upon request)
- Data portability (export in JSON/CSV)
- Privacy policy updated

### Test Integrity:
- Unique session tokens (SHA-256 hashed)
- IP tracking (detect proxy usage)
- Tab switch monitoring
- Randomized question order
- Answer encryption before storage

---

## 📚 Training & Documentation

### Admin Training:
1. **Interview Scheduling** (30 min video tutorial)
2. **Test Template Creation** (45 min hands-on)
3. **Feedback Best Practices** (20 min module)
4. **Analytics Interpretation** (30 min webinar)

### Candidate Resources:
1. **Test Taking Tips** (blog article)
2. **Video Interview Best Practices** (infographic)
3. **Technical Test FAQ** (knowledge base)
4. **Troubleshooting Guide** (step-by-step)

---

## 🚀 Launch Checklist

### Pre-Launch (1 week before):
- [ ] Staging environment tested with real users
- [ ] Load testing completed (50 concurrent users)
- [ ] Security penetration test passed
- [ ] Backup & restore procedures verified
- [ ] Monitoring & alerting configured (Sentry, New Relic)
- [ ] Support team trained
- [ ] Documentation published

### Launch Day:
- [ ] Database migrations executed
- [ ] Feature flags enabled
- [ ] Announcement email sent to HR team
- [ ] Candidate portal links activated
- [ ] On-call developer assigned

### Post-Launch (1 week after):
- [ ] Daily metrics review
- [ ] Bug triage & hotfixes
- [ ] User feedback collection
- [ ] Performance optimization
- [ ] Celebrate success! 🎉

---

## 📞 Support & Maintenance

### Support Tiers:
1. **Tier 1**: Candidates (email support@bizmark.id, 24h response)
2. **Tier 2**: Admin users (Slack #recruitment-tech, 4h response)
3. **Tier 3**: System issues (on-call developer, 1h response)

### Maintenance Windows:
- **Weekly**: Sunday 02:00-04:00 WIB (minor updates)
- **Monthly**: Last Sunday 02:00-06:00 WIB (major updates)

---

## 🎯 Next Steps (Immediate Actions)

1. **Stakeholder Approval** (Week 0)
   - Present this document to management
   - Get budget approval ($780/year)
   - Assign project team

2. **Kick-off Meeting** (Week 1, Day 1)
   - Review roadmap with developers
   - Set up project management (Jira/Trello)
   - Create Git branch: `feature/advanced-recruitment`

3. **Start Phase 1** (Week 1, Day 2)
   - Begin database migrations
   - Set up testing environment
   - Daily stand-ups at 10:00 WIB

---

## 📄 Appendix

### A. Technology Stack
- **Backend**: Laravel 10.x, PHP 8.2
- **Frontend**: Blade templates, Alpine.js, Tailwind CSS
- **Video**: Jitsi Meet + Zoom SDK (fallback)
- **Testing**: PHPUnit, Laravel Dusk (E2E)
- **Monitoring**: Laravel Telescope, Sentry
- **Queue**: Redis (for email, notifications)
- **Storage**: Local (with S3 option for recordings)

### B. API Endpoints (Sample)
```
POST /api/recruitment/interview/schedule
GET  /api/recruitment/interview/{id}
PATCH /api/recruitment/interview/{id}/reschedule
POST /api/recruitment/test/assign
GET  /api/recruitment/test/session/{token}
POST /api/recruitment/test/submit-answer
```

### C. Database ERD
```
job_applications (existing)
    ├── recruitment_stages (1:N)
    ├── interview_schedules (1:N)
    │       └── interview_feedback (1:N)
    ├── test_sessions (1:N)
    │       └── test_answers (1:N)
    └── technical_test_submissions (1:N)

test_templates (master data)
    └── test_sessions (1:N)
```

---

**Document Status**: ✅ Ready for Review  
**Next Review Date**: December 1, 2025  
**Owner**: Development Team  
**Approver**: Management / HR Director
