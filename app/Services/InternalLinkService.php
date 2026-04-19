<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleTopic;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class InternalLinkService
{
    /**
     * pSEO service keywords → slugs mapping for cross-linking
     */
    protected array $serviceKeywords = [
        'perizinan-lb3' => ['limbah b3', 'pengelolaan limbah', 'limbah berbahaya', 'perizinan lb3', 'izin lb3'],
        'amdal' => ['amdal', 'analisis dampak lingkungan', 'analisis mengenai dampak'],
        'ukl-upl' => ['ukl-upl', 'ukl upl', 'upaya pengelolaan lingkungan', 'upaya pemantauan lingkungan'],
        'oss-nib' => ['oss', 'nib', 'nomor induk berusaha', 'oss rba', 'online single submission'],
        'pbg-slf' => ['pbg', 'slf', 'persetujuan bangunan gedung', 'sertifikat laik fungsi', 'izin mendirikan bangunan', 'imb'],
        'izin-operasional' => ['izin operasional', 'izin usaha', 'izin industri'],
        'konsultan-lingkungan' => ['konsultan lingkungan', 'konsultasi lingkungan', 'environmental consultant'],
        'monitoring-digital' => ['monitoring lingkungan', 'pemantauan lingkungan', 'monitoring digital'],
        'izin-k3' => ['izin k3', 'keselamatan kerja', 'kesehatan kerja', 'k3'],
    ];

    /**
     * Add internal links to article content (articles + pSEO cross-links)
     */
    public function addInternalLinks(string $content, ArticleTopic $topic, int $targetCount = 3): string
    {
        \Illuminate\Support\Facades\Log::info('🔗 Adding internal links', [
            'topic_id' => $topic->id,
            'target_count' => $targetCount,
        ]);

        // 1. Find relevant existing articles
        $relatedArticles = $this->findRelatedArticles($topic, $targetCount * 2);
        
        if ($relatedArticles->isEmpty()) {
            \Illuminate\Support\Facades\Log::warning('⚠️  No related articles found for internal linking', [
                'topic_id' => $topic->id,
            ]);
            return $content;
        }
        
        // 2. Identify anchor opportunities in content
        $opportunities = $this->findAnchorOpportunities($content, $relatedArticles);
        
        if (empty($opportunities)) {
            \Illuminate\Support\Facades\Log::warning('⚠️  No anchor opportunities found in content', [
                'topic_id' => $topic->id,
                'related_articles' => $relatedArticles->count(),
            ]);
            return $content;
        }
        
        // 3. Insert links naturally (limit to target count)
        $opportunities = array_slice($opportunities, 0, $targetCount);
        $contentWithLinks = $this->insertLinks($content, $opportunities);
        
        \Illuminate\Support\Facades\Log::info('✅ Internal links added', [
            'topic_id' => $topic->id,
            'links_added' => count($opportunities),
        ]);
        
        return $contentWithLinks;
    }

    /**
     * Find related published articles
     */
    protected function findRelatedArticles(ArticleTopic $topic, int $limit): Collection
    {
        // Prioritize same-cluster articles for topic authority
        $clusterId = $topic->topic_cluster_id;

        $query = Article::published();

        if ($clusterId) {
            // Same cluster first, then same category, then keyword matches
            $query->where(function($q) use ($topic, $clusterId) {
                $q->where('topic_cluster_id', $clusterId)
                  ->orWhere('category', $topic->category);

                if (!empty($topic->tags)) {
                    foreach ($topic->tags as $tag) {
                        $q->orWhereJsonContains('tags', $tag);
                    }
                }
            })
            ->orderByRaw("CASE WHEN topic_cluster_id = ? THEN 0 ELSE 1 END", [$clusterId])
            ->orderBy('published_at', 'desc');
        } else {
            $query->where(function($q) use ($topic) {
                $q->where('category', $topic->category);

                if (!empty($topic->tags)) {
                    foreach ($topic->tags as $tag) {
                        $q->orWhereJsonContains('tags', $tag);
                    }
                }

                if (!empty($topic->keywords)) {
                    foreach ($topic->keywords as $keyword) {
                        $q->orWhere('title', 'LIKE', "%{$keyword}%");
                    }
                }
            })
            ->orderBy('published_at', 'desc');
        }

        return $query->limit($limit)->get();
    }

    /**
     * Find natural anchor opportunities in content
     */
    protected function findAnchorOpportunities(string $content, Collection $articles): array
    {
        $opportunities = [];
        
        foreach ($articles as $article) {
            // Extract potential anchor text from article
            $anchorTexts = $this->extractAnchorTexts($article);
            
            foreach ($anchorTexts as $anchorText) {
                // Search for this phrase in content (case-insensitive)
                $pattern = '/<p>(.*?)' . preg_quote($anchorText, '/') . '(.*?)<\/p>/isu';
                
                if (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                    // Check if not already a link
                    $contextBefore = substr($content, max(0, $matches[0][1] - 50), 50);
                    $contextAfter = substr($content, $matches[0][1], strlen($matches[0][0]) + 50);
                    
                    if (strpos($contextBefore . $contextAfter, '<a ') === false) {
                        $opportunities[] = [
                            'anchor_text' => $anchorText,
                            'position' => $matches[0][1],
                            'article' => $article,
                            'context' => $matches[0][0],
                        ];
                    }
                }
            }
        }
        
        // Sort by position (earlier in content = higher priority)
        usort($opportunities, fn($a, $b) => $a['position'] <=> $b['position']);
        
        // Remove duplicates (same article)
        $uniqueOpportunities = [];
        $usedArticles = [];
        
        foreach ($opportunities as $opp) {
            if (!in_array($opp['article']->id, $usedArticles)) {
                $uniqueOpportunities[] = $opp;
                $usedArticles[] = $opp['article']->id;
            }
        }
        
        return $uniqueOpportunities;
    }

    /**
     * Extract potential anchor texts from article
     */
    protected function extractAnchorTexts(Article $article): array
    {
        $texts = [];
        
        // 1. Article title (most specific)
        $texts[] = $article->title;
        
        // 2. Tags (if available)
        if (!empty($article->tags)) {
            foreach ($article->tags as $tag) {
                if (strlen($tag) > 5) { // Ignore very short tags
                    $texts[] = $tag;
                }
            }
        }
        
        // 3. Extract key phrases from title (e.g., "Cara Mengurus IMB" from "Cara Mengurus IMB Rumah Tinggal")
        $titleWords = explode(' ', $article->title);
        if (count($titleWords) > 3) {
            // First 3-4 words
            $texts[] = implode(' ', array_slice($titleWords, 0, 3));
            $texts[] = implode(' ', array_slice($titleWords, 0, 4));
        }
        
        return array_unique($texts);
    }

    /**
     * Insert links into content
     */
    protected function insertLinks(string $content, array $opportunities): string
    {
        // Process from end to preserve positions
        $opportunities = array_reverse($opportunities);
        
        foreach ($opportunities as $opp) {
            $anchorText = $opp['anchor_text'];
            $article = $opp['article'];
            
            try {
                $url = route('blog.article.id', $article->slug);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Failed to generate route for internal link', [
                    'article_slug' => $article->slug,
                    'error' => $e->getMessage()
                ]);
                continue; // Skip this link if route fails
            }
            
            // Create link with appropriate attributes
            $link = sprintf(
                '<a href="%s" class="text-blue-600 hover:underline" title="%s">%s</a>',
                $url,
                htmlspecialchars($article->title),
                $anchorText
            );
            
            // Replace first occurrence of anchor text (case-insensitive, within <p> tags)
            $pattern = '/(<p>.*?)(' . preg_quote($anchorText, '/') . ')(.*?<\/p>)/isu';
            $replacement = '$1' . $link . '$3';
            
            $content = preg_replace($pattern, $replacement, $content, 1);
        }
        
        return $content;
    }

    /**
     * Validate internal links in content
     */
    public function validateLinks(string $content): array
    {
        $linkPattern = '/<a\s+href="([^"]+)"[^>]*>([^<]+)<\/a>/i';
        preg_match_all($linkPattern, $content, $matches, PREG_SET_ORDER);
        
        $stats = [
            'total_links' => count($matches),
            'internal_links' => 0,
            'external_links' => 0,
            'broken_links' => 0,
        ];
        
        foreach ($matches as $match) {
            $url = $match[1];
            
            if (strpos($url, config('app.url')) !== false || strpos($url, '/') === 0) {
                $stats['internal_links']++;
            } else {
                $stats['external_links']++;
            }
        }
        
        return $stats;
    }

    /**
     * Inject backlinks into existing articles pointing to a newly published article.
     * This is the "reverse" direction — old articles get links to the new article.
     *
     * @return int Number of articles updated
     */
    public function injectBacklinks(Article $newArticle, int $maxUpdates = 5): int
    {
        \Illuminate\Support\Facades\Log::info('🔗 Injecting backlinks for new article', [
            'article_id' => $newArticle->id,
            'slug' => $newArticle->slug,
        ]);

        // Extract anchor texts from the new article
        $anchorTexts = $this->extractAnchorTexts($newArticle);

        if (empty($anchorTexts)) {
            return 0;
        }

        // Find candidate articles — prioritize same cluster for topical authority
        $query = Article::published()
            ->where('id', '!=', $newArticle->id);

        if ($newArticle->topic_cluster_id) {
            $query->where(function ($q) use ($newArticle) {
                $q->where('topic_cluster_id', $newArticle->topic_cluster_id)
                  ->orWhere('category', $newArticle->category);
                if (!empty($newArticle->tags)) {
                    foreach ($newArticle->tags as $tag) {
                        $q->orWhereJsonContains('tags', $tag);
                    }
                }
            })
            ->orderByRaw("CASE WHEN topic_cluster_id = ? THEN 0 ELSE 1 END", [$newArticle->topic_cluster_id]);
        } else {
            $query->where(function ($q) use ($newArticle) {
                $q->where('category', $newArticle->category);
                if (!empty($newArticle->tags)) {
                    foreach ($newArticle->tags as $tag) {
                        $q->orWhereJsonContains('tags', $tag);
                    }
                }
            });
        }

        $candidates = $query->orderBy('published_at', 'desc')
            ->limit($maxUpdates * 3)
            ->get();

        if ($candidates->isEmpty()) {
            \Illuminate\Support\Facades\Log::info('⚠️ No candidate articles for backlink injection');
            return 0;
        }

        $updated = 0;
        $newArticleUrl = route('blog.article.id', $newArticle->slug);

        foreach ($candidates as $candidate) {
            if ($updated >= $maxUpdates) {
                break;
            }

            $content = $candidate->content;

            // Skip if this article already links to the new article
            if (str_contains($content, $newArticleUrl) || str_contains($content, '/blog/' . $newArticle->slug)) {
                continue;
            }

            // Try to find a natural anchor opportunity
            $injected = false;
            foreach ($anchorTexts as $anchorText) {
                $pattern = '/<p>(.*?)(' . preg_quote($anchorText, '/') . ')(.*?)<\/p>/isu';

                if (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                    // Ensure we're not inside an existing link
                    $context = $matches[0][0];
                    if (str_contains($context, '<a ')) {
                        continue;
                    }

                    $link = sprintf(
                        '<a href="%s" class="text-blue-600 hover:underline" title="%s">%s</a>',
                        $newArticleUrl,
                        htmlspecialchars($newArticle->title),
                        $anchorText
                    );

                    $replacement = '$1' . $link . '$3';
                    $newContent = preg_replace($pattern, $replacement, $content, 1);

                    if ($newContent && $newContent !== $content) {
                        $candidate->content = $newContent;
                        $candidate->saveQuietly(); // Don't trigger observer loops
                        $updated++;
                        $injected = true;

                        \Illuminate\Support\Facades\Log::info('✅ Backlink injected', [
                            'target_article' => $candidate->slug,
                            'anchor' => $anchorText,
                            'links_to' => $newArticle->slug,
                        ]);
                        break; // One link per article
                    }
                }
            }
        }

        \Illuminate\Support\Facades\Log::info('🔗 Backlink injection complete', [
            'new_article' => $newArticle->slug,
            'articles_updated' => $updated,
        ]);

        return $updated;
    }

    /**
     * Batch scan all published articles and inject cross-links where missing.
     * Used for initial backfill or periodic maintenance.
     *
     * @return array Stats about links injected
     */
    public function batchBacklinkScan(int $limit = 50): array
    {
        $stats = ['scanned' => 0, 'updated' => 0, 'links_injected' => 0];

        $articles = Article::published()
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();

        foreach ($articles as $article) {
            $stats['scanned']++;
            $injected = $this->injectBacklinks($article, 3);
            if ($injected > 0) {
                $stats['updated']++;
                $stats['links_injected'] += $injected;
            }
        }

        \Illuminate\Support\Facades\Log::info('🔗 Batch backlink scan complete', $stats);

        return $stats;
    }

    /**
     * Inject pSEO cross-links into article content.
     * Finds mentions of service keywords and links them to the closest pSEO city page.
     *
     * @param string $content Article HTML content
     * @param string|null $preferredCity Preferred city slug (from article topic or cluster)
     * @param int $maxLinks Maximum pSEO links to inject
     * @return string Modified content with pSEO cross-links
     */
    public function injectPseoLinks(string $content, ?string $preferredCity = null, int $maxLinks = 2): string
    {
        $cities = config('programmatic_seo.cities', []);
        $serviceKeys = config('programmatic_seo.services', []);

        if (empty($cities) || empty($serviceKeys)) {
            return $content;
        }

        // Pick a city for linking: preferred or top population cities
        $targetCities = [];
        if ($preferredCity && isset($cities[$preferredCity])) {
            $targetCities[] = $preferredCity;
        }
        // Add major city fallbacks
        $majorCities = ['jakarta-selatan', 'surabaya', 'karawang', 'bekasi', 'semarang'];
        foreach ($majorCities as $mc) {
            if (isset($cities[$mc]) && !in_array($mc, $targetCities)) {
                $targetCities[] = $mc;
            }
            if (count($targetCities) >= 3) break;
        }

        if (empty($targetCities)) {
            $targetCities = [array_key_first($cities)];
        }

        $linksInjected = 0;
        $linkedServices = [];

        foreach ($this->serviceKeywords as $serviceSlug => $keywords) {
            if ($linksInjected >= $maxLinks) break;
            if (!in_array($serviceSlug, $serviceKeys)) continue;
            if (in_array($serviceSlug, $linkedServices)) continue;

            foreach ($keywords as $keyword) {
                if ($linksInjected >= $maxLinks) break;

                // Match keyword in <p> tags, not already inside a link
                $pattern = '/(<p>(?:(?!<a\s).)*?)(' . preg_quote($keyword, '/') . ')((?:(?!<a\s).)*?<\/p>)/isu';

                if (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                    // Verify we're not inside an existing <a> tag
                    $before = substr($content, max(0, $matches[0][1] - 200), 200);
                    if (preg_match('/<a\b[^>]*>[^<]*$/i', $before)) {
                        continue;
                    }

                    $citySlug = $targetCities[$linksInjected % count($targetCities)];
                    $cityName = $cities[$citySlug]['name'] ?? ucfirst($citySlug);

                    $url = url("/layanan/{$serviceSlug}/{$citySlug}");
                    $linkTitle = "Jasa " . ucwords(str_replace('-', ' ', $serviceSlug)) . " {$cityName}";
                    $link = sprintf(
                        '<a href="%s" class="text-blue-600 hover:underline" title="%s">%s</a>',
                        $url,
                        htmlspecialchars($linkTitle),
                        $matches[2][0]
                    );

                    $replacement = $matches[1][0] . $link . $matches[3][0];
                    $content = substr_replace($content, $replacement, $matches[0][1], strlen($matches[0][0]));

                    $linksInjected++;
                    $linkedServices[] = $serviceSlug;

                    \Illuminate\Support\Facades\Log::info('🔗 pSEO cross-link injected', [
                        'keyword' => $keyword,
                        'service' => $serviceSlug,
                        'city' => $citySlug,
                    ]);
                    break; // Only one link per service
                }
            }
        }

        return $content;
    }

    /**
     * Batch inject pSEO cross-links into all published articles missing them.
     *
     * @return array Stats about links injected
     */
    public function batchPseoLinkScan(int $limit = 50): array
    {
        $stats = ['scanned' => 0, 'updated' => 0, 'links_injected' => 0];

        $articles = Article::published()
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();

        $cities = config('programmatic_seo.cities', []);
        $cityKeys = array_keys($cities);

        foreach ($articles as $article) {
            $stats['scanned']++;

            // Skip if already has pSEO links
            if (str_contains($article->content ?? '', '/layanan/') && preg_match('/\/layanan\/[\w-]+\/[\w-]+/', $article->content)) {
                continue;
            }

            // Pick a relevant city based on article content mentions
            $preferredCity = null;
            foreach ($cityKeys as $citySlug) {
                $cityName = $cities[$citySlug]['name'] ?? '';
                if ($cityName && stripos($article->content ?? '', $cityName) !== false) {
                    $preferredCity = $citySlug;
                    break;
                }
            }

            $newContent = $this->injectPseoLinks($article->content ?? '', $preferredCity, 2);
            if ($newContent !== ($article->content ?? '')) {
                $article->content = $newContent;
                $article->saveQuietly();
                $stats['updated']++;
                $stats['links_injected'] += 2; // Approximate
            }
        }

        \Illuminate\Support\Facades\Log::info('🔗 Batch pSEO link scan complete', $stats);

        return $stats;
    }
}
