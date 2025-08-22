<template>
  <Head title="Detail Produk" />
  <component :is="layoutComponent">
    <div class="w-full mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-semibold">{{ product.nama_produk }}</h1>
          <p class="text-gray-600">Detail informasi produk</p>
        </div>
        <div class="flex">
          <Link :href="backUrl" class="btn btn-outline">
          <PhArrowLeft :size="20" color="black" weight="regular" />
          Kembali
          </Link>
        </div>
      </div>

      <!-- Content -->
      <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 lg:p-8">
          <!-- Product Information -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="space-y-6">
              <div>
                <p class="text-lg font-medium mb-4">
                  Informasi Layanan
                </p>

                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-600">
                      Nama Produk
                    </label>
                    <p class="mt-1 text-sm">
                      {{ product.nama_produk }}
                    </p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-600">
                      Deskripsi
                    </label>
                    <p class="mt-1 text-sm">
                      {{ product.deskripsi || 'Tidak ada deskripsi' }}
                    </p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-600">
                      Harga
                    </label>
                    <p class="mt-1 text-lg font-semibold">
                      Rp {{ formatCurrency(product.harga) }}
                    </p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-600">
                      Stok
                    </label>
                    <div class="mt-1 space-x-2">
                      <div class="badge" :class="getStockBadgeClass(product.stok)">
                        {{ product.stok }} unit
                      </div>
                      <span class="text-sm" :class="getStockTextClass(product.stok)">
                        {{ getStockStatus(product.stok) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Additional Information -->
            <div v-if="!canAccess(['pasien'])" class="space-y-6">
              <div>
                <p class="text-lg font-medium mb-4">
                  Informasi Sistem
                </p>

                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-600">
                      Dibuat Pada
                    </label>
                    <p class="mt-1 text-sm">
                      {{ formatDate(product.created_at) }}
                    </p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-600">
                      Diperbarui Pada
                    </label>
                    <p class="mt-1 text-sm">
                      {{ formatDate(product.updated_at) }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end">
            <Link v-if="canAccess(['admin', 'manajer_stok'])" :href="`/products/${product.id}/edit`"
              class="btn btn-outline btn-primary">
            <PhNotePencil :size="20" color="black" weight="regular" />
            Edit Produk
            </Link>
          </div>
        </div>
      </div>

      <!-- Additional Info -->
      <div v-if="canAccess(['admin', 'manajer_stok'])" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 lg:p-8">
          <p class="text-lg font-medium">Informasi Tambahan</p>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="stat">
              <div class="stat-title">Status Ketersediaan</div>
              <div class="stat-value text-sm" :class="getStockTextClass(product.stok)">
                {{ getStockStatus(product.stok) }}
              </div>
              <div class="stat-desc">{{ product.stok }} unit tersedia</div>
            </div>
  
            <div class="stat">
              <div class="stat-title">Harga per Unit</div>
              <div class="stat-value text-lg text-primary">Rp {{ formatCurrency(product.harga) }}</div>
              <div class="stat-desc">Harga jual saat ini</div>
            </div>
  
            <div class="stat">
              <div class="stat-title">Total Nilai Inventori</div>
              <div class="stat-value text-lg text-success">
                Rp {{ formatCurrency(product.harga * product.stok) }}
              </div>
              <div class="stat-desc">Harga × Stok</div>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Stock History for Admin and Manajer Stok -->
      <div v-if="canAccess(['admin', 'manajer_stok'])" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 lg:p-8">
          <p class="text-lg font-medium">Riwayat Perubahan Stok</p>

          <div v-if="stockHistory && stockHistory.length > 0" class="overflow-x-auto">
            <table class="table table-zebra">
              <thead>
                <tr>
                  <th>Tanggal & Waktu</th>
                  <th>Stok Sebelum</th>
                  <th>Stok Sesudah</th>
                  <th>Perubahan</th>
                  <th>Diubah Oleh</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="history in stockHistory" :key="history.id">
                  <td>
                    <div class="font-mono text-sm">
                      {{ formatDateTime(history.created_at) }}
                    </div>
                  </td>
                  <td>
                    <div class="badge badge-outline">
                      {{ history.old_stock }} unit
                    </div>
                  </td>
                  <td>
                    <div class="badge badge-outline">
                      {{ history.new_stock }} unit
                    </div>
                  </td>
                  <td>
                    <div class="flex items-center gap-1">
                      <span v-if="history.new_stock > history.old_stock" class="text-success">
                        +{{ history.new_stock - history.old_stock }}
                      </span>
                      <span v-else-if="history.new_stock < history.old_stock" class="text-error">
                        {{ history.new_stock - history.old_stock }}
                      </span>
                      <span v-else class="text-base-content/60">0</span>
                    </div>
                  </td>
                  <td>
                    <div class="text-sm">
                      {{ history.user?.name || 'Sistem' }}
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
  
          <div v-else class="text-center py-4">
            <div class="flex flex-col justify-center items-center text-center">
              <PhChartLine :size="80" color="black" weight="regular" />
              <p class="text-lg font-medium text-gray-600">Belum ada riwayat perubahan stok</p>
              <p class="text-base text-gray-600">Riwayat akan muncul setelah ada perubahan stok produk.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

  </component>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PasienLayout from '@/Layouts/PasienLayout.vue'
import { PhArrowLeft, PhNotePencil, PhChartLine } from '@phosphor-icons/vue'

const props = defineProps({
  product: Object,
  stockHistory: Array,
  auth: Object,
})

// Get user from page props
const { props: pageProps } = usePage()
const user = computed(() => pageProps.auth?.user)

// Choose layout and back URL based on user role
const layoutComponent = computed(() => {
  return user.value?.role === 'pasien' ? PasienLayout : AppLayout
})

const backUrl = computed(() => {
  return user.value?.role === 'pasien' ? '/products-catalog' : '/products'
})

// Stock adjustment form
const { data: stockForm, put: putStock, processing: stockProcessing, errors: stockFormErrors } = useForm({
  new_stock: '',
})

const stockErrors = ref({})
const lastStockChange = ref({
  oldStock: 0,
  newStock: 0,
  difference: 0
})

// Computed properties for stock adjustment
const stockDifference = computed(() => {
  if (stockForm.new_stock === '') return 0
  return parseInt(stockForm.new_stock) - props.product.stok
})

// Utility functions
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID').format(amount)
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatDateTime = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const canAccess = (roles) => {
  if (!props.auth?.user?.role) return false
  return roles.includes(props.auth.user.role)
}

const getStockBadgeClass = (stock) => {
  if (stock === 0) return 'badge-error'
  if (stock <= 10) return 'badge-warning'
  return 'badge-success'
}

const getStockTextClass = (stock) => {
  if (stock === 0) return 'text-error'
  if (stock <= 10) return 'text-warning'
  return 'text-success'
}

const getStockStatus = (stock) => {
  if (stock === 0) return 'Habis'
  if (stock <= 10) return 'Stok Rendah'
  return 'Tersedia'
}
</script>
