# BUG-04: CashAccountController Decomposition Plan

## Current State

[`CashAccountController`](app/Modules/Finansial/Controllers/CashAccountController.php:1) is **880 lines** with **5 mixed responsibilities**:

| Responsibility | Methods | Est. Lines |
|---|---|---|
| **CRUD actions** | `index()`, `create()`, `store()`, `show()`, `edit()`, `update()`, `destroy()`, `getActiveCashAccounts()` | ~200 |
| **Period/Date management** | Date range switch logic (duplicated in `index()` + `show()`), `getAvailablePeriods()`, `getLatestTransactionDate()` | ~100 |
| **Financial analytics** | `getFinancialSummary()`, `getCashFlowStatement()` | ~200 |
| **Transaction queries** | `getAccountMutations()`, `getRecentTransactions()`, `getGeneralTransactions()` | ~230 |
| **Unassigned tracking** | BUG-01 fix code in `show()` | ~50 |

## Target Architecture

```
CashAccountController (ramping: ~100 lines)
  ├── CashAccountService        → CRUD + account queries
  ├── CashFlowService           → Financial analytics (PSAK 2)
  ├── MutationService           → Transaction history + running balance
  └── PeriodService             → Date range logic + available periods
```

This mirrors the [`DashboardDataService`](app/Services/Dashboard/DashboardDataService.php:7) pattern: a controller delegates to injected services.

## Service Specifications

### 1. [`PeriodService`](app/Modules/Finansial/Services/PeriodService.php)

**Purpose:** Centralize all date/period logic, eliminating duplication between `index()` and `show()`.

```php
class PeriodService
{
    /**
     * Resolve date range from request filters.
     * Consolidates the switch/case logic currently duplicated in index() and show().
     *
     * @return array{startDate: Carbon, endDate: Carbon, filterType: string, selectedMonth: int, selectedYear: int}
     */
    public function resolveDateRange(Request $request): array;

    /**
     * Get available periods from all transaction sources.
     * Migrated from controller's getAvailablePeriods().
     */
    public function getAvailablePeriods(): array;

    /**
     * Get the most recent transaction date across all sources.
     * Migrated from controller's getLatestTransactionDate().
     */
    public function getLatestTransactionDate(): ?Carbon;
}
```

### 2. [`CashFlowService`](app/Modules/Finansial/Services/CashFlowService.php)

**Purpose:** Financial analytics - fully migrated from private methods.

```php
class CashFlowService
{
    /**
     * Get financial summary for dashboard cards.
     * Migrated from controller's getFinancialSummary().
     * Returns: liquid_assets, total_receivables, cash_inflow_this_month, etc.
     */
    public function getFinancialSummary(?Carbon $startDate = null, ?Carbon $endDate = null): array;

    /**
     * Get PSAK 2 compliant cash flow statement.
     * Migrated from controller's getCashFlowStatement().
     * Returns: operating_receipts, operating_payments, net_operating, etc.
     */
    public function getCashFlowStatement(?Carbon $startDate = null, ?Carbon $endDate = null): array;
}
```

### 3. [`MutationService`](app/Modules/Finansial/Services/MutationService.php)

**Purpose:** All transaction/mutation queries.

```php
class MutationService
{
    /**
     * Get recent transactions timeline (merged invoice payments, direct payments, expenses).
     * Migrated from controller's getRecentTransactions().
     */
    public function getRecentTransactions(int $limit = 15, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection;

    /**
     * Get account-specific mutations with running balance calculation.
     * Migrated from controller's getAccountMutations().
     * Includes BUG-01 fix (only assigned transactions).
     */
    public function getAccountMutations(CashAccount $cashAccount, Carbon $startDate, Carbon $endDate, string $transactionType = 'all'): Collection;

    /**
     * Get general (non-project) transactions.
     * Migrated from controller's getGeneralTransactions().
     */
    public function getGeneralTransactions(?Carbon $startDate = null, ?Carbon $endDate = null): array;

    /**
     * Count unassigned transactions for UI warning banner.
     * Migrated from BUG-01 unassigned tracking in controller's show().
     */
    public function getUnassignedCounts(Carbon $startDate, Carbon $endDate, string $transactionType = 'all'): array;
}
```

### 4. [`CashAccountService`](app/Modules/Finansial/Services/CashAccountService.php)

**Purpose:** CRUD operations and account-specific queries.

```php
class CashAccountService
{
    /**
     * Get all accounts ordered by type then name.
     */
    public function getAllOrdered(): Collection;

    /**
     * Get active accounts for API/payment forms.
     * Migrated from controller's getActiveCashAccounts().
     */
    public function getActiveAccounts(): Collection;

    /**
     * Store a new cash account with validation.
     * Migrated from controller's store().
     */
    public function store(array $data): CashAccount;

    /**
     * Update an existing cash account.
     * Migrated from controller's update().
     */
    public function update(CashAccount $account, array $data): bool;

    /**
     * Delete a cash account (with transaction protection).
     * Migrated from controller's destroy().
     */
    public function delete(CashAccount $account): bool;
}
```

## Refactored Controller

After decomposition, [`CashAccountController`](app/Modules/Finansial/Controllers/CashAccountController.php:1) becomes a thin orchestrator (~100 lines):

