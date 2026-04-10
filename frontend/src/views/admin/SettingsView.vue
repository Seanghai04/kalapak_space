<template>
  <div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-sans font-bold dark:text-white">Settings</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your site configuration and preferences</p>
      </div>
      <div class="flex items-center gap-2">
        <span v-if="hasChanges" class="text-xs text-amber-500 font-medium flex items-center gap-1">
          <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse" />
          Unsaved changes
        </span>
        <button @click="handleSave" :disabled="saving || !hasChanges" class="btn-primary flex items-center gap-2 text-sm disabled:opacity-40 disabled:cursor-not-allowed">
          <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
          <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          {{ saving ? 'Saving...' : 'Save Changes' }}
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="w-10 h-10 border-4 border-brand-violet/30 border-t-brand-violet rounded-full animate-spin" />
    </div>

    <!-- Settings Sections -->
    <div v-else class="space-y-6">
      <!-- Navigation Tabs -->
      <div class="glass-card !p-1 flex gap-1 overflow-x-auto">
        <button v-for="group in groupNames" :key="group" @click="activeGroup = group"
          class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap transition-all"
          :class="activeGroup === group
            ? 'bg-brand-violet/10 dark:bg-brand-cyan/10 text-brand-violet dark:text-brand-cyan shadow-sm'
            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-dark-700'">
          <div class="flex items-center gap-2">
            <!-- General Icon -->
            <svg v-if="group === 'general'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <!-- Social Icon -->
            <svg v-else-if="group === 'social'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            <!-- SEO Icon -->
            <svg v-else-if="group === 'seo'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <!-- Default Icon -->
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
            <span class="capitalize">{{ group }}</span>
          </div>
        </button>
      </div>

      <!-- Active Group Settings -->
      <div v-for="group in groupNames" :key="group" v-show="activeGroup === group">
        <div class="glass-card">
          <!-- Group Header -->
          <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200 dark:border-dark-600">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" :class="groupStyle(group).bg">
              <svg v-if="group === 'general'" class="w-5 h-5" :class="groupStyle(group).text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              <svg v-else-if="group === 'social'" class="w-5 h-5" :class="groupStyle(group).text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
              <svg v-else-if="group === 'seo'" class="w-5 h-5" :class="groupStyle(group).text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              <svg v-else class="w-5 h-5" :class="groupStyle(group).text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
            </div>
            <div>
              <h2 class="text-lg font-bold text-gray-900 dark:text-white capitalize">{{ group }} Settings</h2>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ groupDescription(group) }}</p>
            </div>
          </div>

          <!-- Settings Fields -->
          <div class="space-y-5">
            <div v-for="setting in groupedSettings[group]" :key="setting.key" class="group/field">
              <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                {{ formatLabel(setting.key) }}
                <span v-if="fieldHint(setting.key)" class="text-xs font-normal text-gray-400">{{ fieldHint(setting.key) }}</span>
              </label>

              <!-- URL Fields -->
              <div v-if="isUrlField(setting.key)" class="relative">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                </div>
                <input v-model="setting.value" type="url" class="input-field !pl-10 font-mono text-sm" placeholder="https://" @input="markChanged" />
              </div>

              <!-- Email Fields -->
              <div v-else-if="isEmailField(setting.key)" class="relative">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <input v-model="setting.value" type="email" class="input-field !pl-10" placeholder="email@example.com" @input="markChanged" />
              </div>

              <!-- Textarea Fields -->
              <div v-else-if="isTextareaField(setting.key)">
                <textarea v-model="setting.value" rows="3" class="input-field resize-y min-h-[80px]" :placeholder="'Enter ' + formatLabel(setting.key).toLowerCase() + '...'" @input="markChanged" />
                <p class="text-xs text-gray-400 mt-1 text-right">{{ (setting.value || '').length }} characters</p>
              </div>

              <!-- Standard Input -->
              <div v-else>
                <input v-model="setting.value" type="text" class="input-field" :placeholder="'Enter ' + formatLabel(setting.key).toLowerCase() + '...'" @input="markChanged" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Save Footer (Sticky) -->
      <Transition enter-active-class="transition-all duration-300 ease-out" leave-active-class="transition-all duration-300 ease-in" enter-from-class="opacity-0 translate-y-4" leave-to-class="opacity-0 translate-y-4">
        <div v-if="hasChanges" class="sticky bottom-4 z-10">
          <div class="glass-card !p-4 border border-amber-200 dark:border-amber-800/30 bg-amber-50/80 dark:bg-amber-900/10 backdrop-blur-lg flex items-center justify-between">
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
              <p class="text-sm font-medium text-amber-800 dark:text-amber-300">You have unsaved changes</p>
            </div>
            <div class="flex items-center gap-2">
              <button @click="resetChanges" class="px-3 py-1.5 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors">Discard</button>
              <button @click="handleSave" :disabled="saving" class="btn-primary text-sm flex items-center gap-2">
                <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                {{ saving ? 'Saving...' : 'Save Changes' }}
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </div>

    <!-- Success Toast Animation -->
    <Teleport to="body">
      <Transition enter-active-class="transition-all duration-300" leave-active-class="transition-all duration-300" enter-from-class="opacity-0 scale-95" leave-to-class="opacity-0 scale-95">
        <div v-if="showSuccess" class="fixed bottom-8 right-8 z-50 bg-green-500 text-white px-5 py-3 rounded-xl shadow-lg flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          Settings saved successfully!
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { adminApi } from '@/services/api'
import { useUiStore } from '@/stores/ui'

