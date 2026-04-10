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
      <div v-if="open" class="search-overlay fixed inset-0 z-[100] flex items-start justify-center" @mousedown.self="close">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-md" @mousedown="close" />

        <!-- Search Panel -->
        <div class="search-panel relative w-full max-w-2xl mx-4 mt-[10vh] sm:mt-[12vh] flex flex-col rounded-2xl overflow-hidden"
             @keydown="handleKeydown">

          <!-- ── Glass morphism container ── -->
          <div class="relative bg-white/95 dark:bg-dark-900/95 backdrop-blur-2xl shadow-2xl shadow-black/25 ring-1 ring-gray-200/80 dark:ring-white/[0.08] rounded-2xl overflow-hidden">

            <!-- Top gradient accent -->
            <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-brand-violet via-brand-cyan to-brand-violet opacity-60" />

            <!-- ── Search Input Area ── -->
            <div class="flex items-center gap-3 px-5 sm:px-6">
              <!-- Animated search icon -->
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
                :placeholder="activeFilter === 'all' ? 'Search projects, articles, pages...' : `Search ${activeFilter}...`"
                class="flex-1 h-14 sm:h-16 bg-transparent text-base sm:text-[16px] text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 outline-none font-medium"
                autocomplete="off"
                spellcheck="false"
                @input="onInput"
              />

              <!-- Clear button -->
              <button v-if="query.length" @click="clearQuery" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/[0.06] transition-colors group">
                <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>

              <!-- Keyboard shortcut -->
              <kbd class="hidden sm:inline-flex px-2 py-1 rounded-lg bg-gray-100 dark:bg-white/[0.06] text-[10px] font-sans text-gray-400 border border-gray-200/50 dark:border-white/[0.06] select-none">ESC</kbd>
            </div>

            <!-- ── Category Filters ── -->
            <div class="flex items-center gap-1 px-5 sm:px-6 pb-3 border-b border-gray-100 dark:border-white/[0.06]">
              <button
                v-for="filter in filters"
                :key="filter.key"
                @click="setFilter(filter.key)"
                class="search-filter-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-semibold tracking-wide uppercase transition-all duration-200"
                :class="activeFilter === filter.key
                  ? 'bg-brand-violet/10 dark:bg-brand-cyan/10 text-brand-violet dark:text-brand-cyan ring-1 ring-brand-violet/20 dark:ring-brand-cyan/20'
                  : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-white/[0.04]'"
              >
                <component :is="filter.icon" class="w-3 h-3" />
                {{ filter.label }}
                <span v-if="getFilterCount(filter.key) > 0"
                      class="ml-0.5 min-w-[18px] h-[18px] inline-flex items-center justify-center rounded-full text-[9px] font-bold"
                      :class="activeFilter === filter.key
                        ? 'bg-brand-violet/20 dark:bg-brand-cyan/20 text-brand-violet dark:text-brand-cyan'
                        : 'bg-gray-200/80 dark:bg-white/[0.08] text-gray-400 dark:text-gray-500'"
                >{{ getFilterCount(filter.key) }}</span>
              </button>
            </div>

            <!-- ── Results Area ── -->
            <div ref="resultsRef" class="max-h-[50vh] sm:max-h-[55vh] overflow-y-auto overscroll-contain scroll-smooth search-scrollbar">

              <!-- Loading skeleton -->
              <div v-if="loading && !results.length" class="p-3 space-y-2">
                <div v-for="n in 4" :key="n" class="flex items-center gap-3 px-3 py-3 rounded-xl animate-pulse">
                  <div class="w-10 h-10 rounded-xl bg-gray-200/80 dark:bg-white/[0.06]" />
                  <div class="flex-1 space-y-2">
                    <div class="h-3.5 w-2/3 rounded-md bg-gray-200/80 dark:bg-white/[0.06]" />
                    <div class="h-2.5 w-1/2 rounded-md bg-gray-200/60 dark:bg-white/[0.04]" />
                  </div>
                </div>
              </div>

              <!-- Search Results -->
              <div v-else-if="filteredResults.length" class="p-2 sm:p-3">

                <!-- Projects Section -->
                <div v-if="filteredProjects.length" class="mb-2">
                  <div class="flex items-center gap-2 px-3 pt-2 pb-2">
                    <svg class="w-3.5 h-3.5 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/></svg>
                    <span class="text-[10px] font-bold uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">Projects</span>
                    <span class="text-[9px] font-semibold text-gray-300 dark:text-gray-600">{{ filteredProjects.length }}</span>
                  </div>
                  <router-link
                    v-for="(item, i) in filteredProjects"
                    :key="'p' + item.id"
                    :to="`/projects/${item.slug}`"
                    :ref="el => setItemRef(getGlobalIndex('project', i), el)"
                    class="search-result-item group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 cursor-pointer"
                    :class="activeIndex === getGlobalIndex('project', i)
                      ? 'bg-brand-violet/8 dark:bg-brand-cyan/8 ring-1 ring-brand-violet/15 dark:ring-brand-cyan/15'
                      : 'hover:bg-gray-50 dark:hover:bg-white/[0.03]'"
                    @click="handleSelect(item)"
                    @mouseenter="activeIndex = getGlobalIndex('project', i)"
                  >
                    <!-- Thumbnail -->
                    <div class="relative w-10 h-10 rounded-xl overflow-hidden shrink-0 ring-1 ring-gray-200/60 dark:ring-white/[0.06]">
                      <img v-if="item.thumbnail" :src="item.thumbnail" :alt="item.title" class="w-full h-full object-cover" />
                      <div v-else class="w-full h-full bg-gradient-to-br from-brand-violet/10 to-brand-cyan/10 flex items-center justify-center">
                        <svg class="w-4.5 h-4.5 text-brand-violet/40 dark:text-brand-cyan/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/></svg>
                      </div>
                    </div>

                    <!-- Content -->
                    <div class="min-w-0 flex-1">
                      <p class="text-sm font-semibold text-gray-900 dark:text-white truncate" v-html="highlight(item.title)" />
                      <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5 line-clamp-1" v-html="highlight(item.description || '')" />
                      <!-- Tags -->
                      <div v-if="item.tags?.length" class="flex items-center gap-1 mt-1.5">
                        <span v-for="tag in item.tags.slice(0, 3)" :key="tag.id || tag"
                              class="inline-block px-1.5 py-0.5 rounded text-[9px] font-semibold bg-gray-100 dark:bg-white/[0.06] text-gray-500 dark:text-gray-400">
                          {{ tag.name || tag }}
                        </span>
                        <span v-if="item.tags.length > 3" class="text-[9px] text-gray-400">+{{ item.tags.length - 3 }}</span>
                      </div>
                    </div>

                    <!-- Arrow indicator -->
                    <div class="shrink-0 transition-all duration-150"
                         :class="activeIndex === getGlobalIndex('project', i) ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-1'">
                      <svg class="w-4 h-4 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                      </svg>
                    </div>
                  </router-link>
                </div>

                <!-- Blog Posts Section -->
                <div v-if="filteredPosts.length">
                  <div class="flex items-center gap-2 px-3 pt-2 pb-2">
                    <svg class="w-3.5 h-3.5 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"/></svg>
                    <span class="text-[10px] font-bold uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">Articles</span>
                    <span class="text-[9px] font-semibold text-gray-300 dark:text-gray-600">{{ filteredPosts.length }}</span>
                  </div>
                  <router-link
                    v-for="(item, i) in filteredPosts"
                    :key="'b' + item.id"
                    :to="`/blog/${item.slug}`"
                    :ref="el => setItemRef(getGlobalIndex('post', i), el)"
                    class="search-result-item group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 cursor-pointer"
                    :class="activeIndex === getGlobalIndex('post', i)
                      ? 'bg-brand-violet/8 dark:bg-brand-cyan/8 ring-1 ring-brand-violet/15 dark:ring-brand-cyan/15'
                      : 'hover:bg-gray-50 dark:hover:bg-white/[0.03]'"
                    @click="handleSelect(item)"
                    @mouseenter="activeIndex = getGlobalIndex('post', i)"
                  >
                    <div class="relative w-10 h-10 rounded-xl overflow-hidden shrink-0 ring-1 ring-gray-200/60 dark:ring-white/[0.06]">
                      <img v-if="item.featured_image || item.thumbnail" :src="item.featured_image || item.thumbnail" :alt="item.title" class="w-full h-full object-cover" />
                      <div v-else class="w-full h-full bg-gradient-to-br from-brand-violet/10 to-brand-cyan/10 flex items-center justify-center">
                        <svg class="w-4.5 h-4.5 text-brand-violet/40 dark:text-brand-cyan/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"/></svg>
                      </div>
                    </div>

                    <div class="min-w-0 flex-1">
                      <p class="text-sm font-semibold text-gray-900 dark:text-white truncate" v-html="highlight(item.title)" />
                      <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5 line-clamp-1" v-html="highlight(item.excerpt || item.description || '')" />
                      <!-- Meta info -->
                      <div class="flex items-center gap-2 mt-1.5 text-[10px] text-gray-400 dark:text-gray-500">
                        <span v-if="item.category?.name" class="inline-flex items-center gap-1">
                          <span class="w-1 h-1 rounded-full bg-brand-violet dark:bg-brand-cyan" />
                          {{ item.category.name }}
                        </span>
                        <span v-if="item.published_at || item.created_at">{{ formatDate(item.published_at || item.created_at) }}</span>
                      </div>
                    </div>

                    <div class="shrink-0 transition-all duration-150"
                         :class="activeIndex === getGlobalIndex('post', i) ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-1'">
                      <svg class="w-4 h-4 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                      </svg>
                    </div>
                  </router-link>
                </div>

                <!-- Pages Section (shown in filtered results when matching) -->
                <div v-if="filteredPages.length">
                  <div class="flex items-center gap-2 px-3 pt-2 pb-2">
                    <svg class="w-3.5 h-3.5 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                    <span class="text-[10px] font-bold uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">Pages</span>
                  </div>
                  <router-link
                    v-for="(page, i) in filteredPages"
                    :key="'pg' + i"
                    :to="page.to"
                    :ref="el => setItemRef(getGlobalIndex('page', i), el)"
                    class="search-result-item group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 cursor-pointer"
                    :class="activeIndex === getGlobalIndex('page', i)
                      ? 'bg-brand-violet/8 dark:bg-brand-cyan/8 ring-1 ring-brand-violet/15 dark:ring-brand-cyan/15'
                      : 'hover:bg-gray-50 dark:hover:bg-white/[0.03]'"
                    @click="handleSelectPage(page)"
                    @mouseenter="activeIndex = getGlobalIndex('page', i)"
                  >
                    <div class="w-10 h-10 rounded-xl bg-gray-100/80 dark:bg-white/[0.04] flex items-center justify-center shrink-0">
                      <component :is="page.icon" class="w-4.5 h-4.5 text-gray-400 dark:text-gray-500" />
                    </div>
                    <div class="min-w-0 flex-1">
                      <p class="text-sm font-semibold text-gray-900 dark:text-white" v-html="highlight(page.label)" />
                      <p class="text-xs text-gray-400 dark:text-gray-500">{{ page.description }}</p>
                    </div>
                    <div class="shrink-0 transition-all duration-150"
                         :class="activeIndex === getGlobalIndex('page', i) ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-1'">
                      <svg class="w-4 h-4 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                      </svg>
                    </div>
                  </router-link>
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

              <!-- Initial State: Recent Searches + Quick Links -->
              <div v-else-if="query.length < 2 && !loading" class="p-2 sm:p-3">

                <!-- Recent Searches -->
                <div v-if="recentSearches.length" class="mb-3">
                  <div class="flex items-center justify-between px-3 pt-1 pb-2">
                    <span class="text-[10px] font-bold uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">Recent</span>
                    <button @click="clearRecent" class="text-[10px] font-medium text-gray-400 hover:text-red-400 transition-colors">Clear</button>
                  </div>
                  <button
                    v-for="(recent, i) in recentSearches"
                    :key="'r' + i"
                    class="search-result-item group flex items-center gap-3 w-full px-3 py-2.5 rounded-xl transition-all duration-150 text-left"
                    :class="activeIndex === i
                      ? 'bg-brand-violet/8 dark:bg-brand-cyan/8 ring-1 ring-brand-violet/15 dark:ring-brand-cyan/15'
                      : 'hover:bg-gray-50 dark:hover:bg-white/[0.03]'"
                    @click="useRecent(recent)"
                    @mouseenter="activeIndex = i"
                  >
                    <div class="w-8 h-8 rounded-lg bg-gray-100/80 dark:bg-white/[0.04] flex items-center justify-center shrink-0">
                      <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-sm text-gray-600 dark:text-gray-300 truncate">{{ recent }}</span>
                    <svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600 shrink-0 ml-auto opacity-0 group-hover:opacity-100 transition-opacity -rotate-45" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/>
                    </svg>
                  </button>
                </div>

                <!-- Quick Links -->
                <div>
                  <div class="flex items-center gap-2 px-3 pt-1 pb-2">
                    <span class="text-[10px] font-bold uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">Quick Links</span>
                  </div>
                  <div class="grid grid-cols-2 gap-1.5">
                    <router-link
                      v-for="(link, i) in quickLinks"
                      :key="'q' + i"
                      :to="link.to"
                      class="search-quick-link group flex items-center gap-2.5 px-3 py-3 rounded-xl transition-all duration-150"
                      :class="activeIndex === getQuickLinkIndex(i)
                        ? 'bg-brand-violet/8 dark:bg-brand-cyan/8 ring-1 ring-brand-violet/15 dark:ring-brand-cyan/15'
                        : 'hover:bg-gray-50 dark:hover:bg-white/[0.03]'"
                      @click="close"
                      @mouseenter="activeIndex = getQuickLinkIndex(i)"
                    >
                      <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-colors duration-150"
                           :class="activeIndex === getQuickLinkIndex(i)
                             ? 'bg-brand-violet/15 dark:bg-brand-cyan/15'
                             : 'bg-gray-100/80 dark:bg-white/[0.04]'">
                        <component :is="link.icon" class="w-4 h-4 transition-colors duration-150"
                                   :class="activeIndex === getQuickLinkIndex(i)
                                     ? 'text-brand-violet dark:text-brand-cyan'
                                     : 'text-gray-400 dark:text-gray-500'" />
                      </div>
                      <div class="min-w-0 flex-1">
                        <p class="text-[13px] font-semibold text-gray-700 dark:text-gray-200 truncate">{{ link.label }}</p>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 truncate">{{ link.description }}</p>
                      </div>
                    </router-link>
                  </div>
                </div>
              </div>
            </div>

            <!-- ── Footer ── -->
            <div class="flex items-center justify-between px-5 sm:px-6 py-3 border-t border-gray-100 dark:border-white/[0.06] bg-gray-50/50 dark:bg-white/[0.015]">
              <div class="flex items-center gap-4 text-[11px] text-gray-400 dark:text-gray-500">
                <span class="inline-flex items-center gap-1.5">
                  <kbd class="search-kbd">↑↓</kbd>
                  <span class="hidden sm:inline">Navigate</span>
                </span>
                <span class="inline-flex items-center gap-1.5">
                  <kbd class="search-kbd">↵</kbd>
                  <span class="hidden sm:inline">Open</span>
                </span>
                <span class="inline-flex items-center gap-1.5">
                  <kbd class="search-kbd">Tab</kbd>
                  <span class="hidden sm:inline">Filter</span>
                </span>
                <span class="inline-flex items-center gap-1.5">
                  <kbd class="search-kbd">Esc</kbd>
                  <span class="hidden sm:inline">Close</span>
                </span>
              </div>
              <!-- Branding -->
              <div class="flex items-center gap-1.5 text-[10px] text-gray-300 dark:text-gray-600">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <span class="font-semibold tracking-wide">Kalapak Search</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted, h } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { publicApi } from '@/services/api'

