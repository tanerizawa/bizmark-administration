<?php

namespace App\Services;

/**
 * AttentionAnalyzer
 * 
 * Analyze user attention patterns dan detect reading behaviors
 * berdasarkan heatmap data, click patterns, dan scroll behavior.
 * 
 * Detects:
 * - F-Pattern: Natural reading flow (top-left → top-right → down-left)
 * - Z-Pattern: Scanning pattern (zigzag)
 * - Scattered Attention: Unfocused, high cognitive load indicator
 * 
 * @package App\Services
 * @author Bizmark.ID Development Team
 * @version 1.0.0
 */
class AttentionAnalyzer
{
    /**
     * F-Pattern detection thresholds
     */
    const F_PATTERN_TOP_THRESHOLD = 0.3; // 30% of viewport height
    const F_PATTERN_LEFT_WEIGHT = 0.6;   // 60% attention on left side

    /**
     * Attention efficiency thresholds
     */
    const EFFICIENCY_EXCELLENT = 0.8;
    const EFFICIENCY_GOOD = 0.6;
    const EFFICIENCY_ACCEPTABLE = 0.4;

    /**
     * Analyze attention pattern dari heatmap data
     * 
     * Heatmap data format:
     * [
     *   ['x' => 100, 'y' => 50, 'intensity' => 0.9, 'duration_ms' => 1500],
     *   ...
     * ]
     * 
     * @param array $heatmapData Array of attention points
     * @return array {
     *   'pattern': string,
     *   'confidence': float,
     *   'metrics': array,
     *   'recommendations': array
     * }
     */
    public function analyzeAttentionPattern(array $heatmapData): array
    {
        if (empty($heatmapData)) {
            return [
                'pattern' => 'unknown',
                'confidence' => 0,
                'metrics' => [],
                'recommendations' => ['Insufficient data to analyze'],
            ];
        }

        // Detect different patterns
        $fPatternScore = $this->detectFPattern($heatmapData);
        $zPatternScore = $this->detectZPattern($heatmapData);
        $scatteredScore = $this->detectScatteredAttention($heatmapData);

        // Determine dominant pattern
        $patterns = [
            'f-pattern' => $fPatternScore,
            'z-pattern' => $zPatternScore,
            'scattered' => $scatteredScore,
        ];

        arsort($patterns);
        $dominantPattern = array_key_first($patterns);
        $confidence = reset($patterns);

        // Calculate additional metrics
        $efficiency = $this->calculateAttentionEfficiency($heatmapData);
        $focusPoints = $this->identifyFocusPoints($heatmapData);
        $coverage = $this->calculateViewportCoverage($heatmapData);

        return [
            'pattern' => $dominantPattern,
            'confidence' => round($confidence, 3),
            'pattern_scores' => [
                'f_pattern' => round($fPatternScore, 3),
                'z_pattern' => round($zPatternScore, 3),
                'scattered' => round($scatteredScore, 3),
            ],
            'metrics' => [
                'efficiency' => round($efficiency, 3),
                'focus_points' => $focusPoints,
                'viewport_coverage' => round($coverage, 3),
                'average_fixation_duration_ms' => $this->calculateAverageFixation($heatmapData),
            ],
            'recommendations' => $this->generatePatternRecommendations(
                $dominantPattern,
                $confidence,
                $efficiency
            ),
            'total_data_points' => count($heatmapData),
        ];
    }

