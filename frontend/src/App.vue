<template>
  <div :class="{ dark: isDark }" class="min-h-screen">
    <router-view />
    <ToastNotification />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useThemeStore } from '@/stores/theme'
import ToastNotification from '@/components/common/ToastNotification.vue'

const themeStore = useThemeStore()
const isDark = computed(() => themeStore.isDark)
</script>
