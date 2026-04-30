<?php

use Illuminate\Support\Facades\Route;

// Cash Account Routes
Route::get('cash-accounts', [App\Modules\Finansial\Controllers\CashAccountController::class, 'index'])
    ->middleware('permission:finances.view')
    ->name('cash-accounts.index');
Route::get('cash-accounts/{cash_account}', [App\Modules\Finansial\Controllers\CashAccountController::class, 'show'])
    ->middleware('permission:finances.view')
    ->name('cash-accounts.show');
Route::get('cash-accounts/create', [App\Modules\Finansial\Controllers\CashAccountController::class, 'create'])
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
    Route::post('cash-accounts', [App\Modules\Finansial\Controllers\CashAccountController::class, 'store'])->name('cash-accounts.store');
    Route::get('cash-accounts/{cash_account}/edit', [App\Modules\Finansial\Controllers\CashAccountController::class, 'edit'])->name('cash-accounts.edit');
    Route::put('cash-accounts/{cash_account}', [App\Modules\Finansial\Controllers\CashAccountController::class, 'update'])->name('cash-accounts.update');
    Route::patch('cash-accounts/{cash_account}', [App\Modules\Finansial\Controllers\CashAccountController::class, 'update']);
    Route::delete('cash-accounts/{cash_account}', [App\Modules\Finansial\Controllers\CashAccountController::class, 'destroy'])->name('cash-accounts.destroy');

    Route::prefix('general-transactions')->name('general-transactions.')->group(function () {
        Route::post('income', [App\Modules\Finansial\Controllers\GeneralTransactionController::class, 'storeIncome'])->name('income.store');
        Route::get('income/{id}', [App\Modules\Finansial\Controllers\GeneralTransactionController::class, 'getIncome'])->name('income.show');
        Route::put('income/{id}', [App\Modules\Finansial\Controllers\GeneralTransactionController::class, 'updateIncome'])->name('income.update');
        Route::delete('income/{id}', [App\Modules\Finansial\Controllers\GeneralTransactionController::class, 'destroyIncome'])->name('income.destroy');

        Route::post('expense', [App\Modules\Finansial\Controllers\GeneralTransactionController::class, 'storeExpense'])->name('expense.store');
        Route::get('expense/{id}', [App\Modules\Finansial\Controllers\GeneralTransactionController::class, 'getExpense'])->name('expense.show');
        Route::put('expense/{id}', [App\Modules\Finansial\Controllers\GeneralTransactionController::class, 'updateExpense'])->name('expense.update');
        Route::delete('expense/{id}', [App\Modules\Finansial\Controllers\GeneralTransactionController::class, 'destroyExpense'])->name('expense.destroy');
    });
});

// Bank Reconciliation Routes
Route::resource('reconciliations', App\Modules\Finansial\Controllers\BankReconciliationController::class)
    ->only(['index', 'show'])
    ->middleware('permission:finances.view');
Route::resource('reconciliations', App\Modules\Finansial\Controllers\BankReconciliationController::class)
    ->except(['index', 'show'])
    ->middleware(['permission:finances.manage_accounts', '2fa']);
Route::get('reconciliations/{reconciliation}/match', [App\Modules\Finansial\Controllers\BankReconciliationController::class, 'match'])
    ->middleware(['permission:finances.manage_accounts', '2fa'])
    ->name('reconciliations.match');
Route::post('reconciliations/{reconciliation}/auto-match', [App\Modules\Finansial\Controllers\BankReconciliationController::class, 'autoMatch'])
    ->middleware(['permission:finances.manage_accounts', '2fa'])
    ->name('reconciliations.auto-match');
Route::post('reconciliations/{reconciliation}/manual-match', [App\Modules\Finansial\Controllers\BankReconciliationController::class, 'manualMatch'])
    ->middleware(['permission:finances.manage_accounts', '2fa'])
    ->name('reconciliations.manual-match');
Route::post('reconciliations/{reconciliation}/unmatch', [App\Modules\Finansial\Controllers\BankReconciliationController::class, 'unmatch'])
    ->middleware(['permission:finances.manage_accounts', '2fa'])
    ->name('reconciliations.unmatch');
Route::post('reconciliations/{reconciliation}/complete', [App\Modules\Finansial\Controllers\BankReconciliationController::class, 'complete'])
    ->middleware(['permission:finances.manage_accounts', '2fa'])
    ->name('reconciliations.complete');

Route::get('api/cash-accounts/active', [App\Modules\Finansial\Controllers\CashAccountController::class, 'getActiveCashAccounts'])
    ->middleware('permission:finances.view')
    ->name('api.cash-accounts.active');

// Invoice & Direct Income Routes
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

// Payment Schedule Routes
Route::post('projects/{project}/payment-schedules', [App\Modules\Finansial\Controllers\PaymentScheduleController::class, 'store'])
    ->middleware(['permission:finances.manage_payments', '2fa'])
    ->name('projects.payment-schedules.store');
Route::patch('payment-schedules/{schedule}/paid', [App\Modules\Finansial\Controllers\PaymentScheduleController::class, 'markPaid'])
    ->middleware(['permission:finances.manage_payments', '2fa'])
    ->name('payment-schedules.mark-paid');
Route::delete('payment-schedules/{schedule}', [App\Modules\Finansial\Controllers\PaymentScheduleController::class, 'destroy'])
    ->middleware(['permission:finances.manage_payments', '2fa'])
    ->name('payment-schedules.destroy');

// Financial Expense Routes
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

// Excel Export Routes
Route::middleware(['permission:finances.view_reports', '2fa'])->group(function () {
    Route::get('exports/invoices', [App\Modules\Finansial\Controllers\ExportController::class, 'invoices'])->name('exports.invoices');
    Route::get('exports/invoices/{invoice}', [App\Modules\Finansial\Controllers\ExportController::class, 'invoiceDetail'])->name('exports.invoice-detail');
    Route::get('exports/expenses', [App\Modules\Finansial\Controllers\ExportController::class, 'expenses'])->name('exports.expenses');
    Route::get('exports/financial-report', [App\Modules\Finansial\Controllers\ExportController::class, 'financialReport'])->name('exports.financial-report');
});
