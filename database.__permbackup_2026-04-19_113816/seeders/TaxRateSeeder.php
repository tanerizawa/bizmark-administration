<?php

namespace Database\Seeders;

use App\Models\TaxRate;
use Illuminate\Database\Seeder;

class TaxRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rates = [
            [
                'name' => 'Tanpa Pajak',
                'rate' => 0,
                'description' => 'Digunakan untuk transaksi tanpa pajak.',
                'is_default' => false,
                'sort_order' => 0,
            ],
            [
                'name' => 'PPN 11%',
                'rate' => 11,
                'description' => 'Tarif PPN nasional 11%.',
                'is_default' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'PPH 23 2%',
                'rate' => 2,
                'description' => 'Potongan PPH 23 sebesar 2%.',
                'is_default' => false,
                'sort_order' => 20,
            ],
        ];

        foreach ($rates as $rate) {
            TaxRate::updateOrCreate(
                ['name' => $rate['name']],
                array_merge($rate, ['is_active' => true])
            );
        }
    }
}
