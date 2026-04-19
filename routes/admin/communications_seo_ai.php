<?php

use Illuminate\Support\Facades\Route;

    // Email Management Routes
    Route::name('admin.')->middleware(['auth', 'email.access'])->group(function () {
        // Email Management Hub (Unified Tab Interface)
        Route::get('email-management', [App\Http\Controllers\Admin\EmailManagementController::class, 'index'])
            ->middleware('permission:email.view_inbox')
            ->name('email-management.index');
        
        // Email Campaigns
        Route::resource('campaigns', App\Http\Controllers\Admin\EmailCampaignController::class)
            ->middleware('permission:email.manage_campaigns');
        Route::get('campaigns/{id}/send', [App\Http\Controllers\Admin\EmailCampaignController::class, 'send'])
            ->middleware('permission:email.manage_campaigns')
            ->name('campaigns.send');
        Route::post('campaigns/{id}/process-send', [App\Http\Controllers\Admin\EmailCampaignController::class, 'processSend'])
            ->middleware('permission:email.manage_campaigns')
            ->name('campaigns.process-send');
        Route::post('campaigns/{id}/cancel', [App\Http\Controllers\Admin\EmailCampaignController::class, 'cancel'])
            ->middleware('permission:email.manage_campaigns')
            ->name('campaigns.cancel');
        
        // Email Inbox
        Route::get('inbox', [App\Http\Controllers\Admin\EmailInboxController::class, 'index'])
            ->middleware('permission:email.view_inbox')
            ->name('inbox.index');
        Route::get('inbox/compose', [App\Http\Controllers\Admin\EmailInboxController::class, 'compose'])
            ->middleware('permission:email.send_email')
            ->name('inbox.compose');
        Route::post('inbox/send', [App\Http\Controllers\Admin\EmailInboxController::class, 'send'])
            ->middleware('permission:email.send_email')
            ->name('inbox.send');
        Route::delete('inbox/batch-delete', [App\Http\Controllers\Admin\EmailInboxController::class, 'batchDelete'])
            ->middleware('permission:email.manage')
            ->name('inbox.batch-delete');
        Route::get('inbox/{id}', [App\Http\Controllers\Admin\EmailInboxController::class, 'show'])
            ->middleware('permission:email.view_inbox')
            ->name('inbox.show');
        Route::get('inbox/{id}/reply', [App\Http\Controllers\Admin\EmailInboxController::class, 'reply'])
            ->middleware('permission:email.send_email')
            ->name('inbox.reply');
        Route::post('inbox/{id}/reply', [App\Http\Controllers\Admin\EmailInboxController::class, 'sendReply'])
            ->middleware('permission:email.send_email')
            ->name('inbox.send-reply');
        Route::post('inbox/{id}/read', [App\Http\Controllers\Admin\EmailInboxController::class, 'markAsRead'])
            ->middleware('permission:email.view_inbox')
            ->name('inbox.mark-read');
        Route::post('inbox/{id}/unread', [App\Http\Controllers\Admin\EmailInboxController::class, 'markAsUnread'])
            ->middleware('permission:email.view_inbox')
            ->name('inbox.mark-unread');
        Route::post('inbox/{id}/star', [App\Http\Controllers\Admin\EmailInboxController::class, 'toggleStar'])
            ->middleware('permission:email.view_inbox')
            ->name('inbox.toggle-star');
        Route::post('inbox/{id}/trash', [App\Http\Controllers\Admin\EmailInboxController::class, 'moveToTrash'])
            ->middleware('permission:email.manage')
            ->name('inbox.trash');
        Route::delete('inbox/{id}', [App\Http\Controllers\Admin\EmailInboxController::class, 'delete'])
            ->middleware('permission:email.manage')
            ->name('inbox.delete');
        Route::post('inbox/empty-trash', [App\Http\Controllers\Admin\EmailInboxController::class, 'emptyTrash'])
            ->middleware('permission:email.manage')
            ->name('inbox.empty-trash');
        
        // Email Subscribers
        Route::resource('subscribers', App\Http\Controllers\Admin\EmailSubscriberController::class)
            ->middleware('permission:email.manage_subscribers');
        
        // Email Templates
        Route::resource('templates', App\Http\Controllers\Admin\EmailTemplateController::class)
            ->middleware('permission:email.manage_templates');
        
        // Email Settings
        Route::get('email/settings', [App\Http\Controllers\Admin\EmailSettingsController::class, 'index'])
            ->middleware('permission:email.manage_settings')
            ->name('email.settings.index');
        Route::put('email/settings', [App\Http\Controllers\Admin\EmailSettingsController::class, 'update'])
            ->middleware('permission:email.manage_settings')
            ->name('email.settings.update');
        Route::post('email/settings/test', [App\Http\Controllers\Admin\EmailSettingsController::class, 'test'])
            ->middleware('permission:email.manage_settings')
            ->name('email.settings.test');
    });

    // KBLI Settings
    Route::name('admin.')->middleware(['auth', 'permission:master_data.manage'])->group(function () {
        Route::prefix('settings/kbli')->name('settings.kbli.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\KbliSettingsController::class, 'index'])->name('index');
            Route::post('import', [App\Http\Controllers\Admin\KbliSettingsController::class, 'import'])->name('import');
            Route::get('template', [App\Http\Controllers\Admin\KbliSettingsController::class, 'downloadTemplate'])->name('template');
            Route::get('export', [App\Http\Controllers\Admin\KbliSettingsController::class, 'export'])->name('export');
            Route::delete('clear', [App\Http\Controllers\Admin\KbliSettingsController::class, 'clear'])->name('clear');
        });
    });

    // SEO Command Center (Unified Hub)
    Route::name('admin.')->middleware(['auth', 'permission:content.manage'])->group(function () {
        Route::prefix('seo')->name('seo.')->group(function () {
            // Unified Command Center (main entry point)
            Route::get('/command-center', [App\Http\Controllers\Admin\SeoCommandCenterController::class, 'index'])->name('command-center');
            
            // Redirect old dashboard to command-center
            Route::get('/', function() {
                return redirect()->route('admin.seo.command-center');
            })->name('dashboard');
            Route::get('/scores', [App\Http\Controllers\Admin\Seo\SeoScoresController::class, 'scores'])->name('scores');

            // Static POST/GET routes BEFORE wildcard {articleId}
            Route::post('/scores/fix-batch', [App\Http\Controllers\Admin\Seo\SeoScoresController::class, 'fixBatch'])->name('fix-batch');
            Route::post('/scores/rescore-all', [App\Http\Controllers\Admin\Seo\SeoScoresController::class, 'rescoreAll'])->name('rescore-all');
            Route::get('/scores/fix-candidates', [App\Http\Controllers\Admin\Seo\SeoScoresController::class, 'fixCandidates'])->name('fix-candidates');
            Route::post('/scores/fix-single-ajax/{articleId}', [App\Http\Controllers\Admin\Seo\SeoScoresController::class, 'fixSingleAjax'])->name('fix-single-ajax');

            Route::get('/scores/{articleId}', [App\Http\Controllers\Admin\Seo\SeoScoresController::class, 'scoreDetail'])->name('score-detail');
            Route::match(['GET', 'POST'], '/scores/{articleId}/fix', [App\Http\Controllers\Admin\Seo\SeoScoresController::class, 'fixSingle'])->name('fix-single');

            Route::get('/reports', [App\Http\Controllers\Admin\Seo\SeoReportsController::class, 'reports'])->name('reports');
            Route::get('/reports/{reportId}', [App\Http\Controllers\Admin\Seo\SeoReportsController::class, 'reportDetail'])->name('report-detail');
            Route::get('/competitors', [App\Http\Controllers\Admin\Seo\SeoCompetitorsController::class, 'competitors'])->name('competitors');
            Route::get('/competitors/{id}', [App\Http\Controllers\Admin\Seo\SeoCompetitorsController::class, 'competitorDetail'])->name('competitor-detail');
            Route::post('/competitors/{id}/re-analyze', [App\Http\Controllers\Admin\Seo\SeoCompetitorsController::class, 'reAnalyzeKeyword'])->name('competitor-reanalyze');
            Route::post('/competitors/analyze-keyword', [App\Http\Controllers\Admin\Seo\SeoCompetitorsController::class, 'analyzeCustomKeyword'])->name('competitor-analyze-keyword');
            Route::post('/competitors/{id}/apply-fix', [App\Http\Controllers\Admin\Seo\SeoCompetitorsController::class, 'applyCompetitorFix'])->name('competitor-apply-fix');
            Route::post('/competitors/{id}/verify', [App\Http\Controllers\Admin\Seo\SeoCompetitorsController::class, 'verifyCompetitorFix'])->name('competitor-verify');
            Route::get('/competitors/{id}/smart-fix', [App\Http\Controllers\Admin\Seo\SeoCompetitorsController::class, 'competitorSmartFix'])->name('competitor-smart-fix');
            Route::post('/competitors/{id}/smart-fix', [App\Http\Controllers\Admin\Seo\SeoCompetitorsController::class, 'executeSmartFix'])->name('competitor-execute-smart-fix');
            Route::get('/ab-tests', [App\Http\Controllers\Admin\Seo\SeoAbTestsController::class, 'abTests'])->name('ab-tests');
            Route::post('/ab-tests/{id}/evaluate', [App\Http\Controllers\Admin\Seo\SeoAbTestsController::class, 'evaluateAbTest'])->name('ab-tests.evaluate');
            Route::post('/ab-tests/{id}/stop', [App\Http\Controllers\Admin\Seo\SeoAbTestsController::class, 'stopAbTest'])->name('ab-tests.stop');
            Route::post('/ab-tests/{id}/apply', [App\Http\Controllers\Admin\Seo\SeoAbTestsController::class, 'applyAbTestWinner'])->name('ab-tests.apply');
            Route::post('/ab-tests/{id}/update-data', [App\Http\Controllers\Admin\Seo\SeoAbTestsController::class, 'updateAbTestData'])->name('ab-tests.update-data');
            Route::delete('/ab-tests/{id}', [App\Http\Controllers\Admin\Seo\SeoAbTestsController::class, 'deleteAbTest'])->name('ab-tests.delete');
            Route::post('/ab-tests/evaluate-all', [App\Http\Controllers\Admin\Seo\SeoAbTestsController::class, 'evaluateAllAbTests'])->name('ab-tests.evaluate-all');
            Route::get('/search-console', [App\Http\Controllers\Admin\Seo\SeoSearchConsoleController::class, 'searchConsole'])->name('search-console');
            Route::post('/search-console/import', [App\Http\Controllers\Admin\Seo\SeoSearchConsoleController::class, 'importSearchConsole'])->name('search-console.import');
            Route::post('/search-console/clear', [App\Http\Controllers\Admin\Seo\SeoSearchConsoleController::class, 'clearSearchConsole'])->name('search-console.clear');
            Route::get('/refresh-logs', [App\Http\Controllers\Admin\Seo\SeoRefreshLogsController::class, 'refreshLogs'])->name('refresh-logs');
            Route::post('/refresh-logs/run', [App\Http\Controllers\Admin\Seo\SeoRefreshLogsController::class, 'runContentRefresh'])->name('refresh-logs.run');
            Route::post('/refresh-logs/{id}/retry', [App\Http\Controllers\Admin\Seo\SeoRefreshLogsController::class, 'retryRefresh'])->name('refresh-logs.retry');
            Route::delete('/refresh-logs/{id}', [App\Http\Controllers\Admin\Seo\SeoRefreshLogsController::class, 'deleteRefreshLog'])->name('refresh-logs.delete');
            Route::get('/refresh-logs/{id}', [App\Http\Controllers\Admin\Seo\SeoRefreshLogsController::class, 'showRefreshLog'])->name('refresh-logs.show');
            Route::get('/programmatic', [App\Http\Controllers\Admin\Seo\SeoProgrammaticController::class, 'programmatic'])->name('programmatic');
            Route::post('/programmatic/clear-cache', [App\Http\Controllers\Admin\Seo\SeoProgrammaticController::class, 'clearProgrammaticCache'])->name('programmatic.clear-cache');

            // Position Tracking (SERP rankings)
            Route::get('/positions', [App\Http\Controllers\Admin\Seo\SeoPositionsController::class, 'positions'])->name('positions');
            Route::get('/positions/trend/{keyword}', [App\Http\Controllers\Admin\Seo\SeoPositionsController::class, 'positionTrend'])->name('positions.trend');
            Route::post('/positions/track', [App\Http\Controllers\Admin\Seo\SeoPositionsController::class, 'trackPositions'])->name('positions.track');
            Route::get('/alerts', [App\Http\Controllers\Admin\Seo\SeoRankingAlertsController::class, 'rankingAlerts'])->name('alerts');
            Route::post('/alerts/{id}/read', [App\Http\Controllers\Admin\Seo\SeoRankingAlertsController::class, 'markAlertRead'])->name('alerts.read');
            Route::post('/alerts/read-all', [App\Http\Controllers\Admin\Seo\SeoRankingAlertsController::class, 'markAllAlertsRead'])->name('alerts.read-all');

            // Web-triggered SEO commands (replaces manual artisan commands)
            Route::post('/run/snapshot-views', [App\Http\Controllers\Admin\Seo\SeoReportsController::class, 'runSnapshotViews'])->name('run-snapshot-views');
            Route::post('/run/generate-report', [App\Http\Controllers\Admin\Seo\SeoReportsController::class, 'runGenerateReport'])->name('run-generate-report');
            Route::post('/run/competitor-analyze', [App\Http\Controllers\Admin\Seo\SeoCompetitorsController::class, 'runCompetitorAnalyze'])->name('run-competitor-analyze');
            Route::post('/run/ab-tests', [App\Http\Controllers\Admin\Seo\SeoAbTestsController::class, 'runAbTests'])->name('run-ab-tests');
            Route::match(['get', 'post'], '/run/score-articles', [App\Http\Controllers\Admin\Seo\SeoScoresController::class, 'scoreArticlesProgress'])->name('run-score-articles');
        });
    });

    // AI Document Paraphrasing Routes
    Route::middleware('permission:documents.view')->group(function () {
        Route::prefix('projects/{project}/ai')->name('ai.')->group(function () {
            // Paraphrase form and processing
            Route::get('paraphrase', [App\Http\Controllers\AI\DocumentAIController::class, 'create'])->name('paraphrase.create');
            Route::post('paraphrase', [App\Http\Controllers\AI\DocumentAIController::class, 'store'])->name('paraphrase.store');
            
            // Draft management
            Route::get('drafts', [App\Http\Controllers\AI\DocumentAIController::class, 'index'])->name('drafts.index');
            Route::get('drafts/{draft}', [App\Http\Controllers\AI\DocumentAIController::class, 'show'])->name('drafts.show');
            Route::put('drafts/{draft}', [App\Http\Controllers\AI\DocumentAIController::class, 'update'])->name('drafts.update');
            
            // Draft actions
            Route::post('drafts/{draft}/approve', [App\Http\Controllers\AI\DocumentAIController::class, 'approve'])->name('drafts.approve');
            Route::post('drafts/{draft}/reject', [App\Http\Controllers\AI\DocumentAIController::class, 'reject'])->name('drafts.reject');
            Route::delete('drafts/{draft}', [App\Http\Controllers\AI\DocumentAIController::class, 'destroy'])->name('drafts.destroy');
            Route::get('drafts/{draft}/export', [App\Http\Controllers\AI\DocumentAIController::class, 'export'])->name('drafts.export');
            
            // Compliance check routes
            Route::post('drafts/{draft}/check-compliance', [App\Http\Controllers\AI\DocumentAIController::class, 'checkCompliance'])->name('drafts.check-compliance');
            Route::get('drafts/{draft}/compliance-results', [App\Http\Controllers\AI\DocumentAIController::class, 'getComplianceResults'])->name('drafts.compliance-results');
            Route::get('drafts/{draft}/compliance-report', [App\Http\Controllers\AI\DocumentAIController::class, 'exportComplianceReport'])->name('drafts.compliance-report');
            
            // Processing status (AJAX)
            Route::get('status', [App\Http\Controllers\AI\DocumentAIController::class, 'status'])->name('status');
        });

        // Template upload (admin only)
        Route::post('ai/templates/upload', [App\Http\Controllers\AI\DocumentAIController::class, 'uploadTemplate'])->name('ai.templates.upload');
    });