    /**
     * Detect F-Pattern reading behavior
     * 
     * F-Pattern characteristics:
     * 1. Heavy attention on top-left (logo, headline)
     * 2. Horizontal scan along top (navigation)
     * 3. Vertical scan down left side (content)
     * 4. Minimal attention on bottom-right
     * 
     * @param array $data Heatmap data
     * @return float Score 0-1 (confidence level)
     */
    public function detectFPattern(array $data): bool|float
    {
        if (empty($data)) {
            return 0.0;
        }

        $topLeftWeight = 0;
        $topRightWeight = 0;
        $middleLeftWeight = 0;
        $bottomRightWeight = 0;
        $totalWeight = 0;

        foreach ($data as $point) {
            $x = $point['x'] ?? 0;
            $y = $point['y'] ?? 0;
            $intensity = $point['intensity'] ?? 1;
            $duration = $point['duration_ms'] ?? 0;

            // Weight = intensity * duration
            $weight = $intensity * ($duration / 1000);
            $totalWeight += $weight;

            // Classify point into zones
            // Assume viewport: 1920x1080 (can be normalized)
            if ($y < 300) { // Top zone
                if ($x < 960) {
                    $topLeftWeight += $weight;
                } else {
                    $topRightWeight += $weight;
                }
            } elseif ($y < 700) { // Middle zone
                if ($x < 960) {
                    $middleLeftWeight += $weight;
                }
            } else { // Bottom zone
                if ($x >= 960) {
                    $bottomRightWeight += $weight;
                }
            }
        }

        if ($totalWeight == 0) {
            return 0.0;
        }

        // Normalize weights
        $topLeftRatio = $topLeftWeight / $totalWeight;
        $topRightRatio = $topRightWeight / $totalWeight;
        $middleLeftRatio = $middleLeftWeight / $totalWeight;
        $bottomRightRatio = $bottomRightWeight / $totalWeight;

        // F-Pattern scoring
        $fScore = 0;

        // 1. Strong top-left attention (expected: >30%)
        if ($topLeftRatio > 0.3) {
            $fScore += 0.4;
        } elseif ($topLeftRatio > 0.2) {
            $fScore += 0.2;
        }

        // 2. Moderate top-right attention (expected: 15-25%)
        if ($topRightRatio > 0.15 && $topRightRatio < 0.25) {
            $fScore += 0.2;
        }

        // 3. Strong middle-left attention (expected: >20%)
        if ($middleLeftRatio > 0.2) {
            $fScore += 0.3;
        } elseif ($middleLeftRatio > 0.15) {
            $fScore += 0.15;
        }

        // 4. Low bottom-right attention (expected: <10%)
        if ($bottomRightRatio < 0.1) {
            $fScore += 0.1;
        }

        return min(1.0, $fScore);
    }

    /**
     * Detect Z-Pattern scanning behavior
     * 
     * Z-Pattern characteristics:
     * 1. Top-left to top-right (horizontal scan)
     * 2. Diagonal to bottom-left
     * 3. Bottom-left to bottom-right (horizontal scan)
     * 
     * Common in landing pages with less text
     * 
     * @param array $data Heatmap data
     * @return float Score 0-1
     */
    public function detectZPattern(array $data): bool|float
    {
        if (empty($data)) {
            return 0.0;
        }

        // Sort by timestamp to get sequence
        usort($data, function ($a, $b) {
            $timeA = $a['timestamp'] ?? 0;
            $timeB = $b['timestamp'] ?? 0;
            return $timeA <=> $timeB;
        });

        $zScore = 0;
        $totalPoints = count($data);

        // Divide into sections
        $sectionSize = ceil($totalPoints / 4);
        
        $section1 = array_slice($data, 0, $sectionSize);           // First quarter
        $section2 = array_slice($data, $sectionSize, $sectionSize); // Second quarter
        $section3 = array_slice($data, $sectionSize * 2, $sectionSize); // Third quarter
        $section4 = array_slice($data, $sectionSize * 3);           // Last quarter

        // Check Z-Pattern sequence:
        // 1. Section 1: Should be top-left to top-right movement
        if ($this->isHorizontalMovement($section1, 'top')) {
            $zScore += 0.3;
        }

        // 2. Section 2-3: Should show diagonal movement (top-right to bottom-left)
        if ($this->isDiagonalMovement(array_merge($section2, $section3))) {
            $zScore += 0.4;
        }

        // 3. Section 4: Should be bottom-left to bottom-right movement
        if ($this->isHorizontalMovement($section4, 'bottom')) {
            $zScore += 0.3;
        }

        return min(1.0, $zScore);
    }