const props = defineProps({ open: Boolean })
const emit = defineEmits(['close'])

const router = useRouter()
const route = useRoute()

// ── Refs ──
const inputRef = ref(null)
const resultsRef = ref(null)
const itemRefs = {}

// ── State ──
const query = ref('')
const loading = ref(false)
const activeIndex = ref(0)
const activeFilter = ref('all')
const projectResults = ref([])
const postResults = ref([])
const recentSearches = ref([])
let debounceTimer = null

const STORAGE_KEY = 'kalapak_recent_searches'
const MAX_RECENT = 5

// ── Filters ──
const IconAll = () => h('svg', { fill: 'none', stroke: 'currentColor', 'stroke-width': '2', viewBox: '0 0 24 24', class: 'w-3 h-3' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z' })
])
const IconProject = () => h('svg', { fill: 'none', stroke: 'currentColor', 'stroke-width': '2', viewBox: '0 0 24 24', class: 'w-3 h-3' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z' })
])
const IconArticle = () => h('svg', { fill: 'none', stroke: 'currentColor', 'stroke-width': '2', viewBox: '0 0 24 24', class: 'w-3 h-3' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z' })
])
const IconPage = () => h('svg', { fill: 'none', stroke: 'currentColor', 'stroke-width': '2', viewBox: '0 0 24 24', class: 'w-3 h-3' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25' })
])

