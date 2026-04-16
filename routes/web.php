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
use App\Http\Controllers\Financial\OverviewController as FinancialOverviewController;
use App\Http\Controllers\Financial\InvoiceController as FinancialInvoiceController;
use App\Http\Controllers\Financial\PaymentScheduleController as FinancialPaymentScheduleController;
use App\Http\Controllers\Financial\ExpenseController as FinancialExpenseController;
use App\Http\Controllers\Financial\DirectIncomeController as FinancialDirectIncomeController;
use App\Http\Controllers\Financial\ExportController as FinancialExportController;
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
    
    // Static Pages (ID)
    Route::get('/proses', function() {
        return view('landing.pages.process', ['locale' => 'id']);
    })->name('process.id');
    
    Route::get('/tentang', function() {
        return view('landing.pages.about', ['locale' => 'id']);
    })->name('about.id');
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
    
    // Static Pages (EN)
    Route::get('/process', function() {
        return view('landing.pages.process', ['locale' => 'en']);
    })->name('process.en');
    
    Route::get('/about', function() {
        return view('landing.pages.about', ['locale' => 'en']);
    })->name('about.en');
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
Route::get('/estimasi-biaya/pdf/{requestId}', [App\Http\Controllers\ConsultationPageController::class, 'downloadPdf'])->name('consultation.pdf');

// Permohonan Penghitungan Biaya Jasa (Service Cost Request)
Route::prefix('permohonan')->group(function() {
    Route::get('/', [App\Http\Controllers\ServiceCostRequestController::class, 'index'])->name('permohonan.index');
    Route::post('/', [App\Http\Controllers\ServiceCostRequestController::class, 'store'])->name('permohonan.store');
    Route::post('/api/generate-letter-draft', [App\Http\Controllers\ServiceCostRequestController::class, 'generateLetterDraft'])->name('permohonan.generate-letter-draft');
    Route::get('/hasil/{requestNumber}', [App\Http\Controllers\ServiceCostRequestController::class, 'result'])->name('permohonan.result');
    Route::get('/api/status/{requestNumber}', [App\Http\Controllers\ServiceCostRequestController::class, 'checkStatus'])->name('permohonan.status');
});

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

