<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckEmailManagementAccess;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Models\EmailSubscriber;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEmailSubscriberTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Admin']);
        Permission::firstOrCreate(['name' => 'email.manage_subscribers'], ['display_name' => 'Manage Subscribers', 'group' => 'email']);
        Permission::firstOrCreate(['name' => 'email.manage'], ['display_name' => 'Manage Email', 'group' => 'email']);
        $role->permissions()->syncWithoutDetaching(Permission::pluck('id'));

        $this->adminUser = User::factory()->create(['role_id' => $role->id]);
    }

    private function bypass(): array
    {
        return [CheckPermission::class, EnsureTwoFactorVerified::class, CheckEmailManagementAccess::class];
    }

    private function makeSubscriber(array $overrides = []): EmailSubscriber
    {
        return EmailSubscriber::create(array_merge([
            'email' => 'sub'.uniqid().'@example.com',
            'name' => 'Test Subscriber',
            'status' => 'active',
            'source' => 'manual',
        ], $overrides));
    }

    // ── Index ──────────────────────────────────────────────────────────

    public function test_admin_can_view_subscribers_list(): void
    {
        $this->makeSubscriber();

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->get('/admin/subscribers');

        $response->assertStatus(200);
    }

    public function test_index_can_filter_by_status(): void
    {
        $this->makeSubscriber(['status' => 'active']);
        $this->makeSubscriber(['status' => 'unsubscribed']);

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->get('/admin/subscribers?status=active');

        $response->assertStatus(200);
    }

    public function test_index_can_search_by_email(): void
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('ilike not supported on SQLite — PostgreSQL only');
        }

        $this->makeSubscriber(['email' => 'findme@example.com']);

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->get('/admin/subscribers?search=findme');

        $response->assertStatus(200);
    }

    // ── Create / Store ─────────────────────────────────────────────────

    public function test_admin_can_view_create_form(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->get('/admin/subscribers/create');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_subscriber(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->post('/admin/subscribers', [
                'email' => 'new@example.com',
                'name' => 'New Subscriber',
                'phone' => '6281234567890',
            ]);

        $response->assertRedirect(route('admin.subscribers.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('email_subscribers', [
            'email' => 'new@example.com',
            'status' => 'active',
            'source' => 'manual',
        ]);
    }

    public function test_store_requires_email(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->post('/admin/subscribers', ['name' => 'No Email']);

        $response->assertSessionHasErrors('email');
    }

    public function test_store_rejects_invalid_email(): void
    {
        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->post('/admin/subscribers', ['email' => 'not-an-email']);

        $response->assertSessionHasErrors('email');
    }

    public function test_store_rejects_duplicate_email(): void
    {
        $this->makeSubscriber(['email' => 'dup@example.com']);

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->post('/admin/subscribers', ['email' => 'dup@example.com']);

        $response->assertSessionHasErrors('email');
    }

    // ── Show ───────────────────────────────────────────────────────────

    public function test_admin_can_view_subscriber_detail(): void
    {
        $subscriber = $this->makeSubscriber();

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->get("/admin/subscribers/{$subscriber->id}");

        $response->assertStatus(200);
    }

    // ── Update ─────────────────────────────────────────────────────────

    public function test_admin_can_update_subscriber(): void
    {
        $subscriber = $this->makeSubscriber(['email' => 'old@example.com']);

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->put("/admin/subscribers/{$subscriber->id}", [
                'email' => 'updated@example.com',
                'name' => 'Updated Name',
                'status' => 'active',
            ]);

        $response->assertRedirect(route('admin.subscribers.index'));
        $this->assertDatabaseHas('email_subscribers', [
            'id' => $subscriber->id,
            'email' => 'updated@example.com',
            'name' => 'Updated Name',
        ]);
    }

    public function test_admin_can_unsubscribe_a_subscriber(): void
    {
        $subscriber = $this->makeSubscriber(['status' => 'active']);

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->put("/admin/subscribers/{$subscriber->id}", [
                'email' => $subscriber->email,
                'status' => 'unsubscribed',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('email_subscribers', [
            'id' => $subscriber->id,
            'status' => 'unsubscribed',
        ]);
    }

    public function test_update_rejects_invalid_status(): void
    {
        $subscriber = $this->makeSubscriber();

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->put("/admin/subscribers/{$subscriber->id}", [
                'email' => $subscriber->email,
                'status' => 'invalid-status',
            ]);

        $response->assertSessionHasErrors('status');
    }

    // ── Destroy ────────────────────────────────────────────────────────

    public function test_admin_can_delete_subscriber(): void
    {
        $subscriber = $this->makeSubscriber();

        $response = $this->withoutMiddleware($this->bypass())
            ->actingAs($this->adminUser)
            ->delete("/admin/subscribers/{$subscriber->id}");

        $response->assertRedirect(route('admin.subscribers.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('email_subscribers', ['id' => $subscriber->id]);
    }

    // ── Auth ───────────────────────────────────────────────────────────

    public function test_unauthenticated_cannot_access_subscribers(): void
    {
        $this->get('/admin/subscribers')
            ->assertRedirect('/login');
    }
}
