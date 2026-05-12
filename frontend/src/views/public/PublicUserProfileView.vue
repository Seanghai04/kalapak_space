<template>
  <div class="public-profile-page min-h-screen pb-24">
    <!-- Loading -->
    <div v-if="loading" class="flex flex-col items-center justify-center py-32 gap-4">
      <div class="relative w-14 h-14">
        <div class="absolute inset-0 border-[3px] border-gray-200 dark:border-dark-600 border-t-brand-violet dark:border-t-brand-cyan rounded-full animate-spin" />
        <div class="absolute inset-0 flex items-center justify-center">
          <div class="w-6 h-6 border-[3px] border-gray-200 dark:border-dark-600 border-b-brand-cyan dark:border-b-brand-violet rounded-full animate-spin" style="animation-direction: reverse" />
        </div>
      </div>
      <p class="text-sm text-gray-500 dark:text-gray-400">Loading profile…</p>
    </div>

    <EmptyState
      v-else-if="notFound"
      title="Profile not found"
      message="This username does not exist or the account is not active."
    />

    <EmptyState
      v-else-if="loadFailed"
      title="Could not load profile"
      message="The server had a problem loading this profile. Refresh the page or try again in a moment."
    />

    <template v-else-if="profile">
      <div :key="profile.id">
      <!-- Hero -->
      <section class="relative overflow-hidden pt-20 pb-16 sm:pt-24 sm:pb-20">
        <div class="absolute inset-0 bg-gradient-to-br from-dark-900 via-dark-800 to-dark-900 dark:from-dark-950 dark:via-dark-900 dark:to-dark-950" />
        <div class="absolute top-0 right-1/4 w-[500px] h-[500px] bg-brand-violet/10 rounded-full blur-[120px]" />
        <div class="absolute bottom-0 left-1/4 w-[400px] h-[400px] bg-brand-cyan/8 rounded-full blur-[100px]" />

        <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-start gap-8">
          <div class="flex-shrink-0">
            <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl bg-gradient-to-br from-brand-violet to-brand-cyan p-[3px] shadow-lg shadow-brand-violet/25">
              <div class="w-full h-full rounded-[13px] bg-dark-800 overflow-hidden">
                <img v-if="profile.avatar" :src="profile.avatar" :alt="profile.name" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full flex items-center justify-center text-4xl font-bold text-white bg-gradient-to-br from-brand-violet/30 to-brand-cyan/30">
                  {{ profile.name?.charAt(0)?.toUpperCase() }}
                </div>
              </div>
            </div>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-3 mb-2">
              <h1 class="text-2xl sm:text-4xl font-sans font-bold text-white truncate">{{ profile.name }}</h1>
              <span
                v-if="profile.role?.display_name"
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium tracking-wider bg-white/10 text-brand-cyan border border-white/20"
              >
                {{ profile.role.display_name }}
              </span>
            </div>
            <p class="text-brand-cyan/90 text-lg font-medium mb-3">@{{ profile.username }}</p>
            <p v-if="profile.bio" class="text-gray-300 text-sm sm:text-base leading-relaxed max-w-2xl mb-4">{{ profile.bio }}</p>
            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-400">
              <span v-if="profile.joined_at" class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                Joined {{ formatJoined(profile.joined_at) }}
              </span>
              <a
                v-if="profile.github_url"
                :href="profile.github_url"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-1.5 text-gray-300 hover:text-brand-cyan transition-colors"
              >
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                GitHub
              </a>
              <a
                v-if="profile.linkedin_url"
                :href="profile.linkedin_url"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-1.5 text-gray-300 hover:text-brand-cyan transition-colors"
              >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                LinkedIn
              </a>
            </div>
          </div>
        </div>
      </section>

      <!-- Content -->
      <section class="max-w-7xl mx-auto px-4 sm:px-6 -mt-8 relative z-20">
        <div class="flex flex-wrap gap-2 p-1.5 rounded-xl bg-white/80 dark:bg-dark-800/80 backdrop-blur-md border border-gray-200 dark:border-dark-600 w-fit shadow-lg">
          <button
            type="button"
            @click="activeTab = 'posts'"
            :class="activeTab === 'posts' ? 'tab-active' : 'tab-idle'"
            class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all"
          >
            Blog posts
            <span class="ml-1.5 text-xs opacity-70">({{ postsMeta.total ?? posts.length }})</span>
          </button>
          <button
            type="button"
            @click="activeTab = 'projects'"
            :class="activeTab === 'projects' ? 'tab-active' : 'tab-idle'"
            class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all"
          >
            Projects
            <span class="ml-1.5 text-xs opacity-70">({{ projectsMeta.total ?? projects.length }})</span>
          </button>
        </div>

        <!-- Posts -->
        <div v-show="activeTab === 'posts'" class="mt-10">
          <div class="flex flex-wrap gap-2 mb-8">
            <button
              type="button"
              class="px-3 py-1.5 rounded-full text-xs font-medium border transition-colors"
              :class="postSeriesFilter === 'all' ? 'border-brand-violet bg-brand-violet/10 text-brand-violet dark:border-brand-cyan dark:bg-brand-cyan/10 dark:text-brand-cyan' : 'border-gray-200 dark:border-dark-600 text-gray-600 dark:text-gray-400 hover:border-brand-violet/40'"
              @click="postSeriesFilter = 'all'"
            >
              All
            </button>
            <button
              type="button"
              class="px-3 py-1.5 rounded-full text-xs font-medium border transition-colors"
              :class="postSeriesFilter === 'uncategorized' ? 'border-brand-violet bg-brand-violet/10 text-brand-violet dark:border-brand-cyan dark:bg-brand-cyan/10 dark:text-brand-cyan' : 'border-gray-200 dark:border-dark-600 text-gray-600 dark:text-gray-400 hover:border-brand-violet/40'"
              @click="postSeriesFilter = 'uncategorized'"
            >
              Uncategorized
            </button>
            <button
              v-for="s in seriesChipsList"
              :key="s.slug"
              type="button"
              class="px-3 py-1.5 rounded-full text-xs font-medium border transition-colors max-w-[200px] truncate"
              :class="postSeriesFilter === s.slug ? 'border-brand-violet bg-brand-violet/10 text-brand-violet dark:border-brand-cyan dark:bg-brand-cyan/10 dark:text-brand-cyan' : 'border-gray-200 dark:border-dark-600 text-gray-600 dark:text-gray-400 hover:border-brand-violet/40'"
              :title="s.name"
              @click="postSeriesFilter = s.slug"
            >
              {{ s.name }}
            </button>
          </div>

          <div v-if="postsLoading" class="flex justify-center py-16">
            <div class="w-10 h-10 border-4 border-brand-violet/30 border-t-brand-violet rounded-full animate-spin" />
          </div>

          <template v-else-if="posts.length">
            <template v-if="postSeriesFilter === 'all'">
              <div v-for="block in postSeriesBlocks.groups" :key="block.series.slug" class="mb-12 last:mb-0">
                <div class="flex flex-wrap items-center gap-3 mb-5">
                  <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-brand-violet/15 text-brand-violet dark:bg-brand-cyan/15 dark:text-brand-cyan">Series</span>
                  <router-link
                    :to="{ name: 'public-series', params: { username: profile.username, slug: block.series.slug } }"
                    class="text-lg font-bold text-gray-900 dark:text-white hover:text-brand-violet dark:hover:text-brand-cyan transition-colors"
                  >
                    {{ block.series.name }}
                  </router-link>
                  <span class="text-sm text-gray-500 dark:text-gray-400">({{ block.posts.length }})</span>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-7">
                  <router-link
                    v-for="post in block.posts"
                    :key="post.id"
                    :to="`/blog/${post.slug}`"
                    class="group flex flex-col rounded-2xl border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm overflow-hidden hover:border-brand-violet/30 dark:hover:border-brand-cyan/30 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl dark:hover:shadow-glow/10"
                  >
                    <div class="relative aspect-[16/10] overflow-hidden bg-gray-100 dark:bg-dark-700">
                      <img v-if="post.cover_image" :src="post.cover_image" :alt="post.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                      <div v-else class="w-full h-full bg-gradient-brand opacity-20" />
                    </div>
                    <div class="flex flex-col flex-1 p-5">
                      <p class="text-xs text-gray-400 dark:text-gray-500 mb-2">{{ formatDate(post.published_at) }}</p>
                      <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-brand-violet dark:group-hover:text-brand-cyan transition-colors">{{ post.title }}</h3>
                      <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 flex-1">{{ post.excerpt }}</p>
                      <span v-if="post.series" class="mt-3 inline-flex self-start text-[10px] font-semibold px-2 py-0.5 rounded-full bg-brand-violet/10 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan">{{ post.series.name }}</span>
                    </div>
                  </router-link>
                </div>
              </div>
              <div v-if="postSeriesBlocks.uncategorized.length" class="mt-12 pt-10 border-t border-gray-200 dark:border-dark-600">
                <div class="flex flex-wrap items-center gap-3 mb-5">
                  <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-gray-200/80 text-gray-700 dark:bg-dark-600 dark:text-gray-300">Uncategorized</span>
                  <span class="text-sm text-gray-500 dark:text-gray-400">({{ postSeriesBlocks.uncategorized.length }})</span>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-7">
                  <router-link
                    v-for="post in postSeriesBlocks.uncategorized"
                    :key="post.id"
                    :to="`/blog/${post.slug}`"
                    class="group flex flex-col rounded-2xl border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm overflow-hidden hover:border-brand-violet/30 dark:hover:border-brand-cyan/30 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                  >
                    <div class="relative aspect-[16/10] overflow-hidden bg-gray-100 dark:bg-dark-700">
                      <img v-if="post.cover_image" :src="post.cover_image" :alt="post.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                      <div v-else class="w-full h-full bg-gradient-brand opacity-20" />
                    </div>
                    <div class="flex flex-col flex-1 p-5">
                      <p class="text-xs text-gray-400 dark:text-gray-500 mb-2">{{ formatDate(post.published_at) }}</p>
                      <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-brand-violet dark:group-hover:text-brand-cyan transition-colors">{{ post.title }}</h3>
                      <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 flex-1">{{ post.excerpt }}</p>
                    </div>
                  </router-link>
                </div>
              </div>
            </template>
            <div v-else class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-7">
              <router-link
                v-for="post in posts"
                :key="post.id"
                :to="`/blog/${post.slug}`"
                class="group flex flex-col rounded-2xl border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm overflow-hidden hover:border-brand-violet/30 dark:hover:border-brand-cyan/30 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl dark:hover:shadow-glow/10"
              >
                <div class="relative aspect-[16/10] overflow-hidden bg-gray-100 dark:bg-dark-700">
                  <img v-if="post.cover_image" :src="post.cover_image" :alt="post.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                  <div v-else class="w-full h-full bg-gradient-brand opacity-20" />
                </div>
                <div class="flex flex-col flex-1 p-5">
                  <p class="text-xs text-gray-400 dark:text-gray-500 mb-2">{{ formatDate(post.published_at) }}</p>
                  <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-brand-violet dark:group-hover:text-brand-cyan transition-colors">{{ post.title }}</h3>
                  <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 flex-1">{{ post.excerpt }}</p>
                  <span v-if="post.series" class="mt-3 inline-flex self-start text-[10px] font-semibold px-2 py-0.5 rounded-full bg-brand-violet/10 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan">{{ post.series.name }}</span>
                </div>
              </router-link>
            </div>
          </template>
          <EmptyState v-else title="No published posts yet" message="When this member publishes articles, they will appear here." />
          <div v-if="postsMeta.last_page > 1" class="mt-10">
            <Pagination
              :current-page="postsMeta.current_page"
              :last-page="postsMeta.last_page"
              @page-change="fetchPosts"
            />
          </div>
        </div>

        <!-- Projects -->
        <div v-show="activeTab === 'projects'" class="mt-10">
          <div class="flex flex-wrap gap-2 mb-8">
            <button
              type="button"
              class="px-3 py-1.5 rounded-full text-xs font-medium border transition-colors"
              :class="projectCollectionFilter === 'all' ? 'border-brand-violet bg-brand-violet/10 text-brand-violet dark:border-brand-cyan dark:bg-brand-cyan/10 dark:text-brand-cyan' : 'border-gray-200 dark:border-dark-600 text-gray-600 dark:text-gray-400 hover:border-brand-violet/40'"
              @click="projectCollectionFilter = 'all'"
            >
              All
            </button>
            <button
              type="button"
              class="px-3 py-1.5 rounded-full text-xs font-medium border transition-colors"
              :class="projectCollectionFilter === 'uncategorized' ? 'border-brand-violet bg-brand-violet/10 text-brand-violet dark:border-brand-cyan dark:bg-brand-cyan/10 dark:text-brand-cyan' : 'border-gray-200 dark:border-dark-600 text-gray-600 dark:text-gray-400 hover:border-brand-violet/40'"
              @click="projectCollectionFilter = 'uncategorized'"
            >
              Uncategorized
            </button>
            <button
              v-for="c in collectionChipsList"
              :key="c.slug"
              type="button"
              class="px-3 py-1.5 rounded-full text-xs font-medium border transition-colors max-w-[200px] truncate"
              :class="projectCollectionFilter === c.slug ? 'border-brand-violet bg-brand-violet/10 text-brand-violet dark:border-brand-cyan dark:bg-brand-cyan/10 dark:text-brand-cyan' : 'border-gray-200 dark:border-dark-600 text-gray-600 dark:text-gray-400 hover:border-brand-violet/40'"
              :title="c.name"
              @click="projectCollectionFilter = c.slug"
            >
              {{ c.name }}
            </button>
          </div>

          <div v-if="projectsLoading" class="flex justify-center py-16">
            <div class="w-10 h-10 border-4 border-brand-violet/30 border-t-brand-violet rounded-full animate-spin" />
          </div>

          <template v-else-if="projects.length">
            <template v-if="projectCollectionFilter === 'all'">
              <div v-for="block in projectCollectionBlocks.groups" :key="block.collection.slug" class="mb-12 last:mb-0">
                <div class="flex flex-wrap items-center gap-3 mb-5">
                  <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-brand-cyan/15 text-brand-cyan dark:bg-brand-violet/15 dark:text-brand-violet">Collection</span>
                  <router-link
                    :to="{ name: 'public-collection', params: { username: profile.username, slug: block.collection.slug } }"
                    class="text-lg font-bold text-gray-900 dark:text-white hover:text-brand-violet dark:hover:text-brand-cyan transition-colors"
                  >
                    {{ block.collection.name }}
                  </router-link>
                  <span class="text-sm text-gray-500 dark:text-gray-400">({{ block.projects.length }})</span>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-7">
                  <router-link
                    v-for="project in block.projects"
                    :key="project.id"
                    :to="`/projects/${project.slug}`"
                    class="group flex flex-col rounded-2xl border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm overflow-hidden hover:border-brand-violet/30 dark:hover:border-brand-cyan/30 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                  >
                    <div class="relative aspect-video overflow-hidden bg-gray-100 dark:bg-dark-700">
                      <img v-if="project.cover_image" :src="project.cover_image" :alt="project.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                      <div v-else class="w-full h-full bg-gradient-brand opacity-25" />
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                      <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-brand-violet dark:group-hover:text-brand-cyan transition-colors">{{ project.title }}</h3>
                      <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 flex-1">{{ project.description }}</p>
                      <span v-if="project.collection" class="mt-3 inline-flex self-start text-[10px] font-semibold px-2 py-0.5 rounded-full bg-brand-cyan/10 text-brand-cyan dark:bg-brand-violet/10 dark:text-brand-violet">{{ project.collection.name }}</span>
                      <div class="mt-3 flex flex-wrap gap-1.5">
                        <span v-for="tag in (project.tags || []).slice(0, 3)" :key="tag.id" class="px-2 py-0.5 text-[10px] rounded-md bg-brand-violet/10 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan">{{ tag.name }}</span>
                      </div>
                    </div>
                  </router-link>
                </div>
              </div>
              <div v-if="projectCollectionBlocks.uncategorized.length" class="mt-12 pt-10 border-t border-gray-200 dark:border-dark-600">
                <div class="flex flex-wrap items-center gap-3 mb-5">
                  <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-gray-200/80 text-gray-700 dark:bg-dark-600 dark:text-gray-300">Uncategorized</span>
                  <span class="text-sm text-gray-500 dark:text-gray-400">({{ projectCollectionBlocks.uncategorized.length }})</span>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-7">
                  <router-link
                    v-for="project in projectCollectionBlocks.uncategorized"
                    :key="project.id"
                    :to="`/projects/${project.slug}`"
                    class="group flex flex-col rounded-2xl border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm overflow-hidden hover:border-brand-violet/30 dark:hover:border-brand-cyan/30 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                  >
                    <div class="relative aspect-video overflow-hidden bg-gray-100 dark:bg-dark-700">
                      <img v-if="project.cover_image" :src="project.cover_image" :alt="project.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                      <div v-else class="w-full h-full bg-gradient-brand opacity-25" />
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                      <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-brand-violet dark:group-hover:text-brand-cyan transition-colors">{{ project.title }}</h3>
                      <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 flex-1">{{ project.description }}</p>
                      <div class="mt-3 flex flex-wrap gap-1.5">
                        <span v-for="tag in (project.tags || []).slice(0, 3)" :key="tag.id" class="px-2 py-0.5 text-[10px] rounded-md bg-brand-violet/10 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan">{{ tag.name }}</span>
                      </div>
                    </div>
                  </router-link>
                </div>
              </div>
            </template>
            <div v-else class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-7">
              <router-link
                v-for="project in projects"
                :key="project.id"
                :to="`/projects/${project.slug}`"
                class="group flex flex-col rounded-2xl border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm overflow-hidden hover:border-brand-violet/30 dark:hover:border-brand-cyan/30 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
              >
                <div class="relative aspect-video overflow-hidden bg-gray-100 dark:bg-dark-700">
                  <img v-if="project.cover_image" :src="project.cover_image" :alt="project.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                  <div v-else class="w-full h-full bg-gradient-brand opacity-25" />
                </div>
                <div class="p-5 flex-1 flex flex-col">
                  <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-brand-violet dark:group-hover:text-brand-cyan transition-colors">{{ project.title }}</h3>
                  <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 flex-1">{{ project.description }}</p>
                  <span v-if="project.collection" class="mt-3 inline-flex self-start text-[10px] font-semibold px-2 py-0.5 rounded-full bg-brand-cyan/10 text-brand-cyan dark:bg-brand-violet/10 dark:text-brand-violet">{{ project.collection.name }}</span>
                  <div class="mt-3 flex flex-wrap gap-1.5">
                    <span v-for="tag in (project.tags || []).slice(0, 3)" :key="tag.id" class="px-2 py-0.5 text-[10px] rounded-md bg-brand-violet/10 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan">{{ tag.name }}</span>
                  </div>
                </div>
              </router-link>
            </div>
          </template>
          <EmptyState v-else title="No projects yet" message="Projects created or attributed to this member will show up here." />
          <div v-if="projectsMeta.last_page > 1" class="mt-10">
            <Pagination
              :current-page="projectsMeta.current_page"
              :last-page="projectsMeta.last_page"
              @page-change="fetchProjects"
            />
          </div>
        </div>
      </section>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { useRoute } from 'vue-router'
