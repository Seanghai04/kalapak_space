<template>
  <div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-sans font-bold dark:text-white">Applications</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Review and manage team membership applications</p>
      </div>
      <div class="flex items-center gap-3">
        <span class="text-xs text-gray-400 dark:text-gray-500">{{ meta.total || 0 }} total</span>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="glass-card !p-4 flex items-center gap-3 cursor-pointer transition-all hover:scale-[1.02]" :class="statusFilter === '' ? 'ring-2 ring-brand-violet dark:ring-brand-cyan' : ''" @click="statusFilter = ''; fetchApplications()">
        <div class="w-10 h-10 rounded-xl bg-brand-violet/10 dark:bg-brand-violet/20 flex items-center justify-center">
          <svg class="w-5 h-5 text-brand-violet" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
          <p class="text-lg font-bold dark:text-white">{{ allApplications.length }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3 cursor-pointer transition-all hover:scale-[1.02]" :class="statusFilter === 'pending' ? 'ring-2 ring-yellow-400' : ''" @click="statusFilter = 'pending'; fetchApplications()">
        <div class="w-10 h-10 rounded-xl bg-yellow-500/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Pending</p>
          <p class="text-lg font-bold text-yellow-500">{{ countByStatus('pending') }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3 cursor-pointer transition-all hover:scale-[1.02]" :class="statusFilter === 'accepted' ? 'ring-2 ring-green-400' : ''" @click="statusFilter = 'accepted'; fetchApplications()">
        <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Accepted</p>
          <p class="text-lg font-bold text-green-500">{{ countByStatus('accepted') }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3 cursor-pointer transition-all hover:scale-[1.02]" :class="statusFilter === 'rejected' ? 'ring-2 ring-red-400' : ''" @click="statusFilter = 'rejected'; fetchApplications()">
        <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Rejected</p>
          <p class="text-lg font-bold text-red-500">{{ countByStatus('rejected') }}</p>
        </div>
      </div>
    </div>

    <!-- Search & Filters -->
    <div class="glass-card !p-4 mb-6">
      <div class="flex flex-col md:flex-row items-start md:items-center gap-3">
        <div class="relative flex-1 w-full">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input v-model="search" type="text" placeholder="Search by name or email..." class="input-field !pl-10 w-full" @input="debouncedFetch" />
        </div>
        <CustomSelect
          v-model="statusFilter"
          :options="[{ label: 'All Status', value: '' }, { label: 'Pending', value: 'pending' }, { label: 'Reviewing', value: 'reviewing' }, { label: 'Accepted', value: 'accepted' }, { label: 'Rejected', value: 'rejected' }]"
          placeholder="All Status"
          size="md"
          class="w-full md:w-44"
          @change="fetchApplications()"
        />
        <div class="flex items-center border border-gray-200 dark:border-dark-600 rounded-lg overflow-hidden">
          <button @click="viewMode = 'table'" class="p-2 transition-colors" :class="viewMode === 'table' ? 'bg-brand-violet/10 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan' : 'text-gray-400 hover:text-gray-600'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
          </button>
          <button @click="viewMode = 'cards'" class="p-2 transition-colors" :class="viewMode === 'cards' ? 'bg-brand-violet/10 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan' : 'text-gray-400 hover:text-gray-600'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Bulk Actions -->
    <div v-if="selectedIds.length" class="glass-card !p-3 mb-4 flex items-center gap-3 border border-brand-violet/30 dark:border-brand-cyan/30">
      <span class="text-sm font-medium dark:text-white">{{ selectedIds.length }} selected</span>
      <div class="flex items-center gap-2 ml-auto">
        <button @click="bulkUpdateStatus('accepted')" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-green-500/10 text-green-600 dark:text-green-400 hover:bg-green-500/20 transition-colors">Accept All</button>
        <button @click="bulkUpdateStatus('rejected')" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-500/20 transition-colors">Reject All</button>
        <button @click="selectedIds = []" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-500/10 text-gray-600 dark:text-gray-400 hover:bg-gray-500/20 transition-colors">Clear</button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="w-10 h-10 border-4 border-brand-violet/30 border-t-brand-violet rounded-full animate-spin" />
    </div>

    <!-- Table View -->
    <div v-else-if="viewMode === 'table' && applications.length" class="glass-card overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-gray-200 dark:border-dark-600">
            <th class="text-left py-3 px-4">
              <input type="checkbox" :checked="allSelected" @change="toggleSelectAll" class="w-4 h-4 rounded border-gray-300 text-brand-violet focus:ring-brand-violet dark:border-dark-500" />
            </th>
            <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">Applicant</th>
            <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider hidden md:table-cell">Role</th>
            <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider hidden lg:table-cell">Skills</th>
            <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider hidden xl:table-cell">Links</th>
            <th class="text-center py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">Status</th>
            <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider hidden xl:table-cell">Applied</th>
            <th class="text-right py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="app in applications" :key="app.id" class="border-b border-gray-100 dark:border-dark-700 hover:bg-gray-50/50 dark:hover:bg-dark-700/30 transition-colors">
            <td class="py-3 px-4">
              <input type="checkbox" :checked="selectedIds.includes(app.id)" @change="toggleSelect(app.id)" class="w-4 h-4 rounded border-gray-300 text-brand-violet focus:ring-brand-violet dark:border-dark-500" />
            </td>
            <td class="py-3 px-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm" :style="{ background: avatarGradient(app.name) }">
                  {{ app.name?.charAt(0)?.toUpperCase() }}
                </div>
                <div class="min-w-0">
                  <p class="font-semibold text-gray-900 dark:text-white truncate">{{ app.name }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ app.email }}</p>
                </div>
              </div>
            </td>
            <td class="py-3 px-4 hidden md:table-cell">
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-brand-violet/10 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                {{ app.role_applied }}
              </span>
            </td>
            <td class="py-3 px-4 hidden lg:table-cell">
              <div class="flex flex-wrap gap-1 max-w-[200px]">
                <span v-for="skill in parseSkills(app.skills).slice(0, 3)" :key="skill" class="px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 dark:bg-dark-600 dark:text-gray-300">{{ skill }}</span>
                <span v-if="parseSkills(app.skills).length > 3" class="px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-500 dark:bg-dark-600 dark:text-gray-400">+{{ parseSkills(app.skills).length - 3 }}</span>
              </div>
            </td>
            <td class="py-3 px-4 hidden xl:table-cell">
              <div class="flex items-center gap-2">
                <a v-if="app.github_url" :href="app.github_url" target="_blank" rel="noopener" class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-dark-600 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-dark-500 transition-colors" title="GitHub">
                  <svg class="w-3.5 h-3.5 text-gray-600 dark:text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                </a>
                <a v-if="app.linkedin_url" :href="app.linkedin_url" target="_blank" rel="noopener" class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-dark-600 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-dark-500 transition-colors" title="LinkedIn">
                  <svg class="w-3.5 h-3.5 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.205 24 24 23.227 24 22.271V1.729C24 .774 23.205 0 22.222 0h.003z"/></svg>
                </a>
                <a v-if="app.portfolio_url" :href="app.portfolio_url" target="_blank" rel="noopener" class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-dark-600 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-dark-500 transition-colors" title="Portfolio">
                  <svg class="w-3.5 h-3.5 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
                <span v-if="!app.github_url && !app.linkedin_url && !app.portfolio_url" class="text-xs text-gray-400">—</span>
              </div>
            </td>
            <td class="py-3 px-4 text-center">
              <span :class="statusBadgeClass(app.status)" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium">
                <span class="w-1.5 h-1.5 rounded-full" :class="statusDotClass(app.status)" />
                {{ statusLabel(app.status) }}
              </span>
            </td>
            <td class="py-3 px-4 hidden xl:table-cell">
              <div class="text-xs text-gray-500 dark:text-gray-400">
                <p>{{ formatDate(app.created_at) }}</p>
                <p class="text-gray-400 dark:text-gray-500">{{ timeAgo(app.created_at) }}</p>
              </div>
            </td>
            <td class="py-3 px-4 text-right">
              <div class="flex items-center justify-end gap-1">
                <button @click="openDetail(app)" class="p-2 rounded-lg text-gray-400 hover:text-brand-violet hover:bg-brand-violet/10 dark:hover:text-brand-cyan dark:hover:bg-brand-cyan/10 transition-colors" title="View Details">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
                <div class="relative" v-if="app.status === 'pending' || app.status === 'reviewing'">
                  <button @click="quickAccept(app)" class="p-2 rounded-lg text-gray-400 hover:text-green-500 hover:bg-green-500/10 transition-colors" title="Accept">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                  </button>
                  <button @click="quickReject(app)" class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-500/10 transition-colors" title="Reject">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                  </button>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Card View -->
    <div v-else-if="viewMode === 'cards' && applications.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
      <div v-for="app in applications" :key="app.id" class="glass-card hover:border-brand-violet/30 dark:hover:border-brand-cyan/30 transition-all group">
        <!-- Card Header -->
        <div class="flex items-start justify-between mb-4">
          <div class="flex items-center gap-3">
            <input type="checkbox" :checked="selectedIds.includes(app.id)" @change="toggleSelect(app.id)" class="w-4 h-4 rounded border-gray-300 text-brand-violet focus:ring-brand-violet dark:border-dark-500" />
            <div class="w-11 h-11 rounded-full flex items-center justify-center text-white font-bold text-sm" :style="{ background: avatarGradient(app.name) }">
              {{ app.name?.charAt(0)?.toUpperCase() }}
            </div>
            <div class="min-w-0">
              <p class="font-semibold text-gray-900 dark:text-white truncate">{{ app.name }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ app.email }}</p>
            </div>
          </div>
          <span :class="statusBadgeClass(app.status)" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium flex-shrink-0">
            <span class="w-1.5 h-1.5 rounded-full" :class="statusDotClass(app.status)" />
            {{ statusLabel(app.status) }}
          </span>
        </div>

        <!-- Role -->
        <div class="mb-3">
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-brand-violet/10 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            {{ app.role_applied }}
          </span>
        </div>

        <!-- Motivation -->
        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">{{ app.motivation || 'No motivation provided' }}</p>

        <!-- Skills -->
        <div class="flex flex-wrap gap-1 mb-4" v-if="app.skills">
          <span v-for="skill in parseSkills(app.skills).slice(0, 4)" :key="skill" class="px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 dark:bg-dark-600 dark:text-gray-300">{{ skill }}</span>
          <span v-if="parseSkills(app.skills).length > 4" class="px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-500 dark:bg-dark-600 dark:text-gray-400">+{{ parseSkills(app.skills).length - 4 }}</span>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-dark-700">
          <div class="flex items-center gap-2">
            <a v-if="app.github_url" :href="app.github_url" target="_blank" rel="noopener" class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-dark-600 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-dark-500 transition-colors">
              <svg class="w-3.5 h-3.5 text-gray-600 dark:text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
            </a>
            <a v-if="app.linkedin_url" :href="app.linkedin_url" target="_blank" rel="noopener" class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-dark-600 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-dark-500 transition-colors">
              <svg class="w-3.5 h-3.5 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.205 24 24 23.227 24 22.271V1.729C24 .774 23.205 0 22.222 0h.003z"/></svg>
            </a>
            <a v-if="app.portfolio_url" :href="app.portfolio_url" target="_blank" rel="noopener" class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-dark-600 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-dark-500 transition-colors">
              <svg class="w-3.5 h-3.5 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <span class="text-[10px] text-gray-400 dark:text-gray-500 ml-1">{{ timeAgo(app.created_at) }}</span>
          </div>
          <div class="flex items-center gap-1">
            <button @click="openDetail(app)" class="p-2 rounded-lg text-gray-400 hover:text-brand-violet hover:bg-brand-violet/10 dark:hover:text-brand-cyan dark:hover:bg-brand-cyan/10 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>
            <button v-if="app.status === 'pending' || app.status === 'reviewing'" @click="quickAccept(app)" class="p-2 rounded-lg text-gray-400 hover:text-green-500 hover:bg-green-500/10 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </button>
            <button v-if="app.status === 'pending' || app.status === 'reviewing'" @click="quickReject(app)" class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-500/10 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!loading" class="glass-card flex flex-col items-center justify-center py-16">
      <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-dark-700 flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      </div>
      <h3 class="font-semibold text-gray-900 dark:text-white mb-1">No applications</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400">{{ statusFilter ? 'No applications match the selected filter.' : 'No applications have been submitted yet.' }}</p>
      <button v-if="statusFilter" @click="statusFilter = ''; fetchApplications()" class="mt-4 text-sm text-brand-violet dark:text-brand-cyan hover:underline">Clear filter</button>
    </div>

    <!-- Pagination -->
    <div v-if="!loading && meta.last_page > 1" class="mt-6">
      <Pagination :current-page="meta.current_page" :last-page="meta.last_page" @page-change="fetchApplications" />
    </div>

    <!-- Detail Modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition-opacity duration-200" leave-active-class="transition-opacity duration-200" enter-from-class="opacity-0" leave-to-class="opacity-0">
        <div v-if="detailApp" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="detailApp = null">
          <Transition enter-active-class="transition-all duration-200" leave-active-class="transition-all duration-200" enter-from-class="opacity-0 scale-95" leave-to-class="opacity-0 scale-95">
            <div v-if="detailApp" class="bg-white dark:bg-dark-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[85vh] overflow-y-auto border border-gray-200 dark:border-dark-600">
              <!-- Modal Header -->
              <div class="sticky top-0 bg-white dark:bg-dark-800 border-b border-gray-200 dark:border-dark-600 px-6 py-4 flex items-center justify-between rounded-t-2xl z-10">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm" :style="{ background: avatarGradient(detailApp.name) }">
                    {{ detailApp.name?.charAt(0)?.toUpperCase() }}
                  </div>
                  <div>
                    <h3 class="font-bold text-gray-900 dark:text-white">{{ detailApp.name }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ detailApp.email }}</p>
                  </div>
                </div>
                <button @click="detailApp = null" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:text-gray-300 dark:hover:bg-dark-700 transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
              </div>

              <!-- Modal Body -->
              <div class="px-6 py-5 space-y-5">
                <!-- Status & Role Row -->
                <div class="flex items-center gap-3 flex-wrap">
                  <span :class="statusBadgeClass(detailApp.status)" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full" :class="statusDotClass(detailApp.status)" />
                    {{ statusLabel(detailApp.status) }}
                  </span>
                  <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-brand-violet/10 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan">
                    {{ detailApp.role_applied }}
                  </span>
                  <span class="text-xs text-gray-400 dark:text-gray-500 ml-auto">Applied {{ formatDate(detailApp.created_at) }}</span>
                </div>

                <!-- Links -->
                <div v-if="detailApp.github_url || detailApp.linkedin_url || detailApp.portfolio_url" class="flex flex-wrap gap-2">
                  <a v-if="detailApp.github_url" :href="detailApp.github_url" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-50 dark:bg-dark-700 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                    GitHub
                  </a>
                  <a v-if="detailApp.linkedin_url" :href="detailApp.linkedin_url" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-50 dark:bg-dark-700 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors">
                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.205 24 24 23.227 24 22.271V1.729C24 .774 23.205 0 22.222 0h.003z"/></svg>
                    LinkedIn
                  </a>
                  <a v-if="detailApp.portfolio_url" :href="detailApp.portfolio_url" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-50 dark:bg-dark-700 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors">
                    <svg class="w-4 h-4 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Portfolio
                  </a>
                </div>

                <!-- Motivation -->
                <div>
                  <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Motivation</h4>
                  <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line bg-gray-50 dark:bg-dark-700 rounded-xl p-4">{{ detailApp.motivation || 'Not provided' }}</p>
                </div>

                <!-- Skills -->
                <div v-if="detailApp.skills">
                  <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Skills</h4>
                  <div class="flex flex-wrap gap-2">
                    <span v-for="skill in parseSkills(detailApp.skills)" :key="skill" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-700 dark:bg-dark-600 dark:text-gray-300">{{ skill }}</span>
                  </div>
                </div>

                <!-- Admin Notes -->
                <div>
                  <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Admin Notes</h4>
                  <textarea v-model="adminNotes" rows="3" class="input-field w-full" placeholder="Add internal notes about this applicant..." />
                </div>

                <!-- Status Update -->
                <div>
                  <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Update Status</h4>
                  <div class="flex flex-wrap gap-2">
                    <button v-for="s in ['pending', 'reviewing', 'accepted', 'rejected']" :key="s" @click="updateStatusFromModal(s)" :disabled="detailApp.status === s"
                      class="px-4 py-2 rounded-lg text-xs font-medium transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                      :class="s === 'accepted' ? 'bg-green-500/10 text-green-600 dark:text-green-400 hover:bg-green-500/20' : s === 'rejected' ? 'bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-500/20' : s === 'reviewing' ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-500/20' : 'bg-yellow-500/10 text-yellow-600 dark:text-yellow-400 hover:bg-yellow-500/20'">
                      {{ statusLabel(s) }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { adminApi } from '@/services/api'
import { useUiStore } from '@/stores/ui'
import Pagination from '@/components/common/Pagination.vue'
import CustomSelect from '@/components/common/CustomSelect.vue'

const uiStore = useUiStore()
const applications = ref([])
const allApplications = ref([])
const loading = ref(true)
const search = ref('')
const statusFilter = ref('')
const viewMode = ref('table')
const selectedIds = ref([])
const detailApp = ref(null)
const adminNotes = ref('')
const meta = ref({ current_page: 1, last_page: 1, total: 0 })

let debounceTimer = null
function debouncedFetch() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => fetchApplications(), 300)
}

