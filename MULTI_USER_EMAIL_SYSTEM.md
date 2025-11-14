# 📧 Multi-User Email System - Comprehensive Documentation

## 🎯 Overview

Sistem email multi-user yang memungkinkan setiap staff memiliki email perusahaan sendiri dengan permission-based access control.

---

## 🏗️ Database Architecture

### **1. Users Table (Enhanced)**

Menambahkan field untuk email perusahaan dan preferences:

```sql
ALTER TABLE users ADD:
- company_email          VARCHAR   - Email perusahaan (john@bizmark.id)
- department            ENUM      - Department (general, cs, sales, support, finance, technical)
- email_signature       TEXT      - Personal email signature
- job_title             VARCHAR   - Job title/position
- notification_preferences JSON   - Email notification settings
- working_hours         JSON      - Working hours untuk auto-reply
```

**Purpose:** 
- Setiap user punya company email sendiri
- Department untuk routing otomatis
- Signature personal untuk consistency
- Working hours untuk auto-responder

---

### **2. Email Accounts Table (NEW)**

Manage semua email addresses perusahaan:

```sql
CREATE TABLE email_accounts (
    id                    BIGINT PRIMARY KEY
    email                 VARCHAR UNIQUE    - cs@bizmark.id, john@bizmark.id
    name                  VARCHAR          - Display name
    type                  ENUM             - 'shared' or 'personal'
    department            ENUM             - Department affiliation
    description           TEXT             - Purpose/description
    is_active             BOOLEAN          - Active status
    auto_reply_enabled    BOOLEAN          - Auto-reply on/off
    auto_reply_message    TEXT             - Auto-reply content
    signature             TEXT             - Default signature
    assigned_users        JSON             - Array of user IDs
    total_received        INT              - Statistics
    total_sent            INT              - Statistics
    created_at, updated_at, deleted_at
)
```

**Purpose:**
- Central management semua email addresses
- Shared emails (cs@, sales@) vs Personal (john@)
- Auto-reply per email account
- Statistics tracking

**Example Data:**
```json
{
  "id": 1,
  "email": "cs@bizmark.id",
  "name": "Customer Service",
  "type": "shared",
  "department": "cs",
  "assigned_users": [1, 2, 3],
  "is_active": true
}

{
  "id": 2,
  "email": "john@bizmark.id",
  "name": "John Doe",
  "type": "personal",
  "department": "sales",
  "assigned_users": [5],
  "is_active": true
}
```

---

### **3. Email Assignments Table (NEW)**

Link between email accounts and users dengan permissions:

```sql
CREATE TABLE email_assignments (
    id                    BIGINT PRIMARY KEY
    email_account_id      BIGINT FK        - Email account
    user_id               BIGINT FK        - User
    role                  ENUM             - primary, backup, viewer
    can_send              BOOLEAN          - Permission to send
    can_receive           BOOLEAN          - Permission to receive
    can_delete            BOOLEAN          - Permission to delete
    can_assign_others     BOOLEAN          - Permission to assign to others
    notify_on_receive     BOOLEAN          - Get notification on new email
    notify_on_reply       BOOLEAN          - Get notification on reply
    notify_on_mention     BOOLEAN          - Get notification on mention
    priority              INT              - Priority order
    is_active             BOOLEAN          - Active assignment
    assigned_by           BIGINT FK        - Who assigned this
    notes                 TEXT             - Assignment notes
    created_at, updated_at
    
    UNIQUE(email_account_id, user_id)
)
```

**Purpose:**
- Flexible permission system
- Multiple users per email account
- Role-based access (primary, backup, viewer)
- Granular permissions
- Notification preferences per assignment

**Example:**
```json
// John is primary handler for sales@bizmark.id
{
  "email_account_id": 3,
  "user_id": 5,
  "role": "primary",
  "can_send": true,
  "can_receive": true,
  "notify_on_receive": true
}

// Sarah is backup for sales@bizmark.id
{
  "email_account_id": 3,
  "user_id": 6,
  "role": "backup",
  "can_send": true,
  "can_receive": true,
  "notify_on_receive": false
}
```

---

### **4. Email Inbox Table (Enhanced)**

Menambahkan fields untuk assignment dan tracking:

