<template>
  <div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-sans font-bold dark:text-white">Doc Categories</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage categories used to group documentation pages.</p>
      </div>
      <button @click="openCreate" class="btn-primary flex items-center gap-2 text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Category
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="w-10 h-10 border-4 border-brand-violet/30 border-t-brand-violet rounded-full animate-spin" />
    </div>

    <!-- Empty -->
    <div v-else-if="!categories.length" class="glass-card flex flex-col items-center justify-center py-20 text-center">
      <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/>
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/>
      </svg>
      <p class="text-gray-500 dark:text-gray-400 font-medium">No categories yet.</p>
      <p class="text-xs text-gray-400 mt-1">Create a category or assign one when editing a doc.</p>
    </div>

    <!-- Category list -->
    <div v-else class="glass-card overflow-hidden">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-gray-200 dark:border-dark-600">
            <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-400">#</th>
            <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-400">Name</th>
            <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-400">Docs</th>
            <th class="py-3 px-4"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-dark-700">
          <tr
            v-for="(cat, idx) in categories"
            :key="cat.name"
            class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors"
          >
            <td class="py-3 px-4 text-xs font-mono text-gray-300 dark:text-gray-600">{{ idx + 1 }}</td>
            <td class="py-3 px-4">
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                           bg-brand-violet/10 dark:bg-brand-cyan/10 text-brand-violet dark:text-brand-cyan">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                {{ cat.name }}
              </span>
            </td>
            <td class="py-3 px-4">
              <router-link
                :to="{ name: 'admin-docs', query: { category: cat.name } }"
                class="text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan transition-colors"
              >
                {{ cat.doc_count }} {{ cat.doc_count === 1 ? 'doc' : 'docs' }}
              </router-link>
            </td>
            <td class="py-3 px-4">
              <div class="flex items-center justify-end gap-1">
                <button
                  @click="openEdit(cat)"
                  class="p-1.5 rounded-lg text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan hover:bg-brand-violet/10 dark:hover:bg-brand-cyan/10 transition-colors"
                  title="Rename"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                  </svg>
                </button>
                <button
                  @click="openDelete(cat)"
                  class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"
                  title="Delete"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- ── Create / Rename Modal ── -->
    <transition name="modal-fade">
      <div v-if="modal.open" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeModal" />
        <div class="relative w-full max-w-sm bg-white dark:bg-dark-800 rounded-2xl shadow-2xl p-6">
          <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">
            {{ modal.mode === 'create' ? 'New Category' : `Rename "${modal.originalName}"` }}
          </h2>

          <div class="space-y-4">
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Category Name</label>
              <input
                v-model="modal.name"
                type="text"
                class="input-field"
                placeholder="e.g. Big Data, Machine Learning"
                @keydown.enter.prevent="submitModal"
                ref="modalInput"
              />
            </div>
            <p v-if="modal.error" class="text-xs text-red-500">{{ modal.error }}</p>
          </div>

          <div class="flex items-center gap-3 mt-6">
            <button @click="submitModal" :disabled="modal.saving" class="btn-primary flex-1 text-sm flex items-center justify-center gap-2">
              <svg v-if="modal.saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              {{ modal.mode === 'create' ? 'Create' : 'Rename' }}
            </button>
            <button @click="closeModal" class="btn-ghost flex-1 text-sm">Cancel</button>
          </div>
        </div>
      </div>
    </transition>

    <!-- ── Delete Modal ── -->
    <transition name="modal-fade">
      <div v-if="deleteModal.open" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="deleteModal.open = false" />
        <div class="relative w-full max-w-sm bg-white dark:bg-dark-800 rounded-2xl shadow-2xl p-6">
          <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Delete Category</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
            Delete <span class="font-semibold text-gray-700 dark:text-gray-200">"{{ deleteModal.name }}"</span>?
            <span v-if="deleteModal.docCount > 0">
              {{ deleteModal.docCount }} doc{{ deleteModal.docCount !== 1 ? 's' : '' }} will be moved to another category.
            </span>
          </p>

          <!-- Move-to selector (only shown when there are docs in this category) -->
          <div v-if="deleteModal.docCount > 0" class="mb-5">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Move docs to</label>
            <select v-model="deleteModal.moveTo" class="input-field">
              <option value="General">General</option>
              <option
                v-for="cat in categories.filter(c => c.name !== deleteModal.name)"
                :key="cat.name"
                :value="cat.name"
              >{{ cat.name }}</option>
            </select>
          </div>

          <p v-if="deleteModal.error" class="text-xs text-red-500 mb-3">{{ deleteModal.error }}</p>

          <div class="flex items-center gap-3">
            <button @click="confirmDelete" :disabled="deleteModal.saving"
              class="flex-1 text-sm font-semibold px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700 disabled:opacity-50 transition-colors flex items-center justify-center gap-2">
              <svg v-if="deleteModal.saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              Delete
            </button>
            <button @click="deleteModal.open = false" class="btn-ghost flex-1 text-sm">Cancel</button>
          </div>
        </div>
      </div>
    </transition>

    <!-- Toast -->
    <transition name="toast-fade">
      <div v-if="toast.show"
        :class="['fixed top-5 right-5 z-[60] flex items-center gap-2.5 px-4 py-3 rounded-xl shadow-2xl text-sm font-medium pointer-events-none',
          toast.type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white']"
      >
        <svg v-if="toast.type === 'success'" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <svg v-else class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        {{ toast.message }}
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, reactive, nextTick, onMounted } from 'vue'
import { adminApi } from '@/services/api'

