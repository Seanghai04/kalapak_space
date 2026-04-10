<template>
  <div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-sans font-bold dark:text-white">Users</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
          Manage all registered users and their permissions
        </p>
      </div>
      <button @click="showCreateModal = true" class="btn-primary flex items-center gap-2 text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add User
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-brand-violet/10 dark:bg-brand-violet/20 flex items-center justify-center">
          <svg class="w-5 h-5 text-brand-violet" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Total Users</p>
          <p class="text-lg font-bold dark:text-white">{{ meta.total || 0 }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Active</p>
          <p class="text-lg font-bold text-green-500">{{ activeCount }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Inactive</p>
          <p class="text-lg font-bold text-red-500">{{ inactiveCount }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-brand-cyan/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">New This Month</p>
          <p class="text-lg font-bold text-brand-cyan">{{ newThisMonth }}</p>
        </div>
      </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="glass-card !p-4 mb-6">
      <div class="flex flex-col md:flex-row items-start md:items-center gap-3">
        <!-- Search -->
        <div class="relative flex-1 w-full">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input
            v-model="search"
            type="text"
            placeholder="Search by name or email..."
            class="input-field !pl-10 w-full"
            @input="debouncedFetch"
          />
        </div>
        <!-- Role Filter -->
        <CustomSelect
          v-model="roleFilter"
          :options="[{ label: 'All Roles', value: '' }, { label: 'Admin', value: 'admin' }, { label: 'Member', value: 'member' }, { label: 'Guest', value: 'guest' }]"
          placeholder="All Roles"
          size="md"
          class="w-full md:w-40"
          @change="fetchUsers()"
        />
        <!-- Status Filter -->
        <CustomSelect
          v-model="statusFilter"
          :options="[{ label: 'All Status', value: '' }, { label: 'Active', value: 'active' }, { label: 'Inactive', value: 'inactive' }]"
          placeholder="All Status"
          size="md"
          class="w-full md:w-40"
          @change="fetchUsers()"
        />
        <!-- View Toggle -->
        <div class="flex items-center border border-gray-200 dark:border-dark-600 rounded-lg overflow-hidden">
          <button
            @click="viewMode = 'table'"
            class="p-2 transition-colors"
            :class="viewMode === 'table' ? 'bg-brand-violet/10 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
          </button>
          <button
            @click="viewMode = 'grid'"
            class="p-2 transition-colors"
            :class="viewMode === 'grid' ? 'bg-brand-violet/10 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="w-10 h-10 border-4 border-brand-violet/30 border-t-brand-violet rounded-full animate-spin" />
    </div>

    <!-- Table View -->
    <div v-else-if="viewMode === 'table' && users.length" class="glass-card overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-gray-200 dark:border-dark-600">
            <th class="text-left py-3 px-4">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" :checked="allSelected" @change="toggleSelectAll" class="w-4 h-4 rounded border-gray-300 text-brand-violet focus:ring-brand-violet dark:border-dark-500" />
              </label>
            </th>
            <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">User</th>
            <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider hidden lg:table-cell">Role</th>
            <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider hidden md:table-cell">Status</th>
            <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider hidden xl:table-cell">Joined</th>
            <th class="text-right py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="user in users"
            :key="user.id"
            class="border-b border-gray-100 dark:border-dark-700 hover:bg-gray-50/50 dark:hover:bg-dark-700/30 transition-colors"
          >
            <td class="py-3 px-4">
              <input
                type="checkbox"
                :checked="selectedIds.includes(user.id)"
                @change="toggleSelect(user.id)"
                class="w-4 h-4 rounded border-gray-300 text-brand-violet focus:ring-brand-violet dark:border-dark-500"
              />
            </td>
            <td class="py-3 px-4">
              <div class="flex items-center gap-3">
                <div class="relative flex-shrink-0">
                  <img
                    v-if="user.avatar"
                    :src="user.avatar"
                    :alt="user.name"
                    class="w-10 h-10 rounded-full object-cover ring-2 ring-brand-violet/20 dark:ring-brand-cyan/20"
                  />
                  <div v-else class="w-10 h-10 rounded-full bg-gradient-to-br from-brand-violet to-brand-cyan flex items-center justify-center text-white text-sm font-bold">
                    {{ user.name?.charAt(0)?.toUpperCase() }}
                  </div>
                  <span
                    class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white dark:border-dark-800"
                    :class="user.is_active ? 'bg-green-400' : 'bg-gray-300 dark:bg-gray-600'"
                  />
                </div>
                <div class="min-w-0">
                  <p class="font-semibold text-gray-900 dark:text-white truncate">{{ user.name }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ user.email }}</p>
                </div>
              </div>
            </td>
            <td class="py-3 px-4 hidden lg:table-cell">
              <span :class="roleBadgeClass(user.role?.name)" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium">
                <span class="w-1.5 h-1.5 rounded-full" :class="roleDotClass(user.role?.name)" />
                {{ user.role?.display_name || user.role?.name || 'Member' }}
              </span>
            </td>
            <td class="py-3 px-4 hidden md:table-cell">
              <span
                :class="user.is_active
                  ? 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400'
                  : 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400'"
                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium"
              >
                <span class="w-1.5 h-1.5 rounded-full" :class="user.is_active ? 'bg-green-500' : 'bg-red-500'" />
                {{ user.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="py-3 px-4 hidden xl:table-cell text-xs text-gray-500 dark:text-gray-400">
              {{ formatDate(user.created_at) }}
            </td>
            <td class="py-3 px-4 text-right">
              <div class="flex items-center justify-end gap-1">
                <button
                  @click="toggleActive(user)"
                  class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors group"
                  :title="user.is_active ? 'Deactivate' : 'Activate'"
                >
                  <svg v-if="user.is_active" class="w-4 h-4 text-gray-400 group-hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                  <svg v-else class="w-4 h-4 text-gray-400 group-hover:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </button>
                <router-link
                  :to="{ name: 'admin-user-edit', params: { id: user.id } }"
                  class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors group"
                  title="Edit"
                >
                  <svg class="w-4 h-4 text-gray-400 group-hover:text-brand-violet dark:group-hover:text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </router-link>
                <button
                  @click="confirmDelete(user)"
                  class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors group"
                  title="Delete"
                >
                  <svg class="w-4 h-4 text-gray-400 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Bulk Actions -->
      <div v-if="selectedIds.length" class="flex items-center gap-3 px-4 py-3 bg-brand-violet/5 dark:bg-brand-cyan/5 border-t border-gray-200 dark:border-dark-600">
        <span class="text-sm text-gray-600 dark:text-gray-400">{{ selectedIds.length }} selected</span>
        <button @click="bulkDelete" class="text-sm text-red-500 hover:text-red-600 font-medium">Delete Selected</button>
        <button @click="selectedIds = []" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Clear</button>
      </div>
    </div>

    <!-- Grid View -->
    <div v-else-if="viewMode === 'grid' && users.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      <div
        v-for="user in users"
        :key="user.id"
        class="glass-card !p-5 hover:shadow-lg transition-all duration-300 group relative"
      >
        <div class="absolute top-3 right-3">
          <span
            :class="user.is_active
              ? 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400'
              : 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400'"
            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium"
          >
            <span class="w-1.5 h-1.5 rounded-full" :class="user.is_active ? 'bg-green-500' : 'bg-red-500'" />
            {{ user.is_active ? 'Active' : 'Inactive' }}
          </span>
        </div>
        <div class="flex flex-col items-center text-center">
          <div class="relative mb-3">
            <img
              v-if="user.avatar"
              :src="user.avatar"
              :alt="user.name"
              class="w-16 h-16 rounded-full object-cover ring-3 ring-brand-violet/20 dark:ring-brand-cyan/20"
            />
            <div v-else class="w-16 h-16 rounded-full bg-gradient-to-br from-brand-violet to-brand-cyan flex items-center justify-center text-white text-xl font-bold">
              {{ user.name?.charAt(0)?.toUpperCase() }}
            </div>
            <span
              class="absolute bottom-0 right-0 w-4 h-4 rounded-full border-2 border-white dark:border-dark-800"
              :class="user.is_active ? 'bg-green-400' : 'bg-gray-300 dark:bg-gray-600'"
            />
          </div>
          <h3 class="font-semibold text-gray-900 dark:text-white text-sm">{{ user.name }}</h3>
          <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-full mt-0.5">{{ user.email }}</p>
          <span :class="roleBadgeClass(user.role?.name)" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium mt-2">
            {{ user.role?.display_name || user.role?.name || 'Member' }}
          </span>
          <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-2">Joined {{ formatDate(user.created_at) }}</p>
        </div>
        <div class="flex items-center justify-center gap-1 mt-4 pt-3 border-t border-gray-100 dark:border-dark-700">
          <button
            @click="toggleActive(user)"
            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors"
            :title="user.is_active ? 'Deactivate' : 'Activate'"
          >
            <svg v-if="user.is_active" class="w-4 h-4 text-gray-400 hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            <svg v-else class="w-4 h-4 text-gray-400 hover:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </button>
          <router-link
            :to="{ name: 'admin-user-edit', params: { id: user.id } }"
            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors"
            title="Edit"
          >
            <svg class="w-4 h-4 text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
          </router-link>
          <button
            @click="confirmDelete(user)"
            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors"
            title="Delete"
          >
            <svg class="w-4 h-4 text-gray-400 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <EmptyState v-if="!loading && !users.length" title="No users found" message="Try adjusting your search or filters to find what you're looking for." />

    <!-- Pagination -->
    <div v-if="meta.last_page > 1" class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
      <p class="text-sm text-gray-500 dark:text-gray-400">
        Showing <span class="font-medium text-gray-700 dark:text-gray-200">{{ (meta.current_page - 1) * meta.per_page + 1 }}</span>
        to <span class="font-medium text-gray-700 dark:text-gray-200">{{ Math.min(meta.current_page * meta.per_page, meta.total) }}</span>
        of <span class="font-medium text-gray-700 dark:text-gray-200">{{ meta.total }}</span> results
      </p>
      <Pagination :current-page="meta.current_page" :last-page="meta.last_page" @page-change="fetchUsers" />
    </div>

    <!-- Create User Modal -->
    <teleport to="body">
      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showCreateModal = false" />
          <div class="relative bg-white dark:bg-dark-800 rounded-2xl shadow-2xl w-full max-w-md p-6 z-10">
            <div class="flex items-center justify-between mb-5">
              <h2 class="text-lg font-bold dark:text-white">Create New User</h2>
              <button @click="showCreateModal = false" class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-700">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>
            <form @submit.prevent="handleCreate" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                <input v-model="createForm.name" type="text" required class="input-field w-full" placeholder="Enter full name" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                <input v-model="createForm.email" type="email" required class="input-field w-full" placeholder="Enter email address" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                <input v-model="createForm.password" type="password" required minlength="8" class="input-field w-full" placeholder="Minimum 8 characters" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                <CustomSelect
                  v-model="createForm.role_id"
                  :options="[{ label: 'Select role', value: '' }, ...roles.map(r => ({ label: r.display_name || r.name, value: r.id }))]"
                  placeholder="Select role"
                />
              </div>
              <p v-if="createError" class="text-sm text-red-500">{{ createError }}</p>
              <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="showCreateModal = false" class="btn-ghost text-sm">Cancel</button>
                <button type="submit" :disabled="creating" class="btn-primary text-sm">
                  {{ creating ? 'Creating...' : 'Create User' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </transition>
    </teleport>

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
        <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="deleteTarget = null" />
          <div class="relative bg-white dark:bg-dark-800 rounded-2xl shadow-2xl w-full max-w-sm p-6 z-10 text-center">
            <div class="w-12 h-12 rounded-full bg-red-50 dark:bg-red-500/10 flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Delete User</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
              Are you sure you want to delete <strong>{{ deleteTarget.name }}</strong>? This action cannot be undone.
            </p>
            <div class="flex justify-center gap-3">
              <button @click="deleteTarget = null" class="btn-ghost text-sm">Cancel</button>
              <button @click="handleDelete" :disabled="deleting" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors">
                {{ deleting ? 'Deleting...' : 'Delete' }}
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
import { adminApi } from '@/services/api'
import { useUiStore } from '@/stores/ui'
import Pagination from '@/components/common/Pagination.vue'
import EmptyState from '@/components/common/EmptyState.vue'
import CustomSelect from '@/components/common/CustomSelect.vue'

const uiStore = useUiStore()
const users = ref([])
const roles = ref([])
const loading = ref(true)
const search = ref('')
const roleFilter = ref('')
const statusFilter = ref('')
const viewMode = ref('table')
const meta = ref({ current_page: 1, last_page: 1, per_page: 15, total: 0 })
const selectedIds = ref([])

// Create modal
const showCreateModal = ref(false)
const creating = ref(false)
const createError = ref('')
const createForm = ref({ name: '', email: '', password: '', role_id: '' })

// Delete modal
const deleteTarget = ref(null)
const deleting = ref(false)

let debounceTimer = null

const activeCount = computed(() => users.value.filter(u => u.is_active).length)
const inactiveCount = computed(() => users.value.filter(u => !u.is_active).length)
const newThisMonth = computed(() => {
  const now = new Date()
  return users.value.filter(u => {
    const d = new Date(u.created_at)
    return d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear()
  }).length
})
const allSelected = computed(() => users.value.length > 0 && selectedIds.value.length === users.value.length)

function toggleSelectAll() {
  selectedIds.value = allSelected.value ? [] : users.value.map(u => u.id)
}
function toggleSelect(id) {
  const i = selectedIds.value.indexOf(id)
  i === -1 ? selectedIds.value.push(id) : selectedIds.value.splice(i, 1)
}

function debouncedFetch() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => fetchUsers(), 300)
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
  return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

async function fetchUsers(page = 1) {
  loading.value = true
  selectedIds.value = []
  try {
    const params = { page, search: search.value || undefined, role: roleFilter.value || undefined }
    if (statusFilter.value === 'active') params.active = true
    else if (statusFilter.value === 'inactive') params.active = false
    const { data } = await adminApi.getUsers(params)
    users.value = data.data || []
    meta.value = data.meta || { current_page: 1, last_page: 1, per_page: 15, total: 0 }
  } catch { users.value = [] } finally { loading.value = false }
}

async function toggleActive(user) {
  try {
    await adminApi.toggleUserActive(user.id)
    user.is_active = !user.is_active
    uiStore.showToast(`User ${user.is_active ? 'activated' : 'deactivated'}`)
  } catch {
    uiStore.showToast('Failed to update user', 'error')
  }
}

async function handleCreate() {
  creating.value = true
  createError.value = ''
  try {
    await adminApi.createUser(createForm.value)
    uiStore.showToast('User created successfully!')
    showCreateModal.value = false
    createForm.value = { name: '', email: '', password: '', role_id: '' }
    fetchUsers()
  } catch (e) {
    createError.value = e.response?.data?.message || 'Failed to create user'
  } finally { creating.value = false }
}

function confirmDelete(user) {
  deleteTarget.value = user
}

async function handleDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  try {
    await adminApi.deleteUser(deleteTarget.value.id)
    uiStore.showToast('User deleted successfully')
    deleteTarget.value = null
    fetchUsers(meta.value.current_page)
  } catch {
    uiStore.showToast('Failed to delete user', 'error')
  } finally { deleting.value = false }
}

async function bulkDelete() {
  if (!confirm(`Delete ${selectedIds.value.length} users?`)) return
  try {
    await Promise.all(selectedIds.value.map(id => adminApi.deleteUser(id)))
    uiStore.showToast(`${selectedIds.value.length} users deleted`)
    selectedIds.value = []
    fetchUsers(meta.value.current_page)
  } catch {
    uiStore.showToast('Some deletions failed', 'error')
  }
}

async function fetchRoles() {
  try {
    const { data } = await adminApi.getRoles()
    roles.value = data.data || []
  } catch { roles.value = [] }
}

onMounted(() => {
  fetchUsers()
  fetchRoles()
})
</script>
