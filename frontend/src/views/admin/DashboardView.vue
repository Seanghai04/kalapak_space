<template>
  <div>
    <h1 class="text-2xl font-sans font-bold dark:text-white mb-8">Dashboard</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div v-for="(stat, i) in statsCards" :key="i" class="glass-card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ stat.label }}</p>
            <p class="text-2xl font-sans font-bold dark:text-white mt-1">{{ stat.value }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl" :class="stat.bgClass">
            {{ stat.icon }}
          </div>
        </div>
      </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-8">
      <!-- Chart -->
      <div class="glass-card">
        <h3 class="text-lg font-sans font-bold dark:text-white mb-4">Overview</h3>
        <div class="h-64">
          <Bar v-if="chartData" :data="chartData" :options="chartOptions" />
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="glass-card">
        <h3 class="text-lg font-sans font-bold dark:text-white mb-4">Recent Activity</h3>
        <div class="space-y-4 max-h-64 overflow-y-auto scrollbar-thin">
          <div v-for="activity in recentActivity" :key="activity.id" class="flex items-start gap-3 text-sm">
            <div class="w-8 h-8 rounded-full bg-brand-violet/10 dark:bg-brand-cyan/10 flex items-center justify-center text-brand-violet dark:text-brand-cyan shrink-0">
              {{ activity.user?.name?.charAt(0) || '?' }}
            </div>
            <div>
              <p class="dark:text-gray-300">
                <span class="font-medium">{{ activity.user?.name }}</span>
                {{ activity.action }} {{ activity.target_type }}
              </p>
              <p class="text-xs text-gray-400">{{ formatDate(activity.created_at) }}</p>
            </div>
          </div>
          <p v-if="!recentActivity.length" class="text-gray-400 text-sm">No recent activity.</p>
        </div>
      </div>
    </div>

    <!-- Storage Usage (superadmin only; defer until client mounted — matches SSR without user) -->
    <div v-if="dashboardMounted && authStore.isSuperAdmin" class="mt-8">
      <StorageStats />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend } from 'chart.js'
import { adminApi } from '@/services/api'
import { useAuthStore } from '@/stores/auth'
import StorageStats from '@/components/admin/StorageStats.vue'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend)
dayjs.extend(relativeTime)

const authStore = useAuthStore()

/** Avoid SSR vs client mismatch for role-gated blocks (Pinia user from localStorage is client-only). */
const dashboardMounted = ref(false)

const statsCards = ref([
  { label: 'Total Users', value: 0, icon: '👥', bgClass: 'bg-blue-100 dark:bg-blue-900/30' },
  { label: 'Projects', value: 0, icon: '📁', bgClass: 'bg-green-100 dark:bg-green-900/30' },
  { label: 'Blog Posts', value: 0, icon: '📝', bgClass: 'bg-purple-100 dark:bg-purple-900/30' },
  { label: 'Messages', value: 0, icon: '✉️', bgClass: 'bg-yellow-100 dark:bg-yellow-900/30' },
])

const recentActivity = ref([])
const chartData = ref(null)

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } },
  scales: {
    y: { beginAtZero: true, grid: { color: 'rgba(123, 47, 255, 0.1)' } },
    x: { grid: { display: false } },
  },
}

function formatDate(date) {
  return dayjs(date).fromNow()
}

onMounted(async () => {
  dashboardMounted.value = true
  try {
    const [statsRes, activityRes] = await Promise.all([
      adminApi.getDashboardStats(),
      adminApi.getDashboardActivity(),
    ])
    const stats = statsRes.data?.data || {}
    statsCards.value[0].value = stats.total_users || 0
    statsCards.value[1].value = stats.total_projects || 0
    statsCards.value[2].value = stats.total_blog_posts || 0
    statsCards.value[3].value = stats.unread_messages || 0
    recentActivity.value = activityRes.data?.data || []

    chartData.value = {
      labels: ['Users', 'Projects', 'Posts', 'Messages', 'Applications'],
      datasets: [
        {
          label: 'Count',
          data: [stats.total_users || 0, stats.total_projects || 0, stats.total_blog_posts || 0, stats.unread_messages || 0, stats.pending_applications || 0],
          backgroundColor: ['#7b2fff', '#00d4ff', '#9d5fff', '#33ddff', '#5a1fd4'],
          borderRadius: 8,
        },
      ],
    }
  } catch {
    // ignore
  }
})
</script>
