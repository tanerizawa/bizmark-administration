<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ValidateSitemap extends Command
{
    protected $signature = 'sitemap:validate';

    protected $description = 'Validate sitemap.xml structure and content';

    public function handle(): int
    {
        $this->info('🔍 Validating sitemap...');
        $this->newLine();

        $sitemapPath = public_path('sitemap.xml');

        // Check if file exists
        if (! File::exists($sitemapPath)) {
            $this->error('❌ Sitemap file not found!');
            $this->line('   Run: php artisan sitemap:generate');

            return 1;
        }

        $this->line('✅ File exists: '.$sitemapPath);
        $this->line('   Size: '.$this->formatBytes(File::size($sitemapPath)));
        $this->newLine();

        // Load and validate XML
        libxml_use_internal_errors(true);
        $xml = simplexml_load_file($sitemapPath);

        if ($xml === false) {
            $this->error('❌ Invalid XML format!');
            foreach (libxml_get_errors() as $error) {
                $this->line('   '.trim($error->message));
            }
            libxml_clear_errors();

            return 1;
        }

        $this->line('✅ Valid XML format');

        // Check namespace
        $namespaces = $xml->getNamespaces(true);
        if (! isset($namespaces[''])) {
            $this->warn('⚠️  No default namespace found');
        } else {
            $expectedNs = 'http://www.sitemaps.org/schemas/sitemap/0.9';
            if ($namespaces[''] !== $expectedNs) {
                $this->warn('⚠️  Unexpected namespace: '.$namespaces['']);
            } else {
                $this->line('✅ Correct namespace');
            }
        }

        // Count URLs
        $urlCount = count($xml->url);
        $this->line('✅ Total URLs: '.$urlCount);
        $this->newLine();

        // Validate URL structure
        $this->info('📊 Validating URL entries...');
        $errors = 0;
        $warnings = 0;

        $urlNum = 0;
        foreach ($xml->url as $index => $url) {
            $urlNum++;

            // Check required fields
            if (! isset($url->loc)) {
                $this->error("   ❌ URL #{$urlNum}: Missing <loc>");
                $errors++;

                continue;
            }

            $loc = (string) $url->loc;

            // Validate URL format
            if (! filter_var($loc, FILTER_VALIDATE_URL)) {
                $this->error("   ❌ URL #{$urlNum}: Invalid URL format: {$loc}");
                $errors++;

                continue;
            }

            // Check HTTPS
            if (! str_starts_with($loc, 'https://')) {
                $this->warn("   ⚠️  URL #{$urlNum}: Not using HTTPS: {$loc}");
                $warnings++;
            }

            // Check lastmod format
            if (isset($url->lastmod)) {
                $lastmod = (string) $url->lastmod;
                if (! preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $lastmod)) {
                    $this->warn("   ⚠️  URL #{$urlNum}: Invalid lastmod format: {$lastmod}");
                    $warnings++;
                }
            }

            // Check priority range
            if (isset($url->priority)) {
                $priority = (float) $url->priority;
                if ($priority < 0 || $priority > 1) {
                    $this->error("   ❌ URL #{$urlNum}: Priority out of range (0-1): {$priority}");
                    $errors++;
                }
            }

            // Check changefreq values
            if (isset($url->changefreq)) {
                $changefreq = (string) $url->changefreq;
                $validFreqs = ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'];
                if (! in_array($changefreq, $validFreqs)) {
                    $this->error("   ❌ URL #{$urlNum}: Invalid changefreq: {$changefreq}");
                    $errors++;
                }
            }
        }

        $this->newLine();

        // Summary
        if ($errors === 0 && $warnings === 0) {
            $this->info('🎉 Perfect! Sitemap is 100% valid!');
            $this->line('   • No errors found');
            $this->line('   • No warnings found');
            $this->line('   • All '.$urlCount.' URLs are properly formatted');
        } else {
            if ($errors > 0) {
                $this->error('❌ Found '.$errors.' error(s)');
            }
            if ($warnings > 0) {
                $this->warn('⚠️  Found '.$warnings.' warning(s)');
            }
        }

        $this->newLine();

        // Recommendations
        $this->info('💡 Recommendations:');

        if ($urlCount > 50000) {
            $this->line('   • Consider splitting into multiple sitemaps (sitemap index)');
        }

        $fileSize = File::size($sitemapPath);
        if ($fileSize > 10 * 1024 * 1024) { // 10MB
            $this->line('   • Consider compressing sitemap (gzip)');
        }

        $this->line('   • Submit sitemap to Google Search Console: https://search.google.com/search-console');
        $this->line('   • Submit sitemap to Bing Webmaster: https://www.bing.com/webmasters');
        $this->line('   • Verify sitemap URL is accessible: '.config('app.url').'/sitemap.xml');

        return $errors > 0 ? 1 : 0;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2).' '.$units[$pow];
    }
}