    /**
     * Detect scattered attention (indicator of confusion/cognitive overload)
     * 
     * Scattered characteristics:
     * - No clear pattern
     * - High variance in fixation points
     * - Short fixation durations
     * - Random jumps between areas
     * 
     * @param array $data Heatmap data
     * @return float Score 0-1 (higher = more scattered)
     */
    public function detectScatteredAttention(array $data): bool|float
    {
        if (empty($data)) {
            return 0.0;
        }

        $scatteredScore = 0;

        // 1. Calculate variance in X and Y coordinates
        $xCoords = array_column($data, 'x');
        $yCoords = array_column($data, 'y');

        $xVariance = $this->calculateVariance($xCoords);
        $yVariance = $this->calculateVariance($yCoords);

        // High variance = scattered (normalize to 0-1)
        $varianceScore = min(1, ($xVariance + $yVariance) / 1000000);
        $scatteredScore += $varianceScore * 0.4;

        // 2. Check for short fixation durations (< 200ms)
        $shortFixations = 0;
        foreach ($data as $point) {
            $duration = $point['duration_ms'] ?? 0;
            if ($duration < 200) {
                $shortFixations++;
            }
        }
        $shortFixationRatio = $shortFixations / count($data);
        $scatteredScore += $shortFixationRatio * 0.3;

        // 3. Calculate jump distance between consecutive points
        $totalJumpDistance = 0;
        for ($i = 1; $i < count($data); $i++) {
            $prevX = $data[$i - 1]['x'] ?? 0;
            $prevY = $data[$i - 1]['y'] ?? 0;
            $currX = $data[$i]['x'] ?? 0;
            $currY = $data[$i]['y'] ?? 0;

            $distance = sqrt(pow($currX - $prevX, 2) + pow($currY - $prevY, 2));
            $totalJumpDistance += $distance;
        }

        $avgJumpDistance = $totalJumpDistance / (count($data) - 1);
        // Large jumps = scattered (normalize to 0-1)
        $jumpScore = min(1, $avgJumpDistance / 1000);
        $scatteredScore += $jumpScore * 0.3;

        return min(1.0, $scatteredScore);
    }

    /**
     * Calculate attention efficiency
     * 
     * Efficiency = (Focused attention time) / (Total time)
     * Higher efficiency = better UX
     * 
     * @param array $data Heatmap data
     * @return float Efficiency score 0-1
     */
    public function calculateAttentionEfficiency(array $data): float
    {
        if (empty($data)) {
            return 0.0;
        }

        $totalDuration = 0;
        $focusedDuration = 0;

        foreach ($data as $point) {
            $duration = $point['duration_ms'] ?? 0;
            $intensity = $point['intensity'] ?? 0;

            $totalDuration += $duration;

            // Consider "focused" if intensity > 0.5 and duration > 300ms
            if ($intensity > 0.5 && $duration > 300) {
                $focusedDuration += $duration;
            }
        }

        if ($totalDuration == 0) {
            return 0.0;
        }

        return $focusedDuration / $totalDuration;
    }

    /**
     * Identify top focus points (hotspots)
     * 
     * @param array $data
     * @return array Top 5 focus points
     */
    private function identifyFocusPoints(array $data): array
    {
        // Sort by intensity * duration
        usort($data, function ($a, $b) {
            $scoreA = ($a['intensity'] ?? 0) * ($a['duration_ms'] ?? 0);
            $scoreB = ($b['intensity'] ?? 0) * ($b['duration_ms'] ?? 0);
            return $scoreB <=> $scoreA;
        });

        // Get top 5
        $topPoints = array_slice($data, 0, 5);

        return array_map(function ($point) {
            return [
                'x' => $point['x'] ?? 0,
                'y' => $point['y'] ?? 0,
                'score' => round(($point['intensity'] ?? 0) * ($point['duration_ms'] ?? 0), 2),
            ];
        }, $topPoints);
    }

    /**
     * Calculate viewport coverage percentage
     * 
     * @param array $data
     * @return float Coverage 0-1
     */
    private function calculateViewportCoverage(array $data): float
    {
        if (empty($data)) {
            return 0.0;
        }

        // Divide viewport into grid (e.g., 10x10 = 100 cells)
        $gridSize = 10;
        $cells = [];

        foreach ($data as $point) {
            $x = $point['x'] ?? 0;
            $y = $point['y'] ?? 0;

            // Map to grid cell (assuming 1920x1080 viewport)
            $cellX = min($gridSize - 1, floor($x / 192));
            $cellY = min($gridSize - 1, floor($y / 108));

            $cellKey = "{$cellX}-{$cellY}";
            $cells[$cellKey] = true;
        }

        // Coverage = unique cells / total cells
        return count($cells) / ($gridSize * $gridSize);
    }

