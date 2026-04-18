<template>
  <div>
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm mb-6">
      <router-link to="/admin/docs" class="text-gray-500 hover:text-brand-violet dark:hover:text-brand-cyan transition-colors">Docs</router-link>
      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      <span class="text-gray-900 dark:text-white font-medium">{{ isEdit ? 'Edit Doc' : 'New Doc' }}</span>
    </nav>

    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-2xl font-sans font-bold dark:text-white">{{ isEdit ? 'Edit Doc' : 'Create Doc' }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ isEdit ? 'Update documentation content and settings' : 'Write a new documentation page' }}</p>
      </div>
    </div>

    <form @submit.prevent="handleSubmit">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ══ Left Column: Content ══ -->
        <div class="lg:col-span-2 space-y-6">

          <!-- Title & Slug -->
          <div class="glass-card space-y-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
              <svg class="w-4 h-4 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
              Doc Details
            </h2>

            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Title <span class="text-red-400">*</span></label>
              <input v-model="form.title" @input="autoSlug" type="text" required class="input-field" placeholder="e.g. Getting Started with Kalapak" />
            </div>

            <!-- Permalink (WordPress-style) -->
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Permalink</label>
              <div v-if="!slugEditing" class="flex items-center gap-2 px-3 py-2 bg-gray-50 dark:bg-dark-700/40 border border-gray-200 dark:border-dark-600 rounded-lg">
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                <span class="text-xs text-gray-500 dark:text-gray-400 truncate">kalapak-team.space/docs?page=<span class="text-brand-violet dark:text-brand-cyan font-medium">{{ form.slug || slugify(form.title || 'doc-title') }}</span></span>
                <button type="button" @click="slugEditing = true" class="ml-auto shrink-0 text-[10px] font-semibold text-brand-violet dark:text-brand-cyan hover:underline">Edit</button>
              </div>
              <div v-else class="space-y-2">
                <div class="flex items-center">
                  <span class="inline-flex items-center px-3 h-[38px] text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-dark-600 border border-r-0 border-gray-200 dark:border-dark-500 rounded-l-lg">/docs?page=</span>
                  <input v-model="form.slug" @input="slugManuallyEdited = true" ref="slugInputRef" type="text" required class="input-field !rounded-l-none" placeholder="getting-started" />
                </div>
                <div class="flex items-center gap-2">
                  <button type="button" @click="slugEditing = false" class="text-[10px] font-semibold text-brand-violet dark:text-brand-cyan hover:underline">Done</button>
                  <span class="text-gray-300 dark:text-dark-500">|</span>
                  <button type="button" @click="resetSlug" class="text-[10px] font-semibold text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:underline">Reset from title</button>
                  <span v-if="slugManuallyEdited" class="ml-auto text-[10px] text-amber-500 dark:text-amber-400 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Manually edited
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Content Editor -->
          <div class="glass-card space-y-4">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
              <svg class="w-4 h-4 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              Content <span class="text-red-400">*</span>
            </h2>
            <ContentEditor v-if="editorReady" v-model="form.content" />
            <div v-else class="flex items-center justify-center h-48 text-gray-400">
              <svg class="w-5 h-5 animate-spin mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              Loading editor...
            </div>
          </div>
        </div>

        <!-- ══ Right Column: Settings Sidebar ══ -->
        <div class="space-y-6">

          <!-- Publish Settings -->
          <div class="glass-card space-y-4">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
              <svg class="w-4 h-4 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              Settings
            </h2>

            <!-- Status -->
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Status</label>
              <div class="flex gap-2">
                <button
                  type="button"
                  @click="form.status = 'draft'"
                  :class="form.status === 'draft'
                    ? 'bg-yellow-50 text-yellow-700 border-yellow-300 dark:bg-yellow-500/10 dark:text-yellow-400 dark:border-yellow-500/30'
                    : 'bg-white dark:bg-dark-700 text-gray-500 border-gray-200 dark:border-dark-600 hover:border-gray-300'"
                  class="flex-1 px-3 py-2 rounded-lg border text-xs font-medium transition-all text-center"
                >Draft</button>
                <button
                  type="button"
                  @click="form.status = 'published'"
                  :class="form.status === 'published'
                    ? 'bg-green-50 text-green-700 border-green-300 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/30'
                    : 'bg-white dark:bg-dark-700 text-gray-500 border-gray-200 dark:border-dark-600 hover:border-gray-300'"
                  class="flex-1 px-3 py-2 rounded-lg border text-xs font-medium transition-all text-center"
                >Published</button>
              </div>
            </div>

            <!-- Category -->
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Category <span class="text-red-400">*</span></label>
              <input
                v-model="form.category"
                type="text"
                list="category-list"
                required
                class="input-field"
                placeholder="e.g. Getting Started"
              />
              <datalist id="category-list">
                <option v-for="cat in categories" :key="cat" :value="cat" />
              </datalist>
              <p class="text-[10px] text-gray-400 mt-1">Used to group docs in the sidebar.</p>
            </div>

            <!-- Order -->
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Order (within category)</label>
              <input v-model.number="form.order_num" type="number" min="0" class="input-field" placeholder="0" />
              <p class="text-[10px] text-gray-400 mt-1">Lower numbers appear first.</p>
            </div>
          </div>

          <!-- Actions -->
          <div class="glass-card space-y-3">
            <button type="submit" :disabled="saving" class="btn-primary w-full flex items-center justify-center gap-2 text-sm">
              <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              {{ saving ? 'Saving...' : (isEdit ? 'Update Doc' : (form.status === 'published' ? 'Publish Doc' : 'Save Draft')) }}
            </button>
            <router-link to="/admin/docs" class="btn-ghost w-full flex items-center justify-center gap-2 text-sm">
              Cancel
            </router-link>
            <p v-if="error" class="text-sm text-red-500 text-center">{{ error }}</p>
          </div>

          <!-- Preview link (published only) -->
          <div v-if="isEdit && form.status === 'published' && form.slug" class="glass-card">
            <a
              :href="`/docs?page=${form.slug}`"
              target="_blank"
              class="flex items-center gap-2 text-sm text-brand-violet dark:text-brand-cyan hover:underline"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
              View public doc
            </a>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { adminApi } from '@/services/api'
