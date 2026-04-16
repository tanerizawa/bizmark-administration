<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\ProjectPaymentController;
use App\Http\Controllers\ProjectExpenseController;
use App\Http\Controllers\CashAccountController;
use App\Http\Controllers\GeneralTransactionController;
use App\Http\Controllers\BankReconciliationController;
use App\Http\Controllers\PermitTypeController;
use App\Http\Controllers\PermitTemplateController;
use App\Http\Controllers\ProjectPermitController;
use App\Http\Controllers\PermitController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\PublicArticleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\PermitManagementController;
use App\Http\Controllers\Admin\RecruitmentController;
use App\Http\Controllers\Admin\ConsultationLeadController;
use App\Http\Controllers\RssFeedController;
use App\Http\Controllers\ProgrammaticSeoController;
use App\Http\Controllers\ServiceComparisonController;
use App\Http\Controllers\FaqAggregationController;
use App\Http\Controllers\PillarPageController;

// SEO Routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// RSS & Atom Feeds
Route::get('/feed/rss', [RssFeedController::class, 'rss'])->name('feed.rss');
Route::get('/feed/atom', [RssFeedController::class, 'atom'])->name('feed.atom');

// IndexNow key verification
Route::get('/{key}.txt', function (string $key) {
    $indexNowKey = config('services.indexnow.key', '');
    if ($key === $indexNowKey && !empty($indexNowKey)) {
        return response($indexNowKey, 200)->header('Content-Type', 'text/plain');
    }
    abort(404);
})->where('key', '[a-zA-Z0-9_-]+');

// Language Switcher
Route::get('/locale/{locale}', [LocaleController::class, 'setLocale'])
    ->name('locale.set')
    ->where('locale', 'id|en');

// Indonesian Landing Page (Root - Default)
Route::middleware('locale:id')->group(function () {
    Route::get('/layanan', [ServiceController::class, 'index'])->name('services.index.id');
    Route::get('/layanan/kota/{citySlug}', [ProgrammaticSeoController::class, 'cityIndex'])->name('programmatic.city.id');
    Route::get('/layanan/perbandingan', [ServiceComparisonController::class, 'index'])->name('comparison.index');
    Route::get('/layanan/perbandingan/{comparisonSlug}', [ServiceComparisonController::class, 'show'])->name('comparison.show');
    Route::get('/layanan/{slug}', [ServiceController::class, 'show'])->name('services.show.id');
    Route::get('/layanan/{serviceSlug}/sub/{subSlug}', [ServiceController::class, 'showSub'])->name('services.sub.id');
    Route::get('/layanan/{serviceSlug}/{citySlug}', [ProgrammaticSeoController::class, 'serviceLocation'])->name('programmatic.service-location.id');
    Route::get('/faq', [FaqAggregationController::class, 'index'])->name('faq.index');
    Route::get('/faq/{topicSlug}', [FaqAggregationController::class, 'show'])->name('faq.show');
    Route::get('/panduan', [PillarPageController::class, 'index'])->name('pillar.index');
    Route::get('/panduan/{pillarSlug}', [PillarPageController::class, 'show'])->name('pillar.show');
    Route::get('/blog', [PublicArticleController::class, 'index'])->name('blog.index.id');
    Route::get('/blog/kategori/{category}', [PublicArticleController::class, 'category'])->name('blog.category.id');
    Route::get('/blog/tag/{tag}', [PublicArticleController::class, 'tag'])->name('blog.tag.id');
    Route::get('/blog/{slug}', [PublicArticleController::class, 'show'])->name('blog.article.id');
    
    // Legal Pages (ID)
    Route::get('/kebijakan-privasi', function(\Illuminate\Http\Request $request) {
        $isMobile = $request->header('User-Agent') && 
                   (preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $request->header('User-Agent')));
        $view = $isMobile ? 'legal.mobile-privacy' : 'legal.privacy';
        return view($view);
    })->name('privacy.policy.id');
    
    Route::get('/syarat-ketentuan', function(\Illuminate\Http\Request $request) {
        $isMobile = $request->header('User-Agent') && 
                   (preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $request->header('User-Agent')));
        $view = $isMobile ? 'legal.mobile-terms' : 'legal.terms';
        return view($view);
    })->name('terms.conditions.id');
});

