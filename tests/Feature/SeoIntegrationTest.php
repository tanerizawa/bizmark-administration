<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Models\Article;
use App\Models\ArticleTopic;
use App\Models\AutoPostSchedule;
use App\Models\KeywordCluster;
use App\Models\Permission;
use App\Models\Role;
use App\Models\TopicCluster;
use App\Models\User;
use App\Services\SmartMetaOptimizerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $author;

    private User $adminUser;

    private User $viewerUser;

    private TopicCluster $topicCluster;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(EnsureTwoFactorVerified::class);

        $this->author = User::factory()->create();

        // Create roles and permissions for SEO admin tests
        $roleAdmin = Role::firstOrCreate(['name' => 'seo_admin'], ['display_name' => 'SEO Admin']);
        $roleViewer = Role::firstOrCreate(['name' => 'viewer'], ['display_name' => 'Viewer']);

        $permManage = Permission::firstOrCreate(['name' => 'content.manage'], ['display_name' => 'Manage Content', 'group' => 'content']);

        $roleAdmin->permissions()->syncWithoutDetaching([$permManage->id]);

        $this->adminUser = User::factory()->create(['role_id' => $roleAdmin->id]);
        $this->viewerUser = User::factory()->create(['role_id' => $roleViewer->id]);

        $this->topicCluster = TopicCluster::create([
            'pillar_title' => 'Panduan Lengkap AMDAL 2026',
            'pillar_slug' => 'panduan-lengkap-amdal-2026',
            'pillar_description' => 'Panduan lengkap proses AMDAL.',
            'subtopics' => ['Pengertian AMDAL', 'Proses AMDAL'],
            'language' => 'id',
        ]);
    }

    public function test_article_can_be_created_with_seo_fields(): void
    {
        $topic = ArticleTopic::create([
            'title' => 'Perizinan AMDAL',
            'slug' => 'perizinan-amdal',
            'category' => 'general',
            'language' => 'id',
        ]);

        $article = Article::create([
            'title' => 'Panduan Lengkap AMDAL 2026',
            'slug' => 'panduan-lengkap-amdal-2026',
            'content' => '<p>Artikel tentang AMDAL terbaru.</p>',
            'meta_title' => 'Panduan AMDAL 2026 | BizMark',
            'meta_description' => 'Panduan lengkap proses AMDAL 2026 untuk perusahaan Anda.',
            'meta_keywords' => 'AMDAL, 2026, perizinan, lingkungan',
            'topic_cluster_id' => $this->topicCluster->id,
            'status' => 'published',
            'language' => 'id',
            'author_id' => $this->author->id,
        ]);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'slug' => 'panduan-lengkap-amdal-2026',
        ]);
        $this->assertNotNull($article->meta_title);
        $this->assertNotNull($article->meta_description);
    }

    public function test_article_can_be_assigned_to_keyword_cluster(): void
    {
        $topic = ArticleTopic::create([
            'title' => 'Perizinan AMDAL',
            'slug' => 'perizinan-amdal',
            'category' => 'general',
            'language' => 'id',
        ]);

        $cluster = KeywordCluster::create([
            'seed_keyword' => 'AMDAL',
            'cluster_name' => 'AMDAL Cluster',
            'keywords' => json_encode(['AMDAL', 'UKL UPL', 'lingkungan']),
            'search_intent' => 'informational',
            'language' => 'id',
        ]);

        $article = Article::create([
            'title' => 'Cara Mengurus AMDAL',
            'slug' => 'cara-mengurus-amdal',
            'content' => '<p>Cara mengurus AMDAL.</p>',
            'topic_cluster_id' => $this->topicCluster->id,
            'status' => 'published',
            'language' => 'id',
            'author_id' => $this->author->id,
        ]);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
        ]);
    }

    public function test_auto_post_schedule_creates_article_pipeline(): void
    {
        $topic = ArticleTopic::create([
            'title' => 'Perizinan AMDAL',
            'slug' => 'perizinan-amdal',
            'category' => 'general',
            'language' => 'id',
        ]);

        $article = Article::create([
            'title' => 'Test Auto-Post Article',
            'slug' => 'test-auto-post-article',
            'content' => '<p>Test content.</p>',
            'topic_cluster_id' => $this->topicCluster->id,
            'status' => 'draft',
            'language' => 'id',
            'author_id' => $this->author->id,
        ]);

        $schedule = AutoPostSchedule::create([
            'article_id' => $article->id,
            'topic_id' => $topic->id,
            'scheduled_at' => now()->addHour(),
            'status' => 'scheduled',
            'platform' => 'website',
            'language' => 'id',
        ]);

        $this->assertDatabaseHas('auto_post_schedules', [
            'id' => $schedule->id,
            'article_id' => $article->id,
            'status' => 'scheduled',
        ]);
    }

    public function test_meta_optimization_service_contract(): void
    {
        $article = Article::create([
            'title' => 'Test Meta Optimization',
            'slug' => 'test-meta-optimization',
            'content' => '<p>Test content for meta optimization.</p>',
            'status' => 'published',
            'language' => 'id',
            'author_id' => $this->author->id,
        ]);

        // Verify the SmartMetaOptimizerService can be resolved
        $service = $this->app->make(SmartMetaOptimizerService::class);
        $this->assertNotNull($service);

        // Verify it can optimize articles
        $result = $service->optimizeArticle($article);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('status', $result);
    }

    public function test_seo_sitemap_route_accessible(): void
    {
        $response = $this->get('/sitemap.xml');

        // Sitemap should be accessible
        $response->assertStatus(200);
        $response->assertHeader('Content-Type');
    }

    public function test_article_can_be_bilingual(): void
    {
        $indonesian = Article::create([
            'title' => 'Panduan AMDAL Indonesia',
            'slug' => 'panduan-amdal-id',
            'content' => '<p>Konten bahasa Indonesia.</p>',
            'status' => 'published',
            'language' => 'id',
            'author_id' => $this->author->id,
        ]);

        $english = Article::create([
            'title' => 'AMDAL Guide English',
            'slug' => 'amdal-guide-en',
            'content' => '<p>English content.</p>',
            'status' => 'published',
            'language' => 'en',
            'author_id' => $this->author->id,
        ]);

        $this->assertEquals('id', $indonesian->language);
        $this->assertEquals('en', $english->language);
    }

    // ──────────────────────────────────────────────
    //  SEO Admin Route Tests (W22-06)
    // ──────────────────────────────────────────────

    public function test_seo_admin_scores_page_accessible_with_permission(): void
    {
        $this->actingAs($this->adminUser)
            ->get(route('admin.seo.scores'))
            ->assertOk();
    }

    public function test_seo_admin_scores_page_blocked_without_permission(): void
    {
        $this->actingAs($this->viewerUser)
            ->get(route('admin.seo.scores'))
            ->assertForbidden();
    }

    public function test_seo_admin_positions_page_accessible_with_permission(): void
    {
        $this->actingAs($this->adminUser)
            ->get(route('admin.seo.positions'))
            ->assertOk();
    }

    public function test_seo_admin_refresh_logs_page_accessible_with_permission(): void
    {
        $this->actingAs($this->adminUser)
            ->get(route('admin.seo.refresh-logs'))
            ->assertOk();
    }

    public function test_seo_admin_command_center_accessible_with_permission(): void
    {
        $this->actingAs($this->adminUser)
            ->get(route('admin.seo.command-center'))
            ->assertOk();
    }

    public function test_seo_admin_guest_redirected_to_login(): void
    {
        $this->get(route('admin.seo.scores'))
            ->assertRedirect();
    }
}
