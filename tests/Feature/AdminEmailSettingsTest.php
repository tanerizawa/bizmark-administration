<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckEmailManagementAccess;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminEmailSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        Permission::firstOrCreate(
            ['name' => 'email.manage_settings'],
            ['display_name' => 'Manage Email Settings', 'group' => 'email']
        );
        Permission::firstOrCreate(
            ['name' => 'email.manage'],
            ['display_name' => 'Manage Email', 'group' => 'email']
        );
        $role->permissions()->attach(Permission::pluck('id'));

        $this->adminUser = User::factory()->create(['role_id' => $role->id]);
    }

    private function bypass(): array
    {
        return [CheckPermission::class, EnsureTwoFactorVerified::class, CheckEmailManagementAccess::class];
    }

    // ── Index ──────────────────────────────────────────────────────────

    public function test_admin_can_view_email_settings_page(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->get(route('admin.email.settings.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.email.settings.index');
        $response->assertViewHas('settings');
    }

    public function test_settings_contains_expected_keys(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->get(route('admin.email.settings.index'));

        $settings = $response->viewData('settings');
        $this->assertArrayHasKey('mail_mailer', $settings);
        $this->assertArrayHasKey('mail_from_address', $settings);
        $this->assertArrayHasKey('mail_from_name', $settings);
    }

    public function test_unauthenticated_cannot_view_settings(): void
    {
        $response = $this->get(route('admin.email.settings.index'));
        $response->assertRedirect();
    }

    // ── Update ─────────────────────────────────────────────────────────

    public function test_admin_can_update_smtp_settings(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->put(route('admin.email.settings.update'), [
                'mail_mailer' => 'smtp',
                'mail_host' => 'smtp.example.com',
                'mail_port' => 587,
                'mail_username' => 'user@example.com',
                'mail_password' => 'secret',
                'mail_encryption' => 'tls',
                'mail_from_address' => 'noreply@bizmark.id',
                'mail_from_name' => 'Bizmark Test',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_admin_can_update_mailgun_settings(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->put(route('admin.email.settings.update'), [
                'mail_mailer' => 'mailgun',
                'mail_from_address' => 'info@bizmark.id',
                'mail_from_name' => 'Bizmark',
                'mailgun_domain' => 'mg.bizmark.id',
                'mailgun_secret' => 'key-test123',
                'mailgun_endpoint' => 'api.eu.mailgun.net',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_update_requires_mail_from_address(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->put(route('admin.email.settings.update'), [
                'mail_mailer' => 'smtp',
                'mail_from_name' => 'Bizmark',
                // missing mail_from_address
            ]);

        $response->assertSessionHasErrors('mail_from_address');
    }

    public function test_update_requires_valid_email_for_from_address(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->put(route('admin.email.settings.update'), [
                'mail_mailer' => 'smtp',
                'mail_from_address' => 'not-an-email',
                'mail_from_name' => 'Bizmark',
            ]);

        $response->assertSessionHasErrors('mail_from_address');
    }

    public function test_update_requires_mail_mailer(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->put(route('admin.email.settings.update'), [
                'mail_from_address' => 'info@bizmark.id',
                'mail_from_name' => 'Bizmark',
                // missing mail_mailer
            ]);

        $response->assertSessionHasErrors('mail_mailer');
    }

    public function test_update_accepts_alias_fields(): void
    {
        // Controller merges smtp_* aliases → mail_*
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->put(route('admin.email.settings.update'), [
                'smtp_mailer' => 'smtp',
                'smtp_host' => 'smtp.brevo.com',
                'smtp_port' => 587,
                'smtp_username' => 'brevo@example.com',
                'smtp_password' => 'brevopass',
                'smtp_encryption' => 'tls',
                'from_email' => 'noreply@bizmark.id',
                'from_name' => 'Bizmark',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_update_redirect_to_email_management_when_requested(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->put(route('admin.email.settings.update'), [
                'mail_mailer' => 'log',
                'mail_from_address' => 'test@bizmark.id',
                'mail_from_name' => 'Test',
                'redirect_to' => 'email-management',
            ]);

        $response->assertRedirect();
        $this->assertStringContainsString('tab=settings', $response->headers->get('Location'));
    }

    // ── Test Email ─────────────────────────────────────────────────────

    public function test_admin_can_send_test_email(): void
    {
        Mail::fake();

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->postJson(route('admin.email.settings.test'), [
                'test_email' => 'test@example.com',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_test_email_requires_valid_email(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->postJson(route('admin.email.settings.test'), [
                'test_email' => 'invalid',
            ]);

        $response->assertStatus(422);
    }

    public function test_test_email_requires_email_field(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->postJson(route('admin.email.settings.test'), []);

        $response->assertStatus(422);
    }
}
