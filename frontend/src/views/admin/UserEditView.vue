<template>
  <div>
    <!-- Back & Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
      <router-link to="/admin/users" class="hover:text-brand-violet dark:hover:text-brand-cyan transition-colors flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Users
      </router-link>
      <span>/</span>
      <span class="text-gray-700 dark:text-gray-200">Edit User</span>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="w-10 h-10 border-4 border-brand-violet/30 border-t-brand-violet rounded-full animate-spin" />
    </div>

    <div v-else-if="form" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- User Profile Card -->
      <div class="lg:col-span-1">
        <div class="glass-card !p-6 text-center">
          <div class="relative inline-block mb-4">
            <img
              v-if="user.avatar"
              :src="user.avatar"
              :alt="user.name"
              class="w-24 h-24 rounded-full object-cover ring-4 ring-brand-violet/20 dark:ring-brand-cyan/20 mx-auto"
            />
            <div v-else class="w-24 h-24 rounded-full bg-gradient-to-br from-brand-violet to-brand-cyan flex items-center justify-center text-white text-3xl font-bold mx-auto">
              {{ user.name?.charAt(0)?.toUpperCase() }}
            </div>
            <span
              class="absolute bottom-1 right-1 w-5 h-5 rounded-full border-3 border-white dark:border-dark-800"
              :class="user.is_active ? 'bg-green-400' : 'bg-gray-300'"
            />
          </div>
          <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ user.name }}</h2>
          <p v-if="user.username" class="text-sm text-brand-violet dark:text-brand-cyan font-medium">@{{ user.username }}</p>
          <p class="text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</p>
          <span :class="roleBadgeClass(user.role?.name)" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium mt-3">
            <span class="w-1.5 h-1.5 rounded-full" :class="roleDotClass(user.role?.name)" />
            {{ user.role?.display_name || user.role?.name || 'Member' }}
          </span>

          <div class="mt-5 pt-5 border-t border-gray-100 dark:border-dark-700 space-y-3 text-left">
            <div class="flex items-center gap-3 text-sm">
              <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <div>
                <p class="text-gray-400 dark:text-gray-500 text-xs">Joined</p>
                <p class="text-gray-700 dark:text-gray-300">{{ formatDate(user.created_at) }}</p>
              </div>
            </div>
            <div class="flex items-center gap-3 text-sm">
              <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <div>
                <p class="text-gray-400 dark:text-gray-500 text-xs">Status</p>
                <p :class="user.is_active ? 'text-green-500' : 'text-red-500'" class="font-medium">{{ user.is_active ? 'Active' : 'Inactive' }}</p>
              </div>
            </div>
            <div v-if="user.bio" class="flex items-start gap-3 text-sm">
              <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
              <div>
                <p class="text-gray-400 dark:text-gray-500 text-xs">Bio</p>
                <p class="text-gray-700 dark:text-gray-300">{{ user.bio }}</p>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="mt-5 pt-5 border-t border-gray-100 dark:border-dark-700 space-y-2">
            <button
              @click="toggleActive"
              class="w-full py-2 px-4 rounded-lg text-sm font-medium transition-colors"
              :class="user.is_active
                ? 'bg-orange-50 text-orange-600 hover:bg-orange-100 dark:bg-orange-500/10 dark:text-orange-400 dark:hover:bg-orange-500/20'
                : 'bg-green-50 text-green-600 hover:bg-green-100 dark:bg-green-500/10 dark:text-green-400 dark:hover:bg-green-500/20'"
            >
              {{ user.is_active ? 'Deactivate User' : 'Activate User' }}
            </button>
            <button @click="confirmDelete" class="w-full py-2 px-4 rounded-lg text-sm font-medium bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20 transition-colors">
              Delete User
            </button>
          </div>
        </div>
      </div>

      <!-- Edit Form -->
      <div class="lg:col-span-2 space-y-6">
        <form @submit.prevent="handleSubmit" class="glass-card !p-6">          <h3 class="text-lg font-bold dark:text-white mb-5 flex items-center gap-2">
            <svg class="w-5 h-5 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            User Information
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Full Name</label>
              <input v-model="form.name" type="text" required class="input-field w-full" placeholder="Enter full name" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Username</label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">@</span>
                <input v-model="form.username" type="text" required minlength="3" maxlength="30" pattern="[a-zA-Z0-9_]+" class="input-field w-full pl-8" placeholder="unique_handle" />
              </div>
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
              <input v-model="form.email" type="email" required class="input-field w-full" placeholder="Enter email" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Role</label>
              <CustomSelect
                v-model="form.role_id"
                :options="[{ label: 'Select role', value: '' }, ...roles.map(r => ({ label: r.display_name || r.name, value: r.id }))]"
                placeholder="Select role"
              />
            </div>
          </div>
          <div class="flex items-center justify-end gap-3 mt-6 pt-5 border-t border-gray-100 dark:border-dark-700">
            <router-link to="/admin/users" class="btn-ghost text-sm">Cancel</router-link>
            <button type="submit" :disabled="saving" class="btn-primary text-sm flex items-center gap-2">
              <svg v-if="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              <div v-else class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
              {{ saving ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
          <p v-if="error" class="text-sm text-red-500 mt-3">{{ error }}</p>
        </form>

        <!-- Permissions Panel (superadmin only, for admin users) -->
        <div v-if="authStore.isSuperAdmin && isAdminUser" class="glass-card !p-6">
          <h3 class="text-lg font-bold dark:text-white mb-1 flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            Resource Permissions
          </h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
            Control what <strong>{{ user.name }}</strong> can do directly. Unpermitted actions will be queued for your approval.
          </p>

          <div v-if="permissionsLoading" class="flex justify-center py-8">
            <div class="w-8 h-8 border-4 border-brand-violet/30 border-t-brand-violet rounded-full animate-spin" />
          </div>

          <div v-else>
            <!-- Permission Table -->
            <div class="overflow-x-auto">
              <table class="w-full text-sm">
                <thead>
                  <tr class="border-b border-gray-200 dark:border-dark-600">
                    <th class="text-left pb-3 pr-4 font-semibold text-gray-600 dark:text-gray-400">Resource</th>
                    <th class="pb-3 px-4 font-semibold text-gray-600 dark:text-gray-400 text-center">Create</th>
                    <th class="pb-3 px-4 font-semibold text-gray-600 dark:text-gray-400 text-center">Edit</th>
                    <th class="pb-3 px-4 font-semibold text-gray-600 dark:text-gray-400 text-center">Delete</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-dark-700">
                  <tr v-for="res in permissionResources" :key="res.key">
                    <td class="py-3 pr-4">
                      <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" :class="res.bg">
                          <component :is="res.icon" class="w-4 h-4" :class="res.iconColor" />
                        </div>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ res.label }}</span>
                      </div>
                    </td>
                    <td class="py-3 px-4 text-center">
                      <button type="button" @click="togglePerm(res.key, 'can_create')" class="inline-flex items-center justify-center w-9 h-5 rounded-full transition-colors duration-200 focus:outline-none" :class="permissions[res.key]?.can_create ? 'bg-brand-violet dark:bg-brand-cyan' : 'bg-gray-300 dark:bg-dark-500'">
                        <span class="w-4 h-4 rounded-full bg-white shadow transform transition-transform duration-200" :class="permissions[res.key]?.can_create ? 'translate-x-2' : '-translate-x-2'" />
                      </button>
                    </td>
                    <td class="py-3 px-4 text-center">
                      <button type="button" @click="togglePerm(res.key, 'can_update')" class="inline-flex items-center justify-center w-9 h-5 rounded-full transition-colors duration-200 focus:outline-none" :class="permissions[res.key]?.can_update ? 'bg-brand-violet dark:bg-brand-cyan' : 'bg-gray-300 dark:bg-dark-500'">
                        <span class="w-4 h-4 rounded-full bg-white shadow transform transition-transform duration-200" :class="permissions[res.key]?.can_update ? 'translate-x-2' : '-translate-x-2'" />
                      </button>
                    </td>
                    <td class="py-3 px-4 text-center">
                      <button type="button" @click="togglePerm(res.key, 'can_delete')" class="inline-flex items-center justify-center w-9 h-5 rounded-full transition-colors duration-200 focus:outline-none" :class="permissions[res.key]?.can_delete ? 'bg-red-500' : 'bg-gray-300 dark:bg-dark-500'">
                        <span class="w-4 h-4 rounded-full bg-white shadow transform transition-transform duration-200" :class="permissions[res.key]?.can_delete ? 'translate-x-2' : '-translate-x-2'" />
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="flex items-center justify-end gap-3 mt-5 pt-4 border-t border-gray-100 dark:border-dark-700">
              <button type="button" @click="savePermissions" :disabled="permissionsSaving" class="btn-primary text-sm flex items-center gap-2">
                <svg v-if="!permissionsSaving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <div v-else class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                {{ permissionsSaving ? 'Saving...' : 'Save Permissions' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <teleport to="body">
      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showDeleteModal = false" />
          <div class="relative bg-white dark:bg-dark-800 rounded-2xl shadow-2xl w-full max-w-sm p-6 z-10 text-center">
            <div class="w-12 h-12 rounded-full bg-red-50 dark:bg-red-500/10 flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Delete User</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
              Are you sure you want to delete <strong>{{ user.name }}</strong>? This action cannot be undone.
            </p>
            <div class="flex justify-center gap-3">
              <button @click="showDeleteModal = false" class="btn-ghost text-sm">Cancel</button>
              <button @click="handleDelete" :disabled="deletingUser" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors">
                {{ deletingUser ? 'Deleting...' : 'Delete' }}
              </button>
            </div>
          </div>
        </div>
      </transition>
    </teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { adminApi } from '@/services/api'