import dayjs from 'dayjs'
import { publicApi } from '@/services/api'
import EmptyState from '@/components/common/EmptyState.vue'
import Pagination from '@/components/common/Pagination.vue'

const route = useRoute()

const loading = ref(true)
const notFound = ref(false)
const loadFailed = ref(false)
const profile = ref(null)
const activeTab = ref('posts')

const posts = ref([])
const postsLoading = ref(false)
const postsMeta = ref({ current_page: 1, last_page: 1, total: 0 })
const postSeriesFilter = ref('all')

const projects = ref([])
const projectsLoading = ref(false)
const projectsMeta = ref({ current_page: 1, last_page: 1, total: 0 })
const projectCollectionFilter = ref('all')

const seriesChipsList = computed(() => {
  const m = new Map()
  for (const p of posts.value) {
    if (p.series) m.set(p.series.slug, p.series)
  }
  return [...m.values()].sort((a, b) => a.name.localeCompare(b.name))
})

const collectionChipsList = computed(() => {
  const m = new Map()
  for (const p of projects.value) {
    if (p.collection) m.set(p.collection.slug, p.collection)
  }
  return [...m.values()].sort((a, b) => a.name.localeCompare(b.name))
})

const postSeriesBlocks = computed(() => {
  const map = new Map()
  const unc = []
  for (const p of posts.value) {
    if (!p.series) unc.push(p)
    else {
      const k = p.series.slug
      if (!map.has(k)) map.set(k, { series: p.series, posts: [] })
      map.get(k).posts.push(p)
    }
  }
  const groups = [...map.values()].sort((a, b) => a.series.name.localeCompare(b.series.name))
  return { groups, uncategorized: unc }
})

