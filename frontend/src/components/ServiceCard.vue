<template>
  <div class="service-card border rounded-lg overflow-hidden hover:shadow-lg transition">
    <img
      v-if="service.image"
      :src="service.image"
      :alt="service.name"
      class="w-full h-48 object-cover"
    />
    <div class="p-4">
      <span class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded mb-2">
        {{ service.category }}
      </span>
      <h3 class="text-lg font-semibold mb-2">{{ service.name }}</h3>
      <p class="text-gray-600 text-sm mb-4 line-clamp-2">
        {{ service.description }}
      </p>
      <router-link
        :to="{ name: 'service-detail', params: { slug: service.slug } }"
        class="btn btn-primary w-full"
      >
        Order Now
      </router-link>
    </div>
  </div>
</template>

<script setup>
defineProps({
  service: {
    type: Object,
    required: true
  }
})
</script>