import { useUiStore } from '@/stores/ui'
import { useAuthStore } from '@/stores/auth'
import { FolderIcon, RectangleStackIcon, TagIcon, UserGroupIcon, DocumentTextIcon, PhotoIcon } from '@heroicons/vue/24/outline'
import CustomSelect from '@/components/common/CustomSelect.vue'

const route = useRoute()
const router = useRouter()
const uiStore = useUiStore()
const authStore = useAuthStore()

const user = ref({})
const form = ref(null)
const roles = ref([])
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const showDeleteModal = ref(false)
const deletingUser = ref(false)

// Permissions state
const permissions = ref({
  projects:   { can_create: false, can_update: false, can_delete: false },
  categories: { can_create: false, can_update: false, can_delete: false },
  tags:       { can_create: false, can_update: false, can_delete: false },
})
const permissionsLoading = ref(false)
const permissionsSaving = ref(false)

const isAdminUser = computed(() => {
  const roleName = user.value.role?.name?.toLowerCase()
  return roleName === 'admin'
})

const permissionResources = [
  { key: 'projects',     label: 'Projects',       icon: FolderIcon,          bg: 'bg-brand-violet/10', iconColor: 'text-brand-violet dark:text-brand-cyan' },
  { key: 'categories',   label: 'Categories',     icon: RectangleStackIcon,  bg: 'bg-blue-500/10',     iconColor: 'text-blue-500' },
  { key: 'tags',         label: 'Tags',           icon: TagIcon,             bg: 'bg-green-500/10',    iconColor: 'text-green-500' },
  { key: 'team_members', label: 'Team Members',   icon: UserGroupIcon,       bg: 'bg-amber-500/10',    iconColor: 'text-amber-500' },
  { key: 'blog_posts',   label: 'Blog Posts',     icon: DocumentTextIcon,    bg: 'bg-pink-500/10',     iconColor: 'text-pink-500' },
  { key: 'media',        label: 'Media Library',  icon: PhotoIcon,           bg: 'bg-cyan-500/10',     iconColor: 'text-cyan-500' },
]

