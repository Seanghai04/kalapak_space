<template>
  <div class="glass-card">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-sans font-bold dark:text-white">Storage Usage</h3>
      <button
        @click="refresh"
        :disabled="loading"
        class="px-3 py-1.5 text-sm rounded-lg bg-brand-violet/10 dark:bg-brand-cyan/10 text-brand-violet dark:text-brand-cyan hover:bg-brand-violet/20 dark:hover:bg-brand-cyan/20 transition-colors disabled:opacity-50"
      >
        <span v-if="loading" class="inline-flex items-center gap-1">
          <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
          </svg>
          Refreshing…
        </span>
        <span v-else>↻ Refresh</span>
      </button>
    </div>

    <div v-if="error" class="text-sm text-red-500 dark:text-red-400 mb-4">{{ error }}</div>

    <div class="grid sm:grid-cols-2 gap-6">
      <!-- Cloudinary Card -->
      <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-white/50 dark:bg-white/5">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-lg">
            ☁️
          </div>
          <div>
            <p class="font-semibold dark:text-white">Cloudinary</p>
            <p class="text-xs text-gray-400">{{ cloudinary.plan || 'Media & Image CDN' }}</p>
          </div>
        </div>

        <template v-if="cloudinary.configured">
          <!-- Storage -->
          <div class="mb-3">
            <div class="flex justify-between text-sm mb-1">
              <span class="text-gray-500 dark:text-gray-400">Storage</span>
              <span class="dark:text-gray-300 font-medium">{{ cloudinary.storage?.used_formatted }} / {{ cloudinary.storage?.limit_formatted }}</span>
            </div>
            <div class="w-full h-2.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
              <div
                class="h-full rounded-full transition-all duration-500"
                :class="getBarColor(cloudinary.storage?.percentage)"
                :style="{ width: cloudinary.storage?.percentage + '%' }"
              />
            </div>
            <p class="text-xs text-gray-400 mt-0.5 text-right">{{ cloudinary.storage?.percentage }}%</p>
          </div>

          <!-- Bandwidth -->
          <div class="mb-3">
            <div class="flex justify-between text-sm mb-1">
              <span class="text-gray-500 dark:text-gray-400">Bandwidth</span>
              <span class="dark:text-gray-300 font-medium">{{ cloudinary.bandwidth?.used_formatted }} / {{ cloudinary.bandwidth?.limit_formatted }}</span>
            </div>
            <div class="w-full h-2.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
              <div
                class="h-full rounded-full transition-all duration-500"
                :class="getBarColor(cloudinary.bandwidth?.percentage)"
                :style="{ width: cloudinary.bandwidth?.percentage + '%' }"
              />
            </div>
            <p class="text-xs text-gray-400 mt-0.5 text-right">{{ cloudinary.bandwidth?.percentage }}%</p>
          </div>

          <!-- Transformations -->
          <div class="mb-2">
            <div class="flex justify-between text-sm mb-1">
              <span class="text-gray-500 dark:text-gray-400">Transformations</span>
              <span class="dark:text-gray-300 font-medium">{{ cloudinary.transformations?.used?.toLocaleString() }} / {{ cloudinary.transformations?.limit?.toLocaleString() }}</span>
            </div>
            <div class="w-full h-2.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
              <div
                class="h-full rounded-full transition-all duration-500"
                :class="getBarColor(cloudinary.transformations?.percentage)"
                :style="{ width: cloudinary.transformations?.percentage + '%' }"
              />
            </div>
            <p class="text-xs text-gray-400 mt-0.5 text-right">{{ cloudinary.transformations?.percentage }}%</p>
          </div>

          <p class="text-xs text-gray-400 mt-2">📦 {{ cloudinary.resources?.toLocaleString() || 0 }} resources</p>
        </template>
        <p v-else class="text-sm text-gray-400 italic">{{ cloudinary.error || 'Not configured' }}</p>
      </div>

      <!-- Supabase Card -->
      <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-white/50 dark:bg-white/5">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-lg">
            🗄️
          </div>
          <div>
            <p class="font-semibold dark:text-white">Supabase</p>
            <p class="text-xs text-gray-400">{{ supabase.bucket || 'File Storage' }}</p>
          </div>
        </div>

        <template v-if="supabase.configured">
          <!-- Storage -->
          <div class="mb-3">
            <div class="flex justify-between text-sm mb-1">
              <span class="text-gray-500 dark:text-gray-400">Storage</span>
              <span class="dark:text-gray-300 font-medium">{{ supabase.storage?.used_formatted }} / {{ supabase.storage?.limit_formatted }}</span>
            </div>
            <div class="w-full h-2.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
              <div
                class="h-full rounded-full transition-all duration-500"
                :class="getBarColor(supabase.storage?.percentage)"
                :style="{ width: supabase.storage?.percentage + '%' }"
              />
            </div>
            <p class="text-xs text-gray-400 mt-0.5 text-right">{{ supabase.storage?.percentage }}%</p>
          </div>

          <!-- File Count -->
          <p class="text-xs text-gray-400 mb-3">📁 {{ supabase.files?.toLocaleString() || 0 }} files total</p>

          <!-- Folder Breakdown -->
          <div v-if="supabase.folders && Object.keys(supabase.folders).length" class="space-y-1.5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Folders</p>
            <div
              v-for="(info, folder) in supabase.folders"
              :key="folder"
              class="flex justify-between text-xs text-gray-500 dark:text-gray-400"
            >
              <span>📂 {{ folder }}</span>
              <span>{{ info.files }} files · {{ info.size_formatted }}</span>
            </div>
          </div>
        </template>
        <p v-else class="text-sm text-gray-400 italic">{{ supabase.error || 'Not configured' }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { adminApi } from '@/services/api'

const loading = ref(false)
const error = ref(null)

const cloudinary = ref({ configured: false })
const supabase = ref({ configured: false })

function getBarColor(percentage) {
  if (percentage >= 90) return 'bg-red-500'
  if (percentage >= 70) return 'bg-yellow-500'
  return 'bg-brand-violet dark:bg-brand-cyan'
}

async function fetchStats(isRefresh = false) {
  loading.value = true
  error.value = null
  try {
    const { data } = isRefresh
      ? await adminApi.refreshStorageStats()
      : await adminApi.getStorageStats()
    cloudinary.value = data.data?.cloudinary || { configured: false }
    supabase.value = data.data?.supabase || { configured: false }
  } catch (e) {
    error.value = 'Failed to load storage stats'
  } finally {
    loading.value = false
  }
}

function refresh() {
  fetchStats(true)
}

onMounted(() => fetchStats())
</script>
