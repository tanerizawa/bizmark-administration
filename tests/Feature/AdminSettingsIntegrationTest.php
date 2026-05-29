<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSettingsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $unauthorized;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(EnsureTwoFactorVerified::class);

        $roleAdmin = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Admin']);
        $roleViewer = Role::firstOrCreate(['name' => 'viewer'], ['display_name' => 'Viewer']);

        $permSettings = Permission::firstOrCreate(
            ['name' => 'settings.manage'],
            ['display_name' => 'Manage Settings', 'group' => 'settings']
        );
        $roleAdmin->permissions()->syncWithoutDetaching([$permSettings->id]);

        $this->admin = User::factory()->create(['role_id' => $roleAdmin->id]);
        $this->unauthorized = User::factory()->create(['role_id' => $roleViewer->id]);
    }

    // ── Settings Page Access ──────────────────────────────────────

    public function test_settings_page_loads_for_admin(): void
    {
        $this->actingAs($this->admin)
            ->get(route('settings.index'))
            ->assertOk();
    }

    public function test_settings_page_returns_403_for_unauthorized_user(): void
    {
        $this->actingAs($this->unauthorized)
            ->get(route('settings.index'))
            ->assertForbidden();
    }

    public function test_settings_page_redirects_unauthenticated(): void
    {
        $this->get(route('settings.index'))
            ->assertRedirect();
    }

    // ── General Settings Update ───────────────────────────────────

    public function test_admin_can_update_general_settings(): void
    {
        $this->actingAs($this->admin)
            ->put(route('settings.general.update'), [
                'company_name' => 'Bizmark Updated',
                'company_email' => 'updated@bizmark.id',
                'company_phone' => '0217654321',
                'company_website' => 'https://bizmark.id',
                'company_address' => 'Jl. Contoh No. 123',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
    }

    public function test_unauthorized_cannot_update_general_settings(): void
    {
        $this->actingAs($this->unauthorized)
            ->put(route('settings.general.update'), [
                'company_name' => 'Bizmark Updated',
            ])
            ->assertForbidden();
    }

    // ── User Management ───────────────────────────────────────────

    public function test_admin_can_create_user(): void
    {
        $this->actingAs($this->admin)
            ->post(route('settings.users.store'), [
                'username' => 'user_baru',
                'full_name' => 'User Baru',
                'email' => 'baru@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role_id' => $this->admin->role_id,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'baru@example.com',
            'name' => 'user_baru',
        ]);
    }

    public function test_create_user_validates_required_fields(): void
    {
        $this->actingAs($this->admin)
            ->post(route('settings.users.store'), [
                'username' => '',
                'full_name' => '',
                'email' => 'not-an-email',
                'password' => 'short',
                'password_confirmation' => 'different',
            ])
            ->assertSessionHasErrors(['username', 'full_name', 'email', 'password']);
    }

    public function test_unauthorized_cannot_create_user(): void
    {
        $this->actingAs($this->unauthorized)
            ->post(route('settings.users.store'), [
                'username' => 'user_baru',
                'full_name' => 'User Baru',
                'email' => 'baru@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role_id' => $this->admin->role_id,
            ])
            ->assertForbidden();
    }

    // ── Role Management ───────────────────────────────────────────

    public function test_admin_can_create_role(): void
    {
        $this->actingAs($this->admin)
            ->post(route('settings.roles.store'), [
                'name' => 'custom_role',
                'display_name' => 'Custom Role',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('roles', [
            'name' => 'custom_role',
            'display_name' => 'Custom Role',
        ]);
    }

    public function test_unauthorized_cannot_create_role(): void
    {
        $this->actingAs($this->unauthorized)
            ->post(route('settings.roles.store'), [
                'name' => 'custom_role',
                'display_name' => 'Custom Role',
            ])
            ->assertForbidden();
    }

    // ── Security Settings ─────────────────────────────────────────

    public function test_admin_can_update_security_settings(): void
    {
        $this->actingAs($this->admin)
            ->put(route('settings.security.update'), [
                'min_password_length' => 12,
                'password_expiration_days' => 90,
                'session_timeout_minutes' => 120,
                'require_special_char' => true,
                'require_number' => true,
                'require_mixed_case' => true,
                'two_factor_enabled' => true,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
    }

    public function test_unauthorized_cannot_update_security(): void
    {
        $this->actingAs($this->unauthorized)
            ->put(route('settings.security.update'), [
                'min_password_length' => 12,
                'password_expiration_days' => 90,
                'session_timeout_minutes' => 120,
            ])
            ->assertForbidden();
    }
}
