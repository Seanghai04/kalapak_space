<template>
  <div class="blog-post-page">

    <!-- Loading -->
    <div v-if="loading" class="min-h-[60vh] flex items-center justify-center">
      <div class="relative">
        <div class="w-14 h-14 border-[3px] border-gray-200 dark:border-dark-600 border-t-brand-violet dark:border-t-brand-cyan rounded-full animate-spin" />
        <div class="absolute inset-0 flex items-center justify-center">
          <div class="w-6 h-6 border-[3px] border-gray-200 dark:border-dark-600 border-b-brand-cyan dark:border-b-brand-violet rounded-full animate-spin" style="animation-direction: reverse" />
        </div>
      </div>
    </div>

    <template v-else-if="post">

      <!-- ═══════════════════ HERO ═══════════════════ -->
      <section class="relative min-h-[45vh] sm:min-h-[55vh] flex items-end overflow-hidden">
        <!-- Background image -->
        <div v-if="post.cover_image" class="absolute inset-0">
          <img :src="post.cover_image" :alt="post.title" class="w-full h-full object-cover" />
          <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/50 to-black/30" />
        </div>
        <div v-else class="absolute inset-0 bg-gradient-brand opacity-80" />

        <!-- Back button -->
        <router-link to="/blog" class="absolute top-4 left-4 sm:top-6 sm:left-6 z-20 inline-flex items-center gap-2 px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-medium text-white/90 bg-white/10 backdrop-blur-md border border-white/10 hover:bg-white/20 transition-all duration-300">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
          Blog
        </router-link>

        <!-- Hero content -->
        <div class="relative z-10 w-full max-w-4xl mx-auto px-4 sm:px-6 pb-8 sm:pb-12 pt-24 sm:pt-32">
          <div class="flex flex-wrap items-center gap-3 mb-5">
            <span v-if="post.category" class="px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-violet/80 dark:bg-brand-cyan/80 text-white backdrop-blur-sm">
              {{ post.category.name }}
            </span>
            <span v-if="post.is_featured" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-500/80 text-white backdrop-blur-sm">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
              Featured
            </span>
          </div>
          <h1 data-aos="fade-up" class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-sans font-bold text-white leading-tight mb-4 sm:mb-5">
            {{ post.title }}
          </h1>
          <!-- Meta bar -->
          <div data-aos="fade-up" data-aos-delay="100" class="flex flex-wrap items-center gap-3 sm:gap-5 text-xs sm:text-sm text-white/70">
            <div class="flex items-center gap-2.5">
              <div class="w-9 h-9 rounded-full overflow-hidden ring-2 ring-white/20">
                <img v-if="post.author?.avatar" :src="post.author.avatar" :alt="post.author.name" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full bg-gradient-brand flex items-center justify-center text-white text-sm font-bold">{{ post.author?.name?.charAt(0) }}</div>
              </div>
              <span class="font-medium text-white">{{ post.author?.name }}</span>
            </div>
            <span class="flex items-center gap-1.5">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
              {{ formatDate(post.published_at) }}
            </span>
            <span v-if="post.reading_time" class="flex items-center gap-1.5">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              {{ post.reading_time }} min read
            </span>
            <span v-if="post.views_count" class="flex items-center gap-1.5">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
              {{ post.views_count }} views
            </span>
          </div>
        </div>
      </section>

      <!-- ═══════════════════ COVER IMAGE (Full-width) ═══════════════════ -->
      <section v-if="post.cover_image" class="py-8 relative z-10">
        <div class="max-w-5xl mx-auto px-4">
          <div data-aos="fade-up" class="rounded-2xl overflow-hidden shadow-2xl dark:shadow-glow/10">
            <img :src="post.cover_image" :alt="post.title" class="w-full object-cover max-h-[500px]" />
          </div>
        </div>
      </section>

      <!-- ═══════════════════ CONTENT + SIDEBAR ═══════════════════ -->
      <section class="py-8 sm:py-12 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
          <div class="grid lg:grid-cols-[1fr_320px] gap-6 lg:gap-10">

            <!-- Main content -->
            <article data-aos="fade-up"
              class="min-w-0 rounded-2xl border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm p-4 sm:p-8 md:p-12 overflow-hidden">
              <div
                class="prose prose-lg dark:prose-invert max-w-none
                  prose-headings:font-sans prose-headings:text-gray-900 dark:prose-headings:text-white
                  prose-a:text-brand-violet dark:prose-a:text-brand-cyan prose-a:no-underline hover:prose-a:underline
                  prose-code:text-brand-violet dark:prose-code:text-brand-cyan prose-code:bg-brand-violet/5 dark:prose-code:bg-brand-cyan/5 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded-md prose-code:before:content-[''] prose-code:after:content-['']
                  prose-pre:bg-gray-900 dark:prose-pre:bg-dark-900 prose-pre:border prose-pre:border-gray-200 dark:prose-pre:border-dark-600 prose-pre:rounded-xl
                  prose-img:rounded-xl prose-img:shadow-lg
                  prose-blockquote:border-brand-violet dark:prose-blockquote:border-brand-cyan prose-blockquote:bg-brand-violet/5 dark:prose-blockquote:bg-brand-cyan/5 prose-blockquote:rounded-r-xl prose-blockquote:py-1 prose-blockquote:px-6
                  prose-hr:border-gray-200 dark:prose-hr:border-dark-600
                  prose-strong:text-gray-900 dark:prose-strong:text-white
                  prose-table:overflow-hidden prose-table:rounded-xl prose-th:bg-gray-50 dark:prose-th:bg-dark-700 prose-td:border-gray-200 dark:prose-td:border-dark-600"
                v-html="renderedContent" />
            </article>

            <!-- Sidebar -->
            <aside class="min-w-0 space-y-6 lg:sticky lg:top-24 lg:self-start">

              <!-- Author card -->
              <div data-aos="fade-left" data-aos-delay="100"
                class="rounded-2xl border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm p-6 text-center overflow-hidden">
                <div class="w-16 h-16 rounded-full overflow-hidden ring-3 ring-brand-violet/20 dark:ring-brand-cyan/20 mx-auto mb-3">
                  <img v-if="post.author?.avatar" :src="post.author.avatar" :alt="post.author.name" class="w-full h-full object-cover" />
                  <div v-else class="w-full h-full bg-gradient-brand flex items-center justify-center text-white text-2xl font-bold">{{ post.author?.name?.charAt(0) }}</div>
                </div>
                <h3 class="font-sans font-bold text-gray-900 dark:text-white truncate">{{ post.author?.name }}</h3>
                <p class="text-xs text-brand-violet dark:text-brand-cyan uppercase tracking-wider mb-2">Author</p>
                <p v-if="post.author?.bio" class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed line-clamp-3">{{ post.author.bio }}</p>
              </div>

              <!-- Article info card -->
              <div data-aos="fade-left" data-aos-delay="200"
                class="rounded-2xl border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm p-6 overflow-hidden">
                <h4 class="text-xs font-sans font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                  <svg class="w-4 h-4 text-brand-violet dark:text-brand-cyan shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                  Article Info
                </h4>
                <div class="space-y-3 text-sm">
                  <div class="flex items-center justify-between gap-2">
                    <span class="text-gray-500 dark:text-gray-400 shrink-0">Published</span>
                    <span class="text-gray-900 dark:text-white font-medium text-right truncate">{{ formatDate(post.published_at) }}</span>
                  </div>
                  <div v-if="post.reading_time" class="flex items-center justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Reading Time</span>
                    <span class="text-gray-900 dark:text-white font-medium">{{ post.reading_time }} min</span>
                  </div>
                  <div v-if="post.views_count" class="flex items-center justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Views</span>
                    <span class="text-gray-900 dark:text-white font-medium">{{ post.views_count }}</span>
                  </div>
                  <div v-if="post.category" class="flex items-center justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Category</span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-brand-violet/10 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan">
                      {{ post.category.name }}
                    </span>
                  </div>
                  <div v-if="post.comments_count" class="flex items-center justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Comments</span>
                    <span class="text-gray-900 dark:text-white font-medium">{{ post.comments_count }}</span>
                  </div>
                </div>
              </div>

              <!-- Share card -->
              <div data-aos="fade-left" data-aos-delay="300"
                class="rounded-2xl border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm p-6 overflow-hidden">
                <h4 class="text-xs font-sans font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                  <svg class="w-4 h-4 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z"/></svg>
                  Share
                </h4>
                <div class="flex items-center gap-2">
                  <button @click="copyLink"
                    class="flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-xs font-medium border border-gray-200 dark:border-dark-500 text-gray-600 dark:text-gray-400 hover:border-brand-violet dark:hover:border-brand-cyan hover:text-brand-violet dark:hover:text-brand-cyan transition-all duration-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m9.193-9.193a4.5 4.5 0 00-6.364 6.364l4.5 4.5a4.5 4.5 0 006.364-6.364L16.5 7.5" /></svg>
                    {{ copied ? 'Copied!' : 'Copy Link' }}
                  </button>
                  <a :href="`https://twitter.com/intent/tweet?url=${encodeURIComponent(currentUrl)}&text=${encodeURIComponent(post.title)}`" target="_blank" rel="noopener noreferrer"
                    class="flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 dark:border-dark-500 text-gray-500 dark:text-gray-400 hover:border-brand-violet dark:hover:border-brand-cyan hover:text-brand-violet dark:hover:text-brand-cyan transition-all duration-300">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                  </a>
                </div>
              </div>

            </aside>
          </div>
        </div>
      </section>

      <!-- ═══════════════════ NAVIGATION ═══════════════════ -->
      <section class="pb-24 relative z-10">
        <div class="max-w-4xl mx-auto px-4">
          <div data-aos="fade-up" class="flex items-center justify-center">
            <router-link to="/blog" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-medium border border-gray-200 dark:border-dark-600 text-gray-600 dark:text-gray-400 hover:border-brand-violet dark:hover:border-brand-cyan hover:text-brand-violet dark:hover:text-brand-cyan bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm transition-all duration-300 hover:-translate-y-0.5">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
              Back to all articles
            </router-link>
          </div>
        </div>
      </section>

    </template>

    <!-- Not found -->
    <div v-else class="min-h-[60vh] flex items-center justify-center">
      <EmptyState title="Post not found" message="The blog post you're looking for doesn't exist." />
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
import dayjs from 'dayjs'
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
const post = ref(null)
const loading = ref(true)
const copied = ref(false)

