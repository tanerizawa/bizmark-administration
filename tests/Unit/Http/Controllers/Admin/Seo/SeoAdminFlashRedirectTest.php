<?php

namespace Tests\Unit\Http\Controllers\Admin\Seo;

use App\Http\Controllers\Admin\Seo\Concerns\SeoAdminFlashRedirect;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Tests\TestCase;

/**
 * Menggunakan nama route yang sudah terdaftar di aplikasi (tanpa mendaftarkan route baru di runtime).
 */
class SeoAdminFlashRedirectTest extends TestCase
{
    public function test_seo_route_flash_without_route_parameters(): void
    {
        $controller = new class extends Controller {
            use SeoAdminFlashRedirect;

            public function run(): RedirectResponse
            {
                return $this->seoRouteFlash('login', 'success', 'saved');
            }
        };

        $response = $controller->run();

        $this->assertSame(302, $response->getStatusCode());
        $location = $response->headers->get('Location') ?? '';
        $this->assertStringContainsString('login', $location);
    }

    public function test_seo_route_flash_with_route_parameters(): void
    {
        $controller = new class extends Controller {
            use SeoAdminFlashRedirect;

            public function run(): RedirectResponse
            {
                return $this->seoRouteFlash('blog.article.id', 'error', 'bad', ['slug' => 'unit-test-slug']);
            }
        };

        $response = $controller->run();

        $this->assertSame(302, $response->getStatusCode());
        $location = $response->headers->get('Location') ?? '';
        $this->assertStringContainsString('unit-test-slug', $location);
    }
}
