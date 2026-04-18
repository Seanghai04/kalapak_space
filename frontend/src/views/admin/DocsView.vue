<template>
  <div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-sans font-bold dark:text-white">Docs</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage documentation pages and guides</p>
      </div>
      <router-link :to="{ name: 'admin-docs-create' }" class="btn-primary flex items-center gap-2 text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Doc
      </router-link>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-brand-violet/10 dark:bg-brand-violet/20 flex items-center justify-center">
          <svg class="w-5 h-5 text-brand-violet" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Total Docs</p>
          <p class="text-lg font-bold dark:text-white">{{ meta.total || 0 }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Published</p>
          <p class="text-lg font-bold text-green-500">{{ publishedCount }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-yellow-500/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Drafts</p>
          <p class="text-lg font-bold text-yellow-500">{{ draftCount }}</p>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="glass-card !p-4 mb-6">
      <div class="flex flex-col md:flex-row items-start md:items-center gap-3">
        <div class="relative flex-1 w-full">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input v-model="search" type="text" placeholder="Search by title..." class="input-field !pl-10 w-full" @input="debouncedFetch" />
        </div>
        <select v-model="statusFilter" class="input-field w-full md:w-40" @change="fetchDocs()">
          <option value="">All Status</option>
          <option value="published">Published</option>
          <option value="draft">Draft</option>
        </select>
        <select v-model="categoryFilter" class="input-field w-full md:w-48" @change="fetchDocs()">
          <option value="">All Categories</option>
          <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
        </select>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="w-10 h-10 border-4 border-brand-violet/30 border-t-brand-violet rounded-full animate-spin" />
    </div>

    <!-- Table -->
    <div v-else-if="docs.length" class="glass-card overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-gray-200 dark:border-dark-600">
            <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-400">Title</th>
            <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-400 hidden md:table-cell">Category</th>
            <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-400 hidden sm:table-cell">Status</th>
            <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-400 hidden lg:table-cell">Author</th>
            <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-400 hidden lg:table-cell">Updated</th>
            <th class="py-3 px-4 w-24"></th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="doc in docs"
            :key="doc.id"
            class="border-b border-gray-100 dark:border-dark-700 hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors"
          >
            <td class="py-3 px-4">
              <p class="font-semibold text-gray-900 dark:text-white line-clamp-1">{{ doc.title }}</p>
              <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ doc.slug }}</p>
            </td>
            <td class="py-3 px-4 hidden md:table-cell">
              <span class="px-2 py-1 bg-gray-100 dark:bg-white/[0.06] text-gray-600 dark:text-gray-300 rounded-md text-xs">{{ doc.category }}</span>
            </td>
            <td class="py-3 px-4 hidden sm:table-cell">
              <span
                class="px-2 py-1 rounded-full text-xs font-semibold"
                :class="doc.status === 'published'
                  ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
                  : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400'"
              >{{ doc.status }}</span>
            </td>
            <td class="py-3 px-4 hidden lg:table-cell text-gray-500 dark:text-gray-400 text-xs">
              {{ doc.author?.name || '—' }}
            </td>
            <td class="py-3 px-4 hidden lg:table-cell text-gray-500 dark:text-gray-400 text-xs">
              {{ formatDate(doc.updated_at) }}
            </td>
            <td class="py-3 px-4">
              <div class="flex items-center justify-end gap-1">
                <router-link
                  :to="{ name: 'admin-docs-edit', params: { id: doc.id } }"
                  class="p-1.5 rounded-lg text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan hover:bg-gray-100 dark:hover:bg-white/[0.06] transition-colors"
                  title="Edit"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </router-link>
                <button
                  @click="confirmDelete(doc)"
                  class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                  title="Delete"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="meta.last_page > 1" class="flex items-center justify-between px-4 py-3 border-t border-gray-200 dark:border-dark-600">
        <p class="text-xs text-gray-500">Showing {{ docs.length }} of {{ meta.total }} docs</p>
        <div class="flex items-center gap-1">
          <button :disabled="meta.current_page === 1" @click="changePage(meta.current_page - 1)" class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-dark-600 disabled:opacity-40 hover:border-brand-violet transition-colors">Prev</button>
          <span class="text-xs text-gray-500 px-2">{{ meta.current_page }} / {{ meta.last_page }}</span>
          <button :disabled="meta.current_page === meta.last_page" @click="changePage(meta.current_page + 1)" class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-dark-600 disabled:opacity-40 hover:border-brand-violet transition-colors">Next</button>
        </div>
      </div>
    </div>

    <!-- Empty -->
    <div v-else class="glass-card flex flex-col items-center justify-center py-20 text-center">
      <svg class="w-14 h-14 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
      <h3 class="font-semibold dark:text-white mb-1">No docs yet</h3>
      <p class="text-sm text-gray-400 mb-4">Create your first documentation page</p>
      <router-link :to="{ name: 'admin-docs-create' }" class="btn-primary text-sm">New Doc</router-link>
    </div>

    <!-- Delete Confirm Modal -->
    <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
      <div class="glass-card max-w-sm w-full">
        <h3 class="font-bold text-lg dark:text-white mb-2">Delete Doc?</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
          "<strong class="dark:text-white">{{ deleteTarget.title }}</strong>" will be permanently deleted.
        </p>
        <div class="flex justify-end gap-3">
          <button @click="deleteTarget = null" class="btn-ghost text-sm">Cancel</button>
          <button @click="deleteDoc" :disabled="deleting" class="btn-danger text-sm flex items-center gap-2">
            <svg v-if="deleting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { adminApi } from '@/services/api'

const docs = ref([])
const meta = ref({ total: 0, current_page: 1, last_page: 1, per_page: 20 })
const categories = ref([])
const loading = ref(true)
const search = ref('')
const statusFilter = ref('')
const categoryFilter = ref('')
const deleteTarget = ref(null)
const deleting = ref(false)
let debounceTimer = null

const publishedCount = computed(() => docs.value.filter(d => d.status === 'published').length)
const draftCount = computed(() => docs.value.filter(d => d.status === 'draft').length)

async function fetchDocs(page = 1) {
  loading.value = true
  try {
    const params = { page, per_page: 20 }
    if (search.value) params.search = search.value
    if (statusFilter.value) params.status = statusFilter.value
    if (categoryFilter.value) params.category = categoryFilter.value
    const { data } = await adminApi.getDocs(params)
    docs.value = data.data || []
    meta.value = data.meta || meta.value
  } catch {
    docs.value = []
  } finally {
    loading.value = false
  }
}

async function fetchCategories() {
  try {
    const { data } = await adminApi.getDocCategories()
    categories.value = data.data || []
  } catch {
    categories.value = []
  }
}

function debouncedFetch() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => fetchDocs(), 400)
}

function changePage(page) {
  fetchDocs(page)
}

function confirmDelete(doc) {
  deleteTarget.value = doc
}

async function deleteDoc() {
  if (!deleteTarget.value) return
  deleting.value = true
  try {
    await adminApi.deleteDoc(deleteTarget.value.id)
    deleteTarget.value = null
    await fetchDocs(meta.value.current_page)
  } finally {
    deleting.value = false
  }
}

function formatDate(str) {
  if (!str) return '—'
  return new Date(str).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

onMounted(() => {
  fetchDocs()
  fetchCategories()
})
</script>
