<?php

namespace App\Services;

use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SchemaMarkupService
{
    /**
     * Generate Article schema (JSON-LD) for a blog post
     */
    public function articleSchema(Article $article): array
    {
        $url = config('app.url') . '/blog/' . $article->slug;

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => mb_substr($article->meta_title ?? $article->title, 0, 110),
            'description' => mb_substr($article->meta_description ?? $article->excerpt ?? '', 0, 300),
            'image' => $article->featured_image ? asset('storage/' . $article->featured_image) : asset('images/og-image-id.jpg'),
            'author' => $this->buildAuthorSchema($article->author),
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Bizmark.ID',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/logo.png'),
                ],
            ],
            'datePublished' => $article->published_at?->toIso8601String() ?? $article->created_at->toIso8601String(),
            'dateModified' => $article->updated_at->toIso8601String(),
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $url,
            ],
            'url' => $url,
            'keywords' => $article->meta_keywords ?? '',
            'articleSection' => $article->category_label ?? $article->category,
            'inLanguage' => $article->language === 'en' ? 'en-US' : 'id-ID',
            'wordCount' => str_word_count(strip_tags($article->content ?? '')),
        ];

        return $schema;
    }

    /**
     * Build Person or Organization author schema with E-E-A-T signals
     */
    protected function buildAuthorSchema(?User $author): array
    {
        if (!$author) {
            return [
                '@type' => 'Organization',
                'name' => 'Bizmark.ID',
                'url' => config('app.url'),
            ];
        }

        $authorSchema = [
            '@type' => 'Person',
            'name' => $author->author_display_name,
            'url' => config('app.url') . '/blog',
        ];

        if ($author->job_title) {
            $authorSchema['jobTitle'] = $author->job_title;
        } elseif ($author->position) {
            $authorSchema['jobTitle'] = $author->position;
        }

        if ($author->bio) {
            $authorSchema['description'] = mb_substr($author->bio, 0, 300);
        }

        if ($author->expertise) {
            $authorSchema['knowsAbout'] = $author->expertise_list;
        }

        $sameAs = [];
        if ($author->linkedin_url) {
            $sameAs[] = $author->linkedin_url;
        }
        if ($author->twitter_url) {
            $sameAs[] = $author->twitter_url;
        }
        if (!empty($sameAs)) {
            $authorSchema['sameAs'] = $sameAs;
        }

        $authorSchema['worksFor'] = [
            '@type' => 'Organization',
            'name' => 'Bizmark.ID',
            'url' => config('app.url'),
        ];

        return $authorSchema;
    }

    /**
     * Generate FAQ schema from article headings + content
     * Extracts Q&A pairs from h2/h3 headings that look like questions
     */
    public function faqSchema(Article $article): ?array
    {
        $content = $article->content ?? '';
        $faqItems = [];

        // Match headings that look like questions (contain ?, or start with question words)
        preg_match_all(
            '/<h[2-3][^>]*>(.+?)<\/h[2-3]>\s*([\s\S]*?)(?=<h[2-3]|$)/i',
            $content,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $match) {
            $heading = strip_tags(trim($match[1]));
            // Strip {#anchor} cruft
            $heading = preg_replace('/\s*\{#[\w-]+\}/', '', $heading);
            $body = trim($match[2]);

            // Broad question detection for ID + EN headings
            $isQuestion = preg_match('/\?|^(apa|bagaimana|berapa|mengapa|kenapa|kapan|siapa|dimana|apakah|bisakah|haruskah|perlukah|how|what|why|when|where|who|which|can|do|does|is|are|should)\b/i', $heading)
                || preg_match('/\b(apa saja|apa itu|berapa lama|berapa biaya|apa yang|siapa yang)\b/i', $heading);

            if ($isQuestion && !empty($body)) {
                // Extract first 1-2 paragraphs as answer
                preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $body, $pMatches);
                $answerParts = array_slice($pMatches[1] ?? [], 0, 2);
                $answer = strip_tags(implode(' ', $answerParts));

                if (mb_strlen($answer) >= 20) {
                    $faqItems[] = [
                        '@type' => 'Question',
                        'name' => $heading,
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => mb_substr($answer, 0, 500),
                        ],
                    ];
                }
            }
        }

        if (count($faqItems) < 2) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_slice($faqItems, 0, 10),
        ];
    }

    /**
     * Generate HowTo schema for guide/panduan articles
     */
    public function howToSchema(Article $article): ?array
    {
        // Only apply to panduan/guide categories or articles with "panduan"/"langkah"/"cara" in title
        $isGuide = in_array($article->category, ['panduan', 'tips'])
            || preg_match('/panduan|langkah|cara|tutorial|guide|how.to|step/i', $article->title);

        if (!$isGuide) {
            return null;
        }

        $content = $article->content ?? '';
        $steps = [];

        // Extract h2/h3 headings as steps
        preg_match_all('/<h[2-3][^>]*>(.+?)<\/h[2-3]>\s*([\s\S]*?)(?=<h[2-3]|$)/i', $content, $matches, PREG_SET_ORDER);

        $stepNumber = 0;
        foreach ($matches as $match) {
            $heading = strip_tags(trim($match[1]));
            $body = trim($match[2]);

            // Skip TOC, intro headings
            if (preg_match('/daftar isi|table of contents|kesimpulan|conclusion|referensi|sumber/i', $heading)) {
                continue;
            }

            // Extract first paragraph as direction
            preg_match('/<p[^>]*>(.*?)<\/p>/is', $body, $pMatch);
            $direction = strip_tags($pMatch[1] ?? '');

            if (mb_strlen($direction) >= 15) {
                $stepNumber++;
                $steps[] = [
                    '@type' => 'HowToStep',
                    'position' => $stepNumber,
                    'name' => $heading,
                    'text' => mb_substr($direction, 0, 500),
                ];
            }
        }

        if (count($steps) < 2) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            'name' => $article->title,
            'description' => $article->meta_description ?? $article->excerpt ?? '',
            'image' => $article->featured_image ? asset('storage/' . $article->featured_image) : null,
            'totalTime' => 'PT' . max(($article->reading_time ?? 5), 5) . 'M',
            'step' => array_slice($steps, 0, 15),
        ];
    }

    /**
     * Generate Breadcrumb schema for a blog article
     */
    public function breadcrumbSchema(Article $article): array
    {
        $items = [
            ['name' => 'Beranda', 'url' => config('app.url')],
            ['name' => 'Blog', 'url' => config('app.url') . '/blog'],
        ];

        if ($article->category) {
            $items[] = [
                'name' => $article->category_label ?? ucfirst($article->category),
                'url' => config('app.url') . '/blog/kategori/' . $article->category,
            ];
        }

        $items[] = [
            'name' => mb_substr($article->title, 0, 60),
            'url' => config('app.url') . '/blog/' . $article->slug,
        ];

        $listItems = [];
        foreach ($items as $i => $item) {
            $listItems[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $listItems,
        ];
    }

    /**
     * Generate all applicable schemas for an article
     */
    public function allSchemas(Article $article): array
    {
        $schemas = [];

        $schemas[] = $this->articleSchema($article);
        $schemas[] = $this->breadcrumbSchema($article);

        $faq = $this->faqSchema($article);
        if ($faq) {
            $schemas[] = $faq;
        }

        $howTo = $this->howToSchema($article);
        if ($howTo) {
            $schemas[] = $howTo;
        }

        return $schemas;
    }
}
