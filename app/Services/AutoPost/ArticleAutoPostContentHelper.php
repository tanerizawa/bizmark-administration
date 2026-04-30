<?php

namespace App\Services\AutoPost;

use App\Models\Article;
use App\Models\ArticleTopic;
use Illuminate\Support\Str;

class ArticleAutoPostContentHelper
{
    /**
     * Ensure content has "Baca juga" / "Artikel Terkait" section for SEO scoring bonus (+3)
     */
    public function ensureBacaJugaSection(string $content, ArticleTopic $topic): string
    {
        // Check if already has the section (from InternalLinkService or prior processing)
        if (Str::contains($content, ['Baca juga', 'Baca Juga', 'Artikel Terkait', 'artikel terkait'])) {
            return $content;
        }

        $baseUrl = rtrim(config('app.url'), '/');

        // Find related published articles
        $relatedArticles = Article::where('status', 'published')
            ->select('id', 'title', 'slug');

        if ($topic->category) {
            $related = (clone $relatedArticles)
                ->where('category', $topic->category)
                ->inRandomOrder()
                ->limit(5)
                ->get();
        } else {
            $related = collect();
        }

        if ($related->isEmpty()) {
            $related = $relatedArticles->inRandomOrder()->limit(5)->get();
        }

        if ($related->isEmpty()) {
            return $content;
        }

        // Build "Artikel Terkait" section in HTML (auto-post always generates HTML)
        $section = '<hr><h2>Artikel Terkait</h2>';
        $section .= '<p>Baca juga artikel lainnya di Bizmark:</p><ul>';
        foreach ($related as $a) {
            $url = htmlspecialchars("{$baseUrl}/blog/{$a->slug}", ENT_QUOTES, 'UTF-8');
            $title = htmlspecialchars($a->title, ENT_QUOTES, 'UTF-8');
            $section .= "<li><a href=\"{$url}\">{$title}</a></li>";
        }
        $section .= '</ul>';

        return $content."\n\n".$section;
    }
}
