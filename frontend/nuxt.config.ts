export default defineNuxtConfig({
  ssr: false,
  pages: false,
  devtools: { enabled: true },
  modules: ['@pinia/nuxt', '@nuxtjs/tailwindcss'],
  runtimeConfig: {
    public: {
      apiUrl: process.env.NUXT_PUBLIC_API_URL || process.env.VITE_API_URL || '/api',
    },
  },
  css: [
    'aos/dist/aos.css',
    'highlight.js/styles/github-dark.css',
    '@/assets/styles/animations.css',
    '@/assets/styles/glassmorphism.css',
    '@/assets/styles/main.css',
  ],
  alias: {
    '@': '/src',
  },
  compatibilityDate: '2026-04-24',
})
