<template>
  <div>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-sans font-bold dark:text-white">Series</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Group blog posts into themed series</p>
      </div>
      <button type="button" class="btn-primary flex items-center gap-2 text-sm" @click="openCreate">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Series
      </button>
    </div>

    <div class="glass-card !p-4 mb-6">
      <div class="relative max-w-md">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input v-model="search" type="text" placeholder="Search series…" class="input-field !pl-10 w-full" @keyup.enter="fetchList(1)" />
      </div>
    </div>

    <div v-if="loading" class="flex justify-center py-20">
      <div class="w-10 h-10 border-4 border-brand-violet/30 border-t-brand-violet rounded-full animate-spin" />
    </div>

    <div v-else class="glass-card overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-gray-200 dark:border-dark-600 text-left text-gray-500 dark:text-gray-400">
            <th class="py-3 px-4 font-medium">Name</th>
            <th class="py-3 px-4 font-medium hidden sm:table-cell">Slug</th>
            <th class="py-3 px-4 font-medium hidden md:table-cell">Description</th>
            <th class="py-3 px-4 font-medium">Posts</th>
            <th class="py-3 px-4 font-medium hidden lg:table-cell">Created</th>
            <th class="py-3 px-4 font-medium text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in rows" :key="row.id" class="border-b border-gray-100 dark:border-dark-600/80 hover:bg-gray-50/80 dark:hover:bg-white/[0.02]">
            <td class="py-3 px-4">
              <router-link
                :to="{ name: 'public-series', params: { username: row.author?.username || 'user', slug: row.slug } }"
                class="font-semibold text-gray-900 dark:text-white hover:text-brand-violet dark:hover:text-brand-cyan"
              >
                {{ row.name }}
              </router-link>
            </td>
            <td class="py-3 px-4 font-mono text-xs text-gray-500 hidden sm:table-cell">{{ row.slug }}</td>
            <td class="py-3 px-4 text-gray-500 dark:text-gray-400 max-w-xs truncate hidden md:table-cell">{{ row.description || '—' }}</td>
            <td class="py-3 px-4">
              <router-link
                :to="{ name: 'admin-blog', query: { series_id: row.id } }"
                class="text-brand-violet dark:text-brand-cyan hover:underline font-medium"
              >
                {{ row.posts_count ?? 0 }}
              </router-link>
            </td>
            <td class="py-3 px-4 text-gray-500 hidden lg:table-cell">{{ formatDate(row.created_at) }}</td>
            <td class="py-3 px-4 text-right whitespace-nowrap">
              <button type="button" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-600 text-gray-500" title="Edit" @click="openEdit(row)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
              </button>
              <button type="button" class="p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-red-500" title="Delete" @click="confirmDelete(row)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="!rows.length" class="text-center py-12 text-gray-500 text-sm">No series yet. Create one to organize posts.</div>
      <div v-if="meta.last_page > 1" class="p-4 border-t border-gray-100 dark:border-dark-600">
        <Pagination :current-page="meta.current_page" :last-page="meta.last_page" @page-change="fetchList" />
      </div>
    </div>

    <teleport to="body">
      <transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="modalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="modalOpen = false">
          <div class="w-full max-w-md rounded-2xl bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 shadow-xl p-6 space-y-4">
            <h2 class="text-lg font-bold dark:text-white">{{ editingId ? 'Edit Series' : 'New Series' }}</h2>
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Name</label>
              <input v-model="form.name" type="text" class="input-field w-full" @input="onNameInput" />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Slug</label>
              <input v-model="form.slug" type="text" class="input-field w-full font-mono text-sm" @input="slugTouched = true" />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Description</label>
              <textarea v-model="form.description" rows="3" class="input-field w-full" />
            </div>
            <p v-if="modalError" class="text-sm text-red-500">{{ modalError }}</p>
            <div class="flex justify-end gap-2 pt-2">
              <button type="button" class="btn-ghost text-sm" @click="modalOpen = false">Cancel</button>
              <button type="button" class="btn-primary text-sm" :disabled="saving" @click="saveModal">{{ saving ? 'Saving…' : 'Save' }}</button>
            </div>
          </div>
        </div>
      </transition>
    </teleport>

    <teleport to="body">
      <transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="deleteTarget" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="deleteTarget = null">
          <div class="w-full max-w-sm rounded-2xl bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 shadow-xl p-6 text-center">
            <p class="text-gray-700 dark:text-gray-200 mb-4">Delete series <strong>{{ deleteTarget.name }}</strong>? Posts stay published and become uncategorized.</p>
            <div class="flex justify-center gap-2">
              <button type="button" class="btn-ghost text-sm" @click="deleteTarget = null">Cancel</button>
              <button type="button" class="px-4 py-2 rounded-lg bg-red-500 text-white text-sm font-medium" :disabled="deleting" @click="doDelete">{{ deleting ? '…' : 'Delete' }}</button>
            </div>
          </div>
        </div>
      </transition>
    </teleport>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { adminApi } from '@/services/api'
