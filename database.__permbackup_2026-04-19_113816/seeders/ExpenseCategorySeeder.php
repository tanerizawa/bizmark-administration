<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // SDM & Personel
            ['slug' => 'personnel', 'name' => 'Gaji & Honor', 'icon' => 'briefcase', 'group' => 'SDM & Personel', 'sort_order' => 10],
            ['slug' => 'commission', 'name' => 'Komisi', 'icon' => 'handshake', 'group' => 'SDM & Personel', 'sort_order' => 20],
            ['slug' => 'allowance', 'name' => 'Tunjangan & Bonus', 'icon' => 'money-bill-wave', 'group' => 'SDM & Personel', 'sort_order' => 30],

            // Rekanan & Subkontraktor
            ['slug' => 'subcontractor', 'name' => 'Subkontraktor', 'icon' => 'hard-hat', 'group' => 'Rekanan & Subkontraktor', 'sort_order' => 40],
            ['slug' => 'consultant', 'name' => 'Konsultan Eksternal', 'icon' => 'user-tie', 'group' => 'Rekanan & Subkontraktor', 'sort_order' => 50],
            ['slug' => 'supplier', 'name' => 'Rekanan/Partner', 'icon' => 'handshake', 'group' => 'Rekanan & Subkontraktor', 'sort_order' => 60],

            // Layanan Teknis
            ['slug' => 'laboratory', 'name' => 'Laboratorium', 'icon' => 'microscope', 'group' => 'Layanan Teknis', 'sort_order' => 70],
            ['slug' => 'survey', 'name' => 'Survey & Pengukuran', 'icon' => 'ruler-combined', 'group' => 'Layanan Teknis', 'sort_order' => 80],
            ['slug' => 'testing', 'name' => 'Testing & Inspeksi', 'icon' => 'vial', 'group' => 'Layanan Teknis', 'sort_order' => 90],
            ['slug' => 'certification', 'name' => 'Sertifikasi', 'icon' => 'certificate', 'group' => 'Layanan Teknis', 'sort_order' => 100],

            // Peralatan & Perlengkapan
            ['slug' => 'equipment_rental', 'name' => 'Sewa Alat', 'icon' => 'truck-moving', 'group' => 'Peralatan & Perlengkapan', 'sort_order' => 110],
            ['slug' => 'equipment_purchase', 'name' => 'Pembelian Alat', 'icon' => 'tools', 'group' => 'Peralatan & Perlengkapan', 'sort_order' => 120],
            ['slug' => 'materials', 'name' => 'Perlengkapan & Supplies', 'icon' => 'box', 'group' => 'Peralatan & Perlengkapan', 'sort_order' => 130],
            ['slug' => 'maintenance', 'name' => 'Maintenance & Perbaikan', 'icon' => 'wrench', 'group' => 'Peralatan & Perlengkapan', 'sort_order' => 140],

            // Operasional
            ['slug' => 'travel', 'name' => 'Perjalanan Dinas', 'icon' => 'plane', 'group' => 'Operasional', 'sort_order' => 150],
            ['slug' => 'accommodation', 'name' => 'Akomodasi', 'icon' => 'hotel', 'group' => 'Operasional', 'sort_order' => 160],
            ['slug' => 'transportation', 'name' => 'Transportasi', 'icon' => 'car', 'group' => 'Operasional', 'sort_order' => 170],
            ['slug' => 'communication', 'name' => 'Komunikasi & Internet', 'icon' => 'phone', 'group' => 'Operasional', 'sort_order' => 180],
            ['slug' => 'office_supplies', 'name' => 'ATK & Supplies', 'icon' => 'file-alt', 'group' => 'Operasional', 'sort_order' => 190],
            ['slug' => 'printing', 'name' => 'Printing & Dokumen', 'icon' => 'print', 'group' => 'Operasional', 'sort_order' => 200],

            // Legal & Administrasi
            ['slug' => 'permit', 'name' => 'Perizinan', 'icon' => 'file-contract', 'group' => 'Legal & Administrasi', 'sort_order' => 210],
            ['slug' => 'insurance', 'name' => 'Asuransi', 'icon' => 'shield-alt', 'group' => 'Legal & Administrasi', 'sort_order' => 220],
            ['slug' => 'tax', 'name' => 'Pajak & Retribusi', 'icon' => 'dollar-sign', 'group' => 'Legal & Administrasi', 'sort_order' => 230],
            ['slug' => 'legal', 'name' => 'Legal & Notaris', 'icon' => 'balance-scale', 'group' => 'Legal & Administrasi', 'sort_order' => 240],
            ['slug' => 'administration', 'name' => 'Administrasi', 'icon' => 'clipboard-list', 'group' => 'Legal & Administrasi', 'sort_order' => 250],

            // Marketing & Lainnya
            ['slug' => 'marketing', 'name' => 'Marketing & Promosi', 'icon' => 'bullhorn', 'group' => 'Marketing & Lainnya', 'sort_order' => 260],
            ['slug' => 'entertainment', 'name' => 'Entertainment & Jamuan', 'icon' => 'utensils', 'group' => 'Marketing & Lainnya', 'sort_order' => 270],
            ['slug' => 'donation', 'name' => 'Donasi & CSR', 'icon' => 'gift', 'group' => 'Marketing & Lainnya', 'sort_order' => 280],
            ['slug' => 'other', 'name' => 'Lainnya', 'icon' => 'ellipsis-h', 'group' => 'Marketing & Lainnya', 'sort_order' => 999],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::updateOrCreate(
                ['slug' => $category['slug']],
                array_merge($category, ['is_default' => true])
            );
        }
    }
}
