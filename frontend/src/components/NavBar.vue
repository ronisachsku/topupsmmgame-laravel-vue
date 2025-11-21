<template>
  <nav class="navbar bg-white shadow-md">
    <div class="container mx-auto px-4 py-4 flex items-center justify-between">
      <router-link to="/" class="text-2xl font-bold text-blue-600">
        {{ appName }}
      </router-link>

      <div class="nav-links hidden md:flex space-x-6">
        <router-link to="/" class="nav-link">Home</router-link>
        <router-link to="/services" class="nav-link">Services</router-link>
        <router-link to="/order/track" class="nav-link">Track Order</router-link>
      </div>

      <div class="nav-actions flex items-center space-x-4">
        <template v-if="authStore.isAuthenticated">
          <router-link to="/dashboard" class="nav-link">
            Dashboard
          </router-link>
          <button @click="authStore.logout" class="btn btn-outline">
            Logout
          </button>
        </template>
        <template v-else>
          <router-link to="/login" class="btn btn-outline">
            Login
          </router-link>
          <router-link to="/register" class="btn btn-primary">
            Register
          </router-link>
        </template>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const appName = import.meta.env.VITE_APP_NAME || 'TopUp SMM Game'
</script>

<style scoped>
.nav-link {
  @apply text-gray-700 hover:text-blue-600 transition;
}
</style>
