# 🚀 Recruitment System - Quick Start Guide

**Version**: 1.0.0  
**Last Updated**: November 23, 2025  
**Status**: ✅ Phase 1 Complete (Interview Management Ready)

---

## 📋 Table of Contents
1. [System Access](#system-access)
2. [Admin: Schedule Interview](#admin-schedule-interview)
3. [Admin: View Calendar](#admin-view-calendar)
4. [Candidate: Join Interview](#candidate-join-interview)
5. [Admin: Create Test](#admin-create-test)
6. [Candidate: Take Test](#candidate-take-test)
7. [Troubleshooting](#troubleshooting)

---

## 🔐 System Access

### **Admin Panel** (HR/Recruitment Team)
**URL**: `https://bizmark.id/admin/recruitment`

**Requirements**:
- ✅ Login with admin account
- ✅ Permission: `recruitment.manage`

**Navigation**:
```
Dashboard → Admin → Recruitment
├── Interviews (Calendar view)
├── Tests (Template management)
└── Pipeline (Application tracking)
```

### **Candidate Portal** (No Login Required)
**Access Method**: Via unique link sent by email or SMS

**Example**:
```
https://bizmark.id/candidate/interview/AbCdEf123456...
https://bizmark.id/candidate/test/XyZ789...
```

---

## 📅 Admin: Schedule Interview

### **Step 1: Navigate to Interview Calendar**
1. Login to admin panel
2. Go to **Admin → Recruitment → Interviews**
3. Click **"Schedule Interview"** button (top-right)

### **Step 2: Fill Interview Form**
| Field | Description | Example |
|-------|-------------|---------|
| **Candidate** | Select from dropdown | John Doe - Marketing Manager |
| **Date & Time** | When interview will happen | 2025-11-25 10:00 |
| **Duration** | How long (minutes) | 45 minutes |
| **Type** | Video/Phone/In-Person/Panel | Video Conference |
| **Meeting Link** | Leave empty for auto-generate | (auto: Jitsi room) |
| **Interviewers** | Select 1 or more (Ctrl+click) | Sarah HR, Mike CEO |
| **Notes** | Internal preparation notes | Focus on marketing campaigns |

### **Step 3: Submit & Share**
1. Click **"Schedule Interview"**
2. System generates unique candidate link
3. **Copy link** from interview detail page
4. Send to candidate via email/WhatsApp

**Example Link**:
```
https://bizmark.id/candidate/interview/abc123def456...
```

### **Auto-Generated Features** ✨
- ✅ Unique access token (64 characters)
- ✅ Jitsi meeting room (if video)
- ✅ Calendar event (.ics file - coming in Phase 2)
- ✅ Email notification (coming in Phase 2)

---

## 📆 Admin: View Calendar

### **Calendar Features**
1. **Month View**: See all interviews at a glance
2. **Week View**: Detailed weekly schedule
3. **Day View**: Hour-by-hour breakdown
4. **Today's Sidebar**: Quick view of today's interviews

### **Color Coding**
- 🔵 **Blue**: Scheduled (upcoming)
- 🟢 **Green**: Completed
- 🔴 **Red**: Cancelled
- 🟠 **Orange**: Rescheduled

### **Quick Actions**
- **Click Empty Slot**: Create new interview
- **Click Event**: View details
- **Drag Event**: Reschedule (coming soon)

### **Statistics Dashboard**
- Today's Interviews: X
- Upcoming: Y
- Completed: Z
- Cancelled: W

---

## 👤 Candidate: Join Interview

### **Step 1: Open Interview Link**
Candidate receives link from HR:
```
https://bizmark.id/candidate/interview/abc123...
```

### **Step 2: View Interview Details**
Candidate sees:
- ✅ Date & Time
- ✅ Duration
- ✅ Interview Type
- ✅ Countdown Timer
- ✅ Preparation Tips

### **Step 3: Join Meeting**
**Timing**:
- ⏰ **15 minutes before** → Join button appears
- ✅ **During interview** → Join button enabled
- ❌ **After interview ends** → Join button disabled

**Click**: **"Join Interview Now"** button → Redirects to video meeting

### **Video Conference Platforms**
- **Jitsi** (default, self-hosted) - No account needed
- **Zoom** (if custom link provided)
- **Google Meet** (if custom link provided)

### **Reschedule Request** (Optional)
If candidate can't attend:
1. Click **"Request Reschedule"** button
2. Enter reason
3. Suggest 1-3 alternative dates
4. Submit → HR receives notification

**Rules**:
- ⚠️ Must request **24 hours** before interview
- ⚠️ After 24h window → Contact HR directly

---

## 📝 Admin: Create Test

### **Step 1: Navigate to Tests**
1. Admin → Recruitment → Tests
2. Click **"Create Test Template"**

### **Step 2: Fill Template Details**
| Field | Description | Example |
|-------|-------------|---------|
| **Title** | Test name | General Psychology Test |
| **Description** | What test measures | Personality & cognitive assessment |
| **Type** | Category | Psychology, Psychometric, Technical |
| **Duration** | Time limit (minutes) | 60 |
| **Passing Score** | Minimum to pass (%) | 70 |

### **Step 3: Add Questions**
For each question:
1. **Question Text**: "What is your management style?"
2. **Question Type**: Multiple Choice / True-False / Essay / Rating
3. **Options** (if MCQ): A, B, C, D
4. **Correct Answer**: B
5. **Points**: 10

**Example JSON** (auto-generated):
```json
{
  "id": 1,
  "question_text": "What is 2+2?",
  "question_type": "multiple-choice",
  "options": ["2", "3", "4", "5"],
  "correct_answer": "4",
  "points": 5
}
```

### **Step 4: Save & Assign**
1. Click **"Save Template"**
2. Go to candidate's application
3. Click **"Assign Test"**
4. Select template
5. Set expiry date (when test link expires)
6. Submit → Candidate receives link

---

## ✍️ Candidate: Take Test

### **Step 1: Open Test Link**
```
https://bizmark.id/candidate/test/xyz789...
```

### **Step 2: Read Instructions**
Candidate sees:
- ✅ Test title & description
- ✅ Number of questions
- ✅ Time limit
- ✅ Passing score
- ✅ Rules (no tab switching)

### **Step 3: Start Test**
1. Click **"Start Test"**
2. Timer begins immediately
3. Answer questions one by one

### **Test Interface Features**
- ✅ **Auto-save**: Answers saved automatically every 5 seconds
- ✅ **Progress bar**: "Question 3 of 20 (15%)"
- ✅ **Timer**: Countdown in top-right corner
- ✅ **Navigation**: Previous/Next buttons

### **Anti-Cheat System** 🛡️
- ⚠️ **Tab switching detected** → Warning (max 5)
- ⚠️ **Timer expires** → Auto-submit test
- ⚠️ **Refresh page** → Resumes where left off

### **Step 4: Submit Test**
1. Answer all questions (or skip)
2. Click **"Complete Test"**
3. Confirm submission
4. See completion message
5. HR reviews results

---

## 🔍 Admin: View Results

### **Interview Results**
1. Go to Interview detail page
2. Click **"Feedback"** tab
3. See all interviewer assessments:
   - Technical Score (1-10)
   - Communication Score (1-10)
   - Overall Rating (average)
   - Recommendation (Strong Hire / Hire / Maybe / No-Hire)
   - Notes

### **Test Results**
1. Go to Test → Sessions
2. Click session to view results
3. See:
   - Final Score (%)
   - Pass/Fail status
   - Time taken
   - Answer breakdown
   - Tab switches (anti-cheat)

### **Pipeline View**
1. Go to Recruitment → Pipeline
2. See candidate progress:
   - Stage 1: Screening ✅ Passed (85%)
   - Stage 2: Testing 🔄 In Progress
   - Stage 3: Interview ⏳ Not Started
   - Stage 4: Offer ⏳ Not Started

---

## 🛠️ Troubleshooting

### **Problem: "Interview link not working"**
**Symptoms**: 404 error or "Interview not found"

**Solutions**:
1. ✅ Check link is complete (64 character token)
2. ✅ Verify interview hasn't been deleted
3. ✅ Check link hasn't expired (7 days after interview)
4. ✅ Contact HR for new link

---

### **Problem: "Can't join video meeting"**
**Symptoms**: Join button disabled or link doesn't work

**Solutions**:
1. ✅ Check current time (must be 15 min before → after interview)
2. ✅ Verify meeting link is valid
3. ✅ Try different browser (Chrome recommended)
4. ✅ Check internet connection
5. ✅ Allow camera/microphone permissions

---

### **Problem: "Test timer expired"**
**Symptoms**: Test auto-submitted before finishing

**Solutions**:
1. ✅ Test duration is strict (e.g., 60 minutes)
2. ✅ Timer starts when you click "Start Test"
3. ✅ Contact HR to request re-test (if technical issue)

**Prevention**:
- Start test when you have full time available
- Check internet connection before starting
- Close other applications

---

### **Problem: "Too many tab switches"**
**Symptoms**: Test marked as "Flagged" or auto-submitted

**Explanation**:
- System detects when you switch away from test tab
- Maximum 5 switches allowed (anti-cheat)
- Designed to ensure test integrity

**Solutions**:
1. ✅ Take test seriously (don't browse other tabs)
2. ✅ If legitimate reason (connection issue), explain to HR
3. ✅ HR can manually review flagged tests

---

### **Problem: "Admin can't see recruitment menu"**
**Symptoms**: No "Recruitment" option in admin panel

**Solutions**:
1. ✅ Check you're logged in as admin
2. ✅ Verify you have `recruitment.manage` permission
3. ✅ Contact system administrator to grant permission

**Permission Check**:
```php
// Admin should have this in database
permissions: ['recruitment.manage']
```

---

## 📊 System Limits

| Feature | Limit | Reason |
|---------|-------|--------|
| Interview duration | 15 min - 8 hours | Reasonable range |
| Test duration | 5 min - 8 hours | Prevents misuse |
| Access token expiry | 7 days after event | Security |
| Tab switches (test) | Maximum 5 | Anti-cheat |
| Test questions | 1 - 100 | Performance |
| Interviewers per interview | 1 - 10 | Practical limit |

---

## 🎯 Best Practices

### **For HR/Admin**
1. ✅ Schedule interviews **at least 24 hours** in advance
2. ✅ Send candidate link **immediately** after scheduling
3. ✅ Add **internal notes** for interviewers
4. ✅ Assign **multiple interviewers** for important roles
5. ✅ Review **test results within 48 hours**

### **For Candidates**
1. ✅ Test camera/microphone **before** joining
2. ✅ Join **5-10 minutes early** to troubleshoot
3. ✅ Find a **quiet location** with good lighting
4. ✅ Keep interview link **accessible** (bookmark)
5. ✅ Complete tests **in one sitting** (no breaks)

### **For Interviewers**
1. ✅ Review candidate CV **before interview**
2. ✅ Check **internal notes** from HR
3. ✅ Submit **feedback within 24 hours**
4. ✅ Be specific in **assessment notes**

---

## 📞 Support Contacts

### **Technical Issues**
- **Email**: tech@bizmark.id
- **Phone**: [Your IT Support Number]

### **HR Questions**
- **Email**: hr@bizmark.id
- **Phone**: [Your HR Number]

### **Emergency** (Interview Day)
- **WhatsApp**: [Your Emergency Number]

---

## 🔄 Coming Soon (Phase 2)

### **Email Notifications** 📧
- Interview scheduled → Candidate receives email + .ics calendar
- 24h reminder → "Your interview is tomorrow"
- Interview rescheduled → Notification to all parties
- Test assigned → Link sent via email

### **Advanced Features** 🚀
- Video conference recording
- Automated interview scoring (AI)
- Bulk interview scheduling
- Interview templates
- Mobile app

---

## 📚 Additional Resources

### **Video Tutorials** (Coming Soon)
- How to schedule an interview (5 min)
- How to create a test (10 min)
- How to review candidate pipeline (7 min)

### **Documentation**
- **Full Technical Docs**: `RECRUITMENT_PHASE1_IMPLEMENTATION_COMPLETE.md`
- **System Analysis**: `RECRUITMENT_ADVANCED_SYSTEM_ANALYSIS.md`
- **Visual Roadmap**: `RECRUITMENT_VISUAL_ROADMAP.md`

---

## ✅ System Health Check

Run these checks to ensure system is working:

### **Database Check**
```bash
php artisan migrate:status
# All recruitment tables should show "Ran"
```

### **Route Check**
```bash
php artisan route:list --name=recruitment
# Should show 17+ routes
```

### **Permission Check**
```bash
php artisan permission:show recruitment.manage
# Should show users with this permission
```

---

**Last Updated**: November 23, 2025  
**Version**: 1.0.0  
**Status**: ✅ Production Ready (Phase 1)