const allSelected = computed(() => applications.value.length > 0 && selectedIds.value.length === applications.value.length)

function toggleSelectAll() {
  selectedIds.value = allSelected.value ? [] : applications.value.map(a => a.id)
}

function toggleSelect(id) {
  const idx = selectedIds.value.indexOf(id)
  idx === -1 ? selectedIds.value.push(id) : selectedIds.value.splice(idx, 1)
}

function countByStatus(status) {
  return allApplications.value.filter(a => a.status === status).length
}

async function fetchApplications(page = 1) {
  loading.value = true
  try {
    const { data } = await adminApi.getApplications({
      page: typeof page === 'number' ? page : 1,
      status: statusFilter.value || undefined,
      search: search.value || undefined,
    })
    applications.value = data.data || []
    meta.value = data.meta || { current_page: 1, last_page: 1, total: 0 }
    if (!statusFilter.value && !search.value && page === 1) {
      allApplications.value = [...applications.value]
    }
  } catch { applications.value = [] } finally { loading.value = false }
}

async function fetchAllForStats() {
  try {
    const { data } = await adminApi.getApplications({ per_page: 999 })
    allApplications.value = data.data || []
  } catch {}
}

async function updateStatus(app, status, notes) {
  try {
    await adminApi.updateApplicationStatus(app.id, { status, admin_notes: notes || undefined })
    app.status = status
    const idx = allApplications.value.findIndex(a => a.id === app.id)
    if (idx !== -1) allApplications.value[idx].status = status
    uiStore.showToast(`Application ${statusLabel(status).toLowerCase()}`)
  } catch { uiStore.showToast('Failed to update status', 'error') }
}

