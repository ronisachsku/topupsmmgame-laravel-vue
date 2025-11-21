<template>
  <div class="service-list-view">
    <NavBar />
    
    <main class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold mb-8">All Services</h1>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <ServiceCard
          v-for="service in serviceStore.services"
          :key="service.id"
          :service="service"
        />
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useServiceStore } from '@/stores/service'
import NavBar from '@/components/NavBar.vue'
import Footer from '@/components/Footer.vue'
import ServiceCard from '@/components/ServiceCard.vue'

const serviceStore = useServiceStore()

onMounted(() => {
  serviceStore.fetchServices()
})
</script>
