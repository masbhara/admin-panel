const defaultTheme = require('tailwindcss/defaultTheme')

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue',
  ],
  darkMode: 'class',
  theme: {
    extend: {
      fontFamily: {
        sans: ['Manrope', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        primary: {
          50: 'var(--color-primary-50, #f0f9ff)',
          100: 'var(--color-primary-100, #e0f2fe)',
          200: 'var(--color-primary-200, #bae6fd)',
          300: 'var(--color-primary-300, #7dd3fc)',
          400: 'var(--color-primary-400, #38bdf8)',
          500: 'var(--color-primary-500, #0ea5e9)',
          600: 'var(--color-primary-600, #0284c7)',
          700: 'var(--color-primary-700, #0369a1)',
          800: 'var(--color-primary-800, #075985)',
          900: 'var(--color-primary-900, #0c4a6e)',
          950: 'var(--color-primary-950, #082f49)',
        },
        secondary: {
          50: 'var(--color-secondary-50, #f8fafc)',
          100: 'var(--color-secondary-100, #f1f5f9)',
          200: 'var(--color-secondary-200, #e2e8f0)',
          300: 'var(--color-secondary-300, #cbd5e1)',
          400: 'var(--color-secondary-400, #94a3b8)',
          500: 'var(--color-secondary-500, #64748b)',
          600: 'var(--color-secondary-600, #475569)',
          700: 'var(--color-secondary-700, #334155)',
          800: 'var(--color-secondary-800, #1e293b)',
          900: 'var(--color-secondary-900, #0f172a)',
          950: 'var(--color-secondary-950, #020617)',
        },
        background: {
          primary: 'var(--color-bg-primary, #ffffff)',
          secondary: 'var(--color-bg-secondary, #f1f5f9)',
          tertiary: 'var(--color-bg-tertiary, #e2e8f0)',
        },
        text: {
          primary: 'var(--color-text-primary, #0f172a)',
          secondary: 'var(--color-text-secondary, #475569)',
          tertiary: 'var(--color-text-tertiary, #94a3b8)',
          inverted: 'var(--color-text-inverted, #f8fafc)',
        },
        border: {
          light: 'var(--color-border-light, #e2e8f0)',
          default: 'var(--color-border-default, #cbd5e1)',
          dark: 'var(--color-border-dark, #94a3b8)',
        },
      },
    },
  },
  plugins: [require('@tailwindcss/forms')],
}
