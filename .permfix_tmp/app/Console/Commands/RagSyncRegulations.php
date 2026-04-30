<?php

namespace App\Console\Commands;

use App\Services\PerizinanAIService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RagSyncRegulations extends Command
{
    protected $signature = 'rag:sync-regulations {--force : Force sync ignoring cache}';
    protected $description = 'Sync regulation data from RAG API and warm caches';

    private const BUSINESS_TYPES = ['PT', 'CV', 'UMKM', 'Koperasi', 'Yayasan'];
    private const LOCATIONS = ['Jakarta', 'Bandung', 'Surabaya', 'Semarang', 'Bali'];
    private const LOCATION_TYPES = ['komersial', 'industri', 'perumahan', 'campuran'];

    public function handle(): int
    {
        $this->info('🔄 Starting RAG regulation sync...');

        try {
            $service = app(PerizinanAIService::class);

            if (!$service->testConnection()) {
                $this->error('❌ Cannot connect to RAG API');
                Log::error('rag:sync-regulations - API connection failed');
                return self::FAILURE;
            }

            $force = $this->option('force');
            $synced = 0;
            $errors = 0;

            // Sync business type regulations
            foreach (self::BUSINESS_TYPES as $type) {
                foreach (self::LOCATIONS as $location) {
                    try {
                        if ($force) {
                            $cacheKey = "rag_business_{$type}_{$location}";
                            Cache::forget($cacheKey);
                        }
                        $service->getBusinessTypeRegulations($type, $location);
                        $synced++;
                    } catch (\Exception $e) {
                        $errors++;
                        Log::warning("rag:sync-regulations - Failed for {$type}/{$location}: {$e->getMessage()}");
                    }
                }
            }

            // Sync location type requirements
            foreach (self::LOCATION_TYPES as $locType) {
                foreach ([10, 50, 100] as $employees) {
                    try {
                        if ($force) {
                            $cacheKey = "rag_location_{$locType}_{$employees}";
                            Cache::forget($cacheKey);
                        }
                        $service->getLocationRequirements($locType, $employees);
                        $synced++;
                    } catch (\Exception $e) {
                        $errors++;
                        Log::warning("rag:sync-regulations - Failed for {$locType}/{$employees}: {$e->getMessage()}");
                    }
                }
            }

            $this->info("✅ RAG sync complete: {$synced} cached, {$errors} errors");
            Log::info("rag:sync-regulations completed", compact('synced', 'errors'));

            return $errors > 0 ? self::FAILURE : self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Sync failed: {$e->getMessage()}");
            Log::error('rag:sync-regulations - Fatal error', ['error' => $e->getMessage()]);
            return self::FAILURE;
        }
    }
}
