<template>
  <div class="home-view">
    <NavBar />
    
    <main class="container mx-auto px-4 py-8">
      <!-- Hero Section -->
      <section class="hero mb-12 text-center">
        <h1 class="text-4xl font-bold mb-4">TopUp SMM Game</h1>
        <p class="text-xl text-gray-600 mb-8">
          Platform top-up game dan layanan social media marketing terpercaya
        </p>
        <router-link
          to="/services"
          class="btn btn-primary"
        >
          Lihat Layanan
        </router-link>
      </section>

      <!-- Categories -->
      <section class="categories mb-12">
        <h2 class="text-2xl font-bold mb-6">Kategori Layanan</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <button
            v-for="category in categories"
            :key="category"
            @click="filterByCategory(category)"
            class="category-card p-6 border rounded-lg hover:shadow-lg transition"
          >
            <h3 class="font-semibold">{{ category }}</h3>
          </button>
        </div>
      </section>

      <!-- Featured Services -->
      <section class="services">
        <h2 class="text-2xl font-bold mb-6">Layanan Populer</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <ServiceCard
            v-for="service in services.slice(0, 6)"
            :key="service.id"
            :service="service"
          />
        </div>
      </section>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useServiceStore } from '@/stores/service'
import NavBar from '@/components/NavBar.vue'
import Footer from '@/components/Footer.vue'
import ServiceCard from '@/components/ServiceCard.vue'

const router = useRouter()
const serviceStore = useServiceStore()

const { services, categories } = serviceStore

onMounted(async () => {
  await serviceStore.fetchServices()
  await serviceStore.fetchCategories()
})

function filterByCategory(category) {
  router.push({ name: 'services', query: { category } })
}
</script>
