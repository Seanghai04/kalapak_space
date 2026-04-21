<template>
  <div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-sans font-bold dark:text-white">Doc Menus</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
          Manage documentation navigation. Structure: <span class="text-brand-violet dark:text-brand-cyan font-medium">Main Menu → Sub-Menu → Pages → Subpages</span>
        </p>
      </div>
      <button @click="openCreate(null)" class="btn-primary flex items-center gap-2 text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Main Menu
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="w-10 h-10 border-4 border-brand-violet/30 border-t-brand-violet rounded-full animate-spin" />
    </div>

    <!-- Empty state -->
    <div v-else-if="menus.length === 0" class="glass-card flex flex-col items-center justify-center py-20 text-center">
      <svg class="w-14 h-14 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
      <p class="text-gray-500 dark:text-gray-400 font-medium mb-1">No main menus yet</p>
      <p class="text-sm text-gray-400 dark:text-gray-500 mb-5">Create your first main menu to organize documentation sections.</p>
      <button @click="openCreate(null)" class="btn-primary text-sm">Create first menu</button>
    </div>

    <!-- Hierarchical tree -->
    <div v-else class="space-y-4">
      <div
        v-for="mainMenu in menus"
        :key="mainMenu.id"
        class="glass-card overflow-hidden"
      >
        <!-- ── Main Menu Row ── -->
        <div class="flex items-center gap-3 p-4">
          <!-- Expand/Collapse toggle -->
          <button
            @click="toggleExpand(mainMenu.id)"
            class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/[0.06] transition-colors"
          >
            <svg
              class="w-4 h-4 transition-transform duration-200"
              :class="{ 'rotate-90': expanded.has(mainMenu.id) }"
              fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
          </button>

          <!-- Icon -->
          <div class="w-9 h-9 rounded-xl bg-brand-violet/10 dark:bg-brand-cyan/10 flex items-center justify-center flex-shrink-0">
            <svg class="w-4.5 h-4.5 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/></svg>
          </div>

          <!-- Name + slug -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
              <span class="font-semibold text-gray-900 dark:text-white">{{ mainMenu.name }}</span>
              <span class="text-[10px] px-1.5 py-0.5 rounded bg-brand-violet/10 dark:bg-brand-cyan/10 text-brand-violet dark:text-brand-cyan font-semibold uppercase tracking-wide">Main</span>
            </div>
            <p class="text-xs text-gray-400 font-mono">/{{ mainMenu.slug }}</p>
          </div>

          <!-- Stats -->
          <div class="hidden sm:flex items-center gap-3 text-xs text-gray-400 mr-2">
            <span class="flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/></svg>
              {{ mainMenu.children?.length ?? 0 }} sub-menus
            </span>
            <span class="flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
              {{ mainMenu.doc_count }} pages
            </span>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-1">
            <button
              @click="openCreate(mainMenu.id)"
              class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium text-brand-violet dark:text-brand-cyan hover:bg-brand-violet/10 dark:hover:bg-brand-cyan/10 transition-colors border border-brand-violet/20 dark:border-brand-cyan/20"
              title="Add sub-menu"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
              Sub-Menu
            </button>
            <router-link
              :to="{ name: 'admin-docs-create', query: { menu_id: mainMenu.id } }"
              class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan hover:bg-brand-violet/5 dark:hover:bg-brand-cyan/5 transition-colors border border-gray-200 dark:border-white/[0.08]"
              title="Add page"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
              Page
            </router-link>
            <button
              @click="openEdit(mainMenu)"
              class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/[0.06] transition-colors"
              title="Edit"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
            </button>
            <button
              @click="confirmDelete(mainMenu)"
              class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
              title="Delete"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
            </button>
          </div>
        </div>

        <!-- ── Sub-Menus (expanded) ── -->
        <div v-if="expanded.has(mainMenu.id) && mainMenu.children?.length" class="border-t border-gray-100 dark:border-white/[0.06]">
          <div
            v-for="sub in mainMenu.children"
            :key="sub.id"
            class="flex items-center gap-3 px-4 py-3 pl-12 border-b border-gray-100 dark:border-white/[0.04] last:border-0 hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors"
          >
            <!-- Sub-menu icon connector -->
            <div class="flex-shrink-0 flex items-center">
              <div class="w-px h-4 bg-gray-200 dark:bg-white/[0.1] mr-3" />
              <div class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-white/[0.06] flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
              </div>
            </div>

            <div class="flex-1 min-w-0">
              <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ sub.name }}</span>
              <p class="text-[11px] text-gray-400 font-mono">/{{ sub.slug }}</p>
            </div>

            <span class="text-xs text-gray-400 mr-1">{{ sub.doc_count }} page{{ sub.doc_count !== 1 ? 's' : '' }}</span>

            <div class="flex items-center gap-1">
              <router-link
                :to="{ name: 'admin-docs-create', query: { menu_id: sub.id } }"
                class="flex items-center gap-1 px-2 py-1 rounded-lg text-[11px] font-medium text-gray-500 dark:text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan hover:bg-brand-violet/5 dark:hover:bg-brand-cyan/5 transition-colors border border-gray-200 dark:border-white/[0.08]"
                title="Add page"
              >
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Page
              </router-link>
              <router-link
                :to="{ name: 'admin-docs', query: { menu_id: sub.id } }"
                class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/[0.06] transition-colors"
                title="View pages"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              </router-link>
              <button
                @click="openEdit(sub)"
                class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/[0.06] transition-colors"
                title="Edit"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
              </button>
              <button
                @click="confirmDelete(sub)"
                class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                title="Delete"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
              </button>
            </div>
          </div>
        </div>

        <!-- ── Expanded but no sub-menus hint ── -->
        <div
          v-if="expanded.has(mainMenu.id) && (!mainMenu.children || mainMenu.children.length === 0)"
          class="border-t border-gray-100 dark:border-white/[0.06] px-12 py-3 text-xs text-gray-400 dark:text-gray-500 italic"
        >
          No sub-menus. Click "+ Sub-Menu" to add one, or "+ Page" to add a page directly.
        </div>
      </div>
    </div>

    <!-- ── Create / Edit Modal ── -->
    <transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
      <div v-if="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="closeModal">
        <transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
          <div v-if="modalOpen" class="w-full max-w-md bg-white dark:bg-dark-800 rounded-2xl shadow-2xl ring-1 ring-gray-200/80 dark:ring-white/[0.08] overflow-hidden">
            <!-- Modal header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-white/[0.06]">
              <h2 class="text-base font-bold text-gray-900 dark:text-white">
                {{ editingMenu ? 'Edit Menu' : (form.parent_id ? 'New Sub-Menu' : 'New Main Menu') }}
              </h2>
              <button @click="closeModal" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/[0.06] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>

            <!-- Modal body -->
            <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
              <!-- Parent label (for sub-menu creation) -->
              <div v-if="form.parent_id && !editingMenu" class="flex items-center gap-2 px-3 py-2 rounded-lg bg-brand-violet/5 dark:bg-brand-cyan/5 border border-brand-violet/20 dark:border-brand-cyan/20">
                <svg class="w-3.5 h-3.5 text-brand-violet dark:text-brand-cyan flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/></svg>
                <span class="text-xs text-brand-violet dark:text-brand-cyan">
                  Adding sub-menu under <strong>{{ parentMenuName }}</strong>
                </span>
              </div>

              <!-- Name -->
              <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                  {{ form.parent_id ? 'Sub-Menu Name' : 'Main Menu Name' }} <span class="text-red-400">*</span>
                </label>
                <input
                  v-model="form.name"
                  type="text"
                  required
                  class="input-field"
                  :placeholder="form.parent_id ? 'e.g. Installation, Authentication' : 'e.g. Getting Started, API Reference'"
                />
              </div>

              <!-- Slug preview -->
              <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 dark:bg-dark-700/40 border border-gray-200 dark:border-dark-600 rounded-lg">
                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                <span class="text-xs text-gray-500 dark:text-gray-400">Slug: <span class="text-brand-violet dark:text-brand-cyan font-mono font-medium">{{ slugPreview || 'menu-name' }}</span></span>
              </div>

              <!-- Description -->
              <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Description <span class="text-gray-400 font-normal">(optional)</span></label>
                <textarea v-model="form.description" rows="2" class="input-field resize-none" placeholder="Short description of this section..." />
              </div>

              <!-- Order -->
              <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Display Order</label>
                <input v-model.number="form.order_num" type="number" min="0" class="input-field" placeholder="0" />
                <p class="text-[10px] text-gray-400 mt-1">Lower numbers appear first in the sidebar.</p>
              </div>

              <p v-if="formError" class="text-sm text-red-500">{{ formError }}</p>

              <!-- Actions -->
              <div class="flex items-center gap-3 pt-2">
                <button type="button" @click="closeModal" class="btn-ghost flex-1 text-sm">Cancel</button>
                <button type="submit" :disabled="saving" class="btn-primary flex-1 flex items-center justify-center gap-2 text-sm">
                  <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                  {{ saving ? 'Saving...' : (editingMenu ? 'Update' : 'Create') }}
                </button>
              </div>
            </form>
          </div>
        </transition>
      </div>
    </transition>

    <!-- ── Delete Confirm Modal ── -->
    <transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
      <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="deleteTarget = null">
        <div class="w-full max-w-sm bg-white dark:bg-dark-800 rounded-2xl shadow-2xl ring-1 ring-gray-200/80 dark:ring-white/[0.08] p-6 space-y-4">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            </div>
            <div>
              <h3 class="font-bold text-gray-900 dark:text-white">Delete "{{ deleteTarget.name }}"?</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                {{ deleteTarget.parent_id ? 'This sub-menu and its pages association will be removed.' : 'This main menu and all its sub-menus will be removed.' }}
              </p>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <button @click="deleteTarget = null" class="btn-ghost flex-1 text-sm">Cancel</button>
            <button @click="handleDelete" :disabled="deleting" class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-semibold transition-colors disabled:opacity-50">
              <svg v-if="deleting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              {{ deleting ? 'Deleting...' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { adminApi } from '@/services/api'
