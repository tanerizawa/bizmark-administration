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
use App\Http\Controllers\PermitTypeController;
use App\Http\Controllers\PermitTemplateController;
use App\Http\Controllers\ProjectPermitController;
use App\Http\Controllers\PermitController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\ExportController;

// Landing Page (Public)
Route::get('/', function () {
    return view('landing');
})->name('landing');

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

    // Master Data - Permit Management Routes (Phase 2A)
    Route::resource('permit-types', PermitTypeController::class);
    Route::patch('permit-types/{permitType}/toggle-status', [PermitTypeController::class, 'toggleStatus'])->name('permit-types.toggle-status');

    Route::resource('permit-templates', PermitTemplateController::class);
    Route::post('permit-templates/{permitTemplate}/apply', [PermitTemplateController::class, 'applyToProject'])->name('permit-templates.apply');

    // Project Permit Management Routes (Phase 2A - Sprint 3)
    // Note: These routes are legacy and might overlap with PermitController routes below
    // Route::post('projects/{project}/permits', [ProjectPermitController::class, 'store'])->name('projects.permits.store');
    // Route::post('projects/{project}/permits/apply-template', [ProjectPermitController::class, 'applyTemplate'])->name('projects.permits.apply-template');
    // Route::patch('projects/{project}/permits/reorder', [ProjectPermitController::class, 'reorder'])->name('projects.permits.reorder.old');
    // Route::patch('permits/{permit}/status', [ProjectPermitController::class, 'updateStatus'])->name('permits.update-status');
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
});
