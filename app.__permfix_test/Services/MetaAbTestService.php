<?php

namespace App\Services;

use App\Models\Article;
use App\Models\MetaAbTest;
use Illuminate\Support\Facades\Log;

class MetaAbTestService
{
    protected OpenRouterService $ai;

    public function __construct(OpenRouterService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * Create a new A/B test for an article's meta tags
     */
    public function createTest(Article $article, string $testType = 'both'): ?MetaAbTest
    {
        // Don't create if there's already an active test
        if (MetaAbTest::where('article_id', $article->id)->running()->exists()) {
            return null;
        }

        try {
            $variantB = $this->generateVariant($article);
            if (! $variantB) {
                return null;
            }

            return MetaAbTest::create([
                'article_id' => $article->id,
                'test_type' => $testType,
                'variant_a_title' => $article->meta_title ?: $article->title,
                'variant_a_description' => $article->meta_description ?: $article->excerpt,
                'variant_b_title' => $variantB['title'] ?? $article->meta_title,
                'variant_b_description' => $variantB['description'] ?? $article->meta_description,
                'status' => 'running',
                'started_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error("MetaAbTest: Failed for article #{$article->id}", ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Generate AI variant B for meta tags
     */
    protected function generateVariant(Article $article): ?array
    {
        $prompt = <<<PROMPT
Buat variant B untuk A/B test meta tags artikel ini. Tujuan: meningkatkan CTR di Google.

Judul: {$article->title}
Meta Title saat ini (Variant A): {$article->meta_title}
Meta Description saat ini (Variant A): {$article->meta_description}
Kategori: {$article->category}

Buat variant B yang BERBEDA dari A tapi tetap relevan:
- Title ≤60 karakter, gunakan power words, angka, atau emotional trigger
- Description ≤155 karakter, gunakan CTA dan benefit yang jelas

Response JSON:
{"title": "...", "description": "..."}
PROMPT;

        $response = $this->ai->chat([
            ['role' => 'user', 'content' => $prompt],
        ], [
            'model' => 'google/gemini-2.5-flash',
            'temperature' => 0.7,
            'max_tokens' => 500,
        ]);

        if (! $response['success']) {
            return null;
        }

        return json_decode(
            preg_replace('/```json\s*|\s*```/', '', trim($response['content'])),
            true
        );
    }

    /**
     * Evaluate running tests and determine winners
     */
    public function evaluateTests(): array
    {
        $results = [];
        $tests = MetaAbTest::running()
            ->where('started_at', '<', now()->subDays(7)) // Minimum 7 days of data
            ->get();

        foreach ($tests as $test) {
            $ctrA = $test->ctr_a;
            $ctrB = $test->ctr_b;
            $totalData = $test->variant_a_impressions + $test->variant_b_impressions;

            // Need minimum 100 total impressions
            if ($totalData < 100) {
                continue;
            }

            // Simple statistical significance check
            $confidence = $this->calculateConfidence($test);

            if ($confidence >= 90) {
                $winner = $ctrB > $ctrA ? 'b' : 'a';
                $test->update([
                    'winner' => $winner,
                    'confidence' => $confidence,
                    'status' => 'completed',
                    'ended_at' => now(),
                ]);

                // Apply winner to article
                if ($winner === 'b') {
                    $article = $test->article;
                    if ($test->variant_b_title) {
                        $article->meta_title = $test->variant_b_title;
                    }
                    if ($test->variant_b_description) {
                        $article->meta_description = $test->variant_b_description;
                    }
                    $article->save();
                }

                $results[] = [
                    'test_id' => $test->id,
                    'article' => $test->article->title ?? 'Unknown',
                    'winner' => $winner,
                    'confidence' => $confidence,
                    'ctr_a' => $ctrA,
                    'ctr_b' => $ctrB,
                ];
            } elseif ($test->started_at->lt(now()->subDays(30))) {
                // If 30 days passed without significance, mark inconclusive
                $test->update([
                    'winner' => 'inconclusive',
                    'confidence' => $confidence,
                    'status' => 'completed',
                    'ended_at' => now(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Calculate statistical confidence using z-test approximation
     */
    protected function calculateConfidence(MetaAbTest $test): float
    {
        $nA = max($test->variant_a_impressions, 1);
        $nB = max($test->variant_b_impressions, 1);
        $pA = $test->variant_a_clicks / $nA;
        $pB = $test->variant_b_clicks / $nB;

        $pPool = ($test->variant_a_clicks + $test->variant_b_clicks) / ($nA + $nB);
        $se = sqrt($pPool * (1 - $pPool) * (1 / $nA + 1 / $nB));

        if ($se == 0) {
            return 0;
        }

        $z = abs($pA - $pB) / $se;

        // Convert z-score to confidence
        if ($z >= 2.576) {
            return 99;
        }
        if ($z >= 1.960) {
            return 95;
        }
        if ($z >= 1.645) {
            return 90;
        }
        if ($z >= 1.282) {
            return 80;
        }

        return round(min($z / 1.645 * 90, 89), 1);
    }

    /**
     * Auto-create tests for top articles without active tests
     */
    public function autoCreateTests(int $limit = 3): array
    {
        $created = [];

        $articles = Article::published()
            ->where('views_count', '>', 50)
            ->whereDoesntHave('metaAbTests', function ($q) {
                $q->where('status', 'running');
            })
            ->orderByDesc('views_count')
            ->take($limit)
            ->get();

        foreach ($articles as $article) {
            $test = $this->createTest($article);
            if ($test) {
                $created[] = $test;
            }
        }

        return $created;
    }
}