// English/PMA Landing Page (Explicit) - Responsive (No Mobile Redirect)
Route::prefix('en')->middleware('locale:en')->group(function () {
    Route::get('/', function(\Illuminate\Http\Request $request) {
        // Fully responsive landing page — serves all devices
        // Manual mobile override handled by DeviceDetection middleware (?mobile=1)
        return app(PublicArticleController::class)->landing($request);
    })->name('landing.en');
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index.en');
    Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show.en');
    Route::get('/services/{serviceSlug}/sub/{subSlug}', [ServiceController::class, 'showSub'])->name('services.sub.en');
    Route::get('/blog', [PublicArticleController::class, 'index'])->name('blog.index.en');
    Route::get('/blog/category/{category}', [PublicArticleController::class, 'category'])->name('blog.category.en');
    Route::get('/blog/tag/{tag}', [PublicArticleController::class, 'tag'])->name('blog.tag.en');
    Route::get('/blog/{slug}', [PublicArticleController::class, 'show'])->name('blog.article.en');
    
    // PMA Inquiry Form (English)
    Route::get('/inquiry', [App\Http\Controllers\PMAInquiryController::class, 'create'])->name('pma.inquiry.create');
    Route::post('/inquiry', [App\Http\Controllers\PMAInquiryController::class, 'store'])->name('pma.inquiry.store');
    Route::get('/inquiry/result/{inquiryNumber}', [App\Http\Controllers\PMAInquiryController::class, 'result'])->name('pma.inquiry.result');
    
    // Legal Pages (EN)
    Route::get('/privacy-policy', function() {
        return view('legal.en.privacy');
    })->name('privacy.policy.en');
    
    Route::get('/terms-conditions', function() {
        return view('legal.en.terms');
    })->name('terms.conditions.en');
});

// Redirect old /id URLs to root for backward compatibility
Route::redirect('/id', '/', 301);
Route::redirect('/id/layanan', '/layanan', 301);
Route::redirect('/id/blog', '/blog', 301);

// Contact Page
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Consultation Request / Cost Estimate (Public)
Route::get('/estimasi-biaya', [App\Http\Controllers\ConsultationPageController::class, 'index'])->name('consultation.index');
Route::get('/estimasi-biaya/hasil/{requestId}', [App\Http\Controllers\ConsultationPageController::class, 'result'])->name('consultation.result');

// Landing Page (Public) - Indonesian Default - Responsive (No Mobile Redirect)
Route::middleware('locale:id')->get('/', function(\Illuminate\Http\Request $request) {
    // Fully responsive landing page — serves all devices
    // Manual mobile override handled by DeviceDetection middleware (?mobile=1)
    return app(PublicArticleController::class)->landing($request);
})->name('landing.id');

// Service Inquiry - Free AI Analysis (Landing Page Lead Generation)
Route::prefix('konsultasi-gratis')->group(function() {
    Route::get('/', [App\Http\Controllers\Landing\ServiceInquiryController::class, 'create'])
        ->name('landing.service-inquiry.create');
    Route::post('/', [App\Http\Controllers\Landing\ServiceInquiryController::class, 'store'])
        ->name('landing.service-inquiry.store');
    Route::get('/hasil/{inquiryNumber}', [App\Http\Controllers\Landing\ServiceInquiryController::class, 'result'])
        ->name('landing.service-inquiry.result');
    Route::get('/api/status/{inquiryNumber}', [App\Http\Controllers\Landing\ServiceInquiryController::class, 'show'])
        ->name('landing.service-inquiry.show');
    Route::post('/api/check-rate-limit', [App\Http\Controllers\Landing\ServiceInquiryController::class, 'checkRateLimit'])
        ->name('landing.service-inquiry.check-rate-limit');
});

// Permit Calculator Tool (Public)
Route::get('/kalkulator-perizinan', [App\Http\Controllers\CalculatorController::class, 'index'])->name('calculator.index');
Route::post('/kalkulator-perizinan/calculate', [App\Http\Controllers\CalculatorController::class, 'calculate'])->name('calculator.calculate');

// Polygon SHP Maker Tool (Public)
Route::get('/polygon-shp-maker', [App\Http\Controllers\PolygonToolController::class, 'index'])->name('polygon.shp.index');