function quickAccept(app) { updateStatus(app, 'accepted') }
function quickReject(app) { updateStatus(app, 'rejected') }

async function bulkUpdateStatus(status) {
  for (const id of selectedIds.value) {
    const app = applications.value.find(a => a.id === id)
    if (app) await updateStatus(app, status)
  }
  selectedIds.value = []
}

function openDetail(app) {
  detailApp.value = app
  adminNotes.value = app.admin_notes || ''
}

async function updateStatusFromModal(status) {
  await updateStatus(detailApp.value, status, adminNotes.value)
  detailApp.value = { ...detailApp.value, status }
}

function parseSkills(skills) {
  if (!skills) return []
  if (Array.isArray(skills)) return skills
  return skills.split(',').map(s => s.trim()).filter(Boolean)
}

function statusLabel(status) {
  return { pending: 'Pending', reviewing: 'Reviewing', accepted: 'Accepted', rejected: 'Rejected' }[status] || status
}

function statusBadgeClass(status) {
  return {
    pending: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
    reviewing: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    accepted: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
    rejected: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
  }[status] || 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400'
}

function statusDotClass(status) {
  return {
    pending: 'bg-yellow-500',
    reviewing: 'bg-blue-500',
    accepted: 'bg-green-500',
    rejected: 'bg-red-500',
  }[status] || 'bg-gray-500'
}

function avatarGradient(name) {
  const colors = [
    'linear-gradient(135deg, #6c5ce7, #a29bfe)',
    'linear-gradient(135deg, #00cec9, #81ecec)',
    'linear-gradient(135deg, #e17055, #fab1a0)',
    'linear-gradient(135deg, #0984e3, #74b9ff)',
    'linear-gradient(135deg, #6c5ce7, #fd79a8)',
    'linear-gradient(135deg, #00b894, #55efc4)',
    'linear-gradient(135deg, #fdcb6e, #e17055)',
  ]
  const idx = (name || '').charCodeAt(0) % colors.length
  return colors[idx]
}

function formatDate(date) {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

function timeAgo(date) {
  if (!date) return ''
  const diff = Date.now() - new Date(date).getTime()
  const mins = Math.floor(diff / 60000)
  if (mins < 60) return `${mins}m ago`
  const hours = Math.floor(mins / 60)
  if (hours < 24) return `${hours}h ago`
  const days = Math.floor(hours / 24)
  if (days < 30) return `${days}d ago`
  return `${Math.floor(days / 30)}mo ago`
}

onMounted(() => {
  fetchApplications()
  fetchAllForStats()
})
</script>
