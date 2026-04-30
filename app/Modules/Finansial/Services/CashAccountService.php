<?php

namespace App\Modules\Finansial\Services;

use App\Models\CashAccount;
use Illuminate\Database\Eloquent\Collection;

/**
 * CashAccountService — CRUD operations and account queries.
 *
 * Migrated from CashAccountController:
 * - store()                    → store()
 * - update()                   → update()
 * - destroy()                  → delete()
 * - getActiveCashAccounts()    → getActiveAccounts()
 * - Index account listing      → getAllOrdered()
 */
class CashAccountService
{
    /**
     * Get all accounts ordered by type then name.
     */
    public function getAllOrdered(): Collection
    {
        return CashAccount::orderBy('account_type')->orderBy('account_name')->get();
    }

    /**
     * Get active accounts for API (used in payment forms).
     *
     * Migrated from CashAccountController::getActiveCashAccounts().
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveAccounts()
    {
        $accounts = CashAccount::active()
            ->orderBy('account_type')
            ->orderBy('account_name')
            ->get(['id', 'account_name', 'account_type', 'account_number', 'bank_name', 'current_balance', 'is_active']);

        return response()->json($accounts);
    }

    /**
     * Store a new cash account.
     *
     * Migrated from CashAccountController::store().
     * Validation is handled by the controller via FormRequest or $request->validate().
     */
    public function store(array $data): CashAccount
    {
        $data['current_balance'] = $data['initial_balance'];
        $data['is_active'] = true;

        return CashAccount::create($data);
    }

    /**
     * Update an existing cash account.
     *
     * Migrated from CashAccountController::update().
     * Validation handled by the controller.
     */
    public function update(CashAccount $account, array $data): bool
    {
        return $account->update($data);
    }

    /**
     * Delete a cash account with transaction protection.
     *
     * Migrated from CashAccountController::destroy().
     * Prevents deletion if account has associated transactions.
     *
     * @return array{success: bool, message: string}
     */
    public function delete(CashAccount $account): array
    {
        if ($account->payments()->count() > 0 || $account->expenses()->count() > 0) {
            return [
                'success' => false,
                'message' => 'Akun kas tidak dapat dihapus karena masih memiliki transaksi',
            ];
        }

        $account->delete();

        return [
            'success' => true,
            'message' => 'Akun kas berhasil dihapus',
        ];
    }
}