const filters = [
  { key: 'all', label: 'All', icon: IconAll },
  { key: 'projects', label: 'Projects', icon: IconProject },
  { key: 'articles', label: 'Articles', icon: IconArticle },
  { key: 'pages', label: 'Pages', icon: IconPage },
]

// ── Quick Links / Pages ──
const IconHome = () => h('svg', { fill: 'none', stroke: 'currentColor', 'stroke-width': '1.8', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25' })
])
const IconAbout = () => h('svg', { fill: 'none', stroke: 'currentColor', 'stroke-width': '1.8', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z' })
])
const IconPortfolio = () => h('svg', { fill: 'none', stroke: 'currentColor', 'stroke-width': '1.8', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z' })
])
const IconBlog = () => h('svg', { fill: 'none', stroke: 'currentColor', 'stroke-width': '1.8', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z' })
])
const IconCareers = () => h('svg', { fill: 'none', stroke: 'currentColor', 'stroke-width': '1.8', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z' })
])
const IconContact = () => h('svg', { fill: 'none', stroke: 'currentColor', 'stroke-width': '1.8', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75' })
])

const allPages = [
  { to: '/', label: 'Home', description: 'Landing page', icon: IconHome },
  { to: '/about', label: 'About Us', description: 'Meet the team', icon: IconAbout },
  { to: '/projects', label: 'Portfolio', description: 'Our projects', icon: IconPortfolio },
  { to: '/blog', label: 'Blog & Insights', description: 'Articles & tutorials', icon: IconBlog },
  { to: '/join', label: 'Careers', description: 'Join our team', icon: IconCareers },
  { to: '/contact', label: 'Contact', description: 'Get in touch', icon: IconContact },
]