// Admin-only direct login (hidden path, configurable via ADMIN_SECRET_PATH env)
// Rotate by changing ADMIN_SECRET_PATH in .env + php artisan config:clear
if ($adminSecretPath = trim((string) config('auth.admin_secret_path'), '/')) {
    Route::get('/'.$adminSecretPath, [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])
        ->name('admin.login');
    Route::post('/'.$adminSecretPath, [App\Http\Controllers\Auth\LoginController::class, 'login'])
        ->middleware('throttle:3,1'); // Stricter rate limit for admin path
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

// Protected Routes (require authentication)
// All admin panel routes are prefixed with /admin/
Route::middleware(['auth'])->prefix('admin')->group(function () {
    // Dashboard - desktop version (mobile auto-redirects handled in DetectMobile middleware)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::post('/dashboard/clear-cache', [DashboardController::class, 'clearCache'])->name('dashboard.clear-cache');
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home');

    // Admin Profile Routes
    Route::name('admin.')->group(function () {
        Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::get('/notifications', [App\Http\Controllers\Admin\ProfileController::class, 'notifications'])->name('notifications');
        Route::get('/notifications/{id}/read', [App\Http\Controllers\Admin\ProfileController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [App\Http\Controllers\Admin\ProfileController::class, 'markAllAsRead'])->name('notifications.read-all');
        
        // Global Search
        Route::get('/search', [App\Http\Controllers\Admin\SearchController::class, 'search'])->name('search');
    });

    // Export Routes
    Route::middleware('permission:projects.view')->group(function () {
        Route::get('export/projects', [ExportController::class, 'exportProjects'])->name('export.projects');
        Route::get('export/projects/{id}/details', [ExportController::class, 'exportProjectDetails'])->name('export.project.details');
    });

    // Project Management Routes
    Route::middleware('permission:projects.view')->group(function () {
        Route::resource('projects', ProjectController::class);
        Route::patch('projects/{project}/status', [ProjectController::class, 'updateStatus'])->name('projects.update-status');
    });

    // Task Management Routes
    Route::middleware('permission:tasks.view')->group(function () {
        Route::resource('tasks', TaskController::class);
        Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
        Route::patch('tasks/{task}/assignment', [TaskController::class, 'updateAssignment'])->name('tasks.update-assignment');
        Route::patch('projects/{project}/tasks/reorder', [TaskController::class, 'reorder'])->name('projects.tasks.reorder');
    });

    // Document Management Routes
    Route::middleware('permission:documents.view')->group(function () {
        Route::resource('documents', DocumentController::class);
        Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
        Route::get('api/tasks-by-project', [DocumentController::class, 'getTasksByProject'])->name('api.tasks-by-project');
    });

    // Institution Management Routes
    Route::middleware('permission:institutions.view')->group(function () {
        Route::resource('institutions', InstitutionController::class);
        Route::get('api/institutions', [InstitutionController::class, 'apiIndex'])->name('api.institutions');
    });

    // Client Management Routes
    Route::middleware('permission:clients.view')->group(function () {
        Route::resource('clients', App\Http\Controllers\ClientController::class);
        Route::get('api/clients', [App\Http\Controllers\ClientController::class, 'apiIndex'])->name('api.clients');
    });

    // Lead Management Routes (Unified with Tabs)
    Route::middleware('permission:clients.view')->group(function () {
        // Unified Lead Management Page with Tabs
        Route::get('leads', [App\Http\Controllers\Admin\LeadManagementController::class, 'index'])->name('admin.leads.index');
        
        // Service Inquiry routes (kept for detail pages and actions)
        Route::get('service-inquiries/export', [App\Http\Controllers\Admin\ServiceInquiryController::class, 'export'])->name('admin.service-inquiries.export');
        Route::get('service-inquiries/{serviceInquiry}', [App\Http\Controllers\Admin\ServiceInquiryController::class, 'show'])->name('admin.service-inquiries.show');
        Route::patch('service-inquiries/{serviceInquiry}/status', [App\Http\Controllers\Admin\ServiceInquiryController::class, 'updateStatus'])->name('admin.service-inquiries.update-status');
        Route::patch('service-inquiries/{serviceInquiry}/priority', [App\Http\Controllers\Admin\ServiceInquiryController::class, 'updatePriority'])->name('admin.service-inquiries.update-priority');
        Route::post('service-inquiries/{serviceInquiry}/note', [App\Http\Controllers\Admin\ServiceInquiryController::class, 'addNote'])->name('admin.service-inquiries.add-note');
        Route::post('service-inquiries/{serviceInquiry}/convert', [App\Http\Controllers\Admin\ServiceInquiryController::class, 'convertToProject'])->name('admin.service-inquiries.convert');
        Route::delete('service-inquiries/{serviceInquiry}', [App\Http\Controllers\Admin\ServiceInquiryController::class, 'destroy'])->name('admin.service-inquiries.destroy');
        
        // Consultation Leads routes (kept for detail pages and actions)
        Route::get('consultation-leads', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'index'])->name('admin.consultation-leads.index');
        Route::get('consultation-leads/export', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'export'])->name('admin.consultation-leads.export');
        Route::get('consultation-leads/{consultation}', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'show'])->name('admin.consultation-leads.show');
        Route::post('consultation-leads/{consultation}/update-status', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'updateStatus'])->name('admin.consultation-leads.update-status');
        Route::post('consultation-leads/{consultation}/mark-contacted', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'markContacted'])->name('admin.consultation-leads.mark-contacted');
        Route::post('consultation-leads/{consultation}/convert', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'convertToClient'])->name('admin.consultation-leads.convert-to-client');
        Route::post('consultation-leads/{consultation}/note', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'addNote'])->name('admin.consultation-leads.add-note');
        Route::delete('consultation-leads/{consultation}', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'destroy'])->name('admin.consultation-leads.destroy');
        
        // Service Cost Request routes (for detail pages and admin actions)
        Route::get('service-cost-requests/{requestNumber}', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'show'])->name('admin.service-cost-requests.show');
        Route::patch('service-cost-requests/{requestNumber}/status', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'updateStatus'])->name('admin.service-cost-requests.update-status');
        Route::post('service-cost-requests/{requestNumber}/note', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'addNote'])->name('admin.service-cost-requests.add-note');
        Route::post('service-cost-requests/{requestNumber}/generate-quote', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'generateQuote'])->name('admin.service-cost-requests.generate-quote');
        Route::post('service-cost-requests/{requestNumber}/regenerate-content', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'regenerateQuoteContent'])->name('admin.service-cost-requests.regenerate-content');
        Route::post('service-cost-requests/{requestNumber}/send-email', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'sendQuoteEmail'])->name('admin.service-cost-requests.send-email');
        Route::post('service-cost-requests/{requestNumber}/complete', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'complete'])->name('admin.service-cost-requests.complete');
        Route::post('service-cost-requests/{requestNumber}/archive', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'archive'])->name('admin.service-cost-requests.archive');
        
        // Backward compatibility redirect
        Route::redirect('service-inquiries', '/admin/leads?tab=service-inquiries');
    });

    // Financial Management Routes (Phase 1)
    // Read-only routes (auth required)
    Route::middleware('auth')->group(function () {
        Route::get('cash-accounts', [CashAccountController::class, 'index'])->name('cash-accounts.index');
        Route::get('cash-accounts/create', [CashAccountController::class, 'create'])->name('cash-accounts.create');
        Route::get('cash-accounts/{cash_account}', [CashAccountController::class, 'show'])->name('cash-accounts.show');
    });
    
    // Write routes (require permission)
    Route::middleware('permission:finances.view')->group(function () {
        Route::post('projects/{project}/payments', [ProjectPaymentController::class, 'store'])->name('projects.payments.store');
        Route::delete('payments/{payment}', [ProjectPaymentController::class, 'destroy'])->name('payments.destroy');
        Route::post('projects/{project}/expenses', [ProjectExpenseController::class, 'store'])->name('projects.expenses.store');
        Route::delete('expenses/{expense}', [ProjectExpenseController::class, 'destroy'])->name('expenses.destroy');
        
        // Cash accounts write operations
        Route::post('cash-accounts', [CashAccountController::class, 'store'])->name('cash-accounts.store');
        Route::get('cash-accounts/{cash_account}/edit', [CashAccountController::class, 'edit'])->name('cash-accounts.edit');
        Route::put('cash-accounts/{cash_account}', [CashAccountController::class, 'update'])->name('cash-accounts.update');
        Route::patch('cash-accounts/{cash_account}', [CashAccountController::class, 'update']);
        Route::delete('cash-accounts/{cash_account}', [CashAccountController::class, 'destroy'])->name('cash-accounts.destroy');

        // General Transaction Routes (Phase 2 - Non-Project Income/Expense)
        Route::prefix('general-transactions')->name('general-transactions.')->group(function () {
            // General Income
            Route::post('income', [GeneralTransactionController::class, 'storeIncome'])->name('income.store');
            Route::get('income/{id}', [GeneralTransactionController::class, 'getIncome'])->name('income.show');
            Route::put('income/{id}', [GeneralTransactionController::class, 'updateIncome'])->name('income.update');
            Route::delete('income/{id}', [GeneralTransactionController::class, 'destroyIncome'])->name('income.destroy');
            
            // General Expense
            Route::post('expense', [GeneralTransactionController::class, 'storeExpense'])->name('expense.store');
            Route::get('expense/{id}', [GeneralTransactionController::class, 'getExpense'])->name('expense.show');
            Route::put('expense/{id}', [GeneralTransactionController::class, 'updateExpense'])->name('expense.update');
            Route::delete('expense/{id}', [GeneralTransactionController::class, 'destroyExpense'])->name('expense.destroy');
        });

        // Bank Reconciliation Routes (Phase 1B)
        Route::resource('reconciliations', BankReconciliationController::class);
        Route::get('reconciliations/{reconciliation}/match', [BankReconciliationController::class, 'match'])->name('reconciliations.match');
        Route::post('reconciliations/{reconciliation}/auto-match', [BankReconciliationController::class, 'autoMatch'])->name('reconciliations.auto-match');
        Route::post('reconciliations/{reconciliation}/manual-match', [BankReconciliationController::class, 'manualMatch'])->name('reconciliations.manual-match');
        Route::post('reconciliations/{reconciliation}/unmatch', [BankReconciliationController::class, 'unmatch'])->name('reconciliations.unmatch');
        Route::post('reconciliations/{reconciliation}/complete', [BankReconciliationController::class, 'complete'])->name('reconciliations.complete');
    });
    
    // API endpoints for AJAX calls (requires auth only, no specific permission)
    Route::middleware('auth')->group(function () {
        Route::get('api/cash-accounts/active', [CashAccountController::class, 'getActiveCashAccounts'])->name('api.cash-accounts.active');
    });

    // Article Management Routes
    Route::middleware('permission:content.manage')->group(function () {
        Route::resource('articles', ArticleController::class);
        Route::post('articles/{article}/publish', [ArticleController::class, 'publish'])->name('articles.publish');
        Route::post('articles/{article}/unpublish', [ArticleController::class, 'unpublish'])->name('articles.unpublish');
        Route::post('articles/{article}/archive', [ArticleController::class, 'archive'])->name('articles.archive');
        Route::post('articles/upload-image', [ArticleController::class, 'uploadImage'])->name('articles.upload-image');
        
        // Pexels API Routes
        Route::prefix('pexels')->name('pexels.')->group(function () {
            Route::get('search', [App\Http\Controllers\Admin\PexelsController::class, 'search'])->name('search');
            Route::get('curated', [App\Http\Controllers\Admin\PexelsController::class, 'curated'])->name('curated');
            Route::post('download', [App\Http\Controllers\Admin\PexelsController::class, 'download'])->name('download');
        });
        
        // Auto-Post Management Routes (Unified Dashboard)
        Route::prefix('auto-post')->name('auto-post.')->group(function () {
            // Unified Dashboard with Tabs
            Route::get('/', [App\Http\Controllers\Admin\AutoPostController::class, 'index'])->name('index');
            
            // Configuration (kept for form submission)
            Route::put('config', [App\Http\Controllers\Admin\AutoPostConfigController::class, 'update'])->name('config.update');
            Route::post('config/toggle', [App\Http\Controllers\Admin\AutoPostConfigController::class, 'toggle'])->name('config.toggle');
            
            // Legacy redirect: config -> unified dashboard
            Route::get('config', fn() => redirect()->route('auto-post.index', ['tab' => 'config']))->name('config');
            
            // Topics Management (resource routes for CRUD)
            Route::resource('topics', App\Http\Controllers\Admin\ArticleTopicController::class);
            Route::post('topics/bulk-action', [App\Http\Controllers\Admin\ArticleTopicController::class, 'bulkAction'])->name('topics.bulk-action');
            
            // Schedules Management (resource routes for CRUD)
            Route::resource('schedules', App\Http\Controllers\Admin\AutoPostScheduleController::class)->except(['edit', 'update']);
            Route::post('schedules/bulk-action', [App\Http\Controllers\Admin\AutoPostScheduleController::class, 'bulkAction'])->name('schedules.bulk-action');
            Route::post('schedules/generate-batch', [App\Http\Controllers\Admin\AutoPostScheduleController::class, 'generateBatch'])->name('schedules.generate-batch');
            Route::post('schedules/{schedule}/retry', [App\Http\Controllers\Admin\AutoPostScheduleController::class, 'retry'])->name('schedules.retry');
            Route::post('schedules/{schedule}/process-now', [App\Http\Controllers\Admin\AutoPostScheduleController::class, 'processNow'])->name('schedules.process-now');
            
            // Legacy redirects
            Route::get('analytics', fn() => redirect()->route('auto-post.index', ['tab' => 'analytics']))->name('analytics');
            Route::get('logs', [App\Http\Controllers\Admin\AutoPostLogController::class, 'index'])->name('logs.index');
            Route::get('logs/recent', [App\Http\Controllers\Admin\AutoPostLogController::class, 'recent'])->name('logs.recent');
        });
    });

    // Master Data - Permit Management Routes (Phase 2A)
    Route::middleware('permission:master_data.manage')->group(function () {
        Route::resource('permit-types', PermitTypeController::class);
        Route::patch('permit-types/{permitType}/toggle-status', [PermitTypeController::class, 'toggleStatus'])->name('permit-types.toggle-status');

        Route::resource('permit-templates', PermitTemplateController::class);
        Route::post('permit-templates/{permitTemplate}/apply', [PermitTemplateController::class, 'applyToProject'])->name('permit-templates.apply');
    });

    // Project Permit Management Routes (Phase 2A - Sprint 3)
    Route::middleware('permission:projects.view')->group(function () {
        // Individual permit status update (used by permits tab)
        Route::patch('permits/{permit}/status', [ProjectPermitController::class, 'updateStatus'])->name('permits.update-status');
    });
    
    // Note: These routes are legacy and might overlap with PermitController routes below
    // Route::post('projects/{project}/permits', [ProjectPermitController::class, 'store'])->name('projects.permits.store');
    // Route::post('projects/{project}/permits/apply-template', [ProjectPermitController::class, 'applyTemplate'])->name('projects.permits.apply-template');
    // Route::patch('projects/{project}/permits/reorder', [ProjectPermitController::class, 'reorder'])->name('projects.permits.reorder.old');
    // Route::post('permits/{permit}/dependencies', [ProjectPermitController::class, 'addDependency'])->name('permits.add-dependency');
    // Route::delete('permits/{permit}/dependencies/{dependency}', [ProjectPermitController::class, 'removeDependency'])->name('permits.remove-dependency');
    // Route::delete('permits/{permit}', [ProjectPermitController::class, 'destroy'])->name('permits.destroy');

    // Financial Tab Management Routes (Phase 2A - Sprint 6)
    // Refactored: FinancialController (1089 LOC) dipecah ke per-domain controller di App\Http\Controllers\Financial\*
    Route::middleware('permission:invoices.view')->group(function () {
        Route::get('projects/{project}/financial', [FinancialOverviewController::class, 'index'])->name('projects.financial');

        Route::post('projects/{project}/invoices', [FinancialInvoiceController::class, 'store'])->name('projects.invoices.store');
        Route::get('invoices/{invoice}', [FinancialInvoiceController::class, 'show'])->name('invoices.show');
        Route::get('invoices/{invoice}/pdf', [FinancialInvoiceController::class, 'downloadPDF'])->name('invoices.download-pdf');
        Route::patch('invoices/{invoice}/status', [FinancialInvoiceController::class, 'updateStatus'])->name('invoices.update-status');
        Route::post('invoices/{invoice}/payment', [FinancialInvoiceController::class, 'recordPayment'])->name('invoices.record-payment');
        Route::delete('invoices/{invoice}', [FinancialInvoiceController::class, 'destroy'])->name('invoices.destroy');

        Route::post('projects/{project}/direct-income', [FinancialDirectIncomeController::class, 'store'])->name('projects.direct-income.store');
        Route::get('projects/{project}/direct-income/{payment}', [FinancialDirectIncomeController::class, 'edit'])->name('projects.direct-income.edit');
        Route::patch('projects/{project}/direct-income/{payment}', [FinancialDirectIncomeController::class, 'update'])->name('projects.direct-income.update');
        Route::delete('projects/{project}/direct-income/{payment}', [FinancialDirectIncomeController::class, 'destroy'])->name('projects.direct-income.destroy');

        Route::post('projects/{project}/payment-schedules', [FinancialPaymentScheduleController::class, 'store'])->name('projects.payment-schedules.store');
        Route::patch('payment-schedules/{schedule}/paid', [FinancialPaymentScheduleController::class, 'markPaid'])->name('payment-schedules.mark-paid');
        Route::delete('payment-schedules/{schedule}', [FinancialPaymentScheduleController::class, 'destroy'])->name('payment-schedules.destroy');

        Route::post('projects/{project}/financial-expenses', [FinancialExpenseController::class, 'store'])->name('projects.financial-expenses.store');
        Route::get('financial-expenses/{expense}', [FinancialExpenseController::class, 'show'])->name('financial-expenses.show');
        Route::patch('financial-expenses/{expense}', [FinancialExpenseController::class, 'update'])->name('financial-expenses.update');
        Route::delete('financial-expenses/{expense}', [FinancialExpenseController::class, 'destroy'])->name('financial-expenses.destroy');
        Route::delete('financial-expenses/{expense}/delete-receipt', [FinancialExpenseController::class, 'deleteReceipt'])->name('financial-expenses.delete-receipt');
        Route::patch('financial-expenses/{expense}/mark-invoiced', [FinancialExpenseController::class, 'markInvoiced'])->name('financial-expenses.mark-invoiced');
        Route::patch('financial-expenses/{expense}/record-payment', [FinancialExpenseController::class, 'recordReceivablePayment'])->name('financial-expenses.record-payment');
        Route::patch('financial-expenses/{expense}/remove-receivable', [FinancialExpenseController::class, 'removeReceivable'])->name('financial-expenses.remove-receivable');
    });

    // Excel Export Routes (Phase 2A - Sprint 7)
    Route::middleware('permission:invoices.view')->group(function () {
        Route::get('exports/invoices', [FinancialExportController::class, 'invoices'])->name('exports.invoices');
        Route::get('exports/invoices/{invoice}', [FinancialExportController::class, 'invoiceDetail'])->name('exports.invoice-detail');
        Route::get('exports/expenses', [FinancialExportController::class, 'expenses'])->name('exports.expenses');
        Route::get('exports/financial-report', [FinancialExportController::class, 'financialReport'])->name('exports.financial-report');
    });

    // Permit Management Routes (Phase 2A - Sprint 8)
    Route::middleware('permission:projects.view')->group(function () {
        Route::get('projects/{project}/permits', [PermitController::class, 'index'])->name('projects.permits');
        Route::post('projects/{project}/permits', [PermitController::class, 'store'])->name('projects.permits.store');
        Route::patch('permits/{permit}', [PermitController::class, 'update'])->name('permits.update');
        Route::delete('permits/{permit}', [PermitController::class, 'destroy'])->name('permits.destroy');
        Route::post('projects/{project}/permits/apply-template', [PermitController::class, 'applyTemplate'])->name('projects.permits.apply-template');
        Route::post('permits/{permit}/dependencies', [PermitController::class, 'addDependency'])->name('permits.add-dependency');
        Route::post('projects/{project}/permits/reorder', [PermitController::class, 'reorder'])->name('projects.permits.reorder');
        // Bulk operations
        Route::post('projects/{project}/permits/bulk-update-status', [PermitController::class, 'bulkUpdateStatus'])->name('projects.permits.bulk-update-status');
        Route::post('projects/{project}/permits/bulk-delete', [PermitController::class, 'bulkDelete'])->name('projects.permits.bulk-delete');
        // Document management
        Route::post('projects/{project}/permits/{permit}/documents/upload', [PermitController::class, 'uploadDocument'])->name('permits.documents.upload');
        Route::get('projects/{project}/permits/documents/{document}/download', [PermitController::class, 'downloadDocument'])->name('permits.documents.download');
        Route::delete('projects/{project}/permits/documents/{document}', [PermitController::class, 'deleteDocument'])->name('permits.documents.delete');
        Route::post('permits/documents/{document}/delete', [PermitController::class, 'deleteDocumentPost'])->name('permits.documents.delete-post');
    });

    // Settings Management Routes (Phase 2A - Sprint 9)
    Route::middleware('permission:settings.manage')->group(function () {
        Route::get('settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings/general', [App\Http\Controllers\SettingsController::class, 'updateGeneral'])->name('settings.general.update');
        
        // User Management
        Route::post('settings/users', [App\Http\Controllers\SettingsController::class, 'storeUser'])->name('settings.users.store');
        Route::put('settings/users/{user}', [App\Http\Controllers\SettingsController::class, 'updateUser'])->name('settings.users.update');
        Route::delete('settings/users/{user}', [App\Http\Controllers\SettingsController::class, 'deleteUser'])->name('settings.users.delete');
        Route::patch('settings/users/{user}/toggle-status', [App\Http\Controllers\SettingsController::class, 'toggleUserStatus'])->name('settings.users.toggle-status');

        // Role Management
        Route::post('settings/roles', [App\Http\Controllers\SettingsController::class, 'storeRole'])->name('settings.roles.store');
        Route::put('settings/roles/{role}', [App\Http\Controllers\SettingsController::class, 'updateRole'])->name('settings.roles.update');
        Route::delete('settings/roles/{role}', [App\Http\Controllers\SettingsController::class, 'deleteRole'])->name('settings.roles.delete');

        // Financial Settings
        Route::post('settings/financial/expense-categories', [App\Http\Controllers\SettingsController::class, 'storeExpenseCategory'])->name('settings.financial.expense-categories.store');
        Route::put('settings/financial/expense-categories/{expenseCategory}', [App\Http\Controllers\SettingsController::class, 'updateExpenseCategory'])->name('settings.financial.expense-categories.update');
        Route::delete('settings/financial/expense-categories/{expenseCategory}', [App\Http\Controllers\SettingsController::class, 'deleteExpenseCategory'])->name('settings.financial.expense-categories.delete');

        Route::post('settings/financial/payment-methods', [App\Http\Controllers\SettingsController::class, 'storePaymentMethod'])->name('settings.financial.payment-methods.store');
        Route::put('settings/financial/payment-methods/{paymentMethod}', [App\Http\Controllers\SettingsController::class, 'updatePaymentMethod'])->name('settings.financial.payment-methods.update');
        Route::delete('settings/financial/payment-methods/{paymentMethod}', [App\Http\Controllers\SettingsController::class, 'deletePaymentMethod'])->name('settings.financial.payment-methods.delete');

        Route::post('settings/financial/tax-rates', [App\Http\Controllers\SettingsController::class, 'storeTaxRate'])->name('settings.financial.tax-rates.store');
        Route::put('settings/financial/tax-rates/{taxRate}', [App\Http\Controllers\SettingsController::class, 'updateTaxRate'])->name('settings.financial.tax-rates.update');
        Route::delete('settings/financial/tax-rates/{taxRate}', [App\Http\Controllers\SettingsController::class, 'deleteTaxRate'])->name('settings.financial.tax-rates.delete');

        // Project Settings
        Route::post('settings/project/statuses', [App\Http\Controllers\SettingsController::class, 'storeProjectStatus'])->name('settings.project.statuses.store');
        Route::put('settings/project/statuses/{projectStatus}', [App\Http\Controllers\SettingsController::class, 'updateProjectStatus'])->name('settings.project.statuses.update');
        Route::delete('settings/project/statuses/{projectStatus}', [App\Http\Controllers\SettingsController::class, 'deleteProjectStatus'])->name('settings.project.statuses.delete');

        // Security Settings
        Route::put('settings/security', [App\Http\Controllers\SettingsController::class, 'updateSecurity'])->name('settings.security.update');
    });

    // Career Management Routes (Admin)
    Route::name('admin.')->middleware('permission:recruitment.view')->group(function () {
        // Job Vacancy Management
        Route::resource('jobs', App\Http\Controllers\Admin\JobVacancyController::class);
        
        // Tab Views for Job Detail Hub
        Route::get('jobs/{id}/applications', [App\Http\Controllers\Admin\JobVacancyController::class, 'applications'])->name('jobs.applications');
        Route::get('jobs/{id}/pipeline', [App\Http\Controllers\Admin\RecruitmentPipelineController::class, 'jobPipeline'])->name('jobs.pipeline');
        Route::get('jobs/{id}/tests', [App\Http\Controllers\Admin\JobVacancyController::class, 'tests'])->name('jobs.tests');
        Route::get('jobs/{id}/interviews', [App\Http\Controllers\Admin\InterviewScheduleController::class, 'jobInterviews'])->name('jobs.interviews');
        
        // Job Application Management
        Route::get('applications', [App\Http\Controllers\Admin\JobApplicationController::class, 'index'])->name('applications.index');
        Route::get('applications/{id}', [App\Http\Controllers\Admin\JobApplicationController::class, 'show'])->name('applications.show');
        Route::patch('applications/{id}/status', [App\Http\Controllers\Admin\JobApplicationController::class, 'updateStatus'])->name('applications.update-status');
        Route::get('applications/{id}/download-cv', [App\Http\Controllers\Admin\JobApplicationController::class, 'downloadCv'])->name('applications.download-cv');
        Route::get('applications/{id}/download-portfolio', [App\Http\Controllers\Admin\JobApplicationController::class, 'downloadPortfolio'])->name('applications.download-portfolio');
        Route::delete('applications/{id}', [App\Http\Controllers\Admin\JobApplicationController::class, 'destroy'])->name('applications.destroy');
    });
    
    // Email Management Routes
    Route::name('admin.')->middleware(['auth', 'email.access'])->group(function () {
        // Email Management Hub (Unified Tab Interface)
        Route::get('email-management', [App\Http\Controllers\Admin\EmailManagementController::class, 'index'])->name('email-management.index');
        
        // Email Campaigns
        Route::resource('campaigns', App\Http\Controllers\Admin\EmailCampaignController::class);
        Route::get('campaigns/{id}/send', [App\Http\Controllers\Admin\EmailCampaignController::class, 'send'])->name('campaigns.send');
        Route::post('campaigns/{id}/process-send', [App\Http\Controllers\Admin\EmailCampaignController::class, 'processSend'])->name('campaigns.process-send');
        Route::post('campaigns/{id}/cancel', [App\Http\Controllers\Admin\EmailCampaignController::class, 'cancel'])->name('campaigns.cancel');
        
        // Email Inbox
        Route::get('inbox', [App\Http\Controllers\Admin\EmailInboxController::class, 'index'])->name('inbox.index');
        Route::get('inbox/compose', [App\Http\Controllers\Admin\EmailInboxController::class, 'compose'])->name('inbox.compose');
        Route::post('inbox/send', [App\Http\Controllers\Admin\EmailInboxController::class, 'send'])->name('inbox.send');
        Route::delete('inbox/batch-delete', [App\Http\Controllers\Admin\EmailInboxController::class, 'batchDelete'])->name('inbox.batch-delete');
        Route::get('inbox/{id}', [App\Http\Controllers\Admin\EmailInboxController::class, 'show'])->name('inbox.show');
        Route::get('inbox/{id}/reply', [App\Http\Controllers\Admin\EmailInboxController::class, 'reply'])->name('inbox.reply');
        Route::post('inbox/{id}/reply', [App\Http\Controllers\Admin\EmailInboxController::class, 'sendReply'])->name('inbox.send-reply');
        Route::post('inbox/{id}/read', [App\Http\Controllers\Admin\EmailInboxController::class, 'markAsRead'])->name('inbox.mark-read');
        Route::post('inbox/{id}/unread', [App\Http\Controllers\Admin\EmailInboxController::class, 'markAsUnread'])->name('inbox.mark-unread');
        Route::post('inbox/{id}/star', [App\Http\Controllers\Admin\EmailInboxController::class, 'toggleStar'])->name('inbox.toggle-star');
        Route::post('inbox/{id}/trash', [App\Http\Controllers\Admin\EmailInboxController::class, 'moveToTrash'])->name('inbox.trash');
        Route::delete('inbox/{id}', [App\Http\Controllers\Admin\EmailInboxController::class, 'delete'])->name('inbox.delete');
        Route::post('inbox/empty-trash', [App\Http\Controllers\Admin\EmailInboxController::class, 'emptyTrash'])->name('inbox.empty-trash');
        
        // Email Subscribers
        Route::resource('subscribers', App\Http\Controllers\Admin\EmailSubscriberController::class);
        
        // Email Templates
        Route::resource('templates', App\Http\Controllers\Admin\EmailTemplateController::class);
        
        // Email Settings
        Route::get('email/settings', [App\Http\Controllers\Admin\EmailSettingsController::class, 'index'])->name('email.settings.index');
        Route::put('email/settings', [App\Http\Controllers\Admin\EmailSettingsController::class, 'update'])->name('email.settings.update');
        Route::post('email/settings/test', [App\Http\Controllers\Admin\EmailSettingsController::class, 'test'])->name('email.settings.test');
        
        // KBLI Settings
        Route::prefix('settings/kbli')->name('settings.kbli.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\KbliSettingsController::class, 'index'])->name('index');
            Route::post('import', [App\Http\Controllers\Admin\KbliSettingsController::class, 'import'])->name('import');
            Route::get('template', [App\Http\Controllers\Admin\KbliSettingsController::class, 'downloadTemplate'])->name('template');
            Route::get('export', [App\Http\Controllers\Admin\KbliSettingsController::class, 'export'])->name('export');
            Route::delete('clear', [App\Http\Controllers\Admin\KbliSettingsController::class, 'clear'])->name('clear');
        });

        // SEO Command Center (Unified Hub)
        Route::prefix('seo')->name('seo.')->group(function () {
            // Unified Command Center (main entry point)
            Route::get('/command-center', [App\Http\Controllers\Admin\SeoCommandCenterController::class, 'index'])->name('command-center');
            
            // Redirect old dashboard to command-center
            Route::get('/', function() {
                return redirect()->route('admin.seo.command-center');
            })->name('dashboard');
            Route::get('/scores', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'scores'])->name('scores');

            // Static POST/GET routes BEFORE wildcard {articleId}
            Route::post('/scores/fix-batch', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'fixBatch'])->name('fix-batch');
            Route::post('/scores/rescore-all', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'rescoreAll'])->name('rescore-all');
            Route::get('/scores/fix-candidates', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'fixCandidates'])->name('fix-candidates');
            Route::post('/scores/fix-single-ajax/{articleId}', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'fixSingleAjax'])->name('fix-single-ajax');

            Route::get('/scores/{articleId}', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'scoreDetail'])->name('score-detail');
            Route::post('/scores/{articleId}/fix', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'fixSingle'])->name('fix-single');

            Route::get('/reports', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'reports'])->name('reports');
            Route::get('/reports/{reportId}', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'reportDetail'])->name('report-detail');
            Route::get('/competitors', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'competitors'])->name('competitors');
            Route::get('/competitors/{id}', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'competitorDetail'])->name('competitor-detail');
            Route::post('/competitors/{id}/re-analyze', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'reAnalyzeKeyword'])->name('competitor-reanalyze');
            Route::post('/competitors/analyze-keyword', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'analyzeCustomKeyword'])->name('competitor-analyze-keyword');
            Route::post('/competitors/{id}/apply-fix', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'applyCompetitorFix'])->name('competitor-apply-fix');
            Route::post('/competitors/{id}/verify', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'verifyCompetitorFix'])->name('competitor-verify');
            Route::get('/competitors/{id}/smart-fix', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'competitorSmartFix'])->name('competitor-smart-fix');
            Route::post('/competitors/{id}/smart-fix', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'executeSmartFix'])->name('competitor-execute-smart-fix');
            Route::get('/ab-tests', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'abTests'])->name('ab-tests');
            Route::post('/ab-tests/{id}/evaluate', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'evaluateAbTest'])->name('ab-tests.evaluate');
            Route::post('/ab-tests/{id}/stop', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'stopAbTest'])->name('ab-tests.stop');
            Route::post('/ab-tests/{id}/apply', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'applyAbTestWinner'])->name('ab-tests.apply');
            Route::post('/ab-tests/{id}/update-data', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'updateAbTestData'])->name('ab-tests.update-data');
            Route::delete('/ab-tests/{id}', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'deleteAbTest'])->name('ab-tests.delete');
            Route::post('/ab-tests/evaluate-all', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'evaluateAllAbTests'])->name('ab-tests.evaluate-all');
            Route::get('/search-console', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'searchConsole'])->name('search-console');
            Route::post('/search-console/import', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'importSearchConsole'])->name('search-console.import');
            Route::post('/search-console/clear', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'clearSearchConsole'])->name('search-console.clear');
            Route::get('/refresh-logs', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'refreshLogs'])->name('refresh-logs');
            Route::post('/refresh-logs/run', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'runContentRefresh'])->name('refresh-logs.run');
            Route::post('/refresh-logs/{id}/retry', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'retryRefresh'])->name('refresh-logs.retry');
            Route::delete('/refresh-logs/{id}', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'deleteRefreshLog'])->name('refresh-logs.delete');
            Route::get('/refresh-logs/{id}', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'showRefreshLog'])->name('refresh-logs.show');
            Route::get('/programmatic', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'programmatic'])->name('programmatic');
            Route::post('/programmatic/clear-cache', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'clearProgrammaticCache'])->name('programmatic.clear-cache');

            // Position Tracking (SERP rankings)
            Route::get('/positions', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'positions'])->name('positions');
            Route::get('/positions/trend/{keyword}', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'positionTrend'])->name('positions.trend');
            Route::post('/positions/track', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'trackPositions'])->name('positions.track');
            Route::get('/alerts', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'rankingAlerts'])->name('alerts');
            Route::post('/alerts/{id}/read', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'markAlertRead'])->name('alerts.read');
            Route::post('/alerts/read-all', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'markAllAlertsRead'])->name('alerts.read-all');

            // Web-triggered SEO commands (replaces manual artisan commands)
            Route::post('/run/snapshot-views', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'runSnapshotViews'])->name('run-snapshot-views');
            Route::post('/run/generate-report', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'runGenerateReport'])->name('run-generate-report');
            Route::post('/run/competitor-analyze', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'runCompetitorAnalyze'])->name('run-competitor-analyze');
            Route::post('/run/ab-tests', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'runAbTests'])->name('run-ab-tests');
            Route::match(['get', 'post'], '/run/score-articles', [App\Http\Controllers\Admin\SeoAnalyticsController::class, 'scoreArticlesProgress'])->name('run-score-articles');
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
});

