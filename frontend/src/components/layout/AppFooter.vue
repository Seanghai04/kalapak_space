<template>
  <footer class="relative z-10 border-t border-gray-200 dark:border-dark-600 bg-white/50 dark:bg-dark-800/50 backdrop-blur-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
        <!-- Brand -->
        <div class="md:col-span-1">
          <img src="https://res.cloudinary.com/di1hdlb8k/image/upload/q_auto/f_auto/v1775860922/Logo_kalapak_om1ygl.png" alt="Kalapak Logo" class="w-10 h-10 rounded-xl object-contain mb-3" />
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Crafting Digital Futures from Cambodia to the World.
          </p>
        </div>

        <!-- Quick Links -->
        <div>
          <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Quick Links</h4>
          <ul class="space-y-2">
            <li v-for="link in quickLinks" :key="link.to">
              <router-link :to="link.to" class="text-sm text-gray-600 dark:text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan transition-colors">
                {{ link.label }}
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Resources -->
        <div>
          <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Resources</h4>
          <ul class="space-y-2">
            <li v-for="link in resourceLinks" :key="link.to">
              <router-link :to="link.to" class="text-sm text-gray-600 dark:text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan transition-colors">
                {{ link.label }}
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Connect -->
        <div>
          <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Connect</h4>
          <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
            Follow us on social media for updates.
          </p>
          <div class="flex gap-3">
            <a href="#" class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-dark-700 flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-brand-violet hover:text-white dark:hover:bg-brand-cyan dark:hover:text-dark-900 transition-all duration-200">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
            </a>
            <a href="#" class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-dark-700 flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-brand-violet hover:text-white dark:hover:bg-brand-cyan dark:hover:text-dark-900 transition-all duration-200">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 00-5.487 0 12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 00.031.057 19.9 19.9 0 005.993 3.03.078.078 0 00.084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 00-.041-.106 13.107 13.107 0 01-1.872-.892.077.077 0 01-.008-.128 10.2 10.2 0 00.372-.292.074.074 0 01.077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01c.12.098.246.198.373.292a.077.077 0 01-.006.127 12.299 12.299 0 01-1.873.892.077.077 0 00-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028 19.839 19.839 0 006.002-3.03.077.077 0 00.032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 00-.031-.03z"/></svg>
            </a>
          </div>
        </div>
      </div>

      <div class="mt-10 pt-6 border-t border-gray-200 dark:border-dark-600 text-center">
        <p class="text-sm text-gray-500 dark:text-gray-400">
          &copy; {{ new Date().getFullYear() }} Kalapak Code Team. All rights reserved.
        </p>
      </div>
    </div>
  </footer>
</template>

<script setup>
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
]
</script>
