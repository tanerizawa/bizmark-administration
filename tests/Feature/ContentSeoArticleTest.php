<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Models\Article;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContentSeoArticleTest extends TestCase
{
    use RefreshDatabase;

    private User $contentEditor;

    private User $viewerUser;

    private Article $publishedArticle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(EnsureTwoFactorVerified::class);

        $roleEditor = Role::firstOrCreate(['name' => 'content_editor'], ['display_name' => 'Content Editor']);
        $roleViewer = Role::firstOrCreate(['name' => 'viewer'], ['display_name' => 'Viewer']);

        $permManage = Permission::firstOrCreate(['name' => 'content.manage'], ['display_name' => 'Manage Content',  'group' => 'content']);
        $permView = Permission::firstOrCreate(['name' => 'content.view_articles'], ['display_name' => 'View Articles',   'group' => 'content']);
        $permCreate = Permission::firstOrCreate(['name' => 'content.create_articles'], ['display_name' => 'Create Articles', 'group' => 'content']);
        $permEdit = Permission::firstOrCreate(['name' => 'content.edit_articles'], ['display_name' => 'Edit Articles',   'group' => 'content']);
        $permDelete = Permission::firstOrCreate(['name' => 'content.delete_articles'], ['display_name' => 'Delete Articles', 'group' => 'content']);
        $permPublish = Permission::firstOrCreate(['name' => 'content.publish_articles'], ['display_name' => 'Publish Articles', 'group' => 'content']);
        $roleEditor->permissions()->syncWithoutDetaching([
            $permManage->id, $permView->id, $permCreate->id,
            $permEdit->id, $permDelete->id, $permPublish->id,
        ]);

        $this->contentEditor = User::factory()->create(['role_id' => $roleEditor->id]);
        $this->viewerUser = User::factory()->create(['role_id' => $roleViewer->id]);

        $this->publishedArticle = Article::create([
            'title' => 'Panduan Lengkap Pengurusan UKL-UPL',
            'slug' => 'panduan-ukl-upl',
            'excerpt' => 'Ringkasan panduan UKL-UPL.',
            'content' => '<p>Konten artikel lengkap tentang UKL-UPL.</p>',
            'category' => 'perizinan',
            'language' => 'id',
            'status' => 'published',
            'published_at' => now()->subDay(),
            'author_id' => $this->contentEditor->id,
            'source_type' => 'manual',
        ]);
    }

    // ── Public Blog ──────────────────────────────────────────────

    public function test_public_blog_index_is_accessible(): void
    {
        $this->get(route('blog.index.id'))
            ->assertOk();
    }

    public function test_public_blog_shows_published_article(): void
    {
        $this->get(route('blog.article.id', $this->publishedArticle->slug))
            ->assertOk()
            ->assertSee($this->publishedArticle->title);
    }

    public function test_public_blog_returns_404_for_unknown_slug(): void
    {
        $this->get(route('blog.article.id', 'slug-tidak-ada'))
            ->assertNotFound();
    }

    public function test_draft_article_not_accessible_publicly(): void
    {
        $draft = Article::create([
            'title' => 'Draft Artikel',
            'slug' => 'draft-artikel',
            'content' => '<p>Isi draft.</p>',
            'category' => 'perizinan',
            'language' => 'id',
            'status' => 'draft',
            'source_type' => 'manual',
            'author_id' => $this->contentEditor->id,
        ]);

        $this->get(route('blog.article.id', $draft->slug))
            ->assertNotFound();
    }

    // ── Admin Article CRUD ────────────────────────────────────────

    public function test_content_editor_can_list_articles(): void
    {
        $this->actingAs($this->contentEditor)
            ->get(route('articles.index'))
            ->assertOk();
    }

    public function test_viewer_cannot_list_articles(): void
    {
        $this->actingAs($this->viewerUser)
            ->get(route('articles.index'))
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_access_admin_articles(): void
    {
        $this->get(route('articles.index'))
            ->assertRedirect();
    }

    public function test_content_editor_can_create_article(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->contentEditor)
            ->post(route('articles.store'), [
                'title' => 'Tips Perizinan OSS RBA',
                'excerpt' => 'Ringkasan tips OSS RBA.',
                'content' => '<p>Konten lengkap tentang perizinan OSS RBA.</p>',
                'category' => 'tips',
                'language' => 'id',
                'status' => 'draft',
                'source_type' => 'manual',
                'tags' => ['oss', 'perizinan', 'rba'],
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('articles', [
            'title' => 'Tips Perizinan OSS RBA',
            'status' => 'draft',
        ]);
    }

    public function test_viewer_cannot_create_article(): void
    {
        $this->actingAs($this->viewerUser)
            ->post(route('articles.store'), [
                'title' => 'Test Article',
                'content' => '<p>Test</p>',
                'category' => 'perizinan',
                'language' => 'id',
                'status' => 'draft',
            ])
            ->assertForbidden();
    }

    public function test_content_editor_can_delete_article(): void
    {
        $article = Article::create([
            'title' => 'Artikel Yang Akan Dihapus',
            'slug' => 'artikel-hapus',
            'content' => '<p>Isi artikel.</p>',
            'category' => 'perizinan',
            'language' => 'id',
            'status' => 'draft',
            'source_type' => 'manual',
            'author_id' => $this->contentEditor->id,
        ]);

        $this->actingAs($this->contentEditor)
            ->delete(route('articles.destroy', $article->slug))
            ->assertRedirect();

        $this->assertSoftDeleted('articles', ['id' => $article->id]);
    }
}
