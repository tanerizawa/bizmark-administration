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
      /* ============================================
       * NEUROSCIENCE COLOR PALETTE
       * ============================================ */
      colors: {
        // Primary - Soft Periwinkle
        'neuro-primary': {
          DEFAULT: '#8B9FD8',
          light: '#A8B8E6',
          dark: '#6B7FB8',
          muted: '#D4DCF2',
        },
        // Secondary - Sage Green
        'neuro-secondary': {
          DEFAULT: '#A8C5A8',
          light: '#C5DCC5',
          dark: '#7FA87F',
          muted: '#E5F2E5',
        },
        // Accent - Warm Taupe
        'neuro-accent': {
          DEFAULT: '#C9B5A0',
          light: '#E0D3C4',
          dark: '#A89882',
          muted: '#F0EBE5',
        },
        // Success - Soft Mint
        'neuro-success': {
          DEFAULT: '#88D4AB',
          light: '#A8E4C3',
          dark: '#68B48B',
          bg: '#E8F7EF',
          border: '#B8E7CE',
        },
        // Warning - Butter Yellow
        'neuro-warning': {
          DEFAULT: '#F5D887',
          light: '#F9E5A8',
          dark: '#D9BC68',
          bg: '#FEF9E8',
          border: '#F9E9B8',
        },
        // Error - Blush Pink
        'neuro-error': {
          DEFAULT: '#E8A0A0',
          light: '#F2C5C5',
          dark: '#D88080',
          bg: '#FCF0F0',
          border: '#F2C5C5',
        },
        // Info - Soft Lavender
        'neuro-info': {
          DEFAULT: '#B8A8D8',
          light: '#D9CEE8',
          dark: '#9888B8',
          bg: '#F5F2FA',
          border: '#D9CEE8',
        },
      },

      /* ============================================
       * FONT FAMILY
       * ============================================ */
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif'],
        mono: ['SF Mono', 'Monaco', 'Cascadia Code', 'Roboto Mono', 'Consolas', 'Courier New', 'monospace'],
      },

      /* ============================================
       * FONT SIZES - Modular Scale 1.250
       * ============================================ */
      fontSize: {
        'xs': ['0.75rem', { lineHeight: '1.5' }],      // 12px
        'sm': ['0.875rem', { lineHeight: '1.5' }],     // 14px
        'base': ['1rem', { lineHeight: '1.625' }],     // 16px
        'lg': ['1.125rem', { lineHeight: '1.625' }],   // 18px
        'xl': ['1.25rem', { lineHeight: '1.375' }],    // 20px
        '2xl': ['1.5rem', { lineHeight: '1.375' }],    // 24px
        '3xl': ['1.875rem', { lineHeight: '1.25' }],   // 30px
        '4xl': ['2.25rem', { lineHeight: '1.25' }],    // 36px
        '5xl': ['3rem', { lineHeight: '1' }],          // 48px
        '6xl': ['3.75rem', { lineHeight: '1' }],       // 60px
      },

      /* ============================================
       * SPACING - 8px Base Scale
       * ============================================ */
      spacing: {
        '0.5': '0.125rem',   // 2px
        '1': '0.25rem',      // 4px
        '2': '0.5rem',       // 8px
        '3': '0.75rem',      // 12px
        '4': '1rem',         // 16px
        '5': '1.5rem',       // 24px
        '6': '2rem',         // 32px
        '7': '2.5rem',       // 40px
        '8': '3rem',         // 48px
        '10': '4rem',        // 64px
        '12': '6rem',        // 96px
        '16': '8rem',        // 128px
        '20': '10rem',       // 160px
      },

      /* ============================================
       * BOX SHADOWS - Soft & Natural
       * ============================================ */
      boxShadow: {
        'xs': '0 1px 2px rgba(44, 42, 39, 0.06)',
        'sm': '0 2px 4px rgba(44, 42, 39, 0.08), 0 1px 2px rgba(44, 42, 39, 0.04)',
        'md': '0 4px 8px rgba(44, 42, 39, 0.10), 0 2px 4px rgba(44, 42, 39, 0.06)',
        'lg': '0 8px 16px rgba(44, 42, 39, 0.12), 0 4px 8px rgba(44, 42, 39, 0.08)',
        'xl': '0 16px 32px rgba(44, 42, 39, 0.14), 0 8px 16px rgba(44, 42, 39, 0.10)',
        '2xl': '0 24px 48px rgba(44, 42, 39, 0.16), 0 12px 24px rgba(44, 42, 39, 0.12)',
        'inner': 'inset 0 2px 4px rgba(0, 0, 0, 0.06)',
        'focus': '0 0 0 3px rgba(139, 159, 216, 0.25)',
        'none': 'none',
      },

      /* ============================================
       * BORDER RADIUS - Consistent Rounding
       * ============================================ */
      borderRadius: {
        'none': '0',
        'sm': '0.25rem',      // 4px
        'DEFAULT': '0.5rem',  // 8px
        'md': '0.625rem',     // 10px (Apple-inspired)
        'lg': '0.75rem',      // 12px
        'xl': '1rem',         // 16px
        '2xl': '1.25rem',     // 20px
        'full': '9999px',
      },

      /* ============================================
       * ANIMATION DURATIONS
       * ============================================ */
      transitionDuration: {
        'fast': '150ms',
        'DEFAULT': '250ms',
        'slow': '350ms',
        'slower': '500ms',
      },

      /* ============================================
       * ANIMATION TIMING FUNCTIONS
       * ============================================ */
      transitionTimingFunction: {
        'ease-in': 'cubic-bezier(0.4, 0, 1, 1)',
        'ease-out': 'cubic-bezier(0, 0, 0.2, 1)',
        'ease-in-out': 'cubic-bezier(0.4, 0, 0.2, 1)',
        'spring': 'cubic-bezier(0.34, 1.56, 0.64, 1)',
        'smooth': 'cubic-bezier(0.25, 0.1, 0.25, 1)',
      },

      /* ============================================
       * KEYFRAMES
       * ============================================ */
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideUp: {
          '0%': { opacity: '0', transform: 'translateY(20px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        slideDown: {
          '0%': { opacity: '0', transform: 'translateY(-20px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        scaleIn: {
          '0%': { opacity: '0', transform: 'scale(0.95)' },
          '100%': { opacity: '1', transform: 'scale(1)' },
        },
        pulse: {
          '0%, 100%': { opacity: '1' },
          '50%': { opacity: '0.7' },
        },
        spin: {
          'from': { transform: 'rotate(0deg)' },
          'to': { transform: 'rotate(360deg)' },
        },
        shimmer: {
          '0%': { backgroundPosition: '-200% 0' },
          '100%': { backgroundPosition: '200% 0' },
        },
      },

      /* ============================================
       * ANIMATIONS
       * ============================================ */
      animation: {
        'fadeIn': 'fadeIn 250ms ease-out',
        'slideUp': 'slideUp 350ms ease-out',
        'slideDown': 'slideDown 350ms ease-out',
        'scaleIn': 'scaleIn 250ms ease-out',
        'pulse': 'pulse 500ms ease-in-out infinite',
        'spin': 'spin 1s linear infinite',
        'shimmer': 'shimmer 2s linear infinite',
      },

      /* ============================================
       * BREAKPOINTS (Already defined by Tailwind, but documented here)
       * ============================================ */
      screens: {
        // Inherited from Tailwind defaults:
        // 'sm': '640px',
        // 'md': '768px',
        // 'lg': '1024px',
        // 'xl': '1280px',
        // '2xl': '1536px',
      },
    },
  },
  plugins: [],
}
