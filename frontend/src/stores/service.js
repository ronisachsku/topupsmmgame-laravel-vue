import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/utils/api'

export const useServiceStore = defineStore('service', () => {
  const services = ref([])
  const categories = ref([])
  const currentService = ref(null)
  const loading = ref(false)

  async function fetchServices(filters = {}) {
    loading.value = true
    try {
      const response = await api.get('/services', { params: filters })
      if (response.data.success) {
        services.value = response.data.data
      }
    } catch (error) {
      console.error('Failed to fetch services:', error)
    } finally {
      loading.value = false
    }
  }

  async function fetchCategories() {
    try {
      const response = await api.get('/services/categories')
      if (response.data.success) {
        categories.value = response.data.data
      }
    } catch (error) {
      console.error('Failed to fetch categories:', error)
    }
  }

  async function fetchService(slug) {
    loading.value = true
    try {
      const response = await api.get(`/services/${slug}`)
      if (response.data.success) {
        currentService.value = response.data.data
        return response.data.data
      }
    } catch (error) {
      console.error('Failed to fetch service:', error)
    } finally {
      loading.value = false
    }
  }

  return {
    services,
    categories,
    currentService,
    loading,
    fetchServices,
    fetchCategories,
    fetchService,
  }
})
