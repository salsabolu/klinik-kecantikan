<template>
  <AppLayout>

    <Head title="Detail Reservasi" />

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold">Detail Reservasi #{{ reservation.id }}</h2>
              <div class="flex space-x-2">
                <Link
                  :href="$page.props.auth.user.role === 'dokter' ? route('doctor.history') : route('reservations.index')"
                  class="btn btn-outline">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                  </path>
                </svg>
                Kembali
                </Link>
                <Link :href="route('reservations.edit', reservation.id)" class="btn btn-warning"
                  v-if="$page.props.auth.user.role === 'admin' || $page.props.auth.user.role === 'resepsionis'">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                  </path>
                </svg>
                Edit
                </Link>
              </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <!-- Main Information -->
              <div class="lg:col-span-2 space-y-6">
                <!-- Reservation Status -->
                <div class="card bg-base-100 shadow-md">
                  <div class="card-body">
                    <h3 class="card-title">Status Reservasi</h3>
                    <div class="flex items-center justify-between">
                      <div class="badge text-lg py-3 px-4" :class="getStatusClass(reservation.status)">
                        {{ getStatusText(reservation.status) }}
                      </div>
                      <div class="dropdown dropdown-end"
                        v-if="$page.props.auth.user.role === 'admin' || $page.props.auth.user.role === 'resepsionis'">
                        <label tabindex="0" class="btn btn-sm btn-outline">
                          Ubah Status
                          <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                          </svg>
                        </label>
                        <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
                          <li v-if="reservation.status !== 'pending'">
                            <a @click="updateStatus('pending')">Pending</a>
                          </li>
                          <li v-if="reservation.status !== 'confirmed'">
                            <a @click="updateStatus('confirmed')">Konfirmasi</a>
                          </li>
                          <li v-if="reservation.status !== 'completed'">
                            <a @click="updateStatus('completed')">Selesai</a>
                          </li>
                          <li v-if="reservation.status !== 'cancelled'">
                            <a @click="updateStatus('cancelled')">Batalkan</a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Patient Information -->
                <div class="card bg-base-100 shadow-md">
                  <div class="card-body">
                    <h3 class="card-title">Informasi Pasien</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label class="label">
                          <span class="label-text font-medium">Nama Lengkap</span>
                        </label>
                        <div class="text-lg">{{ reservation.patient.nama_lengkap }}</div>
                      </div>
                      <div>
                        <label class="label">
                          <span class="label-text font-medium">Nomor Telepon</span>
                        </label>
                        <div class="text-lg">{{ reservation.patient.nomor_telepon }}</div>
                      </div>
                      <div>
                        <label class="label">
                          <span class="label-text font-medium">Email</span>
                        </label>
                        <div class="text-lg">{{ reservation.patient.email || '-' }}</div>
                      </div>
                      <div>
                        <label class="label">
                          <span class="label-text font-medium">Jenis Kelamin</span>
                        </label>
                        <div class="text-lg">{{ reservation.patient.jenis_kelamin }}</div>
                      </div>
                      <div class="md:col-span-2">
                        <label class="label">
                          <span class="label-text font-medium">Alamat</span>
                        </label>
                        <div class="text-lg">{{ reservation.patient.alamat }}</div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Service Information -->
                <div class="card bg-base-100 shadow-md">
                  <div class="card-body">
                    <h3 class="card-title">Informasi Layanan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label class="label">
                          <span class="label-text font-medium">Nama Layanan</span>
                        </label>
                        <div class="text-lg">{{ reservation.service.nama_layanan }}</div>
                      </div>
                      <div>
                        <label class="label">
                          <span class="label-text font-medium">Harga</span>
                        </label>
                        <div class="text-lg font-semibold text-primary">{{ formatCurrency(reservation.service.harga) }}
                        </div>
                      </div>
                      <div>
                        <label class="label">
                          <span class="label-text font-medium">Durasi</span>
                        </label>
                        <div class="text-lg">{{ reservation.service.durasi_menit }} menit</div>
                      </div>
                      <div class="md:col-span-2">
                        <label class="label">
                          <span class="label-text font-medium">Deskripsi</span>
                        </label>
                        <div class="text-sm text-gray-600">{{ reservation.service.deskripsi }}</div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Notes -->
                <div v-if="reservation.catatan" class="card bg-base-100 shadow-md">
                  <div class="card-body">
                    <h3 class="card-title">Catatan</h3>
                    <div class="text-gray-700">{{ reservation.catatan }}</div>
                  </div>
                </div>

                <!-- Medical Records Section -->
                <div v-if="canAccess(['admin', 'dokter'])" class="card bg-base-100 shadow-md">
                  <div class="card-body">
                    <div class="flex justify-between items-center mb-4">
                      <h3 class="card-title">Rekam Medis</h3>
                      <Link :href="route('medical-records.create', { reservation_id: reservation.id })"
                        class="btn btn-primary btn-sm"
                        v-if="$page.props.auth.user.role === 'dokter' && (reservation.status === 'confirmed' || reservation.status === 'completed')">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                      </svg>
                      Buat Rekam Medis
                      </Link>
                    </div>

                    <div v-if="reservation.medical_records && reservation.medical_records.length > 0" class="space-y-4">
                      <div v-for="record in reservation.medical_records" :key="record.id"
                        class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex justify-between items-start mb-3">
                          <div>
                            <div class="font-medium text-gray-900">{{ formatDate(record.tanggal_pemeriksaan) }}</div>
                            <div class="text-sm text-gray-500">Oleh: {{ record.user.name }}</div>
                          </div>
                          <Link :href="route('medical-records.show', record.id)" class="btn btn-sm btn-outline">
                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                          </svg>
                          Detail
                          </Link>
                        </div>

                        <div class="space-y-2 text-sm">
                          <div>
                            <span class="font-medium text-gray-700">Diagnosa:</span>
                            <div class="text-gray-600 mt-1">{{ record.diagnosa }}</div>
                          </div>
                          <div>
                            <span class="font-medium text-gray-700">Tindakan:</span>
                            <div class="text-gray-600 mt-1">{{ record.tindakan }}</div>
                          </div>
                          <div v-if="record.catatan">
                            <span class="font-medium text-gray-700">Catatan:</span>
                            <div class="text-gray-600 mt-1">{{ record.catatan }}</div>
                          </div>

                          <!-- Allergies and Medications -->
                          <div v-if="record.allergies?.length > 0 || record.medications?.length > 0"
                            class="mt-3 pt-3 border-t border-gray-100">
                            <div v-if="record.allergies?.length > 0" class="mb-2">
                              <span class="font-medium text-red-700">Alergi:</span>
                              <div class="flex flex-wrap gap-1 mt-1">
                                <span v-for="allergy in record.allergies" :key="allergy.name"
                                  class="inline-block px-2 py-1 text-xs bg-red-100 text-red-800 rounded">
                                  {{ allergy.name }}
                                </span>
                              </div>
                            </div>
                            <div v-if="record.medications?.length > 0">
                              <span class="font-medium text-blue-700">Obat:</span>
                              <div class="flex flex-wrap gap-1 mt-1">
                                <span v-for="medication in record.medications" :key="medication.name"
                                  class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded"
                                  :title="`${medication.dosage || ''} ${medication.frequency || ''}`.trim()">
                                  {{ medication.name }}
                                </span>
                              </div>
                            </div>
                          </div>

                          <!-- Attachments Section -->
                          <div v-if="record.attachments && record.attachments.length > 0" 
                            class="mt-3 pt-3 border-t border-gray-100">
                            <span class="font-medium text-gray-700">Lampiran:</span>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-2">
                              <div v-for="attachment in record.attachments" :key="attachment.id"
                                class="relative group">
                                <!-- Image Attachments -->
                                <div v-if="attachment.mime_type.startsWith('image/')" 
                                  class="aspect-square bg-gray-100 rounded-lg overflow-hidden cursor-pointer hover:opacity-80 transition-opacity"
                                  @click="openAttachment(attachment)">
                                  <img :src="attachment.file_url" 
                                    :alt="attachment.filename"
                                    class="w-full h-full object-cover">
                                  <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" 
                                      fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                  </div>
                                </div>

                                <!-- Document Attachments -->
                                <div v-else 
                                  class="aspect-square bg-gray-100 rounded-lg overflow-hidden cursor-pointer hover:bg-gray-200 transition-colors flex flex-col items-center justify-center p-2"
                                  @click="openAttachment(attachment)">
                                  <svg class="w-8 h-8 text-gray-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                  </svg>
                                  <div class="text-xs text-gray-600 text-center leading-tight">
                                    {{ attachment.filename.length > 15 ? attachment.filename.substring(0, 12) + '...' : attachment.filename }}
                                  </div>
                                </div>

                                <!-- Attachment Type Badge -->
                                <div class="absolute top-1 left-1">
                                  <span class="px-1 py-0.5 text-xs rounded text-white text-shadow" 
                                    :class="{
                                      'bg-green-500': attachment.type === 'foto_sebelum',
                                      'bg-blue-500': attachment.type === 'foto_sesudah', 
                                      'bg-purple-500': attachment.type === 'hasil_lab',
                                      'bg-gray-500': attachment.type === 'lainnya'
                                    }">
                                    {{ getAttachmentTypeLabel(attachment.type) }}
                                  </span>
                                </div>

                                <!-- File Size -->
                                <div class="absolute bottom-1 right-1">
                                  <span class="px-1 py-0.5 text-xs bg-black bg-opacity-50 text-white rounded">
                                    {{ attachment.human_readable_size }}
                                  </span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div v-else class="text-center py-8 text-gray-500">
                      <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                      </svg>
                      <p>Belum ada rekam medis untuk reservasi ini</p>
                      <Link :href="route('medical-records.create', { reservation_id: reservation.id })"
                        class="btn btn-primary btn-sm mt-3"
                        v-if="$page.props.auth.user.role === 'dokter' && (reservation.status === 'confirmed' || reservation.status === 'completed')">
                      Buat Rekam Medis Pertama
                      </Link>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Sidebar -->
              <div class="space-y-6">
                <!-- Schedule Information -->
                <div class="card bg-primary text-primary-content shadow-md">
                  <div class="card-body">
                    <h3 class="card-title">Jadwal</h3>
                    <div class="space-y-3">
                      <div>
                        <div class="text-sm opacity-70">Tanggal</div>
                        <div class="text-xl font-bold">{{ formatDate(reservation.tanggal_reservasi) }}</div>
                      </div>
                      <div>
                        <div class="text-sm opacity-70">Waktu</div>
                        <div class="text-lg font-semibold">
                          {{ formatTime(reservation.waktu_mulai) }} - {{ formatTime(reservation.waktu_selesai) }}
                        </div>
                      </div>
                      <div>
                        <div class="text-sm opacity-70">Dokter/Terapis</div>
                        <div class="text-lg font-semibold">{{ reservation.user.name }}</div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Quick Actions -->
                <div class="card bg-base-100 shadow-md">
                  <div class="card-body">
                    <h3 class="card-title">Aksi Cepat</h3>
                    <div class="space-y-2">
                      <button @click="updateStatus('confirmed')" class="btn btn-success btn-block"
                        v-if="reservation.status === 'pending' && ($page.props.auth.user.role === 'admin' || $page.props.auth.user.role === 'resepsionis')">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                          </path>
                        </svg>
                        Konfirmasi
                      </button>

                      <button @click="updateStatus('completed')" class="btn btn-info btn-block"
                        v-if="reservation.status === 'confirmed' && ($page.props.auth.user.role === 'admin' || $page.props.auth.user.role === 'resepsionis')">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Selesai
                      </button>

                      <button @click="confirmCancel" class="btn btn-error btn-block"
                        v-if="reservation.status !== 'cancelled' && reservation.status !== 'completed' && ($page.props.auth.user.role === 'admin' || $page.props.auth.user.role === 'resepsionis')">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batalkan
                      </button>

                      <Link :href="route('patients.show', reservation.patient.id)" class="btn btn-outline btn-block"
                        v-if="$page.props.auth.user.role === 'admin' || $page.props.auth.user.role === 'resepsionis'">
                      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                      Lihat Pasien
                      </Link>
                    </div>
                  </div>
                </div>

                <!-- Reservation Meta -->
                <div class="card bg-base-100 shadow-md">
                  <div class="card-body">
                    <h3 class="card-title">Informasi Sistem</h3>
                    <div class="space-y-2 text-sm">
                      <div>
                        <span class="font-medium">Dibuat:</span>
                        <br>{{ formatDateTime(reservation.created_at) }}
                      </div>
                      <div>
                        <span class="font-medium">Diperbarui:</span>
                        <br>{{ formatDateTime(reservation.updated_at) }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div v-if="showCancelModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg">Konfirmasi Pembatalan</h3>
        <p class="py-4">Apakah Anda yakin ingin membatalkan reservasi ini?</p>
        <div class="modal-action">
          <button @click="showCancelModal = false" class="btn">Tidak</button>
          <button @click="cancelReservation" class="btn btn-error">Ya, Batalkan</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { router, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  reservation: Object,
})

const page = usePage()
const showCancelModal = ref(false)

const updateStatus = (status) => {
  router.patch(route('reservations.update', props.reservation.id), { status }, {
    preserveState: true,
    preserveScroll: true,
  })
}

const confirmCancel = () => {
  showCancelModal.value = true
}

const cancelReservation = () => {
  updateStatus('cancelled')
  showCancelModal.value = false
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
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

const formatDateTime = (datetime) => {
  return new Date(datetime).toLocaleString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
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

// Check access permissions
const canAccess = (roles) => {
  const userRole = page.props.auth?.user?.role
  return roles.includes(userRole)
}

// Attachment functions
const getAttachmentTypeLabel = (type) => {
  const labels = {
    'foto_sebelum': 'Sebelum',
    'foto_sesudah': 'Sesudah',
    'hasil_lab': 'Lab',
    'lainnya': 'Lainnya'
  }
  return labels[type] || type
}

const openAttachment = (attachment) => {
  // Open attachment in new tab
  window.open(attachment.file_url, '_blank')
}
</script>