const quickLinks = allPages

// ── Computed ──
const results = computed(() => [...projectResults.value, ...postResults.value])

const filteredProjects = computed(() => {
  if (activeFilter.value === 'articles' || activeFilter.value === 'pages') return []
  return projectResults.value
})

const filteredPosts = computed(() => {
  if (activeFilter.value === 'projects' || activeFilter.value === 'pages') return []
  return postResults.value
})

const filteredPages = computed(() => {
  if (activeFilter.value === 'projects' || activeFilter.value === 'articles') return []
  if (query.value.length < 2) return []
  const q = query.value.toLowerCase()
  return allPages.filter(p => p.label.toLowerCase().includes(q) || p.description.toLowerCase().includes(q))
})

const filteredResults = computed(() => [...filteredProjects.value, ...filteredPosts.value, ...filteredPages.value])

const totalItems = computed(() => {
  if (query.value.length < 2) {
    return recentSearches.value.length + quickLinks.length
  }
  return filteredResults.value.length
})

// ── Methods ──
function getFilterCount(key) {
  if (query.value.length < 2) return 0
  if (key === 'all') return results.value.length + filteredPages.value.length
  if (key === 'projects') return projectResults.value.length
  if (key === 'articles') return postResults.value.length
  if (key === 'pages') return allPages.filter(p => {
    const q = query.value.toLowerCase()
    return p.label.toLowerCase().includes(q) || p.description.toLowerCase().includes(q)
  }).length
  return 0
}

