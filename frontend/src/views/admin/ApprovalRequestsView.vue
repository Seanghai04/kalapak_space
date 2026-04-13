<template>
  <div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-sans font-bold dark:text-white">Approval Requests</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Review and approve or reject pending admin actions</p>
      </div>
      <div class="flex items-center gap-2">
        <span v-if="pendingCount > 0" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">
          <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse" />
          {{ pendingCount }} pending
        </span>
        <button @click="fetchRequests" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-700 transition-colors">
          <svg class="w-4 h-4" :class="loading ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
          Refresh
        </button>
      </div>
    </div>

    <!-- Tabs -->
    <div class="glass-card !p-1 flex gap-1 mb-6 w-fit">
      <button
        v-for="tab in tabs"
        :key="tab.value"
        @click="activeTab = tab.value; fetchRequests()"
        class="px-4 py-2 text-sm font-medium rounded-lg transition-all whitespace-nowrap"
        :class="activeTab === tab.value
          ? 'bg-brand-violet/10 dark:bg-brand-cyan/10 text-brand-violet dark:text-brand-cyan shadow-sm'
          : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-dark-700'"
      >
        {{ tab.label }}
        <span v-if="tab.value === 'pending' && pendingCount > 0" class="ml-1.5 text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">
          {{ pendingCount }}
        </span>
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="w-10 h-10 border-4 border-brand-violet/30 border-t-brand-violet rounded-full animate-spin" />
    </div>

    <!-- Empty State -->
    <div v-else-if="!requests.length" class="glass-card flex flex-col items-center justify-center py-20 text-center">
      <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-dark-700 flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
      </div>
      <p class="text-gray-500 dark:text-gray-400 font-medium">No {{ activeTab === 'pending' ? 'pending' : '' }} requests</p>
      <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">{{ activeTab === 'pending' ? 'All caught up!' : 'No requests have been made yet.' }}</p>
    </div>

    <!-- Requests List -->
    <div v-else class="space-y-3">
      <div
        v-for="req in requests"
        :key="req.id"
        class="glass-card !p-4 flex flex-col sm:flex-row sm:items-center gap-4"
      >
        <!-- Action badge + Subject -->
        <div class="flex items-center gap-3 flex-1 min-w-0">
          <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center" :class="actionStyle(req.action).bg">
            <svg class="w-5 h-5" :class="actionStyle(req.action).text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="req.action === 'create'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              <path v-else-if="req.action === 'update'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </div>
          <div class="min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
              <span class="text-xs font-bold uppercase tracking-wide px-1.5 py-0.5 rounded" :class="actionStyle(req.action).badge">
                {{ req.action }}
              </span>
              <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ req.subject_type }}</span>
              <span v-if="req.subject_label" class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                "{{ req.subject_label }}"
              </span>
            </div>
            <div class="flex items-center gap-2 mt-1">
              <span class="text-xs text-gray-400 dark:text-gray-500">by</span>
              <span class="text-xs font-medium text-gray-600 dark:text-gray-300">{{ req.requester?.name || 'Unknown' }}</span>
              <span class="text-xs text-gray-400">·</span>
              <span class="text-xs text-gray-400 dark:text-gray-500">{{ formatDate(req.created_at) }}</span>
            </div>
          </div>
        </div>

        <!-- Status / Actions -->
        <div class="flex items-center gap-2 flex-shrink-0">
          <!-- Pending: approve/reject buttons -->
          <template v-if="req.status === 'pending'">
            <button
              @click="openApprove(req)"
              :disabled="processing === req.id"
              class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-900/30 border border-green-200 dark:border-green-800/40 transition-colors disabled:opacity-50"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              Approve
            </button>
            <button
              @click="openReject(req)"
              :disabled="processing === req.id"
              class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 border border-red-200 dark:border-red-800/40 transition-colors disabled:opacity-50"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              Reject
            </button>
          </template>
          <!-- Resolved: status pill -->
          <template v-else>
            <span
              class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
              :class="req.status === 'approved' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'"
            >
              <svg v-if="req.status === 'approved'" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              <svg v-else class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
              {{ req.status }}
            </span>
            <span v-if="req.reviewer" class="text-xs text-gray-400 dark:text-gray-500">by {{ req.reviewer.name }}</span>
          </template>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="meta.last_page > 1" class="flex items-center justify-center gap-2 mt-6">
      <button
        :disabled="meta.current_page === 1"
        @click="page--; fetchRequests()"
        class="px-3 py-1.5 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
      >← Prev</button>
      <span class="text-sm text-gray-500 dark:text-gray-400">{{ meta.current_page }} / {{ meta.last_page }}</span>
      <button
        :disabled="meta.current_page === meta.last_page"
        @click="page++; fetchRequests()"
        class="px-3 py-1.5 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
      >Next →</button>
    </div>

    <!-- Approve Modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0" leave-active-class="transition duration-150" leave-to-class="opacity-0">
        <div v-if="approveModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="approveModal = false">
          <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-2xl w-full max-w-md p-6">
            <div class="flex items-center gap-3 mb-4">
              <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Approve Request</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">This will apply the action immediately.</p>
              </div>
            </div>
            <div class="mb-4 p-3 rounded-xl bg-gray-50 dark:bg-dark-700">
              <p class="text-sm text-gray-600 dark:text-gray-300">
                <span class="font-semibold">{{ selectedReq?.action }}</span> action on
                <span class="font-semibold">{{ selectedReq?.subject_type }}</span>
                <span v-if="selectedReq?.subject_label"> "{{ selectedReq.subject_label }}"</span>
                by <span class="font-semibold">{{ selectedReq?.requester?.name }}</span>
              </p>
            </div>
            <div class="mb-5">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Note (optional)</label>
              <textarea v-model="reviewNote" rows="2" class="input-field resize-none" placeholder="Add a note..." />
            </div>
            <div class="flex gap-2 justify-end">
              <button @click="approveModal = false" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-700 transition-colors">Cancel</button>
              <button @click="confirmApprove" :disabled="processing" class="btn-primary text-sm flex items-center gap-2">
                <svg v-if="processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                {{ processing ? 'Approving...' : 'Approve' }}
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Reject Modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0" leave-active-class="transition duration-150" leave-to-class="opacity-0">
        <div v-if="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="rejectModal = false">
          <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-2xl w-full max-w-md p-6">
            <div class="flex items-center gap-3 mb-4">
              <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Reject Request</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">The action will not be applied.</p>
              </div>
            </div>
            <div class="mb-4 p-3 rounded-xl bg-gray-50 dark:bg-dark-700">
              <p class="text-sm text-gray-600 dark:text-gray-300">
                <span class="font-semibold">{{ selectedReq?.action }}</span> action on
                <span class="font-semibold">{{ selectedReq?.subject_type }}</span>
                <span v-if="selectedReq?.subject_label"> "{{ selectedReq.subject_label }}"</span>
                by <span class="font-semibold">{{ selectedReq?.requester?.name }}</span>
              </p>
            </div>
            <div class="mb-5">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Reason for rejection <span class="text-red-500">*</span></label>
              <textarea v-model="reviewNote" rows="2" class="input-field resize-none" placeholder="Provide a reason..." />
              <p v-if="noteError" class="text-xs text-red-500 mt-1">{{ noteError }}</p>
            </div>
            <div class="flex gap-2 justify-end">
              <button @click="rejectModal = false" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-700 transition-colors">Cancel</button>
              <button @click="confirmReject" :disabled="processing" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-red-600 hover:bg-red-700 text-white transition-colors disabled:opacity-50">
                <svg v-if="processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                {{ processing ? 'Rejecting...' : 'Reject' }}
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { adminApi } from '@/services/api'
import { useUiStore } from '@/stores/ui'

