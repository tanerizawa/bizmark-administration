<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Jobs\GenerateAutoPostArticle;
use App\Models\ArticleTopic;
use App\Models\AutoPostConfig;
use App\Models\AutoPostSchedule;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AutoPostPipelineTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        Permission::create(['name' => 'content.manage', 'display_name' => 'Manage Content', 'group' => 'content']);
        $role->permissions()->attach(Permission::pluck('id'));

        $this->adminUser = User::factory()->create(['role_id' => $role->id]);
    }

    // ── AutoPostConfig Tests ───────────────────────────────────────────

    public function test_config_toggle_enables_auto_posting(): void
    {
        $config = AutoPostConfig::current();
        $this->assertFalse($config->is_enabled);

        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->postJson('/admin/auto-post/config/toggle');

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'is_enabled' => true]);

        $this->assertTrue(AutoPostConfig::current()->fresh()->is_enabled);
    }

    public function test_config_toggle_disables_auto_posting_when_enabled(): void
    {
        AutoPostConfig::current()->update(['is_enabled' => true]);

        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->postJson('/admin/auto-post/config/toggle');

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'is_enabled' => false]);
    }

    public function test_config_update_persists_settings(): void
    {
        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->put('/admin/auto-post/config', [
                'posts_per_day' => 2,
                'post_times' => ['09:00', '18:00'],
                'ai_model' => 'anthropic/claude-sonnet-4',
                'min_word_count' => 500,
                'max_word_count' => 1000,
                'duplicate_threshold' => 0.8,
                'internal_links_count' => 2,
                'auto_publish' => false,
                'is_enabled' => false,
            ]);

        $response->assertRedirect(route('auto-post.config'));
        $response->assertSessionHas('success');

        $config = AutoPostConfig::current()->fresh();
        $this->assertEquals(2, $config->posts_per_day);
        $this->assertEquals(['09:00', '18:00'], $config->post_times);
        $this->assertEquals(500, $config->min_word_count);
    }

    public function test_config_update_rejects_mismatched_post_times(): void
    {
        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->put('/admin/auto-post/config', [
                'posts_per_day' => 3,
                'post_times' => ['09:00', '18:00'], // Only 2, but posts_per_day=3
                'ai_model' => 'anthropic/claude-sonnet-4',
                'min_word_count' => 500,
                'max_word_count' => 1000,
                'duplicate_threshold' => 0.8,
                'internal_links_count' => 2,
            ]);

        $response->assertSessionHasErrors('post_times');
    }

    public function test_unauthenticated_cannot_toggle_config(): void
    {
        $this->postJson('/admin/auto-post/config/toggle')
            ->assertStatus(401);
    }

    // ── AutoPostSchedule Tests ─────────────────────────────────────────

    public function test_admin_can_create_schedule_for_pending_topic(): void
    {
        $topic = ArticleTopic::create([
            'title' => 'Test Topic PKL Terbaru 2026',
            'category' => 'regulation',
            'status' => 'pending',
            'priority' => 5,
        ]);

        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->post('/admin/auto-post/schedules', [
                'topic_id' => $topic->id,
                'scheduled_at' => now()->addDay()->toDateTimeString(),
            ]);

        $response->assertRedirect(route('auto-post.schedules.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('auto_post_schedules', [
            'topic_id' => $topic->id,
            'status' => 'pending',
        ]);
    }

    public function test_cannot_schedule_non_pending_topic(): void
    {
        $topic = ArticleTopic::create([
            'title' => 'Already Published Topic',
            'category' => 'tips',
            'status' => 'published',
            'priority' => 3,
        ]);

        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->post('/admin/auto-post/schedules', [
                'topic_id' => $topic->id,
                'scheduled_at' => now()->addDay()->toDateTimeString(),
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('auto_post_schedules', ['topic_id' => $topic->id]);
    }

    public function test_schedule_requires_future_datetime(): void
    {
        $topic = ArticleTopic::create([
            'title' => 'Future Topic',
            'category' => 'tips',
            'status' => 'pending',
            'priority' => 1,
        ]);

        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->post('/admin/auto-post/schedules', [
                'topic_id' => $topic->id,
                'scheduled_at' => now()->subHour()->toDateTimeString(),
            ]);

        $response->assertSessionHasErrors('scheduled_at');
    }

    public function test_admin_can_delete_pending_schedule(): void
    {
        $topic = ArticleTopic::create([
            'title' => 'Deletable Topic',
            'category' => 'general',
            'status' => 'pending',
            'priority' => 1,
        ]);
        $schedule = AutoPostSchedule::create([
            'topic_id' => $topic->id,
            'scheduled_at' => now()->addDay(),
            'status' => 'pending',
        ]);

        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->delete("/admin/auto-post/schedules/{$schedule->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('auto_post_schedules', ['id' => $schedule->id]);
    }

    public function test_cannot_delete_completed_schedule(): void
    {
        $topic = ArticleTopic::create([
            'title' => 'Completed Topic',
            'category' => 'general',
            'status' => 'published',
            'priority' => 1,
        ]);
        $schedule = AutoPostSchedule::create([
            'topic_id' => $topic->id,
            'scheduled_at' => now()->subDay(),
            'status' => 'completed',
        ]);

        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->delete("/admin/auto-post/schedules/{$schedule->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('auto_post_schedules', ['id' => $schedule->id]);
    }

    public function test_process_now_dispatches_job_for_pending_schedule(): void
    {
        Queue::fake();

        $topic = ArticleTopic::create([
            'title' => 'Immediate Process Topic',
            'category' => 'tips',
            'status' => 'pending',
            'priority' => 10,
        ]);
        $schedule = AutoPostSchedule::create([
            'topic_id' => $topic->id,
            'scheduled_at' => now()->addHour(),
            'status' => 'pending',
        ]);

        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->post("/admin/auto-post/schedules/{$schedule->id}/process-now");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Queue::assertPushed(GenerateAutoPostArticle::class, function ($job) {
            return $job->queue === 'default';
        });
    }

    public function test_process_now_rejects_non_pending_schedule(): void
    {
        Queue::fake();

        $topic = ArticleTopic::create([
            'title' => 'Processing Topic',
            'category' => 'tips',
            'status' => 'pending',
            'priority' => 1,
        ]);
        $schedule = AutoPostSchedule::create([
            'topic_id' => $topic->id,
            'scheduled_at' => now()->addHour(),
            'status' => 'processing',
        ]);

        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->post("/admin/auto-post/schedules/{$schedule->id}/process-now");

        $response->assertSessionHas('error');
        Queue::assertNothingPushed();
    }

    public function test_retry_dispatches_job_for_failed_schedule(): void
    {
        Queue::fake();

        $topic = ArticleTopic::create([
            'title' => 'Retry Topic',
            'category' => 'general',
            'status' => 'pending',
            'priority' => 1,
        ]);
        $schedule = AutoPostSchedule::create([
            'topic_id' => $topic->id,
            'scheduled_at' => now()->subDay(),
            'status' => 'failed',
            'error_message' => 'API timeout',
        ]);

        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->post("/admin/auto-post/schedules/{$schedule->id}/retry");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('auto_post_schedules', [
            'id' => $schedule->id,
            'status' => 'pending',
            'error_message' => null,
        ]);

        Queue::assertPushed(GenerateAutoPostArticle::class);
    }

    public function test_retry_rejects_non_failed_schedule(): void
    {
        Queue::fake();

        $topic = ArticleTopic::create([
            'title' => 'Pending Topic',
            'category' => 'general',
            'status' => 'pending',
            'priority' => 1,
        ]);
        $schedule = AutoPostSchedule::create([
            'topic_id' => $topic->id,
            'scheduled_at' => now()->addDay(),
            'status' => 'pending',
        ]);

        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->post("/admin/auto-post/schedules/{$schedule->id}/retry");

        $response->assertSessionHas('error');
        Queue::assertNothingPushed();
    }

    public function test_bulk_action_cancel_pending_schedules(): void
    {
        $topic1 = ArticleTopic::create(['title' => 'Bulk Topic 1', 'category' => 'general', 'status' => 'pending', 'priority' => 1]);
        $topic2 = ArticleTopic::create(['title' => 'Bulk Topic 2', 'category' => 'tips', 'status' => 'pending', 'priority' => 2]);

        AutoPostSchedule::create(['topic_id' => $topic1->id, 'scheduled_at' => now()->addDay(), 'status' => 'pending']);
        AutoPostSchedule::create(['topic_id' => $topic2->id, 'scheduled_at' => now()->addDays(2), 'status' => 'pending']);

        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->post('/admin/auto-post/schedules/bulk-action', [
                'action' => 'cancel_pending',
                'scope' => 'all',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('auto_post_schedules', ['status' => 'pending']);
        $this->assertEquals(2, AutoPostSchedule::where('status', 'cancelled')->count());
    }
}
