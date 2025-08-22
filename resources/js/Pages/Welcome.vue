<template>
  <Head title="Dashboard" />
  <!-- Navigation Bar -->
  <div class="navbar fixed z-50 bg-primary-bg border-b border-clinic-green px-6 lg:px-12 py-2 lg:py-4">
    <div class="navbar-start">
      <div class="flex items-center space-x-1 md:space-x-6">
        <!-- Logo -->
        <a href="#">
          <div class="flex flex-row md:flex-none items-center gap-3">
            <img src="/Logo-Klinik.svg" alt="Esthetician Clinique" class="h-10 w-10" />
            <!-- <h1 class="text-2xl font-serif font-normal text-clinic-green block md:hidden">Esthetician Clinique</h1> -->
          </div>
        </a>

        <!-- Navigation Links -->
        <div class="flex space-x-3 md:space-x-6 ml-8">
          <a href="#services" class="text-primary transition-colors hidden md:block">Katalog Layanan</a>
          <a href="#products" class="text-primary transition-colors hidden md:block">Katalog Produk</a>
          <a href="#services" class="text-primary transition-colors block md:hidden text-sm">Layanan</a>
          <a href="#products" class="text-primary transition-colors block md:hidden text-sm">Produk</a>
        </div>
      </div>
    </div>

    <div class="navbar-end">
      <Link v-if="canLogin" href="/login" class="btn btn-outline btn-primary btn-sm md:btn-md hidden md:block">
      Masuk
      </Link>
    </div>
  </div>

  <!-- Hero Section -->
  <div id="#" class="hero h-screen flex justify-center">
    <div class="flex flex-row items-center px-12 space-x-4 md:space-x-8">
      <div class="w-full h-fit text-left">
        <h1 class="text-5xl md:text-7xl text-clinic-green font-normal pb-4">Wujudkan
          Kecantikan Impian Anda</h1>
        <p class="text-base md:text-lg pb-8 leading-relaxed">
          Dengan teknologi terdepan dan tim dokter berpengalaman, kami siap memberikan perawatan terbaik untuk
          kecantikan dan kesehatan kulit Anda.
        </p>
        <div class="flex">
          <Link v-if="canLogin" href="/login" class="btn btn-primary btn-sm md:btn-md">Reservasi Sekarang
          <PhArrowRight :size="24" color="#1A3300" weight="regular" />
          </Link>
        </div>
      </div>
      <img src="/images/1.jpg" alt="Esthetician Clinique"
        class="object-cover shadow-lg w-full h-80 rounded-lg hidden md:block">
    </div>
  </div>

  <!-- Services Section -->
  <section id="services" class="min-h-screen px-10 pt-20">
    <div class="container mx-auto px-8 md:px-0">
      <div class="text-center pb-4">
        <h2 class="text-clinic-green text-4xl md:text-5xl font-normal pb-2">Layanan Kami</h2>
        <p class="text-primary hover:no-underline max-w-2xl mx-auto">
          Temukan berbagai layanan kecantikan dan kesehatan terbaik untuk Anda
        </p>
      </div>

      <div class="flex flex-col space-y-2">
        <!-- Button Lihat Semua -->
        <div class="w-full flex justify-center md:justify-end">
          <Link v-if="canLogin" href="/login" class="btn btn-outline btn-sm">
          Lihat Semua
          <PhArrowRight :size="20" color="#1A3300" weight="regular" />
          </Link>
        </div>

        <!-- Card Layanan -->
        <div v-if="services && services.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div v-for="service in services.slice(0, 6)" :key="service.id"
            class="card border border-clinic-green shadow-lg hover:shadow-xl transition-shadow">
            <figure class="px-6 pt-6">
              <div class="w-full h-52 rounded-xl flex items-center justify-center">
                <img src="/images/1.jpg" alt="Esthetician Clinique" class="object-cover w-full h-full rounded-lg">
              </div>
            </figure>
            <div class="card-body">
              <p class="card-title">{{ service.nama_layanan }}</p>
              <p class="card-description">{{ service.deskripsi ? service.deskripsi.substring(0, 100) + '...' :
                'Deskripsi tidak tersedia' }}</p>
              <div class="flex justify-between items-center pt-1">
                <span class="card-subtitle">{{ formatCurrency(service.harga) }}</span>
                <span class="badge badge-primary">{{ service.durasi_menit }} menit</span>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-12">
          <p class="text-gray-500">Layanan akan segera tersedia</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Products Section -->
  <section id="products" class="min-h-screen px-10 pt-20">
    <div class="container mx-auto px-8 md:px-0">
      <div class="text-center pb-4">
        <h2 class="text-clinic-green text-4xl md:text-5xl font-normal pb-2">Produk Kami</h2>
        <p class="text-primary hover:no-underline max-w-2xl mx-auto">
          Koleksi produk kecantikan berkualitas tinggi untuk perawatan di rumah
        </p>
      </div>

      <div class="flex flex-col space-y-2">
        <!-- Button Lihat Semua -->
        <div class="w-full flex justify-center md:justify-end">
          <Link v-if="canLogin" href="/login" class="btn btn-outline btn-sm">
          Lihat Semua
          <PhArrowRight :size="20" color="#1A3300" weight="regular" />
          </Link>
        </div>

        <!-- Card Produk -->
        <div v-if="products && products.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div v-for="product in products.slice(0, 8)" :key="product.id"
            class="card border border-clinic-green shadow-lg hover:shadow-xl transition-shadow">
            <figure class="px-6 pt-6">
              <div class="w-full h-52 rounded-xl flex items-center justify-center">
                <img src="/images/2.jpg" alt="Esthetician Clinique"
                  class="object-cover w-full h-full rounded-lg lazy-load">
              </div>
            </figure>
            <div class="card-body">
              <p class="card-title">{{ product.nama_produk }}</p>
              <p class="card-description">{{ product.deskripsi ? product.deskripsi.substring(0, 60) + '...' :
                'Deskripsi tidak tersedia' }}</p>
              <div class="flex justify-between items-center pt-1">
                <span class="card-subtitle">{{ formatCurrency(product.harga) }}</span>
                <span class="badge badge-primary" :class="getStockBadgeClass(product.stok)">
                  Stok: {{ product.stok }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-12">
          <p class="text-gray-500">Produk akan segera tersedia</p>
        </div>
      </div>
    </div>

  </section>

  <!-- Footer -->
  <footer class="footer bg-clinic-yellow p-12 mt-12">
    <div>
      <h3 class="card-title">Informasi Kontak</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="">
          <p class="font-medium">Telepon</p>
          <p>+62 21 1234 5678</p>
        </div>
        <div>
          <p class="font-medium">Email</p>
          <p>info@klinik.com</p>
        </div>
        <div>
          <p class="font-medium">Alamat</p>
          <p>Jl. Sudirman No. 123, Jakarta</p>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { PhArrowRight } from "@phosphor-icons/vue"

defineProps({
  canLogin: Boolean,
  services: Array,
  products: Array,
})

// Utility functions
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(amount)
}

const getStockBadgeClass = (stock) => {
  if (stock === 0) return 'badge-error'
  if (stock <= 10) return 'badge-warning'
  return 'badge-success'
}
</script>
