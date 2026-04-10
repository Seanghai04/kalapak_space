<template>
  <teleport to="body">
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="open" class="fixed inset-0 z-[100] flex items-start justify-center" @mousedown.self="close">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-md" @mousedown="close" />

        <!-- Search Panel -->
        <div class="relative w-full max-w-2xl mx-4 mt-[10vh] sm:mt-[12vh] flex flex-col rounded-2xl overflow-hidden"
             @keydown="handleKeydown">

          <div class="relative bg-white/95 dark:bg-dark-900/95 backdrop-blur-2xl shadow-2xl shadow-black/25 ring-1 ring-gray-200/80 dark:ring-white/[0.08] rounded-2xl overflow-hidden">

            <!-- Top accent -->
            <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-brand-violet via-brand-cyan to-brand-violet opacity-60" />

            <!-- Search Input -->
            <div class="flex items-center gap-3 px-5 sm:px-6">
              <div class="relative shrink-0">
                <svg v-if="!loading" class="w-5 h-5 text-brand-violet dark:text-brand-cyan transition-colors duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                <div v-else class="w-5 h-5 border-2 border-brand-violet/30 dark:border-brand-cyan/30 border-t-brand-violet dark:border-t-brand-cyan rounded-full animate-spin" />
              </div>

              <input
                ref="inputRef"
                v-model="query"
                type="text"
                :placeholder="activeFilter === 'all' ? 'Search users, projects, posts, messages...' : `Search ${activeFilter}...`"
                class="flex-1 h-14 sm:h-16 bg-transparent text-base sm:text-[16px] text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 outline-none font-medium"
                autocomplete="off"
                spellcheck="false"
                @input="onInput"
              />

              <button v-if="query.length" @click="clearQuery" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/[0.06] transition-colors group">
                <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>

              <kbd class="hidden sm:inline-flex px-2 py-1 rounded-lg bg-gray-100 dark:bg-white/[0.06] text-[10px] font-code text-gray-400 border border-gray-200/50 dark:border-white/[0.06] select-none">ESC</kbd>
            </div>

            <!-- Filters -->
            <div class="flex items-center gap-1 px-5 sm:px-6 pb-3 border-b border-gray-100 dark:border-white/[0.06] overflow-x-auto">
              <button
                v-for="filter in filters"
                :key="filter.key"
                @click="setFilter(filter.key)"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-semibold tracking-wide uppercase transition-all duration-200 whitespace-nowrap"
                :class="activeFilter === filter.key
                  ? 'bg-brand-violet/10 dark:bg-brand-cyan/10 text-brand-violet dark:text-brand-cyan ring-1 ring-brand-violet/20 dark:ring-brand-cyan/20'
                  : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-white/[0.04]'"
              >
                {{ filter.label }}
                <span v-if="totals[filter.countKey] > 0"
                      class="ml-0.5 min-w-[18px] h-[18px] inline-flex items-center justify-center rounded-full text-[9px] font-bold"
                      :class="activeFilter === filter.key
                        ? 'bg-brand-violet/20 dark:bg-brand-cyan/20 text-brand-violet dark:text-brand-cyan'
                        : 'bg-gray-200/80 dark:bg-white/[0.08] text-gray-400 dark:text-gray-500'"
                >{{ totals[filter.countKey] }}</span>
              </button>
            </div>

            <!-- Results -->
            <div class="max-h-[50vh] sm:max-h-[55vh] overflow-y-auto overscroll-contain search-scrollbar">

              <!-- Loading skeleton -->
              <div v-if="loading && !hasResults" class="p-3 space-y-2">
                <div v-for="n in 4" :key="n" class="flex items-center gap-3 px-3 py-3 rounded-xl animate-pulse">
                  <div class="w-10 h-10 rounded-xl bg-gray-200/80 dark:bg-white/[0.06]" />
                  <div class="flex-1 space-y-2">
                    <div class="h-3.5 w-2/3 rounded-md bg-gray-200/80 dark:bg-white/[0.06]" />
                    <div class="h-2.5 w-1/2 rounded-md bg-gray-200/60 dark:bg-white/[0.04]" />
                  </div>
                </div>
              </div>

              <!-- Results by category -->
              <div v-else-if="hasResults" class="p-2 sm:p-3">

                <!-- Users -->
                <div v-if="filteredData.users?.length" class="mb-2">
                  <div class="flex items-center gap-2 px-3 pt-2 pb-2">
                    <UsersIcon class="w-3.5 h-3.5 text-brand-violet dark:text-brand-cyan" />
                    <span class="text-[10px] font-bold uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">Users</span>
                    <span class="text-[9px] font-semibold text-gray-300 dark:text-gray-600">{{ filteredData.users.length }}</span>
                  </div>
                  <button
                    v-for="(item, i) in filteredData.users"
                    :key="'u' + item.id"
                    class="group flex items-center gap-3 w-full px-3 py-2.5 rounded-xl transition-all duration-150 cursor-pointer text-left"
                    :class="activeIndex === getGlobalIndex('users', i)
                      ? 'bg-brand-violet/8 dark:bg-brand-cyan/8 ring-1 ring-brand-violet/15 dark:ring-brand-cyan/15'
                      : 'hover:bg-gray-50 dark:hover:bg-white/[0.03]'"
                    @click="navigateTo('/admin/users', item)"
                    @mouseenter="activeIndex = getGlobalIndex('users', i)"
                  >
                    <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0 ring-1 ring-gray-200/60 dark:ring-white/[0.06] bg-gradient-to-br from-brand-violet/10 to-brand-cyan/10 flex items-center justify-center">
                      <img v-if="item.avatar" :src="item.avatar" class="w-full h-full object-cover" />
                      <UsersIcon v-else class="w-4.5 h-4.5 text-brand-violet/40 dark:text-brand-cyan/40" />
                    </div>
                    <div class="min-w-0 flex-1">
                      <p class="text-sm font-semibold text-gray-900 dark:text-white truncate" v-html="highlight(item.title)" />
                      <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5" v-html="highlight(item.subtitle)" />
                    </div>
                    <span class="text-[10px] font-medium px-2 py-0.5 rounded-md bg-gray-100 dark:bg-white/[0.06] text-gray-500 dark:text-gray-400">{{ item.meta }}</span>
                    <ArrowIcon :active="activeIndex === getGlobalIndex('users', i)" />
                  </button>
                </div>

                <!-- Projects -->
                <div v-if="filteredData.projects?.length" class="mb-2">
                  <div class="flex items-center gap-2 px-3 pt-2 pb-2">
                    <FolderIcon class="w-3.5 h-3.5 text-brand-violet dark:text-brand-cyan" />
                    <span class="text-[10px] font-bold uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">Projects</span>
                    <span class="text-[9px] font-semibold text-gray-300 dark:text-gray-600">{{ filteredData.projects.length }}</span>
                  </div>
                  <button
                    v-for="(item, i) in filteredData.projects"
                    :key="'p' + item.id"
                    class="group flex items-center gap-3 w-full px-3 py-2.5 rounded-xl transition-all duration-150 cursor-pointer text-left"
                    :class="activeIndex === getGlobalIndex('projects', i)
                      ? 'bg-brand-violet/8 dark:bg-brand-cyan/8 ring-1 ring-brand-violet/15 dark:ring-brand-cyan/15'
                      : 'hover:bg-gray-50 dark:hover:bg-white/[0.03]'"
                    @click="navigateTo(`/admin/projects`, item)"
                    @mouseenter="activeIndex = getGlobalIndex('projects', i)"
                  >
                    <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0 ring-1 ring-gray-200/60 dark:ring-white/[0.06] bg-gradient-to-br from-brand-violet/10 to-brand-cyan/10 flex items-center justify-center">
                      <img v-if="item.thumbnail" :src="item.thumbnail" class="w-full h-full object-cover" />
                      <FolderIcon v-else class="w-4.5 h-4.5 text-brand-violet/40 dark:text-brand-cyan/40" />
                    </div>
                    <div class="min-w-0 flex-1">
                      <p class="text-sm font-semibold text-gray-900 dark:text-white truncate" v-html="highlight(item.title)" />
                      <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ item.meta }}</p>
                    </div>
                    <span class="text-[10px] font-medium px-2 py-0.5 rounded-md capitalize" :class="statusClass(item.subtitle)">{{ item.subtitle }}</span>
                    <ArrowIcon :active="activeIndex === getGlobalIndex('projects', i)" />
                  </button>
                </div>

                <!-- Blog Posts -->
                <div v-if="filteredData.posts?.length" class="mb-2">
                  <div class="flex items-center gap-2 px-3 pt-2 pb-2">
                    <DocumentTextIcon class="w-3.5 h-3.5 text-brand-violet dark:text-brand-cyan" />
                    <span class="text-[10px] font-bold uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">Blog Posts</span>
                    <span class="text-[9px] font-semibold text-gray-300 dark:text-gray-600">{{ filteredData.posts.length }}</span>
                  </div>
                  <button
                    v-for="(item, i) in filteredData.posts"
                    :key="'b' + item.id"
                    class="group flex items-center gap-3 w-full px-3 py-2.5 rounded-xl transition-all duration-150 cursor-pointer text-left"
                    :class="activeIndex === getGlobalIndex('posts', i)
                      ? 'bg-brand-violet/8 dark:bg-brand-cyan/8 ring-1 ring-brand-violet/15 dark:ring-brand-cyan/15'
                      : 'hover:bg-gray-50 dark:hover:bg-white/[0.03]'"
                    @click="navigateTo('/admin/blog', item)"
                    @mouseenter="activeIndex = getGlobalIndex('posts', i)"
                  >
                    <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0 ring-1 ring-gray-200/60 dark:ring-white/[0.06] bg-gradient-to-br from-brand-violet/10 to-brand-cyan/10 flex items-center justify-center">
                      <img v-if="item.thumbnail" :src="item.thumbnail" class="w-full h-full object-cover" />
                      <DocumentTextIcon v-else class="w-4.5 h-4.5 text-brand-violet/40 dark:text-brand-cyan/40" />
                    </div>
                    <div class="min-w-0 flex-1">
                      <p class="text-sm font-semibold text-gray-900 dark:text-white truncate" v-html="highlight(item.title)" />
                      <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ item.subtitle }}</p>
                    </div>
                    <span class="text-[10px] font-medium px-2 py-0.5 rounded-md capitalize" :class="statusClass(item.meta)">{{ item.meta }}</span>
                    <ArrowIcon :active="activeIndex === getGlobalIndex('posts', i)" />
                  </button>
                </div>

                <!-- Messages -->
                <div v-if="filteredData.messages?.length" class="mb-2">
                  <div class="flex items-center gap-2 px-3 pt-2 pb-2">
                    <EnvelopeIcon class="w-3.5 h-3.5 text-brand-violet dark:text-brand-cyan" />
                    <span class="text-[10px] font-bold uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">Messages</span>
                    <span class="text-[9px] font-semibold text-gray-300 dark:text-gray-600">{{ filteredData.messages.length }}</span>
                  </div>
                  <button
                    v-for="(item, i) in filteredData.messages"
                    :key="'m' + item.id"
                    class="group flex items-center gap-3 w-full px-3 py-2.5 rounded-xl transition-all duration-150 cursor-pointer text-left"
                    :class="activeIndex === getGlobalIndex('messages', i)
                      ? 'bg-brand-violet/8 dark:bg-brand-cyan/8 ring-1 ring-brand-violet/15 dark:ring-brand-cyan/15'
                      : 'hover:bg-gray-50 dark:hover:bg-white/[0.03]'"
                    @click="navigateTo('/admin/messages', item)"
                    @mouseenter="activeIndex = getGlobalIndex('messages', i)"
                  >
                    <div class="w-10 h-10 rounded-xl shrink-0 ring-1 ring-gray-200/60 dark:ring-white/[0.06] bg-gradient-to-br from-brand-violet/10 to-brand-cyan/10 flex items-center justify-center">
                      <EnvelopeIcon class="w-4.5 h-4.5 text-brand-violet/40 dark:text-brand-cyan/40" />
                    </div>
                    <div class="min-w-0 flex-1">
                      <p class="text-sm font-semibold text-gray-900 dark:text-white truncate" v-html="highlight(item.title)" />
                      <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5" v-html="highlight(item.subtitle)" />
                    </div>
                    <span class="text-[10px] font-medium px-2 py-0.5 rounded-md"
                          :class="item.is_read ? 'bg-gray-100 dark:bg-white/[0.06] text-gray-500 dark:text-gray-400' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'">
                      {{ item.meta }}
                    </span>
                    <ArrowIcon :active="activeIndex === getGlobalIndex('messages', i)" />
                  </button>
                </div>

                <!-- Applications -->
                <div v-if="filteredData.applications?.length" class="mb-2">
                  <div class="flex items-center gap-2 px-3 pt-2 pb-2">
                    <InboxStackIcon class="w-3.5 h-3.5 text-brand-violet dark:text-brand-cyan" />
                    <span class="text-[10px] font-bold uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">Applications</span>
                    <span class="text-[9px] font-semibold text-gray-300 dark:text-gray-600">{{ filteredData.applications.length }}</span>
                  </div>
                  <button
                    v-for="(item, i) in filteredData.applications"
                    :key="'a' + item.id"
                    class="group flex items-center gap-3 w-full px-3 py-2.5 rounded-xl transition-all duration-150 cursor-pointer text-left"
                    :class="activeIndex === getGlobalIndex('applications', i)
                      ? 'bg-brand-violet/8 dark:bg-brand-cyan/8 ring-1 ring-brand-violet/15 dark:ring-brand-cyan/15'
                      : 'hover:bg-gray-50 dark:hover:bg-white/[0.03]'"
                    @click="navigateTo('/admin/applications', item)"
                    @mouseenter="activeIndex = getGlobalIndex('applications', i)"
                  >
                    <div class="w-10 h-10 rounded-xl shrink-0 ring-1 ring-gray-200/60 dark:ring-white/[0.06] bg-gradient-to-br from-brand-violet/10 to-brand-cyan/10 flex items-center justify-center">
                      <InboxStackIcon class="w-4.5 h-4.5 text-brand-violet/40 dark:text-brand-cyan/40" />
                    </div>
                    <div class="min-w-0 flex-1">
                      <p class="text-sm font-semibold text-gray-900 dark:text-white truncate" v-html="highlight(item.title)" />
                      <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5" v-html="highlight(item.subtitle)" />
                    </div>
                    <span class="text-[10px] font-medium px-2 py-0.5 rounded-md capitalize" :class="statusClass(item.meta)">{{ item.meta }}</span>
                    <ArrowIcon :active="activeIndex === getGlobalIndex('applications', i)" />
                  </button>
                </div>

                <!-- Team -->
                <div v-if="filteredData.team?.length" class="mb-2">
                  <div class="flex items-center gap-2 px-3 pt-2 pb-2">
                    <UserGroupIcon class="w-3.5 h-3.5 text-brand-violet dark:text-brand-cyan" />
                    <span class="text-[10px] font-bold uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">Team</span>
                    <span class="text-[9px] font-semibold text-gray-300 dark:text-gray-600">{{ filteredData.team.length }}</span>
                  </div>
                  <button
                    v-for="(item, i) in filteredData.team"
                    :key="'t' + item.id"
                    class="group flex items-center gap-3 w-full px-3 py-2.5 rounded-xl transition-all duration-150 cursor-pointer text-left"
                    :class="activeIndex === getGlobalIndex('team', i)
                      ? 'bg-brand-violet/8 dark:bg-brand-cyan/8 ring-1 ring-brand-violet/15 dark:ring-brand-cyan/15'
                      : 'hover:bg-gray-50 dark:hover:bg-white/[0.03]'"
                    @click="navigateTo('/admin/team', item)"
                    @mouseenter="activeIndex = getGlobalIndex('team', i)"
                  >
                    <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0 ring-1 ring-gray-200/60 dark:ring-white/[0.06] bg-gradient-to-br from-brand-violet/10 to-brand-cyan/10 flex items-center justify-center">
                      <img v-if="item.avatar" :src="item.avatar" class="w-full h-full object-cover" />
                      <UserGroupIcon v-else class="w-4.5 h-4.5 text-brand-violet/40 dark:text-brand-cyan/40" />
                    </div>
                    <div class="min-w-0 flex-1">
                      <p class="text-sm font-semibold text-gray-900 dark:text-white truncate" v-html="highlight(item.title)" />
                      <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5" v-html="highlight(item.subtitle)" />
                    </div>
                    <ArrowIcon :active="activeIndex === getGlobalIndex('team', i)" />
                  </button>
                </div>

                <!-- Tags -->
                <div v-if="filteredData.tags?.length" class="mb-2">
                  <div class="flex items-center gap-2 px-3 pt-2 pb-2">
                    <TagIcon class="w-3.5 h-3.5 text-brand-violet dark:text-brand-cyan" />
                    <span class="text-[10px] font-bold uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">Tags</span>
                    <span class="text-[9px] font-semibold text-gray-300 dark:text-gray-600">{{ filteredData.tags.length }}</span>
                  </div>
                  <button
                    v-for="(item, i) in filteredData.tags"
                    :key="'tg' + item.id"
                    class="group flex items-center gap-3 w-full px-3 py-2.5 rounded-xl transition-all duration-150 cursor-pointer text-left"
                    :class="activeIndex === getGlobalIndex('tags', i)
                      ? 'bg-brand-violet/8 dark:bg-brand-cyan/8 ring-1 ring-brand-violet/15 dark:ring-brand-cyan/15'
                      : 'hover:bg-gray-50 dark:hover:bg-white/[0.03]'"
                    @click="navigateTo('/admin/tags', item)"
                    @mouseenter="activeIndex = getGlobalIndex('tags', i)"
                  >
                    <div class="w-10 h-10 rounded-xl shrink-0 ring-1 ring-gray-200/60 dark:ring-white/[0.06] flex items-center justify-center"
                         :style="item.color ? `background: ${item.color}15` : ''">
                      <TagIcon class="w-4.5 h-4.5" :style="item.color ? `color: ${item.color}` : ''" :class="!item.color && 'text-brand-violet/40 dark:text-brand-cyan/40'" />
                    </div>
                    <div class="min-w-0 flex-1">
                      <p class="text-sm font-semibold text-gray-900 dark:text-white truncate" v-html="highlight(item.title)" />
                      <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ item.subtitle }}</p>
                    </div>
                    <ArrowIcon :active="activeIndex === getGlobalIndex('tags', i)" />
                  </button>
                </div>
              </div>

              <!-- No results -->
              <div v-else-if="query.length >= 2 && !loading" class="py-14 text-center">
                <div class="relative inline-flex items-center justify-center w-16 h-16 mb-4">
                  <div class="absolute inset-0 rounded-2xl bg-gray-100 dark:bg-white/[0.04] rotate-6" />
                  <div class="absolute inset-0 rounded-2xl bg-gray-100 dark:bg-white/[0.04] -rotate-3" />
                  <div class="relative w-full h-full rounded-2xl bg-gray-50 dark:bg-white/[0.03] flex items-center justify-center">
                    <svg class="w-7 h-7 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                      <line x1="9" y1="9" x2="15" y2="15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                  </div>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No results for "<span class="font-semibold text-gray-700 dark:text-gray-200">{{ query }}</span>"</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5 max-w-xs mx-auto">Try adjusting your search terms or browse by category</p>
              </div>

              <!-- Initial State: Quick navigation -->
              <div v-else-if="query.length < 2 && !loading" class="p-2 sm:p-3">
                <div class="flex items-center gap-2 px-3 pt-1 pb-2">
                  <span class="text-[10px] font-bold uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">Quick Navigation</span>
                </div>
                <div class="grid grid-cols-2 gap-1.5">
                  <router-link
                    v-for="(link, i) in quickLinks"
                    :key="'q' + i"
                    :to="link.to"
                    class="group flex items-center gap-2.5 px-3 py-3 rounded-xl transition-all duration-150 hover:bg-gray-50 dark:hover:bg-white/[0.03]"
                    @click="close"
                  >
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 bg-gray-100/80 dark:bg-white/[0.04]">
                      <component :is="link.icon" class="w-4 h-4 text-gray-400 dark:text-gray-500" />
                    </div>
                    <div class="min-w-0 flex-1">
                      <p class="text-[13px] font-semibold text-gray-700 dark:text-gray-200 truncate">{{ link.label }}</p>
                      <p class="text-[10px] text-gray-400 dark:text-gray-500 truncate">{{ link.description }}</p>
                    </div>
                  </router-link>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between px-5 sm:px-6 py-3 border-t border-gray-100 dark:border-white/[0.06] bg-gray-50/50 dark:bg-white/[0.015]">
              <div class="flex items-center gap-4 text-[11px] text-gray-400 dark:text-gray-500">
                <span class="inline-flex items-center gap-1.5">
                  <kbd class="inline-flex items-center justify-center min-w-[20px] h-5 px-1 rounded bg-gray-200/80 dark:bg-white/[0.08] text-[10px] font-code text-gray-500 dark:text-gray-400 border border-gray-300/50 dark:border-white/[0.06]">↑↓</kbd>
                  <span class="hidden sm:inline">Navigate</span>
                </span>
                <span class="inline-flex items-center gap-1.5">
                  <kbd class="inline-flex items-center justify-center min-w-[20px] h-5 px-1 rounded bg-gray-200/80 dark:bg-white/[0.08] text-[10px] font-code text-gray-500 dark:text-gray-400 border border-gray-300/50 dark:border-white/[0.06]">↵</kbd>
                  <span class="hidden sm:inline">Open</span>
                </span>
                <span class="inline-flex items-center gap-1.5">
                  <kbd class="inline-flex items-center justify-center min-w-[20px] h-5 px-1 rounded bg-gray-200/80 dark:bg-white/[0.08] text-[10px] font-code text-gray-500 dark:text-gray-400 border border-gray-300/50 dark:border-white/[0.06]">Esc</kbd>
                  <span class="hidden sm:inline">Close</span>
                </span>
              </div>
              <div class="flex items-center gap-1.5 text-[10px] text-gray-300 dark:text-gray-600">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <span class="font-semibold tracking-wide">Admin Search</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { adminApi } from '@/services/api'
