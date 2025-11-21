import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/utils/api'
import { useToast } from 'vue-toastification'

const toast = useToast()

export const useOrderStore = defineStore('order', () => {
  const orders = ref([])
  const currentOrder = ref(null)
  const loading = ref(false)

  async function createOrder(orderData) {
    loading.value = true
    try {
      const response = await api.post('/orders', orderData)
      if (response.data.success) {
        currentOrder.value = response.data.data
        toast.success('Order created successfully!')
        return response.data.data
      }
    } catch (error) {
      console.error('Failed to create order:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  async function fetchOrder(orderNumber) {
    loading.value = true
    try {
      const response = await api.get(`/orders/${orderNumber}`)
      if (response.data.success) {
        currentOrder.value = response.data.data
        return response.data.data
      }
    } catch (error) {
      console.error('Failed to fetch order:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  async function fetchUserOrders() {
    loading.value = true
    try {
      const response = await api.get('/user/orders')
      if (response.data.success) {
        orders.value = response.data.data.data
        return response.data.data
      }
    } catch (error) {
      console.error('Failed to fetch orders:', error)
    } finally {
      loading.value = false
    }
  }

  async function processPayment(orderNumber) {
    loading.value = true
    try {
      const response = await api.post('/payments/process', { order_number: orderNumber })
      if (response.data.success) {
        return response.data.data
      }
    } catch (error) {
      console.error('Failed to process payment:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  return {
    orders,
    currentOrder,
    loading,
    createOrder,
    fetchOrder,
    fetchUserOrders,
    processPayment,
  }
})
