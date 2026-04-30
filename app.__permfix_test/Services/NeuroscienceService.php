<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

/**
 * NeuroscienceService
 *
 * Service untuk menganalisis dan mengoptimalkan user experience
 * berdasarkan prinsip neuroscience dan cognitive psychology.
 *
 * Principles:
 * - Miller's Law: 7±2 chunk limit
 * - Hick's Law: Decision time optimization
 * - F-Pattern: Visual attention flow
 * - Color Psychology: Emotional response mapping
 *
 * @author Bizmark.ID Development Team
 *
 * @version 1.0.0
 */
class NeuroscienceService
{
    /**
     * Maximum cognitive load score (threshold)
     * Above this = cognitive overload
     */
    const MAX_COGNITIVE_LOAD = 30;

    /**
     * Miller's Law: Ideal chunk size
     */
    const IDEAL_CHUNK_SIZE = 7;

    /**
     * Visual weight constants
     */
    const WEIGHT_MULTIPLIERS = [
        'size' => 0.8,
        'color' => 0.6,
        'position' => 0.7,
        'contrast' => 0.7,
        'motion' => 1.5,
    ];

    /**
     * Analyze cognitive load dari array elements
     *
     * Cognitive Load Formula:
     * Load = Σ(element_complexity * visual_weight * position_factor)
     *
     * @param  array  $elements  Array of UI elements with properties
     * @return array {
     *               'score': float,
     *               'status': string,
     *               'recommendations': array,
     *               'breakdown': array
     *               }
     */
    public function analyzeCognitiveLoad(array $elements): array
    {
        if (empty($elements)) {
            return [
                'score' => 0,
                'status' => 'empty',
                'recommendations' => ['Add content to analyze'],
                'breakdown' => [],
            ];
        }

        $totalLoad = 0;
        $breakdown = [];

        foreach ($elements as $element) {
            $complexity = $this->calculateElementComplexity($element);
            $visualWeight = $this->calculateVisualWeight($element);
            $positionFactor = $this->getPositionWeight($element['position'] ?? 'middle');

            $elementLoad = $complexity * $visualWeight * $positionFactor;
            $totalLoad += $elementLoad;

            $breakdown[] = [
                'element' => $element['type'] ?? 'unknown',
                'complexity' => round($complexity, 2),
                'visual_weight' => round($visualWeight, 2),
                'position_factor' => round($positionFactor, 2),
                'load' => round($elementLoad, 2),
            ];
        }

        // Normalize score (0-100 scale)
        // Average load per element, scaled appropriately
        $avgLoadPerElement = $totalLoad / count($elements);
        $normalizedScore = min(99.9, $avgLoadPerElement * 5); // Adjusted multiplier

        return [
            'score' => round($normalizedScore, 2),
            'status' => $this->getCognitiveLoadStatus($normalizedScore),
            'recommendations' => $this->generateRecommendations($normalizedScore, $breakdown),
            'breakdown' => $breakdown,
            'total_elements' => count($elements),
            'average_load_per_element' => round($totalLoad / count($elements), 2),
        ];
    }

    /**
     * Optimize attention flow dengan reordering elements
     *
     * Strategy:
     * 1. Sort by visual weight (descending)
     * 2. Apply F-Pattern positioning
     * 3. Balance cognitive load per section
     *
     * @return array Optimized elements with new positions
     */
    public function optimizeAttentionFlow(array $elements): array
    {
        if (empty($elements)) {
            return [];
        }

        // Calculate visual weight untuk setiap element
        $weightedElements = array_map(function ($element) {
            $element['calculated_weight'] = $this->calculateVisualWeight($element);

            return $element;
        }, $elements);

        // Sort by visual weight (descending)
        usort($weightedElements, function ($a, $b) {
            return $b['calculated_weight'] <=> $a['calculated_weight'];
        });

        // Apply F-Pattern positioning
        $optimized = [];
        $fZones = ['top-left', 'top-right', 'middle-left', 'middle-right', 'bottom-left', 'bottom-right'];
        $zoneIndex = 0;

        foreach ($weightedElements as $element) {
            $element['optimized_position'] = $fZones[$zoneIndex % count($fZones)];
            $element['attention_priority'] = $zoneIndex + 1;
            $optimized[] = $element;
            $zoneIndex++;
        }

        return $optimized;
    }

