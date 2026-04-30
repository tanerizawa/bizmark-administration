<?php

namespace App\Services\AutoPost;

use App\Models\ArticleTopic;
use App\Models\AutoPostLog;
use App\Models\AutoPostSchedule;
use App\Services\PexelsService;
use Illuminate\Support\Str;

class ArticleAutoPostImageHelper
{
    public function __construct(protected PexelsService $pexelsService) {}


    /**
     * Fetch a relevant featured image from Pexels based on topic context
     */
    public function fetchFeaturedImage(ArticleTopic $topic, AutoPostSchedule $schedule): ?string
    {
        AutoPostLog::logInfo('featured_image_search', '🖼️ Searching for featured image on Pexels', [
            'schedule_id' => $schedule->id,
            'topic_id' => $topic->id,
        ]);

        // Build search queries from topic context, ordered by specificity
        $queries = $this->buildImageSearchQueries($topic);

        $bestPhoto = null;
        $bestScore = -1;
        $bestQuery = null;

        foreach ($queries as $query) {
            try {
                $results = $this->pexelsService->searchPhotos($query, 10, 1, [
                    'orientation' => 'landscape',
                    'size' => 'large',
                    'locale' => 'id-ID',
                ]);

                if (!empty($results['photos'])) {
                    foreach ($results['photos'] as $photo) {
                        $score = $this->scorePhotoCandidate($photo, $query, $topic);
                        if ($score > $bestScore) {
                            $bestScore = $score;
                            $bestPhoto = $photo;
                            $bestQuery = $query;
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Pexels search failed for query', [
                    'query' => $query,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Last-resort fallback to curated image if all query results are too weak.
        if (!$bestPhoto) {
            try {
                $curated = $this->pexelsService->getCuratedPhotos(10, 1);
                foreach ($curated['photos'] ?? [] as $photo) {
                    $score = $this->scorePhotoCandidate($photo, '', $topic);
                    if ($score > $bestScore) {
                        $bestScore = $score;
                        $bestPhoto = $photo;
                        $bestQuery = 'curated_fallback';
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Pexels curated fallback failed', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($bestPhoto) {
            $imageUrl = $bestPhoto['src']['large2x'] ?? $bestPhoto['src']['large'] ?? $bestPhoto['src']['original'];
            $path = $this->pexelsService->downloadAndSavePhoto(
                $imageUrl,
                $bestPhoto['photographer'] ?? 'Unknown',
                $bestPhoto['id']
            );

            if ($path) {
                AutoPostLog::logSuccess('featured_image_found', '🖼️ Featured image downloaded from Pexels', [
                    'schedule_id' => $schedule->id,
                    'topic_id' => $topic->id,
                    'context' => [
                        'query' => $bestQuery,
                        'photo_id' => $bestPhoto['id'],
                        'photographer' => $bestPhoto['photographer'] ?? 'Unknown',
                        'path' => $path,
                        'relevance_score' => $bestScore,
                    ],
                ]);

                return $path;
            }
        }

        AutoPostLog::logWarning('featured_image_not_found', '⚠️ No suitable featured image found', [
            'schedule_id' => $schedule->id,
            'topic_id' => $topic->id,
        ]);

        return null;
    }

    /**
     * Build prioritized search queries for Pexels from topic context
     */
    public function buildImageSearchQueries(ArticleTopic $topic): array
    {
        $queries = [];

        $title = trim((string) $topic->title);
        $keywords = collect($topic->keywords ?? [])->filter()->map(fn ($k) => trim((string) $k))->values();

        // 1) Prioritize first two meaningful keywords
        if ($keywords->isNotEmpty()) {
            $queries[] = $keywords->take(2)->implode(' ');
            $queries[] = $keywords->take(3)->implode(' ');
        }

        // 2) Main title + Indonesia business context
        if ($title !== '') {
            $queries[] = $title;
            $queries[] = $title . ' Indonesia bisnis';
        }

        // 3) Simplified title without year/noise words
        $cleanTitle = preg_replace('/\b\d{4}\b/', '', $topic->title);
        $cleanTitle = preg_replace('/\b\d+\b/', '', $cleanTitle);
        $cleanTitle = preg_replace('/[^\w\s]/u', '', $cleanTitle);
        $stopWords = ['dan', 'yang', 'untuk', 'dengan', 'dalam', 'cara', 'panduan', 'lengkap'];
        $words = array_values(array_filter(explode(' ', Str::lower(trim($cleanTitle))), function ($w) use ($stopWords) {
            return strlen($w) > 2 && !in_array($w, $stopWords, true);
        }));
        if (count($words) > 4) {
            $words = array_slice($words, 0, 4);
        }
        if (!empty($words)) {
            $queries[] = implode(' ', $words);
        }

        // 4) Category-based fallback queries for better semantic match
        $categoryMap = [
            'tips' => ['konsultasi bisnis kantor', 'dokumen perizinan usaha indonesia'],
            'regulation' => ['regulasi pemerintah dokumen legal', 'izin usaha dokumen resmi'],
            'general' => ['bisnis indonesia kantor profesional', 'dokumen usaha indonesia'],
            'case-study' => ['tim bisnis meeting sukses', 'analisis bisnis indonesia'],
            'news' => ['berita bisnis indonesia', 'perkembangan usaha indonesia'],
        ];

        foreach (($categoryMap[$topic->category] ?? ['dokumen perizinan bisnis indonesia']) as $fallbackQuery) {
            $queries[] = $fallbackQuery;
        }

        // 5) Hard fallback
        $queries[] = 'perizinan usaha indonesia';
        $queries[] = 'business office document';

        return array_unique(array_filter($queries));
    }

    /**
     * Score candidate photos by text relevance and visual suitability.
     */
    public function scorePhotoCandidate(array $photo, string $query, ArticleTopic $topic): int
    {
        $score = 0;

        $queryTokens = $this->extractSearchTokens($query);
        $topicTokens = $this->extractSearchTokens($topic->title . ' ' . implode(' ', $topic->keywords ?? []));
        $categoryHints = $this->extractSearchTokens(implode(' ', $this->getCategoryImageHints($topic->category)));

        $alt = Str::lower((string) ($photo['alt'] ?? ''));
        $url = Str::lower((string) ($photo['url'] ?? ''));
        $photographer = Str::lower((string) ($photo['photographer'] ?? ''));
        $haystack = $alt . ' ' . $url . ' ' . $photographer;

        foreach ($queryTokens as $token) {
            if (Str::contains($haystack, $token)) {
                $score += 8;
            }
        }

        foreach ($topicTokens as $token) {
            if (Str::contains($haystack, $token)) {
                $score += 4;
            }
        }

        foreach ($categoryHints as $hint) {
            if (Str::contains($haystack, $hint)) {
                $score += 3;
            }
        }

        // Prefer landscape-like ratio and larger image width when available.
        $width = (int) ($photo['width'] ?? 0);
        $height = (int) ($photo['height'] ?? 1);
        if ($width > 0 && $height > 0) {
            $ratio = $width / max(1, $height);
            if ($ratio >= 1.3 && $ratio <= 2.2) {
                $score += 6;
            }
            if ($width >= 1600) {
                $score += 4;
            }
        }

        return $score;
    }

    public function extractSearchTokens(string $text): array
    {
        $normalized = Str::lower(preg_replace('/[^\pL\pN\s]/u', ' ', $text));
        $parts = preg_split('/\s+/', $normalized) ?: [];
        $stop = ['dan', 'yang', 'untuk', 'dengan', 'dalam', 'atau', 'the', 'for', 'from'];

        return array_values(array_unique(array_filter($parts, function ($token) use ($stop) {
            return mb_strlen($token) > 2 && !in_array($token, $stop, true);
        })));
    }

    public function getCategoryImageHints(?string $category): array
    {
        $hints = [
            'tips' => ['business', 'office', 'document', 'consulting', 'planning'],
            'regulation' => ['legal', 'government', 'regulation', 'law', 'document'],
            'general' => ['business', 'office', 'meeting', 'document'],
            'case-study' => ['success', 'teamwork', 'strategy', 'meeting'],
            'news' => ['news', 'media', 'business', 'analysis'],
        ];

        return $hints[$category] ?? ['business', 'office', 'document'];
    }

}