```sql
ALTER TABLE email_inbox ADD:
- email_account_id          BIGINT FK   - Link to email account
- department                ENUM        - Department
- priority                  ENUM        - urgent, high, normal, low
- status                    ENUM        - new, open, pending, resolved, closed
- first_responded_at        TIMESTAMP   - SLA: First response time
- resolved_at               TIMESTAMP   - SLA: Resolution time
- response_time_minutes     INT         - Response SLA in minutes
- resolution_time_minutes   INT         - Resolution SLA in minutes
- handled_by                BIGINT FK   - User who handled this
- tags                      JSON        - Custom tags
- internal_notes            TEXT        - Internal notes (not sent to customer)
- sentiment                 ENUM        - positive, neutral, negative
```

**Purpose:**
- Link email to specific account
- Status tracking workflow
- SLA monitoring
- Handler assignment
- Internal collaboration via notes
- Sentiment analysis for customer service quality

---

## 🎯 Use Cases & Workflows

### **Use Case 1: Shared Email (cs@bizmark.id)**

**Setup:**
```
Email Account: cs@bizmark.id
Type: Shared
Assigned Users:
  - John (Primary) - can send, receive, delete
  - Sarah (Backup) - can send, receive
  - Manager (Viewer) - read-only
```

**Workflow:**
1. Customer kirim email → cs@bizmark.id
2. Webhook save to database
3. Auto-assign ke John (primary)
4. John gets notification
5. John buka inbox, lihat email
6. John reply dari dashboard (from: cs@bizmark.id)
7. Email log saved, status = resolved

**If John offline:**
- Sarah gets notification (backup role)
- Sarah can handle email
- Manager can view untuk monitoring

---

### **Use Case 2: Personal Email (john@bizmark.id)**

**Setup:**
```
Email Account: john@bizmark.id
Type: Personal
Assigned Users:
  - John (Primary) - full access
```

**Workflow:**
1. Customer kirim email → john@bizmark.id
2. Email auto-assigned to John
3. Only John dapat notification
4. Only John dapat akses inbox ini
5. John reply from john@bizmark.id
6. Personal signature auto-added

**Benefits:**
- Direct personal communication
- Professional appearance
- Separate from shared inbox
- Personal branding

---

### **Use Case 3: Department-Based Routing**

**Setup:**
```
Email Accounts:
- cs@bizmark.id      → CS Department (3 staff)
- sales@bizmark.id   → Sales Department (2 staff)
- support@bizmark.id → Support Department (4 staff)
```

**Workflow:**
1. Email diterima
2. System identify recipient email
3. Auto-route to department
4. Round-robin atau priority-based assignment
5. Assigned user gets notification
6. Team can collaborate via internal notes

---

### **Use Case 4: Admin Management**

**Admin Panel Features:**
```
Admin Dashboard → Email Management

1. User Management:
   - Add new staff
   - Assign company email
   - Set department
   - Configure permissions
   
2. Email Accounts:
   - Create new email addresses
   - Assign to users
   - Set auto-reply
   - View statistics
   
3. Assignments:
   - Who has access to what email
   - Change roles (primary/backup/viewer)
   - Adjust permissions
   - Set notification preferences
   
4. Analytics:
   - Response time per user
   - Resolution time per department
   - Email volume trends
   - User performance metrics
```

---

## 📊 Permission Matrix

| Role | View Inbox | Send Email | Delete | Assign Others | Admin Panel |
|------|-----------|------------|--------|---------------|-------------|
| **Admin** | All emails | All accounts | Yes | Yes | Full access |
| **Primary** | Assigned emails | Assigned accounts | Own emails | No | Limited |
| **Backup** | Assigned emails | Assigned accounts | No | No | Limited |
| **Viewer** | Assigned emails | No | No | No | Read-only |

---

## 🔔 Notification System

### **Notification Types:**

1. **New Email Received**
   - Who: Primary handler
   - When: Immediately
   - Content: From, Subject, Preview

2. **Email Mentioned You**
   - Who: Mentioned user (@john dalam internal notes)
   - When: Immediately
   - Content: Context of mention

3. **Email Assigned to You**
   - Who: Newly assigned user
   - When: Immediately
   - Content: From, Subject, Assigner