function togglePerm(resource, field) {
  permissions.value[resource][field] = !permissions.value[resource][field]
}

async function loadPermissions(userId) {
  permissionsLoading.value = true
  try {
    const { data } = await adminApi.getUserPermissions(userId)
    permissions.value = data.data
  } catch {} finally {
    permissionsLoading.value = false
  }
}

async function savePermissions() {
  permissionsSaving.value = true
  try {
    const payload = Object.entries(permissions.value).map(([resource, perms]) => ({
      resource,
      ...perms,
    }))
    await adminApi.updateUserPermissions(route.params.id, { permissions: payload })
    uiStore.showToast('Permissions saved successfully!')
  } catch {
    uiStore.showToast('Failed to save permissions', 'error')
  } finally {
    permissionsSaving.value = false
  }
}

function roleBadgeClass(role) {
  const map = {
    admin: 'bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400',
    member: 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
    guest: 'bg-gray-100 text-gray-600 dark:bg-gray-500/10 dark:text-gray-400',
  }
  return map[role] || map.member
}

function roleDotClass(role) {
  const map = { admin: 'bg-purple-500', member: 'bg-blue-500', guest: 'bg-gray-400' }
  return map[role] || map.member
}

function formatDate(date) {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })
}

onMounted(async () => {
  try {
    const [userRes, rolesRes] = await Promise.all([
      adminApi.getUser(route.params.id),
      adminApi.getRoles(),
    ])
    const u = userRes.data.data
    user.value = u
    form.value = { name: u.name, username: u.username || '', email: u.email, role_id: u.role?.id || '' }
    roles.value = rolesRes.data.data || []

    // Load per-user permissions if this is an admin user and current user is superadmin
    if (authStore.isSuperAdmin && u.role?.name?.toLowerCase() === 'admin') {
      await loadPermissions(u.id)
    }
  } catch {
    form.value = null
  } finally {
    loading.value = false
  }
})

async function handleSubmit() {
  saving.value = true
  error.value = ''
  try {
    await adminApi.updateUser(route.params.id, form.value)
    uiStore.showToast('User updated successfully!')
    router.push({ name: 'admin-users' })
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to update'
  } finally {
    saving.value = false
  }
}

async function toggleActive() {
  try {
    await adminApi.toggleUserActive(user.value.id)
    user.value.is_active = !user.value.is_active
    uiStore.showToast(`User ${user.value.is_active ? 'activated' : 'deactivated'}`)
  } catch {
    uiStore.showToast('Failed to update user', 'error')
  }
}

function confirmDelete() {
  showDeleteModal.value = true
}

async function handleDelete() {
  deletingUser.value = true
  try {
    await adminApi.deleteUser(user.value.id)
    uiStore.showToast('User deleted successfully')
    router.push({ name: 'admin-users' })
  } catch {
    uiStore.showToast('Failed to delete user', 'error')
  } finally {
    deletingUser.value = false
    showDeleteModal.value = false
  }
}
</script>
