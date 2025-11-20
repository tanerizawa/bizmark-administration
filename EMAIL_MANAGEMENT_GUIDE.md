# 📧 Email Management System - User Guide

## Overview

Sistem manajemen email BizMark.id memungkinkan Anda untuk:
- Mengelola akun email perusahaan (IMAP/SMTP)
- Assign email ke user tertentu
- Mengatur permission per user (read, send, delete, manage)
- Mengelola inbox, campaigns, subscribers, dan templates
- Tracking statistik penggunaan email

---

## 🎯 Fitur Utama

### 1. **Email Accounts Management**
**Lokasi:** Admin Panel → Email Management → Email Accounts  
**Route:** `/admin/email-accounts`

**Fungsi:**
- Tambah akun email baru (personal/shared/support)
- Konfigurasi IMAP/SMTP settings
- Set quota dan rate limits
- Monitoring status koneksi
- Assign user ke email account
- Manage permissions (read, send, delete, manage)

**Statistik:**
- Total accounts
- Shared accounts
- Active accounts  
- Inactive accounts

**Filters:**
- Search by email/name/department
- Filter by type (personal/shared/support)
- Filter by department
- Filter by status (active/inactive)

---

### 2. **Inbox Management**
**Lokasi:** Admin Panel → Email Management → Inbox  
**Route:** `/admin/inbox`

**Fungsi:**
- View incoming emails dari assigned accounts
- Reply to emails
- Compose new emails
- Delete emails
- Filter by account/folder/status
- Unread count badge

---

### 3. **Email Campaigns**
**Lokasi:** Admin Panel → Email Management → Campaigns  
**Route:** `/admin/campaigns`

**Fungsi:**
- Buat email campaigns (newsletters, announcements)
- Schedule sending
- Select recipients (subscribers, clients, custom lists)
- Use templates
- Track campaign metrics (opens, clicks, bounces)
- A/B testing (planned)

---

### 4. **Subscribers Management**
**Lokasi:** Admin Panel → Email Management → Subscribers  
**Route:** `/admin/subscribers`

**Fungsi:**
- Manage email subscribers
- Import/export subscriber lists
- Segmentation (by interest, status, etc)
- Subscription preferences
- Unsubscribe management
- Active subscribers count badge

---

### 5. **Email Templates**
**Lokasi:** Admin Panel → Email Management → Templates  
**Route:** `/admin/templates`

**Fungsi:**
- Create reusable email templates
- Categories (marketing, transactional, notification)
- Template variables ({{name}}, {{company}}, etc)
- Preview and test
- Version control

---

### 6. **Email Settings**
**Lokasi:** Admin Panel → Email Management → Email Settings  
**Route:** `/admin/email/settings`

**Fungsi:**
- SMTP configuration (global fallback)
- Email branding (logo, colors, footer)
- Default sender name/email
- Rate limiting
- Bounce handling
- Webhook settings

---

## 📋 How to Use

### A. Menambah Email Account

1. **Navigate:** Admin Panel → Email Management → Email Accounts
2. **Click:** "New Email Account" button (top-right)
3. **Fill Form:**
   ```
   Email Address: support@bizmark.id
   Account Name: Support Team
   Type: Shared
   Department: Customer Support
   
   IMAP Settings:
   - Host: mail.bizmark.id
   - Port: 993
   - Encryption: SSL
   - Username: support@bizmark.id
   - Password: ********
   
   SMTP Settings:
   - Host: mail.bizmark.id
   - Port: 465
   - Encryption: SSL
   - Username: support@bizmark.id
   - Password: ********
   
   Quota: 5 GB
   Daily Limit: 500 emails
   ```
4. **Save:** Klik "Create Email Account"
5. **Test Connection:** Sistem akan test IMAP/SMTP connection

### B. Assign User ke Email Account

**Method 1: Single Assignment**
1. Go to Email Account detail page
2. Click "Assign User" button
3. Select user from dropdown
4. Set permissions:
   - ✅ **Read**: View emails
   - ✅ **Send**: Send emails from this account
   - ✅ **Delete**: Delete emails
   - ⬜ **Manage**: Manage account settings
