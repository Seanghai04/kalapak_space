<template>
  <nav
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-500"
    :class="[
      scrolled
        ? 'nav-scrolled backdrop-blur-2xl shadow-lg shadow-black/5 dark:shadow-black/20'
        : 'nav-top backdrop-blur-md'
    ]"
  >
    <!-- Top accent line -->
    <div class="absolute top-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-brand-violet/40 dark:via-brand-cyan/40 to-transparent" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-[68px]">

        <!-- ── Logo ── -->
        <router-link to="/" class="nav-logo group flex items-center gap-2.5 relative">
          <img src="https://res.cloudinary.com/kalapak/image/upload/q_auto/f_auto/v1775860922/Logo_kalapak_om1ygl.png" alt="Kalapak Logo" class="w-9 h-9 rounded-xl object-contain shadow-md shadow-brand-violet/20 dark:shadow-brand-cyan/20 group-hover:shadow-lg group-hover:shadow-brand-violet/30 dark:group-hover:shadow-brand-cyan/30 transition-all duration-300 group-hover:scale-105" />
          <div class="flex flex-col leading-none">
            <span class="text-[15px] font-sans font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-brand-violet dark:group-hover:text-brand-cyan transition-colors duration-300">Kalapak</span>
            <span class="text-[9px] font-medium uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500">Code Team</span>
          </div>
        </router-link>

        <!-- ── Desktop Navigation ── -->
        <div class="hidden lg:flex items-center">
          <div class="nav-links-container flex items-center gap-0.5 px-1 py-1 rounded-2xl bg-gray-100/70 dark:bg-white/[0.04] border border-gray-200/50 dark:border-white/[0.06]">
            <router-link
              v-for="link in navLinks"
              :key="link.name"
              :to="link.to"
              class="nav-link relative px-3 py-1.5 rounded-xl text-[12.5px] font-semibold tracking-wide transition-all duration-300 whitespace-nowrap"
              :class="[
                isActive(link.name)
                  ? 'nav-link-active text-white shadow-md shadow-brand-violet/25 dark:shadow-brand-cyan/25'
                  : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
              ]"
            >
              <span class="relative z-10">
                {{ link.label }}
              </span>
              <!-- Active pill background -->
              <span
                v-if="isActive(link.name)"
                class="absolute inset-0 rounded-xl bg-gradient-to-r from-brand-violet to-brand-cyan"
              />
              <!-- Hover background -->
              <span
                v-else
                class="absolute inset-0 rounded-xl bg-white dark:bg-white/[0.06] opacity-0 hover-bg transition-opacity duration-200"
              />
            </router-link>
          </div>
        </div>

        <!-- ── Right Actions ── -->
        <div class="flex items-center gap-2">
          <!-- Search button -->
          <button
            @click="openSearch"
            class="nav-action-btn hidden sm:flex items-center gap-2 px-3 py-2 rounded-xl text-xs text-gray-400 dark:text-gray-500 border border-gray-200/70 dark:border-white/[0.06] bg-white/50 dark:bg-white/[0.02] hover:border-brand-violet/30 dark:hover:border-brand-cyan/30 hover:text-gray-600 dark:hover:text-gray-300 transition-all duration-200"
            title="Search"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            <span class="hidden md:inline font-medium">Search</span>
            <kbd class="hidden md:inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-md bg-gray-100 dark:bg-white/[0.06] text-[10px] font-code text-gray-400 border border-gray-200/50 dark:border-white/[0.06]">⌘K</kbd>
          </button>

          <!-- Divider -->
          <div class="hidden sm:block w-px h-6 bg-gray-200 dark:bg-white/10" />

          <ThemeToggle />

          <!-- Auth Section -->
          <template v-if="authStore.isAuthenticated">
            <!-- Notification bell -->
            <div class="relative" ref="notifDropdownRef">
              <button
                @click="toggleNotifications"
                class="nav-icon-btn relative p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/[0.06] transition-all duration-200"
              >
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                <span v-if="notifStore.unreadCount > 0" class="absolute top-1 right-1 min-w-[16px] h-4 flex items-center justify-center px-1 rounded-full bg-red-500 text-white text-[10px] font-bold ring-2 ring-white dark:ring-dark-800">
                  {{ notifStore.unreadCount > 99 ? '99+' : notifStore.unreadCount }}
                </span>
              </button>

              <!-- Notification Backdrop (mobile only) -->
              <div v-if="notifOpen" class="fixed inset-0 bg-black/30 z-40 sm:hidden" @click="notifOpen = false" />
              <!-- Notification Dropdown -->
              <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
              >
                <div v-if="notifOpen" class="fixed left-4 right-4 top-20 z-50 sm:absolute sm:left-auto sm:right-0 sm:top-full sm:mt-3 sm:w-96 rounded-2xl bg-white dark:bg-dark-800 shadow-xl shadow-black/10 dark:shadow-black/40 ring-1 ring-gray-200/80 dark:ring-white/[0.08] overflow-hidden">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-white/[0.06]">
                      <h3 class="text-sm font-bold text-gray-900 dark:text-white">Notifications</h3>
                      <button
                        v-if="notifStore.unreadCount > 0"
                        @click="notifStore.markAllRead()"
                        class="text-[11px] font-semibold text-brand-violet dark:text-brand-cyan hover:underline"
                      >
                        Mark all read
                      </button>
                    </div>

                    <!-- Notification list -->
                    <div class="max-h-80 overflow-y-auto">
                      <div v-if="notifStore.loading && notifStore.notifications.length === 0" class="flex items-center justify-center py-8">
                        <svg class="w-5 h-5 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                      </div>

                      <div v-else-if="notifStore.notifications.length === 0" class="flex flex-col items-center justify-center py-10 px-4">
                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                        <p class="text-sm text-gray-400 dark:text-gray-500">No notifications yet</p>
                      </div>

                      <div v-else>
                        <div
                          v-for="notif in notifStore.notifications"
                          :key="notif.id"
                          class="group relative flex gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-white/[0.03] transition-colors cursor-pointer border-b border-gray-50 dark:border-white/[0.03] last:border-0"
                          :class="{ 'bg-brand-violet/[0.03] dark:bg-brand-cyan/[0.03]': !notif.read_at }"
                          @click="handleNotifClick(notif)"
                        >
                          <!-- Icon -->
                          <div class="flex-shrink-0 mt-0.5">
                            <div
                              class="w-8 h-8 rounded-lg flex items-center justify-center"
                              :class="notifIconClass(notif.data?.icon)"
                            >
                              <svg v-if="notif.data?.icon === 'success'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                              <svg v-else-if="notif.data?.icon === 'warning'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                              <svg v-else-if="notif.data?.icon === 'error'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                            </div>
                          </div>

                          <!-- Content -->
                          <div class="flex-1 min-w-0">
                            <p class="text-[13px] font-semibold text-gray-900 dark:text-white truncate">
                              {{ notif.data?.title || 'Notification' }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mt-0.5">
                              {{ notif.data?.message }}
                            </p>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">
                              {{ formatTime(notif.created_at) }}
                            </p>
                          </div>

                          <!-- Unread dot -->
                          <div v-if="!notif.read_at" class="flex-shrink-0 mt-2">
                            <span class="w-2 h-2 rounded-full bg-brand-violet dark:bg-brand-cyan block" />
                          </div>

                          <!-- Delete button -->
                          <button
                            @click.stop="notifStore.deleteNotification(notif.id)"
                            class="absolute top-2 right-2 p-1 rounded-md text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 opacity-0 group-hover:opacity-100 transition-all"
                          >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                          </button>
                        </div>
                      </div>
                    </div>
                </div>
              </transition>
            </div>

            <!-- User Profile Dropdown -->
            <div class="relative" ref="profileDropdownRef">
              <button
                @click="profileOpen = !profileOpen"
                class="nav-profile-btn flex items-center gap-2.5 pl-2 pr-3 py-1.5 rounded-xl hover:bg-gray-100/80 dark:hover:bg-white/[0.05] transition-all duration-200"
              >
                <div class="relative">
                  <img
                    v-if="authStore.user?.avatar"
                    :src="authStore.user.avatar"
                    :alt="authStore.user?.name"
                    class="w-8 h-8 rounded-lg object-cover ring-2 ring-brand-violet/20 dark:ring-brand-cyan/20"
                  />
                  <div v-else class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-violet to-brand-cyan flex items-center justify-center text-white text-xs font-bold shadow-sm">
                    {{ authStore.user?.name?.charAt(0)?.toUpperCase() || '?' }}
                  </div>
                  <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-green-500 ring-2 ring-white dark:ring-dark-800" />
                </div>
                <div class="hidden sm:flex flex-col items-start leading-none">
                  <span class="text-[13px] font-semibold text-gray-700 dark:text-gray-200 max-w-[100px] truncate">
                    {{ authStore.user?.name || 'User' }}
                  </span>
                  <span class="text-[10px] text-gray-400 dark:text-gray-500">
                    {{ authStore.user?.role?.name || 'Member' }}
                  </span>
                </div>
                <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-300" :class="{ 'rotate-180': profileOpen }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              <!-- Profile Dropdown -->
              <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 scale-95 -translate-y-1"
                enter-to-class="opacity-100 scale-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 scale-100 translate-y-0"
                leave-to-class="opacity-0 scale-95 -translate-y-1"
              >
                <div v-if="profileOpen" class="absolute right-0 mt-3 w-64 rounded-2xl bg-white dark:bg-dark-800 shadow-xl shadow-black/10 dark:shadow-black/40 ring-1 ring-gray-200/80 dark:ring-white/[0.08] py-2 z-50 overflow-hidden">
                  <!-- User info header -->
                  <div class="px-4 py-3.5 border-b border-gray-100 dark:border-white/[0.06]">
                    <div class="flex items-center gap-3">
                      <img
                        v-if="authStore.user?.avatar"
                        :src="authStore.user.avatar"
                        class="w-10 h-10 rounded-xl object-cover"
                      />
                      <div v-else class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-violet to-brand-cyan flex items-center justify-center text-white font-bold">
                        {{ authStore.user?.name?.charAt(0)?.toUpperCase() || '?' }}
                      </div>
                      <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ authStore.user?.name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ authStore.user?.email }}</p>
                      </div>
                    </div>
                    <span v-if="authStore.user?.role?.name" class="inline-flex items-center gap-1 mt-2.5 px-2.5 py-1 text-[10px] font-semibold rounded-lg bg-brand-violet/8 text-brand-violet dark:bg-brand-cyan/8 dark:text-brand-cyan uppercase tracking-wider">
                      <span class="w-1 h-1 rounded-full bg-current" />
                      {{ authStore.user.role.name }}
                    </span>
                  </div>

                  <!-- Menu items -->
                  <div class="py-1.5 px-1.5">
                    <router-link
                      to="/member/profile"
                      class="dropdown-item"
                      @click="profileOpen = false"
                    >
                      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                      <span>My Profile</span>
                    </router-link>
                    <router-link
                      to="/member/settings"
                      class="dropdown-item"
                      @click="profileOpen = false"
                    >
                      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                      <span>Settings</span>
                    </router-link>
                    <router-link
                      v-if="authStore.isAdmin"
                      :to="{ name: 'admin-dashboard' }"
                      class="dropdown-item"
                      @click="profileOpen = false"
                    >
                      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" /></svg>
                      <span>Dashboard</span>
                      <span class="ml-auto text-[9px] font-bold tracking-wider uppercase px-1.5 py-0.5 rounded-md bg-brand-violet/10 text-brand-violet dark:bg-brand-cyan/10 dark:text-brand-cyan">Admin</span>
                    </router-link>
                  </div>

                  <!-- Logout -->
                  <div class="border-t border-gray-100 dark:border-white/[0.06] mt-1 pt-1 px-1.5 pb-0.5">
                    <button
                      @click="handleLogout(); profileOpen = false"
                      class="dropdown-item dropdown-item-danger w-full"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                      <span>Sign Out</span>
                    </button>
                  </div>
                </div>
              </transition>
            </div>
          </template>

          <!-- Guest actions -->
          <template v-else>
            <router-link :to="{ name: 'login' }" class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-[13px] font-semibold text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-white/[0.05] transition-all duration-200">
              Sign In
            </router-link>
            <router-link :to="{ name: 'register' }" class="nav-cta-btn group relative hidden sm:inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-[13px] font-bold text-white overflow-hidden shadow-md shadow-brand-violet/20 dark:shadow-brand-cyan/20 hover:shadow-lg hover:shadow-brand-violet/30 dark:hover:shadow-brand-cyan/30 hover:-translate-y-[1px] transition-all duration-300">
              <span class="relative z-10">Get Started</span>
              <svg class="relative z-10 w-3.5 h-3.5 transition-transform duration-300 group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
              <span class="absolute inset-0 bg-gradient-to-r from-brand-violet to-brand-cyan" />
              <span class="absolute inset-0 bg-gradient-to-r from-brand-cyan to-brand-violet opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
            </router-link>
          </template>

          <!-- Mobile menu button -->
          <button
            class="lg:hidden relative p-2.5 rounded-xl text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/[0.06] transition-all duration-200"
            @click="mobileOpen = !mobileOpen"
          >
            <div class="w-5 h-4 flex flex-col justify-between relative">
              <span class="hamburger-line" :class="{ 'rotate-45 translate-y-[7px]': mobileOpen }" />
              <span class="hamburger-line" :class="{ 'opacity-0 scale-x-0': mobileOpen }" />
              <span class="hamburger-line" :class="{ '-rotate-45 -translate-y-[7px]': mobileOpen }" />
            </div>
          </button>
        </div>
      </div>
    </div>

    <!-- ── Mobile Navigation Panel ── -->
    <transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="mobileOpen" class="lg:hidden fixed inset-0 top-[68px] z-40">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="mobileOpen = false" />

        <!-- Panel -->
        <div class="relative mx-4 mt-3 rounded-2xl bg-white dark:bg-dark-800 shadow-2xl ring-1 ring-gray-200/80 dark:ring-white/[0.08] overflow-hidden max-h-[calc(100vh-100px)] overflow-y-auto">
          <!-- Mobile user profile header -->
          <div v-if="authStore.isAuthenticated" class="flex items-center gap-3 p-5 border-b border-gray-100 dark:border-white/[0.06] bg-gray-50/50 dark:bg-white/[0.02]">
            <img
              v-if="authStore.user?.avatar"
              :src="authStore.user.avatar"
              :alt="authStore.user?.name"
              class="w-12 h-12 rounded-xl object-cover ring-2 ring-brand-violet/20 dark:ring-brand-cyan/20"
            />
            <div v-else class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand-violet to-brand-cyan flex items-center justify-center text-white font-bold text-lg shadow-md">
              {{ authStore.user?.name?.charAt(0)?.toUpperCase() || '?' }}
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ authStore.user?.name }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ authStore.user?.email }}</p>
            </div>
            <span v-if="authStore.user?.role?.name" class="px-2 py-1 text-[9px] font-bold uppercase tracking-wider rounded-lg bg-brand-violet/8 text-brand-violet dark:bg-brand-cyan/8 dark:text-brand-cyan">
              {{ authStore.user.role.name }}
            </span>
          </div>

          <!-- Navigation links -->
          <div class="p-3 space-y-0.5">
            <router-link
              v-for="link in navLinks"
              :key="link.name"
              :to="link.to"
              class="mobile-nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200"
              :class="[
                isActive(link.name)
                  ? 'text-white bg-gradient-to-r from-brand-violet to-brand-cyan shadow-md'
                  : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-white/[0.05]'
              ]"
              @click="mobileOpen = false"
            >
              <component :is="link.icon" class="w-[18px] h-[18px] flex-shrink-0" :class="isActive(link.name) ? 'text-white/80' : 'text-gray-400'" />
              {{ link.label }}
              <svg v-if="isActive(link.name)" class="w-4 h-4 ml-auto text-white/60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            </router-link>
          </div>

          <!-- Mobile auth section -->
          <div v-if="authStore.isAuthenticated" class="p-3 pt-0 space-y-0.5">
            <div class="h-px bg-gray-100 dark:bg-white/[0.06] my-2" />
            <router-link to="/member/profile" class="mobile-nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-white/[0.05]" @click="mobileOpen = false">
              <svg class="w-[18px] h-[18px] flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
              My Profile
            </router-link>
            <router-link v-if="authStore.isAdmin" :to="{ name: 'admin-dashboard' }" class="mobile-nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-white/[0.05]" @click="mobileOpen = false">
              <svg class="w-[18px] h-[18px] flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5"/></svg>
              Dashboard
            </router-link>
            <button
              @click="handleLogout(); mobileOpen = false"
              class="mobile-nav-link flex items-center gap-3 w-full px-4 py-3 rounded-xl text-sm font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10"
            >
              <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
              Sign Out
            </button>
          </div>

          <!-- Guest CTA in mobile -->
          <div v-else class="p-4 pt-2 space-y-2 border-t border-gray-100 dark:border-white/[0.06]">
            <router-link :to="{ name: 'login' }" class="flex items-center justify-center gap-2 w-full py-3 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-white/[0.08] hover:bg-gray-50 dark:hover:bg-white/[0.03] transition-colors" @click="mobileOpen = false">
              Sign In
            </router-link>
            <router-link :to="{ name: 'register' }" class="flex items-center justify-center gap-2 w-full py-3 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-brand-violet to-brand-cyan shadow-md" @click="mobileOpen = false">
              Get Started Free
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </router-link>
          </div>
        </div>
      </div>
    </transition>

    <!-- Bottom accent line (subtle) -->
    <div class="absolute bottom-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-gray-200/50 dark:via-white/[0.04] to-transparent" />
  </nav>

  <!-- ═══ Search Modal ═══ -->
  <SearchModal :open="searchOpen" @close="closeSearch" />

  <!-- Spacer -->
  <div class="h-[68px]" />
