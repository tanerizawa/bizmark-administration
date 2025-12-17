<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BacklinkTarget;
use App\Models\Backlink;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use DOMDocument;
use DOMXPath;

class CrawlBacklinksCommand extends Command
{
    protected $signature = 'backlink:crawl 
                            {--target= : Specific target ID to crawl}
                            {--url= : Specific URL to crawl}
                            {--all : Crawl all targets}
                            {--limit=20 : Maximum targets to crawl}
                            {--force : Force crawl even if recently checked}';

    protected $description = 'Crawl target websites to automatically detect and record bizmark.id backlinks';

    private $bizmarkDomains = [
        'bizmark.id',
        'www.bizmark.id',
    ];

    public function handle()
    {
        $this->info('🕷️  Backlink Crawler Started');
        $this->newLine();

        $targetId = $this->option('target');
        $url = $this->option('url');
        $all = $this->option('all');
        $limit = (int) $this->option('limit');

        if ($url) {
            $this->crawlUrl($url);
            return 0;
        }

        // Get targets to crawl
        $query = BacklinkTarget::query()
            ->where('status', '!=', 'rejected');

        if ($targetId) {
            $query->where('id', $targetId);
        } elseif (!$all) {
            $query->whereIn('status', ['accepted', 'responded']);
        }

        $targets = $query->limit($limit)->get();

        if ($targets->isEmpty()) {
            $this->error('❌ No targets found to crawl');
            return 1;
        }

        $this->info("🔍 Found {$targets->count()} target(s) to crawl");
        $this->newLine();

        $discovered = 0;
        $updated = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar($targets->count());
        $progressBar->start();

        foreach ($targets as $target) {
            try {
                $result = $this->crawlTarget($target);
                
                $discovered += $result['new_backlinks'];
                $updated += $result['updated_backlinks'];

                $progressBar->advance();
                
                // Rate limiting - be respectful
                sleep(3);
            } catch (\Exception $e) {
                $errors++;
                Log::error('Crawl failed for target', [
                    'target_id' => $target->id,
                    'url' => $target->website_url,
                    'error' => $e->getMessage()
                ]);
                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info('📊 Crawl Summary:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Targets Crawled', $targets->count()],
                ['New Backlinks Found', $discovered],
                ['Existing Updated', $updated],
                ['Errors', $errors],
            ]
        );

        if ($discovered > 0) {
            $this->info("✅ {$discovered} new backlink(s) automatically recorded!");
        }

        return 0;
    }

    /**
     * Crawl specific target website
     */
    private function crawlTarget(BacklinkTarget $target): array
    {
        $this->line("Crawling: {$target->website_name}");

        $response = Http::timeout(15)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; BizmarkBot/1.0; +https://bizmark.id/bot)',
                'Accept' => 'text/html',
            ])
            ->get($target->website_url);

        if (!$response->successful()) {
            throw new \Exception("Failed to fetch: HTTP {$response->status()}");
        }

        $html = $response->body();
        $backlinks = $this->extractBacklinks($html, $target);

        $newCount = 0;
        $updateCount = 0;

        foreach ($backlinks as $backlinkData) {
            $existing = Backlink::where('source_url', $backlinkData['source_url'])
                ->where('target_url', $backlinkData['target_url'])
                ->first();

            if ($existing) {
                // Update existing backlink
                $existing->update([
                    'anchor_text' => $backlinkData['anchor_text'],
                    'type' => $backlinkData['type'],
                    'status' => 'active',
                    'last_checked_at' => now(),
                ]);
                $updateCount++;
                $this->line("  ✓ Updated: {$backlinkData['anchor_text']}");
            } else {
                // Create new backlink
                Backlink::create([
                    'backlink_target_id' => $target->id,
                    'source_url' => $backlinkData['source_url'],
                    'target_url' => $backlinkData['target_url'],
                    'anchor_text' => $backlinkData['anchor_text'],
                    'type' => $backlinkData['type'],
                    'status' => 'active',
                    'domain_authority' => $target->domain_authority,
                    'acquired_at' => now(),
                    'last_checked_at' => now(),
                    'notes' => 'Auto-discovered by crawler',
                ]);
                $newCount++;
                $this->info("  🆕 New: {$backlinkData['anchor_text']}");
            }
        }

        return [
            'new_backlinks' => $newCount,
            'updated_backlinks' => $updateCount,
        ];
    }

    /**
     * Extract backlinks from HTML
     */
    private function extractBacklinks(string $html, BacklinkTarget $target): array
    {
        $backlinks = [];

        // Use DOMDocument for proper HTML parsing
        $dom = new DOMDocument();
        @$dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($dom);

        // Find all links
        $links = $xpath->query('//a[@href]');

        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            $anchorText = trim($link->textContent);
            $rel = $link->getAttribute('rel');

            // Check if link points to bizmark.id
            if ($this->isBizmarkLink($href)) {
                $backlinks[] = [
                    'source_url' => $target->website_url,
                    'target_url' => $this->normalizeUrl($href),
                    'anchor_text' => $anchorText ?: 'Bizmark.ID',
                    'type' => $this->getLinkType($rel),
                ];
            }
        }

        return $backlinks;
    }

    /**
     * Check if URL is bizmark.id link
     */
    private function isBizmarkLink(string $url): bool
    {
        $normalizedUrl = strtolower($url);

        foreach ($this->bizmarkDomains as $domain) {
            if (
                str_contains($normalizedUrl, $domain) ||
                str_starts_with($normalizedUrl, '/' . $domain) ||
                str_starts_with($normalizedUrl, 'https://' . $domain) ||
                str_starts_with($normalizedUrl, 'http://' . $domain)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Normalize URL to full format
     */
    private function normalizeUrl(string $url): string
    {
        if (str_starts_with($url, '//')) {
            return 'https:' . $url;
        }

        if (str_starts_with($url, '/')) {
            return 'https://bizmark.id' . $url;
        }

        if (!str_starts_with($url, 'http')) {
            return 'https://bizmark.id/' . ltrim($url, '/');
        }

        return $url;
    }

    /**
     * Determine link type (dofollow/nofollow)
     */
    private function getLinkType(string $rel): string
    {
        return str_contains(strtolower($rel), 'nofollow') ? 'nofollow' : 'dofollow';
    }

    /**
     * Crawl specific URL (debugging)
     */
    private function crawlUrl(string $url): void
    {
        $this->info("Crawling URL: {$url}");

        try {
            $response = Http::timeout(15)->get($url);
            $html = $response->body();

            $backlinks = $this->extractBacklinks($html, new BacklinkTarget([
                'website_url' => $url,
                'website_name' => 'Custom URL',
            ]));

            $this->info("Found {$backlinks->count()} backlink(s):");
            
            foreach ($backlinks as $backlink) {
                $this->line("  • {$backlink['anchor_text']} → {$backlink['target_url']} [{$backlink['type']}]");
            }
        } catch (\Exception $e) {
            $this->error("Failed: " . $e->getMessage());
        }
    }
}
