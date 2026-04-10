<template>
  <div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-sans font-bold dark:text-white">Messages</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Contact form submissions and inquiries</p>
      </div>
      <div class="flex items-center gap-2">
        <button v-if="selectedIds.length" @click="bulkDelete" class="px-3 py-2 text-xs font-medium rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-500/20 transition-colors flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
          Delete {{ selectedIds.length }}
        </button>
        <button v-if="selectedIds.length" @click="bulkMarkRead" class="px-3 py-2 text-xs font-medium rounded-lg bg-brand-violet/10 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan hover:opacity-80 transition-colors flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"/></svg>
          Mark Read
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="glass-card !p-4 flex items-center gap-3 cursor-pointer transition-all hover:scale-[1.02]" :class="readFilter === '' ? 'ring-2 ring-brand-violet dark:ring-brand-cyan' : ''" @click="readFilter = ''; fetchMessages()">
        <div class="w-10 h-10 rounded-xl bg-brand-violet/10 dark:bg-brand-violet/20 flex items-center justify-center">
          <svg class="w-5 h-5 text-brand-violet" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
          <p class="text-lg font-bold dark:text-white">{{ allMessages.length }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3 cursor-pointer transition-all hover:scale-[1.02]" :class="readFilter === 'unread' ? 'ring-2 ring-blue-400' : ''" @click="readFilter = 'unread'; fetchMessages()">
        <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center relative">
          <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-blue-500 border-2 border-white dark:border-dark-800" />
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Unread</p>
          <p class="text-lg font-bold text-blue-500">{{ unreadCount }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3 cursor-pointer transition-all hover:scale-[1.02]" :class="readFilter === 'read' ? 'ring-2 ring-green-400' : ''" @click="readFilter = 'read'; fetchMessages()">
        <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Read</p>
          <p class="text-lg font-bold text-green-500">{{ readCount }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-brand-cyan/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Latest</p>
          <p class="text-sm font-bold text-brand-cyan truncate">{{ latestTime }}</p>
        </div>
      </div>
    </div>

    <!-- Search & Filters -->
    <div class="glass-card !p-4 mb-6">
      <div class="flex flex-col md:flex-row items-start md:items-center gap-3">
        <div class="relative flex-1 w-full">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input v-model="search" type="text" placeholder="Search by name, email, or subject..." class="input-field !pl-10 w-full" @input="debouncedFetch" />
        </div>
        <CustomSelect
          v-model="readFilter"
          :options="[{ label: 'All Messages', value: '' }, { label: 'Unread', value: 'unread' }, { label: 'Read', value: 'read' }]"
          placeholder="All Messages"
          size="md"
          class="w-full md:w-40"
          @change="fetchMessages()"
        />
        <div class="flex items-center border border-gray-200 dark:border-dark-600 rounded-lg overflow-hidden">
          <button @click="viewMode = 'inbox'" class="p-2 transition-colors" :class="viewMode === 'inbox' ? 'bg-brand-violet/10 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan' : 'text-gray-400 hover:text-gray-600'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
          </button>
          <button @click="viewMode = 'cards'" class="p-2 transition-colors" :class="viewMode === 'cards' ? 'bg-brand-violet/10 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan' : 'text-gray-400 hover:text-gray-600'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="w-10 h-10 border-4 border-brand-violet/30 border-t-brand-violet rounded-full animate-spin" />
    </div>

    <!-- Inbox View (Table-like) -->
    <div v-else-if="viewMode === 'inbox' && messages.length" class="glass-card overflow-hidden">
      <div class="divide-y divide-gray-100 dark:divide-dark-700">
        <div v-for="msg in messages" :key="msg.id"
          class="flex items-center gap-4 px-5 py-4 cursor-pointer transition-all hover:bg-gray-50/50 dark:hover:bg-dark-700/30"
          :class="{ 'bg-blue-50/30 dark:bg-blue-900/5': !msg.is_read }"
          @click="viewMessage(msg)">
          <!-- Checkbox -->
          <input type="checkbox" :checked="selectedIds.includes(msg.id)" @click.stop @change="toggleSelect(msg.id)" class="w-4 h-4 rounded border-gray-300 text-brand-violet focus:ring-brand-violet dark:border-dark-500 flex-shrink-0" />
          <!-- Unread Dot -->
          <div class="w-2.5 flex-shrink-0">
            <span v-if="!msg.is_read" class="block w-2.5 h-2.5 rounded-full bg-blue-500" />
          </div>
          <!-- Avatar -->
          <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-xs flex-shrink-0" :style="{ background: avatarGradient(msg.name) }">
            {{ msg.name?.charAt(0)?.toUpperCase() }}
          </div>
          <!-- Content -->
          <div class="flex-1 min-w-0 grid grid-cols-1 lg:grid-cols-[180px_1fr] gap-x-4">
            <div class="min-w-0">
              <p class="text-sm truncate" :class="msg.is_read ? 'text-gray-600 dark:text-gray-400' : 'font-semibold text-gray-900 dark:text-white'">{{ msg.name }}</p>
              <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ msg.email }}</p>
            </div>
            <div class="min-w-0 hidden lg:block">
              <p class="text-sm truncate" :class="msg.is_read ? 'text-gray-600 dark:text-gray-400' : 'font-semibold text-gray-900 dark:text-white'">
                {{ msg.subject }}
                <span class="font-normal text-gray-400 dark:text-gray-500"> — {{ msg.body || msg.message || '' }}</span>
              </p>
            </div>
          </div>
          <!-- Date & Actions -->
          <div class="flex items-center gap-2 flex-shrink-0">
            <span class="text-xs whitespace-nowrap" :class="msg.is_read ? 'text-gray-400 dark:text-gray-500' : 'text-blue-500 font-medium'">{{ timeAgo(msg.created_at) }}</span>
            <div class="flex items-center opacity-0 group-hover:opacity-100 transition-opacity">
              <button @click.stop="deleteMessage(msg)" class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-500/10 transition-colors" title="Delete">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Card View -->
    <div v-else-if="viewMode === 'cards' && messages.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
      <div v-for="msg in messages" :key="msg.id"
        class="glass-card cursor-pointer transition-all hover:border-brand-violet/30 dark:hover:border-brand-cyan/30 hover:shadow-lg group"
        :class="{ 'border-l-4 border-l-blue-500': !msg.is_read }"
        @click="viewMessage(msg)">
        <!-- Card Header -->
        <div class="flex items-start justify-between mb-3">
          <div class="flex items-center gap-3">
            <input type="checkbox" :checked="selectedIds.includes(msg.id)" @click.stop @change="toggleSelect(msg.id)" class="w-4 h-4 rounded border-gray-300 text-brand-violet focus:ring-brand-violet dark:border-dark-500" />
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm" :style="{ background: avatarGradient(msg.name) }">
              {{ msg.name?.charAt(0)?.toUpperCase() }}
            </div>
            <div class="min-w-0">
              <p class="text-sm truncate" :class="msg.is_read ? 'text-gray-600 dark:text-gray-400' : 'font-semibold text-gray-900 dark:text-white'">{{ msg.name }}</p>
              <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ msg.email }}</p>
            </div>
          </div>
          <div class="flex items-center gap-1.5 flex-shrink-0">
            <span v-if="!msg.is_read" class="w-2 h-2 rounded-full bg-blue-500" />
            <button @click.stop="deleteMessage(msg)" class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-500/10 transition-colors opacity-0 group-hover:opacity-100">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </div>
        </div>
        <!-- Subject -->
        <h3 class="text-sm mb-2 line-clamp-1" :class="msg.is_read ? 'text-gray-700 dark:text-gray-300' : 'font-semibold text-gray-900 dark:text-white'">{{ msg.subject }}</h3>
        <!-- Body Preview -->
        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mb-3 leading-relaxed">{{ msg.body || msg.message || 'No content' }}</p>
        <!-- Footer -->
        <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-dark-700">
          <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ formatDate(msg.created_at) }}</span>
          <span class="text-[10px]" :class="msg.is_read ? 'text-green-500' : 'text-blue-500'">{{ msg.is_read ? 'Read' : 'New' }}</span>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!loading" class="glass-card flex flex-col items-center justify-center py-16">
      <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-dark-700 flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
      </div>
      <h3 class="font-semibold text-gray-900 dark:text-white mb-1">No messages</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400">{{ readFilter ? 'No messages match the selected filter.' : 'Your inbox is empty. Messages from the contact form will appear here.' }}</p>
      <button v-if="readFilter || search" @click="readFilter = ''; search = ''; fetchMessages()" class="mt-4 text-sm text-brand-violet dark:text-brand-cyan hover:underline">Clear filters</button>
    </div>

    <!-- Pagination -->
    <div v-if="!loading && meta.last_page > 1" class="mt-6">
      <Pagination :current-page="meta.current_page" :last-page="meta.last_page" @page-change="fetchMessages" />
    </div>

    <!-- Detail Modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition-opacity duration-200" leave-active-class="transition-opacity duration-200" enter-from-class="opacity-0" leave-to-class="opacity-0">
        <div v-if="selected" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="selected = null">
          <Transition enter-active-class="transition-all duration-200" leave-active-class="transition-all duration-200" enter-from-class="opacity-0 scale-95" leave-to-class="opacity-0 scale-95">
            <div v-if="selected" class="bg-white dark:bg-dark-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[85vh] overflow-y-auto border border-gray-200 dark:border-dark-600">
              <!-- Modal Header -->
              <div class="sticky top-0 bg-white dark:bg-dark-800 border-b border-gray-200 dark:border-dark-600 px-6 py-4 flex items-center justify-between rounded-t-2xl z-10">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm" :style="{ background: avatarGradient(selected.name) }">
                    {{ selected.name?.charAt(0)?.toUpperCase() }}
                  </div>
                  <div>
                    <h3 class="font-bold text-gray-900 dark:text-white">{{ selected.name }}</h3>
                    <a :href="'mailto:' + selected.email" class="text-xs text-brand-violet dark:text-brand-cyan hover:underline">{{ selected.email }}</a>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <button @click="deleteMessage(selected); selected = null" class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-500/10 transition-colors" title="Delete">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                  <button @click="selected = null" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:text-gray-300 dark:hover:bg-dark-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                  </button>
                </div>
              </div>

              <!-- Modal Body -->
              <div class="px-6 py-5 space-y-5">
                <!-- Subject & Meta -->
                <div>
                  <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ selected.subject }}</h2>
                  <div class="flex items-center gap-3 flex-wrap">
                    <span :class="selected.is_read ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium">
                      <span class="w-1.5 h-1.5 rounded-full" :class="selected.is_read ? 'bg-green-500' : 'bg-blue-500'" />
                      {{ selected.is_read ? 'Read' : 'Unread' }}
                    </span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ formatDate(selected.created_at) }}</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">({{ timeAgo(selected.created_at) }})</span>
                  </div>
                </div>

                <!-- Message Body -->
                <div class="bg-gray-50 dark:bg-dark-700 rounded-xl p-5">
                  <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">{{ selected.body || selected.message || 'No content' }}</p>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3 pt-2">
                  <a :href="'mailto:' + selected.email + '?subject=Re: ' + encodeURIComponent(selected.subject)"
                    class="btn-primary flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10l9 6 9-6M3 10v8a2 2 0 002 2h14a2 2 0 002-2v-8"/></svg>
                    Reply via Email
                  </a>
                  <button @click="copyEmail(selected.email)" class="px-4 py-2 text-sm font-medium rounded-lg bg-gray-100 dark:bg-dark-600 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-500 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                    {{ copied ? 'Copied!' : 'Copy Email' }}
                  </button>
                </div>
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
                <h3 class="font-bold text-gray-900 dark:text-white">Delete Message</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">This action cannot be undone.</p>
              </div>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-5">
              Are you sure you want to delete the message from <strong class="dark:text-white">{{ deleteTarget.name }}</strong>?
            </p>
            <div class="flex items-center gap-3 justify-end">
              <button @click="deleteTarget = null" class="px-4 py-2 text-sm font-medium rounded-lg bg-gray-100 dark:bg-dark-600 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-500 transition-colors">Cancel</button>
              <button @click="confirmDelete" class="px-4 py-2 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors">Delete</button>
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
import Pagination from '@/components/common/Pagination.vue'
import CustomSelect from '@/components/common/CustomSelect.vue'

