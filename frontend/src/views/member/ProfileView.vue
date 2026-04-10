<template>
  <div class="min-h-screen">

    <!-- Hero / Profile Header -->
    <section class="relative overflow-hidden pt-24 pb-32">
      <!-- Background -->
      <div class="absolute inset-0 bg-gradient-to-br from-dark-900 via-dark-800 to-dark-900"></div>
      <div class="absolute top-0 left-1/4 w-[600px] h-[600px] bg-brand-violet/8 rounded-full blur-[150px]"></div>
      <div class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-brand-cyan/6 rounded-full blur-[120px]"></div>
      <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>

      <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-end gap-6">
          <!-- Avatar -->
          <div class="relative group">
            <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl bg-gradient-to-br from-brand-violet to-brand-cyan p-[3px] shadow-lg shadow-brand-violet/25">
              <div class="w-full h-full rounded-[13px] bg-dark-800 overflow-hidden">
                <img
                  v-if="authStore.user?.avatar"
                  :src="authStore.user.avatar"
                  class="w-full h-full object-cover"
                  alt="Profile avatar"
                />
                <div v-else class="w-full h-full flex items-center justify-center text-4xl font-sans font-bold text-white bg-gradient-to-br from-brand-violet/20 to-brand-cyan/20">
                  {{ authStore.user?.name?.charAt(0)?.toUpperCase() }}
                </div>
              </div>
            </div>
            <label class="absolute inset-0 rounded-2xl bg-black/50 opacity-0 group-hover:opacity-100 transition-all cursor-pointer flex items-center justify-center">
              <div class="text-center">
                <svg class="w-8 h-8 text-white mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z" />
                </svg>
                <span class="text-xs text-white/80 font-medium">Change</span>
              </div>
              <input type="file" accept="image/*" class="hidden" @change="uploadAvatar" />
            </label>
            <!-- Upload status indicator -->
            <div v-if="avatarUploading" class="absolute inset-0 rounded-2xl bg-black/60 flex items-center justify-center">
              <svg class="w-8 h-8 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>
          </div>

          <!-- User Info -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 mb-1">
              <h1 class="text-2xl sm:text-3xl font-sans font-bold text-white truncate">{{ authStore.user?.name }}</h1>
              <span
                v-if="authStore.user?.role?.name"
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-sans font-medium tracking-wider"
                :class="authStore.isAdmin ? 'bg-brand-violet/20 text-brand-cyan border border-brand-violet/30' : 'bg-green-500/10 text-green-400 border border-green-500/20'"
              >
                {{ authStore.user.role.name.toUpperCase() }}
              </span>
            </div>
            <p class="text-gray-400 mb-3">{{ authStore.user?.email }}</p>
            <div class="flex flex-wrap items-center gap-4">
              <a
                v-if="authStore.user?.github_url"
                :href="authStore.user.github_url"
                target="_blank"
                class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-brand-cyan transition-colors"
              >
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                GitHub
              </a>
              <a
                v-if="authStore.user?.linkedin_url"
                :href="authStore.user.linkedin_url"
                target="_blank"
                class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-brand-cyan transition-colors"
              >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                LinkedIn
              </a>
              <span class="inline-flex items-center gap-1.5 text-sm text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                Joined {{ joinedDate }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 -mt-16 relative z-20 pb-20">

      <!-- Tab Navigation -->
      <div class="flex gap-1 p-1 rounded-xl bg-dark-800/80 dark:bg-dark-800/80 bg-gray-100 backdrop-blur-sm border border-dark-600/50 dark:border-dark-600/50 border-gray-200 mb-8 w-fit">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          class="flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-medium transition-all"
          :class="activeTab === tab.id
            ? 'bg-gradient-to-r from-brand-violet to-brand-cyan text-white shadow-lg shadow-brand-violet/25'
            : 'text-gray-400 hover:text-white hover:bg-dark-700/50'"
        >
          <span v-html="tab.icon"></span>
          {{ tab.label }}
        </button>
      </div>

      <!-- Profile Tab -->
      <div v-show="activeTab === 'profile'" class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left Column: Profile Info Card -->
        <div class="lg:col-span-1 space-y-6">
          <!-- Quick Info Card -->
          <div class="rounded-2xl border border-dark-600 dark:border-dark-600 border-gray-200 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm p-6">
            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">Quick Info</h3>
            <div class="space-y-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-brand-violet/10 flex items-center justify-center">
                  <svg class="w-5 h-5 text-brand-violet" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                  </svg>
                </div>
                <div class="min-w-0">
                  <p class="text-xs text-gray-500">Full Name</p>
                  <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ authStore.user?.name }}</p>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-brand-cyan/10 flex items-center justify-center">
                  <svg class="w-5 h-5 text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                  </svg>
                </div>
                <div class="min-w-0">
                  <p class="text-xs text-gray-500">Email</p>
                  <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ authStore.user?.email }}</p>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center">
                  <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div class="min-w-0">
                  <p class="text-xs text-gray-500">Status</p>
                  <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-green-400"></span>
                    <p class="text-sm font-medium text-green-400">Active</p>
                  </div>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-yellow-500/10 flex items-center justify-center">
                  <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                  </svg>
                </div>
                <div class="min-w-0">
                  <p class="text-xs text-gray-500">Role</p>
                  <p class="text-sm font-medium text-gray-900 dark:text-white capitalize">{{ authStore.user?.role?.name || 'Member' }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Bio Card -->
          <div class="rounded-2xl border border-dark-600 dark:border-dark-600 border-gray-200 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm p-6">
            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">About</h3>
            <p v-if="authStore.user?.bio" class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">{{ authStore.user.bio }}</p>
            <p v-else class="text-sm text-gray-500 italic">No bio added yet. Edit your profile to add one!</p>
          </div>
        </div>

        <!-- Right Column: Edit Form -->
        <div class="lg:col-span-2">
          <div class="rounded-2xl border border-dark-600 dark:border-dark-600 border-gray-200 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-dark-600/50 dark:border-dark-600/50 border-gray-200">
              <h3 class="text-lg font-sans font-semibold text-gray-900 dark:text-white">Edit Profile</h3>
              <p class="text-sm text-gray-500 mt-0.5">Update your personal information and social links</p>
            </div>

            <form @submit.prevent="handleUpdateProfile" class="p-6 space-y-6">
              <!-- Name & Email Row -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                      </svg>
                    </div>
                    <input
                      v-model="profileForm.name"
                      type="text"
                      required
                      class="w-full pl-12 pr-4 py-3 bg-dark-800/50 dark:bg-dark-800/50 bg-gray-50 border border-dark-600 dark:border-dark-600 border-gray-200 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:border-brand-violet focus:ring-1 focus:ring-brand-violet/50 transition-all"
                    />
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                      </svg>
                    </div>
                    <input
                      v-model="profileForm.email"
                      type="email"
                      disabled
                      class="w-full pl-12 pr-4 py-3 bg-dark-700/30 dark:bg-dark-700/30 bg-gray-100 border border-dark-600/50 dark:border-dark-600/50 border-gray-200 rounded-xl text-gray-500 cursor-not-allowed"
                    />
                  </div>
                  <p class="mt-1.5 text-xs text-gray-500">Email cannot be changed</p>
                </div>
              </div>

              <!-- Bio -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bio</label>
                <div class="relative">
                  <textarea
                    v-model="profileForm.bio"
                    rows="4"
                    maxlength="1000"
                    class="w-full px-4 py-3 bg-dark-800/50 dark:bg-dark-800/50 bg-gray-50 border border-dark-600 dark:border-dark-600 border-gray-200 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:border-brand-violet focus:ring-1 focus:ring-brand-violet/50 transition-all resize-none"
                    placeholder="Tell the world about yourself — what drives you, what you build, what you're passionate about..."
                  ></textarea>
                  <span class="absolute bottom-3 right-3 text-xs text-gray-500">{{ profileForm.bio?.length || 0 }}/1000</span>
                </div>
              </div>

              <!-- Social Links -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Social Links</label>
                <div class="space-y-3">
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                      <svg class="w-5 h-5 text-gray-400" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                    </div>
                    <input
                      v-model="profileForm.github_url"
                      type="url"
                      class="w-full pl-12 pr-4 py-3 bg-dark-800/50 dark:bg-dark-800/50 bg-gray-50 border border-dark-600 dark:border-dark-600 border-gray-200 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:border-brand-violet focus:ring-1 focus:ring-brand-violet/50 transition-all"
                      placeholder="https://github.com/username"
                    />
                  </div>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                      <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </div>
                    <input
                      v-model="profileForm.linkedin_url"
                      type="url"
                      class="w-full pl-12 pr-4 py-3 bg-dark-800/50 dark:bg-dark-800/50 bg-gray-50 border border-dark-600 dark:border-dark-600 border-gray-200 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:border-brand-violet focus:ring-1 focus:ring-brand-violet/50 transition-all"
                      placeholder="https://linkedin.com/in/username"
                    />
                  </div>
                </div>
              </div>

              <!-- Success/Error Message -->
              <transition name="fade">
                <div v-if="profileMsg" class="flex items-center gap-3 px-4 py-3 rounded-xl" :class="profileError ? 'bg-red-500/10 border border-red-500/20' : 'bg-green-500/10 border border-green-500/20'">
                  <svg v-if="!profileError" class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <svg v-else class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                  </svg>
                  <p class="text-sm" :class="profileError ? 'text-red-400' : 'text-green-400'">{{ profileMsg }}</p>
                </div>
              </transition>

              <!-- Submit -->
              <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" @click="resetProfileForm" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-400 hover:text-white hover:bg-dark-700/50 transition-all">
                  Reset
                </button>
                <button
                  type="submit"
                  :disabled="saving"
                  class="relative px-8 py-2.5 rounded-xl font-semibold text-white overflow-hidden group disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                >
                  <div class="absolute inset-0 bg-gradient-to-r from-brand-violet to-brand-cyan group-hover:opacity-90 transition-opacity"></div>
                  <span class="relative flex items-center gap-2">
                    <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ saving ? 'Saving...' : 'Save Changes' }}
                  </span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Security Tab -->
      <div v-show="activeTab === 'security'">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Left: Security Tips -->
          <div class="lg:col-span-1 space-y-6">
            <div class="rounded-2xl border border-dark-600 dark:border-dark-600 border-gray-200 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm p-6">
              <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">Security Tips</h3>
              <div class="space-y-4">
                <div v-for="(tip, i) in securityTips" :key="i" class="flex items-start gap-3">
                  <div class="w-8 h-8 rounded-lg bg-brand-violet/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span v-html="tip.icon"></span>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ tip.title }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ tip.desc }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right: Password Form -->
          <div class="lg:col-span-2">
            <div class="rounded-2xl border border-dark-600 dark:border-dark-600 border-gray-200 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm overflow-hidden">
              <div class="px-6 py-5 border-b border-dark-600/50 dark:border-dark-600/50 border-gray-200">
                <h3 class="text-lg font-sans font-semibold text-gray-900 dark:text-white">Change Password</h3>
                <p class="text-sm text-gray-500 mt-0.5">Ensure your account stays secure by updating your password</p>
              </div>

              <form @submit.prevent="handleUpdatePassword" class="p-6 space-y-6">
                <!-- Current Password -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Password</label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                      </svg>
                    </div>
                    <input
                      v-model="passwordForm.current_password"
                      :type="showCurrentPw ? 'text' : 'password'"
                      required
                      class="w-full pl-12 pr-12 py-3 bg-dark-800/50 dark:bg-dark-800/50 bg-gray-50 border border-dark-600 dark:border-dark-600 border-gray-200 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:border-brand-violet focus:ring-1 focus:ring-brand-violet/50 transition-all"
                      placeholder="Enter current password"
                    />
                    <button type="button" @click="showCurrentPw = !showCurrentPw" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-gray-300 transition-colors">
                      <svg v-if="!showCurrentPw" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                      <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                    </button>
                  </div>
                </div>

                <!-- New Password -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Password</label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                      </svg>
                    </div>
                    <input
                      v-model="passwordForm.password"
                      :type="showNewPw ? 'text' : 'password'"
                      required
                      minlength="8"
                      class="w-full pl-12 pr-12 py-3 bg-dark-800/50 dark:bg-dark-800/50 bg-gray-50 border border-dark-600 dark:border-dark-600 border-gray-200 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:border-brand-violet focus:ring-1 focus:ring-brand-violet/50 transition-all"
                      placeholder="Min. 8 characters"
                    />
                    <button type="button" @click="showNewPw = !showNewPw" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-gray-300 transition-colors">
                      <svg v-if="!showNewPw" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                      <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                    </button>
                  </div>
                  <!-- Strength meter -->
                  <div v-if="passwordForm.password" class="mt-3 space-y-2">
                    <div class="flex gap-1.5">
                      <div v-for="i in 4" :key="i" class="h-1.5 flex-1 rounded-full transition-all duration-300" :class="i <= pwStrength.score ? pwStrength.color : 'bg-dark-600'"></div>
                    </div>
                    <p class="text-xs" :class="pwStrength.textColor">{{ pwStrength.label }}</p>
                  </div>
                </div>

                <!-- Confirm New Password -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm New Password</label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                      </svg>
                    </div>
                    <input
                      v-model="passwordForm.password_confirmation"
                      :type="showConfirmPw ? 'text' : 'password'"
                      required
                      class="w-full pl-12 pr-12 py-3 bg-dark-800/50 dark:bg-dark-800/50 bg-gray-50 border border-dark-600 dark:border-dark-600 border-gray-200 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:border-brand-violet focus:ring-1 focus:ring-brand-violet/50 transition-all"
                      placeholder="Re-enter new password"
                    />
                    <button type="button" @click="showConfirmPw = !showConfirmPw" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-gray-300 transition-colors">
                      <svg v-if="!showConfirmPw" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                      <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                    </button>
                    <!-- Match indicator -->
                    <div v-if="passwordForm.password_confirmation" class="absolute inset-y-0 right-10 flex items-center pr-2">
                      <svg v-if="newPwMatch" class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                      <svg v-else class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                  </div>
                </div>

                <!-- Success/Error Message -->
                <transition name="fade">
                  <div v-if="pwMsg" class="flex items-center gap-3 px-4 py-3 rounded-xl" :class="pwError ? 'bg-red-500/10 border border-red-500/20' : 'bg-green-500/10 border border-green-500/20'">
                    <svg v-if="!pwError" class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <svg v-else class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                    <p class="text-sm" :class="pwError ? 'text-red-400' : 'text-green-400'">{{ pwMsg }}</p>
                  </div>
                </transition>

                <!-- Submit -->
                <div class="flex items-center justify-end gap-3 pt-2">
                  <button type="button" @click="resetPasswordForm" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-400 hover:text-white hover:bg-dark-700/50 transition-all">
                    Cancel
                  </button>
                  <button
                    type="submit"
                    :disabled="savingPw"
                    class="relative px-8 py-2.5 rounded-xl font-semibold text-white overflow-hidden group disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                  >
                    <div class="absolute inset-0 bg-gradient-to-r from-brand-violet to-brand-cyan group-hover:opacity-90 transition-opacity"></div>
                    <span class="relative flex items-center gap-2">
                      <svg v-if="savingPw" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      {{ savingPw ? 'Updating...' : 'Update Password' }}
                    </span>
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Danger Zone Tab -->
      <div v-show="activeTab === 'danger'">
        <div class="max-w-2xl">
          <div class="rounded-2xl border border-red-500/20 bg-red-500/5 backdrop-blur-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-red-500/10">
              <h3 class="text-lg font-sans font-semibold text-red-400">Danger Zone</h3>
              <p class="text-sm text-gray-500 mt-0.5">Irreversible and destructive actions</p>
            </div>
            <div class="p-6 space-y-6">
              <!-- Deactivate account -->
              <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-red-500/10">
                <div>
                  <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Deactivate Account</h4>
                  <p class="text-sm text-gray-500 mt-1">Temporarily disable your account. You can reactivate it later by contacting support.</p>
                </div>
                <button class="px-5 py-2 rounded-xl border border-red-500/30 text-red-400 text-sm font-medium hover:bg-red-500/10 transition-all whitespace-nowrap">
                  Deactivate
                </button>
              </div>
              <!-- Delete account -->
              <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                  <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Delete Account</h4>
                  <p class="text-sm text-gray-500 mt-1">Permanently delete your account and all associated data. This action cannot be undone.</p>
                </div>
                <button class="px-5 py-2 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm font-medium hover:bg-red-500/20 transition-all whitespace-nowrap">
                  Delete Account
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useUiStore } from '@/stores/ui'

