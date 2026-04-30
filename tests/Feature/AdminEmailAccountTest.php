<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckEmailManagementAccess;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Models\EmailAccount;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEmailAccountTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        Permission::create(['name' => 'email.manage', 'display_name' => 'Manage Email', 'group' => 'email']);
        $role->permissions()->attach(Permission::pluck('id'));

        $this->adminUser = User::factory()->create(['role_id' => $role->id]);
    }

    private function bypass(): array
    {
        return [CheckPermission::class, EnsureTwoFactorVerified::class, CheckEmailManagementAccess::class];
    }

    // ── Index ──────────────────────────────────────────────────────────

    public function test_admin_can_view_email_accounts_list(): void
    {
        EmailAccount::create([
            'email' => 'support@bizmark.id',
            'name' => 'Support',
            'type' => 'shared',
            'department' => 'general',
            'is_active' => true,
        ]);

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->get('/admin/email-accounts');

        $response->assertStatus(200);
    }

    // ── Store ──────────────────────────────────────────────────────────

    public function test_admin_can_create_email_account(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->post('/admin/email-accounts', [
                'email' => 'ops@bizmark.id',
                'name' => 'Operations',
                'type' => 'shared',
                'department' => 'general',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('email_accounts', [
            'email' => 'ops@bizmark.id',
            'name' => 'Operations',
            'type' => 'shared',
        ]);
    }

    public function test_store_requires_valid_email(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->post('/admin/email-accounts', [
                'email' => 'not-an-email',
                'name' => 'Bad',
                'type' => 'shared',
                'department' => 'general',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_store_requires_valid_type(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->post('/admin/email-accounts', [
                'email' => 'test@bizmark.id',
                'name' => 'Test',
                'type' => 'invalid-type',
                'department' => 'general',
            ]);

        $response->assertSessionHasErrors('type');
    }

    public function test_store_rejects_duplicate_email(): void
    {
        EmailAccount::create([
            'email' => 'dup@bizmark.id',
            'name' => 'First',
            'type' => 'personal',
            'department' => 'finance',
        ]);

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->post('/admin/email-accounts', [
                'email' => 'dup@bizmark.id',
                'name' => 'Second',
                'type' => 'personal',
                'department' => 'finance',
            ]);

        $response->assertSessionHasErrors('email');
    }

    // ── Show ───────────────────────────────────────────────────────────

    public function test_admin_can_view_email_account(): void
    {
        $account = EmailAccount::create([
            'email' => 'info@bizmark.id',
            'name' => 'Info',
            'type' => 'shared',
            'department' => 'general',
        ]);

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->get("/admin/email-accounts/{$account->id}");

        $response->assertStatus(200);
    }

    // ── Update ─────────────────────────────────────────────────────────

    public function test_admin_can_update_email_account(): void
    {
        $account = EmailAccount::create([
            'email' => 'old@bizmark.id',
            'name' => 'Old Name',
            'type' => 'personal',
            'department' => 'finance',
        ]);

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->put("/admin/email-accounts/{$account->id}", [
                'email' => 'old@bizmark.id',
                'name' => 'New Name',
                'type' => 'personal',
                'department' => 'finance',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('email_accounts', [
            'id' => $account->id,
            'name' => 'New Name',
        ]);
    }

    // ── Destroy ────────────────────────────────────────────────────────

    public function test_admin_can_delete_email_account_without_assignments(): void
    {
        $account = EmailAccount::create([
            'email' => 'delete@bizmark.id',
            'name' => 'To Delete',
            'type' => 'personal',
            'department' => 'technical',
        ]);

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->delete("/admin/email-accounts/{$account->id}");

        $response->assertRedirect(route('admin.email-accounts.index'));
        $this->assertSoftDeleted('email_accounts', ['id' => $account->id]);
    }

    public function test_cannot_delete_account_with_active_assignments(): void
    {
        $account = EmailAccount::create([
            'email' => 'assigned@bizmark.id',
            'name' => 'Assigned Account',
            'type' => 'shared',
            'department' => 'sales',
        ]);

        // Simulate active assignment directly in pivot
        \DB::table('email_assignments')->insert([
            'email_account_id' => $account->id,
            'user_id' => $this->adminUser->id,
            'role' => 'primary',
            'can_send' => true,
            'can_receive' => true,
            'can_delete' => false,
            'can_assign_others' => false,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->delete("/admin/email-accounts/{$account->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('email_accounts', ['id' => $account->id]);
    }

    public function test_force_delete_bypasses_assignment_check(): void
    {
        $account = EmailAccount::create([
            'email' => 'force@bizmark.id',
            'name' => 'Force Delete',
            'type' => 'shared',
            'department' => 'technical',
        ]);

        \DB::table('email_assignments')->insert([
            'email_account_id' => $account->id,
            'user_id' => $this->adminUser->id,
            'role' => 'backup',
            'can_send' => false,
            'can_receive' => true,
            'can_delete' => false,
            'can_assign_others' => false,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->delete("/admin/email-accounts/{$account->id}", ['force' => '1']);

        $response->assertRedirect(route('admin.email-accounts.index'));
        $this->assertSoftDeleted('email_accounts', ['id' => $account->id]);
    }

    // ── Auth ───────────────────────────────────────────────────────────

    public function test_unauthenticated_cannot_access_email_accounts(): void
    {
        $this->get('/admin/email-accounts')
            ->assertRedirect('/login');
    }
}
