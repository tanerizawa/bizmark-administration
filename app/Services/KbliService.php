<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class KbliService
{
    /**
     * Sample KBLI data - In production, this should be loaded from database or external API
     * Data ini bisa di-update secara berkala dari sumber resmi OSS/BPS
     */
    private static function getKbliData(): array
    {
        return [
            // KATEGORI A - PERTANIAN, KEHUTANAN DAN PERIKANAN
            ['code' => '01111', 'description' => 'Pertanian Padi', 'category' => 'Rendah', 'sector' => 'A'],
            ['code' => '01112', 'description' => 'Pertanian Jagung', 'category' => 'Rendah', 'sector' => 'A'],
            ['code' => '01113', 'description' => 'Pertanian Kacang-Kacangan', 'category' => 'Rendah', 'sector' => 'A'],
            ['code' => '01131', 'description' => 'Pertanian Sayuran', 'category' => 'Rendah', 'sector' => 'A'],
            ['code' => '01132', 'description' => 'Pertanian Buah-Buahan', 'category' => 'Rendah', 'sector' => 'A'],
            
            // KATEGORI C - INDUSTRI PENGOLAHAN
            ['code' => '10101', 'description' => 'Industri Pengolahan dan Pengawetan Daging', 'category' => 'Menengah Tinggi', 'sector' => 'C'],
            ['code' => '10102', 'description' => 'Industri Pengolahan dan Pengawetan Ikan dan Biota Air', 'category' => 'Menengah Tinggi', 'sector' => 'C'],
            ['code' => '10103', 'description' => 'Industri Pengolahan dan Pengawetan Buah-Buahan dan Sayuran', 'category' => 'Menengah Rendah', 'sector' => 'C'],
            ['code' => '10301', 'description' => 'Industri Pengolahan Kentang', 'category' => 'Menengah Rendah', 'sector' => 'C'],
            ['code' => '10710', 'description' => 'Industri Roti dan Kue', 'category' => 'Menengah Rendah', 'sector' => 'C'],
            ['code' => '10720', 'description' => 'Industri Gula', 'category' => 'Menengah Tinggi', 'sector' => 'C'],
            ['code' => '10730', 'description' => 'Industri Kakao, Coklat dan Kembang Gula', 'category' => 'Menengah Rendah', 'sector' => 'C'],
            ['code' => '10740', 'description' => 'Industri Makaroni, Mie dan Produk Sejenisnya', 'category' => 'Menengah Rendah', 'sector' => 'C'],
            ['code' => '10750', 'description' => 'Industri Makanan dan Masakan Olahan', 'category' => 'Menengah Rendah', 'sector' => 'C'],
            ['code' => '10790', 'description' => 'Industri Makanan Lainnya', 'category' => 'Menengah Rendah', 'sector' => 'C'],
            ['code' => '11010', 'description' => 'Industri Minuman Keras', 'category' => 'Tinggi', 'sector' => 'C'],
            ['code' => '11020', 'description' => 'Industri Minuman Mengandung Alkohol Tidak Suling', 'category' => 'Tinggi', 'sector' => 'C'],
            ['code' => '11030', 'description' => 'Industri Minuman Tidak Beralkohol', 'category' => 'Menengah Rendah', 'sector' => 'C'],
            
            // KATEGORI G - PERDAGANGAN
            ['code' => '45100', 'description' => 'Perdagangan Mobil', 'category' => 'Rendah', 'sector' => 'G'],
            ['code' => '46311', 'description' => 'Perdagangan Besar Beras dan Palawija', 'category' => 'Rendah', 'sector' => 'G'],
            ['code' => '46312', 'description' => 'Perdagangan Besar Buah-Buahan dan Sayuran Segar', 'category' => 'Rendah', 'sector' => 'G'],
            ['code' => '46321', 'description' => 'Perdagangan Besar Daging dan Produknya', 'category' => 'Menengah Rendah', 'sector' => 'G'],
            ['code' => '46322', 'description' => 'Perdagangan Besar Ikan dan Produknya', 'category' => 'Menengah Rendah', 'sector' => 'G'],
            ['code' => '47111', 'description' => 'Perdagangan Eceran Berbagai Macam Barang di Toserba', 'category' => 'Rendah', 'sector' => 'G'],
            ['code' => '47211', 'description' => 'Perdagangan Eceran Beras dan Palawija', 'category' => 'Rendah', 'sector' => 'G'],
            ['code' => '47221', 'description' => 'Perdagangan Eceran Daging dan Produk Daging', 'category' => 'Rendah', 'sector' => 'G'],
            ['code' => '47222', 'description' => 'Perdagangan Eceran Ikan dan Produk Ikan', 'category' => 'Rendah', 'sector' => 'G'],
            ['code' => '47230', 'description' => 'Perdagangan Eceran Buah-Buahan dan Sayuran Segar', 'category' => 'Rendah', 'sector' => 'G'],
            
            // KATEGORI I - PENYEDIAAN AKOMODASI DAN PENYEDIAAN MAKAN MINUM
            ['code' => '55101', 'description' => 'Hotel Bintang 5', 'category' => 'Menengah Tinggi', 'sector' => 'I'],
            ['code' => '55102', 'description' => 'Hotel Bintang 4', 'category' => 'Menengah Tinggi', 'sector' => 'I'],
            ['code' => '55103', 'description' => 'Hotel Bintang 3', 'category' => 'Menengah Rendah', 'sector' => 'I'],
            ['code' => '55104', 'description' => 'Hotel Bintang 1 dan 2', 'category' => 'Menengah Rendah', 'sector' => 'I'],
            ['code' => '55105', 'description' => 'Hotel Melati', 'category' => 'Rendah', 'sector' => 'I'],
            ['code' => '56101', 'description' => 'Restoran', 'category' => 'Menengah Rendah', 'sector' => 'I'],
            ['code' => '56102', 'description' => 'Katering', 'category' => 'Menengah Rendah', 'sector' => 'I'],
            ['code' => '56301', 'description' => 'Kedai Kopi', 'category' => 'Rendah', 'sector' => 'I'],
            
            // KATEGORI J - INFORMASI DAN KOMUNIKASI
            ['code' => '58110', 'description' => 'Penerbitan Buku', 'category' => 'Rendah', 'sector' => 'J'],
            ['code' => '58120', 'description' => 'Penerbitan Direktori dan Mailing List', 'category' => 'Rendah', 'sector' => 'J'],
            ['code' => '58130', 'description' => 'Penerbitan Surat Kabar, Jurnal dan Majalah', 'category' => 'Menengah Rendah', 'sector' => 'J'],
            ['code' => '62010', 'description' => 'Aktivitas Pemrograman Komputer', 'category' => 'Rendah', 'sector' => 'J'],
            ['code' => '62020', 'description' => 'Aktivitas Konsultasi Komputer', 'category' => 'Rendah', 'sector' => 'J'],
            ['code' => '62090', 'description' => 'Aktivitas Teknologi Informasi dan Jasa Komputer Lainnya', 'category' => 'Rendah', 'sector' => 'J'],
            ['code' => '63111', 'description' => 'Aktivitas Pengolahan Data', 'category' => 'Rendah', 'sector' => 'J'],
            ['code' => '63112', 'description' => 'Aktivitas Web Portal', 'category' => 'Rendah', 'sector' => 'J'],
            
            // KATEGORI M - AKTIVITAS PROFESIONAL, ILMIAH DAN TEKNIS
            ['code' => '69100', 'description' => 'Aktivitas Hukum', 'category' => 'Rendah', 'sector' => 'M'],
            ['code' => '69200', 'description' => 'Aktivitas Akuntansi, Pembukuan dan Audit', 'category' => 'Rendah', 'sector' => 'M'],
            ['code' => '70100', 'description' => 'Aktivitas Kantor Pusat', 'category' => 'Rendah', 'sector' => 'M'],
            ['code' => '70200', 'description' => 'Aktivitas Konsultasi Manajemen', 'category' => 'Rendah', 'sector' => 'M'],
            ['code' => '71101', 'description' => 'Aktivitas Arsitektur', 'category' => 'Menengah Rendah', 'sector' => 'M'],
            ['code' => '71102', 'description' => 'Aktivitas Keinsinyuran dan Konsultasi Teknis', 'category' => 'Menengah Rendah', 'sector' => 'M'],
            ['code' => '73100', 'description' => 'Aktivitas Periklanan', 'category' => 'Rendah', 'sector' => 'M'],
            ['code' => '73200', 'description' => 'Riset Pasar dan Jajak Pendapat', 'category' => 'Rendah', 'sector' => 'M'],
            
            // KATEGORI S - AKTIVITAS JASA LAINNYA
            ['code' => '96021', 'description' => 'Salon Kecantikan', 'category' => 'Rendah', 'sector' => 'S'],
            ['code' => '96022', 'description' => 'Pangkas Rambut', 'category' => 'Rendah', 'sector' => 'S'],
            ['code' => '96030', 'description' => 'Aktivitas Pemakaman dan Pekuburan', 'category' => 'Rendah', 'sector' => 'S'],
        ];
    }
    
    /**
     * Search KBLI by keyword
     */
    public function search(string $keyword): array
    {
        $keyword = strtolower($keyword);
        $data = self::getKbliData();
        
        return array_filter($data, function($item) use ($keyword) {
            return str_contains(strtolower($item['code']), $keyword) ||
                   str_contains(strtolower($item['description']), $keyword);
        });
    }
    
    /**
     * Get KBLI by code
     */
    public function getByCode(string $code): ?array
    {
        $data = self::getKbliData();
        
        foreach ($data as $item) {
            if ($item['code'] === $code) {
                return $item;
            }
        }
        
        return null;
    }
    
    /**
     * Get all KBLI data
     */
    public function getAll(): array
    {
        return self::getKbliData();
    }
    
    /**
     * Get KBLI by category
     */
    public function getByCategory(string $category): array
    {
        $data = self::getKbliData();
        
        return array_filter($data, function($item) use ($category) {
            return $item['category'] === $category;
        });
    }
    
    /**
     * Get KBLI by sector
     */
    public function getBySector(string $sector): array
    {
        $data = self::getKbliData();
        
        return array_filter($data, function($item) use ($sector) {
            return $item['sector'] === $sector;
        });
    }
}