```php
class CashAccountController extends Controller
{
    public function __construct(
        private CashAccountService $cashAccount,
        private CashFlowService $cashFlow,
        private MutationService $mutation,
        private PeriodService $period,
    ) {}

    public function index(Request $request)
    {
        $dateRange = $this->period->resolveDateRange($request);
        $accounts  = $this->cashAccount->getAllOrdered();
        $summary   = $this->cashFlow->getFinancialSummary(...$dateRange);
        $statement = $this->cashFlow->getCashFlowStatement(...$dateRange);
        $transactions = $this->mutation->getRecentTransactions(50, ...$dateRange);
        $generalTransactions = $this->mutation->getGeneralTransactions(...$dateRange);
        $reconciliations = BankReconciliation::with('cashAccount')->latest()->paginate(20, ['*'], 'reconciliations_page')->withQueryString();

        return view('cash-accounts.index', compact('accounts', 'summary', 'statement', ...));
    }

    public function show(CashAccount $cashAccount, Request $request)
    {
        $dateRange = $this->period->resolveDateRange($request);
        $mutations = $this->mutation->getAccountMutations($cashAccount, ...$dateRange, $request->input('transaction_type', 'all'));
        $unassigned = $this->mutation->getUnassignedCounts(...$dateRange, $request->input('transaction_type', 'all'));

        return view('cash-accounts.show', compact('cashAccount', 'mutations', 'unassigned', ...));
    }

    public function store(Request $request) { $this->cashAccount->store($request->validated()); ... }
    public function update(Request $request, CashAccount $cashAccount) { $this->cashAccount->update($cashAccount, $request->validated()); ... }
    public function destroy(CashAccount $cashAccount) { $this->cashAccount->delete($cashAccount); ... }
    public function getActiveCashAccounts() { return $this->cashAccount->getActiveAccounts(); }
}
```

## Implementation Steps

### Step 1: Create [`PeriodService`](app/Modules/Finansial/Services/PeriodService.php)
- Migrate `getAvailablePeriods()` — exact copy
- Migrate `getLatestTransactionDate()` — exact copy
- Create new `resolveDateRange(Request): array` — consolidates the switch/case duplicated in `index()` + `show()`

### Step 2: Create [`CashFlowService`](app/Modules/Finansial/Services/CashFlowService.php)
- Migrate `getFinancialSummary()` — exact copy
- Migrate `getCashFlowStatement()` — exact copy
- No behavioral changes needed

### Step 3: Create [`MutationService`](app/Modules/Finansial/Services/MutationService.php)
- Migrate `getRecentTransactions()` — exact copy
- Migrate `getAccountMutations()` — exact copy (includes BUG-01 fix)
- Migrate `getGeneralTransactions()` — exact copy
- Extract unassigned tracking from `show()` into `getUnassignedCounts()`

### Step 4: Create [`CashAccountService`](app/Modules/Finansial/Services/CashAccountService.php)
- Migrate `store()` logic — extract validation to controller, keep creation
- Migrate `update()` logic
- Migrate `destroy()` logic with transaction check
- Migrate `getActiveCashAccounts()`

### Step 5: Refactor [`CashAccountController`](app/Modules/Finansial/Controllers/CashAccountController.php)
- Inject all 4 services via constructor
- Replace private method calls with service calls
- Remove all private helper methods
- Use `PeriodService::resolveDateRange()` in both `index()` and `show()`

### Step 6: Verify
- Run `php artisan route:list` — ensure no route changes
- Run `php artisan cache:clear && php artisan config:clear`
- Verify index page loads (financial summary, cash flow, recent transactions)
- Verify show page loads (mutations, running balance, unassigned warning)
- Verify CRUD (create, update, delete) still works

## Files to Create
1. [`app/Modules/Finansial/Services/PeriodService.php`](app/Modules/Finansial/Services/PeriodService.php) — NEW (80 lines)
2. [`app/Modules/Finansial/Services/CashFlowService.php`](app/Modules/Finansial/Services/CashFlowService.php) — NEW (200 lines)
3. [`app/Modules/Finansial/Services/MutationService.php`](app/Modules/Finansial/Services/MutationService.php) — NEW (230 lines)
4. [`app/Modules/Finansial/Services/CashAccountService.php`](app/Modules/Finansial/Services/CashAccountService.php) — NEW (80 lines)

## Files to Modify
5. [`app/Modules/Finansial/Controllers/CashAccountController.php`](app/Modules/Finansial/Controllers/CashAccountController.php:1) — REWRITE (~880 → ~100 lines)
6. [`app/Modules/Finansial/Providers/FinansialServiceProvider.php`](app/Modules/Finansial/Providers/FinansialServiceProvider.php:1) — No changes needed (auto-discovery in Laravel)

## Risk Mitigation

| Risk | Mitigation |
|------|------------|
| **Regression in index()** — wrong date range, missing data | Step-by-step migration: 1 service at a time, verify after each |
| **Regression in show()** — mutations, running balance | Unit-level migration: move one private method at a time |
| **Unassigned banner disappearing** | `getUnassignedCounts()` returns same structure; view expects `$unassignedInvoicePayments`, `$unassignedExpenses`, etc. |
| **Laravel auto-discovery fails** | Register services in `FinansialServiceProvider::register()` if needed |