const authStore = useAuthStore()
const uiStore = useUiStore()

// Tabs
const activeTab = ref('profile')
const tabs = [
  {
    id: 'profile',
    label: 'Profile',
    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>'
  },
  {
    id: 'security',
    label: 'Security',
    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>'
  },
  {
    id: 'danger',
    label: 'Danger Zone',
    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>'
  },
]

const securityTips = [
  { title: 'Use a strong password', desc: 'Mix uppercase, lowercase, numbers & symbols', icon: '<svg class="w-4 h-4 text-brand-violet" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" /></svg>' },
  { title: 'Change regularly', desc: 'Update your password every 3-6 months', icon: '<svg class="w-4 h-4 text-brand-violet" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182" /></svg>' },
  { title: 'Don\'t reuse passwords', desc: 'Use unique passwords for each service', icon: '<svg class="w-4 h-4 text-brand-violet" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>' },
  { title: 'Enable 2FA', desc: 'Add an extra layer of security (coming soon)', icon: '<svg class="w-4 h-4 text-brand-violet" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>' },
]

// Profile form
const profileForm = ref({ name: '', email: '', bio: '', github_url: '', linkedin_url: '' })
const saving = ref(false)
const profileMsg = ref('')
const profileError = ref(false)
const avatarUploading = ref(false)

