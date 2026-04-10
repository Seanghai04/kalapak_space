/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        brand: {
          violet: '#7b2fff',
          'violet-dark': '#5a1fd4',
          'violet-light': '#9d5fff',
          cyan: '#00d4ff',
          'cyan-dark': '#00a8cc',
          'cyan-light': '#33ddff',
        },
        dark: {
          900: '#020024',
          800: '#0d0a3e',
          700: '#151248',
          600: '#1e1a5a',
          500: '#2a2570',
        },
        surface: {
          dark: 'rgba(13, 10, 62, 0.8)',
          'dark-hover': 'rgba(30, 26, 90, 0.8)',
          light: 'rgba(255, 255, 255, 0.85)',
          'light-hover': 'rgba(255, 255, 255, 0.95)',
        },
      },
      fontFamily: {
        code: ['"Fira Code"', 'monospace'],
        sans: ['"Google Sans"', 'sans-serif'],
      },
      boxShadow: {
        glow: '0 0 20px rgba(123, 47, 255, 0.3)',
        'glow-cyan': '0 0 20px rgba(0, 212, 255, 0.3)',
        'glow-lg': '0 0 40px rgba(123, 47, 255, 0.4)',
        glass: '0 8px 32px rgba(0, 0, 0, 0.12)',
        'glass-dark': '0 8px 32px rgba(0, 0, 0, 0.4)',
      },
      animation: {
        float: 'float 6s ease-in-out infinite',
        'glow-pulse': 'glow-pulse 3s ease-in-out infinite',
        'fade-in': 'fade-in 0.6s ease-out forwards',
        'slide-up': 'slide-up 0.6s ease-out forwards',
        'slide-down': 'slide-down 0.3s ease-out forwards',
        'spin-slow': 'spin 8s linear infinite',
      },
      keyframes: {
        float: {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-20px)' },
        },
        'glow-pulse': {
          '0%, 100%': { boxShadow: '0 0 20px rgba(123, 47, 255, 0.3)' },
          '50%': { boxShadow: '0 0 40px rgba(123, 47, 255, 0.6)' },
        },
        'fade-in': {
          from: { opacity: '0' },
          to: { opacity: '1' },
        },
        'slide-up': {
          from: { opacity: '0', transform: 'translateY(30px)' },
          to: { opacity: '1', transform: 'translateY(0)' },
        },
        'slide-down': {
          from: { opacity: '0', transform: 'translateY(-10px)' },
          to: { opacity: '1', transform: 'translateY(0)' },
        },
      },
      backgroundImage: {
        'gradient-brand': 'linear-gradient(135deg, #7b2fff, #00d4ff)',
        'gradient-dark': 'linear-gradient(135deg, #020024, #0d0a3e, #020024)',
      },
    },
  },
  plugins: [],
}
