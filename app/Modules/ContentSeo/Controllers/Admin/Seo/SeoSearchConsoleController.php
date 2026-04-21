<?php

namespace App\Modules\ContentSeo\Controllers\Admin\Seo;

use App\Modules\ContentSeo\Controllers\Admin\Concerns\SeoAdminFlashRedirect;
use App\Http\Controllers\Controller;
use App\Models\SearchConsoleData;
use App\Services\SearchConsoleService;
use Illuminate\Http\Request;

class SeoSearchConsoleController extends Controller
{
    use SeoAdminFlashRedirect;

    /**
     * Search Console data dashboard
     */
    public function searchConsole(Request $request, SearchConsoleService $service)
    {
        $days = (int) $request->get('days', 30);

        $summary = $service->getSummary($days);
        $topQueries = $service->getTopQueries($days, 20);
        $topPages = $service->getTopPages($days, 20);
        $opportunities = $service->getOpportunities($days);
        $lastImport = SearchConsoleData::max('updated_at');

        return view('admin.seo.search-console', compact('summary', 'topQueries', 'topPages', 'opportunities', 'days', 'lastImport'));
    }

    /**
     * Import/sync Search Console data
     */
    public function importSearchConsole(Request $request, SearchConsoleService $service)
    {
        $days = (int) $request->get('days', 7);
        $result = $service->importFromGSC($days);

        $source = $result['source'] === 'simulated' ? 'Simulated' : 'Google API';
        return $this->seoRouteFlash(
            'admin.seo.search-console',
            'success',
            "{$result['imported']} records imported ({$source}).",
            ['days' => $days]
        );
    }

    /**
     * Clear all Search Console data
     */
    public function clearSearchConsole()
    {
        $count = SearchConsoleData::count();
        SearchConsoleData::truncate();

        return $this->seoRouteFlash('admin.seo.search-console', 'success', "{$count} records cleared.");
    }
}

