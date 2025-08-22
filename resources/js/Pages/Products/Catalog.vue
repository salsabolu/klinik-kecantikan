<template>

  <Head title="Katalog Produk" />
  <PasienLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-serif font-bold text-clinic-green">Katalog Produk</h1>
          <p class="text-gray-600 mt-1">Temukan berbagai produk kecantikan terbaik untuk Anda.
            Nikmati pengalaman perawatan yang profesional.</p>
        </div>
      </div>

      <!-- Filters -->
      <div class="card bg-white border border-clinic-green shadow-lg">
        <div class="card-body">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
              <label class="label">
                <span class="label-text">Pencarian</span>
              </label>
              <div class="relative">
                <input v-model="filters.search" type="text" placeholder="Nama produk atau deskripsi..."
                  class="input input-bordered w-full pr-10" @input="search" />
                <button v-if="filters.search" @click="clearSearch"
                  class="absolute inset-y-0 right-0 pr-3 flex items-center">
                  <PhX :size="20" color="gray" weight="regular" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Products Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div v-for="product in filteredProducts" :key="product.id"
          class="card bg-white border border-clinic-green shadow-lg hover:shadow-xl transition-shadow">
          <div class="card-body">
            <p class="card-title">{{ product.nama_produk }}</p>
            <p class="card-description">{{ product.deskripsi || 'Tidak ada deskripsi' }}</p>
            <div class="flex justify-between items-center pt-4">
              <span class="card-subtitle">{{ formatCurrency(product.harga) }}</span>
              <Link :href="route('products.show.pasien', product.id)" class="btn btn-primary btn-base">
              Detail
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredProducts.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-base-content/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
        </svg>
        <h3 class="mt-2 text-lg font-medium text-base-content">Tidak ada produk</h3>
        <p class="mt-1 text-base-content/60">Produk tidak ditemukan dengan kriteria pencarian.</p>
      </div>
    </div>
  </PasienLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import PasienLayout from '@/Layouts/PasienLayout.vue'
import { PhX } from '@phosphor-icons/vue'

// Props
const props = defineProps({
  products: Array,
})

// Reactive filters
const filters = ref({
  search: '',
})

// Filtered products based on search
const filteredProducts = computed(() => {
  if (!filters.value.search) {
    return props.products
  }

  const searchTerm = filters.value.search.toLowerCase()
  return props.products.filter(product => {
    return product.nama_produk.toLowerCase().includes(searchTerm) ||
      (product.deskripsi && product.deskripsi.toLowerCase().includes(searchTerm))
  })
})

// Clear search
const clearSearch = () => {
  filters.value.search = ''
}

// Search function (for consistency, though not needed for client-side filtering)
const search = () => {
  // This is handled by the computed filteredProducts
}

// Methods
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(amount)
}
</script>
