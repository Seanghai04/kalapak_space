<template>
  <div>
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm mb-6">
      <router-link to="/admin/docs" class="text-gray-500 hover:text-brand-violet dark:hover:text-brand-cyan transition-colors">Docs</router-link>
      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      <span class="text-gray-900 dark:text-white font-medium">{{ isEdit ? 'Edit Doc' : 'New Doc' }}</span>
    </nav>

    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-2xl font-sans font-bold dark:text-white">{{ isEdit ? 'Edit Doc' : 'Create Doc' }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ isEdit ? 'Update documentation content and settings' : 'Write a new documentation page' }}</p>
      </div>
    </div>

    <form @submit.prevent="handleSubmit">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ══ Left Column: Content ══ -->
        <div class="lg:col-span-2 space-y-6">

          <!-- Title & Slug -->
          <div class="glass-card space-y-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
              <svg class="w-4 h-4 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
              Page Details
            </h2>

            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Title <span class="text-red-400">*</span></label>
              <input v-model="form.title" @input="autoSlug" type="text" required class="input-field" placeholder="e.g. Getting Started with Kalapak" />
            </div>

            <!-- Permalink (WordPress-style) -->
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Permalink</label>
              <div v-if="!slugEditing" class="flex items-center gap-2 px-3 py-2 bg-gray-50 dark:bg-dark-700/40 border border-gray-200 dark:border-dark-600 rounded-lg">
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                <span class="text-xs text-gray-500 dark:text-gray-400 truncate">kalapak-team.space/docs?page=<span class="text-brand-violet dark:text-brand-cyan font-medium">{{ form.slug || slugify(form.title || 'doc-title') }}</span></span>
                <button type="button" @click="slugEditing = true" class="ml-auto shrink-0 text-[10px] font-semibold text-brand-violet dark:text-brand-cyan hover:underline">Edit</button>
              </div>
              <div v-else class="space-y-2">
                <div class="flex items-center">
                  <span class="inline-flex items-center px-3 h-[38px] text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-dark-600 border border-r-0 border-gray-200 dark:border-dark-500 rounded-l-lg">/docs?page=</span>
                  <input v-model="form.slug" @input="slugManuallyEdited = true" ref="slugInputRef" type="text" required class="input-field !rounded-l-none" placeholder="getting-started" />
                </div>
                <div class="flex items-center gap-2">
                  <button type="button" @click="slugEditing = false" class="text-[10px] font-semibold text-brand-violet dark:text-brand-cyan hover:underline">Done</button>
                  <span class="text-gray-300 dark:text-dark-500">|</span>
                  <button type="button" @click="resetSlug" class="text-[10px] font-semibold text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:underline">Reset from title</button>
                  <span v-if="slugManuallyEdited" class="ml-auto text-[10px] text-amber-500 dark:text-amber-400 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Manually edited
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- ══ Sections ("On This Page") ══ -->
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <h2 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10"/></svg>
                Sections
                <span class="text-[10px] font-normal text-gray-400 dark:text-gray-500">(appear as "On this page" in public view)</span>
              </h2>
              <button type="button" @click="addSection"
                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-brand-violet/10 dark:bg-brand-cyan/10 text-brand-violet dark:text-brand-cyan hover:bg-brand-violet/20 dark:hover:bg-brand-cyan/20 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Add Section
              </button>
            </div>

            <!-- Empty state -->
            <div v-if="form.sections.length === 0"
              class="glass-card flex flex-col items-center justify-center py-10 text-center border-2 border-dashed border-gray-200 dark:border-white/[0.08]">
              <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">No sections yet</p>
              <p class="text-xs text-gray-400 dark:text-gray-500">Click "Add Section" to add headings and content blocks.</p>
            </div>

            <!-- Section cards -->
            <div v-for="(section, idx) in form.sections" :key="section._key" class="glass-card space-y-4">
              <!-- Section header -->
              <div class="flex items-center justify-between -mb-1">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                  Section {{ idx + 1 }}
                </span>
                <div class="flex items-center gap-1">
                  <button type="button" @click="moveSectionUp(idx)" :disabled="idx === 0"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan disabled:opacity-30 disabled:cursor-not-allowed transition-colors" title="Move up">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                  </button>
                  <button type="button" @click="moveSectionDown(idx)" :disabled="idx === form.sections.length - 1"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan disabled:opacity-30 disabled:cursor-not-allowed transition-colors" title="Move down">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                  </button>
                  <button type="button" @click="removeSection(idx)"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors ml-1" title="Remove section">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </div>
              </div>

              <!-- Section heading -->
              <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                  Heading <span class="text-red-400">*</span>
                  <span class="ml-1 text-[10px] font-normal text-gray-400">(shown as TOC item in public view)</span>
                </label>
                <input
                  v-model="section.heading"
                  type="text"
                  required
                  class="input-field"
                  :placeholder="`e.g. Installation`"
                />
              </div>

              <!-- Tiptap content editor -->
              <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Content</label>
                <ContentEditor v-if="editorReady" v-model="section.content" />
                <div v-else class="flex items-center justify-center h-32 text-gray-400 rounded-xl border border-gray-200 dark:border-dark-600">
                  <svg class="w-4 h-4 animate-spin mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                  Loading editor...
                </div>
              </div>
            </div>

            <!-- Add another section shortcut (bottom) -->
            <button v-if="form.sections.length > 0" type="button" @click="addSection"
              class="w-full flex items-center justify-center gap-2 py-3 rounded-xl border-2 border-dashed border-gray-200 dark:border-white/[0.08] text-sm text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan hover:border-brand-violet dark:hover:border-brand-cyan transition-all">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
              Add Another Section
            </button>
          </div>

        </div>

        <!-- ══ Right Column: Settings Sidebar ══ -->
        <div class="space-y-6">

          <!-- Publish Settings -->
          <div class="glass-card space-y-4">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
              <svg class="w-4 h-4 text-brand-violet dark:text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              Settings
            </h2>

            <!-- Status -->
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Status</label>
              <div class="flex gap-2">
                <button
                  type="button"
                  @click="form.status = 'draft'"
                  :class="form.status === 'draft'
                    ? 'bg-yellow-50 text-yellow-700 border-yellow-300 dark:bg-yellow-500/10 dark:text-yellow-400 dark:border-yellow-500/30'
                    : 'bg-white dark:bg-dark-700 text-gray-500 border-gray-200 dark:border-dark-600 hover:border-gray-300'"
                  class="flex-1 px-3 py-2 rounded-lg border text-xs font-medium transition-all text-center"
                >Draft</button>
                <button
                  type="button"
                  @click="form.status = 'published'"
                  :class="form.status === 'published'
                    ? 'bg-green-50 text-green-700 border-green-300 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/30'
                    : 'bg-white dark:bg-dark-700 text-gray-500 border-gray-200 dark:border-dark-600 hover:border-gray-300'"
                  class="flex-1 px-3 py-2 rounded-lg border text-xs font-medium transition-all text-center"
                >Published</button>
              </div>
            </div>

            <!-- Category (multi-select chips) -->
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Category</label>

              <!-- Selected chips -->
              <div class="flex flex-wrap gap-1.5 mb-2" v-if="form.categories.length">
                <span
                  v-for="cat in form.categories" :key="cat"
                  class="inline-flex items-center gap-1 pl-2.5 pr-1.5 py-0.5 rounded-full text-xs font-semibold
                         bg-brand-violet/15 dark:bg-brand-cyan/15 text-brand-violet dark:text-brand-cyan"
                >
                  {{ cat }}
                  <button type="button" @click="removeCategory(cat)"
                    class="w-4 h-4 rounded-full flex items-center justify-center hover:bg-brand-violet/20 dark:hover:bg-brand-cyan/20 transition-colors">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </button>
                </span>
              </div>

              <!-- Dropdown to add categories -->
              <div class="relative" ref="catDropdownRef">
                <div
                  class="input-field flex items-center gap-2 cursor-pointer select-none"
                  @click="catOpen = !catOpen"
                >
                  <span class="flex-1 text-sm" :class="form.categories.length ? 'text-gray-400 dark:text-gray-500' : 'text-gray-400'">
                    {{ form.categories.length ? 'Add more...' : 'Select categories...' }}
                  </span>
                  <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform" :class="catOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                  </svg>
                </div>

                <div v-if="catOpen"
                  class="absolute z-20 w-full mt-1 bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 rounded-xl shadow-xl overflow-hidden">
                  <!-- Existing categories -->
                  <div class="max-h-40 overflow-y-auto">
                    <button
                      v-for="cat in categories" :key="cat.name"
                      type="button"
                      @click="toggleCategory(cat.name)"
                      class="w-full flex items-center gap-2 px-3 py-2 text-sm text-left hover:bg-gray-50 dark:hover:bg-white/[0.04] transition-colors"
                      :class="form.categories.includes(cat.name) ? 'text-brand-violet dark:text-brand-cyan font-semibold' : 'text-gray-700 dark:text-gray-300'"
                    >
                      <svg v-if="form.categories.includes(cat.name)" class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                      </svg>
                      <span v-else class="w-3.5 h-3.5 shrink-0" />
                      {{ cat.name }}
                    </button>
                    <p v-if="!categories.length" class="px-3 py-2 text-xs text-gray-400">No categories yet.</p>
                  </div>
                  <!-- New category input -->
                  <div class="border-t border-gray-100 dark:border-dark-700 p-2">
                    <div class="flex gap-2">
                      <input
                        v-model="newCatInput"
                        @keydown.enter.prevent="addNewCategory"
                        type="text"
                        placeholder="New category name..."
                        class="flex-1 text-xs px-2.5 py-1.5 rounded-lg border border-gray-200 dark:border-dark-600 bg-transparent outline-none focus:border-brand-violet dark:focus:border-brand-cyan text-gray-800 dark:text-gray-200"
                      />
                      <button type="button" @click="addNewCategory"
                        class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-brand-violet/10 dark:bg-brand-cyan/10 text-brand-violet dark:text-brand-cyan hover:bg-brand-violet/20 dark:hover:bg-brand-cyan/20 transition-colors">
                        Add
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <p class="text-[10px] text-gray-400 mt-1">Choose one or more categories.</p>
            </div>

            <!-- Menu -->
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                Menu <span class="text-red-400">*</span>
              </label>
              <select v-model.number="form.doc_menu_id" required class="input-field">
                <option :value="null" disabled>-- Select a menu --</option>
                <option v-for="menu in menus" :key="menu.id" :value="menu.id">
                  {{ menu.parent_id ? '\u00a0\u00a0\u2514 ' + menu.name : menu.name }}
                </option>
              </select>
              <div class="flex items-center justify-between mt-1">
                <p class="text-[10px] text-gray-400">Groups pages in the sidebar.</p>
                <router-link :to="{ name: 'admin-doc-menus' }" class="text-[10px] text-brand-violet dark:text-brand-cyan hover:underline">Manage menus</router-link>
              </div>
            </div>

            <!-- Parent Page -->
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                Parent Page
                <span class="ml-1 font-normal text-gray-400">(optional — creates a subpage)</span>
              </label>
              <select v-model="form.parent_id" class="input-field">
                <option :value="null">— None (top-level page) —</option>
                <optgroup v-for="(group, cat) in groupedParentDocs" :key="cat" :label="cat">
                  <option
                    v-for="doc in group"
                    :key="doc.id"
                    :value="doc.id"
                    :disabled="isEdit && doc.id === parseInt(route.params.id)"
                  >
                    {{ doc.title }}
                  </option>
                </optgroup>
              </select>
              <p class="text-[10px] text-gray-400 mt-1">Subpages appear indented under the parent in the sidebar.</p>
            </div>

            <!-- Order -->
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Order</label>
              <input v-model.number="form.order_num" type="number" min="0" class="input-field" placeholder="0" />
              <p class="text-[10px] text-gray-400 mt-1">Lower numbers appear first.</p>
            </div>
          </div>

          <!-- Actions -->
          <div class="glass-card space-y-3">
            <button type="submit" :disabled="saving" class="btn-primary w-full flex items-center justify-center gap-2 text-sm">
              <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              {{ saving ? 'Saving...' : (isEdit ? 'Update Doc' : (form.status === 'published' ? 'Publish Doc' : 'Save Draft')) }}
            </button>
            <router-link to="/admin/docs" class="btn-ghost w-full flex items-center justify-center gap-2 text-sm">
              Cancel
            </router-link>
            <p v-if="error" class="text-sm text-red-500 text-center">{{ error }}</p>
          </div>

          <!-- Preview link (published only) -->
          <div v-if="isEdit && form.status === 'published' && form.slug" class="glass-card">
            <a
              :href="`/docs?page=${form.slug}`"
              target="_blank"
              class="flex items-center gap-2 text-sm text-brand-violet dark:text-brand-cyan hover:underline"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
              View public doc
            </a>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { adminApi } from '@/services/api'