    /**
     * Calculate visual weight of an element
     *
     * Factors:
     * - Size (width * height)
     * - Color contrast
     * - Position in viewport
     * - Motion/Animation
     * - Typography weight
     *
     * @param  array|object  $element
     * @return float Visual weight score (0-10)
     */
    public function calculateVisualWeight($element): float
    {
        $element = (array) $element;

        $weight = 0;

        // Size factor (0-2)
        if (isset($element['width']) && isset($element['height'])) {
            $size = ($element['width'] * $element['height']) / 50000; // Adjusted normalization
            $weight += min(2, $size) * self::WEIGHT_MULTIPLIERS['size'];
        }

        // Color contrast factor (0-2)
        if (isset($element['color'])) {
            $colorWeight = $this->getColorContrastWeight($element['color']);
            $weight += $colorWeight * self::WEIGHT_MULTIPLIERS['color'];
        }

        // Position factor (0-2)
        if (isset($element['position'])) {
            $positionWeight = $this->getPositionWeight($element['position']);
            $weight += $positionWeight * self::WEIGHT_MULTIPLIERS['position'];
        }

        // Motion/Animation factor (0-2)
        if (isset($element['animated']) && $element['animated'] === true) {
            $weight += 2 * self::WEIGHT_MULTIPLIERS['motion'];
        }

        // Typography weight (0-1.5)
        if (isset($element['font_weight'])) {
            $fontWeight = $element['font_weight'] / 400; // Normalize (400 = normal)
            $weight += min(1.5, $fontWeight);
        }

        return min(10, $weight); // Cap at 10
    }

    /**
     * Simplify decision-making dengan mengurangi options
     *
     * Hick's Law: RT = a + b * log2(n)
     * Dimana n = number of choices
     *
     * @param  array  $options  Array of decision options
     * @return array Simplified options (max 7 by Miller's Law)
     */
    public function simplifyDecisions(array $options): array
    {
        if (count($options) <= self::IDEAL_CHUNK_SIZE) {
            return [
                'original_count' => count($options),
                'optimized_count' => count($options),
                'options' => $options,
                'decision_time_saved_ms' => 0,
                'status' => 'optimal',
            ];
        }

        // Calculate decision time (Hick's Law)
        $originalTime = $this->calculateDecisionTime(count($options));

        // Sort by priority/importance (descending - highest first)
        usort($options, function ($a, $b) {
            $priorityA = $a['priority'] ?? 0;
            $priorityB = $b['priority'] ?? 0;

            return $priorityB <=> $priorityA; // Descending order
        });

        // Keep top 7 (Miller's Law)
        $simplified = array_slice($options, 0, self::IDEAL_CHUNK_SIZE);
        $optimizedTime = $this->calculateDecisionTime(count($simplified));

        return [
            'original_count' => count($options),
            'optimized_count' => count($simplified),
            'options' => $simplified,
            'removed_options' => array_slice($options, self::IDEAL_CHUNK_SIZE),
            'decision_time_saved_ms' => round($originalTime - $optimizedTime, 2),
            'status' => 'optimized',
        ];
    }

    /**
     * Get color contrast weight for visual attention
     *
     * High contrast colors (red, blue, bright yellow) = higher weight
     * Low contrast colors (gray, beige) = lower weight
     *
     * @param  string  $color  Hex color code or CSS variable
     * @return float Weight (0-2)
     */
    public function getColorContrastWeight($color): float
    {
        // Color psychology mapping
        $colorWeights = [
            // High attention colors
            '#C5221F' => 2.0,  // Danger red
            '#0A66C2' => 1.8,  // Primary blue
            '#B86B00' => 1.7,  // Warning amber
            '#057642' => 1.5,  // Success green

            // Medium attention colors
            '#378FE9' => 1.2,  // Light blue
            '#E8F5E9' => 0.8,  // Light green
            '#FFF3E0' => 0.7,  // Light amber

            // Low attention colors
            '#F3F6F8' => 0.3,  // Neutral gray
            '#FFFFFF' => 0.2,  // White
            '#E8ECEF' => 0.3,  // Light gray
        ];

        // Check direct match
        if (isset($colorWeights[$color])) {
            return $colorWeights[$color];
        }

        // Check if it's a CSS variable reference
        if (strpos($color, 'var(') === 0) {
            $configColors = Config::get('neuroscience.color_psychology.hex_values', []);
            foreach ($configColors as $key => $hexColor) {
                if (strpos($color, $key) !== false && isset($colorWeights[$hexColor])) {
                    return $colorWeights[$hexColor];
                }
            }
        }

        // Default: medium contrast
        return 1.0;
    }

