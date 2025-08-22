<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-serif font-bold text-clinic-green">Detail Data Pasien</h1>
          <p class="text-gray-600 mt-1 font-sans">Informasi lengkap pasien dan riwayat kunjungan</p>
        </div>
        <div class="flex gap-2">
          <Link 
            href="/patients"
            class="btn btn-outline font-sans"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
          </Link>
          <Link 
            :href="`/patients/${patient.id}/edit`"
            class="btn bg-clinic-yellow text-clinic-green border-clinic-yellow hover:bg-yellow-400 font-sans font-semibold"
            v-if="canAccess(['admin', 'resepsionis'])"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Data
          </Link>
        </div>
      </div>

      <!-- Patient Information -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Personal Info Card -->
        <div class="lg:col-span-1">
          <div class="card bg-white shadow-lg rounded-xl border border-gray-200">
            <div class="card-body bg-primary-bg">
              <h2 class="card-title text-clinic-green font-serif">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Informasi Pribadi
              </h2>
              
              <div class="space-y-4">
                <div>
                  <label class="text-sm font-medium text-gray-600 font-sans">Nama Lengkap</label>
                  <p class="text-clinic-green font-medium font-sans">{{ patient.nama_lengkap }}</p>
                </div>
                
                <div>
                  <label class="text-sm font-medium text-gray-600 font-sans">Tanggal Lahir</label>
                  <p class="text-clinic-green font-sans">{{ formatDate(patient.tanggal_lahir) }} ({{ calculateAge(patient.tanggal_lahir) }} tahun)</p>
                </div>
                
                <div>
                  <label class="text-sm font-medium text-gray-600 font-sans">Jenis Kelamin</label>
                  <p class="text-clinic-green font-sans">{{ patient.jenis_kelamin }}</p>
                </div>
                
                <div>
                  <label class="text-sm font-medium text-base-content/60">Nomor Telepon</label>
                  <p class="text-base-content">{{ patient.nomor_telepon }}</p>
                </div>
                
                <div v-if="patient.email">
                  <label class="text-sm font-medium text-base-content/60">Email</label>
                  <p class="text-base-content">{{ patient.email }}</p>
                </div>
                
                <div>
                  <label class="text-sm font-medium text-base-content/60">Alamat</label>
                  <p class="text-base-content">{{ patient.alamat }}</p>
                </div>

                <div v-if="patient.user">
                  <label class="text-sm font-medium text-base-content/60">Status Akun</label>
                  <div class="ml-2 badge badge-primary">Memiliki Akun Login</div>
                </div>
                <div v-else>
                  <label class="text-sm font-medium text-base-content/60">Status Akun</label>
                  <div class="badge badge-neutral">Belum Memiliki Akun</div>
                </div>
                
                <div>
                  <label class="text-sm font-medium text-base-content/60">Terdaftar Sejak</label>
                  <p class="text-base-content">{{ formatDateTime(patient.created_at) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- History and Records -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Reservations History -->
          <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
              <h2 class="card-title text-primary">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Riwayat Reservasi
                <div class="badge badge-primary">{{ reservations.total || 0 }}</div>
              </h2>
              
              <div v-if="reservations.data && reservations.data.length > 0" class="space-y-3">
                <div 
                  v-for="reservation in reservations.data" 
                  :key="reservation.id"
                  class="p-4 border border-base-300 rounded-lg cursor-pointer hover:bg-base-200 transition-colors"
                  @click="navigateToReservation(reservation.id)"
                >
                  <div class="flex justify-between items-start">
                    <div class="space-y-2">
                      <h4 class="font-semibold">{{ reservation.service?.nama_layanan || 'Layanan Tidak Diketahui' }}</h4>
                      <p class="text-sm text-base-content/60">
                        {{ formatDate(reservation.tanggal_reservasi) }} - {{ reservation.waktu_reservasi }}
                      </p>
                      <p class="text-sm">Ditangani oleh: {{ reservation.user?.name || 'Tidak Diketahui' }}</p>
                      <p v-if="reservation.catatan" class="text-sm italic">Catatan: {{ reservation.catatan }}</p>
                    </div>
                    <div class="flex flex-col items-end space-y-2">
                      <div class="badge" :class="getStatusBadgeClass(reservation.status)">
                        {{ getStatusLabel(reservation.status) }}
                      </div>
                      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                      </svg>
                    </div>
                  </div>
                </div>
                
                <!-- Pagination for Reservations -->
                <div v-if="reservations.last_page > 1" class="flex justify-center mt-4">
                  <div class="join">
                    <Link 
                      v-for="(link, index) in reservations.links" 
                      :key="index"
                      :href="link.url || '#'" 
                      class="join-item btn btn-sm"
                      :class="{ 'btn-active': link.active, 'btn-disabled': !link.url }"
                      v-html="link.label"
                    />
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-8 text-base-content/60">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p>Belum ada riwayat reservasi</p>
              </div>
            </div>
          </div>

          <!-- Medical Records - Only for Admin and Dokter -->
          <div v-if="canAccess(['admin', 'dokter'])" class="card bg-base-100 shadow-xl">
            <div class="card-body">
              <h2 class="card-title text-primary">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Rekam Medis
                <div class="badge badge-primary">{{ medicalRecords.total || 0 }}</div>
              </h2>
              
              <div v-if="medicalRecords.data && medicalRecords.data.length > 0" class="space-y-3">
                <div 
                  v-for="record in medicalRecords.data" 
                  :key="record.id"
                  class="p-4 border border-base-300 rounded-lg cursor-pointer hover:bg-base-200 transition-colors"
                  @click="navigateToMedicalRecord(record.id)"
                >
                  <div class="space-y-2">
                    <div class="flex justify-between items-start">
                      <h4 class="font-semibold">{{ formatDate(record.tanggal_pemeriksaan) }}</h4>
                      <div class="flex items-center space-x-2">
                        <p class="text-sm text-base-content/60">{{ record.user?.name || 'Tidak Diketahui' }}</p>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                      </div>
                    </div>
                    <p v-if="record.diagnosa" class="text-sm"><strong>Diagnosis:</strong> {{ record.diagnosa }}</p>
                    <p v-if="record.tindakan" class="text-sm"><strong>Tindakan:</strong> {{ record.tindakan }}</p>
                    <p v-if="record.catatan" class="text-sm"><strong>Catatan:</strong> {{ record.catatan }}</p>
                    <p v-if="record.reservation" class="text-sm"><strong>Layanan:</strong> {{ record.reservation.service?.nama_layanan }}</p>
                  </div>
                </div>
                
                <!-- Pagination for Medical Records -->
                <div v-if="medicalRecords.last_page > 1" class="flex justify-center mt-4">
                  <div class="join">
                    <Link 
                      v-for="(link, index) in medicalRecords.links" 
                      :key="index"
                      :href="link.url || '#'" 
                      class="join-item btn btn-sm"
                      :class="{ 'btn-active': link.active, 'btn-disabled': !link.url }"
                      v-html="link.label"
                    />
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-8 text-base-content/60">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p>Belum ada rekam medis</p>
              </div>
            </div>
          </div>

          <!-- Transactions -->
          <div v-if="canAccess(['admin', 'resepsionis'])" class="card bg-base-100 shadow-xl">
            <div class="card-body">
              <h2 class="card-title text-primary">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                Riwayat Transaksi
                <div class="badge badge-primary">{{ patient.transactions?.length || 0 }}</div>
              </h2>
              
              <div v-if="patient.transactions && patient.transactions.length > 0" class="space-y-3">
                <div 
                  v-for="transaction in patient.transactions" 
                  :key="transaction.id"
                  class="p-4 border border-base-300 rounded-lg"
                >
                  <div class="flex justify-between items-start">
                    <div class="space-y-2">
                      <h4 class="font-semibold">Transaksi #{{ transaction.id }}</h4>
                      <p class="text-sm text-base-content/60">{{ formatDateTime(transaction.created_at) }}</p>
                      <p class="text-sm">Kasir: {{ transaction.user?.name || 'Tidak Diketahui' }}</p>
                    </div>
                    <div class="text-right">
                      <p class="font-bold text-lg">Rp {{ formatCurrency(transaction.total_harga) }}</p>
                      <div class="badge badge-success">{{ transaction.metode_pembayaran }}</div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-8 text-base-content/60">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                <p>Belum ada riwayat transaksi</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import AppLayout from '~/Layouts/AppLayout.vue'

const props = defineProps({
  patient: Object,
  reservations: Object,
  medicalRecords: Object,
  transactions: Object,
})

const page = usePage()

// Navigation functions
const navigateToReservation = (id) => {
  // Use different route based on user role
  if (page.props.auth.user.role === 'dokter') {
    router.get(route('reservations.show.doctor', id))
  } else {
    router.get(route('reservations.show', id))
  }
}

const navigateToMedicalRecord = (id) => {
  router.get(route('medical-records.show', id))
}

// Helper functions
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const formatDateTime = (dateString) => {
  return new Date(dateString).toLocaleString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const calculateAge = (birthDate) => {
  const today = new Date()
  const birth = new Date(birthDate)
  let age = today.getFullYear() - birth.getFullYear()
  const monthDiff = today.getMonth() - birth.getMonth()
  
  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
    age--
  }
  
  return age
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID').format(amount)
}

const getStatusLabel = (status) => {
  const labels = {
    'pending': 'Menunggu',
    'confirmed': 'Dikonfirmasi',
    'completed': 'Selesai',
    'cancelled': 'Dibatalkan'
  }
  return labels[status] || status
}

const getStatusBadgeClass = (status) => {
  const classes = {
    'pending': 'badge-warning',
    'confirmed': 'badge-info',
    'completed': 'badge-success',
    'cancelled': 'badge-error'
  }
  return classes[status] || 'badge-neutral'
}

// Check access permissions
const canAccess = (roles) => {
  const userRole = page.props.auth?.user?.role
  return roles.includes(userRole)
}
</script>
