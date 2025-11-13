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
    Route::get('export/projects', [ExportController::class, 'exportProjects'])->name('export.projects');
    Route::get('export/projects/{id}/details', [ExportController::class, 'exportProjectDetails'])->name('export.project.details');

    // Project Management Routes
    Route::resource('projects', ProjectController::class);
    Route::patch('projects/{project}/status', [ProjectController::class, 'updateStatus'])->name('projects.update-status');

    // Task Management Routes
    Route::resource('tasks', TaskController::class);
    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::patch('tasks/{task}/assignment', [TaskController::class, 'updateAssignment'])->name('tasks.update-assignment');
    Route::patch('projects/{project}/tasks/reorder', [TaskController::class, 'reorder'])->name('projects.tasks.reorder');

    // Document Management Routes
    Route::resource('documents', DocumentController::class);
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('api/tasks-by-project', [DocumentController::class, 'getTasksByProject'])->name('api.tasks-by-project');

    // Institution Management Routes
    Route::resource('institutions', InstitutionController::class);
    Route::get('api/institutions', [InstitutionController::class, 'apiIndex'])->name('api.institutions');

    // Client Management Routes
    Route::resource('clients', App\Http\Controllers\ClientController::class);
    Route::get('api/clients', [App\Http\Controllers\ClientController::class, 'apiIndex'])->name('api.clients');

    // Financial Management Routes (Phase 1)
    Route::post('projects/{project}/payments', [ProjectPaymentController::class, 'store'])->name('projects.payments.store');
    Route::delete('payments/{payment}', [ProjectPaymentController::class, 'destroy'])->name('payments.destroy');
    Route::post('projects/{project}/expenses', [ProjectExpenseController::class, 'store'])->name('projects.expenses.store');
    Route::delete('expenses/{expense}', [ProjectExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::resource('cash-accounts', CashAccountController::class);
    
    // API endpoint for active cash accounts
    Route::get('api/cash-accounts/active', [CashAccountController::class, 'getActiveCashAccounts'])->name('api.cash-accounts.active');

    // Bank Reconciliation Routes (Phase 1B)
    Route::resource('reconciliations', BankReconciliationController::class);
    Route::get('reconciliations/{reconciliation}/match', [BankReconciliationController::class, 'match'])->name('reconciliations.match');
    Route::post('reconciliations/{reconciliation}/auto-match', [BankReconciliationController::class, 'autoMatch'])->name('reconciliations.auto-match');
    Route::post('reconciliations/{reconciliation}/manual-match', [BankReconciliationController::class, 'manualMatch'])->name('reconciliations.manual-match');
    Route::post('reconciliations/{reconciliation}/unmatch', [BankReconciliationController::class, 'unmatch'])->name('reconciliations.unmatch');
    Route::post('reconciliations/{reconciliation}/complete', [BankReconciliationController::class, 'complete'])->name('reconciliations.complete');

    // Article Management Routes
    Route::resource('articles', ArticleController::class);
    Route::post('articles/{article}/publish', [ArticleController::class, 'publish'])->name('articles.publish');
    Route::post('articles/{article}/unpublish', [ArticleController::class, 'unpublish'])->name('articles.unpublish');
    Route::post('articles/{article}/archive', [ArticleController::class, 'archive'])->name('articles.archive');
    Route::post('articles/upload-image', [ArticleController::class, 'uploadImage'])->name('articles.upload-image');

    // Master Data - Permit Management Routes (Phase 2A)
    Route::resource('permit-types', PermitTypeController::class);
    Route::patch('permit-types/{permitType}/toggle-status', [PermitTypeController::class, 'toggleStatus'])->name('permit-types.toggle-status');

    Route::resource('permit-templates', PermitTemplateController::class);
    Route::post('permit-templates/{permitTemplate}/apply', [PermitTemplateController::class, 'applyToProject'])->name('permit-templates.apply');

    // Project Permit Management Routes (Phase 2A - Sprint 3)
    // Individual permit status update (used by permits tab)
    Route::patch('permits/{permit}/status', [ProjectPermitController::class, 'updateStatus'])->name('permits.update-status');
    
    // Note: These routes are legacy and might overlap with PermitController routes below
    // Route::post('projects/{project}/permits', [ProjectPermitController::class, 'store'])->name('projects.permits.store');
    // Route::post('projects/{project}/permits/apply-template', [ProjectPermitController::class, 'applyTemplate'])->name('projects.permits.apply-template');
    // Route::patch('projects/{project}/permits/reorder', [ProjectPermitController::class, 'reorder'])->name('projects.permits.reorder.old');
    // Route::post('permits/{permit}/dependencies', [ProjectPermitController::class, 'addDependency'])->name('permits.add-dependency');
    // Route::delete('permits/{permit}/dependencies/{dependency}', [ProjectPermitController::class, 'removeDependency'])->name('permits.remove-dependency');
    // Route::delete('permits/{permit}', [ProjectPermitController::class, 'destroy'])->name('permits.destroy');

    // Financial Tab Management Routes (Phase 2A - Sprint 6)
    Route::get('projects/{project}/financial', [FinancialController::class, 'index'])->name('projects.financial');
    Route::post('projects/{project}/invoices', [FinancialController::class, 'storeInvoice'])->name('projects.invoices.store');
    Route::get('invoices/{invoice}', [FinancialController::class, 'showInvoice'])->name('invoices.show');
    Route::get('invoices/{invoice}/pdf', [FinancialController::class, 'downloadInvoicePDF'])->name('invoices.download-pdf');
    Route::patch('invoices/{invoice}/status', [FinancialController::class, 'updateInvoiceStatus'])->name('invoices.update-status');
    Route::post('invoices/{invoice}/payment', [FinancialController::class, 'recordPayment'])->name('invoices.record-payment');
    Route::delete('invoices/{invoice}', [FinancialController::class, 'destroyInvoice'])->name('invoices.destroy');
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

    // Excel Export Routes (Phase 2A - Sprint 7)
    Route::get('exports/invoices', [FinancialController::class, 'exportInvoices'])->name('exports.invoices');
    Route::get('exports/invoices/{invoice}', [FinancialController::class, 'exportInvoiceDetail'])->name('exports.invoice-detail');
    Route::get('exports/expenses', [FinancialController::class, 'exportExpenses'])->name('exports.expenses');
    Route::get('exports/financial-report', [FinancialController::class, 'exportFinancialReport'])->name('exports.financial-report');

    // Permit Management Routes (Phase 2A - Sprint 8)
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

    // Settings Management Routes (Phase 2A - Sprint 9)
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

    // Career Management Routes (Admin)
    Route::prefix('admin')->name('admin.')->group(function () {
        // Job Vacancy Management
        Route::resource('jobs', App\Http\Controllers\Admin\JobVacancyController::class);
        
        // Job Application Management
        Route::get('applications', [App\Http\Controllers\Admin\JobApplicationController::class, 'index'])->name('applications.index');
        Route::get('applications/{id}', [App\Http\Controllers\Admin\JobApplicationController::class, 'show'])->name('applications.show');
        Route::patch('applications/{id}/status', [App\Http\Controllers\Admin\JobApplicationController::class, 'updateStatus'])->name('applications.update-status');
        Route::get('applications/{id}/download-cv', [App\Http\Controllers\Admin\JobApplicationController::class, 'downloadCv'])->name('applications.download-cv');
        Route::get('applications/{id}/download-portfolio', [App\Http\Controllers\Admin\JobApplicationController::class, 'downloadPortfolio'])->name('applications.download-portfolio');
        Route::delete('applications/{id}', [App\Http\Controllers\Admin\JobApplicationController::class, 'destroy'])->name('applications.destroy');
        
        // Email Management Routes
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
    });

    // AI Document Paraphrasing Routes
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

// Client Portal Routes
Route::prefix('client')->name('client.')->group(function () {
    // Guest routes (login, register, password reset)
    Route::middleware('guest:client')->group(function () {
        Route::get('/login', [App\Http\Controllers\Auth\ClientAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [App\Http\Controllers\Auth\ClientAuthController::class, 'login']);
        
        Route::get('/forgot-password', [App\Http\Controllers\Auth\ClientAuthController::class, 'showForgotPasswordForm'])->name('password.request');
        Route::post('/forgot-password', [App\Http\Controllers\Auth\ClientAuthController::class, 'sendResetLinkEmail'])->name('password.email');
        
        Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\ClientAuthController::class, 'showResetPasswordForm'])->name('password.reset');
        Route::post('/reset-password', [App\Http\Controllers\Auth\ClientAuthController::class, 'resetPassword'])->name('password.update');
    });
    
    // Protected routes (requires authentication)
    Route::middleware('auth:client')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [App\Http\Controllers\Auth\ClientAuthController::class, 'logout'])->name('logout');
    });
});
