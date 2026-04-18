<?php

namespace Tests\Unit\Http\Controllers\Admin\Seo;

use App\Http\Controllers\Admin\Seo\SeoRefreshLogsController;
use App\Models\Article;
use App\Services\ContentRefreshService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Tests\TestCase;

class SeoRefreshLogsControllerTest extends TestCase
{
    public function test_run_content_refresh_redirects_when_no_stale_articles(): void
    {
        $service = $this->createMock(ContentRefreshService::class);
        $service->expects($this->once())
            ->method('getStaleArticles')
            ->with(90, 2)
            ->willReturn(new EloquentCollection());

        $request = Request::create('/admin/seo/refresh-logs/run', 'POST', []);
        $controller = new SeoRefreshLogsController();
        $response = $controller->runContentRefresh($request, $service);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(route('admin.seo.refresh-logs'), $response->headers->get('Location'));

        $success = $response->getSession()?->get('success');
        $this->assertIsString($success);
        $this->assertStringContainsString('Tidak ada artikel stale', $success);
    }

    public function test_run_content_refresh_processes_stale_batch_and_redirects(): void
    {
        $a = Article::make([
            'title' => 'Alpha',
            'slug' => 'alpha-'.uniqid('', true),
            'content' => 'c',
            'author_id' => 1,
            'status' => 'published',
        ]);
        $b = Article::make([
            'title' => 'Beta',
            'slug' => 'beta-'.uniqid('', true),
            'content' => 'c',
            'author_id' => 1,
            'status' => 'published',
        ]);
        $stale = new EloquentCollection([$a, $b]);

        $service = $this->createMock(ContentRefreshService::class);
        $service->expects($this->once())
            ->method('getStaleArticles')
            ->with(90, 3)
            ->willReturn($stale);
        $service->expects($this->exactly(2))
            ->method('refreshArticle')
            ->willReturnCallback(static function (Article $article): array {
                return ['status' => $article->title === 'Alpha' ? 'refreshed' : 'error'];
            });

        $request = Request::create('/admin/seo/refresh-logs/run', 'POST', ['limit' => 3]);
        $controller = new SeoRefreshLogsController();
        $response = $controller->runContentRefresh($request, $service);

        $this->assertSame(302, $response->getStatusCode());
        $success = $response->getSession()?->get('success');
        $this->assertIsString($success);
        $this->assertStringContainsString('1 berhasil', $success);
        $this->assertStringContainsString('1 error', $success);
        $this->assertStringContainsString('2 artikel', $success);
    }

    public function test_run_content_refresh_caps_limit_at_five(): void
    {
        $service = $this->createMock(ContentRefreshService::class);
        $service->expects($this->once())
            ->method('getStaleArticles')
            ->with(90, 5)
            ->willReturn(new EloquentCollection());

        $request = Request::create('/admin/seo/refresh-logs/run', 'POST', ['limit' => 99]);
        $controller = new SeoRefreshLogsController();
        $controller->runContentRefresh($request, $service);
    }
}
