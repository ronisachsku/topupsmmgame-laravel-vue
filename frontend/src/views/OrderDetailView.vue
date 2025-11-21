<template>
  <div class="order-detail-view">
    <NavBar />
    
    <main class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold mb-8">Order Details</h1>
      
      <div v-if="orderStore.loading">Loading...</div>
      <div v-else-if="orderStore.currentOrder" class="card">
        <p><strong>Order Number:</strong> {{ orderStore.currentOrder.order_number }}</p>
        <p><strong>Status:</strong> {{ orderStore.currentOrder.status_payment }}</p>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useOrderStore } from '@/stores/order'
import NavBar from '@/components/NavBar.vue'
import Footer from '@/components/Footer.vue'

const route = useRoute()
const orderStore = useOrderStore()

onMounted(() => {
  orderStore.fetchOrder(route.params.orderNumber)
})
</script>
