<template>
  <div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-sans font-bold dark:text-white">Reorder Docs</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
          Drag to reorder Main Menus, Sub-Menus, and Pages. Items can move between groups.
        </p>
      </div>
      <div class="flex items-center gap-3">
        <router-link :to="{ name: 'admin-docs' }" class="btn-ghost text-sm">Back to Docs</router-link>
        <button
          @click="saveOrder"
          :disabled="saving || loading || !isDirty"
          class="btn-primary text-sm flex items-center gap-2 disabled:opacity-50"
        >
          <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
          </svg>
          <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          {{ saving ? 'Saving...' : 'Save Order' }}
        </button>
      </div>
    </div>

    <!-- Toast -->
    <transition name="toast-fade">
      <div
        v-if="toast.show"
        :class="['fixed top-5 right-5 z-50 flex items-center gap-2.5 px-4 py-3 rounded-xl shadow-2xl text-sm font-medium pointer-events-none',
          toast.type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white']"
      >
        <svg v-if="toast.type === 'success'" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <svg v-else class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        {{ toast.message }}
      </div>
    </transition>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="w-10 h-10 border-4 border-brand-violet/30 border-t-brand-violet rounded-full animate-spin" />
    </div>

    <!-- Empty -->
    <div v-else-if="!mainMenus.length && !unassigned.length" class="glass-card flex flex-col items-center justify-center py-20 text-center">
      <p class="text-gray-400">No menus or docs found. Create a Main Menu first.</p>
    </div>

    <template v-else>
      <!-- ── Main Menus (draggable) ── -->
      <draggable
        v-model="mainMenus"
        item-key="id"
        handle=".mm-grip"
        :animation="200"
        ghost-class="ring-2 ring-brand-violet opacity-40"
        chosen-class="scale-[1.005] shadow-xl"
        class="space-y-4"
      >
        <template #item="{ element: mm, index: mmIdx }">
          <div class="rounded-xl border border-brand-violet/30 bg-white dark:bg-dark-800/60 overflow-hidden shadow-sm">

            <!-- Main Menu header -->
            <div class="flex items-center gap-2 px-3 py-2.5 bg-brand-violet/10 select-none border-b border-brand-violet/20">
              <!-- Drag grip -->
              <div class="mm-grip shrink-0 flex items-center justify-center w-7 h-7 rounded-md
                          text-brand-violet/60 hover:text-brand-violet hover:bg-brand-violet/10
                          cursor-grab active:cursor-grabbing transition-colors" title="Drag main menu">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16">
                  <circle cx="5" cy="3" r="1.2"/><circle cx="11" cy="3" r="1.2"/>
                  <circle cx="5" cy="8" r="1.2"/><circle cx="11" cy="8" r="1.2"/>
                  <circle cx="5" cy="13" r="1.2"/><circle cx="11" cy="13" r="1.2"/>
                </svg>
              </div>
              <!-- Up/Down -->
              <div class="flex flex-col shrink-0">
                <button @click="moveItem(mainMenus, mmIdx, -1)" :disabled="mmIdx === 0"
                  class="p-0.5 rounded text-brand-violet/50 hover:text-brand-violet hover:bg-brand-violet/10
                         disabled:opacity-20 disabled:cursor-not-allowed transition-colors leading-none">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/>
                  </svg>
                </button>
                <button @click="moveItem(mainMenus, mmIdx, 1)" :disabled="mmIdx === mainMenus.length - 1"
                  class="p-0.5 rounded text-brand-violet/50 hover:text-brand-violet hover:bg-brand-violet/10
                         disabled:opacity-20 disabled:cursor-not-allowed transition-colors leading-none">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/>
                  </svg>
                </button>
              </div>
              <!-- Badge -->
              <span class="shrink-0 text-[10px] font-bold uppercase tracking-wide px-1.5 py-0.5 rounded
                           bg-brand-violet/20 text-brand-violet">Main</span>
              <!-- Name -->
              <span class="font-semibold text-sm text-gray-800 dark:text-white flex-1 truncate">{{ mm.name }}</span>
              <!-- Counts -->
              <span class="shrink-0 text-xs text-gray-400">
                {{ mm.subMenus.length }} sub · {{ mm.docs.length }} direct
              </span>
              <!-- Position -->
              <span class="shrink-0 text-xs font-mono text-gray-300 dark:text-gray-600 w-5 text-right">{{ mmIdx + 1 }}</span>
            </div>

            <div class="p-2 space-y-2">

              <!-- ── Sub-Menus (draggable across main menus) ── -->
              <draggable
                v-model="mm.subMenus"
                item-key="id"
                handle=".sm-grip"
                group="kalapak-submenus"
                :animation="150"
                ghost-class="ring-2 ring-brand-cyan/50 opacity-40"
                chosen-class="scale-[1.005]"
                class="space-y-1.5"
              >
                <template #item="{ element: sm, index: smIdx }">
                  <div class="rounded-lg border border-brand-cyan/20 bg-white dark:bg-dark-700/40 overflow-hidden">
                    <!-- Sub-Menu header -->
                    <div class="flex items-center gap-2 px-2.5 py-2 bg-brand-cyan/5 select-none border-b border-brand-cyan/10">
                      <!-- Drag grip -->
                      <div class="sm-grip shrink-0 flex items-center justify-center w-6 h-6 rounded
                                  text-brand-cyan/50 hover:text-brand-cyan hover:bg-brand-cyan/10
                                  cursor-grab active:cursor-grabbing transition-colors" title="Drag sub-menu">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 16 16">
                          <circle cx="5" cy="3" r="1.2"/><circle cx="11" cy="3" r="1.2"/>
                          <circle cx="5" cy="8" r="1.2"/><circle cx="11" cy="8" r="1.2"/>
                          <circle cx="5" cy="13" r="1.2"/><circle cx="11" cy="13" r="1.2"/>
                        </svg>
                      </div>
                      <!-- Up/Down -->
                      <div class="flex flex-col shrink-0">
                        <button @click="moveItem(mm.subMenus, smIdx, -1)" :disabled="smIdx === 0"
                          class="p-0.5 rounded text-brand-cyan/50 hover:text-brand-cyan hover:bg-brand-cyan/10
                                 disabled:opacity-20 disabled:cursor-not-allowed transition-colors leading-none">
                          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/>
                          </svg>
                        </button>
                        <button @click="moveItem(mm.subMenus, smIdx, 1)" :disabled="smIdx === mm.subMenus.length - 1"
                          class="p-0.5 rounded text-brand-cyan/50 hover:text-brand-cyan hover:bg-brand-cyan/10
                                 disabled:opacity-20 disabled:cursor-not-allowed transition-colors leading-none">
                          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/>
                          </svg>
                        </button>
                      </div>
                      <!-- Badge -->
                      <span class="shrink-0 text-[10px] font-bold uppercase tracking-wide px-1.5 py-0.5 rounded
                                   bg-brand-cyan/15 text-brand-cyan">Sub</span>
                      <!-- Name -->
                      <span class="font-medium text-sm text-gray-700 dark:text-gray-200 flex-1 truncate">{{ sm.name }}</span>
                      <!-- Doc count -->
                      <span class="shrink-0 text-xs text-gray-400">{{ sm.docs.length }} pages</span>
                      <!-- Position -->
                      <span class="shrink-0 text-xs font-mono text-gray-300 dark:text-gray-600 w-5 text-right">{{ smIdx + 1 }}</span>
                    </div>

                    <!-- Docs under this sub-menu -->
                    <draggable
                      v-model="sm.docs"
                      item-key="id"
                      handle=".doc-grip"
                      group="kalapak-docs"
                      :animation="120"
                      ghost-class="bg-green-500/10 opacity-50"
                      class="divide-y divide-gray-100 dark:divide-dark-600 min-h-[2rem]"
                    >
                      <template #item="{ element: doc, index: docIdx }">
                        <DocRow
                          :doc="doc"
                          :idx="docIdx"
                          :total="sm.docs.length"
                          @move="moveItem(sm.docs, docIdx, $event)"
                        />
                      </template>
                      <template #footer>
                        <div v-if="sm.docs.length === 0"
                          class="px-4 py-3 text-center text-xs text-gray-400 italic">
                          Drop pages here
                        </div>
                      </template>
                    </draggable>
                  </div>
                </template>

                <!-- Empty sub-menu drop zone -->
                <template #footer>
                  <div v-if="mm.subMenus.length === 0"
                    class="px-4 py-2 text-center text-xs text-gray-400 italic border border-dashed border-gray-200 dark:border-dark-600 rounded-lg">
                    Drop sub-menus here
                  </div>
                </template>
              </draggable>

              <!-- ── Docs directly under this Main Menu ── -->
              <div v-if="mm.docs.length > 0 || true" class="rounded-lg border border-gray-200 dark:border-dark-600 overflow-hidden">
                <div class="px-2.5 py-1.5 bg-gray-50 dark:bg-white/[0.03] border-b border-gray-100 dark:border-dark-600">
                  <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Direct Pages</span>
                </div>
                <draggable
                  v-model="mm.docs"
                  item-key="id"
                  handle=".doc-grip"
                  group="kalapak-docs"
                  :animation="120"
                  ghost-class="bg-green-500/10 opacity-50"
                  class="divide-y divide-gray-100 dark:divide-dark-600 min-h-[2rem]"
                >
                  <template #item="{ element: doc, index: docIdx }">
                    <DocRow
                      :doc="doc"
                      :idx="docIdx"
                      :total="mm.docs.length"
                      @move="moveItem(mm.docs, docIdx, $event)"
                    />
                  </template>
                  <template #footer>
                    <div v-if="mm.docs.length === 0"
                      class="px-4 py-3 text-center text-xs text-gray-400 italic">
                      Drop pages directly here
                    </div>
                  </template>
                </draggable>
              </div>

            </div>
          </div>
        </template>
      </draggable>

      <!-- ── Unassigned Docs ── -->
      <div v-if="unassigned.length > 0" class="mt-4 rounded-xl border border-yellow-400/30 bg-white dark:bg-dark-800/40 overflow-hidden shadow-sm">
        <div class="px-3 py-2.5 bg-yellow-400/10 border-b border-yellow-400/20 select-none">
          <span class="text-sm font-semibold text-yellow-600 dark:text-yellow-400">Unassigned Pages</span>
          <span class="ml-2 text-xs text-gray-400">Drag these into a menu above</span>
        </div>
        <draggable
          v-model="unassigned"
          item-key="id"
          handle=".doc-grip"
          group="kalapak-docs"
          :animation="120"
          ghost-class="bg-green-500/10 opacity-50"
          class="divide-y divide-gray-100 dark:divide-dark-600 min-h-[2rem]"
        >
          <template #item="{ element: doc, index: docIdx }">
            <DocRow
              :doc="doc"
              :idx="docIdx"
              :total="unassigned.length"
              @move="moveItem(unassigned, docIdx, $event)"
            />
          </template>
        </draggable>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, defineComponent, h } from 'vue'