const uiStore = useUiStore()
const messages = ref([])
const allMessages = ref([])
const loading = ref(true)
const search = ref('')
const readFilter = ref('')
const viewMode = ref('inbox')
const selectedIds = ref([])
const selected = ref(null)
const deleteTarget = ref(null)
const copied = ref(false)
const meta = ref({ current_page: 1, last_page: 1, total: 0 })

let debounceTimer = null
function debouncedFetch() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => fetchMessages(), 300)
}

const unreadCount = computed(() => allMessages.value.filter(m => !m.is_read).length)
const readCount = computed(() => allMessages.value.filter(m => m.is_read).length)
const latestTime = computed(() => {
  if (!allMessages.value.length) return '—'
  return timeAgo(allMessages.value[0]?.created_at)
})

const allSelected = computed(() => messages.value.length > 0 && selectedIds.value.length === messages.value.length)

function toggleSelectAll() {
  selectedIds.value = allSelected.value ? [] : messages.value.map(m => m.id)
}

function toggleSelect(id) {
  const idx = selectedIds.value.indexOf(id)
  idx === -1 ? selectedIds.value.push(id) : selectedIds.value.splice(idx, 1)
}

async function fetchMessages(page = 1) {
  loading.value = true
  try {
    const params = { page: typeof page === 'number' ? page : 1 }
    if (search.value) params.search = search.value
    if (readFilter.value === 'unread') params.read = false
    if (readFilter.value === 'read') params.read = true
    const { data } = await adminApi.getMessages(params)
    messages.value = data.data || []
    meta.value = data.meta || { current_page: 1, last_page: 1, total: 0 }
    if (!readFilter.value && !search.value && page === 1) {
      allMessages.value = [...messages.value]
    }
  } catch { messages.value = [] } finally { loading.value = false }
}

