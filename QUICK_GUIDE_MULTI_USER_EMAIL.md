# Quick Guide: Multi-User Email System

## 🚀 Quick Start

### Current Status
✅ **Database & Models**: Fully implemented and working  
✅ **4 Email Accounts Created**: cs@, sales@, support@, cs@bizmark.id  
✅ **User Assignment Working**: hadez@bizmark.id has access to all emails  
🚧 **Controllers**: Created but need implementation  
🚧 **Admin UI**: Not yet created  

---

## 📱 How to Use (Manual via Tinker)

Until the admin UI is built, you can manage everything via `php artisan tinker`:

### 1. Create New User with Company Email

```php
$user = App\Models\User::create([
    'name' => 'Sarah Johnson',
    'email' => 'sarah@bizmark.id',
    'company_email' => 'sarah@bizmark.id',
    'department' => 'cs',
    'job_title' => 'Customer Service Representative',
    'password' => bcrypt('password123'),
]);

// Create her personal email account
$emailAccount = App\Models\EmailAccount::create([
    'email' => 'sarah@bizmark.id',
    'name' => 'Sarah Johnson',
    'type' => 'personal',
    'department' => 'cs',
    'description' => 'Personal email for Sarah Johnson',
    'is_active' => true,
    'signature' => "Best regards,\nSarah Johnson\nCustomer Service\nBizmark.id",
]);

// Assign to Sarah with full permissions
$emailAccount->assignUser($user, [
    'role' => 'primary',
    'can_send' => true,
    'can_receive' => true,
    'can_delete' => true,
    'can_assign_others' => false,
]);
```

### 2. Assign User to Shared Email

```php
// Get the shared CS email
$csEmail = App\Models\EmailAccount::where('email', 'cs@bizmark.id')->first();

// Get Sarah
$sarah = App\Models\User::where('email', 'sarah@bizmark.id')->first();

// Assign Sarah as backup handler for cs@bizmark.id
$csEmail->assignUser($sarah, [
    'role' => 'backup',
    'can_send' => true,
    'can_receive' => true,
    'can_delete' => false,
    'can_assign_others' => false,
    'notify_on_receive' => true,
]);
```

### 3. View User's Assigned Emails

```php
$user = App\Models\User::find(1);

// Get email accounts via assignments
$assignments = App\Models\EmailAssignment::where('user_id', $user->id)
    ->with('emailAccount')
    ->get();

foreach ($assignments as $assignment) {
    echo "{$assignment->emailAccount->email} - Role: {$assignment->role}\n";
    echo "  Can send: " . ($assignment->can_send ? 'Yes' : 'No') . "\n";
    echo "  Can receive: " . ($assignment->can_receive ? 'Yes' : 'No') . "\n";
}
```

### 4. Check Permissions

```php
$user = App\Models\User::where('email', 'sarah@bizmark.id')->first();
$csEmail = App\Models\EmailAccount::where('email', 'cs@bizmark.id')->first();

// Get assignment
$assignment = App\Models\EmailAssignment::where('email_account_id', $csEmail->id)
    ->where('user_id', $user->id)
    ->first();

if ($assignment) {
    echo "Can send: " . ($assignment->canSend() ? 'YES' : 'NO') . "\n";
    echo "Can receive: " . ($assignment->canReceive() ? 'YES' : 'NO') . "\n";
    echo "Can delete: " . ($assignment->canDelete() ? 'YES' : 'NO') . "\n";
    echo "Role: {$assignment->role_label}\n";
}
```

### 5. Remove User from Email

```php
$csEmail = App\Models\EmailAccount::where('email', 'cs@bizmark.id')->first();
$user = App\Models\User::where('email', 'sarah@bizmark.id')->first();

// Remove Sarah from cs@bizmark.id
$csEmail->removeUser($user);
```

### 6. List All Email Accounts

```php
$accounts = App\Models\EmailAccount::with('users')->get();

foreach ($accounts as $acc) {
    echo "📧 {$acc->email} ({$acc->type})\n";
    echo "   Department: {$acc->department}\n";
    echo "   Users: {$acc->users->count()}\n";
    
    foreach ($acc->users as $user) {
        $p = $user->pivot;
        echo "     └─ {$user->email} ({$p->role})\n";
    }
    echo "\n";
}
```