const currentUrl = computed(() => window.location.href)

const renderedContent = computed(() => {
  if (!post.value?.content) return ''
  return DOMPurify.sanitize(marked.parse(post.value.content), purifyConfig)
})

function formatDate(date) {
  return date ? dayjs(date).format('MMMM D, YYYY') : ''
}

function copyLink() {
  navigator.clipboard.writeText(window.location.href)
  copied.value = true
  setTimeout(() => { copied.value = false }, 2000)
}

function addCopyButtons() {
  nextTick(() => {
    const container = document.querySelector('.prose')
    if (!container) return
    container.querySelectorAll('pre').forEach((pre) => {
      if (pre.parentElement?.classList.contains('code-block-wrapper')) return
      const wrapper = document.createElement('div')
      wrapper.className = 'code-block-wrapper'
      pre.parentNode.insertBefore(wrapper, pre)
      wrapper.appendChild(pre)
      const btn = document.createElement('button')
      btn.className = 'copy-code-btn'
      btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg><span>Copy</span>'
      btn.addEventListener('click', () => {
        const code = pre.querySelector('code')
        const text = code ? code.innerText : pre.innerText
        navigator.clipboard.writeText(text)
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><span>Copied!</span>'
        btn.classList.add('copied')
        setTimeout(() => {
          btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg><span>Copy</span>'
          btn.classList.remove('copied')
        }, 2000)
      })
      wrapper.appendChild(btn)
    })
  })
}

onMounted(async () => {
  try {
    const { data } = await publicApi.getBlogPost(route.params.slug)
    post.value = data.data
  } catch {
    post.value = null
  } finally {
    loading.value = false
    addCopyButtons()
  }
})
</script>
