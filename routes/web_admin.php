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
use App\Http\Controllers\Admin\Seo\SeoScoresController;
use App\Http\Controllers\Admin\Seo\SeoReportsController;
use App\Http\Controllers\Admin\Seo\SeoCompetitorsController;
use App\Http\Controllers\Admin\Seo\SeoAbTestsController;
use App\Http\Controllers\Admin\Seo\SeoSearchConsoleController;
use App\Http\Controllers\Admin\Seo\SeoRefreshLogsController;
use App\Http\Controllers\Admin\Seo\SeoProgrammaticController;
use App\Http\Controllers\Admin\Seo\SeoPositionsController;
use App\Http\Controllers\Admin\Seo\SeoRankingAlertsController;

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
            Route::get('/scores', [SeoScoresController::class, 'scores'])->name('scores');

            // Static POST/GET routes BEFORE wildcard {articleId}
            Route::post('/scores/fix-batch', [SeoScoresController::class, 'fixBatch'])->name('fix-batch');
            Route::post('/scores/rescore-all', [SeoScoresController::class, 'rescoreAll'])->name('rescore-all');
            Route::get('/scores/fix-candidates', [SeoScoresController::class, 'fixCandidates'])->name('fix-candidates');
            Route::post('/scores/fix-single-ajax/{articleId}', [SeoScoresController::class, 'fixSingleAjax'])->name('fix-single-ajax');

            Route::get('/scores/{articleId}', [SeoScoresController::class, 'scoreDetail'])->name('score-detail');
            Route::post('/scores/{articleId}/fix', [SeoScoresController::class, 'fixSingle'])->name('fix-single');

            Route::get('/reports', [SeoReportsController::class, 'reports'])->name('reports');
            Route::get('/reports/{reportId}', [SeoReportsController::class, 'reportDetail'])->name('report-detail');
            Route::get('/competitors', [SeoCompetitorsController::class, 'competitors'])->name('competitors');
            Route::get('/competitors/{id}', [SeoCompetitorsController::class, 'competitorDetail'])->name('competitor-detail');
            Route::post('/competitors/{id}/re-analyze', [SeoCompetitorsController::class, 'reAnalyzeKeyword'])->name('competitor-reanalyze');
            Route::post('/competitors/analyze-keyword', [SeoCompetitorsController::class, 'analyzeCustomKeyword'])->name('competitor-analyze-keyword');
            Route::post('/competitors/{id}/apply-fix', [SeoCompetitorsController::class, 'applyCompetitorFix'])->name('competitor-apply-fix');
            Route::post('/competitors/{id}/verify', [SeoCompetitorsController::class, 'verifyCompetitorFix'])->name('competitor-verify');
            Route::get('/competitors/{id}/smart-fix', [SeoCompetitorsController::class, 'competitorSmartFix'])->name('competitor-smart-fix');
            Route::post('/competitors/{id}/smart-fix', [SeoCompetitorsController::class, 'executeSmartFix'])->name('competitor-execute-smart-fix');
            Route::get('/ab-tests', [SeoAbTestsController::class, 'abTests'])->name('ab-tests');
            Route::post('/ab-tests/{id}/evaluate', [SeoAbTestsController::class, 'evaluateAbTest'])->name('ab-tests.evaluate');
            Route::post('/ab-tests/{id}/stop', [SeoAbTestsController::class, 'stopAbTest'])->name('ab-tests.stop');
            Route::post('/ab-tests/{id}/apply', [SeoAbTestsController::class, 'applyAbTestWinner'])->name('ab-tests.apply');
            Route::post('/ab-tests/{id}/update-data', [SeoAbTestsController::class, 'updateAbTestData'])->name('ab-tests.update-data');
            Route::delete('/ab-tests/{id}', [SeoAbTestsController::class, 'deleteAbTest'])->name('ab-tests.delete');
            Route::post('/ab-tests/evaluate-all', [SeoAbTestsController::class, 'evaluateAllAbTests'])->name('ab-tests.evaluate-all');
            Route::get('/search-console', [SeoSearchConsoleController::class, 'searchConsole'])->name('search-console');
            Route::post('/search-console/import', [SeoSearchConsoleController::class, 'importSearchConsole'])->name('search-console.import');
            Route::post('/search-console/clear', [SeoSearchConsoleController::class, 'clearSearchConsole'])->name('search-console.clear');
            Route::get('/refresh-logs', [SeoRefreshLogsController::class, 'refreshLogs'])->name('refresh-logs');
            Route::post('/refresh-logs/run', [SeoRefreshLogsController::class, 'runContentRefresh'])->name('refresh-logs.run');
            Route::post('/refresh-logs/{id}/retry', [SeoRefreshLogsController::class, 'retryRefresh'])->name('refresh-logs.retry');
            Route::delete('/refresh-logs/{id}', [SeoRefreshLogsController::class, 'deleteRefreshLog'])->name('refresh-logs.delete');
            Route::get('/refresh-logs/{id}', [SeoRefreshLogsController::class, 'showRefreshLog'])->name('refresh-logs.show');
            Route::get('/programmatic', [SeoProgrammaticController::class, 'programmatic'])->name('programmatic');
            Route::post('/programmatic/clear-cache', [SeoProgrammaticController::class, 'clearProgrammaticCache'])->name('programmatic.clear-cache');

            // Position Tracking (SERP rankings)
            Route::get('/positions', [SeoPositionsController::class, 'positions'])->name('positions');
            Route::get('/positions/trend/{keyword}', [SeoPositionsController::class, 'positionTrend'])->name('positions.trend');
            Route::post('/positions/track', [SeoPositionsController::class, 'trackPositions'])->name('positions.track');
            Route::get('/alerts', [SeoRankingAlertsController::class, 'rankingAlerts'])->name('alerts');
            Route::post('/alerts/{id}/read', [SeoRankingAlertsController::class, 'markAlertRead'])->name('alerts.read');
            Route::post('/alerts/read-all', [SeoRankingAlertsController::class, 'markAllAlertsRead'])->name('alerts.read-all');

            // Web-triggered SEO commands (replaces manual artisan commands)
            Route::post('/run/snapshot-views', [SeoReportsController::class, 'runSnapshotViews'])->name('run-snapshot-views');
            Route::post('/run/generate-report', [SeoReportsController::class, 'runGenerateReport'])->name('run-generate-report');
            Route::post('/run/competitor-analyze', [SeoCompetitorsController::class, 'runCompetitorAnalyze'])->name('run-competitor-analyze');
            Route::post('/run/ab-tests', [SeoAbTestsController::class, 'runAbTests'])->name('run-ab-tests');
            Route::match(['get', 'post'], '/run/score-articles', [SeoScoresController::class, 'scoreArticlesProgress'])->name('run-score-articles');
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
