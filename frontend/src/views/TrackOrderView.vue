<template>
  <div class="track-order-view">
    <NavBar />
    
    <main class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold mb-8">Track Your Order</h1>
      
      <div class="max-w-md mx-auto">
        <form @submit.prevent="trackOrder">
          <input
            v-model="orderNumber"
            type="text"
            placeholder="Enter order number"
            class="input mb-4"
          />
          <button type="submit" class="btn btn-primary w-full">
            Track Order
          </button>
        </form>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import NavBar from '@/components/NavBar.vue'
import Footer from '@/components/Footer.vue'

const router = useRouter()
const orderNumber = ref('')

function trackOrder() {
  if (orderNumber.value) {
    router.push({ name: 'order-detail', params: { orderNumber: orderNumber.value } })
  }
}
</script>