import { useUiStore } from '@/stores/ui'

const uiStore = useUiStore()
const loading = ref(false)
const menus = ref([])

// Which main menus are expanded (showing sub-menus)
const expanded = ref(new Set())

function toggleExpand(id) {
  if (expanded.value.has(id)) {
    expanded.value.delete(id)
  } else {
    expanded.value.add(id)
  }
  expanded.value = new Set(expanded.value)
}

// Modal state
const modalOpen = ref(false)
const editingMenu = ref(null)
const saving = ref(false)
const formError = ref('')
const form = ref({ name: '', description: '', order_num: 0, parent_id: null })

// Delete state
const deleteTarget = ref(null)
const deleting = ref(false)

const slugPreview = computed(() => {
  return form.value.name
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9\s-]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
})

const parentMenuName = computed(() => {
  if (!form.value.parent_id) return ''
  const found = menus.value.find(m => m.id === form.value.parent_id)
  return found?.name ?? ''
})

async function fetchMenus() {
  loading.value = true
  try {
    const { data } = await adminApi.getDocMenus()
    menus.value = data.data || []
    // Auto-expand all main menus on first load
    menus.value.forEach(m => expanded.value.add(m.id))
    expanded.value = new Set(expanded.value)
  } catch {
    uiStore.showToast('Failed to load menus.', 'error')
  } finally {
    loading.value = false
  }
}