import ContentEditor from '@/components/common/ContentEditor.vue'

const route = useRoute()
const router = useRouter()

const isEdit = computed(() => !!route.params.id)
const editorReady = ref(false)
const saving = ref(false)
const error = ref('')
const slugEditing = ref(false)
const slugManuallyEdited = ref(false)
const slugInputRef = ref(null)
const menus = ref([])
const allParentDocs = ref([])
const categories = ref([])
const catOpen = ref(false)
const newCatInput = ref('')
const catDropdownRef = ref(null)

const form = ref({
  title: '',
  slug: '',
  categories: ['General'],
  doc_menu_id: null,
  parent_id: null,
  order_num: 0,
  status: 'draft',
  sections: [],
})

// Docs grouped by menu for the parent-page selector
const groupedParentDocs = computed(() => {
  const groups = {}
  for (const doc of allParentDocs.value) {
    if (isEdit.value && doc.id === parseInt(route.params.id)) continue
    const menuEntry = menus.value.find(m => m.id === doc.doc_menu_id)
    const cat = menuEntry?.name || 'General'
    if (!groups[cat]) groups[cat] = []
    groups[cat].push(doc)
  }
  return groups
})

// ── Slug helpers ──────────────────────────────────────────────────
function slugify(text) {
  return text
    .toString()
    .toLowerCase()
    .trim()
    .replace(/\s+/g, '-')
    .replace(/[^\w-]+/g, '')
    .replace(/--+/g, '-')
    .replace(/^-+|-+$/g, '')
}

