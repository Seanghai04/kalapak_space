import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useThemeStore = defineStore('theme', () => {
  const getInitialTheme = () => {
    if (import.meta.client) {
      return localStorage.getItem('theme') || 'dark'
    }
    return 'dark'
  }

  const theme = ref(getInitialTheme())

  const isDark = computed(() => theme.value === 'dark')

  function toggle() {
    theme.value = theme.value === 'dark' ? 'light' : 'dark'
    if (import.meta.client) {
      localStorage.setItem('theme', theme.value)
    }
    applyTheme()
  }

  function setTheme(value) {
    theme.value = value
    if (import.meta.client) {
      localStorage.setItem('theme', value)
    }
    applyTheme()
  }

  function applyTheme() {
    if (!import.meta.client) return

    if (theme.value === 'dark') {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }
  }

  // Apply on init
  applyTheme()

  return { theme, isDark, toggle, setTheme }
})
