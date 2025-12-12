<?php

namespace App\Services;

use App\Models\AutoPostConfig;

class ArticleQualityService
{
    /**
     * Validate generated article meets quality standards
     */
    public function validateQuality(array $articleData, AutoPostConfig $config): array
    {
        \Log::info('🔍 Validating article quality');
        
        $issues = [];
        $warnings = [];
        
        // 1. Word count check
        $wordCount = str_word_count(strip_tags($articleData['content']));
        if ($wordCount < $config->min_word_count) {
            $issues[] = "Word count too low: {$wordCount} words (minimum: {$config->min_word_count})";
        } elseif ($wordCount > $config->max_word_count) {
            $warnings[] = "Word count high: {$wordCount} words (maximum: {$config->max_word_count})";
        }
        
        // 2. Reading time check
        $readingTime = $articleData['reading_time'] ?? 0;
        if ($readingTime < $config->min_reading_time) {
            $issues[] = "Reading time too short: {$readingTime} min (minimum: {$config->min_reading_time})";
        }
        
        // 3. Heading count
        $headingCount = $this->countHeadings($articleData['content']);
        if ($headingCount < $config->min_headings) {
            $issues[] = "Not enough headings: {$headingCount} (minimum: {$config->min_headings})";
        } elseif ($headingCount > $config->max_headings) {
            $warnings[] = "Too many headings: {$headingCount} (maximum: {$config->max_headings})";
        }
        
        // 4. Paragraph count
        $paragraphCount = substr_count($articleData['content'], '<p>');
        if ($paragraphCount < $config->min_paragraphs) {
            $issues[] = "Not enough paragraphs: {$paragraphCount} (minimum: {$config->min_paragraphs})";
        }
        
        // 5. Content structure validation
        if (!$this->hasProperStructure($articleData['content'])) {
            $issues[] = "Content lacks proper HTML structure";
        }
        
        // 6. Check for placeholder text
        $placeholders = $this->findPlaceholders($articleData['content']);
        if (!empty($placeholders)) {
            $issues[] = "Contains placeholders: " . implode(', ', $placeholders);
        }
        
        // 7. Meta fields validation
        if (empty($articleData['excerpt'])) {
            $issues[] = "Missing excerpt";
        }
        
        if (empty($articleData['meta_title'])) {
            $warnings[] = "Missing meta title";
        }
        
        if (empty($articleData['meta_description'])) {
            $warnings[] = "Missing meta description";
        }
        
        // 8. Title length check
        if (strlen($articleData['title']) < 20) {
            $warnings[] = "Title too short: " . strlen($articleData['title']) . " characters";
        } elseif (strlen($articleData['title']) > 100) {
            $warnings[] = "Title too long: " . strlen($articleData['title']) . " characters";
        }
        
        // 9. Check for duplicate content patterns (very basic)
        if ($this->hasDuplicatePatterns($articleData['content'])) {
            $warnings[] = "Detected repetitive content patterns";
        }
        
        $isValid = empty($issues);
        
        $result = [
            'valid' => $isValid,
            'issues' => $issues,
            'warnings' => $warnings,
            'metrics' => [
                'word_count' => $wordCount,
                'heading_count' => $headingCount,
                'paragraph_count' => $paragraphCount,
                'reading_time' => $readingTime,
                'title_length' => strlen($articleData['title']),
                'excerpt_length' => strlen($articleData['excerpt'] ?? ''),
            ],
            'quality_score' => $this->calculateQualityScore($articleData, $config, $issues, $warnings),
        ];
        
        if ($isValid) {
            \Log::info('✅ Article quality validation passed', [
                'score' => $result['quality_score'],
                'warnings' => count($warnings),
            ]);
        } else {
            \Log::warning('⚠️  Article quality validation failed', [
                'issues' => count($issues),
                'warnings' => count($warnings),
            ]);
        }
        
        return $result;
    }

