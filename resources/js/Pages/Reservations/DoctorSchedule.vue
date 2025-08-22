<template>
  <AppLayout>
    <Head title="Jadwal Reservasi Hari Ini" />
    
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <div>
                <h2 class="text-2xl font-semibold text-gray-900">Jadwal Reservasi Hari Ini</h2>
                <p class="text-gray-600 mt-1">{{ formatDate(date) }}</p>
              </div>
              <div class="flex space-x-2">
                <Link :href="route('doctor.upcoming')" class="btn btn-info">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Jadwal Mendatang
                </Link>
                <Link :href="route('doctor.history')" class="btn btn-outline">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                  </svg>
                  Riwayat Reservasi
                </Link>
              </div>
            </div>

            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
              <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <div class="flex items-center">
                  <div class="p-2 bg-blue-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Reservasi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ reservations.length }}</p>
                  </div>
                </div>
              </div>

              <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <div class="flex items-center">
                  <div class="p-2 bg-green-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Selesai</p>
                    <p class="text-2xl font-bold text-gray-900">{{ getStatusCount('completed') }}</p>
                  </div>
                </div>
              </div>

              <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <div class="flex items-center">
                  <div class="p-2 bg-yellow-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Menunggu</p>
                    <p class="text-2xl font-bold text-gray-900">{{ getStatusCount('pending') + getStatusCount('confirmed') }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Schedule List -->
            <div v-if="reservations.length > 0" class="space-y-4">
              <div v-for="(reservation, index) in reservations" :key="reservation.id" 
                   class="bg-white border rounded-lg p-6 hover:shadow-md transition-shadow cursor-pointer hover:bg-gray-50"
                   @click="navigateToReservation(reservation.id)">
                <div class="flex items-center justify-between">
                  <div class="flex items-start space-x-4 flex-1">
                    <!-- Row Number -->
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-semibold">
                      {{ index + 1 }}
                    </div>
                    
                    <div class="flex-1">
                      <div class="flex items-center space-x-4">
                        <div class="text-lg font-semibold text-blue-600">
                          {{ formatTime(reservation.waktu_mulai) }} - {{ formatTime(reservation.waktu_selesai) }}
                        </div>
                        <div class="badge" :class="getStatusClass(reservation.status)">
                          {{ getStatusText(reservation.status) }}
                        </div>
                      </div>
                    
                    <div class="mt-2">
                      <h3 class="text-lg font-medium text-gray-900">{{ reservation.patient.nama_lengkap }}</h3>
                      <p class="text-gray-600">{{ reservation.service.nama_layanan }}</p>
                      <p class="text-sm text-gray-500">{{ reservation.patient.nomor_telepon }}</p>
                    </div>

                    <div v-if="reservation.catatan" class="mt-2">
                      <p class="text-sm text-gray-600">
                        <span class="font-medium">Catatan:</span> {{ reservation.catatan }}
                      </p>
                    </div>
                    </div>
                  </div>

                  <div class="flex space-x-2">
                    <Link :href="route('medical-records.create', { reservation_id: reservation.id })" 
                          class="btn btn-sm btn-primary"
                          @click.stop
                          v-if="reservation.status === 'confirmed' || reservation.status === 'completed'">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      </svg>
                      Buat Rekam Medis
                    </Link>
                    
                    <Link :href="route('reservations.show.doctor', reservation.id)" 
                          class="btn btn-sm btn-outline"
                          @click.stop>
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                      Detail
                    </Link>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada reservasi hari ini</h3>
              <p class="mt-1 text-sm text-gray-500">Anda belum memiliki jadwal reservasi untuk hari ini.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import AppLayout from '~/Layouts/AppLayout.vue'

// Props
const props = defineProps({
  reservations: Array,
  date: String,
})

// Methods
const navigateToReservation = (reservationId) => {
  router.visit(route('reservations.show.doctor', reservationId))
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatTime = (time) => {
  return new Date(`2000-01-01 ${time}`).toLocaleTimeString('id-ID', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getStatusCount = (status) => {
  return props.reservations.filter(reservation => reservation.status === status).length
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
</script>
