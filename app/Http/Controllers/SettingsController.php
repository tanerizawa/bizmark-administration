<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Models\Permission;
use App\Models\ProjectExpense;
use App\Models\ProjectPayment;
use App\Models\ProjectStatus;
use App\Models\Role;
use App\Models\SecuritySetting;
use App\Models\TaxRate;
use App\Models\User;
use App\Http\Controllers\Traits\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizePermission('settings.manage', 'Anda tidak memiliki akses ke halaman pengaturan.');
    }

    /**
     * Display settings page with tabs
     */
    public function index(Request $request)
    {
        $activeTab = $request->input('tab', 'general');

        $businessSetting = BusinessSetting::current();
        $securitySetting = SecuritySetting::current();

        $users = User::with('role')->orderBy('name')->get();
        $roles = Role::withCount('users')->with('permissions')->get();
        $permissions = Permission::all()->groupBy('group');

        $expenseCategories = ExpenseCategory::options();
        $paymentMethods = PaymentMethod::options();
        $taxRates = TaxRate::options();
        $projectStatuses = ProjectStatus::orderBy('sort_order')->orderBy('name')->get();

        return view('settings.index', compact(
            'activeTab',
            'businessSetting',
            'securitySetting',
            'users',
            'roles',
            'permissions',
            'expenseCategories',
            'paymentMethods',
            'taxRates',
            'projectStatuses'
        ));
    }

    protected function passwordRule(): Password
    {
        $security = SecuritySetting::current();

        $rule = Password::min($security->min_password_length ?? 8);

        if ($security->require_mixed_case) {
            $rule->mixedCase();
        }

        if ($security->require_number) {
            $rule->numbers();
        }

        if ($security->require_special_char) {
            $rule->symbols();
        }

        return $rule;
    }

    protected function resetDefault(string $modelClass, ?int $exceptId = null): void
    {
        $query = $modelClass::query();

        if ($exceptId !== null) {
            $query->where('id', '!=', $exceptId);
        }

        $query->update(['is_default' => false]);
    }

    /**
     * General Settings
     */
    public function updateGeneral(Request $request)
    {
        $setting = BusinessSetting::current();

        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:255',
            'company_website' => 'nullable|url|max:255',
            'company_address' => 'nullable|string',
        ]);

        $maintenance = $request->boolean('maintenance_mode');
        $emailNotifications = $request->boolean('email_notifications');
        $originalMaintenance = $setting->maintenance_mode;

        $setting->fill($validated);
        $setting->maintenance_mode = $maintenance;
        $setting->email_notifications = $emailNotifications;
        $setting->save();

        if ($maintenance && !$originalMaintenance) {
            Artisan::call('down', ['--secret' => config('app.maintenance_secret', 'bizmark-maintenance')]);
        } elseif (!$maintenance && $originalMaintenance) {
            Artisan::call('up');
        }

        return redirect()->route('settings.index', ['tab' => 'general'])
            ->with('success', 'Pengaturan umum berhasil diperbarui');
    }

    /**
     * User Management
     */
    public function storeUser(Request $request)
    {
        $rule = $this->passwordRule();

        try {
            $validated = $request->validate([
                'username' => 'required|string|max:255|unique:users,username|regex:/^[a-z0-9_]+$/',
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'employee_id' => 'nullable|string|max:255|unique:users,employee_id',
                'password' => ['required', 'confirmed', $rule],
                'role_id' => 'required|exists:roles,id',
                'position' => 'nullable|string|max:255',
                'department' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'avatar' => 'nullable|image|max:2048',
            ]);

            // Set 'name' from username for backward compatibility
            $validated['name'] = $validated['username'];
            $validated['password'] = Hash::make($validated['password']);
            $validated['is_active'] = true;

            if ($request->hasFile('avatar')) {
                $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            User::create($validated);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'User berhasil ditambahkan']);
            }

            return redirect()->route('settings.index', ['tab' => 'users'])
                ->with('success', 'User berhasil ditambahkan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
    }

    public function updateUser(Request $request, User $user)
    {
        $rule = $this->passwordRule();

        try {
            $validated = $request->validate([
                'username' => 'required|string|max:255|unique:users,username,' . $user->id . '|regex:/^[a-z0-9_]+$/',
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'employee_id' => 'nullable|string|max:255|unique:users,employee_id,' . $user->id,
                'password' => ['nullable', 'confirmed', $rule],
                'role_id' => 'required|exists:roles,id',
                'position' => 'nullable|string|max:255',
                'department' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'is_active' => 'nullable|boolean',
                'avatar' => 'nullable|image|max:2048',
            ]);

            // Update 'name' from username for backward compatibility
            $validated['name'] = $validated['username'];

            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $validated['is_active'] = $request->boolean('is_active', $user->is_active);

            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $user->update($validated);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'User berhasil diperbarui']);
            }

            return redirect()->route('settings.index', ['tab' => 'users'])
                ->with('success', 'User berhasil diperbarui');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus user yang sedang login');
        }

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('settings.index', ['tab' => 'users'])
            ->with('success', 'User berhasil dihapus');
    }

    public function toggleUserStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        return redirect()->route('settings.index', ['tab' => 'users'])
            ->with('success', 'Status user berhasil diubah');
    }

    /**
     * Role Management
     */
    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'is_system' => false,
        ]);

        if (!empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return redirect()->route('settings.index', ['tab' => 'roles'])
            ->with('success', 'Role berhasil ditambahkan');
    }

    public function updateRole(Request $request, Role $role)
    {
        $validated = $request->validate([
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        } else {
            $role->permissions()->sync([]);
        }

        return redirect()->route('settings.index', ['tab' => 'roles'])
            ->with('success', 'Role berhasil diperbarui');
    }

    public function deleteRole(Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', 'Tidak dapat menghapus system role');
        }

        if ($role->users()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus role yang masih digunakan oleh user');
        }

        $role->delete();

        return redirect()->route('settings.index', ['tab' => 'roles'])
            ->with('success', 'Role berhasil dihapus');
    }

    /**
     * Financial Settings - Expense Categories
     */
    public function storeExpenseCategory(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'required|alpha_dash|max:50|unique:expense_categories,slug',
            'name' => 'required|string|max:255',
            'group' => 'nullable|string|max:255',
            'icon' => 'required|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_default'] = $request->boolean('is_default', false);

        ExpenseCategory::create($validated);

        return redirect()->route('settings.index', ['tab' => 'financial'])
            ->with('success', 'Kategori pengeluaran berhasil ditambahkan');
    }

    public function updateExpenseCategory(Request $request, ExpenseCategory $expenseCategory)
    {
        $validated = $request->validate([
            'slug' => [
                'required',
                'alpha_dash',
                'max:50',
                Rule::unique('expense_categories', 'slug')->ignore($expenseCategory->id),
            ],
            'name' => 'required|string|max:255',
            'group' => 'nullable|string|max:255',
            'icon' => 'required|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', $expenseCategory->is_active);
        $validated['is_default'] = $request->boolean('is_default', $expenseCategory->is_default);

        $expenseCategory->update($validated);

        return redirect()->route('settings.index', ['tab' => 'financial'])
            ->with('success', 'Kategori pengeluaran berhasil diperbarui');
    }

    public function deleteExpenseCategory(ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->is_default) {
            return back()->with('error', 'Kategori bawaan tidak dapat dihapus');
        }

        $isInUse = ProjectExpense::where('category', $expenseCategory->slug)->exists();
        if ($isInUse) {
            return back()->with('error', 'Kategori masih digunakan dalam transaksi dan tidak dapat dihapus');
        }

        $expenseCategory->delete();

        return redirect()->route('settings.index', ['tab' => 'financial'])
            ->with('success', 'Kategori pengeluaran berhasil dihapus');
    }

    /**
     * Financial Settings - Payment Methods
     */
    public function storePaymentMethod(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|alpha_dash|max:50|unique:payment_methods,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'requires_cash_account' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['requires_cash_account'] = $request->boolean('requires_cash_account', false);
        $validated['is_default'] = $request->boolean('is_default', false);
        $validated['is_active'] = $request->boolean('is_active', true);

        $paymentMethod = PaymentMethod::create($validated);

        if ($paymentMethod->is_default) {
            $this->resetDefault(PaymentMethod::class, $paymentMethod->id);
        }

        return redirect()->route('settings.index', ['tab' => 'financial'])
            ->with('success', 'Metode pembayaran berhasil ditambahkan');
    }

    public function updatePaymentMethod(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'alpha_dash',
                'max:50',
                Rule::unique('payment_methods', 'code')->ignore($paymentMethod->id),
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'requires_cash_account' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['requires_cash_account'] = $request->boolean('requires_cash_account', $paymentMethod->requires_cash_account);
        $validated['is_default'] = $request->boolean('is_default', $paymentMethod->is_default);
        $validated['is_active'] = $request->boolean('is_active', $paymentMethod->is_active);

        $paymentMethod->update($validated);

        if ($paymentMethod->is_default) {
            $this->resetDefault(PaymentMethod::class, $paymentMethod->id);
        }

        return redirect()->route('settings.index', ['tab' => 'financial'])
            ->with('success', 'Metode pembayaran berhasil diperbarui');
    }

    public function deletePaymentMethod(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->is_default) {
            return back()->with('error', 'Metode pembayaran default tidak dapat dihapus');
        }

        $inUse = ProjectPayment::where('payment_method', $paymentMethod->code)->exists()
            || ProjectExpense::where('payment_method', $paymentMethod->code)->exists();

        if ($inUse) {
            return back()->with('error', 'Metode pembayaran sedang digunakan pada transaksi');
        }

        $paymentMethod->delete();

        return redirect()->route('settings.index', ['tab' => 'financial'])
            ->with('success', 'Metode pembayaran berhasil dihapus');
    }

    /**
     * Financial Settings - Tax Rates
     */
    public function storeTaxRate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tax_rates,name',
            'rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_default'] = $request->boolean('is_default', false);
        $validated['is_active'] = $request->boolean('is_active', true);

        $taxRate = TaxRate::create($validated);

        if ($taxRate->is_default) {
            $this->resetDefault(TaxRate::class, $taxRate->id);
        }

        return redirect()->route('settings.index', ['tab' => 'financial'])
            ->with('success', 'Tarif pajak berhasil ditambahkan');
    }

    public function updateTaxRate(Request $request, TaxRate $taxRate)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tax_rates', 'name')->ignore($taxRate->id),
            ],
            'rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_default'] = $request->boolean('is_default', $taxRate->is_default);
        $validated['is_active'] = $request->boolean('is_active', $taxRate->is_active);

        $taxRate->update($validated);

        if ($taxRate->is_default) {
            $this->resetDefault(TaxRate::class, $taxRate->id);
        }

        return redirect()->route('settings.index', ['tab' => 'financial'])
            ->with('success', 'Tarif pajak berhasil diperbarui');
    }

    public function deleteTaxRate(TaxRate $taxRate)
    {
        if ($taxRate->is_default) {
            return back()->with('error', 'Tarif pajak default tidak dapat dihapus');
        }

        $taxRate->delete();

        return redirect()->route('settings.index', ['tab' => 'financial'])
            ->with('success', 'Tarif pajak berhasil dihapus');
    }

    /**
     * Project Settings - Statuses
     */
    public function storeProjectStatus(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|alpha_dash|max:50|unique:project_statuses,code',
            'description' => 'nullable|string',
            'color' => 'nullable|regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_final' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_final'] = $request->boolean('is_final', false);

        ProjectStatus::create($validated);

        return redirect()->route('settings.index', ['tab' => 'project'])
            ->with('success', 'Status proyek berhasil ditambahkan');
    }

    public function updateProjectStatus(Request $request, ProjectStatus $projectStatus)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'alpha_dash',
                'max:50',
                Rule::unique('project_statuses', 'code')->ignore($projectStatus->id),
            ],
            'description' => 'nullable|string',
            'color' => 'nullable|regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_final' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', $projectStatus->is_active);
        $validated['is_final'] = $request->boolean('is_final', $projectStatus->is_final);

        $projectStatus->update($validated);

        return redirect()->route('settings.index', ['tab' => 'project'])
            ->with('success', 'Status proyek berhasil diperbarui');
    }

    public function deleteProjectStatus(ProjectStatus $projectStatus)
    {
        if ($projectStatus->projects()->exists()) {
            return back()->with('error', 'Status proyek digunakan oleh proyek aktif sehingga tidak dapat dihapus');
        }

        $projectStatus->delete();

        return redirect()->route('settings.index', ['tab' => 'project'])
            ->with('success', 'Status proyek berhasil dihapus');
    }

    /**
     * Security Settings
     */
    public function updateSecurity(Request $request)
    {
        $validated = $request->validate([
            'min_password_length' => 'required|integer|min:6|max:32',
            'require_special_char' => 'nullable|boolean',
            'require_number' => 'nullable|boolean',
            'require_mixed_case' => 'nullable|boolean',
            'enforce_password_expiration' => 'nullable|boolean',
            'password_expiration_days' => 'required|integer|min:7|max:365',
            'session_timeout_minutes' => 'required|integer|min:5|max:240',
            'allow_concurrent_sessions' => 'nullable|boolean',
            'two_factor_enabled' => 'nullable|boolean',
            'activity_log_enabled' => 'nullable|boolean',
        ]);

        $security = SecuritySetting::current();

        $security->update([
            'min_password_length' => $validated['min_password_length'],
            'require_special_char' => $request->boolean('require_special_char'),
            'require_number' => $request->boolean('require_number'),
            'require_mixed_case' => $request->boolean('require_mixed_case'),
            'enforce_password_expiration' => $request->boolean('enforce_password_expiration'),
            'password_expiration_days' => $validated['password_expiration_days'],
            'session_timeout_minutes' => $validated['session_timeout_minutes'],
            'allow_concurrent_sessions' => $request->boolean('allow_concurrent_sessions', true),
            'two_factor_enabled' => $request->boolean('two_factor_enabled'),
            'activity_log_enabled' => $request->boolean('activity_log_enabled', true),
        ]);

        return redirect()->route('settings.index', ['tab' => 'security'])
            ->with('success', 'Pengaturan keamanan berhasil diperbarui');
    }
}
