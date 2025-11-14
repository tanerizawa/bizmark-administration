# 🔒 Security & Authorization Analysis - Best Practice Implementation

## ⚠️ MASALAH KRITIS YANG DITEMUKAN

### Current State (TIDAK AMAN):
```php
// routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::resource('projects', ProjectController::class);  // ❌ No permission check!
    Route::resource('tasks', TaskController::class);        // ❌ No permission check!
    Route::resource('documents', DocumentController::class); // ❌ No permission check!
});

// ProjectController.php
public function index() {
    // ❌ No authorization check!
    $projects = Project::all();
    return view('projects.index', compact('projects'));
}

// resources/views/layouts/app.blade.php
@can('projects.view')
    <a href="{{ route('projects.index') }}">Proyek</a>  // ✅ Menu hidden (UX only)
@endcan
```

**PROBLEM**: User bisa langsung akses `https://bizmark.id/projects` bahkan jika role nya `viewer` atau `accountant`!

---

## 🎯 BEST PRACTICE: Defense in Depth (Multiple Layers)

### Layer 1: Route Level Protection (MIDDLEWARE) ⭐ PRIORITY 1
**Purpose**: Block unauthorized requests before hitting controller

```php
// app/Http/Middleware/CheckPermission.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (!auth()->user()->can($permission)) {
            abort(403, 'Unauthorized action.');
        }
        
        return $next($request);
    }
}

// routes/web.php
Route::middleware(['auth', 'permission:projects.view'])->group(function () {
    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
});

Route::middleware(['auth', 'permission:projects.create'])->group(function () {
    Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
});

Route::middleware(['auth', 'permission:projects.edit'])->group(function () {
    Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
});

Route::middleware(['auth', 'permission:projects.delete'])->group(function () {
    Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
});
```

---

### Layer 2: Controller Level Protection ⭐ PRIORITY 2
**Purpose**: Additional check even if middleware bypassed

#### Option A: Using authorize() method
```php
class ProjectController extends Controller
{
    public function index()
    {
        $this->authorize('projects.view');  // ✅ Check permission
        
        $projects = Project::all();
        return view('projects.index', compact('projects'));
    }
    
    public function create()
    {
        $this->authorize('projects.create');  // ✅ Check permission
        
        return view('projects.create');
    }
    
    public function store(Request $request)
    {
        $this->authorize('projects.create');  // ✅ Check permission
        
        $project = Project::create($request->validated());
        return redirect()->route('projects.index');
    }
    
    public function edit(Project $project)
    {
        $this->authorize('projects.edit');  // ✅ Check permission
        
        return view('projects.edit', compact('project'));
    }
    
    public function update(Request $request, Project $project)
    {
        $this->authorize('projects.edit');  // ✅ Check permission
        
        $project->update($request->validated());
        return redirect()->route('projects.show', $project);
    }
    
    public function destroy(Project $project)
    {
        $this->authorize('projects.delete');  // ✅ Check permission
        
        $project->delete();
        return redirect()->route('projects.index');
    }
}
```

#### Option B: Using middleware in constructor (Cleaner)
```php
class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:projects.view')->only(['index', 'show']);
        $this->middleware('permission:projects.create')->only(['create', 'store']);
        $this->middleware('permission:projects.edit')->only(['edit', 'update']);
        $this->middleware('permission:projects.delete')->only(['destroy']);
    }
    
    public function index()
    {
        // ✅ Already protected by middleware
        $projects = Project::all();
        return view('projects.index', compact('projects'));
    }
}
```

---

### Layer 3: Laravel Policies (Resource-Level Authorization) ⭐ ADVANCED
**Purpose**: Fine-grained control per resource instance