const loading = ref(true)
const categories = ref([])
const modalInput = ref(null)

const modal = reactive({
  open: false,
  mode: 'create',   // 'create' | 'edit'
  originalName: '',
  name: '',
  saving: false,
  error: '',
})

const deleteModal = reactive({
  open: false,
  name: '',
  docCount: 0,
  moveTo: 'General',
  saving: false,
  error: '',
})

const toast = ref({ show: false, type: 'success', message: '' })
let toastTimer = null

// ── Fetch ─────────────────────────────────────────────────────────
async function fetchCategories() {
  loading.value = true
  try {
    const { data } = await adminApi.getDocCategories()
    categories.value = data.data || []
  } catch {
    showToast('error', 'Failed to load categories.')
  } finally {
    loading.value = false
  }
}

// ── Create modal ──────────────────────────────────────────────────
function openCreate() {
  modal.mode = 'create'
  modal.originalName = ''
  modal.name = ''
  modal.error = ''
  modal.saving = false
  modal.open = true
  nextTick(() => modalInput.value?.focus())
}

// ── Edit / rename modal ───────────────────────────────────────────
function openEdit(cat) {
  modal.mode = 'edit'
  modal.originalName = cat.name
  modal.name = cat.name
  modal.error = ''
  modal.saving = false
  modal.open = true
  nextTick(() => modalInput.value?.focus())
}

function closeModal() {
  modal.open = false
}

async function submitModal() {
  modal.error = ''
  if (!modal.name.trim()) { modal.error = 'Name is required.'; return }

  modal.saving = true
  try {
    if (modal.mode === 'create') {
      await adminApi.createDocCategory({ name: modal.name.trim() })
      showToast('success', `Category "${modal.name.trim()}" created.`)
    } else {
      if (modal.name.trim() === modal.originalName) { closeModal(); return }
      await adminApi.renameDocCategory({ old_name: modal.originalName, new_name: modal.name.trim() })
      showToast('success', `Renamed to "${modal.name.trim()}".`)
    }
    await fetchCategories()
    closeModal()
  } catch (err) {
    modal.error = err.response?.data?.message
      || (err.response?.data?.errors?.name?.[0])
      || 'Failed to save.'
  } finally {
    modal.saving = false
  }
}

// ── Delete modal ──────────────────────────────────────────────────
function openDelete(cat) {
  deleteModal.name = cat.name
  deleteModal.docCount = cat.doc_count
  deleteModal.moveTo = categories.value.find(c => c.name !== cat.name)?.name ?? 'General'
  deleteModal.error = ''
  deleteModal.saving = false
  deleteModal.open = true
}

async function confirmDelete() {
  deleteModal.error = ''
  deleteModal.saving = true
  try {
    await adminApi.deleteDocCategory({
      name: deleteModal.name,
      move_to: deleteModal.docCount > 0 ? deleteModal.moveTo : undefined,
    })
    showToast('success', `Category "${deleteModal.name}" deleted.`)
    await fetchCategories()
    deleteModal.open = false
  } catch (err) {
    deleteModal.error = err.response?.data?.message || 'Failed to delete.'
  } finally {
    deleteModal.saving = false
  }
}

// ── Toast ─────────────────────────────────────────────────────────
function showToast(type, message) {
  clearTimeout(toastTimer)
  toast.value = { show: true, type, message }
  toastTimer = setTimeout(() => { toast.value.show = false }, 3500)
}

onMounted(fetchCategories)
</script>

<style scoped>
.modal-fade-enter-active, .modal-fade-leave-active { transition: opacity .2s; }
.modal-fade-enter-from, .modal-fade-leave-to { opacity: 0; }
.toast-fade-enter-active, .toast-fade-leave-active { transition: opacity .25s, transform .25s; }
.toast-fade-enter-from, .toast-fade-leave-to { opacity: 0; transform: translateY(-6px); }
</style>
