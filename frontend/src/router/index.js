import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('@/views/HomeView.vue'),
      meta: { title: 'Home' }
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/auth/LoginView.vue'),
      meta: { title: 'Login', guest: true }
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('@/views/auth/RegisterView.vue'),
      meta: { title: 'Register', guest: true }
    },
    {
      path: '/auth/callback',
      name: 'auth-callback',
      component: () => import('@/views/auth/CallbackView.vue'),
      meta: { title: 'Loading...' }
    },
    {
      path: '/services',
      name: 'services',
      component: () => import('@/views/ServiceListView.vue'),
      meta: { title: 'Services' }
    },
    {
      path: '/services/:slug',
      name: 'service-detail',
      component: () => import('@/views/ServiceDetailView.vue'),
      meta: { title: 'Service Detail' }
    },
    {
      path: '/order/track',
      name: 'track-order',
      component: () => import('@/views/TrackOrderView.vue'),
      meta: { title: 'Track Order' }
    },
    {
      path: '/order/:orderNumber',
      name: 'order-detail',
      component: () => import('@/views/OrderDetailView.vue'),
      meta: { title: 'Order Detail' }
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('@/views/dashboard/DashboardView.vue'),
      meta: { title: 'Dashboard', requiresAuth: true }
    },
    {
      path: '/dashboard/orders',
      name: 'my-orders',
      component: () => import('@/views/dashboard/OrdersView.vue'),
      meta: { title: 'My Orders', requiresAuth: true }
    },
    {
      path: '/dashboard/wallet',
      name: 'wallet',
      component: () => import('@/views/dashboard/WalletView.vue'),
      meta: { title: 'Wallet', requiresAuth: true }
    },
    {
      path: '/dashboard/profile',
      name: 'profile',
      component: () => import('@/views/dashboard/ProfileView.vue'),
      meta: { title: 'Profile', requiresAuth: true }
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('@/views/NotFoundView.vue'),
      meta: { title: '404 - Not Found' }
    }
  ],
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

// Navigation guard
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  
  // Set page title
  document.title = `${to.meta.title || 'Page'} - ${import.meta.env.VITE_APP_NAME}`

  // Check if route requires authentication
  if (to.meta.requiresAuth) {
    const isAuthenticated = await authStore.checkAuth()
    if (!isAuthenticated) {
      next({ name: 'login', query: { redirect: to.fullPath } })
      return
    }
  }

  // Redirect authenticated users away from guest pages
  if (to.meta.guest && authStore.isAuthenticated) {
    next({ name: 'dashboard' })
    return
  }

  next()
})

export default router
