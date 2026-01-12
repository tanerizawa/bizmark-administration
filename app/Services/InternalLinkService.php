<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleTopic;
use Illuminate\Support\Collection;

class InternalLinkService
{
    /**
     * Add internal links to article content
     */
    public function addInternalLinks(string $content, ArticleTopic $topic, int $targetCount = 3): string
    {
        \Log::info('🔗 Adding internal links', [
            'topic_id' => $topic->id,
            'target_count' => $targetCount,
        ]);

        // 1. Find relevant existing articles
        $relatedArticles = $this->findRelatedArticles($topic, $targetCount * 2);
        
        if ($relatedArticles->isEmpty()) {
            \Log::warning('⚠️  No related articles found for internal linking', [
                'topic_id' => $topic->id,
            ]);
            return $content;
        }
        
        // 2. Identify anchor opportunities in content
        $opportunities = $this->findAnchorOpportunities($content, $relatedArticles);
        
        if (empty($opportunities)) {
            \Log::warning('⚠️  No anchor opportunities found in content', [
                'topic_id' => $topic->id,
                'related_articles' => $relatedArticles->count(),
            ]);
            return $content;
        }
        
        // 3. Insert links naturally (limit to target count)
        $opportunities = array_slice($opportunities, 0, $targetCount);
        $contentWithLinks = $this->insertLinks($content, $opportunities);
        
        \Log::info('✅ Internal links added', [
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
        return Article::published()
            ->where(function($query) use ($topic) {
                // Same category
                $query->where('category', $topic->category);
                
                // Or matching tags
                if (!empty($topic->tags)) {
                    foreach ($topic->tags as $tag) {
                        $query->orWhereJsonContains('tags', $tag);
                    }
                }
                
                // Or matching keywords
                if (!empty($topic->keywords)) {
                    foreach ($topic->keywords as $keyword) {
                        $query->orWhere('title', 'ILIKE', "%{$keyword}%")
                              ->orWhere('content', 'ILIKE', "%{$keyword}%");
                    }
                }
            })
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
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
                \Log::warning('Failed to generate route for internal link', [
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
}
