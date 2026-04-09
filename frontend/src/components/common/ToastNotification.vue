<template>
  <transition-group
    tag="div"
    class="fixed top-4 right-4 z-[100] space-y-3 pointer-events-none"
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="opacity-0 translate-x-8"
    enter-to-class="opacity-100 translate-x-0"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="opacity-100 translate-x-0"
    leave-to-class="opacity-0 translate-x-8"
  >
    <div
      v-for="toast in uiStore.toasts"
      :key="toast.id"
      class="pointer-events-auto glass px-5 py-3 flex items-center gap-3 min-w-[280px] max-w-sm"
      :class="toastClasses(toast.type)"
    >
      <svg v-if="toast.type === 'success'" class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
      </svg>
      <svg v-else-if="toast.type === 'error'" class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
      <svg v-else class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <p class="text-sm font-medium flex-1">{{ toast.message }}</p>
      <button @click="uiStore.removeToast(toast.id)" class="shrink-0 text-gray-400 hover:text-gray-600">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
  </transition-group>
</template>

<script setup>
import { useUiStore } from '@/stores/ui'

const uiStore = useUiStore()

function toastClasses(type) {
  switch (type) {
    case 'success': return 'border-l-4 border-l-green-500'
    case 'error': return 'border-l-4 border-l-red-500'
    case 'warning': return 'border-l-4 border-l-yellow-500'
    default: return 'border-l-4 border-l-blue-500'
  }
}
</script>
