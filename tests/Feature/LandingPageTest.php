<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    private User $author;

    protected function setUp(): void
    {
        parent::setUp();
        $this->author = User::factory()->create();
    }

    private function createPublishedArticle(array $overrides = []): Article
    {
        return Article::create(array_merge([
            'title' => 'Panduan Lengkap AMDAL 2026',
            'slug' => 'panduan-lengkap-amdal-2026',
            'content' => '<p>Artikel tentang AMDAL terbaru.</p>',
            'category' => 'general',
            'language' => 'id',
            'tags' => ['AMDAL', 'perizinan'],
            'status' => 'published',
            'published_at' => now()->subDay(),
            'author_id' => $this->author->id,
            'meta_title' => 'Panduan AMDAL 2026 | BizMark',
            'meta_description' => 'Panduan lengkap proses AMDAL 2026.',
            'views_count' => 0,
        ], $overrides));
    }

    // ─── Landing Page Tests ───────────────────────────────

    public function test_landing_page_id_returns_200(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/');

        $response->assertStatus(200);
    }

    public function test_landing_page_en_returns_200(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get('/en');

        $response->assertStatus(200);
    }

    public function test_landing_page_shows_latest_articles_when_articles_exist(): void
    {
        $this->createPublishedArticle();
        $this->createPublishedArticle([
            'title' => 'Tips Mengurus NIB 2026',
            'slug' => 'tips-mengurus-nib-2026',
        ]);
        $this->createPublishedArticle([
            'title' => 'Regulasi Terbaru OSS RBA',
            'slug' => 'regulasi-terbaru-oss-rba',
        ]);

        $response = $this->withSession(['locale' => 'id'])->get('/');

        $response->assertStatus(200);
        $response->assertSee('Panduan Lengkap AMDAL 2026', false);
    }

    public function test_landing_page_handles_empty_articles_gracefully(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/');

        $response->assertStatus(200);
    }

    public function test_landing_page_displays_en_articles_on_en_route(): void
    {
        $this->createPublishedArticle([
            'title' => 'English Article Title',
            'language' => 'en',
            'slug' => 'english-article-title',
        ]);

        $response = $this->withSession(['locale' => 'en'])->get('/en');

        $response->assertStatus(200);
    }

    // ─── Static Page Tests ────────────────────────────────

    public function test_about_page_id_returns_200(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/tentang');

        $response->assertStatus(200);
    }

    public function test_about_page_en_returns_200(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get('/en/about');

        $response->assertStatus(200);
    }

    public function test_process_page_id_returns_200(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/proses');

        $response->assertStatus(200);
    }

    public function test_process_page_en_returns_200(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get('/en/process');

        $response->assertStatus(200);
    }

    public function test_privacy_policy_page_id_returns_200(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/kebijakan-privasi');

        $response->assertStatus(200);
    }

    public function test_privacy_policy_page_en_returns_200(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get('/en/privacy-policy');

        $response->assertStatus(200);
    }

    public function test_terms_conditions_page_id_returns_200(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/syarat-ketentuan');

        $response->assertStatus(200);
    }

    public function test_terms_conditions_page_en_returns_200(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get('/en/terms-conditions');

        $response->assertStatus(200);
    }

    // ─── SEO Route Tests ─────────────────────────────────

    public function test_sitemap_xml_returns_200_with_correct_content_type(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');
    }

    public function test_robots_txt_returns_200(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
    }

    public function test_rss_feed_returns_200(): void
    {
        $response = $this->get('/feed/rss');

        $response->assertStatus(200);
    }

    public function test_atom_feed_returns_200(): void
    {
        $response = $this->get('/feed/atom');

        $response->assertStatus(200);
    }

    public function test_health_endpoint_returns_ok(): void
    {
        $response = $this->get('/health');

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);
    }

    // ─── Service Pages Tests ─────────────────────────────

    public function test_services_index_page_returns_200(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/layanan');

        $response->assertStatus(200);
    }

    public function test_services_index_en_returns_200(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get('/en/services');

        $response->assertStatus(200);
    }

    // ─── FAQ Page Tests ──────────────────────────────────

    public function test_faq_page_returns_200(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/faq');

        $response->assertStatus(200);
    }

    // ─── Contact Page Tests ──────────────────────────────

    public function test_contact_page_returns_200(): void
    {
        $response = $this->get('/contact');

        $response->assertStatus(200);
    }

    // ─── Pillar / Panduan Page Tests ─────────────────────

    public function test_pillar_index_page_returns_200(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/panduan');

        $response->assertStatus(200);
    }

    // ─── Calculator & Tool Page Tests ────────────────────

    public function test_calculator_page_returns_200(): void
    {
        $response = $this->get('/kalkulator-perizinan');

        $response->assertStatus(200);
    }

    public function test_polygon_shp_maker_page_returns_200(): void
    {
        $response = $this->get('/polygon-shp-maker');

        $response->assertStatus(200);
    }

    // ─── Language Switcher Tests ─────────────────────────

    public function test_locale_switcher_redirects_to_id(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get('/locale/id');

        $response->assertStatus(302);
    }

    public function test_locale_switcher_redirects_to_en(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/locale/en');

        $response->assertStatus(302);
    }

    // ─── Backward Compatibility Redirect Tests ───────────

    public function test_old_id_url_redirects_to_root(): void
    {
        $response = $this->get('/id');

        $response->assertStatus(301);
    }

    public function test_old_id_blog_redirects_to_blog(): void
    {
        $response = $this->get('/id/blog');

        $response->assertStatus(301);
    }

    public function test_old_id_layanan_redirects_to_layanan(): void
    {
        $response = $this->get('/id/layanan');

        $response->assertStatus(301);
    }

    // ─── Consultation Estimate Page Tests ────────────────

    public function test_consultation_estimate_page_returns_200(): void
    {
        $response = $this->get('/estimasi-biaya');

        $response->assertStatus(200);
    }

    // ─── Career Page Tests ───────────────────────────────

    public function test_career_page_returns_200(): void
    {
        $response = $this->get('/karir');

        $response->assertStatus(200);
    }
}
