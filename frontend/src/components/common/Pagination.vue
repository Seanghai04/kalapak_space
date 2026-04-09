<template>
  <div class="flex items-center justify-center gap-2 mt-8">
    <button
      :disabled="currentPage <= 1"
      @click="$emit('page-change', currentPage - 1)"
      class="px-3 py-2 rounded-lg text-sm font-medium transition-colors disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-100 dark:hover:bg-dark-700"
    >
      Previous
    </button>

    <template v-for="page in displayedPages" :key="page">
      <span v-if="page === '...'" class="px-2 text-gray-400">...</span>
      <button
        v-else
        @click="$emit('page-change', page)"
        class="w-10 h-10 rounded-lg text-sm font-medium transition-all duration-200"
        :class="[
          page === currentPage
            ? 'bg-gradient-brand text-white shadow-glow'
            : 'hover:bg-gray-100 dark:hover:bg-dark-700 text-gray-600 dark:text-gray-400'
        ]"
      >
        {{ page }}
      </button>
    </template>

    <button
      :disabled="currentPage >= lastPage"
      @click="$emit('page-change', currentPage + 1)"
      class="px-3 py-2 rounded-lg text-sm font-medium transition-colors disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-100 dark:hover:bg-dark-700"
    >
      Next
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  currentPage: { type: Number, required: true },
  lastPage: { type: Number, required: true },
})

defineEmits(['page-change'])

const displayedPages = computed(() => {
  const pages = []
  const total = props.lastPage
  const current = props.currentPage

  if (total <= 7) {
    for (let i = 1; i <= total; i++) pages.push(i)
  } else {
    pages.push(1)
    if (current > 3) pages.push('...')
    for (let i = Math.max(2, current - 1); i <= Math.min(total - 1, current + 1); i++) {
      pages.push(i)
    }
    if (current < total - 2) pages.push('...')
    pages.push(total)
  }

  return pages
})
</script>
