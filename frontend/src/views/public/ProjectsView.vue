<template>
  <div class="projects-page">

    <!-- ═══════════════════ HERO ═══════════════════ -->
    <section class="relative min-h-[45vh] sm:min-h-[50vh] flex items-center justify-center overflow-hidden">
      <!-- Grid bg -->
      <div class="absolute inset-0 projects-hero-grid opacity-[0.03] dark:opacity-[0.05]" />
      <!-- Gradient orbs -->
      <div class="absolute top-1/3 -left-20 sm:-left-40 w-[300px] sm:w-[500px] h-[300px] sm:h-[500px] rounded-full bg-brand-violet/15 blur-[80px] sm:blur-[120px] animate-float" />
      <div class="absolute bottom-1/4 -right-20 sm:-right-40 w-[250px] sm:w-[400px] h-[250px] sm:h-[400px] rounded-full bg-brand-cyan/15 blur-[70px] sm:blur-[100px] animate-float" style="animation-delay: 3s" />

      <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center relative z-10">
        <div data-aos="fade-down" data-aos-duration="800" class="inline-flex items-center gap-2 px-4 py-1.5 mb-6 rounded-full border border-brand-violet/20 dark:border-brand-cyan/20 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm">
          <span class="text-xs font-code text-brand-violet dark:text-brand-cyan uppercase tracking-widest">// Portfolio</span>
        </div>
        <h1 data-aos="fade-up" data-aos-duration="1000" class="text-3xl sm:text-4xl md:text-5xl lg:text-7xl font-sans font-bold mb-4 sm:mb-6 leading-[1.1]">
          <span class="text-gray-900 dark:text-white">Our</span>
          <span class="gradient-text"> Projects</span>
        </h1>
        <p data-aos="fade-up" data-aos-delay="200" class="text-base sm:text-lg md:text-xl text-gray-500 dark:text-gray-400 max-w-2xl mx-auto font-sans leading-relaxed px-2 sm:px-0">
          Real-world applications built with passion. From concept to deployment, explore what we've been crafting.
        </p>
      </div>
    </section>

    <!-- ═══════════════════ STATS BAR ═══════════════════ -->
    <section class="relative z-10 -mt-6 mb-10 sm:mb-16">
      <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-3 gap-0 bg-white/80 dark:bg-dark-800/80 backdrop-blur-xl rounded-2xl border border-gray-100 dark:border-dark-600 shadow-glass dark:shadow-glass-dark overflow-hidden divide-x divide-gray-100 dark:divide-dark-600" data-aos="fade-up">
          <div class="group px-3 sm:px-6 py-4 sm:py-6 text-center hover:bg-brand-violet/5 dark:hover:bg-brand-cyan/5 transition-colors duration-300">
            <p class="text-xl sm:text-2xl md:text-3xl font-sans font-bold gradient-text mb-0.5 group-hover:scale-110 transition-transform duration-300">{{ totalProjects }}</p>
            <p class="text-[9px] sm:text-[10px] text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider">Total Projects</p>
          </div>
          <div class="group px-3 sm:px-6 py-4 sm:py-6 text-center hover:bg-brand-violet/5 dark:hover:bg-brand-cyan/5 transition-colors duration-300">
            <p class="text-xl sm:text-2xl md:text-3xl font-sans font-bold gradient-text mb-0.5 group-hover:scale-110 transition-transform duration-300">{{ activeCount }}</p>
            <p class="text-[9px] sm:text-[10px] text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider">Active</p>
          </div>
          <div class="group px-3 sm:px-6 py-4 sm:py-6 text-center hover:bg-brand-violet/5 dark:hover:bg-brand-cyan/5 transition-colors duration-300">
            <p class="text-xl sm:text-2xl md:text-3xl font-sans font-bold gradient-text mb-0.5 group-hover:scale-110 transition-transform duration-300">{{ openSourceCount }}</p>
            <p class="text-[9px] sm:text-[10px] text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider">Open Source</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════ FILTERS ═══════════════════ -->
    <section class="pb-12 relative z-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div data-aos="fade-up" class="flex flex-col md:flex-row items-stretch md:items-center gap-3 sm:gap-4 p-3 sm:p-4 rounded-2xl border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm">
          <!-- Search -->
          <div class="relative flex-1">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
            <input
              v-model="search"
              type="text"
              placeholder="Search projects..."
              class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 dark:bg-dark-700 border border-gray-200 dark:border-dark-500 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-violet/30 dark:focus:ring-brand-cyan/30 focus:border-brand-violet dark:focus:border-brand-cyan transition-all duration-300"
              @input="debouncedFetch"
            />
          </div>
          <!-- Tag filter -->
          <div class="w-full md:w-44">
            <CustomSelect
              v-model="selectedTag"
              :options="[{ label: 'All Tags', value: '' }, ...tags.map(t => ({ label: t, value: t }))]"
              placeholder="All Tags"
              @change="fetchProjects"
            />
          </div>
          <!-- Status filter -->
          <div class="w-full md:w-44">
            <CustomSelect
              v-model="selectedStatus"
              :options="[
                { label: 'All Status', value: '' },
                { label: 'Completed', value: 'completed' },
                { label: 'In Progress', value: 'in_progress' },
                { label: 'Active', value: 'active' },
                { label: 'Planned', value: 'planned' },
                { label: 'Archived', value: 'archived' }
              ]"
              placeholder="All Status"
              @change="fetchProjects"
            />
          </div>
          <!-- Clear -->
          <button v-if="search || selectedTag || selectedStatus" @click="clearFilters"
            class="px-4 py-3 rounded-xl text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan hover:bg-brand-violet/5 dark:hover:bg-brand-cyan/5 transition-all duration-300 whitespace-nowrap">
            Clear filters
          </button>
        </div>
        <p v-if="!loading" data-aos="fade-up" class="text-xs text-gray-400 dark:text-gray-500 mt-3 px-1">
          Showing {{ projects.length }} {{ projects.length === 1 ? 'project' : 'projects' }}
          <span v-if="search || selectedTag || selectedStatus"> with active filters</span>
        </p>
      </div>
    </section>

    <!-- ═══════════════════ FEATURED PROJECT ═══════════════════ -->
    <section v-if="featuredProject && !search && !selectedTag && !selectedStatus && meta.current_page === 1" class="pb-16 relative z-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div data-aos="fade-up" class="group relative rounded-2xl sm:rounded-3xl overflow-hidden border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm hover:shadow-2xl dark:hover:shadow-glow/10 transition-all duration-500">
          <div class="grid lg:grid-cols-2 gap-0">
            <!-- Image -->
            <div class="relative aspect-video lg:aspect-auto overflow-hidden">
              <img v-if="featuredProject.cover_image" :src="featuredProject.cover_image" :alt="featuredProject.title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
              <div v-else class="w-full h-full bg-gradient-brand opacity-20" />
              <!-- Featured badge -->
              <div class="absolute top-4 left-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-brand-violet/90 dark:bg-brand-cyan/90 text-white text-xs font-bold uppercase tracking-wider backdrop-blur-sm">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                Featured
              </div>
            </div>
            <!-- Content -->
            <div class="p-8 md:p-10 lg:p-12 flex flex-col justify-center">
              <div class="flex flex-wrap items-center gap-2 mb-4">
                <span :class="statusClasses(featuredProject.status)" class="px-2.5 py-1 text-xs rounded-full font-medium">
                  {{ formatStatus(featuredProject.status) }}
                </span>
                <span v-if="featuredProject.is_open_source" class="px-2.5 py-1 text-xs rounded-full font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                  Open Source
                </span>
              </div>
              <h2 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 dark:text-white mb-3 group-hover:text-brand-violet dark:group-hover:text-brand-cyan transition-colors">
                {{ featuredProject.title }}
              </h2>
              <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-6 line-clamp-3">{{ featuredProject.description }}</p>
              <!-- Tags -->
              <div class="flex flex-wrap gap-2 mb-6">
                <span v-for="tag in (featuredProject.tags || []).slice(0, 5)" :key="tag.id"
                  class="px-2.5 py-1 text-xs rounded-lg font-medium bg-brand-violet/8 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan border border-brand-violet/10 dark:border-brand-cyan/15">
                  {{ tag.name }}
                </span>
              </div>
              <!-- Actions -->
              <div class="flex flex-wrap items-center gap-3">
                <router-link :to="`/projects/${featuredProject.slug}`"
                  class="group/btn inline-flex items-center gap-2 px-6 py-3 bg-gradient-brand text-white font-semibold rounded-xl shadow-glow hover:shadow-glow-lg transition-all duration-300 hover:-translate-y-0.5 text-sm">
                  <span>View Project</span>
                  <svg class="w-4 h-4 transition-transform group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </router-link>
                <a v-if="featuredProject.demo_url" :href="featuredProject.demo_url" target="_blank" rel="noopener noreferrer"
                  class="inline-flex items-center gap-2 px-5 py-3 rounded-xl border border-gray-200 dark:border-dark-500 text-gray-700 dark:text-gray-300 text-sm font-medium hover:bg-gray-50 dark:hover:bg-dark-700/50 transition-all duration-300 hover:-translate-y-0.5">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                  Live Demo
                </a>
                <a v-if="featuredProject.repo_url" :href="featuredProject.repo_url" target="_blank" rel="noopener noreferrer"
                  class="inline-flex items-center gap-2 px-5 py-3 rounded-xl border border-gray-200 dark:border-dark-500 text-gray-700 dark:text-gray-300 text-sm font-medium hover:bg-gray-50 dark:hover:bg-dark-700/50 transition-all duration-300 hover:-translate-y-0.5">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                  Source
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════ PROJECTS GRID ═══════════════════ -->
    <section class="pb-16 sm:pb-24 relative z-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <!-- Loading -->
        <div v-if="loading" class="flex items-center justify-center py-20">
          <div class="relative">
            <div class="w-14 h-14 border-[3px] border-gray-200 dark:border-dark-600 border-t-brand-violet dark:border-t-brand-cyan rounded-full animate-spin" />
            <div class="absolute inset-0 flex items-center justify-center">
              <div class="w-6 h-6 border-[3px] border-gray-200 dark:border-dark-600 border-b-brand-cyan dark:border-b-brand-violet rounded-full animate-spin" style="animation-direction: reverse" />
            </div>
          </div>
        </div>

        <!-- Grid -->
        <div v-else-if="displayProjects.length" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-7">
          <router-link v-for="(project, i) in displayProjects" :key="project.id" :to="`/projects/${project.slug}`"
            data-aos="fade-up" :data-aos-delay="(i % 3) * 80"
            class="group relative flex flex-col rounded-2xl border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm overflow-hidden hover:border-brand-violet/30 dark:hover:border-brand-cyan/30 transition-all duration-500 hover:-translate-y-2 hover:shadow-xl dark:hover:shadow-glow/10">
            <!-- Cover image -->
            <div class="relative aspect-[16/10] overflow-hidden bg-gray-100 dark:bg-dark-700">
              <img v-if="project.cover_image" :src="project.cover_image" :alt="project.title"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
              <div v-else class="w-full h-full flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-300 dark:text-dark-500" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"/></svg>
              </div>
              <!-- Overlay gradient -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
              <!-- Status badge -->
              <div class="absolute top-3 right-3">
                <span :class="statusClasses(project.status)" class="px-2.5 py-1 text-[10px] rounded-full font-bold uppercase tracking-wider backdrop-blur-sm">
                  {{ formatStatus(project.status) }}
                </span>
              </div>
              <!-- Open source badge -->
              <div v-if="project.is_open_source" class="absolute top-3 left-3">
                <span class="px-2 py-1 text-[10px] rounded-full font-bold uppercase tracking-wider bg-emerald-500/90 text-white backdrop-blur-sm">
                  OSS
                </span>
              </div>
              <!-- Hover actions overlay -->
              <div class="absolute bottom-3 left-3 right-3 flex items-center gap-2 opacity-0 translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-500">
                <span v-if="project.demo_url" @click.prevent="openLink(project.demo_url)"
                  class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-white/90 dark:bg-dark-800/90 backdrop-blur-sm text-xs font-medium text-gray-700 dark:text-gray-200 hover:bg-white dark:hover:bg-dark-700 transition-colors">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                  Demo
                </span>
                <span v-if="project.repo_url" @click.prevent="openLink(project.repo_url)"
                  class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-white/90 dark:bg-dark-800/90 backdrop-blur-sm text-xs font-medium text-gray-700 dark:text-gray-200 hover:bg-white dark:hover:bg-dark-700 transition-colors">
                  <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                  Code
                </span>
              </div>
            </div>

            <!-- Card body -->
            <div class="flex flex-col flex-1 p-6">
              <!-- Tags -->
              <div class="flex flex-wrap gap-1.5 mb-3">
                <span v-for="tag in (project.tags || []).slice(0, 3)" :key="tag.id"
                  class="px-2 py-0.5 text-[10px] rounded-md font-medium bg-brand-violet/8 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan">
                  {{ tag.name }}
                </span>
                <span v-if="(project.tags || []).length > 3" class="px-2 py-0.5 text-[10px] rounded-md font-medium text-gray-400 dark:text-gray-500">
                  +{{ project.tags.length - 3 }}
                </span>
              </div>

              <!-- Title -->
              <h3 class="text-lg font-sans font-bold text-gray-900 dark:text-white mb-2 group-hover:text-brand-violet dark:group-hover:text-brand-cyan transition-colors leading-snug">
                {{ project.title }}
              </h3>

              <!-- Description -->
              <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed line-clamp-2 mb-4 flex-1">
                {{ project.description }}
              </p>

              <!-- Footer -->
              <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-dark-600">
                <div class="flex items-center gap-3 text-xs text-gray-400 dark:text-gray-500">
                  <span v-if="project.stars_count" class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    {{ project.stars_count }}
                  </span>
                  <span v-if="project.forks_count" class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z"/></svg>
                    {{ project.forks_count }}
                  </span>
                  <span v-if="project.created_at" class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                    {{ formatDate(project.created_at) }}
                  </span>
                </div>
                <span class="text-xs font-medium text-brand-violet dark:text-brand-cyan group-hover:translate-x-1 transition-transform duration-300 flex items-center gap-1">
                  Details
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </span>
              </div>
            </div>

            <!-- Hover gradient bottom bar -->
            <div class="h-[2px] w-full bg-gradient-brand opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
          </router-link>
        </div>

        <!-- Empty state -->
        <EmptyState v-else title="No projects found" message="Try adjusting your search or filters." />

        <!-- Pagination -->
        <div v-if="meta.last_page > 1" class="mt-12">
          <Pagination
            :current-page="meta.current_page"
            :last-page="meta.last_page"
            @page-change="goToPage"
          />
        </div>
      </div>
    </section>


  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { publicApi } from '@/services/api'