function autoSlug() {
  if (!slugManuallyEdited.value) {
    form.value.slug = slugify(form.value.title)
  }
}

function resetSlug() {
  form.value.slug = slugify(form.value.title)
  slugManuallyEdited.value = false
}

// ── Sections ─────────────────────────────────────────────────────
function addSection() {
  form.value.sections.push({
    _key: Date.now() + Math.random(),
    heading: '',
    content: '',
  })
}

function removeSection(idx) {
  form.value.sections.splice(idx, 1)
}

function moveSectionUp(idx) {
  if (idx === 0) return
  const arr = form.value.sections;
  [arr[idx - 1], arr[idx]] = [arr[idx], arr[idx - 1]]
}

function moveSectionDown(idx) {
  const arr = form.value.sections
  if (idx >= arr.length - 1) return;
  [arr[idx], arr[idx + 1]] = [arr[idx + 1], arr[idx]]
}

// ── Category helpers ──────────────────────────────────────────────
function toggleCategory(name) {
  const idx = form.value.categories.indexOf(name)
  if (idx === -1) { form.value.categories.push(name) }
  else { form.value.categories.splice(idx, 1) }
}

function removeCategory(name) {
  form.value.categories = form.value.categories.filter(c => c !== name)
}

async function addNewCategory() {
  const name = newCatInput.value.trim()
  if (!name) return
  // Add to local categories list if not there
  if (!categories.value.find(c => c.name === name)) {
    try {
      await adminApi.createDocCategory({ name })
      await fetchCategories()
    } catch { /* already exists */ }
  }
  if (!form.value.categories.includes(name)) { form.value.categories.push(name) }
  newCatInput.value = ''
}

