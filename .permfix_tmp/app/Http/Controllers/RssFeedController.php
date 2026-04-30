<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Response;

class RssFeedController extends Controller
{
    /**
     * Generate RSS 2.0 Feed
     */
    public function rss(): Response
    {
        $articles = Article::published()
            ->orderBy('published_at', 'desc')
            ->take(50)
            ->get();

        $xml = $this->buildRss($articles);

        return response($xml, 200)
            ->header('Content-Type', 'application/rss+xml; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Generate Atom Feed
     */
    public function atom(): Response
    {
        $articles = Article::published()
            ->orderBy('published_at', 'desc')
            ->take(50)
            ->get();

        $xml = $this->buildAtom($articles);

        return response($xml, 200)
            ->header('Content-Type', 'application/atom+xml; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    protected function buildRss($articles): string
    {
        $appUrl = config('app.url');
        $now = now()->toRfc2822String();

        $items = '';
        foreach ($articles as $article) {
            $url = $appUrl . '/blog/' . e($article->slug);
            $title = e($article->title);
            $desc = e($article->meta_description ?? $article->excerpt ?? mb_substr(strip_tags($article->content), 0, 300));
            $pubDate = ($article->published_at ?? $article->created_at)->toRfc2822String();
            $category = e($article->category_label ?? $article->category);
            $image = $article->featured_image ? asset('storage/' . $article->featured_image) : '';

            $enclosure = '';
            if ($image) {
                $enclosure = '<enclosure url="' . e($image) . '" type="image/jpeg" />';
            }

            $items .= <<<XML
        <item>
            <title>{$title}</title>
            <link>{$url}</link>
            <description>{$desc}</description>
            <pubDate>{$pubDate}</pubDate>
            <guid isPermaLink="true">{$url}</guid>
            <category>{$category}</category>
            {$enclosure}
        </item>

XML;
        }

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/">
    <channel>
        <title>Bizmark.ID - Blog Perizinan &amp; Regulasi</title>
        <link>{$appUrl}/blog</link>
        <description>Artikel terbaru tentang perizinan usaha, limbah B3, AMDAL, UKL-UPL, dan regulasi industri di Indonesia</description>
        <language>id</language>
        <lastBuildDate>{$now}</lastBuildDate>
        <atom:link href="{$appUrl}/feed/rss" rel="self" type="application/rss+xml" />
        <image>
            <url>{$appUrl}/images/logo.png</url>
            <title>Bizmark.ID</title>
            <link>{$appUrl}</link>
        </image>
{$items}
    </channel>
</rss>
XML;
    }

    protected function buildAtom($articles): string
    {
        $appUrl = config('app.url');
        $now = now()->toIso8601String();

        $entries = '';
        foreach ($articles as $article) {
            $url = $appUrl . '/blog/' . e($article->slug);
            $title = e($article->title);
            $summary = e($article->meta_description ?? $article->excerpt ?? mb_substr(strip_tags($article->content), 0, 300));
            $updated = $article->updated_at->toIso8601String();
            $published = ($article->published_at ?? $article->created_at)->toIso8601String();

            $entries .= <<<XML
    <entry>
        <title>{$title}</title>
        <link href="{$url}" />
        <id>{$url}</id>
        <updated>{$updated}</updated>
        <published>{$published}</published>
        <summary>{$summary}</summary>
        <author>
            <name>Bizmark.ID</name>
        </author>
    </entry>

XML;
        }

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
    <title>Bizmark.ID - Blog Perizinan &amp; Regulasi</title>
    <link href="{$appUrl}/blog" />
    <link href="{$appUrl}/feed/atom" rel="self" type="application/atom+xml" />
    <id>{$appUrl}/blog</id>
    <updated>{$now}</updated>
    <subtitle>Artikel terbaru tentang perizinan usaha, limbah B3, AMDAL, UKL-UPL, dan regulasi industri</subtitle>
{$entries}
</feed>
XML;
    }
}
