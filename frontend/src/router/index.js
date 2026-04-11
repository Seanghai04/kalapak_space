import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  // Public
  {
    path: '/',
    component: () => import('@/layouts/PublicLayout.vue'),
    children: [
      { path: '', name: 'home', component: () => import('@/views/public/HomeView.vue'), meta: { title: 'Kalapak Code Team | Modern Tech Solutions from Cambodia' } },
      { path: 'about', name: 'about', component: () => import('@/views/public/AboutView.vue'), meta: { title: 'About Us – Kalapak Code Team' } },
      { path: 'projects', name: 'projects', component: () => import('@/views/public/ProjectsView.vue'), meta: { title: 'Projects – Kalapak Code Team' } },
      { path: 'projects/:slug', name: 'project-detail', component: () => import('@/views/public/ProjectDetailView.vue'), meta: { title: 'Project – Kalapak Code Team' } },
      { path: 'blog', name: 'blog', component: () => import('@/views/public/BlogView.vue'), meta: { title: 'Blog – Kalapak Code Team' } },
      { path: 'blog/:slug', name: 'blog-post', component: () => import('@/views/public/BlogPostView.vue'), meta: { title: 'Blog – Kalapak Code Team' } },
      { path: 'join', name: 'join', component: () => import('@/views/public/JoinUsView.vue'), meta: { title: 'Join Us – Kalapak Code Team' } },
      { path: 'contact', name: 'contact', component: () => import('@/views/public/ContactView.vue'), meta: { title: 'Contact – Kalapak Code Team' } },
      { path: 'privacy', name: 'privacy', component: () => import('@/views/public/PrivacyPolicyView.vue'), meta: { title: 'Privacy Policy – Kalapak Code Team' } },
      { path: 'terms', name: 'terms', component: () => import('@/views/public/TermsOfServiceView.vue'), meta: { title: 'Terms of Service – Kalapak Code Team' } },
    ],
  },
  // Auth
  {
    path: '/auth',
    component: () => import('@/layouts/AuthLayout.vue'),
    meta: { guestOnly: true },
    children: [
      { path: 'login', name: 'login', component: () => import('@/views/auth/LoginView.vue') },
      { path: 'register', name: 'register', component: () => import('@/views/auth/RegisterView.vue') },
      { path: 'forgot-password', name: 'forgot-password', component: () => import('@/views/auth/ForgotPasswordView.vue') },
      { path: 'reset-password/:token', name: 'reset-password', component: () => import('@/views/auth/ResetPasswordView.vue') },
      { path: 'google/callback', name: 'google-callback', component: () => import('@/views/auth/GoogleCallbackView.vue') },
      { path: 'github/callback', name: 'github-callback', component: () => import('@/views/auth/GitHubCallbackView.vue') },
    ],
  },
  // Member
  {
    path: '/member',
    component: () => import('@/layouts/PublicLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      { path: 'profile', name: 'member-profile', component: () => import('@/views/member/ProfileView.vue') },
      { path: 'settings', name: 'member-settings', component: () => import('@/views/member/SettingsView.vue') },
    ],
  },
  // Admin
  {
    path: '/admin',
    component: () => import('@/layouts/AdminLayout.vue'),
    meta: { requiresAuth: true, requiresAdmin: true },
    children: [
      { path: '', name: 'admin-dashboard', component: () => import('@/views/admin/DashboardView.vue') },
      { path: 'users', name: 'admin-users', component: () => import('@/views/admin/UsersView.vue') },
      { path: 'users/:id', name: 'admin-user-edit', component: () => import('@/views/admin/UserEditView.vue') },
      { path: 'projects', name: 'admin-projects', component: () => import('@/views/admin/ProjectsView.vue') },
      { path: 'projects/create', name: 'admin-project-create', component: () => import('@/views/admin/ProjectFormView.vue') },
      { path: 'projects/:id/edit', name: 'admin-project-edit', component: () => import('@/views/admin/ProjectFormView.vue') },
      { path: 'blog', name: 'admin-blog', component: () => import('@/views/admin/BlogPostsView.vue') },
      { path: 'blog/create', name: 'admin-blog-create', component: () => import('@/views/admin/BlogPostFormView.vue') },
      { path: 'blog/:id/edit', name: 'admin-blog-edit', component: () => import('@/views/admin/BlogPostFormView.vue') },
      { path: 'applications', name: 'admin-applications', component: () => import('@/views/admin/ApplicationsView.vue') },
      { path: 'messages', name: 'admin-messages', component: () => import('@/views/admin/MessagesView.vue') },
      { path: 'team', name: 'admin-team', component: () => import('@/views/admin/TeamView.vue') },
      { path: 'media', name: 'admin-media', component: () => import('@/views/admin/MediaView.vue') },
      { path: 'settings', name: 'admin-settings', component: () => import('@/views/admin/SettingsView.vue') },
      { path: 'activity-logs', name: 'admin-activity-logs', component: () => import('@/views/admin/ActivityLogsView.vue') },
      { path: 'roles', name: 'admin-roles', component: () => import('@/views/admin/RolesView.vue') },
      { path: 'tags', name: 'admin-tags', component: () => import('@/views/admin/TagsView.vue') },
    ],
  },
  // 404
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/views/NotFoundView.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) return savedPosition
    if (to.hash) return { el: to.hash, behavior: 'smooth' }
    return { top: 0, behavior: 'smooth' }
  },
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  // Fetch user if token exists but user not loaded
  if (authStore.token && !authStore.user) {
    await authStore.fetchMe()
  }

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return next({ name: 'login', query: { redirect: to.fullPath } })
  }

  if (to.meta.requiresAdmin && !authStore.isAdmin) {
    return next({ name: 'home' })
  }

  if (to.meta.guestOnly && authStore.isAuthenticated) {
    return next({ name: 'home' })
  }

  next()
})

router.afterEach((to) => {
  document.title = to.meta.title || 'Kalapak Code Team | Modern Tech Solutions from Cambodia'
})

export default router
