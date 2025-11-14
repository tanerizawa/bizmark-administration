# Analisis & Rekomendasi Role-Based Sidebar Menu

## 📊 Struktur Menu Saat Ini

### 1. **Main Navigation** (Tidak dikelompokkan)
- Dashboard
- Proyek
- Tugas
- Dokumen
- Instansi
- Klien
- Pengaturan

### 2. **Recruitment**
- Lowongan Kerja
- Lamaran Masuk

### 3. **Email Management**
- Inbox
- Email Accounts
- Campaigns
- Subscribers
- Templates
- Email Settings

### 4. **Master Data**
- Jenis Izin
- Template Izin
- Akun Kas
- Rekonsiliasi Bank

### 5. **Konten & Media**
- Artikel & Berita

---

## 👥 Role yang Ada di Sistem

| ID | Name | Display Name | Deskripsi Umum |
|----|------|--------------|----------------|
| 1 | admin | Administrator | Akses penuh ke semua fitur |
| 2 | manager | Manager | Manajemen proyek, tim, keuangan |
| 3 | accountant | Accountant | Fokus pada keuangan & akuntansi |
| 4 | staff | Staff | Operasional harian proyek |
| 5 | viewer | Viewer | Hanya lihat, tidak bisa edit |

---

## 🔐 Permission yang Ada di Sistem

### Projects Group
- `projects.view`, `projects.create`, `projects.edit`, `projects.delete`

### Clients Group
- `clients.view`, `clients.create`, `clients.edit`, `clients.delete`

### Invoices Group
- `invoices.view`, `invoices.create`, `invoices.edit`, `invoices.delete`, `invoices.approve`

### Finances Group
- `finances.view`, `finances.manage_payments`, `finances.manage_expenses`, `finances.manage_accounts`, `finances.view_reports`

### Tasks Group
- `tasks.view`, `tasks.create`, `tasks.edit`, `tasks.delete`, `tasks.assign`

### Documents Group
- `documents.view`, `documents.upload`, `documents.delete`

### Users Group
- `users.view`, `users.create`, `users.edit`, `users.delete`

### Settings Group
- `settings.manage`, `roles.manage`

---

## 🎯 Rekomendasi Role-Based Menu Visibility

### **Admin** (Full Access)
✅ Semua menu visible

```php
// No restriction needed - admin sees everything
```

---

### **Manager** (Project & Team Management)

**Visible:**
- ✅ Dashboard
- ✅ Proyek (full access)
- ✅ Tugas (full access)
- ✅ Dokumen (full access)
- ✅ Instansi (view only)
- ✅ Klien (full access)
- ✅ Pengaturan (limited: user management only, NO roles/permissions)
- ✅ Recruitment Section (full access)
- ✅ Email Management (full access)
- ✅ Master Data → Jenis Izin (view only)
- ✅ Master Data → Template Izin (view only)
- ✅ Master Data → Akun Kas (view only)
- ✅ Master Data → Rekonsiliasi Bank (view only)
- ✅ Konten & Media → Artikel & Berita (full access)

**Hidden:**
- ❌ Pengaturan → Roles & Permissions tabs

**Permissions Required:**
- `projects.*`, `clients.*`, `tasks.*`, `documents.*`, `users.view`, `users.create`, `users.edit`

---

### **Accountant** (Finance Focus)

**Visible:**
- ✅ Dashboard (finance-focused widgets)
- ✅ Proyek (view only - untuk context financial)
- ✅ Klien (view only - untuk invoicing context)
- ✅ Master Data → Akun Kas (full access)
- ✅ Master Data → Rekonsiliasi Bank (full access)

**Hidden:**
- ❌ Tugas
- ❌ Dokumen
- ❌ Instansi
- ❌ Pengaturan
- ❌ Recruitment Section
- ❌ Email Management
- ❌ Master Data → Jenis Izin
- ❌ Master Data → Template Izin
- ❌ Konten & Media

**Permissions Required:**
- `projects.view`, `clients.view`, `finances.*`, `invoices.*`

---

### **Staff** (Operational)

**Visible:**
- ✅ Dashboard
- ✅ Proyek (view + create tasks)
- ✅ Tugas (view own tasks + assigned tasks)
- ✅ Dokumen (view + upload)
- ✅ Instansi (view only)
- ✅ Klien (view only)

**Hidden:**
- ❌ Pengaturan
- ❌ Recruitment Section
- ❌ Email Management
- ❌ Master Data (all)
- ❌ Konten & Media