import { useUiStore } from '@/stores/ui'
import Pagination from '@/components/common/Pagination.vue'

const uiStore = useUiStore()
const loading = ref(true)
const rows = ref([])
const meta = ref({ current_page: 1, last_page: 1, per_page: 15, total: 0 })
const search = ref('')
const modalOpen = ref(false)
const editingId = ref(null)
const form = ref({ name: '', slug: '', description: '' })
const modalError = ref('')
const saving = ref(false)
const slugTouched = ref(false)
const deleteTarget = ref(null)
const deleting = ref(false)

function slugify(t) {
  return String(t || '').toLowerCase().replace(/[^\w\s-]/g, '').replace(/[\s_]+/g, '-').replace(/^-+|-+$/g, '') || 'series'
}

function onNameInput() {
  if (!editingId.value && !slugTouched.value) {
    form.value.slug = slugify(form.value.name)
  }
}

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

async function fetchList(page = 1) {
  loading.value = true
  try {
    const { data } = await adminApi.getSeries({ page, search: search.value || undefined })
    rows.value = data.data || []
    meta.value = data.meta || meta.value
  } catch {
    rows.value = []
    uiStore.showToast('Failed to load series', 'error')
  } finally {
    loading.value = false
  }
}

function openCreate() {
  editingId.value = null
  form.value = { name: '', slug: '', description: '' }
  slugTouched.value = false
  modalError.value = ''
  modalOpen.value = true
}

function openEdit(row) {
  editingId.value = row.id
  form.value = { name: row.name, slug: row.slug, description: row.description || '' }
  slugTouched.value = true
  modalError.value = ''
  modalOpen.value = true
}

async function saveModal() {
  modalError.value = ''
  saving.value = true
  try {
    if (editingId.value) {
      await adminApi.updateSeries(editingId.value, { ...form.value })
      uiStore.showToast('Series updated')
    } else {
      await adminApi.createSeries({ ...form.value })
      uiStore.showToast('Series created')
    }
    modalOpen.value = false
    fetchList(meta.value.current_page)
  } catch (e) {
    const msg = e.response?.data?.message
    const errs = e.response?.data?.errors
    modalError.value = errs ? Object.values(errs).flat().join(' ') : (msg || 'Save failed')
  } finally {
    saving.value = false
  }
}

function confirmDelete(row) {
  deleteTarget.value = row
}

async function doDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  try {
    await adminApi.deleteSeries(deleteTarget.value.id)
    uiStore.showToast('Series deleted')
    deleteTarget.value = null
    fetchList(meta.value.current_page)
  } catch (e) {
    uiStore.showToast(e.response?.data?.message || 'Delete failed', 'error')
  } finally {
    deleting.value = false
  }
}

onMounted(() => fetchList(1))
</script>
