<?php

namespace App\Console\Commands;

use App\Services\GoogleIndexingService;
use App\Services\SitemapGeneratorService;
use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate {--ping : Ping search engines after generation}';
    protected $description = 'Generate sitemap.xml and optionally ping search engines';

    public function handle(
        SitemapGeneratorService $sitemapGenerator,
        GoogleIndexingService $indexingService
    ): int {
        $this->info('🗺️  Generating sitemap...');
        
        try {
            $sitemapUrl = $sitemapGenerator->generate();
            
            $this->info("✅ Sitemap generated: {$sitemapUrl}");
            
            // Ping search engines if flag is set
            if ($this->option('ping')) {
                $this->newLine();
                $this->info('📡 Pinging search engines...');
                
                $results = $indexingService->pingSearchEngines($sitemapUrl);
                
                foreach ($results as $engine => $success) {
                    $icon = $success ? '✅' : '❌';
                    $status = $success ? 'Success' : 'Failed';
                    $this->line("   {$icon} {$engine}: {$status}");
                }
            }
            
            $this->newLine();
            $this->info('✨ Done!');
            
            return self::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to generate sitemap: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
