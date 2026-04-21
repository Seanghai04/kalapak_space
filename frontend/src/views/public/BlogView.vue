<template>
  <div class="blog-page">

    <!-- ═══════════════════ HERO ═══════════════════ -->
    <section class="relative min-h-[45vh] sm:min-h-[50vh] flex items-center justify-center overflow-hidden">
      <div class="absolute inset-0 blog-hero-grid opacity-[0.03] dark:opacity-[0.05]" />
      <div class="absolute top-1/3 -left-20 sm:-left-40 w-[300px] sm:w-[500px] h-[300px] sm:h-[500px] rounded-full bg-brand-violet/15 blur-[80px] sm:blur-[120px] animate-float" />
      <div class="absolute bottom-1/4 -right-20 sm:-right-40 w-[250px] sm:w-[400px] h-[250px] sm:h-[400px] rounded-full bg-brand-cyan/15 blur-[70px] sm:blur-[100px] animate-float" style="animation-delay: 3s" />

      <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center relative z-10">
        <div data-aos="fade-down" data-aos-duration="800" class="inline-flex items-center gap-2 px-4 py-1.5 mb-6 rounded-full border border-brand-violet/20 dark:border-brand-cyan/20 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm">
          <span class="text-xs font-code text-brand-violet dark:text-brand-cyan uppercase tracking-widest">// Blog</span>
        </div>
        <h1 data-aos="fade-up" data-aos-duration="1000" class="text-3xl sm:text-4xl md:text-5xl lg:text-7xl font-sans font-bold mb-4 sm:mb-6 leading-[1.1]">
          <span class="text-gray-900 dark:text-white">Insights &</span>
          <span class="gradient-text"> Stories</span>
        </h1>
        <p data-aos="fade-up" data-aos-delay="200" class="text-base sm:text-lg md:text-xl text-gray-500 dark:text-gray-400 max-w-2xl mx-auto font-sans leading-relaxed px-2 sm:px-0">
          Tutorials, team updates, and thoughts on building software — from the Kalapak team.
        </p>
      </div>
    </section>

    <!-- ═══════════════════ FILTERS ═══════════════════ -->
    <section class="pb-10 relative z-10">
      <div class="max-w-7xl mx-auto px-4">
        <div data-aos="fade-up" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 p-4 rounded-2xl border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm">
          <!-- Search -->
          <div class="relative flex-1">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
            <input
              v-model="search"
              type="text"
              placeholder="Search articles..."
              class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 dark:bg-dark-700 border border-gray-200 dark:border-dark-500 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-violet/30 dark:focus:ring-brand-cyan/30 focus:border-brand-violet dark:focus:border-brand-cyan transition-all duration-300"
              @input="debouncedFetch"
            />
          </div>
          <!-- Category filter -->
          <div class="w-full sm:w-48">
            <CustomSelect
              v-model="selectedCategory"
              :options="[{ label: 'All Categories', value: '' }, ...categories.map(c => ({ label: c.name, value: c.slug }))]"
              placeholder="All Categories"
              @change="fetchPosts"
            />
          </div>
          <!-- Clear -->
          <button v-if="search || selectedCategory" @click="clearFilters"
            class="px-4 py-3 rounded-xl text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan hover:bg-brand-violet/5 dark:hover:bg-brand-cyan/5 transition-all duration-300 whitespace-nowrap">
            Clear
          </button>
        </div>
        <!-- Category pills -->
        <div v-if="categories.length" data-aos="fade-up" class="flex flex-wrap items-center gap-2 mt-4 px-1">
          <button
            v-for="cat in categories" :key="cat.id"
            @click="selectedCategory = selectedCategory === cat.slug ? '' : cat.slug; fetchPosts()"
            class="px-3 py-1.5 rounded-full text-xs font-medium border transition-all duration-300"
            :class="selectedCategory === cat.slug
              ? 'bg-brand-violet text-white border-brand-violet dark:bg-brand-cyan dark:border-brand-cyan dark:text-dark-900'
              : 'border-gray-200 dark:border-dark-500 text-gray-600 dark:text-gray-400 hover:border-brand-violet/40 dark:hover:border-brand-cyan/40 hover:text-brand-violet dark:hover:text-brand-cyan'">
            {{ cat.name }}
          </button>
        </div>
        <p v-if="!loading" class="text-xs text-gray-400 dark:text-gray-500 mt-3 px-1">
          {{ posts.length }} {{ posts.length === 1 ? 'article' : 'articles' }}
          <span v-if="search || selectedCategory"> matching your filters</span>
        </p>
      </div>
    </section>

    <!-- ═══════════════════ FEATURED POST ═══════════════════ -->
    <section v-if="featuredPost && !search && !selectedCategory && meta.current_page === 1" class="pb-16 relative z-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <router-link :to="`/blog/${featuredPost.slug}`" data-aos="fade-up"
          class="group block relative rounded-2xl sm:rounded-3xl overflow-hidden border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm hover:shadow-2xl dark:hover:shadow-glow/10 transition-all duration-500">
          <div class="grid lg:grid-cols-2 gap-0">
            <!-- Image -->
            <div class="relative aspect-video lg:aspect-auto lg:min-h-[400px] overflow-hidden">
              <img v-if="featuredPost.cover_image" :src="featuredPost.cover_image" :alt="featuredPost.title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
              <div v-else class="w-full h-full bg-gradient-brand opacity-20" />
              <div class="absolute top-4 left-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-brand-violet/90 dark:bg-brand-cyan/90 text-white text-xs font-bold uppercase tracking-wider backdrop-blur-sm">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                Featured
              </div>
            </div>
            <!-- Content -->
            <div class="p-8 md:p-10 lg:p-12 flex flex-col justify-center">
              <div class="flex flex-wrap items-center gap-3 mb-4 text-xs">
                <span v-if="featuredPost.category" class="px-2.5 py-1 rounded-full font-medium bg-brand-violet/10 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan">
                  {{ featuredPost.category.name }}
                </span>
                <span class="text-gray-400 dark:text-gray-500">{{ formatDate(featuredPost.published_at) }}</span>
                <span v-if="featuredPost.reading_time" class="text-gray-400 dark:text-gray-500">· {{ featuredPost.reading_time }} min read</span>
              </div>
              <h2 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 dark:text-white mb-4 group-hover:text-brand-violet dark:group-hover:text-brand-cyan transition-colors leading-snug">
                {{ featuredPost.title }}
              </h2>
              <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-6 line-clamp-3">{{ featuredPost.excerpt }}</p>
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-full overflow-hidden ring-2 ring-gray-100 dark:ring-dark-600">
                    <img v-if="featuredPost.author?.avatar" :src="featuredPost.author.avatar" :alt="featuredPost.author.name" class="w-full h-full object-cover" />
                    <div v-else class="w-full h-full bg-gradient-brand flex items-center justify-center text-white text-sm font-bold">{{ featuredPost.author?.name?.charAt(0) }}</div>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ featuredPost.author?.name }}</p>
                    <p class="text-[10px] text-gray-400 uppercase tracking-wider">Author</p>
                  </div>
                </div>
                <span class="inline-flex items-center gap-1.5 text-sm font-medium text-brand-violet dark:text-brand-cyan group-hover:translate-x-1 transition-transform duration-300">
                  Read Article
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </span>
              </div>
            </div>
          </div>
        </router-link>
      </div>
    </section>

    <!-- ═══════════════════ POSTS GRID ═══════════════════ -->
    <section class="pb-24 relative z-10">
      <div class="max-w-7xl mx-auto px-4">
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
        <div v-else-if="displayPosts.length" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-7">
          <router-link v-for="(post, i) in displayPosts" :key="post.id" :to="`/blog/${post.slug}`"
            data-aos="fade-up" :data-aos-delay="(i % 3) * 80"
            class="group flex flex-col rounded-2xl border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm overflow-hidden hover:border-brand-violet/30 dark:hover:border-brand-cyan/30 transition-all duration-500 hover:-translate-y-2 hover:shadow-xl dark:hover:shadow-glow/10">
            <!-- Cover image -->
            <div class="relative aspect-[16/10] overflow-hidden bg-gray-100 dark:bg-dark-700">
              <img v-if="post.cover_image" :src="post.cover_image" :alt="post.title"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
              <div v-else class="w-full h-full flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-300 dark:text-dark-500" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6V7.5z"/></svg>
              </div>
              <!-- Category badge on image -->
              <div v-if="post.category" class="absolute top-3 left-3">
                <span class="px-2.5 py-1 text-[10px] rounded-full font-bold uppercase tracking-wider bg-white/90 dark:bg-dark-800/90 text-brand-violet dark:text-brand-cyan backdrop-blur-sm">
                  {{ post.category.name }}
                </span>
              </div>
              <!-- Reading time -->
              <div v-if="post.reading_time" class="absolute top-3 right-3">
                <span class="px-2 py-1 text-[10px] rounded-full font-medium bg-black/40 text-white backdrop-blur-sm">
                  {{ post.reading_time }} min
                </span>
              </div>
            </div>

            <!-- Card body -->
            <div class="flex flex-col flex-1 p-6">
              <!-- Meta -->
              <div class="flex items-center gap-2 text-xs text-gray-400 dark:text-gray-500 mb-3">
                <span>{{ formatDate(post.published_at) }}</span>
                <span v-if="post.views_count">· {{ post.views_count }} views</span>
              </div>

              <!-- Title -->
              <h3 class="text-lg font-sans font-bold text-gray-900 dark:text-white mb-2 group-hover:text-brand-violet dark:group-hover:text-brand-cyan transition-colors leading-snug line-clamp-2">
                {{ post.title }}
              </h3>

              <!-- Excerpt -->
              <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed line-clamp-2 mb-5 flex-1">
                {{ post.excerpt }}
              </p>

              <!-- Footer -->
              <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-dark-600">
                <div class="flex items-center gap-2.5">
                  <div class="w-7 h-7 rounded-full overflow-hidden ring-1 ring-gray-100 dark:ring-dark-600">
                    <img v-if="post.author?.avatar" :src="post.author.avatar" :alt="post.author.name" class="w-full h-full object-cover" />
                    <div v-else class="w-full h-full bg-gradient-brand flex items-center justify-center text-white text-[10px] font-bold">{{ post.author?.name?.charAt(0) }}</div>
                  </div>
                  <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ post.author?.name }}</span>
                </div>
                <span class="text-xs font-medium text-brand-violet dark:text-brand-cyan group-hover:translate-x-1 transition-transform duration-300 flex items-center gap-1">
                  Read
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </span>
              </div>
            </div>

            <!-- Hover gradient bottom bar -->
            <div class="h-[2px] w-full bg-gradient-brand opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
          </router-link>
        </div>

        <!-- Empty state -->
        <EmptyState v-else title="No articles found" message="Try adjusting your search or filter." />

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
import dayjs from 'dayjs'
import Pagination from '@/components/common/Pagination.vue'
import EmptyState from '@/components/common/EmptyState.vue'
import CustomSelect from '@/components/common/CustomSelect.vue'

