export default defineNuxtConfig({
  ssr: true,
  pages: true,
  devtools: { enabled: true },
  app: {
    head: {
      titleTemplate: "%s | Kalapak Code Team",
      meta: [
        { name: "description", content: "Modern tech solutions from Cambodia by Kalapak Code Team." },
        { property: "og:site_name", content: "Kalapak Code Team" },
      ],
      link: [
        { rel: 'icon', type: 'image/png', href: 'https://res.cloudinary.com/kalapak/image/upload/q_auto/f_auto/v1775860922/Logo_kalapak_om1ygl.png' },
      ],
    },
  },
  modules: ['@pinia/nuxt', '@nuxtjs/tailwindcss'],
  pinia: {
    storesDirs: ['./src/stores/**'],
  },
  runtimeConfig: {
    public: {
      apiUrl: process.env.NUXT_PUBLIC_API_URL || process.env.VITE_API_URL || '/api',
      siteUrl: process.env.NUXT_PUBLIC_SITE_URL || "https://kalapak.com",
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
