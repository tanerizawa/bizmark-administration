<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ContentRefreshLog;
use Illuminate\Support\Facades\Log;

class ContentRefreshService
{
    protected OpenRouterService $ai;

    protected IndexNowService $indexNow;

    public function __construct(OpenRouterService $ai, IndexNowService $indexNow)
    {
        $this->ai = $ai;
        $this->indexNow = $indexNow;
    }

    /**
     * Find articles that need refreshing (older than $days, sorted by staleness)
     */
    public function getStaleArticles(int $days = 90, int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return Article::published()
            ->where('updated_at', '<', now()->subDays($days))
            ->orderBy('updated_at', 'asc')
            ->take($limit)
            ->get();
    }

    /**
     * Refresh a single article's content using AI
     */
    public function refreshArticle(Article $article, string $triggeredBy = 'cron'): array
    {
        $result = [
            'article_id' => $article->id,
            'title' => $article->title,
            'status' => 'skipped',
            'changes' => [],
        ];

        // Capture before state for audit
        $beforeSnapshot = $article->only(['meta_title', 'meta_description', 'meta_keywords', 'excerpt']);

        try {
            // 1. Generate refreshed content sections
            $refreshData = $this->generateRefresh($article);

            if (empty($refreshData)) {
                $result['status'] = 'no_changes';

                return $result;
            }

            $changes = [];

            // 2. Update meta if improved
            if (! empty($refreshData['meta_title']) && $refreshData['meta_title'] !== $article->meta_title) {
                $article->meta_title = $refreshData['meta_title'];
                $changes[] = 'meta_title';
            }

            if (! empty($refreshData['meta_description']) && $refreshData['meta_description'] !== $article->meta_description) {
                $article->meta_description = $refreshData['meta_description'];
                $changes[] = 'meta_description';
            }

            if (! empty($refreshData['meta_keywords']) && $refreshData['meta_keywords'] !== $article->meta_keywords) {
                $article->meta_keywords = $refreshData['meta_keywords'];
                $changes[] = 'meta_keywords';
            }

            // 3. Update excerpt if improved
            if (! empty($refreshData['excerpt'])) {
                $article->excerpt = $refreshData['excerpt'];
                $changes[] = 'excerpt';
            }

            // 4. Append freshness update to content
            if (! empty($refreshData['update_section'])) {
                $article->content = $this->injectUpdateSection($article->content, $refreshData['update_section']);
                $changes[] = 'content';
            }

            // 5. Update year references in content
            $yearUpdated = $this->updateYearReferences($article);
            if ($yearUpdated) {
                $changes[] = 'year_references';
            }

            if (count($changes) > 0) {
                // Touch updated_at for freshness signal
                $article->save();

                // Re-submit to IndexNow
                $this->indexNow->submitUrl($article->getUrl());

                $result['status'] = 'refreshed';
                $result['changes'] = $changes;

                Log::info("ContentRefresh: Article #{$article->id} refreshed", [
                    'title' => $article->title,
                    'changes' => $changes,
                ]);

                // Persistent audit log
                ContentRefreshLog::create([
                    'article_id' => $article->id,
                    'status' => 'refreshed',
                    'changes' => $changes,
                    'before_snapshot' => $beforeSnapshot ?? [],
                    'after_snapshot' => array_intersect_key($article->getAttributes(), array_flip($changes)),
                    'triggered_by' => $triggeredBy ?? 'cron',
                ]);
            } else {
                $result['status'] = 'no_changes';
            }

        } catch (\Throwable $e) {
            $result['status'] = 'error';
            $result['error'] = $e->getMessage();
            Log::error("ContentRefresh: Failed for article #{$article->id}", [
                'error' => $e->getMessage(),
            ]);

            ContentRefreshLog::create([
                'article_id' => $article->id,
                'status' => 'error',
                'error_message' => $e->getMessage(),
                'triggered_by' => $triggeredBy ?? 'cron',
            ]);
        }

        return $result;
    }