// Career/Jobs Pages (Public)
Route::get('/karir', [App\Http\Controllers\JobVacancyController::class, 'index'])->name('career.index');
Route::get('/karir/{slug}', [App\Http\Controllers\JobVacancyController::class, 'show'])->name('career.show');
Route::get('/karir/{vacancy_id}/apply', [App\Http\Controllers\JobApplicationController::class, 'create'])->name('career.apply');
Route::post('/karir/apply', [App\Http\Controllers\JobApplicationController::class, 'store'])->name('career.apply.store');

// Newsletter Subscription (Public)
Route::post('/subscribe', [App\Http\Controllers\SubscriberController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/unsubscribe/{email}/{token}', [App\Http\Controllers\SubscriberController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::get('/email/track/{tracking_id}', [App\Http\Controllers\SubscriberController::class, 'trackOpen'])->name('email.track');

// Screen Width Detection API (for responsive mobile detection)
Route::post('/api/set-screen-width', function(\Illuminate\Http\Request $request) {
    $width = $request->input('width');
    if ($width && is_numeric($width)) {
        session(['screen_width' => (int)$width]);
    }
    return response()->json(['success' => true, 'width' => session('screen_width')]);
})->name('api.screen-width');

// ========================================
// UNIFIED LOGIN SYSTEM
// ========================================
// Auto-detects Admin or Client based on credentials
// Main login portal for all users
Route::get('/login', [App\Http\Controllers\Auth\UnifiedLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\UnifiedLoginController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 attempts per minute
Route::post('/logout', [App\Http\Controllers\Auth\UnifiedLoginController::class, 'logout'])->name('logout');

// Admin-only direct login (hidden path for internal use)
// Path didefinisikan via ADMIN_SECRET_PATH di .env. Jika kosong, route nonaktif.
if ($adminSecretPath = trim((string) config('auth.admin_secret_path'), '/')) {
    Route::get('/'.$adminSecretPath, [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])
        ->name('admin.login');
    Route::post('/'.$adminSecretPath, [App\Http\Controllers\Auth\LoginController::class, 'login'])
        ->middleware('throttle:3,1');
}

// ========================================
// BACKWARD COMPATIBILITY REDIRECTS
// Redirect old URLs without /admin/ to new URLs with /admin/
// ========================================
Route::middleware(['auth'])->group(function () {
    Route::redirect('/dashboard', '/admin/dashboard', 301);
    Route::redirect('/projects', '/admin/projects', 301);
    Route::redirect('/tasks', '/admin/tasks', 301);
    Route::redirect('/documents', '/admin/documents', 301);
    Route::redirect('/institutions', '/admin/institutions', 301);
    Route::redirect('/clients', '/admin/clients', 301);
    Route::redirect('/settings', '/admin/settings', 301);
    Route::redirect('/cash-accounts', '/admin/cash-accounts', 301);
    Route::redirect('/articles', '/admin/articles', 301);
    Route::redirect('/reconciliations', '/admin/reconciliations', 301);
    Route::redirect('/permit-types', '/admin/permit-types', 301);
    Route::redirect('/permit-templates', '/admin/permit-templates', 301);
    Route::redirect('/auto-post', '/admin/auto-post', 301);
});

// Protected Routes (require authentication) - prefix /admin, guard 'web'.
// Route definitions extracted ke routes/admin.php untuk menjaga file ini tetap ringkas.
Route::middleware(['auth'])->prefix('admin')->group(function () {
    require __DIR__.'/admin.php';
});

// Client Portal Routes - prefix /client, name prefix 'client.'.
// Route definitions extracted ke routes/client.php.
Route::prefix('client')->name('client.')->group(function () {
    require __DIR__.'/client.php';
});

// Email Webhook Routes (untuk menerima email dari Cloudflare/external service)
Route::prefix('webhook/email')->name('webhook.email.')->group(function () {
    // Receive incoming email
    Route::post('/receive', [App\Http\Controllers\EmailWebhookController::class, 'receive'])
        ->middleware('throttle:60,1')
        ->name('receive');
    
    // Test webhook dengan data dummy
    Route::post('/test', [App\Http\Controllers\EmailWebhookController::class, 'test'])
        ->middleware(['auth', 'role:admin'])
        ->name('test');
    
    // Check webhook status
    Route::get('/status', [App\Http\Controllers\EmailWebhookController::class, 'status'])
        ->middleware(['auth', 'role:admin'])
        ->name('status');
});

// AI Settings Management Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('ai-settings', [App\Http\Controllers\Admin\AISettingsController::class, 'index'])
        ->name('ai-settings.index');
    Route::get('ai-settings/recent-changes', [App\Http\Controllers\Admin\AISettingsController::class, 'recentChanges'])
        ->name('ai-settings.recent-changes');
    Route::get('ai-settings/history/{key}', [App\Http\Controllers\Admin\AISettingsController::class, 'history'])
        ->name('ai-settings.history');
    Route::post('ai-settings/update', [App\Http\Controllers\Admin\AISettingsController::class, 'update'])
        ->name('ai-settings.update');
    Route::post('ai-settings/reset/{key}', [App\Http\Controllers\Admin\AISettingsController::class, 'reset'])
        ->name('ai-settings.reset');
    Route::post('ai-settings/clear-cache', [App\Http\Controllers\Admin\AISettingsController::class, 'clearCache'])
        ->name('ai-settings.clear-cache');
});

// Multi-User Email System Routes
Route::middleware(['auth', 'permission:email.manage'])->prefix('admin')->name('admin.')->group(function () {
    // Email Accounts Management
    Route::resource('email-accounts', App\Http\Controllers\Admin\EmailAccountController::class);
    Route::get('email-accounts/{emailAccount}/available-users', [App\Http\Controllers\Admin\EmailAccountController::class, 'availableUsers'])
        ->name('email-accounts.available-users');
    Route::get('email-accounts-stats', [App\Http\Controllers\Admin\EmailAccountController::class, 'stats'])
        ->name('email-accounts.stats');
    
    // Email Assignments
    Route::post('email-accounts/{emailAccount}/assign', [App\Http\Controllers\Admin\EmailAssignmentController::class, 'assign'])
        ->name('email-accounts.assign');
    Route::delete('email-accounts/{emailAccount}/unassign/{user}', [App\Http\Controllers\Admin\EmailAssignmentController::class, 'unassign'])
        ->name('email-accounts.unassign');
    Route::patch('email-accounts/{emailAccount}/permissions/{user}', [App\Http\Controllers\Admin\EmailAssignmentController::class, 'updatePermissions'])
        ->name('email-accounts.permissions.update');
    Route::post('email-accounts/{emailAccount}/bulk-assign', [App\Http\Controllers\Admin\EmailAssignmentController::class, 'bulkAssign'])
        ->name('email-accounts.bulk-assign');
    Route::post('email-accounts/{emailAccount}/transfer-primary', [App\Http\Controllers\Admin\EmailAssignmentController::class, 'transferPrimary'])
        ->name('email-accounts.transfer-primary');
    
    // User's Email Accounts
    Route::get('users/{user}/emails', [App\Http\Controllers\Admin\EmailAssignmentController::class, 'userEmails'])
        ->name('users.emails');
});

// Admin: Permit Application Management (Phase 3)
Route::prefix('admin')->name('admin.')->middleware(['auth:web'])->group(function () {
    // Unified Permit Management Interface (New Tabbed Interface)
    Route::get('permits', [App\Http\Controllers\Admin\PermitManagementController::class, 'index'])
        ->name('permits.index');
    
    // Unified Recruitment Management Interface (Tabbed Interface)
    Route::get('recruitment', [RecruitmentController::class, 'index'])
        ->name('recruitment.index');
    
    // Permit Application List & Detail
    Route::get('permit-applications', [App\Http\Controllers\Admin\ApplicationManagementController::class, 'index'])
        ->name('permit-applications.index');
    Route::get('permit-applications/{id}', [App\Http\Controllers\Admin\ApplicationManagementController::class, 'show'])
        ->name('permit-applications.show');
    
    // Review Actions
    Route::post('permit-applications/{id}/start-review', [App\Http\Controllers\Admin\ApplicationManagementController::class, 'startReview'])
        ->name('permit-applications.start-review');
    Route::post('permit-applications/{id}/update-status', [App\Http\Controllers\Admin\ApplicationManagementController::class, 'updateStatus'])
        ->name('permit-applications.update-status');
    Route::post('permit-applications/{id}/add-notes', [App\Http\Controllers\Admin\ApplicationManagementController::class, 'addNotes'])
        ->name('permit-applications.add-notes');
    
    // Document Verification
    Route::post('permit-applications/{application}/documents/{document}/verify', [App\Http\Controllers\Admin\ApplicationManagementController::class, 'verifyDocument'])
        ->name('permit-applications.documents.verify');
    Route::post('permit-applications/{id}/verify-all-documents', [App\Http\Controllers\Admin\ApplicationManagementController::class, 'verifyAllDocuments'])
        ->name('permit-applications.verify-all-documents');
    Route::post('permit-applications/{id}/request-document-revision', [App\Http\Controllers\Admin\ApplicationManagementController::class, 'requestDocumentRevision'])
        ->name('permit-applications.request-document-revision');
    Route::post('permit-applications/{id}/convert-to-project', [App\Http\Controllers\Admin\ApplicationManagementController::class, 'convertToProject'])
        ->name('permit-applications.convert-to-project');
    
    // Package Revision Management
    Route::get('permit-applications/{id}/revise', [App\Http\Controllers\Admin\PackageRevisionController::class, 'create'])
        ->name('permit-applications.revise');
    Route::post('permit-applications/{id}/revisions', [App\Http\Controllers\Admin\PackageRevisionController::class, 'store'])
        ->name('permit-applications.revisions.store');
    Route::get('permit-applications/{applicationId}/revisions/{revisionId}', [App\Http\Controllers\Admin\PackageRevisionController::class, 'show'])
        ->name('permit-applications.revisions.show');
    
    // Quotation Management
    Route::get('quotations/create', [App\Http\Controllers\Admin\QuotationController::class, 'create'])
        ->name('quotations.create');
    Route::post('quotations', [App\Http\Controllers\Admin\QuotationController::class, 'store'])
        ->name('quotations.store');
    Route::get('quotations/{id}/edit', [App\Http\Controllers\Admin\QuotationController::class, 'edit'])
        ->name('quotations.edit');
    Route::put('quotations/{id}', [App\Http\Controllers\Admin\QuotationController::class, 'update'])
        ->name('quotations.update');
    Route::get('quotations/{id}/pdf', [App\Http\Controllers\Admin\QuotationController::class, 'generatePdf'])
        ->name('quotations.pdf');
    Route::post('quotations/{id}/send-email', [App\Http\Controllers\Admin\QuotationController::class, 'sendEmail'])
        ->name('quotations.send-email');
    
    // Payment Verification (Phase 4)
    Route::get('payments', [App\Http\Controllers\Admin\PaymentVerificationController::class, 'index'])
        ->name('payments.index');
    Route::get('payments/{id}', [App\Http\Controllers\Admin\PaymentVerificationController::class, 'show'])
        ->name('payments.show');
    Route::post('payments/{id}/verify', [App\Http\Controllers\Admin\PaymentVerificationController::class, 'verify'])
        ->name('payments.verify');
    Route::post('payments/{id}/reject', [App\Http\Controllers\Admin\PaymentVerificationController::class, 'reject'])
        ->name('payments.reject');
    
    // Document Review (Phase 3)
    Route::post('documents/{document}/approve', [App\Http\Controllers\Admin\DocumentReviewController::class, 'approve'])
        ->name('documents.approve');
    Route::post('documents/{document}/reject', [App\Http\Controllers\Admin\DocumentReviewController::class, 'reject'])
        ->name('documents.reject');
    Route::post('documents/bulk-approve', [App\Http\Controllers\Admin\DocumentReviewController::class, 'bulkApprove'])
        ->name('documents.bulk-approve');
    Route::post('applications/{application}/documents/approve-all', [App\Http\Controllers\Admin\DocumentReviewController::class, 'approveAll'])
        ->name('applications.documents.approve-all');
    
    // Application Notes/Communication (Phase 4)
    Route::post('applications/{application}/notes', [App\Http\Controllers\Admin\ApplicationNoteController::class, 'store'])
        ->name('applications.notes.store');
    Route::delete('applications/{application}/notes/{note}', [App\Http\Controllers\Admin\ApplicationNoteController::class, 'destroy'])
        ->name('applications.notes.destroy');
    Route::post('notes/{note}/mark-read', [App\Http\Controllers\Admin\ApplicationNoteController::class, 'markAsRead'])
        ->name('notes.mark-read');
});

// Client: Application Notes (Phase 4)
Route::prefix('client')->name('client.')->middleware(['auth:client'])->group(function () {
    Route::post('applications/{application}/notes', [App\Http\Controllers\Client\ApplicationNoteController::class, 'store'])
        ->name('applications.notes.store');
    
    // Package Revision Management for Client
    Route::get('applications/{applicationId}/revisions/{revisionId}', [App\Http\Controllers\Client\RevisionController::class, 'show'])
        ->name('applications.revisions.show');
    Route::post('applications/{applicationId}/revisions/{revisionId}/approve', [App\Http\Controllers\Client\RevisionController::class, 'approve'])
        ->name('applications.revisions.approve');
    Route::post('applications/{applicationId}/revisions/{revisionId}/reject', [App\Http\Controllers\Client\RevisionController::class, 'reject'])
        ->name('applications.revisions.reject');
});

// Client: Push Notifications API (Phase 2)
Route::prefix('api/client/push')->name('api.client.push.')->middleware(['auth:client'])->group(function () {
    Route::post('/subscribe', [App\Http\Controllers\Api\PushNotificationController::class, 'subscribe'])->name('subscribe');
    Route::post('/unsubscribe', [App\Http\Controllers\Api\PushNotificationController::class, 'unsubscribe'])->name('unsubscribe');
    Route::get('/status', [App\Http\Controllers\Api\PushNotificationController::class, 'status'])->name('status');
    Route::post('/test', [App\Http\Controllers\Api\PushNotificationController::class, 'test'])->name('test');
});

// Push Notification Test Tool (Debug only - remove in production)
Route::get('/test-push', function () {
    return view('test-push');
})->middleware(['auth:client'])->name('test.push');

// Payment Callback API (Phase 4)
Route::post('/api/payment/callback', [App\Http\Controllers\Api\PaymentCallbackController::class, 'callback'])
    ->name('api.payment.callback');

// KBLI API
Route::prefix('api/kbli')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\KbliController::class, 'index'])->name('api.kbli.index');
    Route::get('/search', [App\Http\Controllers\Api\KbliController::class, 'search'])->name('api.kbli.search');
    Route::get('/{code}', [App\Http\Controllers\Api\KbliController::class, 'show'])->name('api.kbli.show');
});