/** Open the create modal — parentId = null means "new main menu" */
function openCreate(parentId) {
  editingMenu.value = null
  form.value = { name: '', description: '', order_num: 0, parent_id: parentId }
  formError.value = ''
  modalOpen.value = true
}

function openEdit(menu) {
  editingMenu.value = menu
  form.value = {
    name: menu.name,
    description: menu.description || '',
    order_num: menu.order_num ?? 0,
    parent_id: menu.parent_id ?? null,
  }
  formError.value = ''
  modalOpen.value = true
}

function closeModal() {
  modalOpen.value = false
  editingMenu.value = null
}

async function handleSubmit() {
  formError.value = ''
  saving.value = true
  try {
    if (editingMenu.value) {
      await adminApi.updateDocMenu(editingMenu.value.id, form.value)
      uiStore.showToast('Menu updated.', 'success')
    } else {
      await adminApi.createDocMenu(form.value)
      uiStore.showToast('Menu created.', 'success')
    }
    closeModal()
    await fetchMenus()
  } catch (err) {
    const msg = err.response?.data?.message
      || Object.values(err.response?.data?.errors || {})[0]?.[0]
      || 'An error occurred.'
    formError.value = msg
  } finally {
    saving.value = false
  }
}

function confirmDelete(menu) {
  deleteTarget.value = menu
}

async function handleDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  try {
    await adminApi.deleteDocMenu(deleteTarget.value.id)
    uiStore.showToast('Menu deleted.', 'success')
    deleteTarget.value = null
    await fetchMenus()
  } catch {
    uiStore.showToast('Failed to delete menu.', 'error')
  } finally {
    deleting.value = false
  }
}

onMounted(fetchMenus)
</script>