function getGlobalIndex(type, localIndex) {
  if (query.value.length < 2) return localIndex
  let offset = 0
  if (type === 'project') return localIndex
  if (type === 'post') { offset = filteredProjects.value.length; return offset + localIndex }
  if (type === 'page') { offset = filteredProjects.value.length + filteredPosts.value.length; return offset + localIndex }
  return localIndex
}

function getQuickLinkIndex(i) {
  return recentSearches.value.length + i
}

function setItemRef(index, el) {
  if (el) itemRefs[index] = el.$el || el
}

function setFilter(key) {
  activeFilter.value = key
  activeIndex.value = 0
}

function clearQuery() {
  query.value = ''
  projectResults.value = []
  postResults.value = []
  activeIndex.value = 0
  nextTick(() => inputRef.value?.focus())
}

function close() {
  emit('close')
  query.value = ''
  projectResults.value = []
  postResults.value = []
  activeFilter.value = 'all'
  activeIndex.value = 0
}

function onInput() {
  clearTimeout(debounceTimer)
  activeIndex.value = 0
  if (query.value.length < 2) {
    projectResults.value = []
    postResults.value = []
    loading.value = false
    return
  }
  loading.value = true
  debounceTimer = setTimeout(() => performSearch(), 250)
}

async function performSearch() {
  try {
    const q = query.value
    const [projectsRes, postsRes] = await Promise.all([
      publicApi.getProjects({ search: q, per_page: 6 }),
      publicApi.getBlogPosts({ search: q, per_page: 6 }),
    ])
    projectResults.value = projectsRes.data.data || []
    postResults.value = postsRes.data.data || []
  } catch {
    projectResults.value = []
    postResults.value = []
  } finally {
    loading.value = false
  }
}