    /**
     * Use AI to generate refresh data for an article
     */
    protected function generateRefresh(Article $article): array
    {
        $currentYear = date('Y');
        $lang = $article->language === 'en' ? 'English' : 'Indonesian';

        $prompt = <<<PROMPT
You are an SEO content refresh specialist. Analyze the following article and provide improvements.

Article Title: {$article->title}
Current Meta Title: {$article->meta_title}
Current Meta Description: {$article->meta_description}
Current Meta Keywords: {$article->meta_keywords}
Category: {$article->category}
Language: {$lang}
Published: {$article->published_at?->format('Y-m-d')}
Last Updated: {$article->updated_at?->format('Y-m-d')}

Article Content (first 2000 chars):
{$this->getContentPreview($article->content, 2000)}

INSTRUCTIONS:
1. Improve meta_title for better CTR (max 60 chars), include year {$currentYear}
2. Improve meta_description for better CTR (max 155 chars), include year {$currentYear}
3. Improve meta_keywords (comma-separated, include current year and location keywords)
4. Write a compelling excerpt (max 200 chars)
5. Write a brief "Update {$currentYear}" section (2-3 paragraphs of updated info for this topic, in HTML with <h3> and <p> tags)

Respond ONLY in valid JSON format:
{
    "meta_title": "...",
    "meta_description": "...",
    "meta_keywords": "...",
    "excerpt": "...",
    "update_section": "<h3>Update {$currentYear}</h3><p>...</p>"
}

Write in {$lang}. Do NOT include markdown code fences, only pure JSON.
PROMPT;

        $response = $this->ai->chat([
            ['role' => 'system', 'content' => 'You are an SEO specialist. Respond only in valid JSON format.'],
            ['role' => 'user', 'content' => $prompt],
        ], [
            'model' => 'google/gemini-2.5-flash',
            'temperature' => 0.4,
            'max_tokens' => 2000,
        ]);

        if (empty($response['success']) || empty($response['content'])) {
            return [];
        }

        // Parse JSON response
        $cleaned = trim($response['content']);
        // Strip markdown code fences if present
        $cleaned = preg_replace('/^```(?:json)?\s*/i', '', $cleaned);
        $cleaned = preg_replace('/\s*```\s*$/', '', $cleaned);

        $data = json_decode($cleaned, true);

        return is_array($data) ? $data : [];
    }

    /**
     * Get plain text preview of HTML content
     */
    protected function getContentPreview(string $html, int $maxChars): string
    {
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);

        return mb_substr(trim($text), 0, $maxChars);
    }

    /**
     * Inject an update section before the last heading or at the end
     */
    protected function injectUpdateSection(string $content, string $updateSection): string
    {
        $currentYear = date('Y');

        // Remove any existing update section from previous refreshes
        $content = preg_replace(
            '/<h3[^>]*>Update\s+\d{4}<\/h3>.*?(?=<h[23]|$)/si',
            '',
            $content
        );

        // Add the update section before the closing of content
        // Find the last </p> or </ul> or </ol> and insert after it
        $lastBlockPos = max(
            (int) strrpos($content, '</p>'),
            (int) strrpos($content, '</ul>'),
            (int) strrpos($content, '</ol>')
        );

        if ($lastBlockPos > 0) {
            // Find the end of the last block tag
            $insertPos = $lastBlockPos + strlen(substr($content, $lastBlockPos, strpos($content, '>', $lastBlockPos) - $lastBlockPos + 1));
            $content = substr($content, 0, $insertPos)."\n\n".$updateSection.substr($content, $insertPos);
        } else {
            $content .= "\n\n".$updateSection;
        }

        return $content;
    }

    /**
     * Update outdated year references in content
     */
    protected function updateYearReferences(Article $article): bool
    {
        $currentYear = (int) date('Y');
        $content = $article->content;
        $title = $article->title;
        $metaTitle = $article->meta_title ?? '';
        $changed = false;

        // Update years from 2023-{currentYear-1} to currentYear in titles and content
        for ($year = $currentYear - 3; $year < $currentYear; $year++) {
            $nextYear = $year + 1;

            // Only replace year references that look like article year markers
            // e.g. "Panduan 2024" → "Panduan 2026", "Terbaru 2024" → "Terbaru 2026"
            $patterns = [
                "/(\b(?:panduan|guide|terbaru|update|updated|tahun|year)\s+){$year}\b/i",
                "/\b{$year}(\s*[-\/]\s*{$nextYear})\b/",
            ];

            foreach ($patterns as $pattern) {
                $newContent = preg_replace($pattern, '${1}'.$currentYear, $content);
                if ($newContent !== $content) {
                    $content = $newContent;
                    $changed = true;
                }

                $newTitle = preg_replace($pattern, '${1}'.$currentYear, $title);
                if ($newTitle !== $title) {
                    $title = $newTitle;
                    $changed = true;
                }

                $newMeta = preg_replace($pattern, '${1}'.$currentYear, $metaTitle);
                if ($newMeta !== $metaTitle) {
                    $metaTitle = $newMeta;
                    $changed = true;
                }
            }
        }

        if ($changed) {
            $article->content = $content;
            $article->title = $title;
            if (! empty($metaTitle)) {
                $article->meta_title = $metaTitle;
            }
        }

        return $changed;
    }
}