// ============================================================================
// RECRUITMENT SYSTEM ROUTES
// ============================================================================

// Admin: Recruitment Management
Route::prefix('admin/recruitment')->name('admin.recruitment.')->middleware(['auth:web', 'permission:recruitment.manage'])->group(function () {
    
    // Interview Scheduling (Calendar-based)
    Route::resource('interviews', App\Http\Controllers\Admin\InterviewScheduleController::class);
    Route::get('interviews/{interview}/feedback', [App\Http\Controllers\Admin\InterviewScheduleController::class, 'feedback'])
        ->name('interviews.feedback');
    Route::post('interviews/{interview}/feedback', [App\Http\Controllers\Admin\InterviewScheduleController::class, 'storeFeedback'])
        ->name('interviews.feedback.store');
    
    // Test Management
    Route::resource('tests', App\Http\Controllers\Admin\TestManagementController::class);
    Route::post('tests/assign', [App\Http\Controllers\Admin\TestManagementController::class, 'assign'])
        ->name('tests.assign');
    Route::post('tests/{test}/assign', [App\Http\Controllers\Admin\TestManagementController::class, 'assign'])
        ->name('tests.assign.legacy'); // Keep for backward compatibility
    Route::get('tests/sessions/{session}/results', [App\Http\Controllers\Admin\TestManagementController::class, 'sessionResults'])
        ->name('tests.sessions.results');
    Route::delete('tests/sessions/{session}/cancel', [App\Http\Controllers\Admin\TestManagementController::class, 'cancelSession'])
        ->name('tests.sessions.cancel');
    
    // Manual Evaluation Routes (Essay/Rating Questions)
    Route::get('tests/sessions/{session}/evaluate-manual', [App\Http\Controllers\Admin\TestManagementController::class, 'showEvaluationForm'])
        ->name('tests.sessions.evaluate-manual');
    Route::post('tests/sessions/{session}/evaluate-manual', [App\Http\Controllers\Admin\TestManagementController::class, 'submitEvaluation'])
        ->name('tests.sessions.submit-evaluation-manual');
    
    // Document Editing Test Routes
    Route::post('tests/{test}/upload-template', [App\Http\Controllers\Admin\DocumentEditingTestController::class, 'uploadTemplate'])
        ->name('tests.upload-template');
    Route::get('tests/{test}/download-template', [App\Http\Controllers\Admin\DocumentEditingTestController::class, 'downloadTemplateForHR'])
        ->name('tests.download-template');
    Route::get('tests/sessions/{session}/evaluate', [App\Http\Controllers\Admin\DocumentEditingTestController::class, 'showEvaluationForm'])
        ->name('tests.sessions.evaluate');
    Route::post('tests/sessions/{session}/evaluate', [App\Http\Controllers\Admin\DocumentEditingTestController::class, 'submitEvaluation'])
        ->name('tests.sessions.submit-evaluation');
    Route::get('tests/sessions/{session}/download-submission', [App\Http\Controllers\Admin\DocumentEditingTestController::class, 'downloadSubmission'])
        ->name('tests.sessions.download-submission');
    
    // Recruitment Pipeline Dashboard
    Route::get('pipeline', [App\Http\Controllers\Admin\RecruitmentPipelineController::class, 'index'])
        ->name('pipeline.index');
    Route::get('pipeline/{application}', [App\Http\Controllers\Admin\RecruitmentPipelineController::class, 'show'])
        ->name('pipeline.show');
    Route::post('pipeline/{application}/initialize', [App\Http\Controllers\Admin\RecruitmentPipelineController::class, 'initializeStages'])
        ->name('pipeline.initialize');
    Route::patch('pipeline/stages/{stage}', [App\Http\Controllers\Admin\RecruitmentPipelineController::class, 'updateStage'])
        ->name('pipeline.stages.update');
});

