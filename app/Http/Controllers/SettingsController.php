<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /**
     * Display settings page with tabs
     */
    public function index(Request $request)
    {
        $activeTab = $request->input('tab', 'general');
        
        // Get data based on active tab
        $users = User::with('role')->orderBy('name')->get();
        $roles = Role::withCount('users')->with('permissions')->get();
        $permissions = Permission::all()->groupBy('group');
        
        return view('settings.index', compact('activeTab', 'users', 'roles', 'permissions'));
    }

    /**
     * Store new user
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role_id' => 'required|exists:roles,id',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        User::create($validated);

        return redirect()->route('settings.index', ['tab' => 'users'])
            ->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Update existing user
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role_id' => 'required|exists:roles,id',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return redirect()->route('settings.index', ['tab' => 'users'])
            ->with('success', 'User berhasil diupdate');
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        // Prevent deleting current logged-in user
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus user yang sedang login');
        }

        // Delete avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('settings.index', ['tab' => 'users'])
            ->with('success', 'User berhasil dihapus');
    }

    /**
     * Toggle user active status
     */
    public function toggleUserStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        return back()->with('success', 'Status user berhasil diubah');
    }

    /**
     * Store new role
     */
    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
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

        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return redirect()->route('settings.index', ['tab' => 'roles'])
            ->with('success', 'Role berhasil ditambahkan');
    }

    /**
     * Update role permissions
     */
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
            ->with('success', 'Role berhasil diupdate');
    }

    /**
     * Delete role
     */
    public function deleteRole(Role $role)
    {
        // Prevent deleting system roles
        if ($role->is_system) {
            return back()->with('error', 'Tidak dapat menghapus system role');
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus role yang masih digunakan oleh user');
        }

        $role->delete();

        return redirect()->route('settings.index', ['tab' => 'roles'])
            ->with('success', 'Role berhasil dihapus');
    }
}
