/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        // ============================================
        // NEUROSCIENCE COLOR SYSTEM (CSS Tokens)
        // Using CSS Custom Properties from tokens.css
        // Single Source of Truth - No Hardcoding
        // ============================================
        
        // Primary Colors (LinkedIn Blue - Trust, Professional)
        primary: {
          DEFAULT: 'var(--color-primary)',           // #0A66C2
          dark: 'var(--color-primary-dark)',         // #004182
          darker: 'var(--color-primary-darker)',     // #003161
          light: 'var(--color-primary-light)',       // #378FE9
          lighter: 'var(--color-primary-lighter)',   // #E7F3FF
        },
        
        // Success Colors (Muted Green - Achievement, Approval)
        success: {
          DEFAULT: 'var(--color-success)',           // #057642
          dark: 'var(--color-success-dark)',         // #045D34
          light: 'var(--color-success-light)',       // #E8F5E9
          lighter: 'var(--color-success-lighter)',   // #F1F8F4
        },
        
        // Warning Colors (Warm Amber - Caution, Attention)
        warning: {
          DEFAULT: 'var(--color-warning)',           // #B86B00
          dark: 'var(--color-warning-dark)',         // #8F5500
          light: 'var(--color-warning-light)',       // #FFF3E0
          lighter: 'var(--color-warning-lighter)',   // #FFF8F0
        },
        
        // Danger Colors (Soft Red - Error, Critical)
        danger: {
          DEFAULT: 'var(--color-danger)',            // #C5221F
          dark: 'var(--color-danger-dark)',          // #9E1B19
          light: 'var(--color-danger-light)',        // #FFEBEE
          lighter: 'var(--color-danger-lighter)',    // #FFF5F5
        },
        
        // Neutral Colors (Professional Gray)
        neutral: {
          DEFAULT: 'var(--color-neutral)',           // #5E6D7A
          dark: 'var(--color-neutral-dark)',         // #3D4852
          light: 'var(--color-neutral-light)',       // #E8ECEF
          lighter: 'var(--color-neutral-lighter)',   // #F3F6F8
        },
        
        // Text Colors
        text: {
          primary: 'var(--color-text-primary)',      // #1D2226
          secondary: 'var(--color-text-secondary)',  // #5E6D7A
          tertiary: 'var(--color-text-tertiary)',    // #8B9196
          disabled: 'var(--color-text-disabled)',    // #B0B8C1
          inverse: 'var(--color-text-inverse)',      // #FFFFFF
        },
        
        // Background Colors
        bg: {
          primary: 'var(--color-bg-primary)',        // #FFFFFF
          secondary: 'var(--color-bg-secondary)',    // #F3F6F8
          tertiary: 'var(--color-bg-tertiary)',      // #E8ECEF
          overlay: 'var(--color-bg-overlay)',        // rgba(29, 34, 38, 0.6)
        },
        
        // Border Colors
        border: {
          DEFAULT: 'var(--color-border-primary)',    // #DDE5E9
          secondary: 'var(--color-border-secondary)',// #E8ECEF
          focus: 'var(--color-border-focus)',        // Same as primary
          error: 'var(--color-border-error)',        // Same as danger
        },
      },
      
      // ============================================
      // NEUROSCIENCE SPACING SYSTEM
      // 8px base grid for visual rhythm
      // ============================================
      spacing: {
        'xs': 'var(--spacing-xs)',      // 4px
        'sm': 'var(--spacing-sm)',      // 8px
        'md': 'var(--spacing-md)',      // 16px
        'lg': 'var(--spacing-lg)',      // 24px
        'xl': 'var(--spacing-xl)',      // 32px
        '2xl': 'var(--spacing-2xl)',    // 48px
        '3xl': 'var(--spacing-3xl)',    // 64px
        '4xl': 'var(--spacing-4xl)',    // 96px
        
        // Keep existing custom spacing
        '18': '4.5rem',
        '30': '7.5rem',
        '36': '9rem',
      },
      
      // ============================================
      // NEUROSCIENCE TYPOGRAPHY
      // Modular scale 1.25 for visual harmony
      // ============================================
      fontFamily: {
        sans: 'var(--font-family-sans)',
        mono: 'var(--font-family-mono)',
      },
      fontSize: {
        'xs': 'var(--font-size-xs)',       // 12px
        'sm': 'var(--font-size-sm)',       // 14px
        'base': 'var(--font-size-base)',   // 16px
        'lg': 'var(--font-size-lg)',       // 18px
        'xl': 'var(--font-size-xl)',       // 20px
        '2xl': 'var(--font-size-2xl)',     // 24px
        '3xl': 'var(--font-size-3xl)',     // 30px
        '4xl': 'var(--font-size-4xl)',     // 36px
        '5xl': 'var(--font-size-5xl)',     // 48px
        
        // Keep existing custom sizes
        'hero': ['4rem', { lineHeight: '1.1', fontWeight: '900', letterSpacing: '-0.02em' }],
        'display': ['3rem', { lineHeight: '1.2', fontWeight: '800', letterSpacing: '-0.02em' }],
      },
      fontWeight: {
        'normal': 'var(--font-weight-normal)',       // 400
        'medium': 'var(--font-weight-medium)',       // 500
        'semibold': 'var(--font-weight-semibold)',   // 600
        'bold': 'var(--font-weight-bold)',           // 700
      },
      lineHeight: {
        'tight': 'var(--line-height-tight)',         // 1.25
        'normal': 'var(--line-height-normal)',       // 1.5
        'relaxed': 'var(--line-height-relaxed)',     // 1.75
      },
      
      // ============================================
      // NEUROSCIENCE VISUAL EFFECTS
      // Optimized for cognitive load reduction
      // ============================================
      borderRadius: {
        'sm': 'var(--radius-sm)',      // 4px
        'md': 'var(--radius-md)',      // 8px
        'lg': 'var(--radius-lg)',      // 12px
        'xl': 'var(--radius-xl)',      // 16px
        '2xl': 'var(--radius-2xl)',    // 24px
        'full': 'var(--radius-full)',  // 9999px
        
        // Keep existing for backwards compatibility
        '3xl': '1.5rem',
        '4xl': '2rem',
      },
      boxShadow: {
        'xs': 'var(--shadow-xs)',              // Subtle
        'sm': 'var(--shadow-sm)',              // Small
        'md': 'var(--shadow-md)',              // Medium (default)
        'lg': 'var(--shadow-lg)',              // Large
        'xl': 'var(--shadow-xl)',              // Extra large
        '2xl': 'var(--shadow-2xl)',            // Huge
        'focus': 'var(--shadow-focus)',        // Focus ring (blue)
        'focus-error': 'var(--shadow-focus-error)', // Error focus (red)
        
        // Keep existing
        'soft': '0 2px 15px rgba(0, 0, 0, 0.05)',
        'soft-lg': '0 10px 40px rgba(0, 0, 0, 0.08)',
        'soft-xl': '0 20px 50px rgba(0, 0, 0, 0.12)',
      },
      
      // ============================================
      // NEUROSCIENCE TRANSITIONS
      // Based on neural processing speeds
      // <100ms = Instant, <300ms = Optimal
      // ============================================
      transitionDuration: {
        'fast': '150ms',       // Hover states
        'base': '250ms',       // Optimal neural response
        'slow': '350ms',       // Complex animations
        
        // Keep existing
        '200': '200ms',
        '300': '300ms',
        '400': '400ms',
      },
      transitionTimingFunction: {
        'ease-in-out': 'ease-in-out',
        'smooth': 'cubic-bezier(0.4, 0, 0.2, 1)',
      },
      
      // ============================================
      // NEUROSCIENCE BACKGROUND IMAGES (Gradients)
      // Soft, professional gradients from tokens
      // ============================================
      backgroundImage: {
        'gradient-primary': 'var(--gradient-primary)',
        'gradient-success': 'var(--gradient-success)',
        'gradient-warning': 'var(--gradient-warning)',
        'gradient-danger': 'var(--gradient-danger)',
      },
    },
  },
  plugins: [],
}
