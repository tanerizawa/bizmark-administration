<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CashAccount;

class CashAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            [
                'account_name' => 'Bank BTN',
                'account_type' => 'bank',
                'account_number' => '123456789',
                'bank_name' => 'Bank Tabungan Negara',
                'initial_balance' => 46000000,
                'current_balance' => 46000000,
                'is_active' => true,
                'notes' => 'Rekening operasional utama',
            ],
            [
                'account_name' => 'Kas Tunai',
                'account_type' => 'cash',
                'account_number' => null,
                'bank_name' => null,
                'initial_balance' => 8000000,
                'current_balance' => 8000000,
                'is_active' => true,
                'notes' => 'Kas kecil untuk operasional harian',
            ],
            [
                'account_name' => 'Piutang Mr. Gobs',
                'account_type' => 'receivable',
                'account_number' => null,
                'bank_name' => null,
                'initial_balance' => 20000000,
                'current_balance' => 20000000,
                'is_active' => true,
                'notes' => 'Piutang dari Mr. Gobs (PT Asia Con project)',
            ],
        ];

        foreach ($accounts as $account) {
            CashAccount::create($account);
        }
    }
}

