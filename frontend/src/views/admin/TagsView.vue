<template>
  <div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-code font-bold dark:text-white">Tags</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Organize projects with color-coded tags</p>
      </div>
      <button @click="openCreateModal" class="btn-primary flex items-center gap-2 text-sm">
        <PlusIcon class="w-4 h-4" />
        Add Tag
      </button>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-brand-violet/10 dark:bg-brand-violet/20 flex items-center justify-center">
          <TagIcon class="w-5 h-5 text-brand-violet" />
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Total Tags</p>
          <p class="text-lg font-bold dark:text-white">{{ tags.length }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center">
          <FolderIcon class="w-5 h-5 text-blue-500" />
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Used in Projects</p>
          <p class="text-lg font-bold text-blue-500">{{ usedTags }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center">
          <ExclamationTriangleIcon class="w-5 h-5 text-amber-500" />
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Unused Tags</p>
          <p class="text-lg font-bold text-amber-500">{{ unusedTags }}</p>
        </div>
      </div>
      <div class="glass-card !p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center">
          <SwatchIcon class="w-5 h-5 text-green-500" />
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Unique Colors</p>
          <p class="text-lg font-bold text-green-500">{{ uniqueColors }}</p>
        </div>
      </div>
    </div>

    <!-- Search & View Toggle -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mb-6">
      <div class="relative flex-1">
        <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search tags..."
          class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-white dark:bg-dark-700 border border-gray-200 dark:border-dark-500 text-sm text-gray-900 dark:text-white placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-violet/30 dark:focus:ring-brand-cyan/30 focus:border-brand-violet dark:focus:border-brand-cyan transition-all"
        />
      </div>
      <div class="flex items-center gap-2">
        <CustomSelect
          v-model="sortBy"
          :options="[{ label: 'Name A-Z', value: 'name' }, { label: 'Name Z-A', value: 'name-desc' }, { label: 'Most Used', value: 'projects' }, { label: 'Newest', value: 'newest' }, { label: 'Oldest', value: 'oldest' }]"
          placeholder="Name A-Z"
          size="sm"
        />
        <div class="flex rounded-xl border border-gray-200 dark:border-dark-500 overflow-hidden">
          <button @click="viewMode = 'grid'" class="px-3 py-2.5 transition-colors" :class="viewMode === 'grid' ? 'bg-brand-violet/10 dark:bg-brand-cyan/10 text-brand-violet dark:text-brand-cyan' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-dark-600'">
            <Squares2X2Icon class="w-4 h-4" />
          </button>
          <button @click="viewMode = 'list'" class="px-3 py-2.5 transition-colors border-l border-gray-200 dark:border-dark-500" :class="viewMode === 'list' ? 'bg-brand-violet/10 dark:bg-brand-cyan/10 text-brand-violet dark:text-brand-cyan' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-dark-600'">
            <Bars3Icon class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="w-10 h-10 border-4 border-brand-violet/30 border-t-brand-violet rounded-full animate-spin" />
    </div>

    <!-- Empty State -->
    <div v-else-if="!tags.length" class="glass-card text-center py-16">
      <div class="w-16 h-16 rounded-2xl bg-brand-violet/10 dark:bg-brand-cyan/10 flex items-center justify-center mx-auto mb-4">
        <TagIcon class="w-8 h-8 text-brand-violet dark:text-brand-cyan" />
      </div>
      <h3 class="text-lg font-semibold dark:text-white mb-2">No tags yet</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 max-w-sm mx-auto">Tags help organize your projects. Create your first tag to get started.</p>
      <button @click="openCreateModal" class="btn-primary inline-flex items-center gap-2 text-sm">
        <PlusIcon class="w-4 h-4" />
        Create First Tag
      </button>
    </div>

    <!-- No Results -->
    <div v-else-if="!filteredTags.length" class="glass-card text-center py-16">
      <MagnifyingGlassIcon class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
      <h3 class="text-lg font-semibold dark:text-white mb-1">No tags found</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400">No tags match "{{ searchQuery }}"</p>
    </div>

    <!-- Grid View -->
    <div v-else-if="viewMode === 'grid'" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      <div
        v-for="tag in filteredTags"
        :key="tag.id"
        class="glass-card group relative overflow-hidden transition-all duration-200 hover:shadow-lg hover:scale-[1.01]"
      >
        <!-- Color stripe -->
        <div class="absolute top-0 left-0 right-0 h-1 rounded-t-xl" :style="{ background: tag.color || '#7b2fff' }" />

        <div class="pt-3">
          <!-- Tag visual -->
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div class="w-11 h-11 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110"
                   :style="{ background: (tag.color || '#7b2fff') + '18' }">
                <TagIcon class="w-5 h-5" :style="{ color: tag.color || '#7b2fff' }" />
              </div>
              <div>
                <h3 class="font-bold text-gray-900 dark:text-white text-[15px]">{{ tag.name }}</h3>
                <span class="text-[10px] font-mono text-gray-400 bg-gray-100 dark:bg-dark-600 px-1.5 py-0.5 rounded">{{ tag.slug }}</span>
              </div>
            </div>
            <!-- Actions -->
            <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
              <button @click="openEditModal(tag)" class="p-1.5 rounded-lg text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors" title="Edit">
                <PencilSquareIcon class="w-4 h-4" />
              </button>
              <button @click="confirmDelete(tag)" class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors" title="Delete">
                <TrashIcon class="w-4 h-4" />
              </button>
            </div>
          </div>

          <!-- Color preview -->
          <div class="flex items-center gap-2 mb-3">
            <div class="w-5 h-5 rounded-md ring-1 ring-gray-200 dark:ring-white/10 shadow-sm" :style="{ background: tag.color || '#7b2fff' }" />
            <span class="text-xs font-mono text-gray-500 dark:text-gray-400">{{ tag.color || '#7b2fff' }}</span>
          </div>

          <!-- Project count -->
          <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-dark-500">
            <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
              <FolderIcon class="w-3.5 h-3.5" />
              <span>{{ tag.projects_count ?? 0 }} {{ (tag.projects_count ?? 0) === 1 ? 'project' : 'projects' }}</span>
            </div>
            <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ formatDate(tag.created_at) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- List View -->
    <div v-else class="glass-card !p-0 overflow-hidden">
      <table class="w-full">
        <thead>
          <tr class="border-b border-gray-100 dark:border-dark-500">
            <th class="text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3">Tag</th>
            <th class="text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3 hidden sm:table-cell">Slug</th>
            <th class="text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3">Color</th>
            <th class="text-center text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3">Projects</th>
            <th class="text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3 hidden md:table-cell">Created</th>
            <th class="text-right text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="tag in filteredTags" :key="tag.id"
              class="border-b border-gray-50 dark:border-dark-600 last:border-0 hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors group">
            <td class="px-5 py-3.5">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                     :style="{ background: (tag.color || '#7b2fff') + '18' }">
                  <TagIcon class="w-4 h-4" :style="{ color: tag.color || '#7b2fff' }" />
                </div>
                <span class="font-semibold text-sm text-gray-900 dark:text-white">{{ tag.name }}</span>
              </div>
            </td>
            <td class="px-5 py-3.5 hidden sm:table-cell">
              <span class="text-xs font-mono text-gray-400 bg-gray-100 dark:bg-dark-600 px-2 py-1 rounded">{{ tag.slug }}</span>
            </td>
            <td class="px-5 py-3.5">
              <div class="flex items-center gap-2">
                <div class="w-5 h-5 rounded-md ring-1 ring-gray-200 dark:ring-white/10 shadow-sm" :style="{ background: tag.color || '#7b2fff' }" />
                <span class="text-xs font-mono text-gray-500 dark:text-gray-400 hidden lg:inline">{{ tag.color || '#7b2fff' }}</span>
              </div>
            </td>
            <td class="px-5 py-3.5 text-center">
              <span class="inline-flex items-center justify-center min-w-[28px] h-6 px-2 rounded-full text-xs font-semibold"
                    :class="(tag.projects_count ?? 0) > 0
                      ? 'bg-brand-violet/10 dark:bg-brand-cyan/10 text-brand-violet dark:text-brand-cyan'
                      : 'bg-gray-100 dark:bg-dark-600 text-gray-400 dark:text-gray-500'">
                {{ tag.projects_count ?? 0 }}
              </span>
            </td>
            <td class="px-5 py-3.5 hidden md:table-cell">
              <span class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(tag.created_at) }}</span>
            </td>
            <td class="px-5 py-3.5 text-right">
              <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <button @click="openEditModal(tag)" class="p-1.5 rounded-lg text-gray-400 hover:text-brand-violet dark:hover:text-brand-cyan hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors" title="Edit">
                  <PencilSquareIcon class="w-4 h-4" />
                </button>
                <button @click="confirmDelete(tag)" class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors" title="Delete">
                  <TrashIcon class="w-4 h-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Tag Count Footer -->
    <div v-if="filteredTags.length" class="mt-4 text-center">
      <p class="text-xs text-gray-400 dark:text-gray-500">
        Showing {{ filteredTags.length }} of {{ tags.length }} tags
      </p>
    </div>

    <!-- Create/Edit Modal -->
    <teleport to="body">
      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @mousedown.self="closeModal">
          <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @mousedown="closeModal" />
          <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 scale-95 translate-y-2"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
          >
            <div v-if="showModal" class="relative w-full max-w-md bg-white dark:bg-dark-800 rounded-2xl shadow-2xl ring-1 ring-gray-200/80 dark:ring-white/[0.08] overflow-hidden">
              <!-- Modal top accent -->
              <div class="h-1 w-full" :style="{ background: form.color || '#7b2fff' }" />

              <div class="p-6">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-6">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                         :style="{ background: (form.color || '#7b2fff') + '18' }">
                      <TagIcon class="w-5 h-5" :style="{ color: form.color || '#7b2fff' }" />
                    </div>
                    <div>
                      <h2 class="text-lg font-bold dark:text-white">{{ editingTag ? 'Edit Tag' : 'Create Tag' }}</h2>
                      <p class="text-xs text-gray-500 dark:text-gray-400">{{ editingTag ? 'Update tag details' : 'Add a new tag for projects' }}</p>
                    </div>
                  </div>
                  <button @click="closeModal" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors">
                    <XMarkIcon class="w-5 h-5" />
                  </button>
                </div>

                <form @submit.prevent="handleSubmit" class="space-y-5">
                  <!-- Name -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Name <span class="text-red-400">*</span></label>
                    <input
                      v-model="form.name"
                      @input="autoSlug"
                      type="text"
                      placeholder="e.g. Vue.js"
                      class="w-full px-4 py-2.5 rounded-xl bg-gray-50 dark:bg-dark-700 border border-gray-200 dark:border-dark-500 text-sm text-gray-900 dark:text-white placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-violet/30 dark:focus:ring-brand-cyan/30 focus:border-brand-violet dark:focus:border-brand-cyan transition-all"
                      required
                    />
                  </div>

                  <!-- Slug -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Slug <span class="text-red-400">*</span></label>
                    <input
                      v-model="form.slug"
                      type="text"
                      placeholder="e.g. vuejs"
                      class="w-full px-4 py-2.5 rounded-xl bg-gray-50 dark:bg-dark-700 border border-gray-200 dark:border-dark-500 text-sm font-mono text-gray-900 dark:text-white placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-violet/30 dark:focus:ring-brand-cyan/30 focus:border-brand-violet dark:focus:border-brand-cyan transition-all"
                      required
                    />
                  </div>

                  <!-- Color -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Color</label>
                    <div class="flex items-center gap-3">
                      <div class="relative">
                        <input
                          v-model="form.color"
                          type="color"
                          class="w-12 h-12 rounded-xl border-2 border-gray-200 dark:border-dark-500 cursor-pointer overflow-hidden appearance-none bg-transparent [&::-webkit-color-swatch-wrapper]:p-0 [&::-webkit-color-swatch]:rounded-lg [&::-webkit-color-swatch]:border-none"
                        />
                      </div>
                      <input
                        v-model="form.color"
                        type="text"
                        placeholder="#7b2fff"
                        maxlength="7"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-gray-50 dark:bg-dark-700 border border-gray-200 dark:border-dark-500 text-sm font-mono text-gray-900 dark:text-white placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-violet/30 dark:focus:ring-brand-cyan/30 focus:border-brand-violet dark:focus:border-brand-cyan transition-all"
                      />
                    </div>
                    <!-- Preset Colors -->
                    <div class="flex flex-wrap gap-2 mt-3">
                      <button
                        v-for="preset in presetColors"
                        :key="preset.color"
                        type="button"
                        @click="form.color = preset.color"
                        class="w-7 h-7 rounded-lg ring-1 transition-all hover:scale-110 shadow-sm"
                        :class="form.color === preset.color ? 'ring-2 ring-offset-2 ring-offset-white dark:ring-offset-dark-800' : 'ring-gray-200 dark:ring-white/10'"
                        :style="{ background: preset.color, '--tw-ring-color': preset.color }"
                        :title="preset.name"
                      />
                    </div>
                  </div>

                  <!-- Preview -->
                  <div class="pt-2">
                    <label class="block text-xs font-semibold text-gray-400 dark:text-gray-500 mb-2 uppercase tracking-wider">Preview</label>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-dark-700/50 border border-gray-100 dark:border-dark-600">
                      <div class="w-9 h-9 rounded-lg flex items-center justify-center"
                           :style="{ background: (form.color || '#7b2fff') + '18' }">
                        <TagIcon class="w-4 h-4" :style="{ color: form.color || '#7b2fff' }" />
                      </div>
                      <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ form.name || 'Tag Name' }}</p>
                        <p class="text-[10px] font-mono text-gray-400">{{ form.slug || 'tag-slug' }}</p>
                      </div>
                      <span class="ml-auto text-xs font-semibold px-2.5 py-1 rounded-full"
                            :style="{ background: (form.color || '#7b2fff') + '18', color: form.color || '#7b2fff' }">
                        Tag
                      </span>
                    </div>
                  </div>

                  <!-- Error -->
                  <div v-if="formError" class="p-3 rounded-xl bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-900/30">
                    <p class="text-sm text-red-600 dark:text-red-400">{{ formError }}</p>
                  </div>

                  <!-- Actions -->
                  <div class="flex items-center gap-3 pt-2">
                    <button type="button" @click="closeModal"
                            class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-dark-600 hover:bg-gray-200 dark:hover:bg-dark-500 transition-colors">
                      Cancel
                    </button>
                    <button type="submit" :disabled="saving"
                            class="flex-1 btn-primary flex items-center justify-center gap-2 text-sm"
                            :style="{ '--btn-color': form.color || undefined }">
                      <div v-if="saving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                      <template v-else>
                        <CheckIcon class="w-4 h-4" />
                        {{ editingTag ? 'Update' : 'Create' }}
                      </template>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </transition>
        </div>
      </transition>
    </teleport>

    <!-- Delete Confirmation Modal -->
    <teleport to="body">
      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @mousedown.self="showDeleteModal = false">
          <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @mousedown="showDeleteModal = false" />
          <div class="relative w-full max-w-sm bg-white dark:bg-dark-800 rounded-2xl shadow-2xl ring-1 ring-gray-200/80 dark:ring-white/[0.08] p-6">
            <div class="text-center">
              <div class="w-14 h-14 rounded-2xl bg-red-50 dark:bg-red-900/10 flex items-center justify-center mx-auto mb-4">
                <TrashIcon class="w-7 h-7 text-red-500" />
              </div>
              <h3 class="text-lg font-bold dark:text-white mb-1">Delete Tag</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                Are you sure you want to delete
              </p>
              <p class="text-sm mb-1">
                <span class="inline-flex items-center gap-1.5 font-semibold px-2.5 py-1 rounded-lg"
                      :style="{ background: (deletingTag?.color || '#7b2fff') + '18', color: deletingTag?.color || '#7b2fff' }">
                  <TagIcon class="w-3.5 h-3.5" />
                  {{ deletingTag?.name }}
                </span>
              </p>
              <p v-if="(deletingTag?.projects_count ?? 0) > 0" class="text-xs text-amber-500 dark:text-amber-400 mt-2">
                ⚠ This tag is used in {{ deletingTag.projects_count }} project(s)
              </p>
            </div>
            <div class="flex items-center gap-3 mt-6">
              <button @click="showDeleteModal = false"
                      class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-dark-600 hover:bg-gray-200 dark:hover:bg-dark-500 transition-colors">
                Cancel
              </button>
              <button @click="handleDelete" :disabled="deleting"
                      class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-red-500 hover:bg-red-600 transition-colors flex items-center justify-center gap-2">
                <div v-if="deleting" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                <template v-else>
                  <TrashIcon class="w-4 h-4" />
                  Delete
                </template>
              </button>
            </div>
          </div>
        </div>
      </transition>
    </teleport>

    <!-- Success Toast -->
    <teleport to="body">
      <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 translate-y-4"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-4"
      >
        <div v-if="toast" class="fixed bottom-6 right-6 z-[60] flex items-center gap-3 px-5 py-3 rounded-xl shadow-lg ring-1"
             :class="toast.type === 'success'
               ? 'bg-green-50 dark:bg-green-900/20 ring-green-200 dark:ring-green-800/30 text-green-700 dark:text-green-400'
               : 'bg-red-50 dark:bg-red-900/20 ring-red-200 dark:ring-red-800/30 text-red-700 dark:text-red-400'">
          <CheckCircleIcon v-if="toast.type === 'success'" class="w-5 h-5 shrink-0" />
          <ExclamationTriangleIcon v-else class="w-5 h-5 shrink-0" />
          <span class="text-sm font-medium">{{ toast.message }}</span>
        </div>
      </transition>
    </teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { adminApi } from '@/services/api'