const projectCollectionBlocks = computed(() => {
  const map = new Map()
  const unc = []
  for (const p of projects.value) {
    if (!p.collection) unc.push(p)
    else {
      const k = p.collection.slug
      if (!map.has(k)) map.set(k, { collection: p.collection, projects: [] })
      map.get(k).projects.push(p)
    }
  }
  const groups = [...map.values()].sort((a, b) => a.collection.name.localeCompare(b.collection.name))
  return { groups, uncategorized: unc }
})

function formatDate(date) {
  return date ? dayjs(date).format('MMM D, YYYY') : ''
}

function formatJoined(date) {
  return date ? dayjs(date).format('MMMM YYYY') : ''
}

function profileUsernameFromRoute() {
  return String(route.params.username || '').trim()
}

function postListParams(page) {
  const params = { page, per_page: 48 }
  if (postSeriesFilter.value === 'uncategorized') params.uncategorized = true
  else if (postSeriesFilter.value !== 'all') params.series = postSeriesFilter.value
  return params
}

function projectListParams(page) {
  const params = { page, per_page: 48 }
  if (projectCollectionFilter.value === 'uncategorized') params.uncategorized = true
  else if (projectCollectionFilter.value !== 'all') params.collection = projectCollectionFilter.value
  return params
}

async function fetchPosts(page = 1) {
  const username = profileUsernameFromRoute()
  if (!username) return
  const pg = typeof page === 'number' ? page : 1
  postsLoading.value = true
  try {
    const { data } = await publicApi.getProfileBlogPosts(username, postListParams(pg))
    posts.value = data.data || []
    postsMeta.value = {
      current_page: data.meta?.current_page ?? 1,
      last_page: data.meta?.last_page ?? 1,
      total: data.meta?.total ?? posts.value.length,
    }
  } catch {
    try {
      const uid = profile.value?.id
      if (uid) {
        const { data } = await publicApi.getBlogPosts({
          author_id: uid,
          page: pg,
          per_page: 48,
        })
        posts.value = data.data || []
        postsMeta.value = {
          current_page: data.meta?.current_page ?? 1,
          last_page: data.meta?.last_page ?? 1,
          total: data.meta?.total ?? posts.value.length,
        }
      } else {
        posts.value = []
        postsMeta.value = { current_page: 1, last_page: 1, total: 0 }
      }
    } catch {
      posts.value = []
      postsMeta.value = { current_page: 1, last_page: 1, total: 0 }
    }
  } finally {
    postsLoading.value = false
  }
}

