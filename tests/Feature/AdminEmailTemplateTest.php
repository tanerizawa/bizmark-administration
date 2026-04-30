<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckEmailManagementAccess;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Models\EmailTemplate;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEmailTemplateTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Admin']);
        Permission::firstOrCreate(['name' => 'email.manage_templates'], ['display_name' => 'Manage Templates', 'group' => 'email']);
        Permission::firstOrCreate(['name' => 'email.manage'], ['display_name' => 'Manage Email', 'group' => 'email']);
        $role->permissions()->syncWithoutDetaching(Permission::pluck('id'));

        $this->adminUser = User::factory()->create(['role_id' => $role->id]);
    }

    private function bypass(): array
    {
        return [CheckPermission::class, EnsureTwoFactorVerified::class, CheckEmailManagementAccess::class];
    }

    private function makeTemplate(array $overrides = []): EmailTemplate
    {
        return EmailTemplate::create(array_merge([
            'name' => 'Test Template '.uniqid(),
            'subject' => 'Hello {{name}}',
            'content' => '<p>Hello {{name}}</p>',
            'category' => 'newsletter',
            'is_active' => true,
        ], $overrides));
    }

    // ── Index ──────────────────────────────────────────────────────────

    public function test_admin_can_view_templates_list(): void
    {
        $this->makeTemplate();

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->get('/admin/templates');

        $response->assertStatus(200);
    }

    // ── Create / Store ─────────────────────────────────────────────────

    public function test_admin_can_view_create_form(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->get('/admin/templates/create');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_template(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->post('/admin/templates', [
                'name' => 'Welcome Email',
                'subject' => 'Welcome to BizMark',
                'content' => '<p>Welcome {{name}}!</p>',
                'category' => 'transactional',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.templates.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('email_templates', [
            'name' => 'Welcome Email',
            'category' => 'transactional',
            'is_active' => true,
        ]);
    }

    public function test_store_requires_name_subject_content_category(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->post('/admin/templates', []);

        $response->assertSessionHasErrors(['name', 'subject', 'content', 'category']);
    }

    public function test_store_rejects_invalid_category(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->post('/admin/templates', [
                'name' => 'Bad',
                'subject' => 'Bad',
                'content' => '<p>Bad</p>',
                'category' => 'invalid-category',
            ]);

        $response->assertSessionHasErrors('category');
    }

    public function test_template_is_inactive_when_checkbox_omitted(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->post('/admin/templates', [
                'name' => 'Inactive Template',
                'subject' => 'Test',
                'content' => '<p>Test</p>',
                'category' => 'promotional',
                // no is_active checkbox
            ]);

        $response->assertRedirect(route('admin.templates.index'));
        $this->assertDatabaseHas('email_templates', [
            'name' => 'Inactive Template',
            'is_active' => false,
        ]);
    }

    // ── Show ───────────────────────────────────────────────────────────

    public function test_admin_can_view_template_detail(): void
    {
        $template = $this->makeTemplate();

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->get("/admin/templates/{$template->id}");

        $response->assertStatus(200);
    }

    // ── Update ─────────────────────────────────────────────────────────

    public function test_admin_can_update_template(): void
    {
        $template = $this->makeTemplate();

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->put("/admin/templates/{$template->id}", [
                'name' => 'Updated Name',
                'subject' => 'Updated Subject',
                'content' => '<p>Updated content</p>',
                'category' => 'promotional',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.templates.index'));
        $this->assertDatabaseHas('email_templates', [
            'id' => $template->id,
            'name' => 'Updated Name',
            'category' => 'promotional',
        ]);
    }

    public function test_update_can_deactivate_template(): void
    {
        $template = $this->makeTemplate(['is_active' => true]);

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->put("/admin/templates/{$template->id}", [
                'name' => $template->name,
                'subject' => $template->subject,
                'content' => $template->content,
                'category' => $template->category,
                // no is_active
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('email_templates', [
            'id' => $template->id,
            'is_active' => false,
        ]);
    }

    // ── Destroy ────────────────────────────────────────────────────────

    public function test_admin_can_delete_template(): void
    {
        $template = $this->makeTemplate();

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->delete("/admin/templates/{$template->id}");

        $response->assertRedirect(route('admin.templates.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('email_templates', ['id' => $template->id]);
    }

    // ── Auth ───────────────────────────────────────────────────────────

    public function test_unauthenticated_cannot_access_templates(): void
    {
        $this->get('/admin/templates')
            ->assertRedirect('/login');
    }
}