</template>

<script setup>
import { ref, h, onMounted, onUnmounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useNotificationStore } from '@/stores/notifications'
import ThemeToggle from '@/components/common/ThemeToggle.vue'
import SearchModal from '@/components/common/SearchModal.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const notifStore = useNotificationStore()
const mobileOpen = ref(false)
const profileOpen = ref(false)
const profileDropdownRef = ref(null)
const notifDropdownRef = ref(null)
const notifOpen = ref(false)
const scrolled = ref(false)

// ── Search state ──
const searchOpen = ref(false)

function openSearch() {
  searchOpen.value = true
}

function closeSearch() {
  searchOpen.value = false
}

function handleSearchKeydown(e) {
  // Ctrl/Cmd+K to open
  if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
    e.preventDefault()
    if (searchOpen.value) closeSearch()
    else openSearch()
  }
  // Escape to close
  if (e.key === 'Escape' && searchOpen.value) {
    closeSearch()
  }
}

// Close search on navigation
watch(() => route.fullPath, () => closeSearch())

// ── Notification helpers ──
function toggleNotifications() {
  notifOpen.value = !notifOpen.value
  if (notifOpen.value) {
    notifStore.fetchNotifications()
  }
}

function handleNotifClick(notif) {
  if (!notif.read_at) {
    notifStore.markAsRead(notif.id)
  }
  if (notif.data?.action_url) {
    notifOpen.value = false
    router.push(notif.data.action_url)
  }
}