**Permissions Required:**
- `projects.view`, `tasks.view`, `tasks.create`, `documents.view`, `documents.upload`, `clients.view`

---

### **Viewer** (Read-Only)

**Visible:**
- ✅ Dashboard (read-only)
- ✅ Proyek (view only)
- ✅ Tugas (view only)
- ✅ Dokumen (view only)
- ✅ Instansi (view only)
- ✅ Klien (view only)

**Hidden:**
- ❌ Pengaturan
- ❌ Recruitment Section
- ❌ Email Management
- ❌ Master Data
- ❌ Konten & Media

**Permissions Required:**
- `projects.view`, `tasks.view`, `documents.view`, `clients.view`

---

## 💡 Permission Mapping untuk Menu Baru

Perlu ditambahkan permission untuk menu yang belum punya:

### Recruitment Group
```php
'recruitment.view_jobs',
'recruitment.manage_jobs',
'recruitment.view_applications',
'recruitment.process_applications'
```

### Email Group
```php
'email.view_inbox',
'email.send_email',
'email.manage_accounts',
'email.manage_campaigns',
'email.manage_subscribers',
'email.manage_templates',
'email.manage_settings'
```

### Institutions Group
```php
'institutions.view',
'institutions.create',
'institutions.edit',
'institutions.delete'
```

### Master Data Group
```php
'master_data.view',
'master_data.edit_permit_types',
'master_data.edit_permit_templates'
```

### Content Group
```php
'content.view_articles',
'content.create_articles',
'content.edit_articles',
'content.delete_articles',
'content.publish_articles'
```

---

## 🔨 Implementasi: Blade Directives

### Opsi 1: Menggunakan `@can` Directive (Recommended)

```blade
{{-- Dashboard - Semua role --}}
<a href="{{ route('dashboard') }}" ...>
    <i class="fas fa-home w-5"></i>
    <span class="ml-3">Dashboard</span>
</a>

{{-- Proyek - Butuh projects.view --}}
@can('projects.view')
<a href="{{ route('projects.index') }}" ...>
    <i class="fas fa-project-diagram w-5"></i>
    <span class="ml-3">Proyek</span>
</a>
@endcan

{{-- Pengaturan - Admin & Manager only --}}
@can('settings.manage')
<a href="{{ route('settings.index') }}" ...>
    <i class="fas fa-cog w-5"></i>
    <span class="ml-3">Pengaturan</span>
</a>
@endcan

{{-- Recruitment Section - Admin & Manager only --}}
@can('recruitment.manage_jobs')
<div class="pt-4 mt-4" style="border-top: 1px solid var(--dark-separator);">
    <p class="px-3 text-xs ...">Recruitment</p>
    ...
</div>
@endcan

{{-- Email Management - Admin & Manager only --}}
@can('email.manage_accounts')
<div class="pt-4 mt-4" style="border-top: 1px solid var(--dark-separator);">
    <p class="px-3 text-xs ...">Email Management</p>
    ...
</div>
@endcan

{{-- Master Data - Tergantung sub-menu --}}
@if(auth()->user()->hasAnyPermission(['finances.manage_accounts', 'master_data.edit_permit_types']))
<div class="pt-4 mt-4" style="border-top: 1px solid var(--dark-separator);">
    <p class="px-3 text-xs ...">Master Data</p>
    
    @can('master_data.edit_permit_types')
    <a href="{{ route('permit-types.index') }}" ...>
        <i class="fas fa-certificate w-5"></i>
        <span class="ml-3">Jenis Izin</span>
    </a>
    @endcan
    
    @can('finances.manage_accounts')
    <a href="{{ route('cash-accounts.index') }}" ...>
        <i class="fas fa-wallet w-5"></i>
        <span class="ml-3">Akun Kas</span>
    </a>
    @endcan
</div>
@endif
```

---

### Opsi 2: Menggunakan Role Check

```blade
{{-- Recruitment - Admin & Manager only --}}
@if(auth()->user()->hasAnyRole(['admin', 'manager']))
<div class="pt-4 mt-4" style="border-top: 1px solid var(--dark-separator);">
    <p class="px-3 text-xs ...">Recruitment</p>
    ...
</div>
@endif

{{-- Master Data - Admin, Manager, Accountant --}}
@if(auth()->user()->hasAnyRole(['admin', 'manager', 'accountant']))
<div class="pt-4 mt-4" style="border-top: 1px solid var(--dark-separator);">
    <p class="px-3 text-xs ...">Master Data</p>
    ...
</div>
@endif
```

---

