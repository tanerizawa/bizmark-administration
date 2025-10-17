<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            [
                'code' => 'bank_transfer',
                'name' => 'Transfer Bank',
                'description' => 'Pembayaran melalui transfer antar bank.',
                'requires_cash_account' => true,
                'is_default' => true,
                'sort_order' => 10,
            ],
            [
                'code' => 'cash',
                'name' => 'Kas Tunai',
                'description' => 'Pembayaran langsung menggunakan uang tunai.',
                'requires_cash_account' => true,
                'sort_order' => 20,
            ],
            [
                'code' => 'check',
                'name' => 'Cek',
                'description' => 'Pembayaran menggunakan cek.',
                'requires_cash_account' => true,
                'sort_order' => 30,
            ],
            [
                'code' => 'giro',
                'name' => 'Giro',
                'description' => 'Pembayaran menggunakan giro/bilyet.',
                'requires_cash_account' => true,
                'sort_order' => 40,
            ],
            [
                'code' => 'other',
                'name' => 'Metode Lainnya',
                'description' => 'Metode pembayaran khusus sesuai kesepakatan.',
                'requires_cash_account' => false,
                'sort_order' => 100,
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['code' => $method['code']],
                array_merge($method, ['is_active' => true])
            );
        }
    }
}
