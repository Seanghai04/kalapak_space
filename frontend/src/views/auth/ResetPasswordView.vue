<template>
  <div>
    <h2 class="text-2xl font-sans font-bold gradient-text mb-6 text-center">Reset Password</h2>
    <form @submit.prevent="handleSubmit" class="space-y-5">
      <div>
        <label class="block text-sm font-medium dark:text-gray-300 mb-1">Email</label>
        <input v-model="form.email" type="email" required class="input-field" placeholder="your@email.com" />
      </div>
      <div>
        <label class="block text-sm font-medium dark:text-gray-300 mb-1">New Password</label>
        <input v-model="form.password" type="password" required minlength="8" class="input-field" placeholder="••••••••" />
      </div>
      <div>
        <label class="block text-sm font-medium dark:text-gray-300 mb-1">Confirm Password</label>
        <input v-model="form.password_confirmation" type="password" required class="input-field" placeholder="••••••••" />
      </div>
      <button type="submit" :disabled="loading" class="btn-primary w-full">
        <span v-if="loading">Resetting...</span>
        <span v-else>Reset Password</span>
      </button>
      <p v-if="error" class="text-sm text-red-500 text-center">{{ error }}</p>
      <p v-if="success" class="text-sm text-green-500 text-center">{{ success }}</p>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { authApi } from '@/services/api'

const route = useRoute()
const router = useRouter()

const form = ref({ email: '', password: '', password_confirmation: '' })
const loading = ref(false)
const error = ref('')
const success = ref('')

onMounted(() => {
  // Pre-fill email from the reset link query param
  if (route.query.email) {
    form.value.email = route.query.email
  }
})

async function handleSubmit() {
  const token = route.params.token || route.query.token
  if (!token) {
    error.value = 'Invalid or missing reset token. Please request a new reset link.'
    return
  }

  if (form.value.password !== form.value.password_confirmation) {
    error.value = 'Password confirmation does not match.'
    return
  }

  loading.value = true
  error.value = ''
  success.value = ''
  try {
    const { data } = await authApi.resetPassword({
      ...form.value,
      token,
    })

    if (!data?.success) {
      throw new Error(data?.message || 'Unable to reset password.')
    }

    success.value = 'Password reset successfully! Redirecting...'
    setTimeout(() => router.push({ name: 'login' }), 2000)
  } catch (e) {
    error.value = e.response?.data?.message || e.message || 'Failed to reset password'
  } finally {
    loading.value = false
  }
}
</script>