```php
// app/Policies/ProjectPolicy.php
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Project;

class ProjectPolicy
{
    /**
     * Determine if user can view any projects
     */
    public function viewAny(User $user): bool
    {
        return $user->can('projects.view');
    }
    
    /**
     * Determine if user can view specific project
     */
    public function view(User $user, Project $project): bool
    {
        // Admin & Manager can view all
        if ($user->hasAnyRole(['admin', 'manager'])) {
            return true;
        }
        
        // Staff can only view projects they're assigned to
        if ($user->hasRole('staff')) {
            return $project->tasks()->where('assigned_user_id', $user->id)->exists();
        }
        
        // Accountant can view for financial context
        if ($user->hasRole('accountant')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Determine if user can create projects
     */
    public function create(User $user): bool
    {
        return $user->can('projects.create');
    }
    
    /**
     * Determine if user can update project
     */
    public function update(User $user, Project $project): bool
    {
        // Admin & Manager can edit all
        if ($user->hasAnyRole(['admin', 'manager'])) {
            return $user->can('projects.edit');
        }
        
        // Staff cannot edit projects
        return false;
    }
    
    /**
     * Determine if user can delete project
     */
    public function delete(User $user, Project $project): bool
    {
        // Only admin & manager with permission
        return $user->hasAnyRole(['admin', 'manager']) && $user->can('projects.delete');
    }
}

// Register in AuthServiceProvider
use App\Models\Project;
use App\Policies\ProjectPolicy;

protected $policies = [
    Project::class => ProjectPolicy::class,
];

// Usage in Controller
class ProjectController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Project::class);
        
        $projects = Project::all();
        return view('projects.index', compact('projects'));
    }
    
    public function show(Project $project)
    {
        $this->authorize('view', $project);  // Check if user can view THIS specific project
        
        return view('projects.show', compact('project'));
    }
    
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        
        $project->update($request->validated());
        return redirect()->route('projects.show', $project);
    }
}
```

---

### Layer 4: View Level Protection (UX Only) ⭐ LOWEST PRIORITY
**Purpose**: Hide UI elements for better UX (NOT for security)

```blade
{{-- Sidebar Menu --}}
@can('projects.view')
    <a href="{{ route('projects.index') }}">Proyek</a>
@endcan

{{-- Action Buttons --}}
@can('projects.create')
    <a href="{{ route('projects.create') }}" class="btn btn-primary">
        Tambah Proyek
    </a>
@endcan

@can('projects.edit')
    <a href="{{ route('projects.edit', $project) }}" class="btn btn-warning">
        Edit
    </a>
@endcan

@can('projects.delete')
    <form action="{{ route('projects.destroy', $project) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Hapus</button>
    </form>
@endcan
```

---

## 📊 COMPARISON: Security Levels

| Layer | Security Level | Performance | Maintenance | Recommended |
|-------|---------------|-------------|-------------|-------------|
| **View Only** | ❌ None (UI only) | ⚡ Fast | Easy | ❌ NO |
| **Route Middleware** | ✅ High | ⚡ Fast | Medium | ✅ YES |
| **Controller Auth** | ✅ High | ⚡ Fast | Medium | ✅ YES |
| **Laravel Policy** | ✅✅ Very High | ⚡ Fast | Complex | ✅ Advanced |
| **All Combined** | ✅✅✅ Maximum | ⚡ Fast | Complex | ✅ BEST |

---

## 🎯 RECOMMENDED IMPLEMENTATION STRATEGY

### Step 1: Create Permission Middleware (30 menit)
```bash
php artisan make:middleware CheckPermission
```

### Step 2: Register Middleware (5 menit)
```php
// app/Http/Kernel.php
protected $middlewareAliases = [
    'permission' => \App\Http\Middleware\CheckPermission::class,
];
```

### Step 3: Protect Routes (2-3 jam untuk semua routes)
```php
// Group routes by permission
Route::middleware(['auth', 'permission:projects.view'])->group(function () {
    // Read routes
});

Route::middleware(['auth', 'permission:projects.create'])->group(function () {
    // Create routes
});
```

### Step 4: Add Controller Authorization (1-2 jam)
```php
// Add to each controller constructor or method
$this->authorize('permission.name');
```

### Step 5: Create Policies (Optional - 3-4 jam)
```bash
php artisan make:policy ProjectPolicy --model=Project
```

---

## 🔥 CRITICAL VULNERABILITIES IN CURRENT SYSTEM

