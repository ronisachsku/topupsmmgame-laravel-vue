import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api, { getCsrfCookie } from '@/utils/api'
import router from '@/router'
import { useToast } from 'vue-toastification'

const toast = useToast()

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('auth_token') || null)
  const loading = ref(false)

  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  async function checkAuth() {
    if (!token.value) {
      return false
    }

    try {
      const response = await api.get('/auth/user')
      if (response.data.success) {
        user.value = response.data.data
        return true
      }
    } catch (error) {
      logout()
      return false
    }
  }

  async function register(credentials) {
    loading.value = true
    try {
      await getCsrfCookie()
      const response = await api.post('/auth/register', credentials)
      
      if (response.data.success) {
        token.value = response.data.data.access_token
        user.value = response.data.data.user
        localStorage.setItem('auth_token', token.value)
        toast.success('Registration successful!')
        router.push('/')
      }
    } catch (error) {
      console.error('Registration failed:', error)
    } finally {
      loading.value = false
    }
  }

  async function login(credentials) {
    loading.value = true
    try {
      await getCsrfCookie()
      const response = await api.post('/auth/login', credentials)
      
      if (response.data.success) {
        token.value = response.data.data.access_token
        user.value = response.data.data.user
        localStorage.setItem('auth_token', token.value)
        toast.success('Login successful!')
        router.push('/')
      }
    } catch (error) {
      console.error('Login failed:', error)
    } finally {
      loading.value = false
    }
  }

  async function loginWithGoogle() {
    window.location.href = `${import.meta.env.VITE_API_BASE_URL}/auth/google`
  }

  async function loginWithFacebook() {
    window.location.href = `${import.meta.env.VITE_API_BASE_URL}/auth/facebook`
  }

  async function handleSocialCallback(token) {
    if (token) {
      localStorage.setItem('auth_token', token)
      await checkAuth()
      toast.success('Login successful!')
      router.push('/')
    } else {
      toast.error('Login failed')
      router.push('/login')
    }
  }

  async function logout() {
    try {
      if (token.value) {
        await api.post('/auth/logout')
      }
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      user.value = null
      token.value = null
      localStorage.removeItem('auth_token')
      router.push('/login')
    }
  }

  async function updateProfile(data) {
    loading.value = true
    try {
      const response = await api.put('/auth/profile', data)
      if (response.data.success) {
        user.value = response.data.data
        toast.success('Profile updated successfully')
      }
    } catch (error) {
      console.error('Profile update failed:', error)
    } finally {
      loading.value = false
    }
  }

  return {
    user,
    token,
    loading,
    isAuthenticated,
    isAdmin,
    checkAuth,
    register,
    login,
    loginWithGoogle,
    loginWithFacebook,
    handleSocialCallback,
    logout,
    updateProfile,
  }
})