import {
  UsersIcon,
  FolderIcon,
  DocumentTextIcon,
  EnvelopeIcon,
  UserGroupIcon,
  InboxStackIcon,
  TagIcon,
  HomeIcon,
  CogIcon,
  PhotoIcon,
  ShieldCheckIcon,
  ClipboardDocumentListIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({ open: Boolean })
const emit = defineEmits(['close'])

const router = useRouter()

const inputRef = ref(null)
const query = ref('')
const loading = ref(false)
const activeIndex = ref(0)
const activeFilter = ref('all')
const results = ref({})
const totals = ref({})
let debounceTimer = null

// Arrow icon helper component
const ArrowIcon = { props: ['active'], template: `<div class="shrink-0 transition-all duration-150" :class="active ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-1'"><svg class="w-4 h-4 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg></div>` }

const filters = [
  { key: 'all', label: 'All', countKey: 'all' },
  { key: 'users', label: 'Users', countKey: 'users' },
  { key: 'projects', label: 'Projects', countKey: 'projects' },
  { key: 'posts', label: 'Posts', countKey: 'posts' },
  { key: 'messages', label: 'Messages', countKey: 'messages' },
  { key: 'applications', label: 'Applications', countKey: 'applications' },
  { key: 'team', label: 'Team', countKey: 'team' },
  { key: 'tags', label: 'Tags', countKey: 'tags' },
]

const quickLinks = [
  { to: '/admin', label: 'Dashboard', description: 'Overview & stats', icon: HomeIcon },
  { to: '/admin/users', label: 'Users', description: 'Manage users', icon: UsersIcon },
  { to: '/admin/projects', label: 'Projects', description: 'Manage projects', icon: FolderIcon },
  { to: '/admin/blog', label: 'Blog Posts', description: 'Manage articles', icon: DocumentTextIcon },
  { to: '/admin/messages', label: 'Messages', description: 'View messages', icon: EnvelopeIcon },
  { to: '/admin/applications', label: 'Applications', description: 'View applications', icon: InboxStackIcon },
  { to: '/admin/team', label: 'Team', description: 'Manage team', icon: UserGroupIcon },
  { to: '/admin/media', label: 'Media', description: 'Media library', icon: PhotoIcon },
  { to: '/admin/tags', label: 'Tags', description: 'Manage tags', icon: TagIcon },
  { to: '/admin/roles', label: 'Roles', description: 'Manage roles', icon: ShieldCheckIcon },
  { to: '/admin/settings', label: 'Settings', description: 'Site settings', icon: CogIcon },
  { to: '/admin/activity-logs', label: 'Activity Logs', description: 'View logs', icon: ClipboardDocumentListIcon },
]

// Sections order for keyboard navigation
const sectionOrder = ['users', 'projects', 'posts', 'messages', 'applications', 'team', 'tags']

const filteredData = computed(() => {
  if (activeFilter.value === 'all') return results.value
  return { [activeFilter.value]: results.value[activeFilter.value] || [] }
})

const hasResults = computed(() => {
  return Object.values(filteredData.value).some(arr => arr?.length > 0)
})

const flatItems = computed(() => {
  const items = []
  for (const section of sectionOrder) {
    const arr = filteredData.value[section]
    if (arr?.length) {
      for (const item of arr) {
        items.push({ section, item })
      }
    }
  }
  return items
})

function getGlobalIndex(section, localIndex) {
  let offset = 0
  for (const s of sectionOrder) {
    if (s === section) return offset + localIndex
    offset += (filteredData.value[s]?.length || 0)
  }
  return localIndex
}

function setFilter(key) {
  activeFilter.value = key
  activeIndex.value = 0
  if (query.value.length >= 2) {
    loading.value = true
    clearTimeout(debounceTimer)
    debounceTimer = setTimeout(() => performSearch(), 100)
  }
}

function clearQuery() {
  query.value = ''
  results.value = {}
  totals.value = {}
  activeIndex.value = 0
  nextTick(() => inputRef.value?.focus())
}

function close() {
  emit('close')
  query.value = ''
  results.value = {}
  totals.value = {}
  activeFilter.value = 'all'
  activeIndex.value = 0
}

function onInput() {
  clearTimeout(debounceTimer)
  activeIndex.value = 0
  if (query.value.length < 2) {
    results.value = {}
    totals.value = {}
    loading.value = false
    return
  }
  loading.value = true
  debounceTimer = setTimeout(() => performSearch(), 250)
}

async function performSearch() {
  try {
    const params = { q: query.value }
    if (activeFilter.value !== 'all') {
      params.filter = activeFilter.value
    }
    const res = await adminApi.search(params)
    results.value = res.data.data || {}
    totals.value = res.data.totals || {}
    // Add total for 'all'
    totals.value.all = Object.values(totals.value).reduce((a, b) => a + b, 0)
  } catch {
    results.value = {}
    totals.value = {}
  } finally {
    loading.value = false
  }
}

function navigateTo(basePath, item) {
  close()
  router.push(basePath)
}

function highlight(text) {
  if (!text || query.value.length < 2) return text
  const escaped = query.value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
  const regex = new RegExp(`(${escaped})`, 'gi')
  return text.replace(regex, '<mark class="bg-brand-violet/20 dark:bg-brand-cyan/20 text-brand-violet dark:text-brand-cyan rounded px-0.5">$1</mark>')
}

function statusClass(status) {
  if (!status) return 'bg-gray-100 dark:bg-white/[0.06] text-gray-500 dark:text-gray-400'
  const s = status.toLowerCase()
  if (s === 'published' || s === 'completed' || s === 'accepted' || s === 'active') return 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400'
  if (s === 'draft' || s === 'pending' || s === 'in_progress') return 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400'
  if (s === 'rejected' || s === 'archived') return 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400'
  return 'bg-gray-100 dark:bg-white/[0.06] text-gray-500 dark:text-gray-400'
}

function handleKeydown(e) {
  if (e.key === 'Escape') {
    e.preventDefault()
    close()
  } else if (e.key === 'ArrowDown') {
    e.preventDefault()
    const max = flatItems.value.length || quickLinks.length
    activeIndex.value = (activeIndex.value + 1) % max
  } else if (e.key === 'ArrowUp') {
    e.preventDefault()
    const max = flatItems.value.length || quickLinks.length
    activeIndex.value = (activeIndex.value - 1 + max) % max
  } else if (e.key === 'Enter') {
    e.preventDefault()
    if (query.value.length >= 2 && flatItems.value.length) {
      const entry = flatItems.value[activeIndex.value]
      if (entry) {
        const routeMap = {
          users: '/admin/users',
          projects: '/admin/projects',
          posts: '/admin/blog',
          messages: '/admin/messages',
          applications: '/admin/applications',
          team: '/admin/team',
          tags: '/admin/tags',
        }
        navigateTo(routeMap[entry.section] || '/admin', entry.item)
      }
    } else if (query.value.length < 2 && quickLinks[activeIndex.value]) {
      close()
      router.push(quickLinks[activeIndex.value].to)
    }
  }
}

// Auto-focus input when opened
watch(() => props.open, (isOpen) => {
  if (isOpen) {
    nextTick(() => inputRef.value?.focus())
  }
})
</script>

<style scoped>
.search-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.search-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.search-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 99px;
}
:root.dark .search-scrollbar::-webkit-scrollbar-thumb,
.dark .search-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.06);
}
</style>