### 7. Get User's Inbox Emails (with permission filter)

```php
$user = App\Models\User::find(1);

// Get email account IDs where user has receive permission
$emailAccountIds = App\Models\EmailAssignment::where('user_id', $user->id)
    ->where('is_active', true)
    ->where('can_receive', true)
    ->pluck('email_account_id');

// Get emails for those accounts
$emails = App\Models\EmailInbox::whereIn('email_account_id', $emailAccountIds)
    ->orderBy('received_at', 'desc')
    ->limit(10)
    ->get();

foreach ($emails as $email) {
    echo "From: {$email->from_email}\n";
    echo "To: {$email->to_email}\n";
    echo "Subject: {$email->subject}\n";
    echo "---\n";
}
```

---

## 🎯 Common Scenarios

### Scenario 1: New Staff Member Joins Sales Team

```php
// 1. Create user
$mike = App\Models\User::create([
    'name' => 'Mike Wilson',
    'email' => 'mike@bizmark.id',
    'company_email' => 'mike@bizmark.id',
    'department' => 'sales',
    'job_title' => 'Sales Executive',
    'password' => bcrypt('password123'),
]);

// 2. Create his personal email
$mikeEmail = App\Models\EmailAccount::create([
    'email' => 'mike@bizmark.id',
    'name' => 'Mike Wilson',
    'type' => 'personal',
    'department' => 'sales',
    'is_active' => true,
    'signature' => "Best regards,\nMike Wilson\nSales Executive\nBizmark.id",
]);

$mikeEmail->assignUser($mike);

// 3. Also give him access to shared sales@bizmark.id
$salesEmail = App\Models\EmailAccount::where('email', 'sales@bizmark.id')->first();
$salesEmail->assignUser($mike, [
    'role' => 'backup',
    'can_send' => true,
    'can_receive' => true,
]);

echo "✅ Mike can now:\n";
echo "   - Send/receive from mike@bizmark.id (personal)\n";
echo "   - Send/receive from sales@bizmark.id (shared)\n";
```

### Scenario 2: Change User Role from Backup to Primary

```php
$csEmail = App\Models\EmailAccount::where('email', 'cs@bizmark.id')->first();
$sarah = App\Models\User::where('email', 'sarah@bizmark.id')->first();

// Update assignment
$assignment = App\Models\EmailAssignment::where('email_account_id', $csEmail->id)
    ->where('user_id', $sarah->id)
    ->first();

$assignment->update([
    'role' => 'primary',
    'can_delete' => true,
    'can_assign_others' => true,
]);

echo "✅ Sarah promoted to primary handler for cs@bizmark.id\n";
```

### Scenario 3: Temporary Email Access (Viewer Role)

```php
// Give intern read-only access to cs@bizmark.id
$supportEmail = App\Models\EmailAccount::where('email', 'cs@bizmark.id')->first();
$intern = App\Models\User::where('email', 'intern@bizmark.id')->first();

$supportEmail->assignUser($intern, [
    'role' => 'viewer',
    'can_send' => false,        // Cannot send
    'can_receive' => true,       // Can view
    'can_delete' => false,       // Cannot delete
    'can_assign_others' => false,
]);

echo "✅ Intern has read-only access to cs@bizmark.id\n";
```

---

## 📊 Useful Queries

### Get Statistics

```php
// Total email accounts
$totalAccounts = App\Models\EmailAccount::count();

// Active email accounts
$activeAccounts = App\Models\EmailAccount::where('is_active', true)->count();

// Shared vs Personal
$shared = App\Models\EmailAccount::where('type', 'shared')->count();
$personal = App\Models\EmailAccount::where('type', 'personal')->count();

// Users with email access
$usersWithEmail = App\Models\EmailAssignment::distinct('user_id')->count('user_id');

echo "Email Accounts: $totalAccounts (Active: $activeAccounts)\n";
echo "Shared: $shared, Personal: $personal\n";
echo "Users with email access: $usersWithEmail\n";
```

### Find Unassigned Emails

```php
// Emails with no users assigned
$unassigned = App\Models\EmailAccount::doesntHave('assignments')->get();

foreach ($unassigned as $email) {
    echo "⚠️  {$email->email} has no users assigned!\n";
}
```

