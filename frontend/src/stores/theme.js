import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useThemeStore = defineStore('theme', () => {
  const isClient = typeof window !== 'undefined'

  const resolveInitialTheme = () => {
    if (!isClient) return 'dark'

    const storedTheme = localStorage.getItem('theme')
    if (storedTheme === 'light' || storedTheme === 'dark') return storedTheme

    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
  }

  const theme = ref('dark')

  const isDark = computed(() => theme.value === 'dark')

  function toggle() {
    setTheme(theme.value === 'dark' ? 'light' : 'dark')
  }

  function setTheme(value) {
    theme.value = value === 'light' ? 'light' : 'dark'
    if (isClient) localStorage.setItem('theme', theme.value)
    applyTheme()
  }

  function initTheme() {
    if (!isClient) return
    theme.value = resolveInitialTheme()
    localStorage.setItem('theme', theme.value)
    applyTheme()
  }

  function applyTheme() {
    if (!isClient) return

    if (theme.value === 'dark') {
      document.documentElement.classList.add('dark')
      document.body.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
      document.body.classList.remove('dark')
    }
  }

  if (isClient) initTheme()

  return { theme, isDark, toggle, setTheme, initTheme }
})