// Client Portal Routes
Route::prefix('client')->name('client.')->group(function () {
    // Guest routes (login, register, password reset)
    Route::middleware('guest:client')->group(function () {
        // Redirect old client login to unified login
        Route::get('/login', fn() => redirect('/login'))->name('legacy.login');
        
        // Keep legacy POST for backwards compatibility (will be deprecated)
        Route::post('/login', [App\Http\Controllers\Auth\ClientAuthController::class, 'login'])
            ->middleware('throttle:5,1'); // Max 5 attempts per minute
        
        Route::get('/register', [App\Http\Controllers\Auth\ClientAuthController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [App\Http\Controllers\Auth\ClientAuthController::class, 'register'])
            ->middleware('throttle:10,1'); // Max 10 registrations per minute
        
        Route::get('/forgot-password', [App\Http\Controllers\Auth\ClientAuthController::class, 'showForgotPasswordForm'])->name('password.request');
        Route::post('/forgot-password', [App\Http\Controllers\Auth\ClientAuthController::class, 'sendResetLinkEmail'])
            ->middleware('throttle:5,1') // Max 5 attempts per minute
            ->name('password.email');
        
        Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\ClientAuthController::class, 'showResetPasswordForm'])->name('password.reset');
        Route::post('/reset-password', [App\Http\Controllers\Auth\ClientAuthController::class, 'resetPassword'])
            ->middleware('throttle:3,1') // Max 3 attempts per minute
            ->name('password.update');
    });
    
    // Protected routes (requires authentication)
    Route::middleware('auth:client')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [App\Http\Controllers\Auth\ClientAuthController::class, 'logout'])->name('logout');
        
        // Service Catalog Routes (KBLI-based AI System)
        Route::get('/services', [App\Http\Controllers\Client\ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/{kbliCode}/context', [App\Http\Controllers\Client\ServiceController::class, 'context'])->name('services.context');
        Route::post('/services/{kbliCode}/context', [App\Http\Controllers\Client\ServiceController::class, 'storeContext'])->name('services.storeContext');
        Route::get('/services/{kbliCode}/download-summary', [App\Http\Controllers\Client\ServiceController::class, 'downloadSummary'])->name('services.downloadSummary');
        Route::get('/services/{kbliCode}', [App\Http\Controllers\Client\ServiceController::class, 'show'])->name('services.show');
        
        // Application Routes
        Route::get('/applications', [App\Http\Controllers\Client\ApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/create', [App\Http\Controllers\Client\ApplicationController::class, 'create'])->name('applications.create');
        Route::post('/applications', [App\Http\Controllers\Client\ApplicationController::class, 'store'])->name('applications.store');
        Route::post('/applications/select-permits', [App\Http\Controllers\Client\ApplicationController::class, 'selectPermits'])->name('applications.select-permits');
        Route::get('/applications/create-package', [App\Http\Controllers\Client\ApplicationController::class, 'createPackage'])->name('applications.create-package');
        Route::post('/applications/store-package', [App\Http\Controllers\Client\ApplicationController::class, 'storeMultiple'])->name('applications.store-package');
        Route::get('/applications/{id}', [App\Http\Controllers\Client\ApplicationController::class, 'show'])->name('applications.show');
        Route::get('/applications/{id}/edit', [App\Http\Controllers\Client\ApplicationController::class, 'edit'])->name('applications.edit');
        Route::put('/applications/{id}', [App\Http\Controllers\Client\ApplicationController::class, 'update'])->name('applications.update');
        Route::get('/applications/{id}/preview-submit', [App\Http\Controllers\Client\ApplicationController::class, 'previewSubmit'])->name('applications.preview-submit');
        Route::post('/applications/{id}/submit', [App\Http\Controllers\Client\ApplicationController::class, 'submit'])->name('applications.submit');
        Route::post('/applications/{id}/cancel', [App\Http\Controllers\Client\ApplicationController::class, 'cancel'])->name('applications.cancel');
        Route::post('/applications/{id}/documents', [App\Http\Controllers\Client\ApplicationController::class, 'uploadDocument'])->name('applications.documents.upload');
        Route::delete('/applications/{applicationId}/documents/{documentId}', [App\Http\Controllers\Client\ApplicationController::class, 'deleteDocument'])->name('applications.documents.delete');
        
        // Quotation Routes (Phase 3.4)
        Route::get('/applications/{id}/quotation', [App\Http\Controllers\Client\ClientQuotationController::class, 'show'])->name('quotations.show');
        Route::post('/applications/{id}/quotation/accept', [App\Http\Controllers\Client\ClientQuotationController::class, 'accept'])->name('quotations.accept');
        Route::post('/applications/{id}/quotation/reject', [App\Http\Controllers\Client\ClientQuotationController::class, 'reject'])->name('quotations.reject');
        
        // Payment Routes (Phase 4)
        Route::get('/applications/{id}/payment', [App\Http\Controllers\Client\PaymentController::class, 'show'])->name('payments.show');
        Route::post('/applications/{id}/payment/initiate', [App\Http\Controllers\Client\PaymentController::class, 'initiate'])->name('payments.initiate');
        Route::post('/applications/{id}/payment/manual', [App\Http\Controllers\Client\PaymentController::class, 'manual'])->name('payments.manual');
        Route::get('/applications/{id}/payment/{paymentId}/success', [App\Http\Controllers\Client\PaymentController::class, 'success'])->name('payments.success');

        // Notifications
        Route::get('/notifications', [App\Http\Controllers\Client\NotificationController::class, 'index'])
            ->name('notifications.index');
        Route::post('/notifications/read-all', [App\Http\Controllers\Client\NotificationController::class, 'markAllRead'])
            ->name('notifications.read-all');

        // Project Routes
        Route::get('/projects', [App\Http\Controllers\Client\ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{id}', [App\Http\Controllers\Client\ProjectController::class, 'show'])->name('projects.show');
        
        // Document Routes
        Route::get('/documents', [App\Http\Controllers\Client\DocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents', [App\Http\Controllers\Client\DocumentController::class, 'store'])->name('documents.store');
        Route::get('/documents/{id}/download', [App\Http\Controllers\Client\DocumentController::class, 'download'])->name('documents.download');
        
        // Profile Routes
        Route::get('/profile', [App\Http\Controllers\Client\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [App\Http\Controllers\Client\ProfileController::class, 'update'])->name('profile.update');
        
        // Email Verification Routes
        Route::get('/verify-email', [App\Http\Controllers\Auth\ClientAuthController::class, 'showVerifyEmailNotice'])
            ->name('verification.notice');
        Route::post('/email/verification-notification', [App\Http\Controllers\Auth\ClientAuthController::class, 'resendVerificationEmail'])
            ->middleware('throttle:3,1')
            ->name('verification.send');
    });
    
    // Email verification callback (accessible without auth to allow verification)
    Route::get('/verify-email/{id}/{hash}', [App\Http\Controllers\Auth\ClientAuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
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

// Twitter OAuth callback placeholder (used for developer portal app setup)
Route::get('/auth/twitter/callback', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Twitter callback endpoint is active.',
    ]);
})->name('auth.twitter.callback');

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