    /**
     * Calculate average fixation duration
     * 
     * @param array $data
     * @return float Average duration in ms
     */
    private function calculateAverageFixation(array $data): float
    {
        if (empty($data)) {
            return 0.0;
        }

        $totalDuration = 0;
        foreach ($data as $point) {
            $totalDuration += $point['duration_ms'] ?? 0;
        }

        return round($totalDuration / count($data), 2);
    }

    /**
     * Generate recommendations based on pattern analysis
     * 
     * @param string $pattern
     * @param float $confidence
     * @param float $efficiency
     * @return array
     */
    private function generatePatternRecommendations(string $pattern, float $confidence, float $efficiency): array
    {
        $recommendations = [];

        if ($pattern === 'scattered') {
            $recommendations[] = 'CRITICAL: Users show scattered attention - high cognitive load detected';
            $recommendations[] = 'Simplify layout and reduce visual clutter';
            $recommendations[] = 'Add clear visual hierarchy with size/color differentiation';
            $recommendations[] = 'Consider progressive disclosure for complex content';
        }

        if ($pattern === 'f-pattern' && $confidence > 0.7) {
            $recommendations[] = 'Excellent F-Pattern detected - maintain current layout';
            $recommendations[] = 'Ensure CTAs are positioned in high-attention zones (top-left, middle-left)';
        }

        if ($pattern === 'f-pattern' && $confidence < 0.5) {
            $recommendations[] = 'Weak F-Pattern - consider restructuring content flow';
            $recommendations[] = 'Move important content to top-left zone';
        }

        if ($pattern === 'z-pattern' && $confidence > 0.7) {
            $recommendations[] = 'Z-Pattern detected - suitable for less text-heavy content';
            $recommendations[] = 'Place CTAs along Z-path diagonal';
        }

        if ($efficiency < self::EFFICIENCY_ACCEPTABLE) {
            $recommendations[] = 'Low attention efficiency - users struggling to focus';
            $recommendations[] = 'Reduce number of competing visual elements';
            $recommendations[] = 'Increase white space around important content';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Attention pattern is acceptable - monitor for improvements';
        }

        return $recommendations;
    }

    /**
     * Check if movement is horizontal
     * 
     * @param array $section
     * @param string $zone 'top' or 'bottom'
     * @return bool
     */
    private function isHorizontalMovement(array $section, string $zone): bool
    {
        if (count($section) < 2) {
            return false;
        }

        $firstPoint = reset($section);
        $lastPoint = end($section);

        $xDelta = abs(($lastPoint['x'] ?? 0) - ($firstPoint['x'] ?? 0));
        $yDelta = abs(($lastPoint['y'] ?? 0) - ($firstPoint['y'] ?? 0));

        // Horizontal if X movement > 3x Y movement
        return $xDelta > ($yDelta * 3);
    }

    /**
     * Check if movement is diagonal
     * 
     * @param array $section
     * @return bool
     */
    private function isDiagonalMovement(array $section): bool
    {
        if (count($section) < 2) {
            return false;
        }

        $firstPoint = reset($section);
        $lastPoint = end($section);

        $xDelta = abs(($lastPoint['x'] ?? 0) - ($firstPoint['x'] ?? 0));
        $yDelta = abs(($lastPoint['y'] ?? 0) - ($firstPoint['y'] ?? 0));

        // Diagonal if X and Y movements are similar (ratio 0.5 - 2.0)
        if ($yDelta == 0) return false;
        $ratio = $xDelta / $yDelta;

        return $ratio > 0.5 && $ratio < 2.0;
    }

    /**
     * Calculate variance of array
     * 
     * @param array $values
     * @return float
     */
    private function calculateVariance(array $values): float
    {
        if (empty($values)) {
            return 0.0;
        }

        $mean = array_sum($values) / count($values);
        $variance = 0;

        foreach ($values as $value) {
            $variance += pow($value - $mean, 2);
        }

        return $variance / count($values);
    }
}
