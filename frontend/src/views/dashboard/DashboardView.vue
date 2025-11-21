<template>
  <div class="dashboard-view">
    <NavBar />
    
    <main class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold mb-8">Dashboard</h1>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <router-link to="/dashboard/orders" class="card hover:shadow-lg transition">
          <h3 class="font-semibold">My Orders</h3>
        </router-link>
        <router-link to="/dashboard/wallet" class="card hover:shadow-lg transition">
          <h3 class="font-semibold">Wallet</h3>
        </router-link>
        <router-link to="/dashboard/profile" class="card hover:shadow-lg transition">
          <h3 class="font-semibold">Profile</h3>
        </router-link>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import NavBar from '@/components/NavBar.vue'
import Footer from '@/components/Footer.vue'
</script>
