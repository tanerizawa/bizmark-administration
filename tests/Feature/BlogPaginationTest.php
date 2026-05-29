<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogPaginationTest extends TestCase
{
    use RefreshDatabase;

    private User $author;

    private static int $articleCounter = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->author = User::factory()->create();
        static::$articleCounter = 0;
    }

    private function createPublishedArticle(array $overrides = []): Article
    {
        static::$articleCounter++;
        $counter = static::$articleCounter;

        return Article::create(array_merge([
            'title' => "Artikel Blog #{$counter}",
            'slug' => "artikel-blog-{$counter}",
            'content' => '<p>Konten artikel blog.</p>',
            'excerpt' => 'Ringkasan artikel blog.',
            'category' => 'general',
            'language' => 'id',
            'tags' => ['blog', 'perizinan'],
            'status' => 'published',
            'published_at' => now()->subDays($counter),
            'author_id' => $this->author->id,
            'meta_title' => "Artikel Blog #{$counter} | BizMark",
            'meta_description' => "Deskripsi artikel blog #{$counter}.",
            'views_count' => 0,
            'reading_time' => 3,
        ], $overrides));
    }

    // ─── Blog Index Tests ────────────────────────────────

    public function test_blog_index_id_returns_200(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/blog');

        $response->assertStatus(200);
    }

    public function test_blog_index_en_returns_200(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get('/en/blog');

        $response->assertStatus(200);
    }

    public function test_blog_index_shows_published_articles(): void
    {
        $this->createPublishedArticle();

        $response = $this->withSession(['locale' => 'id'])->get('/blog');

        $response->assertStatus(200);
        $response->assertSee('Artikel Blog #1', false);
    }

    public function test_blog_index_hides_draft_articles(): void
    {
        Article::create([
            'title' => 'Artikel Draft Rahasia',
            'slug' => 'artikel-draft-rahasia',
            'content' => '<p>Konten draft.</p>',
            'category' => 'general',
            'language' => 'id',
            'status' => 'draft',
            'author_id' => $this->author->id,
        ]);

        $response = $this->withSession(['locale' => 'id'])->get('/blog');

        $response->assertStatus(200);
        $response->assertDontSee('Artikel Draft Rahasia');
    }

    public function test_blog_index_shows_only_id_articles_on_id_route(): void
    {
        $this->createPublishedArticle(['language' => 'id']);
        $this->createPublishedArticle(['language' => 'en', 'slug' => 'en-article-1', 'title' => 'English Article']);

        $response = $this->withSession(['locale' => 'id'])->get('/blog');

        $response->assertStatus(200);
        $response->assertSee('Artikel Blog #1', false);
        $response->assertDontSee('English Article');
    }

    public function test_blog_index_shows_only_en_articles_on_en_route(): void
    {
        $this->createPublishedArticle(['language' => 'en', 'slug' => 'en-article-1', 'title' => 'English First']);
        $this->createPublishedArticle(['language' => 'id', 'slug' => 'id-article-1', 'title' => 'Indonesian Article']);

        $response = $this->withSession(['locale' => 'en'])->get('/en/blog');

        $response->assertStatus(200);
        $response->assertSee('English First', false);
        $response->assertDontSee('Indonesian Article');
    }

    // ─── Blog Pagination Tests ───────────────────────────

    public function test_blog_index_paginates_at_12_per_page(): void
    {
        // Create 13 articles (page 1 has 12, page 2 has 1)
        for ($i = 0; $i < 13; $i++) {
            $this->createPublishedArticle();
        }

        $response = $this->withSession(['locale' => 'id'])->get('/blog');

        $response->assertStatus(200);
        $response->assertSee('Artikel Blog #1', false);
        $response->assertDontSee('Artikel Blog #13');
    }

    public function test_blog_index_page_2_works(): void
    {
        for ($i = 0; $i < 15; $i++) {
            $this->createPublishedArticle();
        }

        $response = $this->withSession(['locale' => 'id'])->get('/blog?page=2');

        $response->assertStatus(200);
    }

    public function test_blog_index_empty_state_when_no_articles(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/blog');

        $response->assertStatus(200);
    }

    // ─── Blog Category Tests ─────────────────────────────

    public function test_blog_category_page_returns_200(): void
    {
        $this->createPublishedArticle(['category' => 'news']);

        $response = $this->withSession(['locale' => 'id'])->get('/blog/kategori/news');

        $response->assertStatus(200);
    }

    public function test_blog_category_filters_by_category(): void
    {
        $this->createPublishedArticle(['category' => 'news', 'title' => 'Berita Terbaru']);
        $this->createPublishedArticle(['category' => 'tips', 'title' => 'Tips Terbaru']);

        $response = $this->withSession(['locale' => 'id'])->get('/blog/kategori/news');

        $response->assertStatus(200);
        $response->assertSee('Berita Terbaru', false);
        $response->assertDontSee('Tips Terbaru');
    }

    public function test_blog_category_en_returns_200(): void
    {
        $this->createPublishedArticle(['language' => 'en', 'category' => 'news']);

        $response = $this->withSession(['locale' => 'en'])->get('/en/blog/category/news');

        $response->assertStatus(200);
    }

    public function test_blog_category_empty_state_for_unmatched_category(): void
    {
        $this->createPublishedArticle(['category' => 'news']);

        $response = $this->withSession(['locale' => 'id'])->get('/blog/kategori/regulation');

        $response->assertStatus(200);
    }

    // ─── Blog Tag Tests ──────────────────────────────────

    public function test_blog_tag_page_returns_200(): void
    {
        $this->createPublishedArticle(['tags' => ['AMDAL', 'lingkungan']]);

        $response = $this->withSession(['locale' => 'id'])->get('/blog/tag/AMDAL');

        $response->assertStatus(200);
    }

    public function test_blog_tag_filters_by_tag(): void
    {
        $this->createPublishedArticle(['tags' => ['AMDAL'], 'title' => 'Artikel AMDAL']);
        $this->createPublishedArticle(['tags' => ['NIB'], 'title' => 'Artikel NIB', 'slug' => 'artikel-nib']);

        $response = $this->withSession(['locale' => 'id'])->get('/blog/tag/AMDAL');

        $response->assertStatus(200);
        $response->assertSee('Artikel AMDAL', false);
        $response->assertDontSee('Artikel NIB');
    }

    public function test_blog_tag_en_returns_200(): void
    {
        $this->createPublishedArticle(['language' => 'en', 'slug' => 'en-amdal', 'tags' => ['AMDAL']]);

        $response = $this->withSession(['locale' => 'en'])->get('/en/blog/tag/AMDAL');

        $response->assertStatus(200);
    }

    public function test_blog_tag_empty_state_for_unmatched_tag(): void
    {
        $this->createPublishedArticle(['tags' => ['AMDAL']]);

        $response = $this->withSession(['locale' => 'id'])->get('/blog/tag/NONEXISTENT');

        $response->assertStatus(200);
    }

    // ─── Blog Article Detail Tests ───────────────────────

    public function test_blog_article_detail_returns_200(): void
    {
        $article = $this->createPublishedArticle();

        $response = $this->withSession(['locale' => 'id'])->get("/blog/{$article->slug}");

        $response->assertStatus(200);
        $response->assertSee($article->title, false);
    }

    public function test_blog_article_detail_increments_views(): void
    {
        $article = $this->createPublishedArticle(['views_count' => 0]);
        $initialViews = $article->views_count;

        $this->withSession(['locale' => 'id'])->get("/blog/{$article->slug}");

        $article->refresh();
        $this->assertGreaterThan($initialViews, $article->views_count);
    }

    public function test_blog_article_detail_shows_related_articles(): void
    {
        $article = $this->createPublishedArticle(['category' => 'news']);
        $related = $this->createPublishedArticle(['category' => 'news', 'title' => 'Related News Article']);

        $response = $this->withSession(['locale' => 'id'])->get("/blog/{$article->slug}");

        $response->assertStatus(200);
        $response->assertSee('Related News Article', false);
    }

    public function test_blog_article_detail_returns_404_for_non_existent_slug(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/blog/non-existent-slug-xyz');

        $response->assertStatus(404);
    }

    public function test_blog_article_detail_en_returns_200(): void
    {
        $article = $this->createPublishedArticle(['language' => 'en', 'title' => 'English Article Detail']);

        $response = $this->withSession(['locale' => 'en'])->get("/en/blog/{$article->slug}");

        $response->assertStatus(200);
        $response->assertSee('English Article Detail', false);
    }

    public function test_blog_article_detail_hides_draft_articles(): void
    {
        $draft = Article::create([
            'title' => 'Draft Article',
            'slug' => 'draft-article',
            'content' => '<p>This is draft.</p>',
            'category' => 'general',
            'language' => 'id',
            'status' => 'draft',
            'author_id' => $this->author->id,
        ]);

        $response = $this->withSession(['locale' => 'id'])->get("/blog/{$draft->slug}");

        $response->assertStatus(404);
    }

    // ─── Blog Article Detail — Author Relationship Tests ─

    public function test_blog_article_detail_loads_author_relationship(): void
    {
        $article = $this->createPublishedArticle();

        $response = $this->withSession(['locale' => 'id'])->get("/blog/{$article->slug}");

        $response->assertStatus(200);
    }
}
