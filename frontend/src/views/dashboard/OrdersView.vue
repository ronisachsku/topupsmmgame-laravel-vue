<template>
  <div class="orders-view">
    <NavBar />
    
    <main class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold mb-8">My Orders</h1>
      
      <div v-if="orderStore.loading">Loading...</div>
      <div v-else class="space-y-4">
        <div v-for="order in orderStore.orders" :key="order.id" class="card">
          <p><strong>{{ order.order_number }}</strong></p>
          <p>{{ order.service.name }}</p>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useOrderStore } from '@/stores/order'
import NavBar from '@/components/NavBar.vue'
import Footer from '@/components/Footer.vue'

const orderStore = useOrderStore()

onMounted(() => {
  orderStore.fetchUserOrders()
})
</script>