import Pagination from '@/components/common/Pagination.vue'
import EmptyState from '@/components/common/EmptyState.vue'
import CustomSelect from '@/components/common/CustomSelect.vue'

const projects = ref([])
const loading = ref(true)
const search = ref('')
const selectedTag = ref('')
const selectedStatus = ref('')
const meta = ref({ current_page: 1, last_page: 1 })
const tags = ref(['Vue.js', 'Laravel', 'Flutter', 'Docker', 'PostgreSQL', 'React', 'Node.js', 'Python', 'Tailwind CSS', 'TypeScript'])

const totalProjects = computed(() => meta.value.total || projects.value.length || 0)
const activeCount = computed(() => projects.value.filter(p => p.status === 'active' || p.status === 'in_progress').length || '–')
const openSourceCount = computed(() => projects.value.filter(p => p.is_open_source).length || '–')

const featuredProject = computed(() => projects.value.find(p => p.is_featured))
const displayProjects = computed(() => {
  if (search.value || selectedTag.value || selectedStatus.value || meta.value.current_page > 1) return projects.value
  return projects.value.filter(p => !p.is_featured)
})

let debounceTimer = null

function debouncedFetch() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(fetchProjects, 300)
}

async function fetchProjects(page = 1) {
  loading.value = true
  try {
    const { data } = await publicApi.getProjects({
      page: typeof page === 'number' ? page : 1,
      per_page: 9,
      search: search.value || undefined,
      tag: selectedTag.value || undefined,
      status: selectedStatus.value || undefined,
    })
    projects.value = data.data || []
    meta.value = data.meta || { current_page: 1, last_page: 1 }
  } catch {
    projects.value = []
  } finally {
    loading.value = false
  }
}

function clearFilters() {
  search.value = ''
  selectedTag.value = ''
  selectedStatus.value = ''
  fetchProjects()
}

function goToPage(page) {
  fetchProjects(page)
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

function formatStatus(status) {
  if (!status) return ''
  return status.replace(/_/g, ' ')
}

function statusClasses(status) {
  const map = {
    completed: 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400',
    active: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400',
    in_progress: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400',
    planned: 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
    archived: 'bg-gray-100 text-gray-600 dark:bg-gray-800/40 dark:text-gray-400',
  }
  return map[status] || 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'
}

function formatDate(date) {
  if (!date) return ''
  return new Date(date).toLocaleDateString('en-US', { month: 'short', year: 'numeric' })
}

function openLink(url) {
  window.open(url, '_blank', 'noopener,noreferrer')
}

onMounted(() => fetchProjects())
</script>

<style scoped>
.projects-hero-grid {
  background-image:
    linear-gradient(rgba(123, 47, 255, 0.1) 1px, transparent 1px),
    linear-gradient(90deg, rgba(123, 47, 255, 0.1) 1px, transparent 1px);
  background-size: 60px 60px;
}
</style>