async function fetchAllForStats() {
  try {
    const { data } = await adminApi.getMessages({ per_page: 999 })
    allMessages.value = data.data || []
  } catch {}
}

async function viewMessage(msg) {
  selected.value = msg
  if (!msg.is_read) {
    try {
      await adminApi.markMessageRead(msg.id)
      msg.is_read = true
      const idx = allMessages.value.findIndex(m => m.id === msg.id)
      if (idx !== -1) allMessages.value[idx].is_read = true
    } catch {}
  }
}

function deleteMessage(msg) {
  deleteTarget.value = msg
}

async function confirmDelete() {
  const msg = deleteTarget.value
  if (!msg) return
  try {
    await adminApi.deleteMessage(msg.id)
    messages.value = messages.value.filter(m => m.id !== msg.id)
    allMessages.value = allMessages.value.filter(m => m.id !== msg.id)
    if (selected.value?.id === msg.id) selected.value = null
    uiStore.showToast('Message deleted')
  } catch { uiStore.showToast('Failed to delete', 'error') }
  deleteTarget.value = null
}

async function bulkDelete() {
  for (const id of selectedIds.value) {
    try {
      await adminApi.deleteMessage(id)
      messages.value = messages.value.filter(m => m.id !== id)
      allMessages.value = allMessages.value.filter(m => m.id !== id)
    } catch {}
  }
  selectedIds.value = []
  uiStore.showToast('Messages deleted')
}

async function bulkMarkRead() {
  for (const id of selectedIds.value) {
    try {
      await adminApi.markMessageRead(id)
      const msg = messages.value.find(m => m.id === id)
      if (msg) msg.is_read = true
      const aMsg = allMessages.value.find(m => m.id === id)
      if (aMsg) aMsg.is_read = true
    } catch {}
  }
  selectedIds.value = []
  uiStore.showToast('Messages marked as read')
}

function copyEmail(email) {
  navigator.clipboard.writeText(email)
  copied.value = true
  setTimeout(() => { copied.value = false }, 2000)
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
  return colors[(name || '').charCodeAt(0) % colors.length]
}

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' })
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

onMounted(() => {
  fetchMessages()
  fetchAllForStats()
})
</script>