### Opsi 3: Custom Helper Method (Most Flexible)

Tambahkan method di `User.php`:

```php
/**
 * Check if user has any of the given permissions
 */
public function hasAnyPermission(array $permissions)
{
    foreach ($permissions as $permission) {
        if ($this->hasPermission($permission)) {
            return true;
        }
    }
    return false;
}

/**
 * Check if user can access recruitment module
 */
public function canAccessRecruitment()
{
    return $this->hasAnyRole(['admin', 'manager']);
}

/**
 * Check if user can access email management
 */
public function canAccessEmailManagement()
{
    return $this->hasAnyRole(['admin', 'manager']);
}

/**
 * Check if user can access master data
 */
public function canAccessMasterData()
{
    return $this->hasAnyRole(['admin', 'manager', 'accountant']);
}
```

Kemudian di Blade:

```blade
@if(auth()->user()->canAccessRecruitment())
<div class="pt-4 mt-4" style="border-top: 1px solid var(--dark-separator);">
    <p class="px-3 text-xs ...">Recruitment</p>
    ...
</div>
@endif
```

---

## 🎨 Best Practice Recommendations

### 1. **Gunakan Permission-Based (bukan Role-Based)**
✅ **DO:**
```blade
@can('projects.view')
    <a href="...">Proyek</a>
@endcan
```

❌ **DON'T:**
```blade
@if(auth()->user()->role->name === 'admin')
    <a href="...">Proyek</a>
@endif
```

**Alasan:** Permission lebih flexible. Jika nanti ada role baru "Project Manager", tinggal kasih permission `projects.view` tanpa ubah code.

---

### 2. **Group Menu Sections**

Untuk section dengan multiple items (Recruitment, Email Management), check permission parent:

```blade
@if(auth()->user()->canAccessRecruitment())
<div class="recruitment-section">
    <a href="{{ route('admin.jobs.index') }}">Lowongan Kerja</a>
    <a href="{{ route('admin.applications.index') }}">Lamaran Masuk</a>
</div>
@endif
```

---

### 3. **Hide Empty Sections**

Jika semua sub-menu di section tidak accessible, hide section header:

```blade
@php
$hasEmailAccess = auth()->user()->hasAnyPermission([
    'email.view_inbox',
    'email.manage_accounts',
    'email.manage_campaigns'
]);
@endphp

@if($hasEmailAccess)
<div class="pt-4 mt-4" style="border-top: 1px solid var(--dark-separator);">
    <p class="px-3 text-xs ...">Email Management</p>
    
    @can('email.view_inbox')
    <a href="{{ route('admin.inbox.index') }}">Inbox</a>
    @endcan
    
    @can('email.manage_accounts')
    <a href="{{ route('admin.email-accounts.index') }}">Email Accounts</a>
    @endcan
</div>
@endif
```

---

### 4. **Dashboard Customization**

Dashboard bisa disesuaikan per role:

```blade
{{-- DashboardController.php --}}
public function index()
{
    $user = auth()->user();
    
    $data = [
        'stats' => $this->getStats(),
    ];
    
    // Accountant hanya perlu finance widgets
    if ($user->hasRole('accountant')) {
        $data['financeWidgets'] = $this->getFinanceData();
        return view('dashboard-accountant', $data);
    }
    
    // Staff hanya perlu task widgets
    if ($user->hasRole('staff')) {
        $data['taskWidgets'] = $this->getTaskData();
        return view('dashboard-staff', $data);
    }
    
    // Admin & Manager full dashboard
    return view('dashboard', $data);
}
```

---

## 📋 Action Items untuk Implementasi

### Phase 1: Setup Permission Baru (1-2 jam)
1. ✅ Buat migration untuk permission baru (recruitment, email, institutions, master data, content)
2. ✅ Buat seeder untuk permission baru
3. ✅ Assign permission ke role yang sesuai
4. ✅ Run migration & seeder

### Phase 2: Update User Model (30 menit)
1. ✅ Tambahkan helper methods (`hasAnyPermission`, `canAccessRecruitment`, dll)
2. ✅ Test methods via tinker

### Phase 3: Update Sidebar View (2-3 jam)
1. ✅ Wrap setiap menu dengan `@can` directive
2. ✅ Wrap section dengan conditional checks
3. ✅ Test dengan berbagai role
4. ✅ Validasi UI tidak broken

