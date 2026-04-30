<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\TopicCluster;
use Illuminate\Support\Facades\Cache;

class PillarPageController extends Controller
{
    /**
     * List all pillar/topic cluster pages
     */
    public function index()
    {
        $clusters = Cache::remember('pillar_pages_index', 3600, function () {
            return TopicCluster::active()
                ->where('language', app()->getLocale() === 'en' ? 'en' : 'id')
                ->get()
                ->map(function ($cluster) {
                    $articles = $cluster->getArticles();

                    return [
                        'title' => $cluster->pillar_title,
                        'slug' => $cluster->pillar_slug,
                        'description' => $cluster->pillar_description,
                        'service_slug' => $cluster->service_slug,
                        'subtopics_count' => count($cluster->subtopics ?? []),
                        'articles_count' => $articles->count(),
                        'total_views' => $articles->sum('views_count'),
                    ];
                });
        });

        $year = date('Y');

        return view('programmatic.pillar-index', compact('clusters', 'year'));
    }

    /**
     * Show a pillar page with full topic cluster content
     */
    public function show(string $pillarSlug)
    {
        $cluster = TopicCluster::where('pillar_slug', $pillarSlug)
            ->active()
            ->firstOrFail();

        $services = config('services_data', []);
        $service = collect($services)->firstWhere('slug', $cluster->service_slug);

        // Get mapped articles
        $articles = $cluster->getArticles();

        // Get keyword clusters for this service
        $keywordClusters = $cluster->getKeywordClusters();

        // Group articles by subtopic type
        $subtopics = collect($cluster->subtopics ?? [])->map(function ($subtopic) use ($articles) {
            $title = $subtopic['title'] ?? '';
            // Find matching article
            $matchedArticle = $articles->first(function ($article) use ($title) {
                $titleWords = array_filter(explode(' ', strtolower($title)), fn ($w) => strlen($w) > 3);
                $articleTitle = strtolower($article->title);
                $matchCount = 0;
                foreach ($titleWords as $word) {
                    if (str_contains($articleTitle, $word)) {
                        $matchCount++;
                    }
                }

                return $matchCount >= 2;
            });

            return [
                'title' => $title,
                'type' => $subtopic['type'] ?? 'general',
                'priority' => $subtopic['priority'] ?? 'medium',
                'article' => $matchedArticle,
                'has_article' => $matchedArticle !== null,
            ];
        });

        // Related clusters
        $relatedClusters = TopicCluster::active()
            ->where('id', '!=', $cluster->id)
            ->where('language', $cluster->language)
            ->take(4)
            ->get();

        $year = date('Y');

        // SEO data
        $seo = [
            'title' => "{$cluster->pillar_title} | Bizmark.ID",
            'description' => $cluster->pillar_description,
            'keywords' => $keywordClusters->flatMap(fn ($kc) => $kc->keywords ?? [])->unique()->implode(', '),
        ];

        // FAQ schema from subtopics
        $faqs = $subtopics->where('type', 'faq')->values()->take(5);

        return view('programmatic.pillar-show', compact(
            'cluster', 'service', 'articles', 'subtopics',
            'keywordClusters', 'relatedClusters', 'year', 'seo', 'faqs'
        ));
    }
}
