<template>
  <div class="service-detail-view">
    <NavBar />
    
    <main class="container mx-auto px-4 py-8">
      <div v-if="serviceStore.loading">Loading...</div>
      <div v-else-if="serviceStore.currentService">
        <h1 class="text-3xl font-bold mb-4">{{ serviceStore.currentService.name }}</h1>
        <p class="text-gray-600 mb-8">{{ serviceStore.currentService.description }}</p>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useServiceStore } from '@/stores/service'
import NavBar from '@/components/NavBar.vue'
import Footer from '@/components/Footer.vue'

const route = useRoute()
const serviceStore = useServiceStore()

onMounted(() => {
  serviceStore.fetchService(route.params.slug)
})
</script>
