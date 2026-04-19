<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Neuroscience UI/UX Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration untuk neuroscience-based user experience optimization.
    | Berdasarkan prinsip cognitive psychology, visual perception, dan
    | behavioral science untuk meningkatkan conversion dan user satisfaction.
    |
    | Principles:
    | - Miller's Law: 7±2 chunk information limit
    | - Hick's Law: Decision time optimization
    | - Fitts's Law: Target size & distance
    | - Gestalt Principles: Visual grouping
    | - F-Pattern Reading: Natural eye flow
    |
    */

    /*
    |--------------------------------------------------------------------------
    | CSS Token System Integration
    |--------------------------------------------------------------------------
    |
    | Referensi ke global CSS token system untuk single source of truth.
    | Semua colors, spacing, dan visual properties defined di tokens.css
    |
    */
    'css_tokens_file' => 'css/tokens.css',

    /*
    |--------------------------------------------------------------------------
    | Color Psychology Mapping
    |--------------------------------------------------------------------------
    |
    | Mapping warna ke emotional responses dan use cases.
    | Colors mempengaruhi user perception, trust, dan action.
    |
    | References:
    | - Primary: LinkedIn Professional Blue (trust, stability)
    | - Success: Forest Green (achievement, go-ahead)
    | - Warning: Warm Amber (caution, attention)
    | - Danger: Soft Red (error, stop)
    | - Neutral: Professional Gray (calm, professional)
    |
    */
    'color_psychology' => [
        // CSS Variable References (untuk frontend)
        'css_variables' => [
            'primary' => 'var(--color-primary)',
            'primary_dark' => 'var(--color-primary-dark)',
            'primary_darker' => 'var(--color-primary-darker)',
            'primary_light' => 'var(--color-primary-light)',
            'primary_lighter' => 'var(--color-primary-lighter)',

            'success' => 'var(--color-success)',
            'success_dark' => 'var(--color-success-dark)',
            'success_light' => 'var(--color-success-light)',
            'success_lighter' => 'var(--color-success-lighter)',

            'warning' => 'var(--color-warning)',
            'warning_dark' => 'var(--color-warning-dark)',
            'warning_light' => 'var(--color-warning-light)',
            'warning_lighter' => 'var(--color-warning-lighter)',

            'danger' => 'var(--color-danger)',
            'danger_dark' => 'var(--color-danger-dark)',
            'danger_light' => 'var(--color-danger-light)',
            'danger_lighter' => 'var(--color-danger-lighter)',

            'neutral' => 'var(--color-neutral)',
            'neutral_dark' => 'var(--color-neutral-dark)',
            'neutral_light' => 'var(--color-neutral-light)',
            'neutral_lighter' => 'var(--color-neutral-lighter)',

            'text_primary' => 'var(--color-text-primary)',
            'text_secondary' => 'var(--color-text-secondary)',
            'text_tertiary' => 'var(--color-text-tertiary)',
            'text_disabled' => 'var(--color-text-disabled)',
            'text_inverse' => 'var(--color-text-inverse)',
        ],

        // Hex Values (untuk backend processing)
        'hex_values' => [
            'primary' => '#0A66C2',
            'primary_dark' => '#004182',
            'primary_darker' => '#003161',
            'primary_light' => '#378FE9',
            'primary_lighter' => '#E7F3FF',

            'success' => '#057642',
            'success_dark' => '#045D34',
            'success_light' => '#E8F5E9',
            'success_lighter' => '#F1F8F4',

            'warning' => '#B86B00',
            'warning_dark' => '#8F5500',
            'warning_light' => '#FFF3E0',
            'warning_lighter' => '#FFF8F0',

            'danger' => '#C5221F',
            'danger_dark' => '#9E1B19',
            'danger_light' => '#FFEBEE',
            'danger_lighter' => '#FFF5F5',

            'neutral' => '#5E6D7A',
            'neutral_dark' => '#3D4852',
            'neutral_light' => '#E8ECEF',
            'neutral_lighter' => '#F3F6F8',

            'text_primary' => '#1D2226',
            'text_secondary' => '#5E6D7A',
            'text_tertiary' => '#8B9196',
            'text_disabled' => '#B0B8C1',
            'text_inverse' => '#FFFFFF',
        ],

        // Emotional Associations
        'emotions' => [
            'primary' => ['trust', 'professional', 'stability', 'corporate'],
            'success' => ['achievement', 'approval', 'growth', 'positive'],
            'warning' => ['caution', 'attention', 'important', 'pending'],
            'danger' => ['error', 'critical', 'stop', 'urgent'],
            'neutral' => ['calm', 'balanced', 'professional', 'stable'],
        ],

        // Use Cases
        'use_cases' => [
            'primary' => ['cta_buttons', 'links', 'brand_elements', 'active_states'],
            'success' => ['success_messages', 'completed_steps', 'positive_feedback', 'checkmarks'],
            'warning' => ['warnings', 'pending_actions', 'important_notices', 'in_progress'],
            'danger' => ['errors', 'destructive_actions', 'critical_alerts', 'required_fields'],
            'neutral' => ['secondary_actions', 'disabled_states', 'backgrounds', 'borders'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cognitive Load Thresholds (Miller's Law)
    |--------------------------------------------------------------------------
    |
    | Optimal: 7±2 items (5-9 range)
    | Human working memory capacity adalah 7±2 chunks.
    | Lebih dari ini = cognitive overload.
    |
    */
    'cognitive_load' => [
        'max_menu_items' => 7,              // Navigation menu items
        'max_form_fields_visible' => 5,     // Form fields tanpa scroll
        'max_service_cards_row' => 3,       // Cards per row
        'ideal_paragraph_length' => 3,      // Sentences per paragraph
        'max_choices_simple' => 5,          // Simple decision choices
        'max_choices_complex' => 3,         // Complex decision choices
        'max_breadcrumb_depth' => 4,        // Breadcrumb levels
        'max_tabs' => 6,                    // Tab items
        'max_bullet_points' => 7,           // List items before grouping

        // Scoring thresholds
        'thresholds' => [
            'excellent' => 15,
            'good' => 25,
            'acceptable' => 30,
            'high' => 40,
            'overload' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Response Time Targets (Neural Processing)
    |--------------------------------------------------------------------------
    |
    | Based on human perception thresholds:
    | - <100ms: Instant (feels seamless)
    | - <300ms: Immediate (optimal neural response)
    | - <1000ms: Acceptable (noticeable but ok)
    | - >1000ms: Slow (requires loading indicator)
    |
    */
    'response_time' => [
        'instant' => 100,      // ms - Feels seamless
        'immediate' => 300,    // ms - Optimal neural response
        'acceptable' => 1000,  // ms - Noticeable delay
        'slow' => 3000,        // ms - Requires progress indicator

        // Classifications
        'classifications' => [
            'instant' => ['label' => 'Instant', 'color' => 'success'],
            'immediate' => ['label' => 'Immediate', 'color' => 'success'],
            'acceptable' => ['label' => 'Acceptable', 'color' => 'warning'],
            'slow' => ['label' => 'Slow', 'color' => 'danger'],
        ],

        // Monitoring
        'enable_monitoring' => env('NEUROSCIENCE_MONITORING', true),
        'log_slow_responses' => env('LOG_SLOW_RESPONSES', true),
        'log_threshold' => 300, // ms - Log responses slower than this
    ],

    /*
    |--------------------------------------------------------------------------
    | Visual Hierarchy Weights
    |--------------------------------------------------------------------------
    |
    | Weight multipliers untuk visual attention.
    | Digunakan oleh NeuroscienceService untuk calculate visual weight.
    |
    */
    'visual_weights' => [
        'size' => 1.5,          // Bigger = more attention
        'color' => 1.3,         // Brighter colors = more attention
        'position' => 1.2,      // Top-left = most attention
        'contrast' => 1.4,      // High contrast = more attention
        'motion' => 2.0,        // Animation = highest attention
        'typography' => 1.1,    // Bold text = more attention
    ],

    /*
    |--------------------------------------------------------------------------
    | F-Pattern Reading Zones
    |--------------------------------------------------------------------------
    |
    | Position weights based on F-Pattern eye tracking research.
    | Users spend most time in top-left, then scan horizontally,
    | then scan down left side.
    |
    */
    'f_pattern' => [
        'zones' => [
            'top_left' => [
                'weight' => 2.0,
                'attention_percentage' => 35,
                'use_for' => ['logo', 'brand', 'primary_cta', 'headline'],
            ],
            'top_right' => [
                'weight' => 1.5,
                'attention_percentage' => 20,
                'use_for' => ['navigation', 'login', 'secondary_cta'],
            ],
            'middle_left' => [
                'weight' => 1.3,
                'attention_percentage' => 25,
                'use_for' => ['main_content', 'key_features', 'benefits'],
            ],
            'middle_right' => [
                'weight' => 1.0,
                'attention_percentage' => 10,
                'use_for' => ['supporting_content', 'sidebar', 'ads'],
            ],
            'bottom_left' => [
                'weight' => 0.8,
                'attention_percentage' => 7,
                'use_for' => ['additional_info', 'related_links'],
            ],
            'bottom_right' => [
                'weight' => 0.6,
                'attention_percentage' => 3,
                'use_for' => ['footer_links', 'legal', 'copyright'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fitts's Law Configuration
    |--------------------------------------------------------------------------
    |
    | Target size and distance optimization for clickable elements.
    | Formula: Time = a + b * log2(Distance/Width + 1)
    |
    */
    'fitts_law' => [
        'min_touch_target' => 44,       // px - Apple HIG minimum
        'comfortable_target' => 48,     // px - Optimal size
        'ideal_spacing' => 8,           // px - Between targets
        'desktop_target' => 32,         // px - Mouse target
        'mobile_target' => 44,          // px - Touch target

        // Button sizes (by importance)
        'button_sizes' => [
            'primary' => ['width' => 160, 'height' => 48],    // Main CTA
            'secondary' => ['width' => 140, 'height' => 44],  // Secondary action
            'tertiary' => ['width' => 120, 'height' => 40],   // Subtle action
            'small' => ['width' => 100, 'height' => 36],      // Compact action
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Typography Scale (Modular Scale 1.25)
    |--------------------------------------------------------------------------
    |
    | Font sizes based on modular scale untuk visual harmony.
    | Base: 16px, Ratio: 1.25 (Major Third)
    |
    */
    'typography' => [
        'scale' => 1.25,
        'base_size' => 16,      // px

        'sizes' => [
            'xs' => 12,         // 16 / 1.25
            'sm' => 14,         // 16 / 1.14
            'base' => 16,       // Base
            'lg' => 18,         // 16 * 1.125
            'xl' => 20,         // 16 * 1.25
            '2xl' => 24,        // 16 * 1.5
            '3xl' => 30,        // 16 * 1.875
            '4xl' => 36,        // 16 * 2.25
            '5xl' => 48,        // 16 * 3
        ],

        'line_heights' => [
            'tight' => 1.25,
            'normal' => 1.5,
            'relaxed' => 1.75,
        ],

        'weights' => [
            'normal' => 400,
            'medium' => 500,
            'semibold' => 600,
            'bold' => 700,
        ],

        // Readability
        'max_line_length' => 75,        // characters for optimal readability
        'paragraph_spacing' => 1.5,     // em
    ],

    /*
    |--------------------------------------------------------------------------
    | Animation & Transitions
    |--------------------------------------------------------------------------
    |
    | Timing based on neural processing speeds.
    | Too fast = jarring, Too slow = sluggish
    |
    */
    'animation' => [
        'duration' => [
            'instant' => 50,        // ms - Micro-interactions
            'fast' => 150,          // ms - Hover states
            'base' => 250,          // ms - Optimal (neural response)
            'slow' => 350,          // ms - Complex animations
            'slower' => 500,        // ms - Page transitions
        ],

        'easing' => [
            'ease_in_out' => 'cubic-bezier(0.4, 0, 0.2, 1)',
            'ease_out' => 'cubic-bezier(0, 0, 0.2, 1)',
            'ease_in' => 'cubic-bezier(0.4, 0, 1, 1)',
            'bounce' => 'cubic-bezier(0.68, -0.55, 0.265, 1.55)',
        ],

        // Accessibility
        'respect_prefers_reduced_motion' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Progressive Disclosure
    |--------------------------------------------------------------------------
    |
    | Show information gradually untuk reduce cognitive load.
    |
    */
    'progressive_disclosure' => [
        'form_steps' => [
            'max_fields_per_step' => 5,
            'show_progress_indicator' => true,
            'allow_step_navigation' => true,
        ],

        'content' => [
            'initial_visible_items' => 3,
            'load_more_increment' => 6,
            'use_skeleton_screens' => true,
        ],

        'accordion' => [
            'allow_multiple_open' => false,     // Force focus
            'auto_collapse_siblings' => true,   // Reduce clutter
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Attention Analytics
    |--------------------------------------------------------------------------
    |
    | Track user attention patterns untuk data-driven optimization.
    |
    */
    'analytics' => [
        'track_attention' => env('TRACK_ATTENTION', true),
        'heatmap_enabled' => env('HEATMAP_ENABLED', false),
        'session_recording' => env('SESSION_RECORDING', false),

        'metrics' => [
            'cognitive_load_score',
            'attention_pattern',        // F-Pattern, Z-Pattern, Scattered
            'attention_efficiency',
            'average_fixation_duration',
            'viewport_coverage',
            'scroll_depth',
            'rage_clicks',              // Frustration indicator
            'time_to_first_interaction',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | A/B Testing Variants
    |--------------------------------------------------------------------------
    |
    | Neuroscience-based A/B test configurations.
    |
    */
    'ab_testing' => [
        'enabled' => env('AB_TESTING_ENABLED', false),

        'variants' => [
            'cta_position' => ['top_left', 'middle_left', 'bottom_left'],
            'cta_color' => ['primary', 'success', 'warning'],
            'headline_length' => ['short', 'medium', 'long'],
            'form_steps' => [1, 2, 3],
            'card_layout' => ['horizontal', 'vertical', 'grid'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Accessibility (WCAG 2.1 AA Compliance)
    |--------------------------------------------------------------------------
    |
    | Ensure neuroscience optimization doesn't compromise accessibility.
    |
    */
    'accessibility' => [
        'contrast_ratio_normal' => 4.5,     // WCAG AA for normal text
        'contrast_ratio_large' => 3.0,      // WCAG AA for large text
        'focus_visible' => true,
        'skip_to_content' => true,
        'aria_labels' => true,
        'keyboard_navigation' => true,

        // Motion
        'prefers_reduced_motion' => true,
        'disable_parallax_on_reduce_motion' => true,

        // Color blindness
        'never_use_color_alone' => true,    // Always combine with icons/text
    ],

];