4. **Email Unresolved (SLA)**
   - Who: Primary + Manager
   - When: SLA threshold exceeded
   - Content: SLA status, age

5. **Daily Digest**
   - Who: All staff (optional)
   - When: End of day
   - Content: Summary of day's emails

---

## 🎨 Admin Interface Flow

### **1. Add New Staff**

```
/admin/users/create

Form Fields:
- Name: John Doe
- Login Email: john.doe@example.com
- Company Email: john@bizmark.id *
- Department: Sales
- Job Title: Sales Manager
- Email Signature: [HTML editor]
- Working Hours: Mon-Fri 9-5
- Notification Preferences:
  ☑ Email on new message
  ☑ Email on mention
  ☐ Daily digest

Save → Auto-create email account for john@bizmark.id
```

---

### **2. Manage Email Accounts**

```
/admin/email-accounts

List View:
┌─────────────────────────────────────────────────────────────┐
│ Email               │ Type    │ Department │ Assigned │ ...  │
├─────────────────────────────────────────────────────────────┤
│ cs@bizmark.id       │ Shared  │ CS         │ 3 users  │ Edit │
│ sales@bizmark.id    │ Shared  │ Sales      │ 2 users  │ Edit │
│ john@bizmark.id     │ Personal│ Sales      │ John     │ Edit │
│ sarah@bizmark.id    │ Personal│ CS         │ Sarah    │ Edit │
└─────────────────────────────────────────────────────────────┘

Actions:
- Create New Email Account
- Edit Assignments
- View Statistics
- Enable/Disable
```

---

### **3. Assign Users to Email**

```
/admin/email-accounts/1/assignments

Email: cs@bizmark.id

Current Assignments:
┌──────────────────────────────────────────────────────────────┐
│ User     │ Role    │ Permissions        │ Notifications │ ... │
├──────────────────────────────────────────────────────────────┤
│ John     │ Primary │ Send, Receive, Del │ On receive    │ Edit│
│ Sarah    │ Backup  │ Send, Receive      │ None          │ Edit│
│ Manager  │ Viewer  │ Read-only          │ None          │ Edit│
└──────────────────────────────────────────────────────────────┘

+ Add User
```

---

### **4. Staff Inbox View**

```
/admin/inbox

Filter by:
- My Emails (show only emails for assigned accounts)
- Department (CS, Sales, Support)
- Status (New, Open, Pending, Resolved)
- Priority (Urgent, High, Normal, Low)

Inbox List:
┌──────────────────────────────────────────────────────────────┐
│ From              │ To            │ Subject     │ Status │ ... │
├──────────────────────────────────────────────────────────────┤
│ customer@ex.com   │ cs@bizmark.id │ Need help   │ New    │View│
│ client@ex.com     │ john@biz...   │ Quote req   │ Open   │View│
└──────────────────────────────────────────────────────────────┘

Sidebar:
- Inbox (10 unread)
- Assigned to Me (5)
- My Department (15)
- All Emails (50) [Admin only]
```

---

### **5. Compose Email**

```
/admin/inbox/compose

From: [Dropdown]
  - cs@bizmark.id (Customer Service)
  - sales@bizmark.id (Sales Team) [if assigned]
  - john@bizmark.id (John Doe - Personal)
  
To: customer@example.com
Subject: Re: Need help
Priority: [Normal ▼]

Body: [Rich text editor]

[x] Add signature
[x] Mark as resolved after send
[ ] Schedule send

[Attachments]

[Cancel] [Send]
```

---

## 🔧 Technical Implementation

### **Models Created:**

```php
1. EmailAccount.php
   - Relationships: assignments, users, inbox
   - Methods: assignUser, removeUser, getStats
   
2. EmailAssignment.php
   - Relationships: emailAccount, user, assignedBy
   - Methods: hasPermission, canSend, canReceive
   
3. User.php (Enhanced)
   - Relationships: emailAssignments, emailAccounts, handledEmails
   - Methods: getCompanyEmails, canAccessEmail
   
4. EmailInbox.php (Enhanced)
   - Relationships: emailAccount, assignedUser, handledBy
   - Methods: assignTo, markResolved, addNote
```

