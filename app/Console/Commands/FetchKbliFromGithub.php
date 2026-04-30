<?php

namespace App\Console\Commands;

use App\Models\Kbli;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchKbliFromGithub extends Command
{
    /**
     * Default GitHub raw URL placeholder untuk KBLI data
     * User harus set URL yang valid atau gunakan local file
     * Contoh valid sources:
     * - https://raw.githubusercontent.com/username/repo/main/kbli.json
     * - https://raw.githubusercontent.com/username/repo/main/kbli.csv
     */
    protected const DEFAULT_GITHUB_URL = null; // User must provide URL

    protected $signature = 'kbli:fetch-github
                            {--url= : URL GitHub raw JSON file}
                            {--fresh : Hapus data lama sebelum import}
                            {--sync : Update existing tanpa hapus data custom}
                            {--dry-run : Simulasi tanpa menyimpan ke DB}';

    protected $description = 'Fetch dan import data KBLI dari GitHub source (default: kbli-2020-indonesia)';

    // Mapping kategori huruf ke nama sektor lengkap
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
        $url = $this->option('url') ?: self::DEFAULT_GITHUB_URL;

        if (empty($url)) {
            $this->error("❌ URL is required!");
            $this->info("   Usage: php artisan kbli:fetch-github --url=https://example.com/kbli.json");
            $this->info("   Or set default URL in the command constant.");

            return self::FAILURE;
        }
        $isDryRun = $this->option('dry-run');
        $isFresh = $this->option('fresh');
        $isSync = $this->option('sync');

        if ($isSync && $isFresh) {
            $this->warn("⚠️  --sync dan --fresh tidak bisa digunakan bersamaan. Menggunakan --sync.");
            $isFresh = false;
        }

        $this->info("🔄 Fetching KBLI data from GitHub...");
        $this->info("   URL: {$url}");

        if ($isDryRun) {
            $this->warn("   Mode: DRY RUN (tidak akan menyimpan ke database)");
        }

        try {
            // Fetch data dari GitHub
            $response = Http::timeout(30)
                ->withOptions([
                    'verify' => false, // Disable SSL verification untuk development
                ])
                ->get($url);

            if (! $response->successful()) {
                $this->error("❌ Failed to fetch data: HTTP {$response->status()}");

                return self::FAILURE;
            }

            $data = $response->json();

            if (empty($data) || ! is_array($data)) {
                $this->error("❌ Invalid JSON data or empty response");

                return self::FAILURE;
            }

            $this->info("✅ Successfully fetched " . count($data) . " KBLI entries");

            // Proses data
            $processed = $this->processKbliData($data, $isDryRun, $isFresh, $isSync);

            // Summary
            $this->newLine();
            $this->info("📊 Summary:");
            $this->info("   - Total fetched: " . count($data));
            $this->info("   - Successfully processed: {$processed['success']}");
            if ($processed['updated'] > 0) {
                $this->info("   - Updated (sync): {$processed['updated']}");
            }
            if ($processed['created'] > 0) {
                $this->info("   - Created: {$processed['created']}");
            }
            $this->info("   - Skipped/Failed: {$processed['failed']}");
            $this->info("   - Database total: " . Kbli::count());

            if ($isDryRun) {
                $this->warn("\n⚠️  This was a dry run. No data was saved to database.");
                $this->info("   Run without --dry-run to save data.");
            }

            return self::SUCCESS;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $this->error("❌ Connection failed: {$e->getMessage()}");
            $this->warn("   Tips: Periksa koneksi internet atau coba URL alternatif");

            return self::FAILURE;

        } catch (\Exception $e) {
            $this->error("❌ Error: {$e->getMessage()}");
            Log::error('KBLI GitHub fetch failed', [
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }

    /**
     * Process and save KBLI data
     */
    private function processKbliData(array $data, bool $isDryRun, bool $isFresh, bool $isSync): array
    {
        $success = 0;
        $failed = 0;
        $updated = 0;
        $created = 0;

        if ($isFresh && ! $isDryRun) {
            $this->warn("🗑️  Clearing existing KBLI data...");
            Kbli::query()->delete();
            $this->info("   Existing data cleared.");
        }

        if ($isSync && ! $isDryRun) {
            $this->info("🔄 Sync mode: Updating existing records, preserving custom data...");
        }

        $bar = $this->output->createProgressBar(count($data));
        $bar->start();

        $now = now();

        foreach ($data as $item) {
            try {
                // Normalize data structure (support multiple formats)
                $code = $this->extractCode($item);
                $description = $this->extractDescription($item);
                $category = $this->extractCategory($item);

                // Skip invalid entries
                if (empty($code) || empty($description)) {
                    $failed++;
                    $bar->advance();
                    continue;
                }

                if (! $isDryRun) {
                    $existing = Kbli::where('code', $code)->first();

                    if ($isSync && $existing) {
                        // SYNC MODE: Only update basic fields, preserve custom data
                        $updateData = [
                            'description' => $description,
                            'sector' => $category,
                            'sub_sector' => $sectorName,
                            'category' => $sectorName,
                            'is_active' => true,
                            'updated_at' => $now,
                        ];

                        // Only update activities/notes if they were empty
                        if (empty($existing->activities) && ! empty($item['uraian'])) {
                            $updateData['activities'] = $item['uraian'];
                        }
                        if (empty($existing->notes) && ! empty($item['keterangan'])) {
                            $updateData['notes'] = $item['keterangan'];
                        }

                        $existing->update($updateData);
                        $updated++;
                    } else {
                        // FRESH or NEW RECORD: Full data
                        $complexity = $this->determineComplexity($code);

                        $kbliData = [
                            'code' => $code,
                            'description' => $description,
                            'sector' => $category,
                            'sub_sector' => $sectorName,
                            'category' => $sectorName,
                            'complexity_level' => $existing?->complexity_level ?? $complexity,
                            'is_active' => true,
                            'usage_count' => $existing?->usage_count ?? 0,
                            'updated_at' => $now,
                        ];

                        if (! empty($item['uraian'])) {
                            $kbliData['activities'] = $item['uraian'];
                        }
                        if (! empty($item['keterangan'])) {
                            $kbliData['notes'] = $item['keterangan'];
                        }

                        Kbli::updateOrCreate(['code' => $code], $kbliData);
                        $created++;
                    }
                }

                $success++;

            } catch (\Exception $e) {
                $failed++;
                Log::warning('KBLI processing failed for item', [
                    'item' => $item,
                    'error' => $e->getMessage(),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        return [
            'success' => $success,
            'failed' => $failed,
            'updated' => $updated,
            'created' => $created,
        ];
    }

    /**
     * Extract code from various data formats
     */
    private function extractCode(array $item): string
    {
        // Try different possible field names
        $possibleFields = ['kode', 'code', 'kbli_code', 'id', 'Kode'];

        foreach ($possibleFields as $field) {
            if (! empty($item[$field])) {
                return str_pad(trim($item[$field]), 5, '0', STR_PAD_LEFT);
            }
        }

        return '';
    }

    /**
     * Extract description from various data formats
     */
    private function extractDescription(array $item): string
    {
        $possibleFields = ['judul', 'description', 'nama', 'title', 'Uraian', 'uraian'];

        foreach ($possibleFields as $field) {
            if (! empty($item[$field])) {
                return trim($item[$field]);
            }
        }

        return '';
    }

    /**
     * Extract category from various data formats
     */
    private function extractCategory(array $item): string
    {
        $possibleFields = ['kategori', 'category', 'sector', 'Kategori'];

        foreach ($possibleFields as $field) {
            if (! empty($item[$field])) {
                return strtoupper(trim($item[$field]));
            }
        }

        // Derive from code (first character if it's a letter)
        $code = $this->extractCode($item);
        if (! empty($code) && ctype_alpha($code[0])) {
            return strtoupper($code[0]);
        }

        return 'Z'; // Default category
    }

    /**
     * Determine complexity level based on KBLI code
     */
    private function determineComplexity(string $code): string
    {
        // High complexity sectors
        $highComplexityPrefixes = ['B', '08', '09', '19', '24', '35', '38'];

        // Medium complexity sectors
        $mediumComplexityPrefixes = ['C', '10', '20', '30', '41', '47', '55', '86'];

        foreach ($highComplexityPrefixes as $prefix) {
            if (str_starts_with($code, $prefix)) {
                return 'high';
            }
        }

        foreach ($mediumComplexityPrefixes as $prefix) {
            if (str_starts_with($code, $prefix)) {
                return 'medium';
            }
        }

        return 'low';
    }
}