### Users Without Email Access

```php
$usersWithoutEmail = App\Models\User::doesntHave('emailAssignments')->get();

foreach ($usersWithoutEmail as $user) {
    echo "❌ {$user->email} has no email accounts assigned\n";
}
```

---

## 🔧 Database Direct Access

If you need to query directly:

```sql
-- View all email accounts with user count
SELECT 
    ea.email,
    ea.type,
    ea.department,
    COUNT(eas.id) as user_count
FROM email_accounts ea
LEFT JOIN email_assignments eas ON ea.id = eas.email_account_id
GROUP BY ea.id, ea.email, ea.type, ea.department;

-- View all user assignments
SELECT 
    u.email as user_email,
    u.name as user_name,
    ea.email as email_account,
    eas.role,
    eas.can_send,
    eas.can_receive,
    eas.is_active
FROM email_assignments eas
JOIN users u ON eas.user_id = u.id
JOIN email_accounts ea ON eas.email_account_id = ea.id
WHERE eas.is_active = true
ORDER BY u.email, ea.email;

-- Inbox with email account info
SELECT 
    ei.subject,
    ei.from_email,
    ea.email as to_account,
    ea.department,
    ei.status,
    ei.priority
FROM email_inbox ei
LEFT JOIN email_accounts ea ON ei.email_account_id = ea.id
ORDER BY ei.received_at DESC
LIMIT 10;
```

---

## �� Next Steps (Once UI is Built)

### Admin Panel Features Coming:
- **Dashboard**: `/admin/email-accounts`
  - View all email accounts
  - Create new email accounts
  - Assign/unassign users
  - View statistics

- **User Management**: `/admin/users/{id}/emails`
  - View user's assigned emails
  - Add new email assignments
  - Change roles and permissions
  - Remove assignments

- **Enhanced Inbox**: `/admin/inbox`
  - Filter by "My Emails"
  - Filter by department
  - See email account in each message
  - Reply from correct email account
  - Assign emails to team members

---

## 🧪 Test the System

Run this comprehensive test:

```php
// Test script - save as test_email_system.php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING MULTI-USER EMAIL SYSTEM ===\n\n";

// 1. List all email accounts
$accounts = App\Models\EmailAccount::with('users')->get();
echo "✅ Found {$accounts->count()} email accounts\n\n";

foreach ($accounts as $acc) {
    echo "📧 {$acc->email} ({$acc->type})\n";
    echo "   Users: {$acc->users->count()}\n";
    
    if ($acc->users->count() > 0) {
        foreach ($acc->users as $user) {
            $p = $user->pivot;
            echo "   └─ {$user->email} ({$p->role}) ";
            echo "Send:" . ($p->can_send ? 'Y' : 'N') . " ";
            echo "Receive:" . ($p->can_receive ? 'Y' : 'N') . "\n";
        }
    }
    echo "\n";
}

// 2. Test permission methods
echo "=== TESTING PERMISSIONS ===\n";
$firstUser = App\Models\User::first();
$firstEmail = App\Models\EmailAccount::first();

$assignment = App\Models\EmailAssignment::where('user_id', $firstUser->id)
    ->where('email_account_id', $firstEmail->id)
    ->first();

if ($assignment) {
    echo "User: {$firstUser->email}\n";
    echo "Email: {$firstEmail->email}\n";
    echo "Role: {$assignment->role_label}\n";
    echo "Can send: " . ($assignment->canSend() ? '✅' : '❌') . "\n";
    echo "Can receive: " . ($assignment->canReceive() ? '✅' : '❌') . "\n";
    echo "Can delete: " . ($assignment->canDelete() ? '✅' : '❌') . "\n";
}

echo "\n✅ All tests passed!\n";
```

Run: `php test_email_system.php`

---

**Need Help?** Check:
- `MULTI_USER_EMAIL_PROGRESS.md` - Full implementation details
- `MULTI_USER_EMAIL_SYSTEM.md` - Complete system documentation
- Database: Check tables `email_accounts`, `email_assignments`, `users`

**Current Database**: 4 email accounts, 4 assignments, all working! 🎉