    /**
     * Get position weight based on F-Pattern reading flow
     *
     * F-Pattern zones:
     * - Top-left: Highest attention (primary)
     * - Top-right: High attention
     * - Middle-left: Medium attention
     * - Middle-right: Low-medium attention
     * - Bottom: Lowest attention
     *
     * @param  string  $position  Position identifier
     * @return float Weight multiplier (0.5-2.0)
     */
    public function getPositionWeight($position): float
    {
        $weights = [
            'top-left' => 2.0,      // F-Pattern Zone 1 (primary)
            'top-right' => 1.5,     // F-Pattern Zone 2
            'middle-left' => 1.3,   // F-Pattern Zone 3
            'middle-right' => 1.0,  // Secondary attention
            'middle' => 1.0,        // Center (natural focus)
            'bottom-left' => 0.8,   // Lower attention
            'bottom-right' => 0.6,  // Lowest attention
            'bottom' => 0.7,        // Footer area
        ];

        return $weights[$position] ?? 1.0;
    }

    /**
     * Calculate element complexity
     *
     * @return float Complexity score (1-5)
     */
    private function calculateElementComplexity(array $element): float
    {
        $complexity = 1.0;

        // Text complexity
        if (isset($element['text'])) {
            $wordCount = str_word_count($element['text']);
            $complexity += min(2, $wordCount / 10);
        }

        // Interactive elements add complexity
        if (isset($element['interactive']) && $element['interactive']) {
            $complexity += 1.0;
        }

        // Nested children add complexity
        if (isset($element['children']) && is_array($element['children'])) {
            $complexity += min(2, count($element['children']) / 5);
        }

        return min(5, $complexity);
    }

    /**
     * Get cognitive load status label
     */
    private function getCognitiveLoadStatus(float $score): string
    {
        if ($score < 15) {
            return 'excellent';
        }
        if ($score < 25) {
            return 'good';
        }
        if ($score < 35) {
            return 'acceptable';
        }
        if ($score < 50) {
            return 'high';
        }

        return 'overload';
    }

    /**
     * Generate recommendations based on cognitive load
     */
    private function generateRecommendations(float $score, array $breakdown): array
    {
        $recommendations = [];

        if ($score > 40) {
            $recommendations[] = 'CRITICAL: Reduce number of elements by 30-50%';
            $recommendations[] = 'Apply progressive disclosure for non-essential content';
        }

        if ($score > 30) {
            $recommendations[] = 'Consider grouping related elements';
            $recommendations[] = 'Use accordion/tabs for complex content';
        }

        if ($score > 20) {
            $recommendations[] = 'Review visual hierarchy - reduce competing focal points';
        }

        // Check for high-load elements
        $highLoadElements = array_filter($breakdown, fn ($el) => $el['load'] > 5);
        if (count($highLoadElements) > 0) {
            $recommendations[] = sprintf(
                'Optimize %d high-complexity elements',
                count($highLoadElements)
            );
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Cognitive load is optimal - maintain current structure';
        }

        return $recommendations;
    }

    /**
     * Calculate decision time using Hick's Law
     *
     * Formula: RT = a + b * log2(n)
     * Where:
     * - a = base response time (200ms)
     * - b = processing time per bit (150ms)
     * - n = number of choices
     *
     * @return float Decision time in milliseconds
     */
    private function calculateDecisionTime(int $choiceCount): float
    {
        if ($choiceCount <= 1) {
            return 200; // Base response time
        }

        $a = 200; // Base response time (ms)
        $b = 150; // Processing time per bit (ms)

        return $a + ($b * log($choiceCount, 2));
    }
}
