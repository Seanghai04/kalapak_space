import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useUiStore = defineStore('ui', () => {
  const loading = ref(false)
  const sidebarOpen = ref(true)
  const toasts = ref([])
  const modal = ref({ show: false, component: null, props: {} })

  let toastId = 0

  function showToast(message, type = 'success', duration = 4000) {
    const id = ++toastId
    toasts.value.push({ id, message, type })
    if (duration > 0) {
      setTimeout(() => removeToast(id), duration)
    }
  }

  function removeToast(id) {
    toasts.value = toasts.value.filter((t) => t.id !== id)
  }

  function toggleSidebar() {
    sidebarOpen.value = !sidebarOpen.value
  }

  function openModal(component, props = {}) {
    modal.value = { show: true, component, props }
  }

  function closeModal() {
    modal.value = { show: false, component: null, props: {} }
  }

  return {
    loading,
    sidebarOpen,
    toasts,
    modal,
    showToast,
    removeToast,
    toggleSidebar,
    openModal,
    closeModal,
  }
})
