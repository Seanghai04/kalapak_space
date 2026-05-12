<template>
  <div class="min-h-screen pb-20">
    <div v-if="loading" class="flex justify-center py-32">
      <div class="w-10 h-10 border-4 border-brand-violet/30 border-t-brand-violet rounded-full animate-spin" />
    </div>
    <EmptyState v-else-if="notFound" title="Series not found" message="This series does not exist or has no public page." />
    <template v-else>
      <section class="relative overflow-hidden pt-20 pb-12 sm:pt-24 bg-gradient-to-br from-dark-900 via-dark-800 to-dark-900">
        <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6">
          <router-link :to="`/u/${author.username}`" class="text-sm text-brand-cyan/90 hover:underline mb-4 inline-block">← @{{ author.username }}</router-link>
          <div class="flex flex-wrap items-center gap-3 mb-2">
            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-white/10 text-brand-cyan border border-white/20">Series</span>
            <h1 class="text-2xl sm:text-3xl font-bold text-white">{{ series.name }}</h1>
          </div>
          <p v-if="series.description" class="text-gray-300 text-sm max-w-2xl mt-2">{{ series.description }}</p>
        </div>
      </section>
      <section class="max-w-7xl mx-auto px-4 sm:px-6 -mt-6 relative z-10">
        <div v-if="posts.length" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-7">
          <router-link
            v-for="post in posts"
            :key="post.id"
            :to="`/blog/${post.slug}`"
            class="group flex flex-col rounded-2xl border border-gray-100 dark:border-dark-600 bg-white/80 dark:bg-dark-800/80 backdrop-blur-sm overflow-hidden hover:border-brand-violet/30 dark:hover:border-brand-cyan/30 transition-all hover:-translate-y-1 hover:shadow-xl"
          >
            <div class="relative aspect-[16/10] overflow-hidden bg-gray-100 dark:bg-dark-700">
              <img v-if="post.cover_image" :src="post.cover_image" :alt="post.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
              <div v-else class="w-full h-full bg-gradient-brand opacity-20" />
            </div>
            <div class="p-5 flex flex-col flex-1">
              <p class="text-xs text-gray-400 mb-2">{{ formatDate(post.published_at) }}</p>
              <h2 class="text-base font-bold text-gray-900 dark:text-white line-clamp-2 group-hover:text-brand-violet dark:group-hover:text-brand-cyan">{{ post.title }}</h2>
              <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mt-2 flex-1">{{ post.excerpt }}</p>
            </div>
          </router-link>
        </div>
        <EmptyState v-else title="No posts in this series" message="Posts will appear here when they are published in this series." />
        <div v-if="meta.last_page > 1" class="mt-10">
          <Pagination :current-page="meta.current_page" :last-page="meta.last_page" @page-change="loadPage" />
        </div>
      </section>
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
const payload = ref(null)

const series = computed(() => payload.value?.series || null)
const author = computed(() => payload.value?.author || { username: '', name: '' })
const posts = computed(() => payload.value?.posts || [])
const meta = computed(() => ({
  current_page: payload.value?.meta?.current_page ?? 1,
  last_page: payload.value?.meta?.last_page ?? 1,
}))

function formatDate(d) {
  return d ? dayjs(d).format('MMM D, YYYY') : ''
}

async function loadPage(page = 1) {
  loading.value = true
  notFound.value = false
  const username = String(route.params.username || '').trim()
  const slug = String(route.params.slug || '').trim()
  if (!username || !slug) {
    notFound.value = true
    loading.value = false
    return
  }
  try {
    const { data } = await publicApi.getPublicSeriesPage(username, slug, { page, per_page: 12 })
    payload.value = data.data
    document.title = `${data.data?.series?.name || 'Series'} – @${username} | Kalapak Code Team`
  } catch (e) {
    if (e.response?.status === 404) notFound.value = true
    payload.value = null
    document.title = 'Series – Kalapak Code Team'
  } finally {
    loading.value = false
  }
}

watch(
  () => [route.params.username, route.params.slug],
  () => loadPage(1),
  { immediate: true }
)
</script>