// Password form
const passwordForm = ref({ current_password: '', password: '', password_confirmation: '' })
const savingPw = ref(false)
const pwMsg = ref('')
const pwError = ref(false)
const showCurrentPw = ref(false)
const showNewPw = ref(false)
const showConfirmPw = ref(false)

const joinedDate = computed(() => {
  if (!authStore.user?.created_at) return 'N/A'
  return new Date(authStore.user.created_at).toLocaleDateString('en-US', { month: 'long', year: 'numeric' })
})

const newPwMatch = computed(() => {
  return passwordForm.value.password && passwordForm.value.password_confirmation && passwordForm.value.password === passwordForm.value.password_confirmation
})

const pwStrength = computed(() => {
  const pw = passwordForm.value.password
  let score = 0
  if (pw.length >= 8) score++
  if (/[A-Z]/.test(pw)) score++
  if (/[0-9]/.test(pw)) score++
  if (/[^A-Za-z0-9]/.test(pw)) score++
  const levels = [
    { score: 0, label: '', color: 'bg-dark-600', textColor: 'text-gray-500' },
    { score: 1, label: 'Weak', color: 'bg-red-500', textColor: 'text-red-400' },
    { score: 2, label: 'Fair', color: 'bg-yellow-500', textColor: 'text-yellow-400' },
    { score: 3, label: 'Good', color: 'bg-blue-500', textColor: 'text-blue-400' },
    { score: 4, label: 'Strong', color: 'bg-green-500', textColor: 'text-green-400' },
  ]
  return levels[score] || levels[0]
})