async function fetchProjects(page = 1) {
  const username = profileUsernameFromRoute()
  if (!username) return
  const pg = typeof page === 'number' ? page : 1
  projectsLoading.value = true
  try {
    const { data } = await publicApi.getProfileProjects(username, projectListParams(pg))
    projects.value = data.data || []
    projectsMeta.value = {
      current_page: data.meta?.current_page ?? 1,
      last_page: data.meta?.last_page ?? 1,
      total: data.meta?.total ?? projects.value.length,
    }
  } catch {
    try {
      const uid = profile.value?.id
      if (uid) {
        const { data } = await publicApi.getProjects({
          created_by: uid,
          page: pg,
          per_page: 48,
        })
        projects.value = data.data || []
        projectsMeta.value = {
          current_page: data.meta?.current_page ?? 1,
          last_page: data.meta?.last_page ?? 1,
          total: data.meta?.total ?? projects.value.length,
        }
      } else {
        projects.value = []
        projectsMeta.value = { current_page: 1, last_page: 1, total: 0 }
      }
    } catch {
      projects.value = []
      projectsMeta.value = { current_page: 1, last_page: 1, total: 0 }
    }
  } finally {
    projectsLoading.value = false
  }
}

async function loadProfile() {
  loading.value = true
  notFound.value = false
  loadFailed.value = false
  profile.value = null
  posts.value = []
  projects.value = []
  postsMeta.value = { current_page: 1, last_page: 1, total: 0 }
  projectsMeta.value = { current_page: 1, last_page: 1, total: 0 }
  postSeriesFilter.value = 'all'
  projectCollectionFilter.value = 'all'
  const username = String(route.params.username || '').trim()
  if (!username) {
    notFound.value = true
    loading.value = false
    return
  }
  try {
    const { data } = await publicApi.getUserProfile(username)
    const d = data.data
    const { blog_posts: _bp, blog_posts_meta: _bpm, projects: _pr, projects_meta: _prm, ...profileFields } = d

    profile.value = profileFields
    document.title = `${profileFields.name} (@${profileFields.username}) | Kalapak Code Team`

    await fetchPosts(1)
    await fetchProjects(1)
  } catch (e) {
    const status = e.response?.status
    if (status === 404) {
      notFound.value = true
    } else {
      loadFailed.value = true
    }
    profile.value = null
    document.title = 'Member profile – Kalapak Code Team'
  } finally {
    loading.value = false
  }
}

watch(
  () => route.params.username,
  () => {
    loadProfile()
  },
  { immediate: true }
)

watch(postSeriesFilter, () => {
  if (profile.value) fetchPosts(1)
})

watch(projectCollectionFilter, () => {
  if (profile.value) fetchProjects(1)
})
</script>

<style scoped>
.tab-active {
  @apply bg-gradient-to-r from-brand-violet to-brand-cyan text-white shadow-md;
}
.tab-idle {
  @apply text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-700;
}
</style>
