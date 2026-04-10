<template>
  <div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-sans font-bold dark:text-white">Projects</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your team's portfolio and showcase projects</p>
      </div>
      <router-link :to="{ name: 'admin-project-create' }" class="btn-primary flex items-center gap-2 text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Project
      </router-link>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-brand-violet/10 dark:bg-brand-violet/20 flex items-center justify-center">
          <svg class="w-5 h-5 text-brand-violet" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
          <p class="text-lg font-bold dark:text-white">{{ meta.total || 0 }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Active</p>
          <p class="text-lg font-bold text-green-500">{{ statusCount('active') }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-yellow-500/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">In Progress</p>
          <p class="text-lg font-bold text-yellow-500">{{ statusCount('wip') }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-brand-cyan/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Featured</p>
          <p class="text-lg font-bold text-brand-cyan">{{ featuredCount }}</p>
        </div>
      </div>
    </div>

    <!-- Filters & Search -->
    <div class="glass-card !p-4 mb-6">
      <div class="flex flex-col md:flex-row items-start md:items-center gap-3">
        <div class="relative flex-1 w-full">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input v-model="search" type="text" placeholder="Search by title..." class="input-field !pl-10 w-full" @input="debouncedFetch" />
        </div>
        <CustomSelect
          v-model="statusFilter"
          :options="[{ label: 'All Status', value: '' }, { label: 'Active', value: 'active' }, { label: 'Work in Progress', value: 'wip' }, { label: 'Archived', value: 'archived' }]"
          placeholder="All Status"
          size="md"
          class="w-full md:w-44"
          @change="fetchProjects()"
        />
        <CustomSelect
          v-model="featuredFilter"
          :options="[{ label: 'All Projects', value: '' }, { label: 'Featured Only', value: 'featured' }]"
          placeholder="All Projects"
          size="md"
          class="w-full md:w-40"
          @change="fetchProjects()"
        />
        <div class="flex items-center border border-gray-200 dark:border-dark-600 rounded-lg overflow-hidden">
          <button @click="viewMode = 'table'" class="p-2 transition-colors" :class="viewMode === 'table' ? 'bg-brand-violet/10 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan' : 'text-gray-400 hover:text-gray-600'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
          </button>
          <button @click="viewMode = 'grid'" class="p-2 transition-colors" :class="viewMode === 'grid' ? 'bg-brand-violet/10 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan' : 'text-gray-400 hover:text-gray-600'">
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
    <div v-else-if="viewMode === 'table' && projects.length" class="glass-card overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-gray-200 dark:border-dark-600">
            <th class="text-left py-3 px-4">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" :checked="allSelected" @change="toggleSelectAll" class="w-4 h-4 rounded border-gray-300 text-brand-violet focus:ring-brand-violet dark:border-dark-500" />
              </label>
            </th>
            <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">Project</th>
            <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider hidden lg:table-cell">Tags</th>
            <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider hidden md:table-cell">Status</th>
            <th class="text-center py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider hidden md:table-cell">Featured</th>
            <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider hidden xl:table-cell">Created</th>
            <th class="text-right py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="project in projects" :key="project.id" class="border-b border-gray-100 dark:border-dark-700 hover:bg-gray-50/50 dark:hover:bg-dark-700/30 transition-colors">
            <td class="py-3 px-4">
              <input type="checkbox" :checked="selectedIds.includes(project.id)" @change="toggleSelect(project.id)" class="w-4 h-4 rounded border-gray-300 text-brand-violet focus:ring-brand-violet dark:border-dark-500" />
            </td>
            <td class="py-3 px-4">
              <div class="flex items-center gap-3">
                <div class="relative flex-shrink-0">
                  <img v-if="project.cover_image" :src="project.cover_image" :alt="project.title" class="w-14 h-10 object-cover rounded-lg ring-1 ring-gray-200 dark:ring-dark-600" />
                  <div v-else class="w-14 h-10 rounded-lg bg-gradient-to-br from-brand-violet/20 to-brand-cyan/20 dark:from-brand-violet/10 dark:to-brand-cyan/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-brand-violet/50 dark:text-brand-cyan/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                  </div>
                </div>
                <div class="min-w-0">
                  <p class="font-semibold text-gray-900 dark:text-white truncate">{{ project.title }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[250px]">{{ project.description }}</p>
                </div>
              </div>
            </td>
            <td class="py-3 px-4 hidden lg:table-cell">
              <div class="flex flex-wrap gap-1">
                <span v-for="tag in (project.tags || []).slice(0, 3)" :key="tag.id" class="px-2 py-0.5 rounded-full text-[10px] font-medium" :style="{ backgroundColor: (tag.color || '#6c5ce7') + '15', color: tag.color || '#6c5ce7' }">
                  {{ tag.name }}
                </span>
                <span v-if="(project.tags || []).length > 3" class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-500 dark:bg-dark-600 dark:text-gray-400">
                  +{{ project.tags.length - 3 }}
                </span>
              </div>
            </td>
            <td class="py-3 px-4 hidden md:table-cell">
              <span :class="statusBadgeClass(project.status)" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium">
                <span class="w-1.5 h-1.5 rounded-full" :class="statusDotClass(project.status)" />
                {{ statusLabel(project.status) }}
              </span>
            </td>
            <td class="py-3 px-4 text-center hidden md:table-cell">
              <button @click="toggleFeatured(project)" class="transition-transform hover:scale-110" :title="project.is_featured ? 'Unfeature' : 'Feature'">
                <svg :class="project.is_featured ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600 hover:text-yellow-300'" class="w-5 h-5 transition-colors" :fill="project.is_featured ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
              </button>
            </td>
            <td class="py-3 px-4 hidden xl:table-cell text-xs text-gray-500 dark:text-gray-400">
              {{ formatDate(project.created_at) }}
            </td>
            <td class="py-3 px-4 text-right">
              <div class="flex items-center justify-end gap-1">
                <a v-if="project.demo_url" :href="project.demo_url" target="_blank" rel="noopener" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors group" title="View Demo">
                  <svg class="w-4 h-4 text-gray-400 group-hover:text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
                <a v-if="project.repo_url" :href="project.repo_url" target="_blank" rel="noopener" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors group" title="View Repo">
                  <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-200" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
                </a>
                <router-link :to="{ name: 'admin-project-edit', params: { id: project.id } }" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors group" title="Edit">
                  <svg class="w-4 h-4 text-gray-400 group-hover:text-brand-violet dark:group-hover:text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </router-link>
                <button @click="confirmDelete(project)" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors group" title="Delete">
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
    <div v-else-if="viewMode === 'grid' && projects.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <div v-for="project in projects" :key="project.id" class="glass-card !p-0 overflow-hidden hover:shadow-lg transition-all duration-300 group">
        <!-- Cover Image -->
        <div class="relative h-40 bg-gradient-to-br from-brand-violet/10 to-brand-cyan/10 dark:from-brand-violet/5 dark:to-brand-cyan/5 overflow-hidden">
          <img v-if="project.cover_image" :src="project.cover_image" :alt="project.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
          <div v-else class="w-full h-full flex items-center justify-center">
            <svg class="w-12 h-12 text-brand-violet/20 dark:text-brand-cyan/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          </div>
          <!-- Status & Featured overlays -->
          <div class="absolute top-3 left-3">
            <span :class="statusBadgeClass(project.status)" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium backdrop-blur-sm">
              <span class="w-1.5 h-1.5 rounded-full" :class="statusDotClass(project.status)" />
              {{ statusLabel(project.status) }}
            </span>
          </div>
          <button v-if="project.is_featured" class="absolute top-3 right-3">
            <svg class="w-5 h-5 text-yellow-400 drop-shadow" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
          </button>
        </div>
        <!-- Content -->
        <div class="p-4">
          <h3 class="font-semibold text-gray-900 dark:text-white text-sm truncate">{{ project.title }}</h3>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ project.description }}</p>
          <!-- Tags -->
          <div class="flex flex-wrap gap-1 mt-3">
            <span v-for="tag in (project.tags || []).slice(0, 3)" :key="tag.id" class="px-2 py-0.5 rounded-full text-[10px] font-medium" :style="{ backgroundColor: (tag.color || '#6c5ce7') + '15', color: tag.color || '#6c5ce7' }">
              {{ tag.name }}
            </span>
          </div>
          <!-- Meta -->
          <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100 dark:border-dark-700">
            <span class="text-[10px] text-gray-400">{{ formatDate(project.created_at) }}</span>
            <div class="flex items-center gap-1">
              <a v-if="project.demo_url" :href="project.demo_url" target="_blank" rel="noopener" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors" title="Demo">
                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
              </a>
              <router-link :to="{ name: 'admin-project-edit', params: { id: project.id } }" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors" title="Edit">
                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
              </router-link>
              <button @click="confirmDelete(project)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors" title="Delete">
                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty -->
    <EmptyState v-if="!loading && !projects.length" title="No projects found" message="Create your first project or adjust your filters." />

    <!-- Pagination -->
    <div v-if="meta.last_page > 1" class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
      <p class="text-sm text-gray-500 dark:text-gray-400">
        Showing <span class="font-medium text-gray-700 dark:text-gray-200">{{ (meta.current_page - 1) * meta.per_page + 1 }}</span>
        to <span class="font-medium text-gray-700 dark:text-gray-200">{{ Math.min(meta.current_page * meta.per_page, meta.total) }}</span>
        of <span class="font-medium text-gray-700 dark:text-gray-200">{{ meta.total }}</span> projects
      </p>
      <Pagination :current-page="meta.current_page" :last-page="meta.last_page" @page-change="fetchProjects" />
    </div>

    <!-- Delete Modal -->
    <teleport to="body">
      <transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="deleteTarget = null" />
          <div class="relative bg-white dark:bg-dark-800 rounded-2xl shadow-2xl w-full max-w-sm p-6 z-10 text-center">
            <div class="w-12 h-12 rounded-full bg-red-50 dark:bg-red-500/10 flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Delete Project</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
              Are you sure you want to delete <strong>"{{ deleteTarget.title }}"</strong>? This action cannot be undone.
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
const projects = ref([])
const loading = ref(true)
const search = ref('')
const statusFilter = ref('')
const featuredFilter = ref('')
const viewMode = ref('table')
const meta = ref({ current_page: 1, last_page: 1, per_page: 15, total: 0 })
const selectedIds = ref([])
const deleteTarget = ref(null)
const deleting = ref(false)
let debounceTimer = null

const statusCount = (s) => projects.value.filter(p => p.status === s).length
const featuredCount = computed(() => projects.value.filter(p => p.is_featured).length)
const allSelected = computed(() => projects.value.length > 0 && selectedIds.value.length === projects.value.length)

function toggleSelectAll() { selectedIds.value = allSelected.value ? [] : projects.value.map(p => p.id) }
function toggleSelect(id) { const i = selectedIds.value.indexOf(id); i === -1 ? selectedIds.value.push(id) : selectedIds.value.splice(i, 1) }

function debouncedFetch() { clearTimeout(debounceTimer); debounceTimer = setTimeout(() => fetchProjects(), 300) }

function statusBadgeClass(status) {
  const map = {
    active: 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400',
    wip: 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400',
    archived: 'bg-gray-100 text-gray-600 dark:bg-gray-500/10 dark:text-gray-400',
    completed: 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
  }
  return map[status] || map.active
}
function statusDotClass(status) {
  const map = { active: 'bg-green-500', wip: 'bg-yellow-500', archived: 'bg-gray-400', completed: 'bg-blue-500' }
  return map[status] || 'bg-green-500'
}
function statusLabel(status) {
  const map = { active: 'Active', wip: 'In Progress', archived: 'Archived', completed: 'Completed' }
  return map[status] || status
}

function formatDate(date) {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

async function fetchProjects(page = 1) {
  loading.value = true
  selectedIds.value = []
  try {
    const params = { page, search: search.value || undefined, status: statusFilter.value || undefined }
    if (featuredFilter.value === 'featured') params.is_featured = true
    const { data } = await adminApi.getProjects(params)
    projects.value = data.data || []
    meta.value = data.meta || { current_page: 1, last_page: 1, per_page: 15, total: 0 }
  } catch { projects.value = [] } finally { loading.value = false }
}

async function toggleFeatured(project) {
  const formData = new FormData()
  formData.append('title', project.title)
  formData.append('slug', project.slug)
  formData.append('description', project.description)
  formData.append('status', project.status)
  formData.append('is_featured', !project.is_featured)
  formData.append('_method', 'PUT')
  try {
    await adminApi.updateProject(project.id, formData)
    project.is_featured = !project.is_featured
    uiStore.showToast(project.is_featured ? 'Project featured!' : 'Project unfeatured')
  } catch { uiStore.showToast('Failed to update', 'error') }
}

function confirmDelete(project) { deleteTarget.value = project }

async function handleDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  try {
    await adminApi.deleteProject(deleteTarget.value.id)
    projects.value = projects.value.filter(p => p.id !== deleteTarget.value.id)
    uiStore.showToast('Project deleted')
    deleteTarget.value = null
  } catch { uiStore.showToast('Failed to delete', 'error') }
  finally { deleting.value = false }
}

async function bulkDelete() {
  if (!confirm(`Delete ${selectedIds.value.length} projects?`)) return
  try {
    await Promise.all(selectedIds.value.map(id => adminApi.deleteProject(id)))
    uiStore.showToast(`${selectedIds.value.length} projects deleted`)
    selectedIds.value = []
    fetchProjects(meta.value.current_page)
  } catch { uiStore.showToast('Some deletions failed', 'error') }
}

onMounted(() => fetchProjects())
</script>
