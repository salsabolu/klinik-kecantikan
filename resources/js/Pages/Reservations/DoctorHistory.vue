<template>
  <AppLayout>
    <Head title="Riwayat Reservasi" />
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <div>
                <h2 class="text-2xl font-semibold text-gray-900">Riwayat Reservasi</h2>
                <p class="text-gray-600 mt-1">Lihat riwayat pasien dan reservasi Anda</p>
              </div>
              <div class="flex space-x-2">
                <Link :href="route('doctor.schedule')" class="btn btn-primary">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Jadwal Hari Ini
                </Link>
              </div>
            </div>

            <!-- Filters -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
              <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Cari Pasien
                  </label>
                  <input 
                    v-model="filters.patient_search"
                    type="text" 
                    placeholder="Nama atau nomor telepon..." 
                    class="input input-bordered w-full"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal Mulai
                  </label>
                  <input 
                    v-model="filters.start_date"
                    type="date" 
                    class="input input-bordered w-full"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal Selesai
                  </label>
                  <input 
                    v-model="filters.end_date"
                    type="date" 
                    class="input input-bordered w-full"
                  />
                </div>

                <FilterButtons @reset="clearFilters" />
              </form>
            </div>

            <!-- Results Summary -->
            <div v-if="reservations.data.length > 0" class="flex justify-between items-center mb-4">
              <p class="text-sm text-gray-600">
                Menampilkan {{ reservations.from }}-{{ reservations.to }} dari {{ reservations.total }} reservasi
              </p>
              <div class="text-sm text-gray-600">
                Total: {{ reservations.total }} | Selesai: {{ getCompletedCount() }}
              </div>
            </div>

            <!-- Reservations Table -->
            <div v-if="reservations.data.length > 0" class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr>
                    <th>Tanggal & Waktu</th>
                    <th>Pasien</th>
                    <th>Layanan</th>
                    <th>Status</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr 
                    v-for="reservation in reservations.data" 
                    :key="reservation.id"
                    class="cursor-pointer hover:bg-gray-50 transition-colors"
                    @click="navigateToReservation(reservation.id)"
                  >
                    <td>
                      <div class="text-sm">
                        <div class="font-medium">{{ formatDate(reservation.tanggal_reservasi) }}</div>
                        <div class="text-gray-500">
                          {{ formatTime(reservation.waktu_mulai) }} - {{ formatTime(reservation.waktu_selesai) }}
                        </div>
                      </div>
                    </td>
                    <td>
                      <div>
                        <div class="font-medium">{{ reservation.patient.nama_lengkap }}</div>
                        <div class="text-sm text-gray-500">{{ reservation.patient.nomor_telepon }}</div>
                      </div>
                    </td>
                    <td>
                      <div class="text-sm">
                        <div class="font-medium">{{ reservation.service.nama_layanan }}</div>
                        <div class="text-gray-500">{{ formatCurrency(reservation.service.harga) }}</div>
                      </div>
                    </td>
                    <td>
                      <div class="badge" :class="getStatusClass(reservation.status)">
                        {{ getStatusText(reservation.status) }}
                      </div>
                    </td>
                    <td>
                      <div class="text-sm text-gray-600 max-w-xs truncate">
                        {{ reservation.catatan || '-' }}
                      </div>
                    </td>
                    <td>
                      <div class="flex space-x-2">
                        <Link :href="route('reservations.show.doctor', reservation.id)" 
                              class="btn btn-sm btn-ghost" title="Lihat Detail"
                              @click.stop>
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                          </svg>
                        </Link>
                        
                        <Link v-if="reservation.status === 'completed'" 
                              :href="route('medical-records.create', { reservation_id: reservation.id })" 
                              class="btn btn-sm btn-ghost" title="Buat Rekam Medis"
                              @click.stop>
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                          </svg>
                        </Link>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada riwayat reservasi</h3>
              <p class="mt-1 text-sm text-gray-500">
                {{ hasFilters ? 'Tidak ada reservasi yang sesuai dengan filter yang dipilih.' : 'Anda belum memiliki riwayat reservasi.' }}
              </p>
            </div>

            <!-- Pagination -->
            <div v-if="reservations.data.length > 0 && reservations.last_page > 1" class="mt-6">
              <div class="flex justify-center">
                <div class="join">
                  <Link 
                    v-for="page in paginationPages" 
                    :key="page"
                    :href="getPageUrl(page)"
                    class="join-item btn"
                    :class="{ 'btn-active': page === reservations.current_page }"
                  >
                    {{ page }}
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AppLayout from '~/Layouts/AppLayout.vue'
import FilterButtons from '~/Components/FilterButtons.vue'

// Props
const props = defineProps({
  reservations: Object,
  filters: Object,
})

// Reactive data
const filters = ref({
  patient_search: props.filters.patient_search || '',
  start_date: props.filters.start_date || '',
  end_date: props.filters.end_date || '',
})

// Computed
const hasFilters = computed(() => {
  return filters.value.patient_search || filters.value.start_date || filters.value.end_date
})

const paginationPages = computed(() => {
  const pages = []
  const current = props.reservations.current_page
  const last = props.reservations.last_page
  
  // Show first page
  if (current > 3) pages.push(1)
  if (current > 4) pages.push('...')
  
  // Show pages around current
  for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
    pages.push(i)
  }
  
  // Show last page
  if (current < last - 3) pages.push('...')
  if (current < last - 2) pages.push(last)
  
  return pages
})

// Methods
const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const formatTime = (time) => {
  return new Date(`2000-01-01 ${time}`).toLocaleTimeString('id-ID', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR'
  }).format(amount)
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'badge-warning',
    confirmed: 'badge-info',
    completed: 'badge-success',
    cancelled: 'badge-error'
  }
  return classes[status] || 'badge-ghost'
}

const getStatusText = (status) => {
  const texts = {
    pending: 'Pending',
    confirmed: 'Dikonfirmasi',
    completed: 'Selesai',
    cancelled: 'Dibatalkan'
  }
  return texts[status] || status
}

const getCompletedCount = () => {
  return props.reservations.data.filter(reservation => reservation.status === 'completed').length
}

const navigateToReservation = (id) => {
  router.get(route('reservations.show.doctor', id))
}

const applyFilters = () => {
  router.get(route('doctor.history'), filters.value, {
    preserveState: true,
    preserveScroll: true,
  })
}

const clearFilters = () => {
  filters.value = {
    patient_search: '',
    start_date: '',
    end_date: '',
  }
  router.get(route('doctor.history'), {}, {
    preserveState: true,
    preserveScroll: true,
  })
}

const getPageUrl = (page) => {
  if (page === '...') return '#'
  
  const params = { ...filters.value, page }
  return route('doctor.history', params)
}
</script>