### Phase 4: Testing (1-2 jam)
1. ✅ Login sebagai Admin → Cek semua menu visible
2. ✅ Login sebagai Manager → Cek menu sesuai rekomendasi
3. ✅ Login sebagai Accountant → Cek hanya menu finance visible
4. ✅ Login sebagai Staff → Cek menu operasional visible
5. ✅ Login sebagai Viewer → Cek hanya read-only menu visible

### Phase 5: Route Protection (1 jam)
Jangan lupa protect routes juga:

```php
// routes/web.php
Route::middleware(['auth', 'can:projects.view'])->group(function () {
    Route::resource('projects', ProjectController::class);
});

Route::middleware(['auth', 'can:recruitment.manage_jobs'])->group(function () {
    Route::resource('admin/jobs', JobController::class);
});
```

---

## 🚀 Recommended Implementation Approach

**Saya rekomendasikan menggunakan Opsi 1 (Permission-based dengan `@can`)** karena:

1. ✅ **Scalable** - Easy to add new roles without changing code
2. ✅ **Laravel Native** - Menggunakan authorization system bawaan Laravel
3. ✅ **Maintainable** - Clear separation of concerns
4. ✅ **Flexible** - Permission bisa di-assign/revoke per user atau per role
5. ✅ **DRY** - No duplicate logic

**Namun perlu tambahan:**
- Create missing permissions terlebih dahulu
- Assign permissions to roles sesuai table di atas
- Add helper method `hasAnyPermission()` di User model
- Update sidebar dengan @can directives

---

## 📝 Migration Script Example

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $permissions = [
            // Recruitment
            ['name' => 'recruitment.view_jobs', 'display_name' => 'View Jobs', 'group' => 'recruitment'],
            ['name' => 'recruitment.manage_jobs', 'display_name' => 'Manage Jobs', 'group' => 'recruitment'],
            ['name' => 'recruitment.view_applications', 'display_name' => 'View Applications', 'group' => 'recruitment'],
            ['name' => 'recruitment.process_applications', 'display_name' => 'Process Applications', 'group' => 'recruitment'],
            
            // Email
            ['name' => 'email.view_inbox', 'display_name' => 'View Inbox', 'group' => 'email'],
            ['name' => 'email.send_email', 'display_name' => 'Send Email', 'group' => 'email'],
            ['name' => 'email.manage_accounts', 'display_name' => 'Manage Email Accounts', 'group' => 'email'],
            ['name' => 'email.manage_campaigns', 'display_name' => 'Manage Campaigns', 'group' => 'email'],
            ['name' => 'email.manage_subscribers', 'display_name' => 'Manage Subscribers', 'group' => 'email'],
            ['name' => 'email.manage_templates', 'display_name' => 'Manage Templates', 'group' => 'email'],
            ['name' => 'email.manage_settings', 'display_name' => 'Manage Email Settings', 'group' => 'email'],
            
            // Institutions
            ['name' => 'institutions.view', 'display_name' => 'View Institutions', 'group' => 'institutions'],
            ['name' => 'institutions.create', 'display_name' => 'Create Institutions', 'group' => 'institutions'],
            ['name' => 'institutions.edit', 'display_name' => 'Edit Institutions', 'group' => 'institutions'],
            ['name' => 'institutions.delete', 'display_name' => 'Delete Institutions', 'group' => 'institutions'],
            
            // Master Data
            ['name' => 'master_data.view', 'display_name' => 'View Master Data', 'group' => 'master_data'],
            ['name' => 'master_data.edit_permit_types', 'display_name' => 'Edit Permit Types', 'group' => 'master_data'],
            ['name' => 'master_data.edit_permit_templates', 'display_name' => 'Edit Permit Templates', 'group' => 'master_data'],
            
            // Content
            ['name' => 'content.view_articles', 'display_name' => 'View Articles', 'group' => 'content'],
            ['name' => 'content.create_articles', 'display_name' => 'Create Articles', 'group' => 'content'],
            ['name' => 'content.edit_articles', 'display_name' => 'Edit Articles', 'group' => 'content'],
            ['name' => 'content.delete_articles', 'display_name' => 'Delete Articles', 'group' => 'content'],
            ['name' => 'content.publish_articles', 'display_name' => 'Publish Articles', 'group' => 'content'],
        ];
        
        foreach ($permissions as $permission) {
            DB::table('permissions')->insert(array_merge($permission, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
    
    public function down()
    {
        DB::table('permissions')->whereIn('group', [
            'recruitment', 'email', 'institutions', 'master_data', 'content'
        ])->delete();
    }
};
```

---

**Apakah Anda ingin saya langsung implementasikan solusi ini?** 🚀
