import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authApi, memberApi } from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('auth_token'))
  const loading = ref(false)

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role?.name === 'admin')
  const isMember = computed(() => ['member', 'admin'].includes(user.value?.role?.name))

  async function login(credentials) {
    loading.value = true
    try {
      const { data } = await authApi.login(credentials)
      token.value = data.data.token
      user.value = data.data.user
      localStorage.setItem('auth_token', data.data.token)
      return data
    } finally {
      loading.value = false
    }
  }

  async function register(formData) {
    loading.value = true
    try {
      const { data } = await authApi.register(formData)
      token.value = data.data.token
      user.value = data.data.user
      localStorage.setItem('auth_token', data.data.token)
      return data
    } finally {
      loading.value = false
    }
  }

  async function fetchMe() {
    if (!token.value) return
    loading.value = true
    try {
      const { data } = await authApi.me()
      user.value = data.data
    } catch {
      logout()
    } finally {
      loading.value = false
    }
  }

  async function updateProfile(formData) {
    const { data } = await memberApi.updateProfile(formData)
    user.value = data.data
    return data
  }

  async function updatePassword(formData) {
    return await memberApi.updatePassword(formData)
  }

  async function uploadAvatar(formData) {
    const { data } = await memberApi.uploadAvatar(formData)
    user.value = data.data
    return data
  }

  async function logout() {
    if (token.value) {
      try { await authApi.logout() } catch { /* ignore */ }
    }
    user.value = null
    token.value = null
    localStorage.removeItem('auth_token')
  }

  return {
    user,
    token,
    loading,
    isAuthenticated,
    isAdmin,
    isMember,
    login,
    register,
    fetchMe,
    updateProfile,
    updatePassword,
    uploadAvatar,
    logout,
  }
})
