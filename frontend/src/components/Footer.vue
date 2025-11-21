<template>
  <footer class="bg-gray-800 text-white py-8 mt-12">
    <div class="container mx-auto px-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <h3 class="text-xl font-bold mb-4">{{ appName }}</h3>
          <p class="text-gray-400">
            Platform top-up game dan layanan social media marketing terpercaya
          </p>
        </div>
        <div>
          <h4 class="font-semibold mb-4">Quick Links</h4>
          <ul class="space-y-2">
            <li><router-link to="/services" class="text-gray-400 hover:text-white">Services</router-link></li>
            <li><router-link to="/order/track" class="text-gray-400 hover:text-white">Track Order</router-link></li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold mb-4">Contact</h4>
          <p class="text-gray-400">Email: support@topupsmmgame.com</p>
          <p class="text-gray-400">WhatsApp: +62 xxx-xxxx-xxxx</p>
        </div>
      </div>
      <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
        <p>&copy; 2025 {{ appName }}. All rights reserved.</p>
      </div>
    </div>
  </footer>
</template>

<script setup>
const appName = import.meta.env.VITE_APP_NAME || 'TopUp SMM Game'
</script>