function handleSelect(item) {
  saveRecent(query.value)
  close()
}

function handleSelectPage(page) {
  saveRecent(query.value)
  close()
}

function useRecent(term) {
  query.value = term
  activeIndex.value = 0
  loading.value = true
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => performSearch(), 100)
}

// Highlight matching text
function highlight(text) {
  if (!text || query.value.length < 2) return text
  const escaped = query.value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
  const regex = new RegExp(`(${escaped})`, 'gi')
  return text.replace(regex, '<mark class="search-highlight">$1</mark>')
}

function formatDate(dateStr) {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

// ── Recent Searches (localStorage) ──
function loadRecent() {
  try {
    const stored = localStorage.getItem(STORAGE_KEY)
    recentSearches.value = stored ? JSON.parse(stored) : []
  } catch { recentSearches.value = [] }
}

function saveRecent(term) {
  if (!term || term.length < 2) return
  const current = [...recentSearches.value]
  const filtered = current.filter(s => s.toLowerCase() !== term.toLowerCase())
  filtered.unshift(term)
  recentSearches.value = filtered.slice(0, MAX_RECENT)
  localStorage.setItem(STORAGE_KEY, JSON.stringify(recentSearches.value))
}

function clearRecent() {
  recentSearches.value = []
  localStorage.removeItem(STORAGE_KEY)
}

// ── Keyboard Navigation ──
function handleKeydown(e) {
  const max = totalItems.value

  if (e.key === 'ArrowDown') {
    e.preventDefault()
    activeIndex.value = (activeIndex.value + 1) % max
    scrollToActive()
  } else if (e.key === 'ArrowUp') {
    e.preventDefault()
    activeIndex.value = (activeIndex.value - 1 + max) % max
    scrollToActive()
  } else if (e.key === 'Enter') {
    e.preventDefault()
    selectActive()
  } else if (e.key === 'Tab') {
    e.preventDefault()
    cycleFilter(e.shiftKey ? -1 : 1)
  } else if (e.key === 'Escape') {
    e.preventDefault()
    close()
  }
}

function scrollToActive() {
  nextTick(() => {
    const el = itemRefs[activeIndex.value]
    if (el && resultsRef.value) {
      el.scrollIntoView?.({ block: 'nearest', behavior: 'smooth' })
    }
  })
}

function selectActive() {
  const idx = activeIndex.value

  // Initial state: recent searches + quick links
  if (query.value.length < 2) {
    if (idx < recentSearches.value.length) {
      useRecent(recentSearches.value[idx])
      return
    }
    const linkIdx = idx - recentSearches.value.length
    if (linkIdx >= 0 && linkIdx < quickLinks.length) {
      router.push(quickLinks[linkIdx].to)
      close()
      return
    }
    return
  }

  // Results state
  let offset = 0
  if (idx < offset + filteredProjects.value.length) {
    const item = filteredProjects.value[idx - offset]
    saveRecent(query.value)
    router.push(`/projects/${item.slug}`)
    close()
    return
  }
  offset += filteredProjects.value.length

  if (idx < offset + filteredPosts.value.length) {
    const item = filteredPosts.value[idx - offset]
    saveRecent(query.value)
    router.push(`/blog/${item.slug}`)
    close()
    return
  }
  offset += filteredPosts.value.length

  if (idx < offset + filteredPages.value.length) {
    const page = filteredPages.value[idx - offset]
    saveRecent(query.value)
    router.push(page.to)
    close()
    return
  }
}

function cycleFilter(dir) {
  const keys = filters.map(f => f.key)
  const currentIdx = keys.indexOf(activeFilter.value)
  const nextIdx = (currentIdx + dir + keys.length) % keys.length
  activeFilter.value = keys[nextIdx]
  activeIndex.value = 0
}

// ── Watchers ──
watch(() => props.open, async (isOpen) => {
  if (isOpen) {
    loadRecent()
    activeIndex.value = 0
    activeFilter.value = 'all'
    query.value = ''
    projectResults.value = []
    postResults.value = []
    await nextTick()
    inputRef.value?.focus()
  }
})

// Close on route change
watch(() => route.fullPath, () => {
  if (props.open) close()
})

onMounted(() => loadRecent())
</script>

<style scoped>
/* ── Panel animation ── */
.search-panel {
  animation: searchPanelIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes searchPanelIn {
  from {
    opacity: 0;
    transform: scale(0.96) translateY(-12px);
    filter: blur(4px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
    filter: blur(0);
  }
}

/* ── Overlay ── */
.search-overlay {
  -webkit-tap-highlight-color: transparent;
}

/* ── Scrollbar ── */
.search-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.search-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.search-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 3px;
}
:root.dark .search-scrollbar::-webkit-scrollbar-thumb,
.dark .search-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.08);
}