const uiStore = useUiStore()
const groupedSettings = ref({})
const originalValues = ref({})
const loading = ref(true)
const saving = ref(false)
const hasChanges = ref(false)
const showSuccess = ref(false)
const activeGroup = ref('')

const groupNames = computed(() => Object.keys(groupedSettings.value))

function formatLabel(key) {
  return key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
}

function groupStyle(group) {
  const styles = {
    general: { bg: 'bg-brand-violet/10 dark:bg-brand-violet/20', text: 'text-brand-violet' },
    social: { bg: 'bg-blue-500/10', text: 'text-blue-500' },
    seo: { bg: 'bg-green-500/10', text: 'text-green-500' },
  }
  return styles[group] || { bg: 'bg-gray-100 dark:bg-dark-700', text: 'text-gray-500' }
}

function groupDescription(group) {
  const desc = {
    general: 'Basic site information like name, tagline, and description',
    social: 'Contact information and social media links',
    seo: 'Search engine optimization and meta tags',
  }
  return desc[group] || 'Configure settings for this section'
}

function fieldHint(key) {
  const hints = {
    site_name: '— Displayed in header & browser tab',
    site_tagline: '— Short slogan shown on homepage',
    site_description: '— Used in about sections',
    contact_email: '— Public contact email',
    meta_title: '— Browser tab & search results title',
    meta_description: '— Search results snippet text',
  }
  return hints[key] || ''
}

function isUrlField(key) {
  return key.includes('_url') || key.includes('_link')
}

function isEmailField(key) {
  return key.includes('email')
}

function isTextareaField(key) {
  return key.includes('description') || key.includes('about') || key.includes('bio')
}

function markChanged() {
  hasChanges.value = true
}

function resetChanges() {
  // Restore original values
  for (const group of Object.keys(groupedSettings.value)) {
    for (const setting of groupedSettings.value[group]) {
      setting.value = originalValues.value[setting.key] ?? setting.value
    }
  }
  hasChanges.value = false
}

onMounted(async () => {
  try {
    const { data } = await adminApi.getSettings()
    groupedSettings.value = data.data || {}
    // Store original values for discard
    for (const group of Object.keys(groupedSettings.value)) {
      for (const setting of groupedSettings.value[group]) {
        originalValues.value[setting.key] = setting.value
      }
    }
    // Set first group as active
    const groups = Object.keys(groupedSettings.value)
    if (groups.length) activeGroup.value = groups[0]
  } catch {} finally { loading.value = false }
})

async function handleSave() {
  saving.value = true
  const settings = {}
  Object.values(groupedSettings.value).flat().forEach(s => { settings[s.key] = s.value })
  try {
    await adminApi.updateSettings({ settings })
    // Update original values
    for (const key of Object.keys(settings)) {
      originalValues.value[key] = settings[key]
    }
    hasChanges.value = false
    showSuccess.value = true
    setTimeout(() => { showSuccess.value = false }, 3000)
    uiStore.showToast('Settings saved successfully!')
  } catch {
    uiStore.showToast('Failed to save settings', 'error')
  } finally {
    saving.value = false
  }
}
</script>