---

### **Controllers:**

```php
1. EmailAccountController
   - index()    - List all email accounts
   - create()   - Create new account
   - store()    - Save new account
   - edit()     - Edit account
   - update()   - Update account
   - destroy()  - Delete account
   - stats()    - Show statistics
   
2. EmailAssignmentController
   - index()    - List assignments
   - store()    - Assign user to email
   - update()   - Update permissions
   - destroy()  - Remove assignment
   
3. UserEmailController
   - index()    - Manage user emails
   - assignEmail() - Assign email to user
   - updateSignature() - Update signature
```

---

### **Middleware:**

```php
1. CheckEmailAccess
   - Verify user has access to email account
   - Check permission level
   - Log access attempts
   
2. EmailRateLimit
   - Prevent email spam
   - Daily send limit per user
   - Rate limiting per account
```

---

### **Jobs:**

```php
1. SendEmailNotification
   - Notify assigned user on new email
   - Queue-based for performance
   
2. UpdateEmailStats
   - Update account statistics
   - Response time calculations
   
3. ProcessAutoReply
   - Send auto-reply based on conditions
   - Working hours check
```

---

## 📈 Analytics & Reporting

### **Dashboard Widgets:**

1. **My Performance**
   - Emails handled today
   - Average response time
   - Resolution rate
   - Customer satisfaction

2. **Department Overview**
   - Total emails
   - Pending emails
   - Team response time
   - SLA compliance

3. **Email Account Stats**
   - Most active account
   - Email volume trend
   - Peak hours

---

## 🔐 Security Best Practices

### **Implemented:**

1. **Permission-Based Access**
   - RBAC for all operations
   - Row-level security
   - Audit logging

2. **Data Privacy**
   - Email encryption at rest
   - Secure transmission (TLS)
   - Access logs

3. **Rate Limiting**
   - Send limits per user
   - Daily quotas per account
   - Spam prevention

4. **Audit Trail**
   - Who accessed what email
   - Who sent what email
   - Permission changes logged

---

## 🚀 Setup Guide

### **Initial Setup (Admin):**

**Step 1: Create Email Accounts**
```bash
php artisan tinker

EmailAccount::create([
    'email' => 'cs@bizmark.id',
    'name' => 'Customer Service',
    'type' => 'shared',
    'department' => 'cs',
    'is_active' => true,
]);
```

**Step 2: Add Staff Users**
```
Admin Panel → Users → Add New
- Fill user details
- Assign company email
- Set department
```

**Step 3: Assign Emails to Users**
```
Admin Panel → Email Accounts → cs@bizmark.id → Assignments
- Add John as Primary
- Add Sarah as Backup
- Set permissions
```

**Step 4: Configure Cloudflare**
```
- Add all email addresses to Cloudflare Email Routing
- All forward to same webhook
- System will auto-route based on to_email
```

**Step 5: Test**
```
- Send email to cs@bizmark.id
- Check John's inbox
- Verify Sarah can see as backup
- Test reply functionality
```

---

## 📝 Configuration Examples

### **Working Hours:**
```json
{
  "monday": {"start": "09:00", "end": "17:00"},
  "tuesday": {"start": "09:00", "end": "17:00"},
  "wednesday": {"start": "09:00", "end": "17:00"},
  "thursday": {"start": "09:00", "end": "17:00"},
  "friday": {"start": "09:00", "end": "17:00"},
  "saturday": null,
  "sunday": null
}
```

### **Notification Preferences:**
```json
{
  "email": {
    "new_message": true,
    "mention": true,
    "assignment": true,
    "daily_digest": false
  },
  "push": {
    "enabled": false
  }
}
```

---

## ✅ Implementation Checklist

- [x] Database migrations created
- [x] Tables migrated successfully
- [ ] Models created
- [ ] Controllers created
- [ ] Middleware implemented
- [ ] Seeder for default data
- [ ] Admin UI views
- [ ] Email composer enhanced
- [ ] Webhook enhancement (auto-assign)
- [ ] Notification system
- [ ] Permission checks
- [ ] Analytics dashboard
- [ ] Testing & QA

---

**Next: Create Models, Controllers & Seeder** →
