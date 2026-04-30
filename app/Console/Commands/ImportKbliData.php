<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportKbliData extends Command
{
    protected $signature = 'kbli:import {--file=kbli_data.json : Path relatif di storage/app/} {--fresh : Hapus data lama sebelum import}';

    protected $description = 'Import data KBLI dari file JSON (storage/app/kbli_data.json) ke tabel kbli';

    // Mapping kategori huruf ke nama sektor
    private array $kategoriMap = [
        'A' => 'Pertanian, Kehutanan dan Perikanan',
        'B' => 'Pertambangan dan Penggalian',
        'C' => 'Industri Pengolahan',
        'D' => 'Pengadaan Listrik, Gas, Uap/Air Panas dan Udara Dingin',
        'E' => 'Pengelolaan Air, Pengelolaan Air Limbah, Pengelolaan dan Daur Ulang Sampah',
        'F' => 'Konstruksi',
        'G' => 'Perdagangan Besar dan Eceran',
        'H' => 'Pengangkutan dan Pergudangan',
        'I' => 'Penyediaan Akomodasi dan Penyediaan Makan Minum',
        'J' => 'Informasi dan Komunikasi',
        'K' => 'Aktivitas Keuangan dan Asuransi',
        'L' => 'Real Estat',
        'M' => 'Aktivitas Profesional, Ilmiah dan Teknis',
        'N' => 'Aktivitas Penyewaan dan Sewa Guna Usaha',
        'O' => 'Administrasi Pemerintahan, Pertahanan dan Jaminan Sosial Wajib',
        'P' => 'Pendidikan',
        'Q' => 'Aktivitas Kesehatan Manusia dan Aktivitas Sosial',
        'R' => 'Kesenian, Hiburan dan Rekreasi',
        'S' => 'Aktivitas Jasa Lainnya',
        'T' => 'Aktivitas Rumah Tangga',
        'U' => 'Aktivitas Badan Internasional',
    ];

    public function handle(): int
    {
        $file = $this->option('file');
        $path = storage_path("app/{$file}");

        if (! file_exists($path)) {
            $this->error("File tidak ditemukan: {$path}");

            return self::FAILURE;
        }

        $this->info("Membaca file: {$path}");
        $data = json_decode(file_get_contents($path), true);

        if (! $data || ! is_array($data)) {
            $this->error('Gagal parse JSON atau file kosong.');

            return self::FAILURE;
        }

        $this->info('Total data KBLI di JSON: '.count($data));

        if ($this->option('fresh')) {
            DB::table('kbli')->delete();
            $this->warn('Data lama di tabel kbli telah dihapus.');
        }

        $now = now();
        $inserted = 0;
        $skipped = 0;
        $chunks = array_chunk($data, 100);

        $bar = $this->output->createProgressBar(count($chunks));
        $bar->start();

        foreach ($chunks as $chunk) {
            $rows = [];
            foreach ($chunk as $item) {
                $code = trim($item['kode'] ?? '');
                if (empty($code)) {
                    $skipped++;

                    continue;
                }

                $kategori = strtoupper(trim($item['kategori'] ?? ''));
                $sectorName = $this->kategoriMap[$kategori] ?? null;

                $rows[] = [
                    'code' => $code,
                    'description' => trim($item['judul'] ?? ''),
                    'sector' => $kategori ?: null,   // varchar(1): huruf kategori A-U
                    'category' => $sectorName,         // varchar(255): nama sektor lengkap
                    'activities' => trim($item['uraian'] ?? '') ?: null,
                    'notes' => trim($item['keterangan'] ?? '') ?: null,
                    'is_active' => true,
                    'usage_count' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $inserted++;
            }

            if (! empty($rows)) {
                DB::table('kbli')->upsert($rows, ['code'], [
                    'description', 'sector', 'category', 'activities', 'notes', 'updated_at',
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Selesai! Diproses: {$inserted}, Dilewati: {$skipped}");
        $this->info('Total di DB: '.DB::table('kbli')->count());

        return self::SUCCESS;
    }
}