import ContentEditor from '@/components/common/ContentEditor.vue'

const route = useRoute()
const router = useRouter()

const isEdit = computed(() => !!route.params.id)
const editorReady = ref(false)
const saving = ref(false)
const error = ref('')
const slugEditing = ref(false)
const slugManuallyEdited = ref(false)
const slugInputRef = ref(null)
const categories = ref([])

const form = ref({
  title: '',
  slug: '',
  content: '',
  category: '',
  order_num: 0,
  status: 'draft',
})

function slugify(text) {
  return text
    .toString()
    .toLowerCase()
    .trim()
    .replace(/\s+/g, '-')
    .replace(/[^\w-]+/g, '')
    .replace(/--+/g, '-')
    .replace(/^-+|-+$/g, '')
}

function autoSlug() {
  if (!slugManuallyEdited.value) {
    form.value.slug = slugify(form.value.title)
  }
}

function resetSlug() {
  form.value.slug = slugify(form.value.title)
  slugManuallyEdited.value = false
}

async function fetchCategories() {
  try {
    const { data } = await adminApi.getDocCategories()
    categories.value = data.data || []
  } catch {
    categories.value = []
  }
}

async function fetchDoc() {
  try {
    const { data } = await adminApi.getDoc(route.params.id)
    const doc = data.data
    form.value = {
      title: doc.title,
      slug: doc.slug,
      content: doc.content,
      category: doc.category,
      order_num: doc.order_num ?? 0,
      status: doc.status,
    }
    slugManuallyEdited.value = true // prevent auto-overwrite on edit
  } catch {
    error.value = 'Failed to load doc.'
  }
}

async function handleSubmit() {
  error.value = ''
  saving.value = true
  try {
    const payload = { ...form.value }
    if (!payload.slug) payload.slug = slugify(payload.title)

    if (isEdit.value) {
      await adminApi.updateDoc(route.params.id, payload)
    } else {
      await adminApi.createDoc(payload)
    }
    router.push({ name: 'admin-docs' })
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to save doc. Please try again.'
  } finally {
    saving.value = false
  }
}

onMounted(async () => {
  await fetchCategories()
  if (isEdit.value) {
    await fetchDoc()
  }
  await nextTick()
  editorReady.value = true
})
</script>
