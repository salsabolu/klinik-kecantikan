<template>
  <AppLayout>
    <Head title="Jadwal Mendatang" />
    
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <div>
                <h2 class="text-2xl font-semibold text-gray-900">Jadwal Reservasi Mendatang</h2>
                <p class="text-gray-600 mt-1">{{ formatDateRange(dateRange.start, dateRange.end) }}</p>
              </div>
              <div class="flex space-x-2">
                <Link :href="route('resepsionis.schedule')" class="btn btn-primary">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Jadwal Hari Ini
                </Link>
                <Link :href="route('reservations.index')" class="btn btn-outline">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                  Kelola Reservasi
                </Link>
              </div>
            </div>

            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
              <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <div class="flex items-center">
                  <div class="p-2 bg-blue-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Reservasi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ getTotalReservations() }}</p>
                  </div>
                </div>
              </div>

              <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <div class="flex items-center">
                  <div class="p-2 bg-yellow-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-gray-900">{{ getStatusCount('pending') }}</p>
                  </div>
                </div>
              </div>

              <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <div class="flex items-center">
                  <div class="p-2 bg-blue-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Dikonfirmasi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ getStatusCount('confirmed') }}</p>
                  </div>
                </div>
              </div>

              <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <div class="flex items-center">
                  <div class="p-2 bg-green-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Dokter Aktif</p>
                    <p class="text-2xl font-bold text-gray-900">{{ Object.keys(reservationsByDoctor).length }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Doctor Schedules -->
            <div v-if="Object.keys(reservationsByDoctor).length > 0" class="space-y-8">
              <div v-for="(reservations, doctorName) in reservationsByDoctor" :key="doctorName" class="bg-gray-50 rounded-lg p-6">
                <!-- Doctor Header -->
                <div class="flex items-center justify-between mb-4">
                  <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                      {{ getDoctorInitials(doctorName) }}
                    </div>
                    <div>
                      <h3 class="text-xl font-semibold text-gray-900">Dr. {{ doctorName }}</h3>
                      <p class="text-gray-600">{{ reservations.length }} jadwal mendatang</p>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="text-sm text-gray-600">Waktu tersibuk</p>
                    <p class="text-lg font-semibold text-gray-900">{{ getFirstTime(reservations) }} - {{ getLastTime(reservations) }}</p>
                  </div>
                </div>

                <!-- Reservations by Date -->
                <div class="space-y-6">
                  <div v-for="(dailyReservations, date) in groupByDate(reservations)" :key="date" class="bg-white rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-3">{{ formatDate(date) }}</h4>
                    <div class="space-y-3">
                      <div v-for="(reservation, index) in dailyReservations" :key="reservation.id" 
                           class="bg-white border rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer hover:bg-gray-50"
                           @click="navigateToReservation(reservation.id)">
                        <div class="flex items-center justify-between">
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
                              <h4 class="text-lg font-medium text-gray-900">{{ reservation.patient.nama_lengkap }}</h4>
                              <p class="text-gray-600">{{ reservation.service.nama_layanan }}</p>
                              <p class="text-sm text-gray-500">{{ reservation.patient.nomor_telepon }}</p>
                            </div>

                            <div v-if="reservation.catatan" class="mt-2">
                              <p class="text-sm text-gray-600">
                                <span class="font-medium">Catatan:</span> {{ reservation.catatan }}
                              </p>
                            </div>
                          </div>

                          <div class="flex space-x-2">
                            <Link :href="route('reservations.show', reservation.id)" 
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
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada jadwal mendatang</h3>
              <p class="mt-1 text-sm text-gray-500">Tidak ada dokter yang memiliki jadwal reservasi untuk 7 hari ke depan.</p>
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
  reservationsByDoctor: Object,
  dateRange: Object,
})

// Methods
const navigateToReservation = (reservationId) => {
  router.visit(route('reservations.show', reservationId))
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

const formatDateRange = (startDate, endDate) => {
  const start = new Date(startDate)
  const end = new Date(endDate)
  return `${start.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })} - ${end.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}`
}

const formatTime = (time) => {
  return new Date(`2000-01-01 ${time}`).toLocaleTimeString('id-ID', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getDoctorInitials = (name) => {
  return name.split(' ').map(word => word.charAt(0)).join('').substring(0, 2).toUpperCase()
}

const getFirstTime = (reservations) => {
  if (reservations.length === 0) return '-'
  const times = reservations.map(r => r.waktu_mulai).sort()
  return formatTime(times[0])
}

const getLastTime = (reservations) => {
  if (reservations.length === 0) return '-'
  const times = reservations.map(r => r.waktu_selesai).sort()
  return formatTime(times[times.length - 1])
}

const getTotalReservations = () => {
  return Object.values(props.reservationsByDoctor).reduce((total, reservations) => total + reservations.length, 0)
}

const getAllReservations = () => {
  return Object.values(props.reservationsByDoctor).flat()
}

const getStatusCount = (status) => {
  return getAllReservations().filter(reservation => reservation.status === status).length
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

const groupByDate = (reservations) => {
  return reservations.reduce((groups, reservation) => {
    const date = reservation.tanggal_reservasi
    if (!groups[date]) {
      groups[date] = []
    }
    groups[date].push(reservation)
    return groups
  }, {})
}
</script>
