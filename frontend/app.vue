<template>
  <div :class="{ dark: isDark }" class="min-h-screen">
    <NuxtPage />
    <ToastNotification />
  </div>
</template>

<script setup>
import { storeToRefs } from "pinia";
import { onMounted } from "vue";
import { useThemeStore } from "@/stores/theme";
import ToastNotification from "@/components/common/ToastNotification.vue";

const themeStore = useThemeStore();
const { isDark } = storeToRefs(themeStore);

onMounted(() => {
  themeStore.initTheme();
});
useKalapakSeo();
</script>
