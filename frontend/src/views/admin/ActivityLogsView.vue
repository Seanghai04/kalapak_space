<template>
  <div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-sans font-bold dark:text-white">Activity Logs</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Track all system actions and user activity</p>
      </div>
      <div class="flex items-center gap-2">
        <button @click="fetchLogs()" class="p-2 rounded-lg text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan hover:bg-brand-violet/10 dark:hover:bg-brand-cyan/10 transition-colors" title="Refresh">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-brand-violet/10 dark:bg-brand-violet/20 flex items-center justify-center">
          <svg class="w-5 h-5 text-brand-violet" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Total Logs</p>
          <p class="text-lg font-bold dark:text-white">{{ meta.total || 0 }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Created</p>
          <p class="text-lg font-bold text-green-500">{{ actionCounts.created }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Updated</p>
          <p class="text-lg font-bold text-blue-500">{{ actionCounts.updated }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Deleted</p>
          <p class="text-lg font-bold text-red-500">{{ actionCounts.deleted }}</p>
        </div>
      </div>
    </div>

    <!-- Search & Filters -->
    <div class="glass-card !p-4 mb-6">
      <div class="flex flex-col md:flex-row items-start md:items-center gap-3">
        <div class="relative flex-1 w-full">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input v-model="search" type="text" placeholder="Search by description, user, or target..." class="input-field !pl-10 w-full" @input="debouncedFetch" />
        </div>
        <CustomSelect
          v-model="actionFilter"
          :options="[{ label: 'All Actions', value: '' }, { label: 'Created', value: 'created' }, { label: 'Updated', value: 'updated' }, { label: 'Deleted', value: 'deleted' }, { label: 'Login', value: 'login' }, { label: 'Logout', value: 'logout' }]"
          placeholder="All Actions"
          size="md"
          class="w-full md:w-44"
          @change="fetchLogs()"
        />
        <div class="flex items-center border border-gray-200 dark:border-dark-600 rounded-lg overflow-hidden">
          <button @click="viewMode = 'table'" class="p-2 transition-colors" :class="viewMode === 'table' ? 'bg-brand-violet/10 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan' : 'text-gray-400 hover:text-gray-600'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
          </button>
          <button @click="viewMode = 'timeline'" class="p-2 transition-colors" :class="viewMode === 'timeline' ? 'bg-brand-violet/10 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan' : 'text-gray-400 hover:text-gray-600'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="w-10 h-10 border-4 border-brand-violet/30 border-t-brand-violet rounded-full animate-spin" />
    </div>

    <!-- Table View -->
    <div v-else-if="viewMode === 'table' && logs.length" class="glass-card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-200 dark:border-dark-600">
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Target</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">IP Address</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Time</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-10"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-dark-700">
            <tr v-for="log in logs" :key="log.id" class="hover:bg-gray-50/50 dark:hover:bg-dark-700/30 transition-colors">
              <td class="px-4 py-3">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-full overflow-hidden bg-gray-100 dark:bg-dark-600 flex-shrink-0">
                    <img v-if="log.user?.avatar" :src="log.user.avatar" :alt="log.user.name" class="w-full h-full object-cover" />
                    <div v-else class="w-full h-full flex items-center justify-center text-xs font-bold text-gray-400">{{ getInitials(log.user?.name) }}</div>
                  </div>
                  <span class="text-sm font-medium text-gray-900 dark:text-white whitespace-nowrap">{{ log.user?.name || 'System' }}</span>
                </div>
              </td>
              <td class="px-4 py-3">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold" :class="actionBadge(log.action)">
                  <component :is="'span'" v-html="actionIcon(log.action)" />
                  {{ log.action }}
                </span>
              </td>
              <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 max-w-[300px] truncate">{{ log.description }}</td>
              <td class="px-4 py-3">
                <span v-if="log.subject_type" class="inline-flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                  <span class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-dark-600 font-mono text-[10px]">{{ log.subject_type }}</span>
                  <span class="text-gray-400">#{{ log.subject_id }}</span>
                </span>
                <span v-else class="text-xs text-gray-400">—</span>
              </td>
              <td class="px-4 py-3">
                <span v-if="log.ip_address" class="text-xs font-mono text-gray-400">{{ log.ip_address }}</span>
                <span v-else class="text-xs text-gray-400">—</span>
              </td>
              <td class="px-4 py-3">
                <div class="flex flex-col">
                  <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ timeAgo(log.created_at) }}</span>
                  <span class="text-[10px] text-gray-400">{{ formatDate(log.created_at) }}</span>
                </div>
              </td>
              <td class="px-4 py-3 text-right">
                <button @click="detailLog = log" class="p-1.5 rounded-lg text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan hover:bg-brand-violet/10 dark:hover:bg-brand-cyan/10 transition-colors" title="View details">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Timeline View -->
    <div v-else-if="viewMode === 'timeline' && logs.length" class="space-y-0">
      <div v-for="(group, date) in groupedByDate" :key="date" class="mb-6">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-dark-700 flex items-center justify-center">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          </div>
          <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ date }}</h3>
          <span class="text-xs text-gray-400 px-2 py-0.5 bg-gray-100 dark:bg-dark-700 rounded-full">{{ group.length }} events</span>
        </div>
        <div class="ml-4 border-l-2 border-gray-200 dark:border-dark-600 pl-6 space-y-3">
          <div v-for="log in group" :key="log.id"
            class="glass-card !p-4 relative cursor-pointer hover:shadow-md transition-shadow"
            @click="detailLog = log">
            <!-- Timeline Dot -->
            <div class="absolute -left-[31px] top-5 w-3 h-3 rounded-full border-2 border-white dark:border-dark-800" :class="dotColor(log.action)" />
            <div class="flex items-start justify-between gap-3">
              <div class="flex items-start gap-3 min-w-0 flex-1">
                <!-- Avatar -->
                <div class="w-9 h-9 rounded-full overflow-hidden bg-gray-100 dark:bg-dark-600 flex-shrink-0 mt-0.5">
                  <img v-if="log.user?.avatar" :src="log.user.avatar" :alt="log.user.name" class="w-full h-full object-cover" />
                  <div v-else class="w-full h-full flex items-center justify-center text-xs font-bold text-gray-400">{{ getInitials(log.user?.name) }}</div>
                </div>
                <div class="min-w-0 flex-1">
                  <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ log.user?.name || 'System' }}</span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold" :class="actionBadge(log.action)">{{ log.action }}</span>
                    <span v-if="log.subject_type" class="text-xs text-gray-400">
                      {{ log.subject_type }} <span class="font-mono">#{{ log.subject_id }}</span>
                    </span>
                  </div>
                  <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5 truncate">{{ log.description }}</p>
                </div>
              </div>
              <div class="flex flex-col items-end flex-shrink-0">
                <span class="text-[11px] text-gray-400 whitespace-nowrap">{{ timeAgo(log.created_at) }}</span>
                <span v-if="log.ip_address" class="text-[10px] font-mono text-gray-400 mt-0.5">{{ log.ip_address }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!loading" class="glass-card flex flex-col items-center justify-center py-16">
      <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-dark-700 flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
      </div>
      <h3 class="font-semibold text-gray-900 dark:text-white mb-1">No activity logs found</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400">{{ search || actionFilter ? 'Try adjusting your filters.' : 'Activity will appear here as actions are performed.' }}</p>
      <button v-if="search || actionFilter" @click="search = ''; actionFilter = ''; fetchLogs()" class="mt-4 text-sm text-brand-violet dark:text-brand-cyan hover:underline">Clear filters</button>
    </div>

    <!-- Pagination -->
    <div v-if="!loading && meta.last_page > 1" class="mt-6">
      <Pagination :current-page="meta.current_page" :last-page="meta.last_page" @page-change="fetchLogs" />
    </div>

    <!-- Detail Modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition-opacity duration-200" leave-active-class="transition-opacity duration-200" enter-from-class="opacity-0" leave-to-class="opacity-0">
        <div v-if="detailLog" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="detailLog = null">
          <Transition enter-active-class="transition-all duration-200" leave-active-class="transition-all duration-200" enter-from-class="opacity-0 scale-95" leave-to-class="opacity-0 scale-95">
            <div v-if="detailLog" class="bg-white dark:bg-dark-800 rounded-2xl shadow-2xl max-w-lg w-full border border-gray-200 dark:border-dark-600 max-h-[85vh] overflow-hidden flex flex-col">
              <!-- Modal Header -->
              <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-dark-600">
                <h3 class="font-bold text-gray-900 dark:text-white">Activity Detail</h3>
                <button @click="detailLog = null" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:text-gray-300 dark:hover:bg-dark-700 transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
              </div>
              <!-- Modal Body -->
              <div class="px-6 py-5 overflow-y-auto space-y-5">
                <!-- User Info -->
                <div class="flex items-center gap-3">
                  <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-100 dark:bg-dark-600">
                    <img v-if="detailLog.user?.avatar" :src="detailLog.user.avatar" :alt="detailLog.user.name" class="w-full h-full object-cover" />
                    <div v-else class="w-full h-full flex items-center justify-center font-bold text-gray-400">{{ getInitials(detailLog.user?.name) }}</div>
                  </div>
                  <div>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ detailLog.user?.name || 'System' }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(detailLog.created_at) }}</p>
                  </div>
                </div>

                <!-- Details Grid -->
                <div class="grid grid-cols-2 gap-4">
                  <div class="p-3 rounded-xl bg-gray-50 dark:bg-dark-700">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Action</p>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold" :class="actionBadge(detailLog.action)">{{ detailLog.action }}</span>
                  </div>
                  <div class="p-3 rounded-xl bg-gray-50 dark:bg-dark-700">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Target</p>
                    <p class="text-sm text-gray-900 dark:text-white">
                      <span v-if="detailLog.subject_type">{{ detailLog.subject_type }} <span class="font-mono text-gray-400">#{{ detailLog.subject_id }}</span></span>
                      <span v-else class="text-gray-400">—</span>
                    </p>
                  </div>
                  <div class="p-3 rounded-xl bg-gray-50 dark:bg-dark-700">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">IP Address</p>
                    <p class="text-sm font-mono text-gray-900 dark:text-white">{{ detailLog.ip_address || '—' }}</p>
                  </div>
                  <div class="p-3 rounded-xl bg-gray-50 dark:bg-dark-700">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Time</p>
                    <p class="text-sm text-gray-900 dark:text-white">{{ timeAgo(detailLog.created_at) }}</p>
                  </div>
                </div>

                <!-- Description -->
                <div class="p-3 rounded-xl bg-gray-50 dark:bg-dark-700">
                  <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Description</p>
                  <p class="text-sm text-gray-700 dark:text-gray-300">{{ detailLog.description }}</p>
                </div>

                <!-- Properties / Changes -->
                <div v-if="detailLog.properties && Object.keys(detailLog.properties).length" class="p-3 rounded-xl bg-gray-50 dark:bg-dark-700">
                  <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Changes / Data</p>
                  <div class="max-h-[200px] overflow-y-auto">
                    <pre class="text-xs font-mono text-gray-600 dark:text-gray-400 whitespace-pre-wrap break-all">{{ JSON.stringify(detailLog.properties, null, 2) }}</pre>
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
import Pagination from '@/components/common/Pagination.vue'
import CustomSelect from '@/components/common/CustomSelect.vue'