const posts = ref([])
const categories = ref([])
const loading = ref(true)
const search = ref('')
const selectedCategory = ref('')
const meta = ref({ current_page: 1, last_page: 1 })

const featuredPost = computed(() => posts.value.find(p => p.is_featured))
const displayPosts = computed(() => {
  if (search.value || selectedCategory.value || meta.value.current_page > 1) return posts.value
  return posts.value.filter(p => !p.is_featured)
})

let debounceTimer = null

function debouncedFetch() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(fetchPosts, 300)
}

function formatDate(date) {
  return date ? dayjs(date).format('MMM D, YYYY') : ''
}

function clearFilters() {
  search.value = ''
  selectedCategory.value = ''
  fetchPosts()
}

async function fetchPosts(page = 1) {
  loading.value = true
  try {
    const { data } = await publicApi.getBlogPosts({
      page: typeof page === 'number' ? page : 1,
      per_page: 9,
      search: search.value || undefined,
      category: selectedCategory.value || undefined,
    })
    posts.value = data.data || []
    meta.value = data.meta || { current_page: 1, last_page: 1 }
  } catch {
    posts.value = []
  } finally {
    loading.value = false
  }
}

function goToPage(page) {
  fetchPosts(page)
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(async () => {
  try {
    const { data } = await publicApi.getBlogCategories()
    categories.value = data.data || []
  } catch {}
  fetchPosts()
})
</script>

<style scoped>
.blog-hero-grid {
  background-image:
    linear-gradient(rgba(123, 47, 255, 0.1) 1px, transparent 1px),
    linear-gradient(90deg, rgba(123, 47, 255, 0.1) 1px, transparent 1px);
  background-size: 60px 60px;
}
</style>
