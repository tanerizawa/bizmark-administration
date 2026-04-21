<?php

use Illuminate\Support\Facades\Route;

    // Lead Management Routes (Unified with Tabs)
    Route::middleware('permission:clients.view')->group(function () {
        // Unified Lead Management Page with Tabs
        Route::get('leads', [App\Http\Controllers\Admin\LeadManagementController::class, 'index'])->name('admin.leads.index');
        
        // Service Inquiry routes (kept for detail pages and actions)
        Route::get('service-inquiries/export', [App\Http\Controllers\Admin\ServiceInquiryController::class, 'export'])->name('admin.service-inquiries.export');
        Route::get('service-inquiries/{serviceInquiry}', [App\Http\Controllers\Admin\ServiceInquiryController::class, 'show'])->name('admin.service-inquiries.show');
        Route::patch('service-inquiries/{serviceInquiry}/status', [App\Http\Controllers\Admin\ServiceInquiryController::class, 'updateStatus'])
            ->middleware('permission:clients.edit')
            ->name('admin.service-inquiries.update-status');
        Route::patch('service-inquiries/{serviceInquiry}/priority', [App\Http\Controllers\Admin\ServiceInquiryController::class, 'updatePriority'])
            ->middleware('permission:clients.edit')
            ->name('admin.service-inquiries.update-priority');
        Route::post('service-inquiries/{serviceInquiry}/note', [App\Http\Controllers\Admin\ServiceInquiryController::class, 'addNote'])
            ->middleware('permission:clients.edit')
            ->name('admin.service-inquiries.add-note');
        Route::post('service-inquiries/{serviceInquiry}/convert', [App\Http\Controllers\Admin\ServiceInquiryController::class, 'convertToProject'])
            ->middleware('permission:projects.create')
            ->name('admin.service-inquiries.convert');
        Route::delete('service-inquiries/{serviceInquiry}', [App\Http\Controllers\Admin\ServiceInquiryController::class, 'destroy'])
            ->middleware('permission:clients.delete')
            ->name('admin.service-inquiries.destroy');
        
        // Consultation Leads routes (kept for detail pages and actions)
        Route::get('consultation-leads', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'index'])->name('admin.consultation-leads.index');
        Route::get('consultation-leads/export', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'export'])->name('admin.consultation-leads.export');
        Route::get('consultation-leads/{consultation}', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'show'])->name('admin.consultation-leads.show');
        Route::post('consultation-leads/{consultation}/update-status', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'updateStatus'])
            ->middleware('permission:clients.edit')
            ->name('admin.consultation-leads.update-status');
        Route::post('consultation-leads/{consultation}/mark-contacted', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'markContacted'])
            ->middleware('permission:clients.edit')
            ->name('admin.consultation-leads.mark-contacted');
        Route::post('consultation-leads/{consultation}/convert', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'convertToClient'])
            ->middleware('permission:clients.create')
            ->name('admin.consultation-leads.convert-to-client');
        Route::post('consultation-leads/{consultation}/note', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'addNote'])
            ->middleware('permission:clients.edit')
            ->name('admin.consultation-leads.add-note');
        Route::delete('consultation-leads/{consultation}', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'destroy'])
            ->middleware('permission:clients.delete')
            ->name('admin.consultation-leads.destroy');
        
        // Service Cost Request routes (for detail pages and admin actions)
        Route::get('service-cost-requests/{requestNumber}', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'show'])->name('admin.service-cost-requests.show');
        Route::patch('service-cost-requests/{requestNumber}/status', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'updateStatus'])
            ->middleware('permission:clients.edit')
            ->name('admin.service-cost-requests.update-status');
        Route::post('service-cost-requests/{requestNumber}/note', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'addNote'])
            ->middleware('permission:clients.edit')
            ->name('admin.service-cost-requests.add-note');
        Route::post('service-cost-requests/{requestNumber}/generate-quote', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'generateQuote'])
            ->middleware('permission:invoices.create')
            ->name('admin.service-cost-requests.generate-quote');
        Route::post('service-cost-requests/{requestNumber}/regenerate-content', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'regenerateQuoteContent'])
            ->middleware('permission:invoices.edit')
            ->name('admin.service-cost-requests.regenerate-content');
        Route::post('service-cost-requests/{requestNumber}/send-email', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'sendQuoteEmail'])
            ->middleware('permission:email.send_email')
            ->name('admin.service-cost-requests.send-email');
        Route::post('service-cost-requests/{requestNumber}/complete', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'complete'])
            ->middleware('permission:clients.edit')
            ->name('admin.service-cost-requests.complete');
        Route::post('service-cost-requests/{requestNumber}/archive', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'archive'])
            ->middleware('permission:clients.edit')
            ->name('admin.service-cost-requests.archive');
        
        // Backward compatibility redirect
        Route::redirect('service-inquiries', '/admin/leads?tab=service-inquiries');
    });

    // Financial Management Routes (Phase 1)
    Route::get('cash-accounts', [App\Http\Controllers\CashAccountController::class, 'index'])
        ->middleware('permission:finances.view')
        ->name('cash-accounts.index');
    Route::get('cash-accounts/{cash_account}', [App\Http\Controllers\CashAccountController::class, 'show'])
        ->middleware('permission:finances.view')
        ->name('cash-accounts.show');
    Route::get('cash-accounts/create', [App\Http\Controllers\CashAccountController::class, 'create'])
        ->middleware('permission:finances.manage_accounts')
        ->name('cash-accounts.create');
    
    Route::middleware(['permission:finances.manage_payments', '2fa'])->group(function () {
        Route::post('projects/{project}/payments', [App\Modules\Proyek\Controllers\Public\ProjectPaymentController::class, 'store'])->name('projects.payments.store');
        Route::delete('payments/{payment}', [App\Modules\Proyek\Controllers\Public\ProjectPaymentController::class, 'destroy'])->name('payments.destroy');
    });

    Route::middleware(['permission:finances.manage_expenses', '2fa'])->group(function () {
        Route::post('projects/{project}/expenses', [App\Modules\Proyek\Controllers\Public\ProjectExpenseController::class, 'store'])->name('projects.expenses.store');
        Route::delete('expenses/{expense}', [App\Modules\Proyek\Controllers\Public\ProjectExpenseController::class, 'destroy'])->name('expenses.destroy');
    });

    Route::middleware(['permission:finances.manage_accounts', '2fa'])->group(function () {
        Route::post('cash-accounts', [App\Http\Controllers\CashAccountController::class, 'store'])->name('cash-accounts.store');
        Route::get('cash-accounts/{cash_account}/edit', [App\Http\Controllers\CashAccountController::class, 'edit'])->name('cash-accounts.edit');
        Route::put('cash-accounts/{cash_account}', [App\Http\Controllers\CashAccountController::class, 'update'])->name('cash-accounts.update');
        Route::patch('cash-accounts/{cash_account}', [App\Http\Controllers\CashAccountController::class, 'update']);
        Route::delete('cash-accounts/{cash_account}', [App\Http\Controllers\CashAccountController::class, 'destroy'])->name('cash-accounts.destroy');

        // General Transaction Routes (Phase 2 - Non-Project Income/Expense)
        Route::prefix('general-transactions')->name('general-transactions.')->group(function () {
            // General Income
            Route::post('income', [App\Http\Controllers\GeneralTransactionController::class, 'storeIncome'])->name('income.store');
            Route::get('income/{id}', [App\Http\Controllers\GeneralTransactionController::class, 'getIncome'])->name('income.show');
            Route::put('income/{id}', [App\Http\Controllers\GeneralTransactionController::class, 'updateIncome'])->name('income.update');
            Route::delete('income/{id}', [App\Http\Controllers\GeneralTransactionController::class, 'destroyIncome'])->name('income.destroy');
            
            // General Expense
            Route::post('expense', [App\Http\Controllers\GeneralTransactionController::class, 'storeExpense'])->name('expense.store');
            Route::get('expense/{id}', [App\Http\Controllers\GeneralTransactionController::class, 'getExpense'])->name('expense.show');
            Route::put('expense/{id}', [App\Http\Controllers\GeneralTransactionController::class, 'updateExpense'])->name('expense.update');
            Route::delete('expense/{id}', [App\Http\Controllers\GeneralTransactionController::class, 'destroyExpense'])->name('expense.destroy');
        });
    });

    Route::resource('reconciliations', App\Http\Controllers\BankReconciliationController::class)
        ->only(['index', 'show'])
        ->middleware('permission:finances.view');
    Route::resource('reconciliations', App\Http\Controllers\BankReconciliationController::class)
        ->except(['index', 'show'])
        ->middleware(['permission:finances.manage_accounts', '2fa']);
    Route::get('reconciliations/{reconciliation}/match', [App\Http\Controllers\BankReconciliationController::class, 'match'])
        ->middleware(['permission:finances.manage_accounts', '2fa'])
        ->name('reconciliations.match');
    Route::post('reconciliations/{reconciliation}/auto-match', [App\Http\Controllers\BankReconciliationController::class, 'autoMatch'])
        ->middleware(['permission:finances.manage_accounts', '2fa'])
        ->name('reconciliations.auto-match');
    Route::post('reconciliations/{reconciliation}/manual-match', [App\Http\Controllers\BankReconciliationController::class, 'manualMatch'])
        ->middleware(['permission:finances.manage_accounts', '2fa'])
        ->name('reconciliations.manual-match');
    Route::post('reconciliations/{reconciliation}/unmatch', [App\Http\Controllers\BankReconciliationController::class, 'unmatch'])
        ->middleware(['permission:finances.manage_accounts', '2fa'])
        ->name('reconciliations.unmatch');
    Route::post('reconciliations/{reconciliation}/complete', [App\Http\Controllers\BankReconciliationController::class, 'complete'])
        ->middleware(['permission:finances.manage_accounts', '2fa'])
        ->name('reconciliations.complete');
    
    Route::get('api/cash-accounts/active', [App\Http\Controllers\CashAccountController::class, 'getActiveCashAccounts'])
        ->middleware('permission:finances.view')
        ->name('api.cash-accounts.active');

    // Article Management Routes
    Route::resource('articles', App\Http\Controllers\ArticleController::class)
        ->only(['index', 'show'])
        ->middleware('permission:content.view_articles');
    Route::resource('articles', App\Http\Controllers\ArticleController::class)
        ->only(['create', 'store'])
        ->middleware('permission:content.create_articles');
    Route::resource('articles', App\Http\Controllers\ArticleController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:content.edit_articles');
    Route::resource('articles', App\Http\Controllers\ArticleController::class)
        ->only(['destroy'])
        ->middleware('permission:content.delete_articles');
    Route::post('articles/{article}/publish', [App\Http\Controllers\ArticleController::class, 'publish'])
        ->middleware('permission:content.publish_articles')
        ->name('articles.publish');
    Route::post('articles/{article}/unpublish', [App\Http\Controllers\ArticleController::class, 'unpublish'])
        ->middleware('permission:content.publish_articles')
        ->name('articles.unpublish');
    Route::post('articles/{article}/archive', [App\Http\Controllers\ArticleController::class, 'archive'])
        ->middleware('permission:content.edit_articles')
        ->name('articles.archive');
    Route::post('articles/upload-image', [App\Http\Controllers\ArticleController::class, 'uploadImage'])
        ->middleware('permission:content.edit_articles')
        ->name('articles.upload-image');
        
        // Pexels API Routes
        Route::prefix('pexels')->name('pexels.')->middleware('permission:content.manage')->group(function () {
            Route::get('search', [App\Http\Controllers\Admin\PexelsController::class, 'search'])->name('search');
            Route::get('curated', [App\Http\Controllers\Admin\PexelsController::class, 'curated'])->name('curated');
            Route::post('download', [App\Http\Controllers\Admin\PexelsController::class, 'download'])->name('download');
        });
        
        // Auto-Post Management Routes (Unified Dashboard)
        Route::prefix('auto-post')->name('auto-post.')->middleware('permission:content.manage')->group(function () {
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

    // Master Data - Permit Management Routes (Phase 2A)
    Route::middleware('permission:master_data.manage')->group(function () {
        Route::resource('permit-types', App\Modules\Perizinan\Controllers\Public\PermitTypeController::class);
        Route::patch('permit-types/{permitType}/toggle-status', [App\Modules\Perizinan\Controllers\Public\PermitTypeController::class, 'toggleStatus'])->name('permit-types.toggle-status');

        Route::resource('permit-templates', App\Modules\Perizinan\Controllers\Public\PermitTemplateController::class);
        Route::post('permit-templates/{permitTemplate}/apply', [App\Modules\Perizinan\Controllers\Public\PermitTemplateController::class, 'applyToProject'])->name('permit-templates.apply');
    });

    // Project Permit Management Routes (Phase 2A - Sprint 3)
    Route::middleware('permission:projects.view')->group(function () {
        // Individual permit status update (used by permits tab)
        Route::patch('permits/{permit}/status', [App\Modules\Perizinan\Controllers\Public\ProjectPermitController::class, 'updateStatus'])->name('permits.update-status');
    });
    
    // Note: These routes are legacy and might overlap with PermitController routes below
    // Route::post('projects/{project}/permits', [App\Modules\Perizinan\Controllers\Public\ProjectPermitController::class, 'store'])->name('projects.permits.store');
    // Route::post('projects/{project}/permits/apply-template', [App\Modules\Perizinan\Controllers\Public\ProjectPermitController::class, 'applyTemplate'])->name('projects.permits.apply-template');
    // Route::patch('projects/{project}/permits/reorder', [App\Modules\Perizinan\Controllers\Public\ProjectPermitController::class, 'reorder'])->name('projects.permits.reorder.old');
    // Route::post('permits/{permit}/dependencies', [App\Modules\Perizinan\Controllers\Public\ProjectPermitController::class, 'addDependency'])->name('permits.add-dependency');
    // Route::delete('permits/{permit}/dependencies/{dependency}', [App\Modules\Perizinan\Controllers\Public\ProjectPermitController::class, 'removeDependency'])->name('permits.remove-dependency');
    // Route::delete('permits/{permit}', [App\Modules\Perizinan\Controllers\Public\ProjectPermitController::class, 'destroy'])->name('permits.destroy');

    // Financial Tab Management Routes (Phase 2A - Sprint 6)
    // Refactored: FinancialController (1089 LOC) dipecah ke per-domain controller di App\Modules\Finansial\Controllers\*
    Route::get('projects/{project}/financial', [App\Modules\Finansial\Controllers\OverviewController::class, 'index'])
        ->middleware('permission:invoices.view')
        ->name('projects.financial');

    Route::post('projects/{project}/invoices', [App\Modules\Finansial\Controllers\InvoiceController::class, 'store'])
        ->middleware(['permission:invoices.create', '2fa'])
        ->name('projects.invoices.store');
    Route::get('invoices/{invoice}', [App\Modules\Finansial\Controllers\InvoiceController::class, 'show'])
        ->middleware('permission:invoices.view')
        ->name('invoices.show');
    Route::get('invoices/{invoice}/pdf', [App\Modules\Finansial\Controllers\InvoiceController::class, 'downloadPDF'])
        ->middleware('permission:invoices.view')
        ->name('invoices.download-pdf');
    Route::patch('invoices/{invoice}/status', [App\Modules\Finansial\Controllers\InvoiceController::class, 'updateStatus'])
        ->middleware(['permission:invoices.edit', '2fa'])
        ->name('invoices.update-status');
    Route::post('invoices/{invoice}/payment', [App\Modules\Finansial\Controllers\InvoiceController::class, 'recordPayment'])
        ->middleware(['permission:finances.manage_payments', '2fa'])
        ->name('invoices.record-payment');
    Route::post('invoices/{invoice}/send', [App\Modules\Finansial\Controllers\InvoiceController::class, 'send'])
        ->middleware(['permission:invoices.edit'])
        ->name('invoices.send');
    Route::delete('invoices/{invoice}', [App\Modules\Finansial\Controllers\InvoiceController::class, 'destroy'])
        ->middleware(['permission:invoices.delete', '2fa'])
        ->name('invoices.destroy');

    Route::post('projects/{project}/direct-income', [App\Modules\Finansial\Controllers\DirectIncomeController::class, 'store'])
        ->middleware(['permission:finances.manage_payments', '2fa'])
        ->name('projects.direct-income.store');
    Route::get('projects/{project}/direct-income/{payment}', [App\Modules\Finansial\Controllers\DirectIncomeController::class, 'edit'])
        ->middleware(['permission:finances.manage_payments', '2fa'])
        ->name('projects.direct-income.edit');
    Route::patch('projects/{project}/direct-income/{payment}', [App\Modules\Finansial\Controllers\DirectIncomeController::class, 'update'])
        ->middleware(['permission:finances.manage_payments', '2fa'])
        ->name('projects.direct-income.update');
    Route::delete('projects/{project}/direct-income/{payment}', [App\Modules\Finansial\Controllers\DirectIncomeController::class, 'destroy'])
        ->middleware(['permission:finances.manage_payments', '2fa'])
        ->name('projects.direct-income.destroy');

    Route::post('projects/{project}/payment-schedules', [App\Modules\Finansial\Controllers\PaymentScheduleController::class, 'store'])
        ->middleware(['permission:finances.manage_payments', '2fa'])
        ->name('projects.payment-schedules.store');
    Route::patch('payment-schedules/{schedule}/paid', [App\Modules\Finansial\Controllers\PaymentScheduleController::class, 'markPaid'])
        ->middleware(['permission:finances.manage_payments', '2fa'])
        ->name('payment-schedules.mark-paid');
    Route::delete('payment-schedules/{schedule}', [App\Modules\Finansial\Controllers\PaymentScheduleController::class, 'destroy'])
        ->middleware(['permission:finances.manage_payments', '2fa'])
        ->name('payment-schedules.destroy');

    Route::post('projects/{project}/financial-expenses', [App\Modules\Finansial\Controllers\ExpenseController::class, 'store'])
        ->middleware(['permission:finances.manage_expenses', '2fa'])
        ->name('projects.financial-expenses.store');
    Route::get('financial-expenses/{expense}', [App\Modules\Finansial\Controllers\ExpenseController::class, 'show'])
        ->middleware('permission:finances.view')
        ->name('financial-expenses.show');
    Route::patch('financial-expenses/{expense}', [App\Modules\Finansial\Controllers\ExpenseController::class, 'update'])
        ->middleware(['permission:finances.manage_expenses', '2fa'])
        ->name('financial-expenses.update');
    Route::delete('financial-expenses/{expense}', [App\Modules\Finansial\Controllers\ExpenseController::class, 'destroy'])
        ->middleware(['permission:finances.manage_expenses', '2fa'])
        ->name('financial-expenses.destroy');
    Route::delete('financial-expenses/{expense}/delete-receipt', [App\Modules\Finansial\Controllers\ExpenseController::class, 'deleteReceipt'])
        ->middleware(['permission:finances.manage_expenses', '2fa'])
        ->name('financial-expenses.delete-receipt');
    Route::patch('financial-expenses/{expense}/mark-invoiced', [App\Modules\Finansial\Controllers\ExpenseController::class, 'markInvoiced'])
        ->middleware(['permission:finances.manage_expenses', '2fa'])
        ->name('financial-expenses.mark-invoiced');
    Route::patch('financial-expenses/{expense}/record-payment', [App\Modules\Finansial\Controllers\ExpenseController::class, 'recordReceivablePayment'])
        ->middleware(['permission:finances.manage_expenses', '2fa'])
        ->name('financial-expenses.record-payment');
    Route::patch('financial-expenses/{expense}/remove-receivable', [App\Modules\Finansial\Controllers\ExpenseController::class, 'removeReceivable'])
        ->middleware(['permission:finances.manage_expenses', '2fa'])
        ->name('financial-expenses.remove-receivable');

    // Excel Export Routes (Phase 2A - Sprint 7)
    Route::middleware(['permission:finances.view_reports', '2fa'])->group(function () {
        Route::get('exports/invoices', [App\Modules\Finansial\Controllers\ExportController::class, 'invoices'])->name('exports.invoices');
        Route::get('exports/invoices/{invoice}', [App\Modules\Finansial\Controllers\ExportController::class, 'invoiceDetail'])->name('exports.invoice-detail');
        Route::get('exports/expenses', [App\Modules\Finansial\Controllers\ExportController::class, 'expenses'])->name('exports.expenses');
        Route::get('exports/financial-report', [App\Modules\Finansial\Controllers\ExportController::class, 'financialReport'])->name('exports.financial-report');
    });

    // Permit Management Routes (Phase 2A - Sprint 8)
    Route::middleware('permission:projects.view')->group(function () {
        Route::get('projects/{project}/permits', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'index'])->name('projects.permits');
        Route::post('projects/{project}/permits', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'store'])->name('projects.permits.store');
        Route::patch('permits/{permit}', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'update'])->name('permits.update');
        Route::delete('permits/{permit}', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'destroy'])->name('permits.destroy');
        Route::post('projects/{project}/permits/apply-template', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'applyTemplate'])->name('projects.permits.apply-template');
        Route::post('permits/{permit}/dependencies', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'addDependency'])->name('permits.add-dependency');
        Route::post('projects/{project}/permits/reorder', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'reorder'])->name('projects.permits.reorder');
        // Bulk operations
        Route::post('projects/{project}/permits/bulk-update-status', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'bulkUpdateStatus'])->name('projects.permits.bulk-update-status');
        Route::post('projects/{project}/permits/bulk-delete', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'bulkDelete'])->name('projects.permits.bulk-delete');
        // Document management
        Route::post('projects/{project}/permits/{permit}/documents/upload', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'uploadDocument'])->name('permits.documents.upload');
        Route::get('projects/{project}/permits/documents/{document}/download', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'downloadDocument'])->name('permits.documents.download');
        Route::delete('projects/{project}/permits/documents/{document}', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'deleteDocument'])->name('permits.documents.delete');
        Route::post('permits/documents/{document}/delete', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'deleteDocumentPost'])->name('permits.documents.delete-post');
    });
