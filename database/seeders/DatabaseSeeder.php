<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            ProjectStatusSeeder::class,
            InstitutionSeeder::class,
            BusinessSettingSeeder::class,
            ExpenseCategorySeeder::class,
            PaymentMethodSeeder::class,
            TaxRateSeeder::class,
            SecuritySettingSeeder::class,
            UserSeeder::class,
            ProjectSeeder::class,
            TaskSeeder::class,
            DocumentSeeder::class,
        ]);
    }
}
