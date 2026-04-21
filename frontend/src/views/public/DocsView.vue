<template>
  <div class="min-h-screen bg-white dark:bg-dark-900 flex">

    <!-- ══ Left Sidebar ══ -->
    <aside
      class="fixed top-0 left-0 h-full w-64 flex-shrink-0 z-20 bg-white dark:bg-dark-800 border-r border-gray-200 dark:border-white/[0.06] overflow-y-auto pt-[68px] transition-transform duration-300"
      :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    >
      <div class="px-4 py-6">
        <!-- Search in docs -->
        <div class="relative mb-6">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search docs..."
            class="w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-white/[0.08] bg-gray-50 dark:bg-white/[0.03] text-gray-700 dark:text-gray-300 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-violet/30 dark:focus:ring-brand-cyan/30 focus:border-brand-violet dark:focus:border-brand-cyan"
          />
        </div>

        <!-- Nav Groups -->
        <div v-if="loading" class="space-y-4">
          <div v-for="i in 4" :key="i" class="animate-pulse">
            <div class="h-3 bg-gray-200 dark:bg-white/10 rounded w-2/3 mb-3"></div>
            <div v-for="j in 3" :key="j" class="h-3 bg-gray-100 dark:bg-white/5 rounded mb-2 ml-2"></div>
          </div>
        </div>

        <nav v-else>
          <div v-for="mainMenu in filteredNavTree" :key="mainMenu.id" class="mb-4">
            <!-- Main Menu header (collapsible) -->
            <button
              @click="toggleMenu(mainMenu.id)"
              class="w-full flex items-center justify-between px-2 mb-2 group"
            >
              <span class="text-[11px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors">{{ mainMenu.name }}</span>
              <svg
                class="w-3 h-3 text-gray-400 dark:text-gray-500 transition-transform duration-200"
                :class="collapsedMenus.has(mainMenu.id) ? '' : '-rotate-90'"
                fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
              ><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div v-show="collapsedMenus.has(mainMenu.id)">
              <!-- Direct pages of main menu (no sub-menu) -->
              <ul v-if="mainMenu.pages?.length" class="space-y-0.5 mb-1">
                <li v-for="page in mainMenu.pages" :key="page.slug">
                  <button
                    @click="loadDoc(page.slug)"
                    class="w-full text-left px-3 py-1.5 rounded-lg text-[13.5px] transition-colors duration-150"
                    :class="currentSlug === page.slug
                      ? 'bg-brand-violet/10 dark:bg-brand-cyan/10 text-brand-violet dark:text-brand-cyan font-semibold border-l-2 border-brand-violet dark:border-brand-cyan'
                      : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/[0.04]'"
                  >{{ page.title }}</button>
                  <ul v-if="page.children?.length" class="mt-0.5 ml-3 pl-3 border-l border-gray-200 dark:border-white/[0.07] space-y-0.5">
                    <li v-for="sub in page.children" :key="sub.slug">
                      <button
                        @click="loadDoc(sub.slug)"
                        class="w-full text-left px-2 py-1.5 rounded-lg text-[13px] transition-colors duration-150"
                        :class="currentSlug === sub.slug
                          ? 'bg-brand-violet/10 dark:bg-brand-cyan/10 text-brand-violet dark:text-brand-cyan font-semibold border-l-2 border-brand-violet dark:border-brand-cyan'
                          : 'text-gray-500 dark:text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/[0.04]'"
                      >{{ sub.title }}</button>
                    </li>
                  </ul>
                </li>
              </ul>

              <!-- Sub-menus with their pages -->
              <div v-for="subMenu in mainMenu.children" :key="subMenu.id" class="mb-2">
                <p class="px-3 pt-1 pb-0.5 text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">{{ subMenu.name }}</p>
                <ul class="space-y-0.5">
                  <li v-for="page in subMenu.pages" :key="page.slug">
                    <button
                      @click="loadDoc(page.slug)"
                      class="w-full text-left px-3 py-1.5 rounded-lg text-[13.5px] transition-colors duration-150"
                      :class="currentSlug === page.slug
                        ? 'bg-brand-violet/10 dark:bg-brand-cyan/10 text-brand-violet dark:text-brand-cyan font-semibold border-l-2 border-brand-violet dark:border-brand-cyan'
                        : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/[0.04]'"
                    >{{ page.title }}</button>
                    <!-- Subpages (indented under page) -->
                    <ul v-if="page.children?.length" class="mt-0.5 ml-3 pl-3 border-l border-gray-200 dark:border-white/[0.07] space-y-0.5">
                      <li v-for="sub in page.children" :key="sub.slug">
                        <button
                          @click="loadDoc(sub.slug)"
                          class="w-full text-left px-2 py-1.5 rounded-lg text-[13px] transition-colors duration-150"
                          :class="currentSlug === sub.slug
                            ? 'bg-brand-violet/10 dark:bg-brand-cyan/10 text-brand-violet dark:text-brand-cyan font-semibold border-l-2 border-brand-violet dark:border-brand-cyan'
                            : 'text-gray-500 dark:text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/[0.04]'"
                        >{{ sub.title }}</button>
                      </li>
                    </ul>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div v-if="filteredNavTree.length === 0 && !loading" class="text-center py-8 text-sm text-gray-400">
            No docs found.
          </div>
        </nav>
      </div>
    </aside>

    <!-- Mobile sidebar backdrop -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 z-10 bg-black/40 lg:hidden"
      @click="sidebarOpen = false"
    />

    <!-- ══ Main Content ══ -->
    <div class="flex-1 lg:ml-64 min-w-0">
      <div class="px-8 sm:px-10 py-10 xl:pr-[320px]">

        <!-- Mobile: sidebar toggle -->
        <button
          @click="sidebarOpen = !sidebarOpen"
          class="lg:hidden mb-6 flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 dark:hover:text-white"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
          Browse Docs
        </button>

        <!-- Loading -->
        <div v-if="docLoading" class="space-y-4 animate-pulse">
          <div class="h-8 bg-gray-200 dark:bg-white/10 rounded w-1/2"></div>
          <div class="h-4 bg-gray-100 dark:bg-white/5 rounded w-full"></div>
          <div class="h-4 bg-gray-100 dark:bg-white/5 rounded w-5/6"></div>
          <div class="h-4 bg-gray-100 dark:bg-white/5 rounded w-3/4"></div>
        </div>

        <!-- No doc selected -->
        <div v-else-if="!currentDoc && !docLoading" class="text-center py-20">
          <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
          <h2 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-2">Select a document</h2>
          <p class="text-gray-400 dark:text-gray-500 text-sm">Choose a topic from the sidebar to get started.</p>
        </div>

        <!-- Doc Content -->
        <article v-else-if="currentDoc" class="doc-article">
          <!-- Header -->
          <div class="mb-8 pb-6 border-b border-gray-200 dark:border-white/[0.06]">
            <div class="flex items-center gap-2 text-xs text-gray-400 dark:text-gray-500 mb-3">
              <span>{{ currentDoc.category }}</span>
              <span>·</span>
              <span>Updated {{ formatDate(currentDoc.updated_at) }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white tracking-tight">{{ currentDoc.title }}</h1>
          </div>

          <!-- Rendered content: sections-based or legacy single-content -->
          <template v-if="currentDoc.sections && currentDoc.sections.length">
            <div v-for="(section, idx) in currentDoc.sections" :key="idx" class="mb-10">
              <h2
                :id="sectionAnchor(section.heading, idx)"
                class="text-2xl font-bold text-gray-900 dark:text-white mt-0 mb-4 pb-3 border-b border-gray-200 dark:border-white/[0.06] scroll-mt-24"
              >{{ section.heading }}</h2>
              <div class="prose-doc" v-html="renderContent(section.content)" />
            </div>
          </template>
          <div v-else class="prose-doc" v-html="renderContent(currentDoc.content)" />

          <!-- Navigation: Prev / Next -->
          <div class="mt-16 pt-6 border-t border-gray-200 dark:border-white/[0.06] flex items-center justify-between gap-4">
            <button
              v-if="prevDoc"
              @click="loadDoc(prevDoc.slug)"
              class="group flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-200 dark:border-white/[0.08] hover:border-brand-violet dark:hover:border-brand-cyan hover:bg-gray-50 dark:hover:bg-white/[0.03] transition-all"
            >
              <svg class="w-4 h-4 text-gray-400 group-hover:text-brand-violet dark:group-hover:text-brand-cyan transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
              <div class="text-left">
                <p class="text-[10px] text-gray-400 uppercase tracking-wide">Previous</p>
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ prevDoc.title }}</p>
              </div>
            </button>
            <div v-else />
            <button
              v-if="nextDoc"
              @click="loadDoc(nextDoc.slug)"
              class="group flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-200 dark:border-white/[0.08] hover:border-brand-violet dark:hover:border-brand-cyan hover:bg-gray-50 dark:hover:bg-white/[0.03] transition-all text-right ml-auto"
            >
              <div>
                <p class="text-[10px] text-gray-400 uppercase tracking-wide">Next</p>
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ nextDoc.title }}</p>
              </div>
              <svg class="w-4 h-4 text-gray-400 group-hover:text-brand-violet dark:group-hover:text-brand-cyan transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </button>
          </div>
        </article>
      </div>
    </div>

    <!-- ══ Right: On this page (TOC) ══ -->
    <aside v-if="currentDoc && tocItems.length" class="hidden xl:block fixed right-0 top-0 w-72 h-full overflow-y-auto pt-[68px] border-l border-gray-200 dark:border-white/[0.06] bg-white dark:bg-dark-800">
      <div class="px-6 py-8">
        <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-4">On this page</p>
        <nav class="space-y-1">
          <a
            v-for="item in tocItems"
            :key="item.id"
            :href="`#${item.id}`"
            @click.prevent="scrollToSection(item.id)"
            class="block text-[13px] py-1 transition-colors duration-150 hover:text-brand-violet dark:hover:text-brand-cyan"
            :class="[
              item.level === 2
                ? 'pl-0 font-medium'
                : 'pl-4 text-[12px] border-l border-gray-200 dark:border-white/[0.08] ml-1',
              activeToc === item.id
                ? 'text-brand-violet dark:text-brand-cyan font-semibold'
                : 'text-gray-500 dark:text-gray-400'
            ]"
          >
            {{ item.text }}
          </a>
        </nav>
      </div>
    </aside>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { publicApi } from '@/services/api'
