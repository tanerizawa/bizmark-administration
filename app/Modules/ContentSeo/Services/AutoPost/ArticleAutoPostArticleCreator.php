<?php

namespace App\Modules\ContentSeo\Services\AutoPost;

use App\Models\Article;
use App\Models\ArticleTopic;

class ArticleAutoPostArticleCreator
{

    /**
     * Create article in database with proper duplicate handling
     */
    public function createArticle(array $articleData, ArticleTopic $topic): Article
    {
        // Get the first admin user as author (or use a specific bot user)
        $author = \App\Models\User::where('role_id', 1)->first();
        
        if (!$author) {
            $author = \App\Models\User::first();
        }
        
        // Check if article with same title already exists (INCLUDING soft deleted)
        $existingArticle = Article::withTrashed()->where('title', $articleData['title'])->first();
        
        if ($existingArticle) {
            \Log::warning('⚠️ Article with same title already exists', [
                'existing_id' => $existingArticle->id,
                'title' => $articleData['title'],
                'is_deleted' => $existingArticle->trashed(),
            ]);
            
            // If soft deleted, restore it and update
            if ($existingArticle->trashed()) {
                $existingArticle->restore();
                $existingArticle->update([
                    'content' => $articleData['content'],
                    'excerpt' => $articleData['excerpt'],
                    'status' => $articleData['status'],
                    'published_at' => $articleData['published_at'],
                    'meta_title' => $articleData['meta_title'],
                    'meta_description' => $articleData['meta_description'],
                    'source_type' => 'auto-generated',
                ]);
                \Log::info('✅ Restored and updated soft-deleted article', [
                    'article_id' => $existingArticle->id,
                ]);
            } elseif ($existingArticle->status === 'draft') {
                // Update existing draft
                $existingArticle->update([
                    'content' => $articleData['content'],
                    'excerpt' => $articleData['excerpt'],
                    'status' => $articleData['status'],
                    'published_at' => $articleData['published_at'],
                    'source_type' => 'auto-generated',
                ]);
            }
            
            return $existingArticle;
        }
        
        // Generate unique slug BEFORE create to avoid constraint violation
        $slug = Article::generateUniqueSlug($articleData['title']);
        
        // Use database transaction to ensure atomicity
        return \DB::transaction(function () use ($articleData, $topic, $author, $slug) {
            return Article::create([
                'title' => $articleData['title'],
                'slug' => $slug, // Explicitly set unique slug
                'content' => $articleData['content'],
                'excerpt' => $articleData['excerpt'],
                'category' => $articleData['category'],
                'language' => $topic->language ?? 'id',
                'tags' => $articleData['tags'],
                'status' => $articleData['status'],
                'published_at' => $articleData['published_at'],
                'author_id' => $author->id,
                'source_type' => 'auto-generated',
                'topic_cluster_id' => $topic->topic_cluster_id,
                'meta_title' => $articleData['meta_title'],
                'meta_description' => $articleData['meta_description'],
                'meta_keywords' => $articleData['meta_keywords'],
                'reading_time' => $articleData['reading_time'],
                'featured_image' => $articleData['featured_image'] ?? null,
                'is_featured' => false,
            ]);
        });
    }

}
