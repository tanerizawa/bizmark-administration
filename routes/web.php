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
use App\Http\Controllers\BankReconciliationController;
use App\Http\Controllers\PermitTypeController;
use App\Http\Controllers\PermitTemplateController;
use App\Http\Controllers\ProjectPermitController;
use App\Http\Controllers\PermitController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\PublicArticleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\SitemapController;

// SEO Routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Language Switcher
Route::get('/locale/{locale}', [LocaleController::class, 'setLocale'])->name('locale.set');

// Legal Pages
Route::get('/kebijakan-privasi', function() {
    return view('legal.privacy');
})->name('privacy.policy');

Route::get('/syarat-ketentuan', function() {
    return view('legal.terms');
})->name('terms.conditions');

// Landing Page (Public) - With Latest Articles
Route::get('/', [PublicArticleController::class, 'landing'])->name('landing');

// Service Pages (Public)
Route::get('/layanan', [ServiceController::class, 'index'])->name('services.index');
Route::get('/layanan/{slug}', [ServiceController::class, 'show'])->name('services.show');

// Public Blog Routes
Route::get('/blog', [PublicArticleController::class, 'index'])->name('blog.index');
Route::get('/blog/category/{category}', [PublicArticleController::class, 'category'])->name('blog.category');
Route::get('/blog/tag/{tag}', [PublicArticleController::class, 'tag'])->name('blog.tag');
Route::get('/blog/{slug}', [PublicArticleController::class, 'show'])->name('blog.article');

// Permit Calculator Tool (Public)
Route::get('/kalkulator-perizinan', [App\Http\Controllers\CalculatorController::class, 'index'])->name('calculator.index');
Route::post('/kalkulator-perizinan/calculate', [App\Http\Controllers\CalculatorController::class, 'calculate'])->name('calculator.calculate');

// Career/Jobs Pages (Public)
Route::get('/karir', [App\Http\Controllers\JobVacancyController::class, 'index'])->name('career.index');
Route::get('/karir/{slug}', [App\Http\Controllers\JobVacancyController::class, 'show'])->name('career.show');
Route::get('/karir/{vacancy_id}/apply', [App\Http\Controllers\JobApplicationController::class, 'create'])->name('career.apply');
Route::post('/karir/apply', [App\Http\Controllers\JobApplicationController::class, 'store'])->name('career.apply.store');

