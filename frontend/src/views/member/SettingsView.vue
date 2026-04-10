<template>
  <div class="py-20">
    <div class="max-w-2xl mx-auto px-4">
      <h1 class="section-heading mb-8" data-aos="fade-up">Settings</h1>

      <div class="glass-card" data-aos="fade-up">
        <h3 class="text-lg font-sans font-bold dark:text-white mb-6">Appearance</h3>
        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium dark:text-white">Dark Mode</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Toggle between light and dark theme</p>
          </div>
          <button
            @click="themeStore.toggle()"
            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
            :class="themeStore.isDark ? 'bg-brand-violet' : 'bg-gray-300'"
          >
            <span
              class="inline-block h-4 w-4 rounded-full bg-white transition-transform"
              :class="themeStore.isDark ? 'translate-x-6' : 'translate-x-1'"
            />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useThemeStore } from '@/stores/theme'
const themeStore = useThemeStore()
</script>
