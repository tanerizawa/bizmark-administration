<?php

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
        Route::get('cash-accounts', [App\Http\Controllers\CashAccountController::class, 'index'])->name('cash-accounts.index');
        Route::get('cash-accounts/create', [App\Http\Controllers\CashAccountController::class, 'create'])->name('cash-accounts.create');
        Route::get('cash-accounts/{cash_account}', [App\Http\Controllers\CashAccountController::class, 'show'])->name('cash-accounts.show');
    });
    
    // Write routes (require permission)
    Route::middleware('permission:finances.view')->group(function () {
        Route::post('projects/{project}/payments', [App\Http\Controllers\ProjectPaymentController::class, 'store'])->name('projects.payments.store');
        Route::delete('payments/{payment}', [App\Http\Controllers\ProjectPaymentController::class, 'destroy'])->name('payments.destroy');
        Route::post('projects/{project}/expenses', [App\Http\Controllers\ProjectExpenseController::class, 'store'])->name('projects.expenses.store');
        Route::delete('expenses/{expense}', [App\Http\Controllers\ProjectExpenseController::class, 'destroy'])->name('expenses.destroy');
        
        // Cash accounts write operations
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

        // Bank Reconciliation Routes (Phase 1B)
        Route::resource('reconciliations', App\Http\Controllers\BankReconciliationController::class);
        Route::get('reconciliations/{reconciliation}/match', [App\Http\Controllers\BankReconciliationController::class, 'match'])->name('reconciliations.match');
        Route::post('reconciliations/{reconciliation}/auto-match', [App\Http\Controllers\BankReconciliationController::class, 'autoMatch'])->name('reconciliations.auto-match');
        Route::post('reconciliations/{reconciliation}/manual-match', [App\Http\Controllers\BankReconciliationController::class, 'manualMatch'])->name('reconciliations.manual-match');
        Route::post('reconciliations/{reconciliation}/unmatch', [App\Http\Controllers\BankReconciliationController::class, 'unmatch'])->name('reconciliations.unmatch');
        Route::post('reconciliations/{reconciliation}/complete', [App\Http\Controllers\BankReconciliationController::class, 'complete'])->name('reconciliations.complete');
    });
    
    // API endpoints for AJAX calls (requires auth only, no specific permission)
    Route::middleware('auth')->group(function () {
        Route::get('api/cash-accounts/active', [App\Http\Controllers\CashAccountController::class, 'getActiveCashAccounts'])->name('api.cash-accounts.active');
    });

    // Article Management Routes
    Route::middleware('permission:content.manage')->group(function () {
        Route::resource('articles', App\Http\Controllers\ArticleController::class);
        Route::post('articles/{article}/publish', [App\Http\Controllers\ArticleController::class, 'publish'])->name('articles.publish');
        Route::post('articles/{article}/unpublish', [App\Http\Controllers\ArticleController::class, 'unpublish'])->name('articles.unpublish');
        Route::post('articles/{article}/archive', [App\Http\Controllers\ArticleController::class, 'archive'])->name('articles.archive');
        Route::post('articles/upload-image', [App\Http\Controllers\ArticleController::class, 'uploadImage'])->name('articles.upload-image');
        
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
        Route::resource('permit-types', App\Http\Controllers\PermitTypeController::class);
        Route::patch('permit-types/{permitType}/toggle-status', [App\Http\Controllers\PermitTypeController::class, 'toggleStatus'])->name('permit-types.toggle-status');

        Route::resource('permit-templates', App\Http\Controllers\PermitTemplateController::class);
        Route::post('permit-templates/{permitTemplate}/apply', [App\Http\Controllers\PermitTemplateController::class, 'applyToProject'])->name('permit-templates.apply');
    });

    // Project Permit Management Routes (Phase 2A - Sprint 3)
    Route::middleware('permission:projects.view')->group(function () {
        // Individual permit status update (used by permits tab)
        Route::patch('permits/{permit}/status', [App\Http\Controllers\ProjectPermitController::class, 'updateStatus'])->name('permits.update-status');
    });
    
    // Note: These routes are legacy and might overlap with PermitController routes below
    // Route::post('projects/{project}/permits', [App\Http\Controllers\ProjectPermitController::class, 'store'])->name('projects.permits.store');
    // Route::post('projects/{project}/permits/apply-template', [App\Http\Controllers\ProjectPermitController::class, 'applyTemplate'])->name('projects.permits.apply-template');
    // Route::patch('projects/{project}/permits/reorder', [App\Http\Controllers\ProjectPermitController::class, 'reorder'])->name('projects.permits.reorder.old');
    // Route::post('permits/{permit}/dependencies', [App\Http\Controllers\ProjectPermitController::class, 'addDependency'])->name('permits.add-dependency');
    // Route::delete('permits/{permit}/dependencies/{dependency}', [App\Http\Controllers\ProjectPermitController::class, 'removeDependency'])->name('permits.remove-dependency');
    // Route::delete('permits/{permit}', [App\Http\Controllers\ProjectPermitController::class, 'destroy'])->name('permits.destroy');

    // Financial Tab Management Routes (Phase 2A - Sprint 6)
    // Refactored: FinancialController (1089 LOC) dipecah ke per-domain controller di App\Http\Controllers\Financial\*
    Route::middleware('permission:invoices.view')->group(function () {
        Route::get('projects/{project}/financial', [App\Http\Controllers\Financial\OverviewController::class, 'index'])->name('projects.financial');

        Route::post('projects/{project}/invoices', [App\Http\Controllers\Financial\InvoiceController::class, 'store'])->name('projects.invoices.store');
        Route::get('invoices/{invoice}', [App\Http\Controllers\Financial\InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('invoices/{invoice}/pdf', [App\Http\Controllers\Financial\InvoiceController::class, 'downloadPDF'])->name('invoices.download-pdf');
        Route::patch('invoices/{invoice}/status', [App\Http\Controllers\Financial\InvoiceController::class, 'updateStatus'])->name('invoices.update-status');
        Route::post('invoices/{invoice}/payment', [App\Http\Controllers\Financial\InvoiceController::class, 'recordPayment'])->name('invoices.record-payment');
        Route::delete('invoices/{invoice}', [App\Http\Controllers\Financial\InvoiceController::class, 'destroy'])->name('invoices.destroy');

        Route::post('projects/{project}/direct-income', [App\Http\Controllers\Financial\DirectIncomeController::class, 'store'])->name('projects.direct-income.store');
        Route::get('projects/{project}/direct-income/{payment}', [App\Http\Controllers\Financial\DirectIncomeController::class, 'edit'])->name('projects.direct-income.edit');
        Route::patch('projects/{project}/direct-income/{payment}', [App\Http\Controllers\Financial\DirectIncomeController::class, 'update'])->name('projects.direct-income.update');
        Route::delete('projects/{project}/direct-income/{payment}', [App\Http\Controllers\Financial\DirectIncomeController::class, 'destroy'])->name('projects.direct-income.destroy');

        Route::post('projects/{project}/payment-schedules', [App\Http\Controllers\Financial\PaymentScheduleController::class, 'store'])->name('projects.payment-schedules.store');
        Route::patch('payment-schedules/{schedule}/paid', [App\Http\Controllers\Financial\PaymentScheduleController::class, 'markPaid'])->name('payment-schedules.mark-paid');
        Route::delete('payment-schedules/{schedule}', [App\Http\Controllers\Financial\PaymentScheduleController::class, 'destroy'])->name('payment-schedules.destroy');

        Route::post('projects/{project}/financial-expenses', [App\Http\Controllers\Financial\ExpenseController::class, 'store'])->name('projects.financial-expenses.store');
        Route::get('financial-expenses/{expense}', [App\Http\Controllers\Financial\ExpenseController::class, 'show'])->name('financial-expenses.show');
        Route::patch('financial-expenses/{expense}', [App\Http\Controllers\Financial\ExpenseController::class, 'update'])->name('financial-expenses.update');
        Route::delete('financial-expenses/{expense}', [App\Http\Controllers\Financial\ExpenseController::class, 'destroy'])->name('financial-expenses.destroy');
        Route::delete('financial-expenses/{expense}/delete-receipt', [App\Http\Controllers\Financial\ExpenseController::class, 'deleteReceipt'])->name('financial-expenses.delete-receipt');
        Route::patch('financial-expenses/{expense}/mark-invoiced', [App\Http\Controllers\Financial\ExpenseController::class, 'markInvoiced'])->name('financial-expenses.mark-invoiced');
        Route::patch('financial-expenses/{expense}/record-payment', [App\Http\Controllers\Financial\ExpenseController::class, 'recordReceivablePayment'])->name('financial-expenses.record-payment');
        Route::patch('financial-expenses/{expense}/remove-receivable', [App\Http\Controllers\Financial\ExpenseController::class, 'removeReceivable'])->name('financial-expenses.remove-receivable');
    });

    // Excel Export Routes (Phase 2A - Sprint 7)
    Route::middleware('permission:invoices.view')->group(function () {
        Route::get('exports/invoices', [App\Http\Controllers\Financial\ExportController::class, 'invoices'])->name('exports.invoices');
        Route::get('exports/invoices/{invoice}', [App\Http\Controllers\Financial\ExportController::class, 'invoiceDetail'])->name('exports.invoice-detail');
        Route::get('exports/expenses', [App\Http\Controllers\Financial\ExportController::class, 'expenses'])->name('exports.expenses');
        Route::get('exports/financial-report', [App\Http\Controllers\Financial\ExportController::class, 'financialReport'])->name('exports.financial-report');
    });

    // Permit Management Routes (Phase 2A - Sprint 8)
    Route::middleware('permission:projects.view')->group(function () {
        Route::get('projects/{project}/permits', [App\Http\Controllers\PermitController::class, 'index'])->name('projects.permits');
        Route::post('projects/{project}/permits', [App\Http\Controllers\PermitController::class, 'store'])->name('projects.permits.store');
        Route::patch('permits/{permit}', [App\Http\Controllers\PermitController::class, 'update'])->name('permits.update');
        Route::delete('permits/{permit}', [App\Http\Controllers\PermitController::class, 'destroy'])->name('permits.destroy');
        Route::post('projects/{project}/permits/apply-template', [App\Http\Controllers\PermitController::class, 'applyTemplate'])->name('projects.permits.apply-template');
        Route::post('permits/{permit}/dependencies', [App\Http\Controllers\PermitController::class, 'addDependency'])->name('permits.add-dependency');
        Route::post('projects/{project}/permits/reorder', [App\Http\Controllers\PermitController::class, 'reorder'])->name('projects.permits.reorder');
        // Bulk operations
        Route::post('projects/{project}/permits/bulk-update-status', [App\Http\Controllers\PermitController::class, 'bulkUpdateStatus'])->name('projects.permits.bulk-update-status');
        Route::post('projects/{project}/permits/bulk-delete', [App\Http\Controllers\PermitController::class, 'bulkDelete'])->name('projects.permits.bulk-delete');
        // Document management
        Route::post('projects/{project}/permits/{permit}/documents/upload', [App\Http\Controllers\PermitController::class, 'uploadDocument'])->name('permits.documents.upload');
        Route::get('projects/{project}/permits/documents/{document}/download', [App\Http\Controllers\PermitController::class, 'downloadDocument'])->name('permits.documents.download');
        Route::delete('projects/{project}/permits/documents/{document}', [App\Http\Controllers\PermitController::class, 'deleteDocument'])->name('permits.documents.delete');
        Route::post('permits/documents/{document}/delete', [App\Http\Controllers\PermitController::class, 'deleteDocumentPost'])->name('permits.documents.delete-post');
    });
