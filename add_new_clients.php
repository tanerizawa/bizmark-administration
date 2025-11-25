<?php

/**
 * Script untuk menambahkan 5 klien baru
 * Tanggal: 22 November 2025
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Client;
use Illuminate\Support\Facades\Hash;

echo "🚀 Menambahkan 5 Klien Baru...\n\n";

$clients = [
    [
        'name' => 'PT Rindu Alam Sejahtera',
        'company_name' => 'PT Rindu Alam Sejahtera',
        'industry' => 'Pengumpulan Limbah B3, Transporter Limbah B3',
        'contact_person' => 'Mukmin Waskito',
        'email' => 'mukmin.waskito@rindualam.com', // Email generated (bisa diubah)
        'phone' => '+62 811-158-825',
        'mobile' => '081115882500',
        'address' => 'Jl. Babakan Tengah, Puseurjaya, Telukjambe Timur',
        'city' => 'Karawang',
        'province' => 'Jawa Barat',
        'postal_code' => '41361',
        'client_type' => 'company',
        'status' => 'active',
        'notes' => 'Kategori: Pengumpulan Limbah B3, Transporter Limbah B3. Manager: Mukmin Waskito',
    ],
    [
        'name' => 'PT Asiacon Cipta Prima',
        'company_name' => 'PT Asiacon Cipta Prima',
        'industry' => 'Industri Pembuatan Paving Block, U-Ditch dan Berbagai Bahan Konstruksi dari Semen',
        'contact_person' => 'Eddy',
        'email' => 'eddy@asiacon.co.id', // Email generated (bisa diubah)
        'phone' => '+62 813-8304-0004',
        'mobile' => '081383040004',
        'address' => 'Jln. Proklamasi, Gg. Puskesmas Tunggakjati, Kel. Tunggakjati, Kec. Karawang Barat',
        'city' => 'Karawang',
        'province' => 'Jawa Barat',
        'postal_code' => null,
        'client_type' => 'company',
        'status' => 'active',
        'notes' => 'Direktur: Eddy. Industri: Paving Block, U-Ditch, Bahan Konstruksi Semen',
    ],
    [
        'name' => 'PT Putra Jaya Laksana Utama',
        'company_name' => 'PT Putra Jaya Laksana Utama',
        'industry' => null, // Tidak disebutkan
        'contact_person' => 'Saiful Bahri',
        'email' => 'pt.putrajayalaksanautama@gmail.com',
        'phone' => '081293511222',
        'mobile' => '081293511222',
        'address' => 'Dusun Cariu Barat Desa Pangulah Utara Kec. Kota Baru',
        'city' => 'Karawang',
        'province' => 'Jawa Barat',
        'postal_code' => null,
        'client_type' => 'company',
        'status' => 'active',
        'notes' => 'Penanggung Jawab: Saiful Bahri',
    ],
    [
        'name' => 'PT Maulida Indo Property',
        'company_name' => 'PT Maulida Indo Property',
        'industry' => 'Property/Real Estate', // Asumsi dari nama perusahaan
        'contact_person' => 'Ahmad Rian Hendra Purnama',
        'email' => 'maulidaproper23@gmail.com',
        'phone' => '081384226679',
        'mobile' => '081384226679',
        'address' => 'Desa Pangulah Selatan, Kecamatan Kotabaru',
        'city' => 'Karawang',
        'province' => 'Jawa Barat',
        'postal_code' => null,
        'client_type' => 'company',
        'status' => 'active',
        'notes' => 'Penanggung Jawab: Ahmad Rian Hendra Purnama',
    ],
    [
        'name' => 'PT Mega Corporindo Mandiri',
        'company_name' => 'PT Mega Corporindo Mandiri',
        'industry' => 'Pengumpulan Limbah B3, Transporter Limbah B3',
        'contact_person' => 'Fahmi',
        'email' => 'fahmi@megacorporindo.com', // Email generated (bisa diubah)
        'phone' => '081319095178',
        'mobile' => '081319095178',
        'address' => 'Jalan Desa Wadas, Kampung Sindang Sari Desa Nomor 34, Desa/Kelurahan Wadas, Kec. Telukjambe Timur',
        'city' => 'Karawang',
        'province' => 'Jawa Barat',
        'postal_code' => '41360',
        'client_type' => 'company',
        'status' => 'active',
        'notes' => 'Contact: Fahmi. Kategori: Pengumpulan Limbah B3, Transporter Limbah B3',
    ],
];

$added = 0;
$skipped = 0;

foreach ($clients as $index => $clientData) {
    try {
        $num = $index + 1;
        
        // Check if client already exists by company name
        $existing = Client::where('company_name', $clientData['company_name'])->first();
        
        if ($existing) {
            echo "⚠️  Klien #" . $num . " - {$clientData['company_name']} sudah ada (ID: {$existing->id})\n";
            $skipped++;
            continue;
        }
        
        // Create client
        $client = Client::create($clientData);
        
        echo "✅ Klien #" . $num . " - {$clientData['company_name']} berhasil ditambahkan (ID: {$client->id})\n";
        echo "   📧 Email: {$clientData['email']}\n";
        echo "   📞 Phone: {$clientData['phone']}\n";
        echo "   👤 Contact: {$clientData['contact_person']}\n";
        echo "   🏢 Industry: " . ($clientData['industry'] ?? 'N/A') . "\n";
        echo "   📍 Lokasi: {$clientData['city']}, {$clientData['province']}\n\n";
        
        $added++;
        
    } catch (\Exception $e) {
        echo "❌ Error menambahkan {$clientData['company_name']}: {$e->getMessage()}\n\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 SUMMARY:\n";
echo "✅ Berhasil ditambahkan: {$added} klien\n";
echo "⚠️  Dilewati (sudah ada): {$skipped} klien\n";
echo "📝 Total dalam database: " . Client::count() . " klien\n";
echo str_repeat("=", 60) . "\n\n";

echo "✨ Selesai!\n";
echo "💡 Catatan: Email yang di-generate otomatis bisa diubah melalui halaman edit klien di admin panel.\n";
