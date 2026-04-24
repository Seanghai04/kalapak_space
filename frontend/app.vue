<template>
  <div :class="{ dark: isDark }" class="min-h-screen">
    <NuxtPage />
    <ToastNotification />
  </div>
</template>

<script setup>
import { useThemeStore } from "@/stores/theme";
import ToastNotification from "@/components/common/ToastNotification.vue";

const themeStore = useThemeStore();
const isDark = computed(() => themeStore.isDark);
useKalapakSeo();
</script>
