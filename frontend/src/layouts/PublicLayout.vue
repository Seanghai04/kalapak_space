<template>
  <div class="min-h-screen flex flex-col bg-white dark:bg-dark-900 transition-colors duration-300">
    <StarfieldBg />
    <AppNavbar />
    <main class="flex-1 relative z-10">
      <router-view />
    </main>
    <AppFooter />
  </div>
</template>

<script setup>
import AppNavbar from '@/components/layout/AppNavbar.vue'
import AppFooter from '@/components/layout/AppFooter.vue'
import StarfieldBg from '@/components/common/StarfieldBg.vue'
</script>
