<template>
  <div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-code font-bold dark:text-white">Roles & Permissions</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage user roles and access levels</p>
      </div>
      <button @click="openModal()" class="btn-primary flex items-center gap-2 text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Add Role
      </button>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-brand-violet/10 dark:bg-brand-violet/20 flex items-center justify-center">
          <svg class="w-5 h-5 text-brand-violet" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Total Roles</p>
          <p class="text-lg font-bold dark:text-white">{{ roles.length }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Total Users</p>
          <p class="text-lg font-bold text-blue-500">{{ totalUsers }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">System Roles</p>
          <p class="text-lg font-bold text-amber-500">{{ systemRoles }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Custom Roles</p>
          <p class="text-lg font-bold text-green-500">{{ customRoles }}</p>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="w-10 h-10 border-4 border-brand-violet/30 border-t-brand-violet rounded-full animate-spin" />
    </div>

    <!-- Roles Grid -->
    <div v-else-if="roles.length" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
      <div v-for="role in roles" :key="role.id"
        class="glass-card group relative overflow-hidden transition-all hover:shadow-lg hover:scale-[1.01]">
        <!-- Role Color Stripe -->
        <div class="absolute top-0 left-0 w-full h-1" :class="roleColor(role.name).stripe" />

        <!-- Header -->
        <div class="flex items-start justify-between mb-4 pt-2">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center" :class="roleColor(role.name).bg">
              <svg v-if="role.name === 'admin'" class="w-5 h-5" :class="roleColor(role.name).icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
              <svg v-else-if="role.name === 'member'" class="w-5 h-5" :class="roleColor(role.name).icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              <svg v-else-if="role.name === 'guest'" class="w-5 h-5" :class="roleColor(role.name).icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              <svg v-else class="w-5 h-5" :class="roleColor(role.name).icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
            <div>
              <h2 class="font-bold text-gray-900 dark:text-white">{{ role.display_name || role.name }}</h2>
              <span class="text-[10px] font-mono text-gray-400 bg-gray-100 dark:bg-dark-600 px-1.5 py-0.5 rounded">{{ role.name }}</span>
            </div>
          </div>
          <!-- Actions -->
          <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <button @click="openModal(role)" class="p-1.5 rounded-lg text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan hover:bg-brand-violet/10 dark:hover:bg-brand-cyan/10 transition-colors" title="Edit">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </button>
            <button v-if="!isSystemRole(role)" @click="deleteTarget = role" class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-500/10 transition-colors" title="Delete">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </div>
        </div>

        <!-- Description -->
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-5">{{ role.description || 'No description provided' }}</p>

        <!-- Footer -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-dark-600">
          <div class="flex items-center gap-2">
            <div class="flex -space-x-2">
              <div v-for="i in Math.min(role.users_count || 0, 4)" :key="i" class="w-7 h-7 rounded-full border-2 border-white dark:border-dark-800 flex items-center justify-center text-[10px] font-bold" :class="roleColor(role.name).bg + ' ' + roleColor(role.name).icon">
                {{ i }}
              </div>
              <div v-if="(role.users_count || 0) > 4" class="w-7 h-7 rounded-full border-2 border-white dark:border-dark-800 bg-gray-100 dark:bg-dark-600 flex items-center justify-center text-[10px] font-bold text-gray-500">
                +{{ role.users_count - 4 }}
              </div>
            </div>
            <span class="text-xs text-gray-500 dark:text-gray-400">
              {{ role.users_count || 0 }} {{ (role.users_count || 0) === 1 ? 'user' : 'users' }}
            </span>
          </div>
          <span v-if="isSystemRole(role)" class="text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full bg-gray-100 dark:bg-dark-600 text-gray-500 dark:text-gray-400">System</span>
          <span v-else class="text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400">Custom</span>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="glass-card flex flex-col items-center justify-center py-16">
      <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-dark-700 flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
      </div>
      <h3 class="font-semibold text-gray-900 dark:text-white mb-1">No roles found</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Create your first role to manage user access.</p>
      <button @click="openModal()" class="btn-primary text-sm">Create Role</button>
    </div>

    <!-- Create/Edit Modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition-opacity duration-200" leave-active-class="transition-opacity duration-200" enter-from-class="opacity-0" leave-to-class="opacity-0">
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="showModal = false">
          <Transition enter-active-class="transition-all duration-200" leave-active-class="transition-all duration-200" enter-from-class="opacity-0 scale-95" leave-to-class="opacity-0 scale-95">
            <div v-if="showModal" class="bg-white dark:bg-dark-800 rounded-2xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-dark-600">
              <!-- Modal Header -->
              <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-dark-600">
                <h3 class="font-bold text-gray-900 dark:text-white">{{ editingRole ? 'Edit Role' : 'Create Role' }}</h3>
                <button @click="showModal = false" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:text-gray-300 dark:hover:bg-dark-700 transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
              </div>
              <!-- Modal Body -->
              <form @submit.prevent="saveRole" class="px-6 py-5 space-y-4">
                <div>
                  <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Display Name <span class="text-red-500">*</span></label>
                  <input v-model="form.display_name" type="text" class="input-field" placeholder="e.g. Content Editor" required />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                    Slug <span class="text-red-500">*</span>
                    <span class="text-xs font-normal text-gray-400 ml-1">— unique identifier</span>
                  </label>
                  <input v-model="form.name" type="text" class="input-field font-mono text-sm" placeholder="e.g. content-editor" required :disabled="editingRole && isSystemRole(editingRole)" />
                  <p v-if="editingRole && isSystemRole(editingRole)" class="text-[10px] text-amber-500 mt-1">System role slug cannot be changed</p>
                </div>
                <div>
                  <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
                  <textarea v-model="form.description" rows="3" class="input-field resize-y" placeholder="Brief description of this role's permissions..." />
                </div>
                <div v-if="formError" class="p-3 rounded-lg bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800/30">
                  <p class="text-sm text-red-600 dark:text-red-400">{{ formError }}</p>
                </div>
              </form>
              <!-- Modal Footer -->
              <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-dark-600">
                <button @click="showModal = false" class="px-4 py-2 text-sm font-medium rounded-lg bg-gray-100 dark:bg-dark-600 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-500 transition-colors">Cancel</button>
                <button @click="saveRole" :disabled="saving" class="btn-primary text-sm flex items-center gap-2">
                  <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                  {{ saving ? 'Saving...' : editingRole ? 'Update Role' : 'Create Role' }}
                </button>
              </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>

    <!-- Delete Confirm Modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition-opacity duration-200" leave-active-class="transition-opacity duration-200" enter-from-class="opacity-0" leave-to-class="opacity-0">
        <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="deleteTarget = null">
          <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-2xl max-w-sm w-full p-6 border border-gray-200 dark:border-dark-600">
            <div class="flex items-center gap-3 mb-4">
              <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              </div>
              <div>
                <h3 class="font-bold text-gray-900 dark:text-white">Delete Role</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">This action cannot be undone.</p>
              </div>
            </div>
            <div class="flex items-center gap-3 mb-5 p-3 rounded-lg bg-gray-50 dark:bg-dark-700">
              <div class="w-10 h-10 rounded-xl flex items-center justify-center" :class="roleColor(deleteTarget.name).bg">
                <svg class="w-5 h-5" :class="roleColor(deleteTarget.name).icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
              </div>
              <div>
                <p class="text-sm font-semibold dark:text-white">{{ deleteTarget.display_name || deleteTarget.name }}</p>
                <p class="text-xs text-gray-400">{{ deleteTarget.users_count || 0 }} users assigned</p>
              </div>
            </div>
            <p v-if="deleteTarget.users_count > 0" class="text-sm text-amber-600 dark:text-amber-400 mb-4 p-3 rounded-lg bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/30">
              This role has {{ deleteTarget.users_count }} assigned users. Reassign them first before deleting.
            </p>
            <div v-if="deleteError" class="text-sm text-red-600 dark:text-red-400 mb-4 p-3 rounded-lg bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800/30">{{ deleteError }}</div>
            <div class="flex items-center gap-3 justify-end">
              <button @click="deleteTarget = null; deleteError = ''" class="px-4 py-2 text-sm font-medium rounded-lg bg-gray-100 dark:bg-dark-600 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-500 transition-colors">Cancel</button>
              <button @click="confirmDelete" :disabled="deleting" class="px-4 py-2 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors disabled:opacity-50 flex items-center gap-2">
                <svg v-if="deleting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                {{ deleting ? 'Deleting...' : 'Delete' }}
              </button>
            </div>
          </div>
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
const roles = ref([])
const loading = ref(true)
const showModal = ref(false)
const editingRole = ref(null)
const deleteTarget = ref(null)
const saving = ref(false)
const deleting = ref(false)
const formError = ref('')
const deleteError = ref('')
const form = ref({ name: '', display_name: '', description: '' })

const totalUsers = computed(() => roles.value.reduce((sum, r) => sum + (r.users_count || 0), 0))
const systemRoles = computed(() => roles.value.filter(r => isSystemRole(r)).length)
const customRoles = computed(() => roles.value.filter(r => !isSystemRole(r)).length)

function isSystemRole(role) {
  return ['admin', 'member', 'guest'].includes(role.name)
}

function roleColor(name) {
  const map = {
    admin: { bg: 'bg-red-500/10', icon: 'text-red-500', stripe: 'bg-gradient-to-r from-red-500 to-red-400' },
    member: { bg: 'bg-blue-500/10', icon: 'text-blue-500', stripe: 'bg-gradient-to-r from-blue-500 to-blue-400' },
    guest: { bg: 'bg-gray-200 dark:bg-gray-700/50', icon: 'text-gray-500', stripe: 'bg-gradient-to-r from-gray-400 to-gray-300' },
  }
  return map[name] || { bg: 'bg-brand-violet/10 dark:bg-brand-cyan/10', icon: 'text-brand-violet dark:text-brand-cyan', stripe: 'bg-gradient-to-r from-brand-violet to-brand-cyan' }
}

function openModal(role = null) {
  editingRole.value = role
  formError.value = ''
  if (role) {
    form.value = { name: role.name, display_name: role.display_name || '', description: role.description || '' }
  } else {
    form.value = { name: '', display_name: '', description: '' }
  }
  showModal.value = true
}

async function saveRole() {
  if (!form.value.display_name || !form.value.name) {
    formError.value = 'Display name and slug are required.'
    return
  }
  saving.value = true
  formError.value = ''
  try {
    if (editingRole.value) {
      const { data } = await adminApi.updateRole(editingRole.value.id, form.value)
      const idx = roles.value.findIndex(r => r.id === editingRole.value.id)
      if (idx !== -1) roles.value[idx] = data.data
      uiStore.showToast('Role updated successfully')
    } else {
      const { data } = await adminApi.createRole(form.value)
      roles.value.push(data.data)
      uiStore.showToast('Role created successfully')
    }
    showModal.value = false
  } catch (err) {
    formError.value = err.response?.data?.message || Object.values(err.response?.data?.errors || {}).flat()[0] || 'Failed to save role'
  } finally {
    saving.value = false
  }
}

async function confirmDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  deleteError.value = ''
  try {
    await adminApi.deleteRole(deleteTarget.value.id)
    roles.value = roles.value.filter(r => r.id !== deleteTarget.value.id)
    uiStore.showToast('Role deleted')
    deleteTarget.value = null
  } catch (err) {
    deleteError.value = err.response?.data?.message || 'Failed to delete role'
  } finally {
    deleting.value = false
  }
}

onMounted(async () => {
  try {
    const { data } = await adminApi.getRoles()
    roles.value = data.data || data
  } catch { roles.value = [] } finally { loading.value = false }
})
</script>