5. Click "Assign"

**Method 2: Bulk Assignment**
1. Go to Email Account detail page
2. Click "Bulk Assign" button
3. Select multiple users (checkboxes)
4. Set default permissions for all
5. Click "Assign All"

### C. Transfer Primary User

Jika email account adalah **personal** type, dapat transfer ownership:
1. Go to Email Account detail page
2. Find current primary user
3. Click "Transfer Primary" button
4. Select new primary user
5. Confirm transfer

### D. Mengirim Email Campaign

1. Go to: Email Management → Campaigns
2. Click "New Campaign"
3. Fill details:
   ```
   Campaign Name: Q1 2025 Newsletter
   Subject: Exciting Updates from BizMark.id
   From: marketing@bizmark.id (select from email accounts)
   
   Recipients:
   [x] All Subscribers
   [ ] Specific Segment: _________
   [ ] Custom List
   
   Template: Marketing Newsletter v2
   
   Schedule:
   ( ) Send Immediately
   (•) Schedule for Later: 2025-01-15 09:00 AM
   ```
4. Preview and Test
5. Click "Schedule Campaign"

---

## 🔐 Permission Levels

| Permission | Description | Actions Allowed |
|-----------|-------------|-----------------|
| **Read** | View emails only | - Read inbox<br>- View sent items<br>- Search emails |
| **Send** | Send emails | Everything in Read +<br>- Compose new emails<br>- Reply to emails<br>- Forward emails |
| **Delete** | Delete emails | Everything in Send +<br>- Delete emails<br>- Empty trash<br>- Bulk delete |
| **Manage** | Full control | Everything in Delete +<br>- Edit account settings<br>- Assign/unassign users<br>- Manage permissions<br>- Delete account |

**Note:** Only admin users can access Email Accounts Management page.

---

## 📊 Statistics Dashboard

Email Accounts index page menampilkan:

1. **Total Accounts**: Semua email accounts di system
2. **Shared Accounts**: Email shared (multi-user)
3. **Active Accounts**: Email dengan status active
4. **Inactive Accounts**: Email disabled/archived

Setiap card clickable untuk filter langsung.

---

## 🚨 Troubleshooting

### Email Account Connection Failed

**Symptom:** Red "Connection Failed" badge di account list  
**Solutions:**
1. Verify IMAP/SMTP credentials
2. Check host/port configuration
3. Ensure firewall allows connections
4. Test credentials in email client (Thunderbird, Outlook)
5. Check email provider settings (some need app-specific passwords)

### User Can't Send Emails

**Symptom:** User sees "Permission Denied" error  
**Solutions:**
1. Check if user has **Send** permission
2. Verify email account is **active**
3. Check daily limit not exceeded
4. Verify SMTP credentials still valid

### Emails Not Showing in Inbox

**Symptom:** New emails not appearing  
**Solutions:**
1. Check IMAP connection status
2. Verify user has **Read** permission
3. Check folder filters (might be in Spam/Archive)
4. Manually sync: Click refresh button
5. Check quota not exceeded

### Campaign Not Sending

**Symptom:** Scheduled campaign stuck in "Pending"  
**Solutions:**
1. Check Laravel Queue is running:
   ```bash
   php artisan queue:work
   ```
2. Verify SMTP settings on sender email account
3. Check scheduled time is in future (not past)
4. Look at Laravel logs: `storage/logs/laravel.log`

---

## 🔧 Technical Details

### Database Tables

```sql
-- Email accounts (⚠️ Note: uses is_active boolean, NOT status varchar)
email_accounts (id, email, name, type, department, description, is_active, auto_reply_enabled, signature, assigned_users)

-- User assignments
email_assignments (id, email_account_id, user_id, permissions, is_primary)

-- Email logs
email_logs (id, email_account_id, user_id, type, subject, status, sent_at)

-- Campaigns
email_campaigns (id, name, subject, from_account_id, recipients, template_id, scheduled_at, status)

-- Subscribers
email_subscribers (email, name, status, subscribed_at, unsubscribed_at)

-- Templates
email_templates (id, name, category, subject, body, variables)
```