import {
  TagIcon,
  FolderIcon,
  PlusIcon,
  PencilSquareIcon,
  TrashIcon,
  XMarkIcon,
  CheckIcon,
  MagnifyingGlassIcon,
  Squares2X2Icon,
  Bars3Icon,
  SwatchIcon,
  ExclamationTriangleIcon,
  CheckCircleIcon,
} from '@heroicons/vue/24/outline'
import CustomSelect from '@/components/common/CustomSelect.vue'

const tags = ref([])
const loading = ref(true)
const searchQuery = ref('')
const viewMode = ref('grid')
const sortBy = ref('name')

// Modal state
const showModal = ref(false)
const editingTag = ref(null)
const saving = ref(false)
const formError = ref('')
const form = ref({ name: '', slug: '', color: '#7b2fff' })

// Delete state
const showDeleteModal = ref(false)
const deletingTag = ref(null)
const deleting = ref(false)

// Toast
const toast = ref(null)
let toastTimer = null

const presetColors = [
  { name: 'Violet', color: '#7b2fff' },
  { name: 'Cyan', color: '#00d4ff' },
  { name: 'Red', color: '#ff2d20' },
  { name: 'Green', color: '#42b883' },
  { name: 'Blue', color: '#3178c6' },
  { name: 'Orange', color: '#ff6f00' },
  { name: 'Pink', color: '#ec4899' },
  { name: 'Teal', color: '#14b8a6' },
  { name: 'Amber', color: '#f59e0b' },
  { name: 'Indigo', color: '#6366f1' },
  { name: 'Sky', color: '#38bdf8' },
  { name: 'Slate', color: '#64748b' },
]