const logs = ref([])
const loading = ref(true)
const search = ref('')
const actionFilter = ref('')
const viewMode = ref('table')
const detailLog = ref(null)
const meta = ref({ current_page: 1, last_page: 1, total: 0 })
let debounceTimer = null

function debouncedFetch() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => fetchLogs(), 300)
}

const actionCounts = computed(() => {
  const counts = { created: 0, updated: 0, deleted: 0 }
  logs.value.forEach(l => { if (counts[l.action] !== undefined) counts[l.action]++ })
  return counts
})

const groupedByDate = computed(() => {
  const groups = {}
  logs.value.forEach(log => {
    const d = formatDateShort(log.created_at)
    if (!groups[d]) groups[d] = []
    groups[d].push(log)
  })
  return groups
})

function getInitials(name) {
  if (!name) return '?'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

function actionBadge(action) {
  const map = {
    created: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
    updated: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    deleted: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    login: 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
    logout: 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
  }
  return map[action] || 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400'
}

function actionIcon(action) {
  const icons = {
    created: '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>',
    updated: '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>',
    deleted: '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>',
    login: '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>',
    logout: '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>',
  }
  return icons[action] || ''
}

function dotColor(action) {
  const map = { created: 'bg-green-500', updated: 'bg-blue-500', deleted: 'bg-red-500', login: 'bg-purple-500', logout: 'bg-gray-400' }
  return map[action] || 'bg-gray-400'
}

function timeAgo(date) {
  if (!date) return ''
  const diff = Date.now() - new Date(date).getTime()
  const mins = Math.floor(diff / 60000)
  if (mins < 1) return 'Just now'
  if (mins < 60) return `${mins}m ago`
  const hours = Math.floor(mins / 60)
  if (hours < 24) return `${hours}h ago`
  const days = Math.floor(hours / 24)
  if (days < 30) return `${days}d ago`
  return `${Math.floor(days / 30)}mo ago`
}

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

function formatDateShort(d) {
  if (!d) return 'Unknown'
  const date = new Date(d)
  const today = new Date()
  const yesterday = new Date(today)
  yesterday.setDate(yesterday.getDate() - 1)

  if (date.toDateString() === today.toDateString()) return 'Today'
  if (date.toDateString() === yesterday.toDateString()) return 'Yesterday'
  return date.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' })
}

async function fetchLogs(page = 1) {
  loading.value = true
  try {
    const params = { page: typeof page === 'number' ? page : 1 }
    if (search.value) params.search = search.value
    if (actionFilter.value) params.action = actionFilter.value
    const { data } = await adminApi.getActivityLogs(params)
    logs.value = data.data || []
    meta.value = data.meta || { current_page: 1, last_page: 1, total: 0 }
  } catch {
    logs.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => fetchLogs())
</script>
