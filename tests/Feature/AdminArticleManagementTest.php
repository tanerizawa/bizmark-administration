<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Models\Article;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminArticleManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $editor;

    private User $viewerUser;

    private Article $draftArticle;

    private Article $publishedArticle;

    private Article $archivedArticle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(EnsureTwoFactorVerified::class);

        $roleEditor = Role::firstOrCreate(['name' => 'content_editor'], ['display_name' => 'Content Editor']);
        $roleViewer = Role::firstOrCreate(['name' => 'viewer'], ['display_name' => 'Viewer']);

        $permManage = Permission::firstOrCreate(['name' => 'content.manage'], ['display_name' => 'Manage Content', 'group' => 'content']);
        $permView = Permission::firstOrCreate(['name' => 'content.view_articles'], ['display_name' => 'View Articles', 'group' => 'content']);
        $permCreate = Permission::firstOrCreate(['name' => 'content.create_articles'], ['display_name' => 'Create Articles', 'group' => 'content']);
        $permEdit = Permission::firstOrCreate(['name' => 'content.edit_articles'], ['display_name' => 'Edit Articles', 'group' => 'content']);
        $permDelete = Permission::firstOrCreate(['name' => 'content.delete_articles'], ['display_name' => 'Delete Articles', 'group' => 'content']);
        $permPublish = Permission::firstOrCreate(['name' => 'content.publish_articles'], ['display_name' => 'Publish Articles', 'group' => 'content']);

        // NOTE: content.manage is required because ArticleController@__construct calls
        // authorizePermission('content.manage') which applies to ALL controller actions
        // as middleware (without ->only() filter).
        $roleEditor->permissions()->syncWithoutDetaching([
            $permManage->id, $permView->id, $permCreate->id, $permEdit->id, $permDelete->id, $permPublish->id,
        ]);

        $this->editor = User::factory()->create(['role_id' => $roleEditor->id]);
        $this->viewerUser = User::factory()->create(['role_id' => $roleViewer->id]);

        $this->draftArticle = Article::create([
            'title' => 'Draft Artikel Admin',
            'slug' => 'draft-artikel-admin',
            'content' => '<p>Konten draft.</p>',
            'category' => 'tips',
            'language' => 'id',
            'status' => 'draft',
            'source_type' => 'manual',
            'author_id' => $this->editor->id,
        ]);

        $this->publishedArticle = Article::create([
            'title' => 'Artikel Published Admin',
            'slug' => 'artikel-published-admin',
            'excerpt' => 'Ringkasan artikel published.',
            'content' => '<p>Konten published.</p>',
            'category' => 'news',
            'language' => 'id',
            'status' => 'published',
            'published_at' => now()->subDay(),
            'source_type' => 'manual',
            'author_id' => $this->editor->id,
        ]);

        $this->archivedArticle = Article::create([
            'title' => 'Artikel Archived Admin',
            'slug' => 'artikel-archived-admin',
            'content' => '<p>Konten archived.</p>',
            'category' => 'general',
            'language' => 'id',
            'status' => 'archived',
            'source_type' => 'manual',
            'author_id' => $this->editor->id,
        ]);
    }

    // ── Article Show (Detail View) ────────────────────────────────

    public function test_editor_can_view_article_detail(): void
    {
        $this->actingAs($this->editor)
            ->get(route('articles.show', $this->draftArticle->slug))
            ->assertOk()
            ->assertSee($this->draftArticle->title);
    }

    public function test_viewer_cannot_view_article_detail(): void
    {
        $this->actingAs($this->viewerUser)
            ->get(route('articles.show', $this->draftArticle->slug))
            ->assertForbidden();
    }

    // ── Article Edit ──────────────────────────────────────────────

    public function test_editor_can_access_edit_page(): void
    {
        $this->actingAs($this->editor)
            ->get(route('articles.edit', $this->draftArticle->slug))
            ->assertOk()
            ->assertSee($this->draftArticle->title);
    }

    public function test_viewer_cannot_access_edit_page(): void
    {
        $this->actingAs($this->viewerUser)
            ->get(route('articles.edit', $this->draftArticle->slug))
            ->assertForbidden();
    }

    // ── Article Update ────────────────────────────────────────────

    public function test_editor_can_update_article(): void
    {
        $this->actingAs($this->editor)
            ->put(route('articles.update', $this->draftArticle->slug), [
                'title' => 'Judul Diubah',
                'content' => '<p>Konten baru.</p>',
                'category' => 'tips',
                'status' => 'draft',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('articles', [
            'id' => $this->draftArticle->id,
            'title' => 'Judul Diubah',
        ]);
    }

    public function test_viewer_cannot_update_article(): void
    {
        $this->actingAs($this->viewerUser)
            ->put(route('articles.update', $this->draftArticle->slug), [
                'title' => 'Judul Diubah',
                'content' => '<p>Konten baru.</p>',
                'category' => 'tips',
                'status' => 'draft',
            ])
            ->assertForbidden();
    }

    public function test_update_validates_required_fields(): void
    {
        $this->actingAs($this->editor)
            ->put(route('articles.update', $this->draftArticle->slug), [
                'title' => '',
                'content' => '',
                'category' => 'invalid',
                'status' => 'invalid',
            ])
            ->assertSessionHasErrors(['title', 'content', 'category', 'status']);
    }

    // ── Publish / Unpublish / Archive Workflow ────────────────────

    public function test_editor_can_publish_draft_article(): void
    {
        $this->actingAs($this->editor)
            ->post(route('articles.publish', $this->draftArticle->slug))
            ->assertRedirect();

        $this->assertDatabaseHas('articles', [
            'id' => $this->draftArticle->id,
            'status' => 'published',
        ]);
    }

    public function test_editor_can_unpublish_published_article(): void
    {
        $this->actingAs($this->editor)
            ->post(route('articles.unpublish', $this->publishedArticle->slug))
            ->assertRedirect();

        $this->assertDatabaseHas('articles', [
            'id' => $this->publishedArticle->id,
            'status' => 'draft',
        ]);
    }

    public function test_editor_can_archive_published_article(): void
    {
        $this->actingAs($this->editor)
            ->post(route('articles.archive', $this->publishedArticle->slug))
            ->assertRedirect();

        $this->assertDatabaseHas('articles', [
            'id' => $this->publishedArticle->id,
            'status' => 'archived',
        ]);
    }

    public function test_viewer_cannot_publish_article(): void
    {
        $this->actingAs($this->viewerUser)
            ->post(route('articles.publish', $this->draftArticle->slug))
            ->assertForbidden();
    }

    public function test_viewer_cannot_unpublish_article(): void
    {
        $this->actingAs($this->viewerUser)
            ->post(route('articles.unpublish', $this->publishedArticle->slug))
            ->assertForbidden();
    }

    public function test_viewer_cannot_archive_article(): void
    {
        $this->actingAs($this->viewerUser)
            ->post(route('articles.archive', $this->publishedArticle->slug))
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_publish_article(): void
    {
        $this->post(route('articles.publish', $this->draftArticle->slug))
            ->assertRedirect();
    }

    // ── Article Create Page ───────────────────────────────────────

    public function test_editor_can_access_create_page(): void
    {
        $this->actingAs($this->editor)
            ->get(route('articles.create'))
            ->assertOk();
    }

    public function test_viewer_cannot_access_create_page(): void
    {
        $this->actingAs($this->viewerUser)
            ->get(route('articles.create'))
            ->assertForbidden();
    }

    // ── Article List with Filters ─────────────────────────────────

    public function test_article_list_filters_by_status(): void
    {
        $this->actingAs($this->editor)
            ->get(route('articles.index').'?status=draft')
            ->assertOk()
            ->assertSee($this->draftArticle->title);
    }

    public function test_article_list_filters_by_category(): void
    {
        $this->actingAs($this->editor)
            ->get(route('articles.index').'?category=tips')
            ->assertOk()
            ->assertSee($this->draftArticle->title);
    }

    public function test_article_list_filters_by_featured(): void
    {
        $this->actingAs($this->editor)
            ->get(route('articles.index').'?featured=1')
            ->assertOk();
    }

    public function test_article_list_tabs_work(): void
    {
        $this->actingAs($this->editor)
            ->get(route('articles.index').'?tab=manual')
            ->assertOk();
    }
}
