<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Support\Facades\Cache;

class FaqAggregationController extends Controller
{
    /**
     * Category-based FAQ topic slugs
     */
    protected array $topics = [
        'perizinan-lingkungan' => [
            'title' => 'Perizinan Lingkungan',
            'description' => 'Pertanyaan umum seputar perizinan lingkungan: AMDAL, UKL-UPL, Limbah B3, dan dokumen lingkungan lainnya.',
            'categories' => ['regulation', 'general'],
            'keywords' => ['amdal', 'ukl-upl', 'limbah b3', 'lingkungan', 'izin lingkungan', 'persetujuan lingkungan'],
            'icon' => 'fa-leaf',
        ],
        'tips-perizinan' => [
            'title' => 'Tips & Panduan Perizinan',
            'description' => 'Tips praktis dan panduan langkah demi langkah untuk mengurus berbagai jenis perizinan industri.',
            'categories' => ['tips', 'general'],
            'keywords' => ['panduan', 'tips', 'cara', 'langkah', 'tutorial', 'prosedur'],
            'icon' => 'fa-lightbulb',
        ],
        'regulasi-industri' => [
            'title' => 'Regulasi & Kebijakan Industri',
            'description' => 'FAQ tentang regulasi terbaru, perubahan kebijakan, dan persyaratan hukum untuk industri manufaktur.',
            'categories' => ['regulation', 'news'],
            'keywords' => ['regulasi', 'peraturan', 'kebijakan', 'hukum', 'uu', 'pp', 'permen'],
            'icon' => 'fa-gavel',
        ],
        'studi-kasus' => [
            'title' => 'Studi Kasus & Pengalaman',
            'description' => 'Pertanyaan berdasarkan studi kasus nyata dan pengalaman klien dalam pengurusan perizinan.',
            'categories' => ['case-study'],
            'keywords' => ['studi kasus', 'pengalaman', 'contoh', 'kasus', 'implementasi'],
            'icon' => 'fa-briefcase',
        ],
    ];

    /**
     * FAQ hub index page
     */
    public function index()
    {
        $topics = collect($this->topics)->map(function ($topic, $slug) {
            $topic['slug'] = $slug;
            $topic['faq_count'] = $this->countFaqsForTopic($topic);

            return $topic;
        });

        return view('programmatic.faq-index', compact('topics'));
    }

    /**
     * Topic-specific FAQ page
     */
    public function show(string $topicSlug)
    {
        if (! isset($this->topics[$topicSlug])) {
            abort(404);
        }

        $topic = $this->topics[$topicSlug];
        $topic['slug'] = $topicSlug;

        $faqs = Cache::remember("faq.topic.{$topicSlug}", 3600, function () use ($topic) {
            return $this->extractFaqsFromArticles($topic);
        });

        $otherTopics = collect($this->topics)->except($topicSlug)->map(function ($t, $s) {
            $t['slug'] = $s;

            return $t;
        });

        $year = (int) date('Y');

        return view('programmatic.faq-topic', compact('topic', 'faqs', 'otherTopics', 'topicSlug', 'year'));
    }

    /**
     * Extract FAQ items from articles matching a topic
     */
    protected function extractFaqsFromArticles(array $topic): array
    {
        $articles = Article::published()
            ->byLanguage('id')
            ->where(function ($q) use ($topic) {
                foreach ($topic['categories'] as $cat) {
                    $q->orWhere('category', $cat);
                }
                foreach (array_slice($topic['keywords'], 0, 3) as $kw) {
                    $q->orWhere('title', 'LIKE', "%{$kw}%");
                }
            })
            ->orderBy('views_count', 'desc')
            ->take(30)
            ->get();

        $faqs = [];

        foreach ($articles as $article) {
            $extracted = $this->extractQuestionsFromContent($article);
            foreach ($extracted as $faq) {
                $faq['source_article'] = [
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'url' => route('blog.article.id', $article->slug),
                ];
                $faqs[] = $faq;
            }
        }

        // Deduplicate by similarity (basic check)
        $unique = [];
        $seenQuestions = [];
        foreach ($faqs as $faq) {
            $normalized = strtolower(preg_replace('/[^a-z0-9\s]/i', '', $faq['question']));
            $isDuplicate = false;
            foreach ($seenQuestions as $seen) {
                if (similar_text($normalized, $seen) / max(strlen($normalized), strlen($seen), 1) > 0.8) {
                    $isDuplicate = true;
                    break;
                }
            }
            if (! $isDuplicate) {
                $unique[] = $faq;
                $seenQuestions[] = $normalized;
            }
        }

        return array_slice($unique, 0, 30);
    }

    /**
     * Extract question-answer pairs from article content
     */
    protected function extractQuestionsFromContent(Article $article): array
    {
        $content = $article->content;
        $faqs = [];

        // Pattern: Find headings that look like questions
        // Match h2, h3, h4 that contain question words or end with ?
        $questionWords = 'apa|bagaimana|berapa|mengapa|kenapa|dimana|kapan|siapa|apakah|bisakah|how|what|why|when|where|which|can|does|is';

        preg_match_all(
            '/<h[2-4][^>]*>(.*?)<\/h[2-4]>\s*(.*?)(?=<h[2-4]|$)/si',
            $content,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $match) {
            $heading = strip_tags(trim($match[1]));
            $body = trim($match[2]);

            // Check if heading looks like a question
            $isQuestion = preg_match('/\?$/', $heading) ||
                          preg_match('/^('.$questionWords.')\b/i', $heading);

            if ($isQuestion && ! empty($body)) {
                // Extract first meaningful paragraph as answer
                $answer = $this->extractAnswer($body);
                if (mb_strlen($answer) >= 50) {
                    $faqs[] = [
                        'question' => rtrim($heading, '?').'?',
                        'answer' => $answer,
                    ];
                }
            }
        }

        return array_slice($faqs, 0, 5);
    }

    /**
     * Extract a clean answer from HTML content
     */
    protected function extractAnswer(string $html): string
    {
        // Get first 1-2 paragraphs
        preg_match_all('/<p[^>]*>(.*?)<\/p>/si', $html, $matches);

        $answer = '';
        foreach ($matches[1] ?? [] as $p) {
            $text = strip_tags(trim($p));
            if (mb_strlen($text) > 30) {
                $answer .= $text.' ';
                if (mb_strlen($answer) > 200) {
                    break;
                }
            }
        }

        // Fallback: strip all tags and take first 300 chars
        if (mb_strlen(trim($answer)) < 50) {
            $answer = strip_tags($html);
            $answer = mb_substr(trim(preg_replace('/\s+/', ' ', $answer)), 0, 300);
        }

        return trim(mb_substr($answer, 0, 500));
    }

    protected function countFaqsForTopic(array $topic): int
    {
        return Article::published()
            ->byLanguage('id')
            ->where(function ($q) use ($topic) {
                foreach ($topic['categories'] as $cat) {
                    $q->orWhere('category', $cat);
                }
            })
            ->count();
    }
}
