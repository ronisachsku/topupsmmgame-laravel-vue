import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'
import DOMPurify from 'dompurify'

const toast = useToast()

// Create axios instance with secure defaults
const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
  timeout: 30000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
  withCredentials: true, // Important for Sanctum
})

// Request interceptor
api.interceptors.request.use(
  (config) => {
    // Get token from localStorage
    const token = localStorage.getItem('auth_token')
    
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }

    // Sanitize request data to prevent XSS
    if (config.data && typeof config.data === 'object') {
      config.data = sanitizeObject(config.data)
    }

    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor
api.interceptors.response.use(
  (response) => {
    // Sanitize response data
    if (response.data && typeof response.data === 'object') {
      response.data = sanitizeObject(response.data)
    }
    return response
  },
  (error) => {
    const authStore = useAuthStore()

    if (error.response) {
      // Handle different error status codes
      switch (error.response.status) {
        case 401:
          // Unauthorized - clear auth and redirect to login
          authStore.logout()
          toast.error('Session expired. Please login again.')
          break
        case 403:
          toast.error('You do not have permission to perform this action.')
          break
        case 404:
          toast.error('Resource not found.')
          break
        case 422:
          // Validation errors
          if (error.response.data.errors) {
            const errors = Object.values(error.response.data.errors).flat()
            errors.forEach(err => toast.error(err))
          } else {
            toast.error(error.response.data.message || 'Validation failed')
          }
          break
        case 429:
          toast.error('Too many requests. Please try again later.')
          break
        case 500:
        case 502:
        case 503:
        case 504:
          toast.error('Server error. Please try again later.')
          break
        default:
          toast.error(error.response.data.message || 'An error occurred')
      }
    } else if (error.request) {
      toast.error('Network error. Please check your connection.')
    } else {
      toast.error('An unexpected error occurred.')
    }

    return Promise.reject(error)
  }
)

// Sanitize function to prevent XSS
function sanitizeObject(obj) {
  if (typeof obj !== 'object' || obj === null) {
    return typeof obj === 'string' ? DOMPurify.sanitize(obj) : obj
  }

  if (Array.isArray(obj)) {
    return obj.map(item => sanitizeObject(item))
  }

  const sanitized = {}
  for (const [key, value] of Object.entries(obj)) {
    sanitized[key] = sanitizeObject(value)
  }
  return sanitized
}

// CSRF token management
export async function getCsrfCookie() {
  await axios.get(`${import.meta.env.VITE_API_BASE_URL.replace('/api', '')}/sanctum/csrf-cookie`)
}

export default api