    /**
     * Count headings in content
     */
    protected function countHeadings(string $content): int
    {
        $h2Count = substr_count($content, '<h2>');
        $h3Count = substr_count($content, '<h3>');
        return $h2Count + $h3Count;
    }

    /**
     * Check if content has proper HTML structure
     */
    protected function hasProperStructure(string $content): bool
    {
        // Must have at least one heading and one paragraph
        $hasHeading = (strpos($content, '<h2>') !== false || strpos($content, '<h3>') !== false);
        $hasParagraph = strpos($content, '<p>') !== false;
        
        // Check for balanced tags
        $h2Open = substr_count($content, '<h2>');
        $h2Close = substr_count($content, '</h2>');
        $h3Open = substr_count($content, '<h3>');
        $h3Close = substr_count($content, '</h3>');
        $pOpen = substr_count($content, '<p>');
        $pClose = substr_count($content, '</p>');
        
        $balanced = ($h2Open === $h2Close) && ($h3Open === $h3Close) && ($pOpen === $pClose);
        
        return $hasHeading && $hasParagraph && $balanced;
    }

    /**
     * Find placeholder text in content
     */
    protected function findPlaceholders(string $content): array
    {
        $placeholders = [];
        $patterns = [
            '/\[YOUR[_\s].*?\]/i',
            '/\[INSERT[_\s].*?\]/i',
            '/\{\{.*?\}\}/i',
            '/TODO:/i',
            '/FIXME:/i',
            '/\[COMPANY[_\s]NAME\]/i',
            '/\[CLIENT[_\s]NAME\]/i',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches)) {
                $placeholders = array_merge($placeholders, $matches[0]);
            }
        }
        
        return array_unique($placeholders);
    }

    /**
     * Check for duplicate content patterns
     */
    protected function hasDuplicatePatterns(string $content): bool
    {
        // Extract paragraphs
        preg_match_all('/<p>(.*?)<\/p>/s', $content, $matches);
        $paragraphs = $matches[1];
        
        if (count($paragraphs) < 3) {
            return false;
        }
        
        // Check if any two paragraphs are very similar
        for ($i = 0; $i < count($paragraphs) - 1; $i++) {
            for ($j = $i + 1; $j < count($paragraphs); $j++) {
                $similarity = 0;
                similar_text($paragraphs[$i], $paragraphs[$j], $similarity);
                
                if ($similarity > 80) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Calculate overall quality score (0-100)
     */
    protected function calculateQualityScore(array $articleData, AutoPostConfig $config, array $issues, array $warnings): int
    {
        $score = 100;
        
        // Deduct for issues (critical)
        $score -= count($issues) * 15;
        
        // Deduct for warnings (minor)
        $score -= count($warnings) * 5;
        
        // Word count bonus/penalty
        $wordCount = str_word_count(strip_tags($articleData['content']));
        $optimalWordCount = ($config->min_word_count + $config->max_word_count) / 2;
        $wordCountRatio = $wordCount / $optimalWordCount;
        
        if ($wordCountRatio < 0.8) {
            $score -= 10;
        } elseif ($wordCountRatio >= 0.9 && $wordCountRatio <= 1.1) {
            $score += 5; // Bonus for optimal word count
        }
        
        // Structure bonus
        $headingCount = $this->countHeadings($articleData['content']);
        if ($headingCount >= $config->min_headings && $headingCount <= $config->max_headings) {
            $score += 5;
        }
        
        return max(0, min(100, $score));
    }

    /**
     * Get quality feedback message
     */
    public function getQualityFeedback(array $validationResult): string
    {
        $score = $validationResult['quality_score'];
        
        if ($score >= 90) {
            return "Excellent quality! Article meets all standards.";
        } elseif ($score >= 75) {
            return "Good quality with minor improvements possible.";
        } elseif ($score >= 60) {
            return "Acceptable quality but needs some refinement.";
        } else {
            return "Quality below standards. Manual review recommended.";
        }
    }
}
