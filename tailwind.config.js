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
        // Neuroscience-Based Soft Color Palette
        primary: {
          50: '#F0F5FB',
          100: '#D9E8F5',
          200: '#B3D1EB',
          300: '#8CBAE1',
          400: '#6BA3D7',
          500: '#5B8DBE', // Primary - Serene Blue
          600: '#4A75A0',
          700: '#3A5D82',
          800: '#2A4564',
          900: '#1A2D46',
        },
        secondary: {
          50: '#FEF5F0',
          100: '#FDE8DC',
          200: '#FBD1B9',
          300: '#F9BA96',
          400: '#F0A373',
          500: '#E8956F', // Secondary - Warm Coral
          600: '#D97D52',
          700: '#C96535',
          800: '#B94D18',
          900: '#A93500',
        },
        accent: {
          50: '#F5F9F0',
          100: '#E8F3DC',
          200: '#D1E7B9',
          300: '#BADB96',
          400: '#A3CF73',
          500: '#7CB342', // Accent - Healing Green
          600: '#6B9B35',
          700: '#5A8328',
          800: '#496B1B',
          900: '#38530E',
        },
        // Warm-Tinted Neutrals
        neutral: {
          50: '#FDFBF8',  // Soft Cream
          100: '#F9F7F4',
          200: '#F5F3F8',  // Pale Lavender
          300: '#F0F4ED',  // Soft Sage
          400: '#E8E3DB',
          500: '#D9D0C4',
          600: '#C4B5A0',
          700: '#9B8B7E',
          800: '#6B5D52',
          900: '#1A1410',  // Dark Charcoal
        },
        // Semantic Colors
        success: '#7CB342',
        warning: '#D4A574',
        error: '#C97C7C',
        info: '#7BA3C0',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
      fontSize: {
        'hero': ['4rem', { lineHeight: '1.1', fontWeight: '900', letterSpacing: '-0.02em' }],
        'display': ['3rem', { lineHeight: '1.2', fontWeight: '800', letterSpacing: '-0.02em' }],
      },
      spacing: {
        '18': '4.5rem',
        '30': '7.5rem',
        '36': '9rem',
      },
      borderRadius: {
        '2xl': '1rem',
        '3xl': '1.5rem',
        '4xl': '2rem',
      },
      boxShadow: {
        'soft': '0 2px 15px rgba(0, 0, 0, 0.05)',
        'soft-lg': '0 10px 40px rgba(0, 0, 0, 0.08)',
        'soft-xl': '0 20px 50px rgba(0, 0, 0, 0.12)',
      },
      transitionDuration: {
        '200': '200ms',
        '300': '300ms',
        '400': '400ms',
      },
      transitionTimingFunction: {
        'smooth': 'cubic-bezier(0.4, 0, 0.2, 1)',
      },
    },
  },
  plugins: [],
}
