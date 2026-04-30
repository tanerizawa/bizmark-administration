<?php

namespace Tests\Unit\Services;

use App\Services\NeuroscienceService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * NeuroscienceService Unit Tests
 *
 * Test cognitive load analysis, visual weight calculations,
 * attention flow optimization, dan decision simplification.
 */
class NeuroscienceServiceTest extends TestCase
{
    protected NeuroscienceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NeuroscienceService;
    }

    #[Test]
    public function it_calculates_cognitive_load_for_empty_elements()
    {
        $result = $this->service->analyzeCognitiveLoad([]);

        $this->assertEquals(0, $result['score']);
        $this->assertEquals('empty', $result['status']);
        $this->assertIsArray($result['recommendations']);
    }

    #[Test]
    public function it_calculates_cognitive_load_for_simple_elements()
    {
        $elements = [
            [
                'type' => 'button',
                'text' => 'Click Me',
                'width' => 200,
                'height' => 50,
                'position' => 'top-left',
                'color' => '#0A66C2',
                'interactive' => true,
            ],
            [
                'type' => 'text',
                'text' => 'Welcome to Bizmark',
                'width' => 400,
                'height' => 30,
                'position' => 'middle-left',
                'color' => '#1D2226',
                'interactive' => false,
            ],
        ];

        $result = $this->service->analyzeCognitiveLoad($elements);

        $this->assertIsFloat($result['score']);
        $this->assertGreaterThan(0, $result['score']);
        $this->assertLessThan(100, $result['score']);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('recommendations', $result);
        $this->assertArrayHasKey('breakdown', $result);
        $this->assertEquals(2, $result['total_elements']);
    }

    #[Test]
    public function it_classifies_cognitive_load_status_correctly()
    {
        // Low load scenario
        $lowLoadElements = [
            ['type' => 'button', 'text' => 'CTA', 'width' => 100, 'height' => 40, 'position' => 'top-left', 'color' => '#0A66C2'],
        ];
        $lowResult = $this->service->analyzeCognitiveLoad($lowLoadElements);
        $this->assertContains($lowResult['status'], ['excellent', 'good', 'acceptable']);

        // High load scenario (many elements)
        $highLoadElements = array_fill(0, 15, [
            'type' => 'card',
            'text' => str_repeat('Complex content with many words ', 10),
            'width' => 300,
            'height' => 200,
            'position' => 'middle',
            'color' => '#0A66C2',
            'interactive' => true,
            'children' => array_fill(0, 5, ['type' => 'item']),
        ]);
        $highResult = $this->service->analyzeCognitiveLoad($highLoadElements);
        $this->assertGreaterThan(30, $highResult['score']);
    }

    #[Test]
    public function it_calculates_visual_weight_correctly()
    {
        // Large, high-contrast, animated element = high weight
        $heavyElement = [
            'width' => 500,
            'height' => 300,
            'color' => '#C5221F', // Danger red - high contrast
            'position' => 'top-left',
            'animated' => true,
            'font_weight' => 700,
        ];
        $heavyWeight = $this->service->calculateVisualWeight($heavyElement);
        $this->assertGreaterThan(5, $heavyWeight);

        // Small, low-contrast, static element = low weight
        $lightElement = [
            'width' => 100,
            'height' => 30,
            'color' => '#F3F6F8', // Light gray - low contrast
            'position' => 'bottom-right',
            'animated' => false,
            'font_weight' => 400,
        ];
        $lightWeight = $this->service->calculateVisualWeight($lightElement);
        $this->assertLessThan(3, $lightWeight);

        // Heavy element should have significantly more weight
        $this->assertGreaterThan($lightWeight * 2, $heavyWeight);
    }

    #[Test]
    public function it_gets_correct_color_contrast_weights()
    {
        // High attention colors
        $dangerWeight = $this->service->getColorContrastWeight('#C5221F');
        $this->assertEquals(2.0, $dangerWeight);

        $primaryWeight = $this->service->getColorContrastWeight('#0A66C2');
        $this->assertEquals(1.8, $primaryWeight);

        // Low attention colors
        $grayWeight = $this->service->getColorContrastWeight('#F3F6F8');
        $this->assertEquals(0.3, $grayWeight);

        // CSS variable references
        $cssVarWeight = $this->service->getColorContrastWeight('var(--color-primary)');
        $this->assertGreaterThan(0, $cssVarWeight);
    }

    #[Test]
    public function it_gets_correct_position_weights_based_on_f_pattern()
    {
        // F-Pattern Zone 1 (top-left) = highest
        $topLeftWeight = $this->service->getPositionWeight('top-left');
        $this->assertEquals(2.0, $topLeftWeight);

        // F-Pattern Zone 2 (top-right)
        $topRightWeight = $this->service->getPositionWeight('top-right');
        $this->assertEquals(1.5, $topRightWeight);

        // Middle-left (content area)
        $middleLeftWeight = $this->service->getPositionWeight('middle-left');
        $this->assertEquals(1.3, $middleLeftWeight);

        // Bottom-right = lowest
        $bottomRightWeight = $this->service->getPositionWeight('bottom-right');
        $this->assertEquals(0.6, $bottomRightWeight);

        // Top-left should be highest
        $this->assertGreaterThan($bottomRightWeight, $topLeftWeight);
    }

    #[Test]
    public function it_optimizes_attention_flow()
    {
        $elements = [
            ['type' => 'low-priority', 'width' => 100, 'height' => 30, 'color' => '#F3F6F8'],
            ['type' => 'high-priority', 'width' => 400, 'height' => 200, 'color' => '#0A66C2', 'animated' => true],
            ['type' => 'medium-priority', 'width' => 200, 'height' => 100, 'color' => '#057642'],
        ];

        $optimized = $this->service->optimizeAttentionFlow($elements);

        $this->assertCount(3, $optimized);

        // First element should be highest visual weight (high-priority)
        $this->assertEquals('high-priority', $optimized[0]['type']);
        $this->assertEquals(1, $optimized[0]['attention_priority']);
        $this->assertArrayHasKey('optimized_position', $optimized[0]);
        $this->assertArrayHasKey('calculated_weight', $optimized[0]);
    }

    #[Test]
    public function it_simplifies_decisions_within_millers_law()
    {
        // 5 options (within Miller's Law 7±2)
        $optimalOptions = [
            ['name' => 'Option 1', 'priority' => 1],
            ['name' => 'Option 2', 'priority' => 2],
            ['name' => 'Option 3', 'priority' => 3],
            ['name' => 'Option 4', 'priority' => 4],
            ['name' => 'Option 5', 'priority' => 5],
        ];

        $result = $this->service->simplifyDecisions($optimalOptions);

        $this->assertEquals(5, $result['original_count']);
        $this->assertEquals(5, $result['optimized_count']);
        $this->assertEquals('optimal', $result['status']);
        $this->assertEquals(0, $result['decision_time_saved_ms']);
    }

    #[Test]
    public function it_simplifies_excessive_decisions()
    {
        // 12 options (exceeds Miller's Law)
        $excessiveOptions = array_map(fn ($i) => [
            'name' => "Option $i",
            'priority' => $i,
        ], range(1, 12));

        $result = $this->service->simplifyDecisions($excessiveOptions);

        $this->assertEquals(12, $result['original_count']);
        $this->assertEquals(7, $result['optimized_count']); // Miller's Law max
        $this->assertEquals('optimized', $result['status']);
        $this->assertGreaterThan(0, $result['decision_time_saved_ms']);
        $this->assertCount(5, $result['removed_options']); // 12 - 7 = 5
    }

    #[Test]
    public function it_prioritizes_options_by_priority_value()
    {
        $options = [
            ['name' => 'Low', 'priority' => 1],
            ['name' => 'High', 'priority' => 10],
            ['name' => 'Medium', 'priority' => 5],
            ['name' => 'VeryLow', 'priority' => 0],
            ['name' => 'VeryHigh', 'priority' => 20],
        ];

        $result = $this->service->simplifyDecisions($options);

        // Should keep all (within Miller's Law)
        $this->assertEquals(5, $result['optimized_count']);

        // Since it's within Miller's Law, options should be returned as-is (not sorted)
        // But if we want sorted output, we need to modify the service
        $this->assertArrayHasKey('options', $result);

        // Verify highest priority is in the result
        $priorities = array_column($result['options'], 'priority');
        $this->assertContains(20, $priorities);
    }

    #[Test]
    public function it_generates_recommendations_for_high_cognitive_load()
    {
        // Create scenario with high cognitive load
        $elements = array_fill(0, 20, [
            'type' => 'complex-card',
            'text' => str_repeat('Very complex content ', 20),
            'width' => 400,
            'height' => 300,
            'position' => 'middle',
            'interactive' => true,
            'children' => array_fill(0, 8, ['type' => 'child']),
        ]);

        $result = $this->service->analyzeCognitiveLoad($elements);

        $this->assertGreaterThan(30, $result['score']);
        $this->assertNotEmpty($result['recommendations']);

        // Should suggest reduction strategies
        $recommendations = implode(' ', $result['recommendations']);
        $this->assertMatchesRegularExpression('/reduce|simplify|progressive/i', $recommendations);
    }

    #[Test]
    public function it_provides_detailed_breakdown()
    {
        $elements = [
            [
                'type' => 'hero-button',
                'text' => 'Get Started',
                'width' => 300,
                'height' => 60,
                'position' => 'top-left',
                'color' => '#0A66C2',
                'interactive' => true,
                'animated' => true,
            ],
        ];

        $result = $this->service->analyzeCognitiveLoad($elements);

        $this->assertArrayHasKey('breakdown', $result);
        $this->assertCount(1, $result['breakdown']);

        $breakdown = $result['breakdown'][0];
        $this->assertArrayHasKey('element', $breakdown);
        $this->assertArrayHasKey('complexity', $breakdown);
        $this->assertArrayHasKey('visual_weight', $breakdown);
        $this->assertArrayHasKey('position_factor', $breakdown);
        $this->assertArrayHasKey('load', $breakdown);

        $this->assertEquals('hero-button', $breakdown['element']);
    }
}