// ── API calls ────────────────────────────────────────────────────
async function fetchMenus() {
  try {
    const { data } = await adminApi.getDocMenusFlat()
    menus.value = data.data || []
  } catch {
    menus.value = []
  }
}

async function fetchCategories() {
  try {
    const { data } = await adminApi.getDocCategories()
    categories.value = data.data || []
  } catch {
    categories.value = []
  }
}

async function fetchAllDocs() {
  try {
    const { data } = await adminApi.getAllDocs()
    allParentDocs.value = data.data || []
  } catch {
    allParentDocs.value = []
  }
}

async function fetchDoc() {
  try {
    const { data } = await adminApi.getDoc(route.params.id)
    const doc = data.data
    form.value = {
      title: doc.title,
      slug: doc.slug,
      categories: Array.isArray(doc.categories) && doc.categories.length ? doc.categories : (doc.category ? [doc.category] : ['General']),
      doc_menu_id: doc.doc_menu_id ?? null,
      parent_id: doc.parent_id ?? null,
      order_num: doc.order_num ?? 0,
      status: doc.status,
      sections: (doc.sections || []).map((s, i) => ({
        _key: i + 1,
        heading: s.heading,
        content: s.content || '',
      })),
    }
    slugManuallyEdited.value = true
  } catch {
    error.value = 'Failed to load doc.'
  }
}

async function handleSubmit() {
  error.value = ''
  saving.value = true
  try {
    const payload = {
      title: form.value.title,
      slug: form.value.slug || slugify(form.value.title),
      categories: form.value.categories?.length ? form.value.categories : ['General'],
      doc_menu_id: form.value.doc_menu_id || null,
      parent_id: form.value.parent_id || null,
      order_num: form.value.order_num,
      status: form.value.status,
      sections: form.value.sections.map(s => ({
        heading: s.heading,
        content: s.content,
      })),
    }

    if (isEdit.value) {
      await adminApi.updateDoc(route.params.id, payload)
    } else {
      await adminApi.createDoc(payload)
    }
    router.push({ name: 'admin-docs' })
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to save doc. Please try again.'
  } finally {
    saving.value = false
  }
}

onMounted(async () => {
  await Promise.all([fetchMenus(), fetchAllDocs(), fetchCategories()])
  if (isEdit.value) {
    await fetchDoc()
  } else if (route.query.menu_id) {
    // Pre-fill doc_menu_id from ?menu_id= query param (navigated from Doc Menus page)
    form.value.doc_menu_id = parseInt(route.query.menu_id)
  }
  await nextTick()
  editorReady.value = true

  // Close category dropdown on outside click
  document.addEventListener('mousedown', (e) => {
    if (catDropdownRef.value && !catDropdownRef.value.contains(e.target)) {
      catOpen.value = false
    }
  })
})
</script>
