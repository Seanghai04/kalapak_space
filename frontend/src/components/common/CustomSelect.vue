<template>
  <div class="relative" ref="containerRef">
    <!-- Trigger Button -->
    <button
      type="button"
      ref="triggerRef"
      @click="toggle"
      :class="[
        'flex items-center justify-between gap-2 w-full text-left rounded-xl border text-sm transition-all duration-200 cursor-pointer backdrop-blur-sm',
        isOpen
          ? 'border-brand-violet dark:border-brand-cyan ring-2 ring-brand-violet/20 dark:ring-brand-cyan/20 bg-white dark:bg-white/[0.08]'
          : 'border-gray-200 dark:border-white/[0.08] hover:border-gray-300 dark:hover:border-white/[0.15] bg-gray-50 dark:bg-white/[0.04]',
        'text-gray-900 dark:text-white',
        sizeClass
      ]"
    >
      <span :class="modelValue || modelValue === 0 ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'">
        {{ selectedLabel }}
      </span>
      <svg
        class="w-4 h-4 flex-shrink-0 transition-transform duration-200"
        :class="isOpen ? 'text-brand-violet dark:text-brand-cyan rotate-180' : 'text-gray-400 dark:text-gray-500'"
        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
      </svg>
    </button>

    <!-- Dropdown (teleported to body to avoid overflow clipping) -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition duration-150 ease-out"
        enter-from-class="opacity-0 translate-y-1 scale-[0.97]"
        enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition duration-100 ease-in"
        leave-from-class="opacity-100 translate-y-0 scale-100"
        leave-to-class="opacity-0 translate-y-1 scale-[0.97]"
      >
        <div
          v-if="isOpen"
          ref="dropdownRef"
          :style="floatingStyle"
          class="fixed z-[9999] rounded-xl border border-gray-200/80 dark:border-white/[0.08] bg-white dark:bg-dark-800/95 shadow-2xl shadow-black/10 dark:shadow-black/50 backdrop-blur-xl overflow-hidden"
        >
          <!-- Search (optional) -->
          <div v-if="searchable" class="p-2 border-b border-gray-100 dark:border-white/[0.06]">
            <input
              ref="searchRef"
              v-model="searchQuery"
              type="text"
              :placeholder="searchPlaceholder"
              class="w-full px-3 py-2 rounded-lg bg-gray-50 dark:bg-white/[0.05] border border-gray-200 dark:border-white/[0.08] text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-brand-violet dark:focus:border-brand-cyan transition-colors"
              @keydown.stop
            />
          </div>

          <!-- Options List -->
          <div class="max-h-60 overflow-y-auto overscroll-contain custom-scrollbar py-1">
            <button
              v-for="opt in filteredOptions"
              :key="opt.value"
              type="button"
              @click="select(opt)"
              :class="[
                'flex items-center w-full px-3.5 py-2.5 text-sm text-left transition-all duration-100 gap-2',
                isSelected(opt)
                  ? 'bg-brand-violet/10 dark:bg-brand-cyan/[0.08] text-brand-violet dark:text-brand-cyan font-medium'
                  : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/[0.05] hover:text-gray-900 dark:hover:text-white'
              ]"
            >
              <span class="flex-1 truncate">{{ opt.label }}</span>
              <svg
                v-if="isSelected(opt)"
                class="w-4 h-4 flex-shrink-0 text-brand-violet dark:text-brand-cyan"
                fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
              </svg>
            </button>
            <div v-if="filteredOptions.length === 0" class="px-3.5 py-3 text-sm text-gray-400 dark:text-gray-500 text-center">
              No results found
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue'

const props = defineProps({
  modelValue: { type: [String, Number], default: '' },
  options: { type: Array, required: true },
  placeholder: { type: String, default: 'Select...' },
  searchable: { type: Boolean, default: false },
  searchPlaceholder: { type: String, default: 'Search...' },
  size: { type: String, default: 'md' }
})

const emit = defineEmits(['update:modelValue', 'change'])

const isOpen = ref(false)
const searchQuery = ref('')
const containerRef = ref(null)
const triggerRef = ref(null)
const dropdownRef = ref(null)
const searchRef = ref(null)

const floatingStyle = ref({
  top: '0px',
  left: '0px',
  width: '180px'
})

function updatePosition() {
  if (!triggerRef.value) return
  const rect = triggerRef.value.getBoundingClientRect()
  floatingStyle.value = {
    top: `${rect.bottom + 6}px`,
    left: `${rect.left}px`,
    width: `${Math.max(rect.width, 180)}px`
  }
}

const sizeClass = computed(() => {
  return props.size === 'sm' ? 'px-3 py-2' : 'px-4 py-3'
})

// Normalize options to { label, value } format
const normalizedOptions = computed(() => {
  return props.options.map(opt => {
    if (typeof opt === 'object' && opt !== null) {
      return { label: String(opt.label), value: opt.value }
    }
    return { label: String(opt), value: opt }
  })
})

const filteredOptions = computed(() => {
  if (!searchQuery.value) return normalizedOptions.value
  const q = searchQuery.value.toLowerCase()
  return normalizedOptions.value.filter(opt => opt.label.toLowerCase().includes(q))
})

const selectedLabel = computed(() => {
  const found = normalizedOptions.value.find(opt => opt.value === props.modelValue)
  return found ? found.label : props.placeholder
})

function isSelected(opt) {
  return opt.value === props.modelValue
}

function toggle() {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    searchQuery.value = ''
    updatePosition()
    nextTick(() => {
      if (props.searchable && searchRef.value) {
        searchRef.value.focus()
      }
    })
  }
}

function select(opt) {
  emit('update:modelValue', opt.value)
  emit('change', opt.value)
  isOpen.value = false
}

function onClickOutside(e) {
  if (containerRef.value && !containerRef.value.contains(e.target) &&
      dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    isOpen.value = false
  }
}

function onScrollOrResize() {
  if (isOpen.value) {
    updatePosition()
  }
}

onMounted(() => {
  document.addEventListener('mousedown', onClickOutside)
  window.addEventListener('scroll', onScrollOrResize, true)
  window.addEventListener('resize', onScrollOrResize)
})

onBeforeUnmount(() => {
  document.removeEventListener('mousedown', onClickOutside)
  window.removeEventListener('scroll', onScrollOrResize, true)
  window.removeEventListener('resize', onScrollOrResize)
})
</script>

<style scoped>
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: rgba(123, 47, 255, 0.2) transparent;
}
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(123, 47, 255, 0.2);
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(123, 47, 255, 0.35);
}
:root.dark .custom-scrollbar,
.dark .custom-scrollbar {
  scrollbar-color: rgba(0, 212, 255, 0.15) transparent;
}
:root.dark .custom-scrollbar::-webkit-scrollbar-thumb,
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(0, 212, 255, 0.15);
}
:root.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover,
.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(0, 212, 255, 0.3);
}
</style>
