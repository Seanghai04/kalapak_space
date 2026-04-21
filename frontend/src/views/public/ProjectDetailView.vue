<template>
  <div class="project-detail-page">

    <!-- Loading -->
    <div v-if="loading" class="min-h-[60vh] flex items-center justify-center">
      <div class="relative">
        <div class="w-14 h-14 border-[3px] border-gray-200 dark:border-dark-600 border-t-brand-violet dark:border-t-brand-cyan rounded-full animate-spin" />
        <div class="absolute inset-0 flex items-center justify-center">
          <div class="w-6 h-6 border-[3px] border-gray-200 dark:border-dark-600 border-b-brand-cyan dark:border-b-brand-violet rounded-full animate-spin" style="animation-direction: reverse" />
        </div>
      </div>
    </div>

    <template v-else-if="project">
      <!-- ═══════════════════ HERO ═══════════════════ -->
      <section class="relative overflow-hidden">
        <!-- Cover image background -->
        <div v-if="project.cover_image" class="absolute inset-0">
          <img :src="project.cover_image" :alt="project.title" class="w-full h-full object-cover" />
          <div class="absolute inset-0 bg-gradient-to-b from-white/85 via-white/70 to-white dark:from-dark-900/90 dark:via-dark-900/80 dark:to-dark-900 backdrop-blur-sm" />
        </div>
        <div v-else class="absolute inset-0">
          <div class="absolute inset-0 projects-hero-grid opacity-[0.03] dark:opacity-[0.05]" />
          <div class="absolute top-1/3 -left-20 sm:-left-40 w-[300px] sm:w-[500px] h-[300px] sm:h-[500px] rounded-full bg-brand-violet/10 blur-[80px] sm:blur-[120px]" />
          <div class="absolute bottom-1/4 -right-20 sm:-right-40 w-[250px] sm:w-[400px] h-[250px] sm:h-[400px] rounded-full bg-brand-cyan/10 blur-[70px] sm:blur-[100px]" />
        </div>

        <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 pt-20 sm:pt-28 pb-10 sm:pb-16">
          <!-- Back button -->
          <router-link to="/projects" data-aos="fade-right"
            class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan transition-colors mb-10 group">
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Back to Projects
          </router-link>

          <!-- Badges -->
          <div data-aos="fade-up" class="flex flex-wrap items-center gap-2 mb-5">
            <span :class="statusClasses(project.status)" class="px-3 py-1 text-xs rounded-full font-bold uppercase tracking-wider">
              {{ formatStatus(project.status) }}
            </span>
            <span v-if="project.is_featured" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
              Featured
            </span>
            <span v-if="project.is_open_source" class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
              Open Source
            </span>
          </div>

          <!-- Title -->
          <h1 data-aos="fade-up" data-aos-delay="100" class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-sans font-bold text-gray-900 dark:text-white mb-4 leading-tight">
            {{ project.title }}
          </h1>

          <!-- Description -->
          <p data-aos="fade-up" data-aos-delay="200" class="text-base sm:text-lg text-gray-600 dark:text-gray-400 max-w-3xl leading-relaxed mb-6 sm:mb-8">
            {{ project.description }}
          </p>

          <!-- Action buttons -->
          <div data-aos="fade-up" data-aos-delay="300" class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3 mb-8 sm:mb-10">
            <a v-if="project.demo_url" :href="project.demo_url" target="_blank" rel="noopener noreferrer"
              class="group inline-flex items-center gap-2 px-7 py-3.5 bg-gradient-brand text-white font-semibold rounded-xl shadow-glow hover:shadow-glow-lg transition-all duration-300 hover:-translate-y-0.5 text-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
              Live Demo
            </a>
            <a v-if="project.repo_url" :href="project.repo_url" target="_blank" rel="noopener noreferrer"
              class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl border border-gray-200 dark:border-dark-500 text-gray-700 dark:text-gray-200 font-semibold hover:bg-gray-50 dark:hover:bg-dark-700/50 transition-all duration-300 hover:-translate-y-0.5 text-sm backdrop-blur-sm">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
              View Source
            </a>
          </div>

          <!-- Meta info bar -->
          <div data-aos="fade-up" data-aos-delay="400" class="flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-gray-500 dark:text-gray-400">
            <span v-if="project.creator" class="flex items-center gap-2">
              <img v-if="project.creator.avatar" :src="project.creator.avatar" class="w-5 h-5 rounded-full object-cover" />
              <span v-else class="w-5 h-5 rounded-full bg-gradient-brand flex items-center justify-center text-white text-[10px] font-bold">{{ project.creator.name?.charAt(0) }}</span>
              {{ project.creator.name }}
            </span>
            <span v-if="project.created_at" class="flex items-center gap-1.5">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
              {{ new Date(project.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) }}
            </span>
            <span v-if="project.stars_count" class="flex items-center gap-1.5">
              <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
              {{ project.stars_count }} stars
            </span>
            <span v-if="project.forks_count" class="flex items-center gap-1.5">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z"/></svg>
              {{ project.forks_count }} forks
            </span>
          </div>
        </div>
      </section>

      <!-- ═══════════════════ COVER IMAGE (full width) ═══════════════════ -->
      <section v-if="project.cover_image" class="pb-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
          <div data-aos="fade-up" class="rounded-2xl overflow-hidden border border-gray-100 dark:border-dark-600 shadow-glass dark:shadow-glass-dark">
            <img :src="project.cover_image" :alt="project.title" class="w-full h-auto max-h-[500px] object-cover" />
          </div>
        </div>
      </section>

      <!-- ═══════════════════ CONTENT ═══════════════════ -->
      <section class="pb-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 overflow-hidden">
          <div class="grid lg:grid-cols-[1fr_280px] gap-8 lg:gap-12">
            <!-- Main content -->
            <div data-aos="fade-up" class="min-w-0">
              <!-- Tags -->
              <div v-if="project.tags && project.tags.length" class="flex flex-wrap gap-2 mb-8">
                <span v-for="tag in project.tags" :key="tag.id"
                  class="px-3 py-1 text-xs rounded-lg font-medium bg-brand-violet/8 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan border border-brand-violet/10 dark:border-brand-cyan/15">
                  {{ tag.name }}
                </span>
              </div>

              <!-- Rendered content -->
              <article class="prose prose-lg dark:prose-invert max-w-none overflow-hidden
                prose-headings:font-sans prose-headings:font-bold
                prose-h2:text-2xl prose-h2:mt-12 prose-h2:mb-4 prose-h2:text-gray-900 dark:prose-h2:text-white
                prose-h3:text-xl prose-h3:mt-8 prose-h3:mb-3
                prose-p:text-gray-600 dark:prose-p:text-gray-400 prose-p:leading-relaxed
                prose-a:text-brand-violet dark:prose-a:text-brand-cyan prose-a:no-underline hover:prose-a:underline
                prose-code:text-brand-violet dark:prose-code:text-brand-cyan prose-code:bg-brand-violet/5 dark:prose-code:bg-brand-cyan/10 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded-md prose-code:font-code prose-code:text-sm
                prose-pre:bg-[#0d1117] dark:prose-pre:bg-[#0d1117] prose-pre:border-0 prose-pre:rounded-none prose-pre:m-0 prose-pre:p-0
                prose-img:rounded-xl prose-img:border prose-img:border-gray-100 dark:prose-img:border-dark-600
                prose-blockquote:border-brand-violet dark:prose-blockquote:border-brand-cyan prose-blockquote:bg-brand-violet/5 dark:prose-blockquote:bg-brand-cyan/5 prose-blockquote:rounded-r-xl prose-blockquote:py-1 prose-blockquote:px-6
                prose-li:text-gray-600 dark:prose-li:text-gray-400"
                v-html="renderedContent" />

              <div v-if="!renderedContent" class="py-12 text-center">
                <p class="text-gray-400 dark:text-gray-500 text-sm italic">No detailed description available yet.</p>
              </div>
            </div>

            <!-- Sidebar -->
            <aside data-aos="fade-left" class="min-w-0 space-y-6">
              <!-- Tech stack -->
              <div v-if="project.tech_stack && project.tech_stack.length"
                class="p-6 rounded-2xl border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm">
                <h3 class="text-sm font-sans font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Tech Stack</h3>
                <div class="flex flex-wrap gap-2">
                  <span v-for="(tech, i) in project.tech_stack" :key="i"
                    class="px-3 py-1.5 text-xs rounded-lg font-medium bg-gray-50 dark:bg-dark-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-dark-500">
                    {{ tech }}
                  </span>
                </div>
              </div>

              <!-- Project info -->
              <div class="p-6 rounded-2xl border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm">
                <h3 class="text-sm font-sans font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Project Info</h3>
                <dl class="space-y-3">
                  <div class="flex justify-between">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Status</dt>
                    <dd><span :class="statusClasses(project.status)" class="px-2 py-0.5 text-[10px] rounded-full font-bold uppercase">{{ formatStatus(project.status) }}</span></dd>
                  </div>
                  <div v-if="project.is_open_source" class="flex justify-between">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">License</dt>
                    <dd class="text-xs text-gray-700 dark:text-gray-300 font-medium">Open Source</dd>
                  </div>
                  <div v-if="project.created_at" class="flex justify-between">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Created</dt>
                    <dd class="text-xs text-gray-700 dark:text-gray-300 font-medium">{{ new Date(project.created_at).toLocaleDateString('en-US', { month: 'short', year: 'numeric' }) }}</dd>
                  </div>
                  <div v-if="project.creator" class="flex justify-between items-center">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Creator</dt>
                    <dd class="flex items-center gap-1.5 text-xs text-gray-700 dark:text-gray-300 font-medium">
                      <img v-if="project.creator.avatar" :src="project.creator.avatar" class="w-4 h-4 rounded-full object-cover" />
                      {{ project.creator.name }}
                    </dd>
                  </div>
                </dl>
              </div>

              <!-- Quick links -->
              <div class="p-6 rounded-2xl border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm">
                <h3 class="text-sm font-sans font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Links</h3>
                <div class="space-y-2">
                  <a v-if="project.demo_url" :href="project.demo_url" target="_blank" rel="noopener noreferrer"
                    class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan hover:bg-brand-violet/5 dark:hover:bg-brand-cyan/5 transition-all duration-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                    Live Demo
                  </a>
                  <a v-if="project.repo_url" :href="project.repo_url" target="_blank" rel="noopener noreferrer"
                    class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan hover:bg-brand-violet/5 dark:hover:bg-brand-cyan/5 transition-all duration-300">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                    GitHub Repository
                  </a>
                  <div v-if="!project.demo_url && !project.repo_url" class="text-xs text-gray-400 dark:text-gray-500 italic px-3">
                    No external links available
                  </div>
                </div>
              </div>
            </aside>
          </div>
        </div>
      </section>

      <!-- ═══════════════════ NAVIGATION ═══════════════════ -->
      <section class="pb-24">
        <div class="max-w-5xl mx-auto px-4">
          <div class="flex items-center justify-between pt-8 border-t border-gray-100 dark:border-dark-600">
            <router-link to="/projects"
              class="group inline-flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan hover:bg-brand-violet/5 dark:hover:bg-brand-cyan/5 transition-all duration-300">
              <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
              All Projects
            </router-link>
            <router-link to="/contact"
              class="group inline-flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-medium text-brand-violet dark:text-brand-cyan hover:bg-brand-violet/5 dark:hover:bg-brand-cyan/5 transition-all duration-300">
              Interested? Let's talk
              <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </router-link>
          </div>
        </div>
      </section>
    </template>

    <!-- Not found -->
    <div v-else class="min-h-[50vh] flex items-center justify-center">
      <EmptyState title="Project not found" message="The project you're looking for doesn't exist or has been removed." />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRoute } from 'vue-router'
