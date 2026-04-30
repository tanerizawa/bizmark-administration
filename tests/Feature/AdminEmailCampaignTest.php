<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckEmailManagementAccess;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Models\EmailCampaign;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEmailCampaignTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Admin']);
        Permission::firstOrCreate(['name' => 'email.manage_campaigns'], ['display_name' => 'Manage Campaigns', 'group' => 'email']);
        Permission::firstOrCreate(['name' => 'email.manage'], ['display_name' => 'Manage Email', 'group' => 'email']);
        $role->permissions()->syncWithoutDetaching(Permission::pluck('id'));

        $this->adminUser = User::factory()->create(['role_id' => $role->id]);
    }

    private function bypass(): array
    {
        return [CheckPermission::class, EnsureTwoFactorVerified::class, CheckEmailManagementAccess::class];
    }

    private function makeCampaign(array $overrides = []): EmailCampaign
    {
        return EmailCampaign::create(array_merge([
            'name' => 'Test Campaign '.uniqid(),
            'subject' => 'Hello from BizMark',
            'content' => '<p>Campaign content here</p>',
            'status' => 'draft',
            'recipient_type' => 'all',
            'created_by' => $this->adminUser->id,
        ], $overrides));
    }

    // ── Index ──────────────────────────────────────────────────────────

    public function test_admin_can_view_campaigns_list(): void
    {
        $this->makeCampaign();

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->get('/admin/campaigns');

        $response->assertStatus(200);
    }

    // ── Store ──────────────────────────────────────────────────────────

    public function test_admin_can_create_draft_campaign(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->post('/admin/campaigns', [
                'name' => 'Newsletter April',
                'subject' => 'April Updates',
                'content' => '<p>April content</p>',
                'recipient_type' => 'all',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('email_campaigns', [
            'name' => 'Newsletter April',
            'status' => 'draft',
        ]);
    }

    public function test_store_with_future_scheduled_at_creates_scheduled_campaign(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->post('/admin/campaigns', [
                'name' => 'Scheduled Newsletter',
                'subject' => 'Scheduled Subject',
                'content' => '<p>Content</p>',
                'recipient_type' => 'all',
                'scheduled_at' => now()->addDay()->toDateTimeString(),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('email_campaigns', [
            'name' => 'Scheduled Newsletter',
            'status' => 'scheduled',
        ]);
    }

    public function test_store_rejects_past_scheduled_at(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->post('/admin/campaigns', [
                'name' => 'Bad Schedule',
                'subject' => 'Bad',
                'content' => '<p>Content</p>',
                'recipient_type' => 'all',
                'scheduled_at' => now()->subHour()->toDateTimeString(),
            ]);

        $response->assertSessionHasErrors('scheduled_at');
    }

    public function test_store_requires_name_subject_content_recipient_type(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->post('/admin/campaigns', []);

        $response->assertSessionHasErrors(['name', 'subject', 'content', 'recipient_type']);
    }

    // ── Show ───────────────────────────────────────────────────────────

    public function test_admin_can_view_campaign_detail(): void
    {
        $campaign = $this->makeCampaign();

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->get("/admin/campaigns/{$campaign->id}");

        $response->assertStatus(200);
    }

    // ── Send flow ──────────────────────────────────────────────────────

    public function test_send_page_shows_recipients_for_draft_campaign(): void
    {
        $campaign = $this->makeCampaign(['status' => 'draft']);

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->get("/admin/campaigns/{$campaign->id}/send");

        $response->assertStatus(200);
    }

    public function test_send_page_redirects_for_already_sent_campaign(): void
    {
        $campaign = $this->makeCampaign(['status' => 'sent', 'sent_at' => now()]);

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->get("/admin/campaigns/{$campaign->id}/send");

        $response->assertRedirect(route('admin.campaigns.show', $campaign));
        $response->assertSessionHas('error');
    }

    // ── Cancel ─────────────────────────────────────────────────────────

    public function test_admin_can_cancel_scheduled_campaign(): void
    {
        $campaign = $this->makeCampaign([
            'status' => 'scheduled',
            'scheduled_at' => now()->addDay(),
        ]);

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->post("/admin/campaigns/{$campaign->id}/cancel");

        $response->assertRedirect(route('admin.campaigns.show', $campaign));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('email_campaigns', [
            'id' => $campaign->id,
            'status' => 'cancelled',
            'scheduled_at' => null,
        ]);
    }

    public function test_cannot_cancel_draft_campaign(): void
    {
        $campaign = $this->makeCampaign(['status' => 'draft']);

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->post("/admin/campaigns/{$campaign->id}/cancel");

        $response->assertRedirect(route('admin.campaigns.show', $campaign));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('email_campaigns', [
            'id' => $campaign->id,
            'status' => 'draft',
        ]);
    }

    // ── Export ─────────────────────────────────────────────────────────

    public function test_admin_can_export_sent_campaign_as_csv(): void
    {
        $campaign = $this->makeCampaign(['status' => 'sent', 'sent_at' => now()]);

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->get("/admin/campaigns/{$campaign->id}/export");

        $response->assertStatus(200);
        $this->assertStringContainsStringIgnoringCase('text/csv', $response->headers->get('Content-Type'));
    }

    public function test_cannot_export_draft_campaign(): void
    {
        $campaign = $this->makeCampaign(['status' => 'draft']);

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->get("/admin/campaigns/{$campaign->id}/export");

        $response->assertRedirect(route('admin.campaigns.show', $campaign));
        $response->assertSessionHas('error');
    }

    // ── Destroy ────────────────────────────────────────────────────────

    public function test_admin_can_delete_draft_campaign(): void
    {
        $campaign = $this->makeCampaign(['status' => 'draft']);

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->delete("/admin/campaigns/{$campaign->id}");

        $response->assertRedirect(route('admin.campaigns.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('email_campaigns', ['id' => $campaign->id]);
    }

    public function test_cannot_delete_sending_campaign(): void
    {
        $campaign = $this->makeCampaign(['status' => 'sending']);

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->delete("/admin/campaigns/{$campaign->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('email_campaigns', ['id' => $campaign->id]);
    }

    // ── Auth ───────────────────────────────────────────────────────────

    public function test_unauthenticated_cannot_access_campaigns(): void
    {
        $this->get('/admin/campaigns')
            ->assertRedirect('/login');
    }
}
