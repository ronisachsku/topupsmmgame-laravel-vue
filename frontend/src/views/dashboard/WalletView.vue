<template>
  <div class="wallet-view">
    <NavBar />
    
    <main class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold mb-8">My Wallet</h1>
      
      <div class="card">
        <p class="text-2xl font-bold">Balance: Rp 0</p>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import NavBar from '@/components/NavBar.vue'
import Footer from '@/components/Footer.vue'
</script>