import draggable from 'vuedraggable'
import { adminApi } from '@/services/api'

// ── Inline DocRow component ───────────────────────────────────────────────────
const DocRow = defineComponent({
  props: { doc: Object, idx: Number, total: Number },
  emits: ['move'],
  setup(props, { emit }) {
    return () => h('div', {
      class: 'flex items-center gap-2 px-2.5 py-2 hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors select-none'
    }, [
      // Drag grip
      h('div', {
        class: 'doc-grip shrink-0 flex items-center justify-center w-6 h-6 rounded text-gray-300 dark:text-gray-600 hover:text-green-500 hover:bg-green-500/10 cursor-grab active:cursor-grabbing transition-colors',
        title: 'Drag page'
      }, [
        h('svg', { class: 'w-3.5 h-3.5', fill: 'currentColor', viewBox: '0 0 16 16' }, [
          h('circle', { cx: '5', cy: '3', r: '1.2' }), h('circle', { cx: '11', cy: '3', r: '1.2' }),
          h('circle', { cx: '5', cy: '8', r: '1.2' }), h('circle', { cx: '11', cy: '8', r: '1.2' }),
          h('circle', { cx: '5', cy: '13', r: '1.2' }), h('circle', { cx: '11', cy: '13', r: '1.2' }),
        ])
      ]),
      // Up/Down
      h('div', { class: 'flex flex-col shrink-0' }, [
        h('button', {
          disabled: props.idx === 0,
          onClick: () => emit('move', -1),
          class: 'p-0.5 rounded text-gray-400 hover:text-green-500 hover:bg-green-500/10 disabled:opacity-20 disabled:cursor-not-allowed transition-colors leading-none'
        }, [h('svg', { class: 'w-3 h-3', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
          h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '3', d: 'M5 15l7-7 7 7' })
        ])]),
        h('button', {
          disabled: props.idx >= props.total - 1,
          onClick: () => emit('move', 1),
          class: 'p-0.5 rounded text-gray-400 hover:text-green-500 hover:bg-green-500/10 disabled:opacity-20 disabled:cursor-not-allowed transition-colors leading-none'
        }, [h('svg', { class: 'w-3 h-3', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
          h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '3', d: 'M19 9l-7 7-7-7' })
        ])]),
      ]),
      // Doc icon
      h('svg', { class: 'w-3.5 h-3.5 shrink-0 text-gray-300 dark:text-gray-600', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
        h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' })
      ]),
      // Info
      h('div', { class: 'flex-1 min-w-0 pl-0.5' }, [
        h('p', { class: 'text-sm font-medium text-gray-800 dark:text-white truncate' }, props.doc.title),
        h('p', { class: 'text-xs text-gray-400 font-mono truncate' }, props.doc.slug),
      ]),
      // Status badge
      h('span', {
        class: ['shrink-0 px-2 py-0.5 rounded-full text-xs font-semibold',
          props.doc.status === 'published'
            ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
            : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400']
      }, props.doc.status ?? 'draft'),
      // Position
      h('span', { class: 'shrink-0 text-xs font-mono text-gray-300 dark:text-gray-600 w-4 text-right' }, String(props.idx + 1)),
    ])
  }
})