// Newsletter Subscription (Public)
Route::post('/subscribe', [App\Http\Controllers\SubscriberController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/unsubscribe/{email}/{token}', [App\Http\Controllers\SubscriberController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::get('/email/track/{tracking_id}', [App\Http\Controllers\SubscriberController::class, 'trackOpen'])->name('email.track');

// Custom Authentication Routes (Hidden Login Path for Security)
// Login routes with custom path '/hadez' instead of '/login'
Route::get('/hadez', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/hadez', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Redirect /login to homepage to hide the real login path
Route::get('/login', function () {
    return redirect('/');
});

// Protected Routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::post('/dashboard/clear-cache', [DashboardController::class, 'clearCache'])->name('dashboard.clear-cache');
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home');

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

    // Financial Management Routes (Phase 1)
    // Read-only routes (auth required)
    Route::middleware('auth')->group(function () {
        Route::get('cash-accounts', [CashAccountController::class, 'index'])->name('cash-accounts.index');
        Route::get('cash-accounts/{cash_account}', [CashAccountController::class, 'show'])->name('cash-accounts.show');
    });
    
    // Write routes (require permission)
    Route::middleware('permission:finances.view')->group(function () {
        Route::post('projects/{project}/payments', [ProjectPaymentController::class, 'store'])->name('projects.payments.store');
        Route::delete('payments/{payment}', [ProjectPaymentController::class, 'destroy'])->name('payments.destroy');
        Route::post('projects/{project}/expenses', [ProjectExpenseController::class, 'store'])->name('projects.expenses.store');
        Route::delete('expenses/{expense}', [ProjectExpenseController::class, 'destroy'])->name('expenses.destroy');
        
        // Cash accounts write operations
        Route::get('cash-accounts/create', [CashAccountController::class, 'create'])->name('cash-accounts.create');
        Route::post('cash-accounts', [CashAccountController::class, 'store'])->name('cash-accounts.store');
        Route::get('cash-accounts/{cash_account}/edit', [CashAccountController::class, 'edit'])->name('cash-accounts.edit');
        Route::put('cash-accounts/{cash_account}', [CashAccountController::class, 'update'])->name('cash-accounts.update');
        Route::patch('cash-accounts/{cash_account}', [CashAccountController::class, 'update']);
        Route::delete('cash-accounts/{cash_account}', [CashAccountController::class, 'destroy'])->name('cash-accounts.destroy');

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
    Route::middleware('permission:invoices.view')->group(function () {
        Route::get('projects/{project}/financial', [FinancialController::class, 'index'])->name('projects.financial');
        Route::post('projects/{project}/invoices', [FinancialController::class, 'storeInvoice'])->name('projects.invoices.store');
        Route::get('invoices/{invoice}', [FinancialController::class, 'showInvoice'])->name('invoices.show');
        Route::get('invoices/{invoice}/pdf', [FinancialController::class, 'downloadInvoicePDF'])->name('invoices.download-pdf');
        Route::patch('invoices/{invoice}/status', [FinancialController::class, 'updateInvoiceStatus'])->name('invoices.update-status');
        Route::post('invoices/{invoice}/payment', [FinancialController::class, 'recordPayment'])->name('invoices.record-payment');
        Route::delete('invoices/{invoice}', [FinancialController::class, 'destroyInvoice'])->name('invoices.destroy');
        Route::post('projects/{project}/direct-income', [FinancialController::class, 'storeDirectIncome'])->name('projects.direct-income.store');
        Route::post('projects/{project}/payment-schedules', [FinancialController::class, 'storePaymentSchedule'])->name('projects.payment-schedules.store');
        Route::patch('payment-schedules/{schedule}/paid', [FinancialController::class, 'markSchedulePaid'])->name('payment-schedules.mark-paid');
        Route::delete('payment-schedules/{schedule}', [FinancialController::class, 'destroySchedule'])->name('payment-schedules.destroy');
        Route::post('projects/{project}/financial-expenses', [FinancialController::class, 'storeExpense'])->name('projects.financial-expenses.store');
        Route::get('financial-expenses/{expense}', [FinancialController::class, 'getExpense'])->name('financial-expenses.show');
        Route::patch('financial-expenses/{expense}', [FinancialController::class, 'updateExpense'])->name('financial-expenses.update');
        Route::delete('financial-expenses/{expense}', [FinancialController::class, 'destroyExpense'])->name('financial-expenses.destroy');
        Route::delete('financial-expenses/{expense}/delete-receipt', [FinancialController::class, 'deleteReceipt'])->name('financial-expenses.delete-receipt');
        Route::patch('financial-expenses/{expense}/mark-invoiced', [FinancialController::class, 'markExpenseInvoiced'])->name('financial-expenses.mark-invoiced');
        Route::patch('financial-expenses/{expense}/record-payment', [FinancialController::class, 'recordReceivablePayment'])->name('financial-expenses.record-payment');
        Route::patch('financial-expenses/{expense}/remove-receivable', [FinancialController::class, 'removeReceivable'])->name('financial-expenses.remove-receivable');
    });

    // Excel Export Routes (Phase 2A - Sprint 7)
    Route::middleware('permission:invoices.view')->group(function () {
        Route::get('exports/invoices', [FinancialController::class, 'exportInvoices'])->name('exports.invoices');
        Route::get('exports/invoices/{invoice}', [FinancialController::class, 'exportInvoiceDetail'])->name('exports.invoice-detail');
        Route::get('exports/expenses', [FinancialController::class, 'exportExpenses'])->name('exports.expenses');
        Route::get('exports/financial-report', [FinancialController::class, 'exportFinancialReport'])->name('exports.financial-report');
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
    Route::prefix('admin')->name('admin.')->middleware('permission:recruitment.view')->group(function () {
        // Job Vacancy Management
        Route::resource('jobs', App\Http\Controllers\Admin\JobVacancyController::class);
        
        // Job Application Management
        Route::get('applications', [App\Http\Controllers\Admin\JobApplicationController::class, 'index'])->name('applications.index');
        Route::get('applications/{id}', [App\Http\Controllers\Admin\JobApplicationController::class, 'show'])->name('applications.show');
        Route::patch('applications/{id}/status', [App\Http\Controllers\Admin\JobApplicationController::class, 'updateStatus'])->name('applications.update-status');
        Route::get('applications/{id}/download-cv', [App\Http\Controllers\Admin\JobApplicationController::class, 'downloadCv'])->name('applications.download-cv');
        Route::get('applications/{id}/download-portfolio', [App\Http\Controllers\Admin\JobApplicationController::class, 'downloadPortfolio'])->name('applications.download-portfolio');
        Route::delete('applications/{id}', [App\Http\Controllers\Admin\JobApplicationController::class, 'destroy'])->name('applications.destroy');
    });
    
    // Email Management Routes
    Route::prefix('admin')->name('admin.')->middleware('permission:email.manage')->group(function () {
        // Email Campaigns
        Route::resource('campaigns', App\Http\Controllers\Admin\EmailCampaignController::class);
        Route::get('campaigns/{id}/send', [App\Http\Controllers\Admin\EmailCampaignController::class, 'send'])->name('campaigns.send');
        Route::post('campaigns/{id}/process-send', [App\Http\Controllers\Admin\EmailCampaignController::class, 'processSend'])->name('campaigns.process-send');
        Route::post('campaigns/{id}/cancel', [App\Http\Controllers\Admin\EmailCampaignController::class, 'cancel'])->name('campaigns.cancel');
        
        // Email Inbox
        Route::get('inbox', [App\Http\Controllers\Admin\EmailInboxController::class, 'index'])->name('inbox.index');
        Route::get('inbox/compose', [App\Http\Controllers\Admin\EmailInboxController::class, 'compose'])->name('inbox.compose');
        Route::post('inbox/send', [App\Http\Controllers\Admin\EmailInboxController::class, 'send'])->name('inbox.send');
        Route::get('inbox/{id}', [App\Http\Controllers\Admin\EmailInboxController::class, 'show'])->name('inbox.show');
        Route::get('inbox/{id}/reply', [App\Http\Controllers\Admin\EmailInboxController::class, 'reply'])->name('inbox.reply');
        Route::post('inbox/{id}/reply', [App\Http\Controllers\Admin\EmailInboxController::class, 'sendReply'])->name('inbox.send-reply');
        Route::post('inbox/{id}/read', [App\Http\Controllers\Admin\EmailInboxController::class, 'markAsRead'])->name('inbox.mark-read');
        Route::post('inbox/{id}/unread', [App\Http\Controllers\Admin\EmailInboxController::class, 'markAsUnread'])->name('inbox.mark-unread');
        Route::post('inbox/{id}/star', [App\Http\Controllers\Admin\EmailInboxController::class, 'toggleStar'])->name('inbox.toggle-star');
        Route::post('inbox/{id}/trash', [App\Http\Controllers\Admin\EmailInboxController::class, 'moveToTrash'])->name('inbox.trash');
        Route::delete('inbox/{id}', [App\Http\Controllers\Admin\EmailInboxController::class, 'delete'])->name('inbox.delete');
        
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
        Route::get('/login', [App\Http\Controllers\Auth\ClientAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [App\Http\Controllers\Auth\ClientAuthController::class, 'login'])
            ->middleware('throttle:5,1'); // Max 5 attempts per minute
        
        Route::get('/register', [App\Http\Controllers\Auth\ClientAuthController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [App\Http\Controllers\Auth\ClientAuthController::class, 'register'])
            ->middleware('throttle:3,1'); // Max 3 registrations per minute
        
        Route::get('/forgot-password', [App\Http\Controllers\Auth\ClientAuthController::class, 'showForgotPasswordForm'])->name('password.request');
        Route::post('/forgot-password', [App\Http\Controllers\Auth\ClientAuthController::class, 'sendResetLinkEmail'])
            ->middleware('throttle:3,1') // Max 3 attempts per minute
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
        ->name('receive');
    
    // Test webhook dengan data dummy
    Route::post('/test', [App\Http\Controllers\EmailWebhookController::class, 'test'])
        ->name('test');
    
    // Check webhook status
    Route::get('/status', [App\Http\Controllers\EmailWebhookController::class, 'status'])
        ->name('status');
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
    // Permit Dashboard
    Route::get('permit-dashboard', [App\Http\Controllers\Admin\PermitDashboardController::class, 'index'])
        ->name('permit-dashboard');
    
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