onMounted(() => {
  if (authStore.user) {
    profileForm.value = {
      name: authStore.user.name || '',
      email: authStore.user.email || '',
      bio: authStore.user.bio || '',
      github_url: authStore.user.github_url || '',
      linkedin_url: authStore.user.linkedin_url || '',
    }
  }
})

function resetProfileForm() {
  if (authStore.user) {
    profileForm.value = {
      name: authStore.user.name || '',
      email: authStore.user.email || '',
      bio: authStore.user.bio || '',
      github_url: authStore.user.github_url || '',
      linkedin_url: authStore.user.linkedin_url || '',
    }
  }
  profileMsg.value = ''
}

function resetPasswordForm() {
  passwordForm.value = { current_password: '', password: '', password_confirmation: '' }
  pwMsg.value = ''
}

async function handleUpdateProfile() {
  saving.value = true
  profileMsg.value = ''
  try {
    await authStore.updateProfile({
      name: profileForm.value.name,
      bio: profileForm.value.bio,
      github_url: profileForm.value.github_url || null,
      linkedin_url: profileForm.value.linkedin_url || null,
    })
    profileMsg.value = 'Profile updated successfully!'
    profileError.value = false
    uiStore.showToast('Profile updated!', 'success')
  } catch (e) {
    profileMsg.value = e.response?.data?.message || 'Failed to update profile'
    profileError.value = true
  } finally {
    saving.value = false
  }
}

async function handleUpdatePassword() {
  savingPw.value = true
  pwMsg.value = ''
  try {
    await authStore.updatePassword(passwordForm.value)
    pwMsg.value = 'Password updated successfully!'
    pwError.value = false
    passwordForm.value = { current_password: '', password: '', password_confirmation: '' }
    uiStore.showToast('Password updated!', 'success')
  } catch (e) {
    pwMsg.value = e.response?.data?.message || 'Failed to update password'
    pwError.value = true
  } finally {
    savingPw.value = false
  }
}

async function uploadAvatar(event) {
  const file = event.target.files[0]
  if (!file) return
  avatarUploading.value = true
  const formData = new FormData()
  formData.append('avatar', file)
  try {
    await authStore.uploadAvatar(formData)
    uiStore.showToast('Avatar updated!', 'success')
  } catch {
    uiStore.showToast('Failed to upload avatar', 'error')
  } finally {
    avatarUploading.value = false
  }
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: all 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