### Vulnerability 1: Direct URL Access
```
User: viewer (only has read permissions)
Current: Can access https://bizmark.id/projects/1/edit ❌
Expected: Should get 403 Forbidden ✅
```

### Vulnerability 2: API Endpoint Exposure
```
User: staff (limited access)
Current: Can call DELETE /projects/1 via Postman ❌
Expected: Should get 403 Forbidden ✅
```

### Vulnerability 3: Form Submission
```
User: accountant (view only)
Current: Can submit form to /projects/1 even if button hidden ❌
Expected: Should get 403 Forbidden ✅
```

---

## 📝 IMPLEMENTATION CHECKLIST

### Phase 1: Critical Security (WAJIB) - 4-6 jam
- [ ] Create CheckPermission middleware
- [ ] Register middleware in Kernel
- [ ] Protect ALL routes with middleware
- [ ] Test dengan berbagai role
- [ ] Add 403 error page

### Phase 2: Controller Protection - 2-3 jam
- [ ] Add authorization to ProjectController
- [ ] Add authorization to TaskController
- [ ] Add authorization to DocumentController
- [ ] Add authorization to InstitutionController
- [ ] Add authorization to ClientController
- [ ] Add authorization to SettingsController

### Phase 3: Advanced (Optional) - 6-8 jam
- [ ] Create ProjectPolicy
- [ ] Create TaskPolicy
- [ ] Create DocumentPolicy
- [ ] Register policies in AuthServiceProvider
- [ ] Refactor controllers to use policies

### Phase 4: Testing - 2-3 jam
- [ ] Test admin (should access everything)
- [ ] Test manager (should access per permission)
- [ ] Test accountant (limited access)
- [ ] Test staff (limited access)
- [ ] Test viewer (read-only)
- [ ] Test direct URL access for each role
- [ ] Test API endpoints
- [ ] Penetration testing

---

## 🚀 QUICK WIN: Minimum Viable Security (1-2 jam)

Jika waktu terbatas, minimal lakukan ini:

```php
// 1. Create middleware
php artisan make:middleware CheckPermission

// 2. Protect kritical routes
Route::middleware(['auth'])->group(function () {
    // Dashboard - semua bisa akses
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Projects - cek permission
    Route::resource('projects', ProjectController::class)->middleware('permission:projects.view');
    
    // Tasks - cek permission  
    Route::resource('tasks', TaskController::class)->middleware('permission:tasks.view');
    
    // Documents - cek permission
    Route::resource('documents', DocumentController::class)->middleware('permission:documents.view');
});

// 3. Add constructor auth to controllers
class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:projects.view')->only(['index', 'show']);
        $this->middleware('permission:projects.create')->only(['create', 'store']);
        $this->middleware('permission:projects.edit')->only(['edit', 'update']);
        $this->middleware('permission:projects.delete')->only(['destroy']);
    }
}
```

---

## ✅ KESIMPULAN: Best Practice

### ❌ WRONG (Current Implementation):
```
[View Only] → No protection at controller/route level
User bisa bypass dengan direct URL access
```

### ✅ CORRECT (Recommended):
```
[Route Middleware] → Block di route level
    ↓
[Controller Authorization] → Double check di controller
    ↓
[Policy (optional)] → Fine-grained per resource
    ↓
[View Protection] → Hide UI for better UX
```

### 🎯 Priority Order:
1. **Route Middleware** (CRITICAL - lakukan sekarang)
2. **Controller Authorization** (HIGH - backup layer)
3. **Laravel Policy** (MEDIUM - for complex cases)
4. **View Protection** (LOW - sudah ada, UX only)

---

## 📚 Resources

- [Laravel Authorization Docs](https://laravel.com/docs/11.x/authorization)
- [Laravel Policies](https://laravel.com/docs/11.x/authorization#creating-policies)
- [Middleware](https://laravel.com/docs/11.x/middleware)
- [OWASP Broken Access Control](https://owasp.org/Top10/A01_2021-Broken_Access_Control/)