// ── State ─────────────────────────────────────────────────────────────────────
const loading  = ref(true)
const saving   = ref(false)
const mainMenus = ref([])   // [{ id, name, subMenus: [...], docs: [...] }]
const unassigned = ref([])  // docs with no doc_menu_id
const originalSnapshot = ref('')
const toast = ref({ show: false, type: 'success', message: '' })
let toastTimer = null

// ── Dirty tracking ────────────────────────────────────────────────────────────
function buildSnapshot() {
  return JSON.stringify({
    menus: mainMenus.value.map(mm => ({
      id: mm.id,
      subs: mm.subMenus.map(sm => ({ id: sm.id, docs: sm.docs.map(d => d.id) })),
      docs: mm.docs.map(d => d.id),
    })),
    unassigned: unassigned.value.map(d => d.id),
  })
}

const isDirty = computed(() => buildSnapshot() !== originalSnapshot.value)

// ── Load ──────────────────────────────────────────────────────────────────────
async function loadData() {
  loading.value = true
  try {
    const [menusRes, docsRes] = await Promise.all([
      adminApi.getDocMenus(),
      adminApi.getAllDocs(),
    ])

    const rawMenus = menusRes.data.data || []
    const rawDocs  = docsRes.data.data  || []

    // Build a lookup: menu id → docs array
    const docsByMenu = {}
    for (const doc of rawDocs) {
      const key = doc.doc_menu_id ?? '__unassigned__'
      if (!docsByMenu[key]) docsByMenu[key] = []
      docsByMenu[key].push(doc)
    }

    // Sort docs within each menu by order_num
    for (const key of Object.keys(docsByMenu)) {
      docsByMenu[key].sort((a, b) => (a.order_num ?? 0) - (b.order_num ?? 0))
    }

    // Build main menus with sub-menus
    mainMenus.value = rawMenus.map(mm => ({
      id:       mm.id,
      name:     mm.name,
      slug:     mm.slug,
      order_num: mm.order_num,
      subMenus: (mm.children || []).map(sm => ({
        id:       sm.id,
        name:     sm.name,
        slug:     sm.slug,
        order_num: sm.order_num,
        parent_id: mm.id,
        docs:     docsByMenu[sm.id] || [],
      })),
      docs: docsByMenu[mm.id] || [],
    }))

    unassigned.value = docsByMenu['__unassigned__'] || []

    snapshotState()
  } catch {
    showToast('error', 'Failed to load data.')
  } finally {
    loading.value = false
  }
}