function notifIconClass(icon) {
  switch (icon) {
    case 'success': return 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400'
    case 'warning': return 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400'
    case 'error': return 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400'
    default: return 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
  }
}

function formatTime(dateStr) {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  const now = new Date()
  const diff = Math.floor((now - date) / 1000)

  if (diff < 60) return 'Just now'
  if (diff < 3600) return `${Math.floor(diff / 60)}m ago`
  if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`
  if (diff < 604800) return `${Math.floor(diff / 86400)}d ago`
  return date.toLocaleDateString()
}

// Start/stop notification polling based on auth
watch(() => authStore.isAuthenticated, (loggedIn) => {
  if (loggedIn) {
    notifStore.startPolling()
  } else {
    notifStore.$reset()
  }
}, { immediate: true })

// ── Icon components (inline functional) ──
const IconHome = (_, { attrs }) => h('svg', { ...attrs, fill: 'none', stroke: 'currentColor', 'stroke-width': '1.8', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25' })
])
const IconAbout = (_, { attrs }) => h('svg', { ...attrs, fill: 'none', stroke: 'currentColor', 'stroke-width': '1.8', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z' })
])
const IconPortfolio = (_, { attrs }) => h('svg', { ...attrs, fill: 'none', stroke: 'currentColor', 'stroke-width': '1.8', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z' })
])
const IconBlog = (_, { attrs }) => h('svg', { ...attrs, fill: 'none', stroke: 'currentColor', 'stroke-width': '1.8', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z' })
])
const IconCareers = (_, { attrs }) => h('svg', { ...attrs, fill: 'none', stroke: 'currentColor', 'stroke-width': '1.8', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z' })
])
const IconContact = (_, { attrs }) => h('svg', { ...attrs, fill: 'none', stroke: 'currentColor', 'stroke-width': '1.8', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75' })
])
const IconDocs = (_, { attrs }) => h('svg', { ...attrs, fill: 'none', stroke: 'currentColor', 'stroke-width': '1.8', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z' })
])

function handleClickOutside(e) {
  if (profileDropdownRef.value && !profileDropdownRef.value.contains(e.target)) {
    profileOpen.value = false
  }
  if (notifDropdownRef.value && !notifDropdownRef.value.contains(e.target)) {
    notifOpen.value = false
  }
}

function handleScroll() {
  scrolled.value = window.scrollY > 20
}

function isActive(name) {
  return route.name === name
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  document.addEventListener('keydown', handleSearchKeydown)
  window.addEventListener('scroll', handleScroll, { passive: true })
  handleScroll()
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  document.removeEventListener('keydown', handleSearchKeydown)
  window.removeEventListener('scroll', handleScroll)
})

const navLinks = [
  { name: 'home', to: '/', label: 'Home', icon: IconHome },
  { name: 'about', to: '/about', label: 'About', icon: IconAbout },
  { name: 'projects', to: '/projects', label: 'Portfolio', icon: IconPortfolio },
  { name: 'blog', to: '/blog', label: 'Insights', icon: IconBlog },
  { name: 'join', to: '/join', label: 'Careers', icon: IconCareers },
  { name: 'contact', to: '/contact', label: 'Contact', icon: IconContact },
  { name: 'docs', to: '/docs', label: 'Docs', icon: IconDocs },
]

function handleLogout() {
  authStore.logout()
  router.push({ name: 'home' })
}
</script>

<style scoped>
/* ── Nav background states ── */
.nav-top {
  background: rgba(255, 255, 255, 0.6);
}
:root.dark .nav-top,
.dark .nav-top {
  background: rgba(2, 0, 36, 0.6);
}

.nav-scrolled {
  background: rgba(255, 255, 255, 0.85);
}
:root.dark .nav-scrolled,
.dark .nav-scrolled {
  background: rgba(2, 0, 36, 0.88);
}

/* ── Active nav link ── */
.nav-link {
  position: relative;
  isolation: isolate;
}

.nav-link .hover-bg {
  z-index: 0;
}

.nav-link:hover .hover-bg {
  opacity: 1;
}

/* ── Hamburger lines ── */
.hamburger-line {
  display: block;
  width: 100%;
  height: 2px;
  border-radius: 2px;
  background: currentColor;
  transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
  transform-origin: center;
}

/* ── Dropdown items ── */
.dropdown-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 12px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 500;
  color: #374151;
  transition: all 0.15s ease;
}

:root.dark .dropdown-item,
.dark .dropdown-item {
  color: #d1d5db;
}

.dropdown-item:hover {
  background: #f3f4f6;
}

:root.dark .dropdown-item:hover,
.dark .dropdown-item:hover {
  background: rgba(255, 255, 255, 0.05);
}

.dropdown-item-danger {
  color: #ef4444 !important;
}

.dropdown-item-danger:hover {
  background: rgba(239, 68, 68, 0.06) !important;
}

:root.dark .dropdown-item-danger:hover,
.dark .dropdown-item-danger:hover {
  background: rgba(239, 68, 68, 0.08) !important;
}


</style>
