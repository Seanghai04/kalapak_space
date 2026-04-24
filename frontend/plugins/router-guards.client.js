import { useAuthStore } from "@/stores/auth";

export default defineNuxtPlugin((nuxtApp) => {
  const router = nuxtApp.$router;

  router.beforeEach(async (to) => {
    const authStore = useAuthStore();

    if (authStore.token && !authStore.user) {
      await authStore.fetchMe();
    }

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
      return { name: "login", query: { redirect: to.fullPath } };
    }

    if (to.meta.requiresAdmin && !authStore.isAdmin) {
      return { name: "home" };
    }

    if (to.meta.requiresSuperAdmin && !authStore.isSuperAdmin) {
      return { name: "admin-dashboard" };
    }

    if (to.meta.guestOnly && authStore.isAuthenticated) {
      return { name: "home" };
    }

    return true;
  });

  router.afterEach((to) => {
    document.title =
      to.meta.title || "Kalapak Code Team | Modern Tech Solutions from Cambodia";
  });
});