### Models & Relationships

```php
// EmailAccount
hasMany: emailLogs
belongsToMany: users (through email_assignments pivot)

// User
belongsToMany: emailAccounts (through email_assignments pivot)

// EmailAssignment (pivot)
belongsTo: emailAccount
belongsTo: user
```

### Routes

```php
// Resource routes
Route::resource('email-accounts', EmailAccountController::class);

// Custom routes
GET  /admin/email-accounts/{account}/available-users
GET  /admin/email-accounts-stats
POST /admin/email-accounts/{account}/assign
POST /admin/email-accounts/{account}/bulk-assign
DELETE /admin/email-accounts/{account}/unassign/{user}
PATCH /admin/email-accounts/{account}/permissions/{user}
POST /admin/email-accounts/{account}/transfer-primary
```

### Permissions Check (Middleware)

```php
// In EmailAccountController
public function __construct()
{
    $this->middleware(['auth', 'role:admin']);
}

// User access check
if (!$user->can('send', $emailAccount)) {
    abort(403, 'You do not have permission to send from this account');
}
```

---

## 📝 Best Practices

### Security

1. **Use App-Specific Passwords**: Jangan gunakan main password untuk IMAP/SMTP
2. **Rotate Credentials**: Update passwords setiap 90 hari
3. **Minimal Permissions**: Beri permission sesuai kebutuhan
4. **Monitor Logs**: Review email_logs untuk suspicious activity
5. **Rate Limiting**: Set daily_limit untuk prevent spam

### Organization

1. **Naming Convention**: 
   - Personal: `firstname.lastname@bizmark.id`
   - Shared: `department@bizmark.id` (support, info, sales)
   - Support: `noreply@bizmark.id`, `system@bizmark.id`

2. **Departments**: Consistent naming (Support, Sales, Marketing, Finance)

3. **Templates**: Categorize by purpose (Marketing, Transactional, Notification)

### Performance

1. **Pagination**: List view paginated (20 per page)
2. **Eager Loading**: Uses `with(['users', 'assignments'])` to prevent N+1
3. **Caching**: Stats cached for 5 minutes
4. **Queue**: Email sending uses Laravel Queue (async)

---

## 🆕 Recent Changes

### Version 2.0 (Current)

✅ **Added Email Accounts menu to sidebar**
- Location: Email Management section
- Shows active accounts count badge
- Icon: `fa-at` (@ symbol)

✅ **Fixed route highlighting**
- Active state when on any `admin.email-accounts.*` route
- Blue background + white text when active

✅ **Optimized sidebar query**
- Uses direct Eloquent query instead of stats() method
- Better performance for real-time badge update

---

## 🎓 Training Checklist

### For Admin Users

- [ ] Add email account (personal & shared)
- [ ] Test IMAP/SMTP connection
- [ ] Assign users to email accounts
- [ ] Set permissions (read, send, delete, manage)
- [ ] Bulk assign users
- [ ] Transfer primary ownership
- [ ] Create email template
- [ ] Create and send campaign
- [ ] Manage subscribers
- [ ] Configure email settings

### For Regular Users

- [ ] Access assigned email accounts
- [ ] Read emails in inbox
- [ ] Send emails (if has permission)
- [ ] Reply to emails
- [ ] Use email templates
- [ ] Check sent emails
- [ ] Manage signature

---

## 📞 Support

**Questions?** Contact:
- **Email:** support@bizmark.id
- **Internal Slack:** #bizmark-support
- **Documentation:** [BizMark Wiki](https://wiki.bizmark.id)

**Bug Reports:** Create issue di GitHub atau email dev team

---

**Last Updated:** 2025-01-09  
**Version:** 2.0  
**Maintained by:** BizMark.id Development Team
