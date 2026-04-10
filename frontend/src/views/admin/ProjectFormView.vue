<template>
  <div>
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm mb-6">
      <router-link to="/admin/projects" class="text-gray-500 hover:text-brand-violet dark:hover:text-brand-cyan transition-colors">Projects</router-link>
      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      <span class="text-gray-900 dark:text-white font-medium">{{ isEdit ? 'Edit Project' : 'New Project' }}</span>
    </nav>

    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-2xl font-sans font-bold dark:text-white">{{ isEdit ? 'Edit Project' : 'Create Project' }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ isEdit ? 'Update project details and settings' : 'Add a new project to your portfolio' }}</p>
      </div>
    </div>

    <form @submit.prevent="handleSubmit">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Basic Info Card -->
          <div class="glass-card space-y-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
              <svg class="w-4 h-4 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              Basic Information
            </h2>
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Title <span class="text-red-400">*</span></label>
              <input v-model="form.title" @input="autoSlug" type="text" required class="input-field" placeholder="My Awesome Project" />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Slug <span class="text-red-400">*</span></label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">/projects/</span>
                <input v-model="form.slug" type="text" required class="input-field !pl-20" placeholder="my-awesome-project" />
              </div>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Short Description <span class="text-red-400">*</span></label>
              <textarea v-model="form.description" required rows="2" class="input-field" placeholder="A brief summary of the project..." />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                Long Description
              </label>
              <ContentEditor v-if="editorReady" v-model="form.long_description" />
              <div v-else class="flex items-center justify-center h-48 border border-gray-200 dark:border-dark-600 rounded-xl text-gray-400">
                <svg class="w-5 h-5 animate-spin mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Loading editor...
              </div>
            </div>
          </div>

          <!-- Links Card -->
          <div class="glass-card space-y-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
              <svg class="w-4 h-4 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
              Links
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Demo URL</label>
                <div class="relative">
                  <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                  <input v-model="form.demo_url" type="url" class="input-field !pl-10" placeholder="https://demo.example.com" />
                </div>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Repository URL</label>
                <div class="relative">
                  <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
                  <input v-model="form.repo_url" type="url" class="input-field !pl-10" placeholder="https://github.com/user/repo" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="space-y-6">
          <!-- Cover Image Card -->
          <div class="glass-card space-y-4">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
              <svg class="w-4 h-4 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              Cover Image
            </h2>
            <div class="relative">
              <div v-if="imagePreview || existingImage" class="relative rounded-xl overflow-hidden group">
                <img :src="imagePreview || existingImage" alt="Cover preview" class="w-full h-40 object-cover" />
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                  <button type="button" @click="removeImage" class="px-3 py-1.5 bg-white/90 text-gray-800 text-xs rounded-lg font-medium hover:bg-white transition-colors">Remove</button>
                </div>
              </div>
              <label v-else class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 dark:border-dark-500 rounded-xl cursor-pointer hover:border-brand-violet dark:hover:border-brand-cyan transition-colors bg-gray-50/50 dark:bg-dark-700/30">
                <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                <span class="text-xs text-gray-500">Click to upload</span>
                <span class="text-[10px] text-gray-400 mt-1">PNG, JPG up to 2MB</span>
                <input type="file" accept="image/*" @change="onFileChange" class="hidden" />
              </label>
            </div>
          </div>

          <!-- Settings Card -->
          <div class="glass-card space-y-4">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
              <svg class="w-4 h-4 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              Settings
            </h2>
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Status</label>
              <CustomSelect
                v-model="form.status"
                :options="[{ label: 'Active', value: 'active' }, { label: 'Work in Progress', value: 'wip' }, { label: 'Archived', value: 'archived' }]"
                placeholder="Active"
              />
            </div>
            <div class="flex items-center justify-between py-2">
              <div>
                <p class="text-sm font-medium dark:text-gray-200">Featured</p>
                <p class="text-[10px] text-gray-500 dark:text-gray-400">Show on homepage</p>
              </div>
              <button type="button" @click="form.is_featured = !form.is_featured" :class="form.is_featured ? 'bg-brand-violet dark:bg-brand-cyan' : 'bg-gray-300 dark:bg-dark-600'" class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors">
                <span :class="form.is_featured ? 'translate-x-5' : 'translate-x-1'" class="inline-block h-3 w-3 rounded-full bg-white transition-transform" />
              </button>
            </div>
            <div class="flex items-center justify-between py-2">
              <div>
                <p class="text-sm font-medium dark:text-gray-200">Open Source</p>
                <p class="text-[10px] text-gray-500 dark:text-gray-400">Publicly available code</p>
              </div>
              <button type="button" @click="form.is_open_source = !form.is_open_source" :class="form.is_open_source ? 'bg-brand-violet dark:bg-brand-cyan' : 'bg-gray-300 dark:bg-dark-600'" class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors">
                <span :class="form.is_open_source ? 'translate-x-5' : 'translate-x-1'" class="inline-block h-3 w-3 rounded-full bg-white transition-transform" />
              </button>
            </div>
          </div>

          <!-- Tags Card -->
          <div class="glass-card space-y-4">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
              <svg class="w-4 h-4 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
              Tags
            </h2>
            <div v-if="availableTags.length" class="flex flex-wrap gap-2">
              <button v-for="tag in availableTags" :key="tag.id" type="button" @click="toggleTag(tag.id)" :class="selectedTags.includes(tag.id) ? 'ring-2 ring-offset-1 dark:ring-offset-dark-800' : 'opacity-60 hover:opacity-100'" :style="{ backgroundColor: (tag.color || '#6c5ce7') + '20', color: tag.color || '#6c5ce7', ringColor: tag.color || '#6c5ce7' }" class="px-3 py-1 rounded-full text-xs font-medium transition-all">
                {{ tag.name }}
              </button>
            </div>
            <p v-else class="text-xs text-gray-500 dark:text-gray-400">No tags available</p>
          </div>

          <!-- Actions Card -->
          <div class="glass-card space-y-3">
            <button type="submit" :disabled="saving" class="btn-primary w-full flex items-center justify-center gap-2 text-sm">
              <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              {{ saving ? 'Saving...' : (isEdit ? 'Update Project' : 'Create Project') }}
            </button>
            <router-link to="/admin/projects" class="btn-ghost w-full flex items-center justify-center gap-2 text-sm">
              Cancel
            </router-link>
            <p v-if="error" class="text-sm text-red-500 text-center">{{ error }}</p>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { adminApi, publicApi } from '@/services/api'