/* ── Keyboard shortcuts ── */
.search-kbd {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 22px;
  padding: 1px 6px;
  border-radius: 5px;
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
  font-size: 10px;
  font-weight: 600;
  line-height: 18px;
  background: rgba(0, 0, 0, 0.04);
  border: 1px solid rgba(0, 0, 0, 0.06);
  color: inherit;
  box-shadow: 0 1px 0 rgba(0, 0, 0, 0.04);
}

:root.dark .search-kbd,
.dark .search-kbd {
  background: rgba(255, 255, 255, 0.04);
  border-color: rgba(255, 255, 255, 0.06);
  box-shadow: 0 1px 0 rgba(0, 0, 0, 0.2);
}

/* ── Search highlight ── */
:deep(.search-highlight) {
  background: rgba(123, 47, 255, 0.12);
  color: #7b2fff;
  border-radius: 2px;
  padding: 0 1px;
  font-weight: inherit;
}

:root.dark :deep(.search-highlight),
.dark :deep(.search-highlight) {
  background: rgba(0, 212, 255, 0.12);
  color: #00d4ff;
}

/* ── Result item ── */
.search-result-item {
  position: relative;
}

/* ── Filter button focus ── */
.search-filter-btn:focus-visible {
  outline: 2px solid rgba(123, 47, 255, 0.5);
  outline-offset: -2px;
  border-radius: 8px;
}

/* ── Loading spin ── */
@keyframes spin {
  to { transform: rotate(360deg); }
}
.animate-spin {
  animation: spin 0.7s linear infinite;
}
</style>
