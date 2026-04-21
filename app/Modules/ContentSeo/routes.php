<?php

use Illuminate\Support\Facades\Route;
use App\Modules\ContentSeo\Controllers\Admin\SeoCommandCenterController;
use App\Modules\ContentSeo\Controllers\Admin\Seo\SeoScoresController;
use App\Modules\ContentSeo\Controllers\Admin\Seo\SeoReportsController;
use App\Modules\ContentSeo\Controllers\Admin\Seo\SeoCompetitorsController;
use App\Modules\ContentSeo\Controllers\Admin\Seo\SeoAbTestsController;
use App\Modules\ContentSeo\Controllers\Admin\Seo\SeoSearchConsoleController;
use App\Modules\ContentSeo\Controllers\Admin\Seo\SeoRefreshLogsController;
use App\Modules\ContentSeo\Controllers\Admin\Seo\SeoProgrammaticController;
use App\Modules\ContentSeo\Controllers\Admin\Seo\SeoPositionsController;
use App\Modules\ContentSeo\Controllers\Admin\Seo\SeoRankingAlertsController;

Route::name('admin.')->middleware(['web', 'auth', 'permission:content.manage'])->prefix('admin')->group(function () {
    Route::prefix('seo')->name('seo.')->group(function () {
        // Unified Command Center (main entry point)
        Route::get('/command-center', [SeoCommandCenterController::class, 'index'])->name('command-center');

        // Redirect old dashboard to command-center
        Route::get('/', function () {
            return redirect()->route('admin.seo.command-center');
        })->name('dashboard');

        // Scores
        Route::get('/scores', [SeoScoresController::class, 'scores'])->name('scores');
        Route::post('/scores/fix-batch', [SeoScoresController::class, 'fixBatch'])->name('fix-batch');
        Route::post('/scores/rescore-all', [SeoScoresController::class, 'rescoreAll'])->name('rescore-all');
        Route::get('/scores/fix-candidates', [SeoScoresController::class, 'fixCandidates'])->name('fix-candidates');
        Route::post('/scores/fix-single-ajax/{articleId}', [SeoScoresController::class, 'fixSingleAjax'])->name('fix-single-ajax');
        Route::get('/scores/{articleId}', [SeoScoresController::class, 'scoreDetail'])->name('score-detail');
        Route::match(['GET', 'POST'], '/scores/{articleId}/fix', [SeoScoresController::class, 'fixSingle'])->name('fix-single');

        // Reports
        Route::get('/reports', [SeoReportsController::class, 'reports'])->name('reports');
        Route::get('/reports/{reportId}', [SeoReportsController::class, 'reportDetail'])->name('report-detail');

        // Competitors
        Route::get('/competitors', [SeoCompetitorsController::class, 'competitors'])->name('competitors');
        Route::get('/competitors/{id}', [SeoCompetitorsController::class, 'competitorDetail'])->name('competitor-detail');
        Route::post('/competitors/{id}/re-analyze', [SeoCompetitorsController::class, 'reAnalyzeKeyword'])->name('competitor-reanalyze');
        Route::post('/competitors/analyze-keyword', [SeoCompetitorsController::class, 'analyzeCustomKeyword'])->name('competitor-analyze-keyword');
        Route::post('/competitors/{id}/apply-fix', [SeoCompetitorsController::class, 'applyCompetitorFix'])->name('competitor-apply-fix');
        Route::post('/competitors/{id}/verify', [SeoCompetitorsController::class, 'verifyCompetitorFix'])->name('competitor-verify');
        Route::get('/competitors/{id}/smart-fix', [SeoCompetitorsController::class, 'competitorSmartFix'])->name('competitor-smart-fix');
        Route::post('/competitors/{id}/smart-fix', [SeoCompetitorsController::class, 'executeSmartFix'])->name('competitor-execute-smart-fix');

        // A/B Tests
        Route::get('/ab-tests', [SeoAbTestsController::class, 'abTests'])->name('ab-tests');
        Route::post('/ab-tests/{id}/evaluate', [SeoAbTestsController::class, 'evaluateAbTest'])->name('ab-tests.evaluate');
        Route::post('/ab-tests/{id}/stop', [SeoAbTestsController::class, 'stopAbTest'])->name('ab-tests.stop');
        Route::post('/ab-tests/{id}/apply', [SeoAbTestsController::class, 'applyAbTestWinner'])->name('ab-tests.apply');
        Route::post('/ab-tests/{id}/update-data', [SeoAbTestsController::class, 'updateAbTestData'])->name('ab-tests.update-data');
        Route::delete('/ab-tests/{id}', [SeoAbTestsController::class, 'deleteAbTest'])->name('ab-tests.delete');
        Route::post('/ab-tests/evaluate-all', [SeoAbTestsController::class, 'evaluateAllAbTests'])->name('ab-tests.evaluate-all');

        // Search Console
        Route::get('/search-console', [SeoSearchConsoleController::class, 'searchConsole'])->name('search-console');
        Route::post('/search-console/import', [SeoSearchConsoleController::class, 'importSearchConsole'])->name('search-console.import');
        Route::post('/search-console/clear', [SeoSearchConsoleController::class, 'clearSearchConsole'])->name('search-console.clear');

        // Refresh Logs
        Route::get('/refresh-logs', [SeoRefreshLogsController::class, 'refreshLogs'])->name('refresh-logs');
        Route::post('/refresh-logs/run', [SeoRefreshLogsController::class, 'runContentRefresh'])->name('refresh-logs.run');
        Route::post('/refresh-logs/{id}/retry', [SeoRefreshLogsController::class, 'retryRefresh'])->name('refresh-logs.retry');
        Route::delete('/refresh-logs/{id}', [SeoRefreshLogsController::class, 'deleteRefreshLog'])->name('refresh-logs.delete');
        Route::get('/refresh-logs/{id}', [SeoRefreshLogsController::class, 'showRefreshLog'])->name('refresh-logs.show');

        // Programmatic SEO
        Route::get('/programmatic', [SeoProgrammaticController::class, 'programmatic'])->name('programmatic');
        Route::post('/programmatic/clear-cache', [SeoProgrammaticController::class, 'clearProgrammaticCache'])->name('programmatic.clear-cache');

        // Position Tracking (SERP rankings)
        Route::get('/positions', [SeoPositionsController::class, 'positions'])->name('positions');
        Route::get('/positions/trend/{keyword}', [SeoPositionsController::class, 'positionTrend'])->name('positions.trend');
        Route::post('/positions/track', [SeoPositionsController::class, 'trackPositions'])->name('positions.track');

        // Ranking Alerts
        Route::get('/alerts', [SeoRankingAlertsController::class, 'rankingAlerts'])->name('alerts');
        Route::post('/alerts/{id}/read', [SeoRankingAlertsController::class, 'markAlertRead'])->name('alerts.read');
        Route::post('/alerts/read-all', [SeoRankingAlertsController::class, 'markAllAlertsRead'])->name('alerts.read-all');

        // Web-triggered SEO commands
        Route::post('/run/snapshot-views', [SeoReportsController::class, 'runSnapshotViews'])->name('run-snapshot-views');
        Route::post('/run/generate-report', [SeoReportsController::class, 'runGenerateReport'])->name('run-generate-report');
        Route::post('/run/competitor-analyze', [SeoCompetitorsController::class, 'runCompetitorAnalyze'])->name('run-competitor-analyze');
        Route::post('/run/ab-tests', [SeoAbTestsController::class, 'runAbTests'])->name('run-ab-tests');
        Route::match(['get', 'post'], '/run/score-articles', [SeoScoresController::class, 'scoreArticlesProgress'])->name('run-score-articles');
    });
});