import { useUiStore } from '@/stores/ui'
import ContentEditor from '@/components/common/ContentEditor.vue'
import CustomSelect from '@/components/common/CustomSelect.vue'

const route = useRoute()
const router = useRouter()
const uiStore = useUiStore()

const isEdit = computed(() => !!route.params.id)
const form = ref({
  title: '', slug: '', description: '', long_description: '', status: 'active',
  is_featured: false, is_open_source: false, demo_url: '', repo_url: '',
})
const availableTags = ref([])
const selectedTags = ref([])
const coverImage = ref(null)
const imagePreview = ref(null)
const existingImage = ref(null)
const saving = ref(false)
const error = ref('')
const editorReady = ref(false)

function onFileChange(e) {
  const file = e.target.files[0]
  if (file) {
    coverImage.value = file
    imagePreview.value = URL.createObjectURL(file)
  }
}

function removeImage() {
  coverImage.value = null
  imagePreview.value = null
  existingImage.value = null
}

function toggleTag(id) {
  const i = selectedTags.value.indexOf(id)
  i === -1 ? selectedTags.value.push(id) : selectedTags.value.splice(i, 1)
}

function slugify(text) {
  return text.toLowerCase().replace(/[^\w\s-]/g, '').replace(/[\s_]+/g, '-').replace(/^-+|-+$/g, '')
}

function autoSlug() {
  if (!isEdit.value) form.value.slug = slugify(form.value.title)
}

onMounted(async () => {
  try {
    const { data } = await publicApi.getTags()
    availableTags.value = data.data || data
  } catch { /* ignore */ }

  if (isEdit.value) {
    try {
      const { data } = await adminApi.getProject(route.params.id)
      const p = data.data
      form.value = {
        title: p.title, slug: p.slug, description: p.description, long_description: p.long_description || '',
        status: p.status, is_featured: p.is_featured, is_open_source: p.is_open_source || false,
        demo_url: p.demo_url || '', repo_url: p.repo_url || '',
      }
      selectedTags.value = (p.tags || []).map((t) => t.id)
      if (p.cover_image) existingImage.value = p.cover_image
    } catch { /* ignore */ }
  }
  editorReady.value = true
})

async function handleSubmit() {
  saving.value = true; error.value = ''
  if (!form.value.slug) form.value.slug = slugify(form.value.title)

  const formData = new FormData()
  Object.entries(form.value).forEach(([k, v]) => {
    if (v !== '' && v !== null && v !== undefined) formData.append(k, v)
  })
  selectedTags.value.forEach((id) => formData.append('tags[]', id))
  if (coverImage.value) formData.append('cover_image', coverImage.value)
  if (isEdit.value) formData.append('_method', 'PUT')

  try {
    if (isEdit.value) {
      await adminApi.updateProject(route.params.id, formData)
    } else {
      await adminApi.createProject(formData)
    }
    uiStore.showToast(`Project ${isEdit.value ? 'updated' : 'created'}!`)
    router.push({ name: 'admin-projects' })
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to save'
  } finally { saving.value = false }
}
</script>