import { marked } from 'marked'
import { markedHighlight } from 'marked-highlight'
import hljs from 'highlight.js'
import DOMPurify from 'dompurify'

// Configure marked to use highlight.js for syntax highlighting
marked.use(markedHighlight({
  langPrefix: 'hljs language-',
  highlight(code, lang) {
    const language = hljs.getLanguage(lang) ? lang : 'plaintext'
    return hljs.highlight(code, { language }).value
  }
}))

// Render content: detect whether it's rich HTML (Tiptap-formatted) or markdown typed as plain text
function renderContent(content) {
  if (!content) return ''
  const trimmed = content.trim()

  if (!trimmed.startsWith('<')) {
    // Plain text — parse as markdown
    return DOMPurify.sanitize(marked.parse(trimmed))
  }

  // If the HTML has actual formatting tags, it's genuine Tiptap rich HTML — use as-is
  const hasRichHtml = /<(strong|em|b|i|h[1-6]|pre|code|ul|ol|li|blockquote|table|thead|tbody|tr|td|th|img|a)\b/i.test(trimmed)
  if (hasRichHtml) {
    return DOMPurify.sanitize(trimmed)
  }

  // Only bare <p> tags wrapping text — extract text preserving paragraph breaks
  const textContent = trimmed
    .replace(/<\/p>\s*<p[^>]*>/gi, '\n\n')
    .replace(/<br\s*\/?>/gi, '\n')
    .replace(/<[^>]+>/g, '')
    .replace(/&nbsp;/g, ' ')
    .replace(/&lt;/g, '<')
    .replace(/&gt;/g, '>')
    .replace(/&amp;/g, '&')
    .replace(/&quot;/g, '"')
    .trim()

  const hasMarkdown = /(\*\*|#{1,6} |`|\[.+\]\(.+\))/.test(textContent)
  if (hasMarkdown) {
    return DOMPurify.sanitize(marked.parse(textContent))
  }

  return DOMPurify.sanitize(trimmed)
}

const route = useRoute()
const router = useRouter()

const navTree = ref([])
const loading = ref(true)
const currentDoc = ref(null)
const docLoading = ref(false)
const currentSlug = ref('')
const searchQuery = ref('')
const sidebarOpen = ref(false)
const tocItems = ref([])
const activeToc = ref('')
const collapsedMenus = ref(new Set())

function toggleMenu(id) {
  if (collapsedMenus.value.has(id)) {
    collapsedMenus.value.delete(id)
  } else {
    collapsedMenus.value.add(id)
  }
  collapsedMenus.value = new Set(collapsedMenus.value)
}

// Flat ordered list of all pages (for prev/next navigation)
const flatDocs = computed(() => {
  const list = []
  for (const mainMenu of navTree.value) {
    for (const page of (mainMenu.pages || [])) {
      list.push(page)
      for (const sub of (page.children || [])) list.push(sub)
    }
    for (const subMenu of (mainMenu.children || [])) {
      for (const page of (subMenu.pages || [])) {
        list.push(page)
        for (const sub of (page.children || [])) list.push(sub)
      }
    }
  }
  return list
})

const filteredNavTree = computed(() => {
  if (!searchQuery.value.trim()) return navTree.value
  const q = searchQuery.value.toLowerCase()
  const matchPage = (p) => p.title.toLowerCase().includes(q) || (p.children || []).some(c => c.title.toLowerCase().includes(q))
  return navTree.value.map(mainMenu => ({
    ...mainMenu,
    pages: (mainMenu.pages || []).filter(matchPage),
    children: (mainMenu.children || []).map(sub => ({
      ...sub,
      pages: (sub.pages || []).filter(matchPage),
    })).filter(sub => sub.pages.length > 0),
  })).filter(m => m.pages.length > 0 || m.children.length > 0)
})

const prevDoc = computed(() => {
  const idx = flatDocs.value.findIndex(d => d.slug === currentSlug.value)
  return idx > 0 ? flatDocs.value[idx - 1] : null
})

const nextDoc = computed(() => {
  const idx = flatDocs.value.findIndex(d => d.slug === currentSlug.value)
  return idx >= 0 && idx < flatDocs.value.length - 1 ? flatDocs.value[idx + 1] : null
})

async function fetchAllDocs() {
  try {
    loading.value = true
    const { data } = await publicApi.getDocsNav()
    navTree.value = data.data || []
    // Expand first main menu by default
    if (navTree.value.length > 0) {
      collapsedMenus.value = new Set([navTree.value[0].id])
    }
  } catch {
    navTree.value = []
  } finally {
    loading.value = false
  }
}

async function loadDoc(slug) {
  if (!slug) return
  currentSlug.value = slug
  router.replace({ name: 'docs', query: { page: slug } })
  sidebarOpen.value = false
  try {
    docLoading.value = true
    currentDoc.value = null
    const { data } = await publicApi.getDoc(slug)
    currentDoc.value = data.data
    await nextTick()
    buildToc()        // initial h2-only pass (instant)
    addHeadingIds()
    addCopyButtons()
    setTimeout(() => buildToc(), 60) // re-run after v-html fully painted to pick up h3s
    window.scrollTo({ top: 0, behavior: 'smooth' })
  } catch {
    currentDoc.value = null
  } finally {
    docLoading.value = false
  }
}

// Convert section heading to a stable DOM id
function sectionAnchor(heading, idx) {
  return heading
    ? heading.toLowerCase().replace(/\s+/g, '-').replace(/[^\w-]/g, '') || `section-${idx}`
    : `section-${idx}`
}

function buildToc() {
  if (currentDoc.value?.sections?.length) {
    // Sections-based TOC: h2 section headings + h3 sub-headings from each section's content
    const items = []
    const proseDocs = document.querySelectorAll('.doc-article .prose-doc')
    currentDoc.value.sections.forEach((s, i) => {
      const sectionId = sectionAnchor(s.heading, i)
      items.push({ id: sectionId, text: s.heading, level: 2 })
      if (proseDocs[i]) {
        proseDocs[i].querySelectorAll('h3').forEach((h3, j) => {
          const id = h3.id || `${sectionId}-sub-${j}`
          h3.id = id
          items.push({ id, text: h3.textContent.trim(), level: 3 })
        })
      }
    })
    tocItems.value = items
  } else {
    // Legacy: auto-extract h2/h3 from rendered HTML
    const container = document.querySelector('.prose-doc')
    if (!container) { tocItems.value = []; return }
    const headings = container.querySelectorAll('h2, h3')
    tocItems.value = Array.from(headings).map((h, i) => {
      const id = h.id || `heading-${i}`
      h.id = id
      return { id, text: h.textContent, level: parseInt(h.tagName[1]) }
    })
  }
}

function scrollToSection(id) {
  const el = document.getElementById(id)
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
  activeToc.value = id
}

function addHeadingIds() {
  if (currentDoc.value?.sections?.length) return // sections use sectionAnchor, already have ids
  const container = document.querySelector('.prose-doc')
  if (!container) return
  container.querySelectorAll('h2, h3').forEach((h, i) => {
    if (!h.id) h.id = `heading-${i}`
  })
}

function addCopyButtons() {
  // Use setTimeout to ensure v-html DOM is fully painted
  setTimeout(() => {
    document.querySelectorAll('.prose-doc pre').forEach(pre => {
      if (pre.parentElement?.classList.contains('code-block-wrapper')) return

      // Detect language from hljs class
      const codeEl = pre.querySelector('code')
      const lang = (codeEl?.className.match(/language-(\w+)/) || [])[1] || ''

      // Wrap pre in a container
      const wrapper = document.createElement('div')
      wrapper.className = 'code-block-wrapper'
      pre.parentNode.insertBefore(wrapper, pre)
      wrapper.appendChild(pre)

      // Header bar with lang label + copy button
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
        const code = codeEl?.innerText || ''
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

function handleScroll() {
  if (currentDoc.value?.sections?.length) {
    // Use section heading anchors for scroll-spy
    const ids = tocItems.value.map(t => t.id)
    let current = ids[0] || ''
    for (const id of ids) {
      const el = document.getElementById(id)
      if (el && el.getBoundingClientRect().top < 120) current = id
    }
    activeToc.value = current
  } else {
    const headings = document.querySelectorAll('.prose-doc h2, .prose-doc h3')
    let current = ''
    headings.forEach(h => {
      if (h.getBoundingClientRect().top < 120) current = h.id
    })
    activeToc.value = current
  }
}

function formatDate(str) {
  if (!str) return ''
  return new Date(str).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

onMounted(async () => {
  await fetchAllDocs()
  const slug = route.query.page
  if (slug) {
    await loadDoc(slug)
  } else if (flatDocs.value.length) {
    await loadDoc(flatDocs.value[0].slug)
  }
  window.addEventListener('scroll', handleScroll, { passive: true })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
/* ── Doc prose styles ── */
.prose-doc {
  color: #374151;
  line-height: 1.75;
  font-size: 15px;
}
:root.dark .prose-doc {
  color: #d1d5db;
}

.prose-doc :deep(h1),
.prose-doc :deep(h2),
.prose-doc :deep(h3),
.prose-doc :deep(h4) {
  font-weight: 700;
  color: #111827;
  scroll-margin-top: 90px;
  line-height: 1.3;
}
:root.dark .prose-doc :deep(h1),
:root.dark .prose-doc :deep(h2),
:root.dark .prose-doc :deep(h3),
:root.dark .prose-doc :deep(h4) {
  color: #f9fafb;
}

.prose-doc :deep(h2) {
  font-size: 1.5rem;
  margin-top: 2.5rem;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #e5e7eb;
}
:root.dark .prose-doc :deep(h2) {
  border-bottom-color: rgba(255,255,255,0.06);
}

.prose-doc :deep(h3) {
  font-size: 1.15rem;
  margin-top: 1.75rem;
  margin-bottom: 0.75rem;
}

.prose-doc :deep(p) {
  margin-bottom: 1.25rem;
}

.prose-doc :deep(a) {
  color: #7b2fff;
  text-decoration: underline;
  text-underline-offset: 2px;
}
:root.dark .prose-doc :deep(a) {
  color: #00d4ff;
}

.prose-doc :deep(ul),
.prose-doc :deep(ol) {
  padding-left: 1.5rem;
  margin-bottom: 1.25rem;
}
.prose-doc :deep(ul) { list-style-type: disc; }
.prose-doc :deep(ol) { list-style-type: decimal; }
.prose-doc :deep(li) { margin-bottom: 0.4rem; }

.prose-doc :deep(code):not(pre code) {
  color: #7b2fff;
  font-family: 'Fira Code', monospace;
  font-size: 0.875em;
}
:root.dark .prose-doc :deep(code):not(pre code) {
  color: #00d4ff;
}

.prose-doc :deep(.code-block-wrapper) {
  margin-bottom: 1.5rem;
}

.prose-doc :deep(pre) {
  background: #0d1117;
  border-radius: 0;
  overflow-x: auto;
  font-family: 'Fira Code', 'Consolas', monospace;
  font-size: 0.875rem;
  line-height: 1.7;
  margin-bottom: 0;
  scrollbar-width: thin;
  scrollbar-color: #30363d #161b22;
}
.prose-doc :deep(pre)::-webkit-scrollbar {
  height: 6px;
}
.prose-doc :deep(pre)::-webkit-scrollbar-track {
  background: #161b22;
}
.prose-doc :deep(pre)::-webkit-scrollbar-thumb {
  background: #30363d;
  border-radius: 3px;
}
.prose-doc :deep(pre)::-webkit-scrollbar-thumb:hover {
  background: #484f58;
}

.prose-doc :deep(pre code.hljs) {
  background: transparent;
  padding: 1.25rem 1.5rem;
  display: block;
}

/* Copy button styles are in the global <style> block below (dynamically injected elements) */

.prose-doc :deep(blockquote) {
  border-left: 3px solid #7b2fff;
  padding: 0.5rem 1rem;
  margin: 1.5rem 0;
  background: #f5f3ff;
  border-radius: 0 8px 8px 0;
  color: #5b21b6;
  font-style: italic;
}
:root.dark .prose-doc :deep(blockquote) {
  border-left-color: #00d4ff;
  background: rgba(0,212,255,0.05);
  color: #a5f3fc;
}

.prose-doc :deep(table) {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 1.5rem;
  font-size: 0.9rem;
}
.prose-doc :deep(th) {
  background: #f9fafb;
  text-align: left;
  padding: 0.6rem 0.8rem;
  font-weight: 600;
  border: 1px solid #e5e7eb;
  color: #374151;
}
:root.dark .prose-doc :deep(th) {
  background: rgba(255,255,255,0.05);
  border-color: rgba(255,255,255,0.06);
  color: #e5e7eb;
}
.prose-doc :deep(td) {
  padding: 0.55rem 0.8rem;
  border: 1px solid #e5e7eb;
  vertical-align: top;
}
:root.dark .prose-doc :deep(td) {
  border-color: rgba(255,255,255,0.06);
}

.prose-doc :deep(hr) {
  border: none;
  border-top: 1px solid #e5e7eb;
  margin: 2rem 0;
}
:root.dark .prose-doc :deep(hr) {
  border-top-color: rgba(255,255,255,0.06);
}

.prose-doc :deep(img) {
  max-width: 100%;
  border-radius: 8px;
  margin: 1rem 0;
}

.doc-article {
  min-height: 60vh;
}

/* Section headings created by the sections system */
.doc-section-h2 {
  scroll-margin-top: 90px;
}
</style>

<!-- Code block styles are in main.css (global) -->
