<template>
  <AppLayout>

    <Head title="Laporan Stok" />
    <div class="space-y-6">
      <!-- Header -->
      <div>
        <h1 class="text-3xl font-semibold">Laporan Stok Produk</h1>
        <p class="text-gray-600">Monitoring stok dan pergerakan produk</p>
      </div>

      <!-- Filter and Stats -->
      <div class="card bg-white shadow-xl">
        <div class="card-body space-y-6">
          <!-- Filter Section -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold">Periode</span>
              </label>
              <select v-model="selectedPeriod" @change="handlePeriodChange" class="select select-bordered">
                <option value="weekly">Mingguan</option>
                <option value="monthly">Bulanan</option>
                <option value="yearly">Tahunan</option>
                <option value="custom">Kustom</option>
              </select>
            </div>

            <div class="form-control" v-if="selectedPeriod === 'custom'">
              <label class="label">
                <span class="label-text font-semibold">Dari Tanggal</span>
              </label>
              <input type="date" v-model="startDate" @change="handleDateChange" class="input input-bordered"
                autocomplete="off" />
            </div>

            <div class="form-control" v-if="selectedPeriod === 'custom'">
              <label class="label">
                <span class="label-text font-semibold">Sampai Tanggal</span>
              </label>
              <input type="date" v-model="endDate" @change="handleDateChange" class="input input-bordered"
                autocomplete="off" />
            </div>

            <ResetFilterButton @reset="resetFilter" />
          </div>

          <!-- Statistics Cards -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Stock Value -->
            <div class="stat bg-gradient-to-br from-clinic-green to-green-800 text-white rounded-lg shadow-lg">
              <div class="stat-figure">
                <PhCube :size="40" color="white" weight="regular" />
              </div>
              <div class="stat-title text-green-100">Nilai Total Stok</div>
              <div class="stat-value text-2xl">{{ formatCurrency(stockValue.total_value || 0) }}</div>
            </div>

            <!-- Low Stock Alert -->
            <div class="stat bg-gradient-to-br from-red-500 to-red-600 text-white rounded-lg shadow-lg">
              <div class="stat-figure">
                <PhWarning :size="40" color="white" weight="regular" />
              </div>
              <div class="stat-title text-red-100">Stok Menipis</div>
              <div class="stat-value text-2xl">{{ lowStockProducts.length }}</div>
            </div>

            <!-- Products Sold -->
            <div class="stat bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow-lg">
              <div class="stat-figure">
                <PhCube :size="40" color="white" weight="regular" />
              </div>
              <div class="stat-title text-red-100">Produk Terjual</div>
              <div class="stat-value text-2xl">{{ productMovement.length }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Low Stock Alert Section -->
      <div class="card bg-white shadow-xl" v-if="lowStockProducts.length > 0">
        <div class="card-body space-y-6">
          <!-- Title -->
          <div class="flex items-start gap-2 text-error">
            <PhWarning :size="30" color="currentColor" weight="regular" />
            <p class="text-lg font-semibold">
              Peringatan Stok Menipis
            </p>
          </div>

          <!-- Alert -->
          <div class="alert alert-soft alert-error">
            <div>
              <p class="font-bold">Perhatian!</p>
              <div class="text-xs">{{ lowStockProducts.length }} produk memiliki stok di bawah {{ threshold }}
                unit. Segera lakukan restocking.</div>
            </div>
          </div>

          <!-- Table -->
          <div class="overflow-x-auto">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Produk</th>
                  <th>Stok Tersisa</th>
                  <th>Harga</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(product, index) in paginatedLowStock" :key="product.id" class="hover">
                  <td>
                    <span class="text-sm">{{ (currentLowStockPage - 1) * lowStockPerPage + index + 1
                    }}</span>
                  </td>
                  <td>
                    <div class="font-semibold">{{ product.nama_produk }}</div>
                    <div class="text-sm text-gray-600 truncate">{{ product.deskripsi }}</div>
                  </td>
                  <td>
                    <span class="badge badge-error badge-sm">{{ product.stok }} unit</span>
                  </td>
                  <td>
                    <span class="font-semibold">{{ formatCurrency(product.harga) }}</span>
                  </td>
                  <td>
                    <span class="badge badge-outline badge-error badge-sm" v-if="product.stok === 0">
                      Habis
                    </span>
                    <span class="badge badge-outline badge-warning badge-sm" v-else-if="product.stok < 5">
                      Kritis
                    </span>
                    <span class="badge badge-outline badge-warning badge-sm" v-else>
                      Rendah
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Pagination for Low Stock -->
            <div class="flex justify-center mt-4" v-if="lowStockPaginationPages.length > 1">
              <div class="join">
                <button v-for="page in lowStockPaginationPages" :key="page" @click="currentLowStockPage = page"
                  class="join-item btn btn-sm" :class="{ 'btn-active': page === currentLowStockPage }">
                  {{ page }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State for Low Stock Alert Section -->
      <div class="card bg-white shadow-xl py-12" v-if="lowStockProducts.length === 0">
        <div class="card-body space-y-6">
          <div class="flex flex-col justify-center items-center text-center">
            <PhCube :size="80" color="black" weight="regular" />
            <p class="text-lg font-medium text-gray-600">Stok Aman</p>
            <p class="text-base text-gray-600 mb-4">Semua produk memiliki stok yang cukup (di atas {{ threshold }}
              unit).</p>
          </div>
        </div>
      </div>

      <!-- Product Movement Report -->
      <div class="card bg-white shadow-xl">
        <div class="card-body space-y-6">
          <!-- Title -->
          <div class="flex items-start gap-2">
            <PhCube :size="30" color="currentColor" weight="regular" />
            <p class="text-lg font-semibold">
              Pergerakan Produk Bulan Ini
            </p>
          </div>

          <!-- Filter Section -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="form-control">
              <label for="movement-start-date" class="label">
                <span class="label-text font-semibold">Dari Tanggal</span>
              </label>
              <input id="movement-start-date" v-model="movementStartDate" @change="handleMovementDateChange" type="date"
                class="input input-bordered" />
            </div>

            <div class="form-control">
              <label for="movement-end-date" class="label">
                <span class="label-text font-semibold">Sampai Tanggal</span>
              </label>
              <input id="movement-end-date" v-model="movementEndDate" @change="handleMovementDateChange" type="date"
                class="input input-bordered" />
            </div>

            <ResetFilterButton text="Reset" @reset="resetMovementFilter" />
          </div>

          <!-- Table -->
          <div class="overflow-x-auto">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Produk</th>
                  <th>Stok Saat Ini</th>
                  <th>Total Terjual</th>
                  <th>Total Pendapatan</th>
                  <th>Rata-rata per Transaksi</th>
                  <th>Status Stok</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(product, index) in paginatedMovement" :key="product.nama_produk" class="hover">
                  <th>
                    <span class="text-sm font-normal">{{ (currentMovementPage - 1) * movementPerPage + index + 1
                      }}</span>
                  </th>
                  <td>
                    <div class="font-semibold">{{ product.nama_produk }}</div>
                  </td>
                  <td>
                    <span class="badge badge-sm" :class="getStockBadgeClass(product.stok)">
                      {{ product.stok }} unit
                    </span>
                  </td>
                  <td>
                    <span class="font-semibold text-blue-600">{{ product.total_sold }} unit</span>
                  </td>
                  <td>
                    <span class="font-semibold text-green-600">{{ formatCurrency(product.total_revenue || 0)
                    }}</span>
                  </td>
                  <td>
                    <span class="text-gray-600">{{ parseFloat(product.avg_sold_per_transaction).toFixed(1) }}
                      unit</span>
                  </td>
                  <td>
                    <span class="badge badge-outline badge-sm" :class="getStockStatusClass(product.stok)">
                      {{ getStockStatusText(product.stok) }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination for Product Movement -->
          <div v-if="filteredProductMovement.length > movementPerPage" class="flex justify-center mt-4">
            <div class="join">
              <button v-for="page in movementPaginationPages" :key="page" @click="currentMovementPage = page"
                class="join-item btn btn-sm" :class="{ 'btn-active': page === currentMovementPage }">
                {{ page }}
              </button>
            </div>
          </div>

          <!-- Empty State for Product Movement -->
          <div v-if="productMovement.length === 0" class="text-center py-12">
            <div class="flex flex-col justify-center items-center text-center">
              <PhCube :size="80" color="black" weight="regular" />
              <p class="text-lg font-medium text-gray-600">Belum Ada Pergerakan Produk</p>
              <p class="text-base text-gray-600 mb-4">Belum ada penjualan produk bulan ini.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { Head, router, Link } from '@inertiajs/vue3'
import AppLayout from '~/Layouts/AppLayout.vue'
import ResetFilterButton from '~/Components/ResetFilterButton.vue'
import { PhCube, PhWarning } from '@phosphor-icons/vue'

// Props
const props = defineProps({
  lowStockProducts: {
    type: Array,
    default: () => []
  },
  productMovement: {
    type: Array,
    default: () => []
  },
  stockValue: {
    type: Object,
    default: () => ({ total_value: 0 })
  },
  filters: {
    type: Object,
    default: () => ({})
  }
})

// Reactive data
const selectedPeriod = ref('monthly')
const startDate = ref(props.filters?.start_date || '')
const endDate = ref(props.filters?.end_date || '')
const threshold = ref(10) // Fixed threshold, tidak perlu filter
const movementStartDate = ref(props.filters?.movement_start_date || '')
const movementEndDate = ref(props.filters?.movement_end_date || '')
const currentLowStockPage = ref(1)
const currentMovementPage = ref(1)
const lowStockPerPage = 10
const movementPerPage = 10

// Initialize dates to current month if not provided
const initializeDates = () => {
  const now = new Date()
  const firstDay = new Date(now.getFullYear(), now.getMonth(), 1)
  const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0)

  if (!startDate.value) startDate.value = firstDay.toISOString().split('T')[0]
  if (!endDate.value) endDate.value = lastDay.toISOString().split('T')[0]
  if (!movementStartDate.value) movementStartDate.value = firstDay.toISOString().split('T')[0]
  if (!movementEndDate.value) movementEndDate.value = lastDay.toISOString().split('T')[0]
}

// Define setPeriodDates function first
const setPeriodDates = () => {
  const now = new Date()

  switch (selectedPeriod.value) {
    case 'weekly':
      const startOfWeek = new Date(now.setDate(now.getDate() - now.getDay()))
      const endOfWeek = new Date(now.setDate(now.getDate() - now.getDay() + 6))
      startDate.value = startOfWeek.toISOString().split('T')[0]
      endDate.value = endOfWeek.toISOString().split('T')[0]
      break
    case 'monthly':
      const firstDay = new Date(now.getFullYear(), now.getMonth(), 1)
      const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0)
      startDate.value = firstDay.toISOString().split('T')[0]
      endDate.value = lastDay.toISOString().split('T')[0]
      break
    case 'yearly':
      const firstDayYear = new Date(now.getFullYear(), 0, 1)
      const lastDayYear = new Date(now.getFullYear(), 11, 31)
      startDate.value = firstDayYear.toISOString().split('T')[0]
      endDate.value = lastDayYear.toISOString().split('T')[0]
      break
    case 'custom':
      // Tidak mengubah tanggal untuk custom
      break
  }
}

// Initialize dates
initializeDates()
setPeriodDates() // Set dates based on default period

// Computed properties for filtering and pagination
const filteredLowStockProducts = computed(() => {
  return props.lowStockProducts || []
})

const filteredProductMovement = computed(() => {
  return props.productMovement || []
})

const paginatedLowStock = computed(() => {
  const start = (currentLowStockPage.value - 1) * lowStockPerPage
  const end = start + lowStockPerPage
  return filteredLowStockProducts.value.slice(start, end)
})

const paginatedMovement = computed(() => {
  const start = (currentMovementPage.value - 1) * movementPerPage
  const end = start + movementPerPage
  return filteredProductMovement.value.slice(start, end)
})

const lowStockPaginationPages = computed(() => {
  const totalPages = Math.ceil(filteredLowStockProducts.value.length / lowStockPerPage)
  const pages = []
  for (let i = 1; i <= totalPages; i++) {
    pages.push(i)
  }
  return pages
})

const movementPaginationPages = computed(() => {
  const totalPages = Math.ceil(filteredProductMovement.value.length / movementPerPage)
  const pages = []
  for (let i = 1; i <= totalPages; i++) {
    pages.push(i)
  }
  return pages
})

// Watch for auto-filtering (tanpa tombol filter)
watch([startDate, endDate], () => {
  if (startDate.value && endDate.value) {
    autoFilterData()
  }
}, { immediate: false })

watch([movementStartDate, movementEndDate], () => {
  if (movementStartDate.value && movementEndDate.value) {
    autoFilterMovementData()
  }
}, { immediate: false })

// Methods
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(amount)
}

