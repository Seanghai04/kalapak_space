<template>
  <footer class="relative z-10">

    <div class="footer-main relative">
      <div class="relative max-w-7xl mx-auto px-6 lg:px-8">

        <!-- ── Top: Brand + Nav columns ── -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 pt-20 pb-16">

          <!-- Brand column (5 cols) -->
          <div class="lg:col-span-5 flex flex-col gap-6">

            <!-- Logo + name -->
            <div class="flex items-center gap-3">
              <img
                src="https://res.cloudinary.com/kalapak/image/upload/q_auto/f_auto/v1775860922/Logo_kalapak_om1ygl.png"
                alt="Kalapak Logo"
                class="w-10 h-10 rounded-xl object-contain"
              />
              <span class="text-xl font-black tracking-tight text-gray-900 dark:text-white">Kalapak</span>
            </div>

            <!-- Tagline -->
            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400 max-w-sm">
              Crafting Digital Futures from Cambodia to the World.<br/>
              We build products that matter.
            </p>

            <!-- Social row -->
            <div class="flex items-center gap-3 pt-1">
              <a href="#" aria-label="GitHub" class="social-btn">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/>
                </svg>
              </a>
              <a href="#" aria-label="Discord" class="social-btn">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 00-5.487 0 12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 00.031.057 19.9 19.9 0 005.993 3.03.078.078 0 00.084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 00-.041-.106 13.107 13.107 0 01-1.872-.892.077.077 0 01-.008-.128 10.2 10.2 0 00.372-.292.074.074 0 01.077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01c.12.098.246.198.373.292a.077.077 0 01-.006.127 12.299 12.299 0 01-1.873.892.077.077 0 00-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028 19.839 19.839 0 006.002-3.03.077.077 0 00.032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 00-.031-.03z"/>
                </svg>
              </a>
              <a href="#" aria-label="X" class="social-btn">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
              </a>
            </div>
          </div>

          <!-- Spacer (1 col) -->
          <div class="hidden lg:block lg:col-span-1" />

          <!-- Quick Links (3 cols) -->
          <div class="lg:col-span-3">
            <p class="footer-col-title">Quick Links</p>
            <ul class="space-y-3 mt-5">
              <li v-for="link in quickLinks" :key="link.to">
                <router-link :to="link.to" class="footer-link">{{ link.label }}</router-link>
              </li>
            </ul>
          </div>

          <!-- Resources (3 cols) -->
          <div class="lg:col-span-3">
            <p class="footer-col-title">Resources</p>
            <ul class="space-y-3 mt-5">
              <li v-for="link in resourceLinks" :key="link.to">
                <router-link :to="link.to" class="footer-link">{{ link.label }}</router-link>
              </li>
            </ul>
          </div>
        </div>

        <!-- ── Bottom bar ── -->
        <div class="flex items-center justify-center py-6">
          <p class="copyright-text">
            &copy; {{ new Date().getFullYear() }} Kalapak. All rights reserved.
          </p>
        </div>

      </div>
    </div>

  </footer>
</template>

<script setup>
import { computed } from 'vue'
import { useThemeStore } from '@/stores/theme'

const themeStore = useThemeStore()
const d = computed(() => themeStore.isDark)

// xAI-style: deep dark base + big vivid glowing orb from bottom-center
const footerBg = computed(() =>
  d.value
    ? `radial-gradient(ellipse 120% 90% at 50% 100%, rgba(123,47,255,0.90) 0%, rgba(0,212,255,0.35) 45%, rgba(0,212,255,0.05) 70%, transparent 85%),
       #020024`
    : `radial-gradient(ellipse 120% 90% at 50% 100%, rgba(123,47,255,0.60) 0%, rgba(0,212,255,0.22) 45%, rgba(0,212,255,0.05) 70%, transparent 85%),
       #f9fafb`
)

const quickLinks = [
  { to: '/', label: 'Home' },
  { to: '/about', label: 'About' },
  { to: '/projects', label: 'Projects' },
  { to: '/contact', label: 'Contact' },
]

const resourceLinks = [
  { to: '/blog', label: 'Blog' },
  { to: '/join', label: 'Join Our Team' },
  { to: '/privacy', label: 'Privacy Policy' },
  { to: '/terms', label: 'Terms of Service' },
]
</script>

<style scoped>
/* ── Footer glow background ── */
.footer-main {
  background: v-bind(footerBg);
}

/* ── Social icon button ── */
.social-btn {
  @apply w-9 h-9 rounded-xl flex items-center justify-center
         bg-white/80 dark:bg-white/5
         border border-gray-200 dark:border-white/10
         text-gray-500 dark:text-gray-400
         hover:bg-brand-violet hover:border-brand-violet hover:text-white
         dark:hover:bg-brand-violet dark:hover:border-brand-violet dark:hover:text-white
         transition-all duration-200 backdrop-blur-sm;
}

/* ── Column heading ── */
.footer-col-title {
  @apply text-xs font-bold uppercase tracking-[0.18em]
         text-brand-violet dark:text-brand-violet;
}

/* ── Nav link ── */
.footer-link {
  @apply text-[15px] text-gray-600 dark:text-gray-400
         hover:text-brand-violet dark:hover:text-white
         transition-colors duration-150;
}

/* ── Copyright gradient text ── */
.copyright-text {
  font-size: 0.875rem;
  font-weight: 500;
  background: linear-gradient(90deg, #7b2fff 0%, #00d4ff 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  opacity: 0.75;
}

/* ── Divider ── */
.footer-divider {
  @apply border-t border-gray-200 dark:border-white/10;
}
</style>