// Stats
const usedTags = computed(() => tags.value.filter(t => (t.projects_count ?? 0) > 0).length)
const unusedTags = computed(() => tags.value.filter(t => (t.projects_count ?? 0) === 0).length)
const uniqueColors = computed(() => new Set(tags.value.map(t => t.color || '#7b2fff')).size)

// Filtered & sorted
const filteredTags = computed(() => {
  let list = [...tags.value]
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(t => t.name.toLowerCase().includes(q) || t.slug.toLowerCase().includes(q))
  }
  switch (sortBy.value) {
    case 'name': list.sort((a, b) => a.name.localeCompare(b.name)); break
    case 'name-desc': list.sort((a, b) => b.name.localeCompare(a.name)); break
    case 'projects': list.sort((a, b) => (b.projects_count ?? 0) - (a.projects_count ?? 0)); break
    case 'newest': list.sort((a, b) => new Date(b.created_at) - new Date(a.created_at)); break
    case 'oldest': list.sort((a, b) => new Date(a.created_at) - new Date(b.created_at)); break
  }
  return list
})

function formatDate(dateStr) {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

function showToast(message, type = 'success') {
  clearTimeout(toastTimer)
  toast.value = { message, type }
  toastTimer = setTimeout(() => { toast.value = null }, 3000)
}

function slugify(text) {
  return text.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')
}

function autoSlug() {
  if (!editingTag.value) {
    form.value.slug = slugify(form.value.name)
  }
}

// CRUD
async function fetchTags() {
  loading.value = true
  try {
    const res = await adminApi.getTags()
    tags.value = res.data.data || []
  } catch {
    showToast('Failed to load tags', 'error')
  } finally {
    loading.value = false
  }
}

function openCreateModal() {
  editingTag.value = null
  form.value = { name: '', slug: '', color: '#7b2fff' }
  formError.value = ''
  showModal.value = true
}

function openEditModal(tag) {
  editingTag.value = tag
  form.value = { name: tag.name, slug: tag.slug, color: tag.color || '#7b2fff' }
  formError.value = ''
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  editingTag.value = null
  formError.value = ''
}

async function handleSubmit() {
  saving.value = true
  formError.value = ''
  try {
    if (editingTag.value) {
      await adminApi.updateTag(editingTag.value.id, form.value)
      showToast('Tag updated successfully')
    } else {
      await adminApi.createTag(form.value)
      showToast('Tag created successfully')
    }
    closeModal()
    await fetchTags()
  } catch (err) {
    const msg = err.response?.data?.message || err.response?.data?.errors
    if (typeof msg === 'object') {
      formError.value = Object.values(msg).flat().join(', ')
    } else {
      formError.value = msg || 'Something went wrong'
    }
  } finally {
    saving.value = false
  }
}

function confirmDelete(tag) {
  deletingTag.value = tag
  showDeleteModal.value = true
}

async function handleDelete() {
  deleting.value = true
  try {
    await adminApi.deleteTag(deletingTag.value.id)
    showToast('Tag deleted successfully')
    showDeleteModal.value = false
    deletingTag.value = null
    await fetchTags()
  } catch {
    showToast('Failed to delete tag', 'error')
  } finally {
    deleting.value = false
  }
}

onMounted(fetchTags)
</script>