const uiStore = useUiStore()

const requests = ref([])
const loading = ref(false)
const processing = ref(null)
const activeTab = ref('pending')
const page = ref(1)
const meta = ref({ current_page: 1, last_page: 1, per_page: 20, total: 0 })
const pendingCount = ref(0)

const approveModal = ref(false)
const rejectModal = ref(false)
const selectedReq = ref(null)
const reviewNote = ref('')
const noteError = ref('')

const tabs = [
  { label: 'Pending', value: 'pending' },
  { label: 'All', value: 'all' },
]

function actionStyle(action) {
  if (action === 'create') return { bg: 'bg-green-100 dark:bg-green-900/20', text: 'text-green-600 dark:text-green-400', badge: 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' }
  if (action === 'update') return { bg: 'bg-blue-100 dark:bg-blue-900/20', text: 'text-blue-600 dark:text-blue-400', badge: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' }
  return { bg: 'bg-red-100 dark:bg-red-900/20', text: 'text-red-600 dark:text-red-400', badge: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }
}

function formatDate(dateStr) {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

async function fetchRequests() {
  loading.value = true
  try {
    const { data } = await adminApi.getApprovalRequests({ status: activeTab.value, page: page.value })
    requests.value = data.data || []
    meta.value = data.meta || meta.value
  } catch {
    uiStore.showToast('Failed to load approval requests', 'error')
  } finally {
    loading.value = false
  }
}

async function fetchPendingCount() {
  try {
    const { data } = await adminApi.getApprovalRequests({ status: 'pending' })
    pendingCount.value = data.meta?.total || 0
  } catch {}
}

function openApprove(req) {
  selectedReq.value = req
  reviewNote.value = ''
  approveModal.value = true
}

function openReject(req) {
  selectedReq.value = req
  reviewNote.value = ''
  noteError.value = ''
  rejectModal.value = true
}

async function confirmApprove() {
  if (!selectedReq.value) return
  processing.value = selectedReq.value.id
  try {
    await adminApi.approveRequest(selectedReq.value.id, { note: reviewNote.value })
    uiStore.showToast('Request approved successfully!')
    approveModal.value = false
    await fetchRequests()
    await fetchPendingCount()
  } catch (err) {
    uiStore.showToast(err.response?.data?.message || 'Failed to approve request', 'error')
  } finally {
    processing.value = null
  }
}

async function confirmReject() {
  if (!selectedReq.value) return
  if (!reviewNote.value.trim()) {
    noteError.value = 'A reason is required when rejecting.'
    return
  }
  noteError.value = ''
  processing.value = selectedReq.value.id
  try {
    await adminApi.rejectRequest(selectedReq.value.id, { note: reviewNote.value })
    uiStore.showToast('Request rejected.')
    rejectModal.value = false
    await fetchRequests()
    await fetchPendingCount()
  } catch (err) {
    uiStore.showToast(err.response?.data?.message || 'Failed to reject request', 'error')
  } finally {
    processing.value = null
  }
}

onMounted(async () => {
  await fetchRequests()
  pendingCount.value = meta.value.total
})
</script>