// Candidate: Interview Portal (ID-based access, no auth required)
Route::prefix('candidate')->name('candidate.')->group(function () {
    
    // Interview Access
    Route::get('interview/{interview}', [App\Http\Controllers\Candidate\InterviewController::class, 'show'])
        ->name('interview.show');
    Route::post('interview/{interview}/reschedule', [App\Http\Controllers\Candidate\InterviewController::class, 'requestReschedule'])
        ->name('interview.reschedule');
    Route::get('interview/{interview}/join', [App\Http\Controllers\Candidate\InterviewController::class, 'join'])
        ->name('interview.join');
    
    // Test Portal
    Route::get('test/{token}', [App\Http\Controllers\Candidate\TestController::class, 'show'])
        ->name('test.show');
    Route::post('test/{token}/start', [App\Http\Controllers\Candidate\TestController::class, 'start'])
        ->name('test.start');
    Route::post('test/{token}/answer', [App\Http\Controllers\Candidate\TestController::class, 'submitAnswer'])
        ->name('test.answer');
    Route::post('test/{token}/complete', [App\Http\Controllers\Candidate\TestController::class, 'complete'])
        ->name('test.complete');
    
    // Document Editing Test - Candidate Routes
    Route::get('test/{token}/download-template', [App\Http\Controllers\Admin\DocumentEditingTestController::class, 'downloadTemplate'])
        ->name('test.download-template');
    Route::post('test/{token}/submit-document', [App\Http\Controllers\Admin\DocumentEditingTestController::class, 'submitDocument'])
        ->name('test.submit-document');
    
    // AJAX endpoints for test interface
    Route::post('test/{token}/track-tab', [App\Http\Controllers\Candidate\TestController::class, 'trackTabSwitch'])
        ->name('test.track-tab');
    Route::get('test/{token}/time', [App\Http\Controllers\Candidate\TestController::class, 'getRemainingTime'])
        ->name('test.time');
});