const handlePeriodChange = () => {
  setPeriodDates()
  if (startDate.value && endDate.value) {
    autoFilterData()
  }
}

const handleDateChange = () => {
  if (startDate.value && endDate.value) {
    autoFilterData()
  }
}

const handleMovementDateChange = () => {
  if (movementStartDate.value && movementEndDate.value) {
    autoFilterMovementData()
  }
}

const autoFilterData = () => {
  router.get(route('reports.stock'), {
    start_date: startDate.value,
    end_date: endDate.value,
    threshold: threshold.value
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  })
}

const autoFilterMovementData = () => {
  router.get(route('reports.stock'), {
    movement_start_date: movementStartDate.value,
    movement_end_date: movementEndDate.value,
    threshold: threshold.value
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  })
}

const getStockBadgeClass = (stock) => {
  if (stock === 0) return 'badge-error'
  if (stock < 5) return 'badge-warning'
  if (stock < 10) return 'badge-warning'
  return 'badge-success'
}

const getStockStatusClass = (stock) => {
  if (stock === 0) return 'badge-error'
  if (stock < 5) return 'badge-warning'
  if (stock < 10) return 'badge-warning'
  return 'badge-success'
}

const getStockStatusText = (stock) => {
  if (stock === 0) return 'Habis'
  if (stock < 5) return 'Kritis'
  if (stock < 10) return 'Rendah'
  return 'Aman'
}

const resetFilter = () => {
  selectedPeriod.value = 'monthly'
  setPeriodDates()
  autoFilterData()
}

const resetMovementFilter = () => {
  const now = new Date()
  const firstDay = new Date(now.getFullYear(), now.getMonth(), 1)
  const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0)

  movementStartDate.value = firstDay.toISOString().split('T')[0]
  movementEndDate.value = lastDay.toISOString().split('T')[0]
  autoFilterMovementData()
}
</script>