function snapshotState() {
  originalSnapshot.value = buildSnapshot()
}

// ── Save ──────────────────────────────────────────────────────────────────────
async function saveOrder() {
  saving.value = true
  try {
    // Build menu items
    const menuItems = []
    mainMenus.value.forEach((mm, mmIdx) => {
      menuItems.push({ id: mm.id, order_num: mmIdx, parent_id: null })
      mm.subMenus.forEach((sm, smIdx) => {
        menuItems.push({ id: sm.id, order_num: smIdx, parent_id: mm.id })
      })
    })

    // Build doc items
    const docItems = []
    mainMenus.value.forEach(mm => {
      mm.docs.forEach((d, idx) => {
        docItems.push({ id: d.id, order_num: idx, doc_menu_id: mm.id })
      })
      mm.subMenus.forEach(sm => {
        sm.docs.forEach((d, idx) => {
          docItems.push({ id: d.id, order_num: idx, doc_menu_id: sm.id })
        })
      })
    })
    unassigned.value.forEach((d, idx) => {
      docItems.push({ id: d.id, order_num: idx, doc_menu_id: null })
    })

    await Promise.all([
      menuItems.length ? adminApi.reorderDocMenus({ items: menuItems }) : Promise.resolve(),
      docItems.length  ? adminApi.reorderDocs({ items: docItems })      : Promise.resolve(),
    ])

    snapshotState()
    showToast('success', 'Order saved!')
  } catch {
    showToast('error', 'Failed to save. Please try again.')
  } finally {
    saving.value = false
  }
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function moveItem(arr, idx, dir) {
  const next = idx + dir
  if (next < 0 || next >= arr.length) return
  ;[arr[idx], arr[next]] = [arr[next], arr[idx]]
}

function showToast(type, message) {
  clearTimeout(toastTimer)
  toast.value = { show: true, type, message }
  toastTimer = setTimeout(() => { toast.value.show = false }, 3000)
}

// ── Init ──────────────────────────────────────────────────────────────────────
onMounted(loadData)
</script>

<style scoped>
.toast-fade-enter-active, .toast-fade-leave-active { transition: opacity .25s, transform .25s; }
.toast-fade-enter-from, .toast-fade-leave-to { opacity: 0; transform: translateY(-6px); }
</style>