import { Marked } from 'marked'
import { markedHighlight } from 'marked-highlight'
import DOMPurify from 'dompurify'
import hljs from 'highlight.js'
import { publicApi } from '@/services/api'
import EmptyState from '@/components/common/EmptyState.vue'

const purifyConfig = {
  ADD_TAGS: ['iframe'],
  ADD_ATTR: ['allow', 'allowfullscreen', 'frameborder', 'src', 'style', 'data-youtube-video'],
}

const marked = new Marked(
  markedHighlight({
    langPrefix: 'hljs language-',
    highlight(code, lang) {
      if (lang && hljs.getLanguage(lang)) {
        return hljs.highlight(code, { language: lang }).value
      }
      return hljs.highlightAuto(code).value
    },
  })
)

const route = useRoute()
const project = ref(null)
const loading = ref(true)

const renderedContent = computed(() => {
  if (!project.value?.long_description) return ''
  return DOMPurify.sanitize(marked.parse(project.value.long_description), purifyConfig)
})

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

function addCopyButtons() {
  setTimeout(() => {
    const container = document.querySelector('.prose')
    if (!container) return
    container.querySelectorAll('pre').forEach((pre) => {
      if (pre.parentElement?.classList.contains('code-block-wrapper')) return

      const codeEl = pre.querySelector('code')
      const lang = (codeEl?.className.match(/language-(\w+)/) || [])[1] || ''

      const wrapper = document.createElement('div')
      wrapper.className = 'code-block-wrapper'
      pre.parentNode.insertBefore(wrapper, pre)
      wrapper.appendChild(pre)

      const header = document.createElement('div')
      header.className = 'code-block-header'
      header.innerHTML = `
        <span class="code-block-lang">${lang}</span>
        <button class="copy-code-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
          Copy
        </button>`

      const btn = header.querySelector('.copy-code-btn')
      btn.addEventListener('click', async () => {
        const code = codeEl?.innerText || pre.innerText
        try {
          await navigator.clipboard.writeText(code)
          btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Copied!`
          btn.classList.add('copied')
        } catch {}
        setTimeout(() => {
          btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg> Copy`
          btn.classList.remove('copied')
        }, 2000)
      })

      wrapper.insertBefore(header, pre)
    })
  }, 50)
}

onMounted(async () => {
  try {
    const { data } = await publicApi.getProject(route.params.slug)
    project.value = data.data
  } catch {
    project.value = null
  } finally {
    loading.value = false
    addCopyButtons()
  }
})
</script>

<style scoped>
.projects-hero-grid {
  background-image:
    linear-gradient(rgba(123, 47, 255, 0.1) 1px, transparent 1px),
    linear-gradient(90deg, rgba(123, 47, 255, 0.1) 1px, transparent 1px);
  background-size: 60px 60px;
}
</style>
