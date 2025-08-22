<template>

  <Head title="Detail Reservasi" />
  <PasienLayout>
    <div class="w-full mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-semibold">Detail Reservasi</h1>
          <p class="text-gray-600">Informasi lengkap reservasi Anda</p>
        </div>
        <div class="flex">
          <Link href="/my-reservations" class="btn btn-outline">
          <PhArrowLeft :size="20" color="black" weight="regular" />
          Kembali
          </Link>
        </div>
      </div>

      <!-- Content -->
      <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 lg:p-8">
          <!-- Reservation Info -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Main Info -->
            <!-- Treatment -->
            <div class="card bg-white shadow-md">
              <div class="card-body space-y-4">
                <!-- Title -->
                <div class="flex items-start gap-2">
                  <PhFlask :size="30" color="currentColor" weight="regular" />
                  <p class="text-lg font-semibold">
                    Layanan
                  </p>
                </div>

                <div class="space-y-2">
                  <div>
                    <p class="text-xl font-semibold">{{ reservation.service.nama_layanan }}</p>
                    <p class="text-base text-gray-600">{{ reservation.service.deskripsi }}</p>
                  </div>
                  <div class="flex items-center justify-between">
                    <div class="flex items-center text-sm gap-2">
                      <PhClock :size="20" color="currentColor" weight="regular" />
                      Durasi: {{ reservation.service.durasi_menit }} menit
                    </div>
                    <div class="text-lg font-semibold">
                      {{ formatCurrency(reservation.service.harga) }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Schedule & Status -->
            <div class="card bg-white shadow-md">
              <div class="card-body space-y-4">
                <!-- Title -->
                <div class="flex items-start gap-2">
                  <PhCalendarCheck :size="30" color="currentColor" weight="regular" />
                  <p class="text-lg font-semibold">
                    Jadwal Reservasi
                  </p>
                </div>

                <!-- Content -->
                <div class="space-y-2">
                  <div class="text-center">
                    <div class="text-xl font-semibold">
                      {{ formatDate(reservation.tanggal_reservasi) }}
                    </div>
                    <div class="text-base font-medium">
                      {{ reservation.waktu_mulai }} - {{ reservation.waktu_selesai }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Doctor/Terapis -->
            <div class="card bg-white shadow-md">
              <div class="card-body space-y-4">
                <!-- Title -->
                <div class="flex items-start gap-2">
                  <PhUser :size="30" color="currentColor" weight="regular" />
                  <p class="text-lg font-semibold">
                    Dokter/Terapis
                  </p>
                </div>

                <!-- Content -->
                <div class="flex items-center">
                  <div>
                    <p class="text-lg font-semibold">{{ reservation.user.name }}</p>
                    <p class="text-gray-600">{{ getRoleLabel(reservation.user.role) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Reservation Status -->
            <div class="card bg-white shadow-md">
              <div class="card-body space-y-4">
                <!-- Title -->
                <div class="flex items-start gap-2">
                  <PhCheckSquare :size="30" color="currentColor" weight="regular" />
                  <p class="text-lg font-semibold">
                    Status Reservasi
                  </p>
                </div>

                <!-- Content -->
                <div class="space-y-2">
                  <div class="flex items-center justify-between">
                    <span>Status Saat Ini:</span>
                    <div class="badge" :class="getStatusBadgeClass(reservation.status)">
                      {{ getStatusLabel(reservation.status) }}
                    </div>
                  </div>
                  <div class="text-sm text-gray-600">
                    <p v-if="reservation.status === 'pending'">
                      Reservasi Anda sedang menunggu konfirmasi dari klinik. Kami akan menghubungi Anda segera.
                    </p>
                    <p v-else-if="reservation.status === 'confirmed'">
                      Reservasi Anda telah dikonfirmasi. Mohon datang tepat waktu sesuai jadwal.
                    </p>
                    <p v-else-if="reservation.status === 'completed'">
                      Treatment telah selesai dilakukan. Terima kasih atas kepercayaan Anda.
                    </p>
                    <p v-else-if="reservation.status === 'cancelled'">
                      Reservasi ini telah dibatalkan.
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div v-if="reservation.catatan" class="mt-8">
            <div class="card bg-amber-50 border border-amber-200">
              <div class="card-body space-y-4">
                <!-- Title -->
                <div class="flex items-start gap-2 text-amber-800">
                  <PhNotePencil :size="30" color="currentColor" weight="regular" />
                  <p class="text-lg font-semibold">
                    Catatan Tambahan
                  </p>
                </div>

                <!-- Content -->
                <p class="text-amber-800">{{ reservation.catatan }}</p>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex justify-end mt-8">
            <button @click="printReservation" class="btn btn-outline gap-2">
              <PhPrinter :size="20" color="currentColor" weight="regular" />
              Cetak
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div v-if="showCancelModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">Konfirmasi Pembatalan</h3>
        <p class="mb-6">Apakah Anda yakin ingin membatalkan reservasi ini? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="modal-action">
          <button @click="showCancelModal = false" class="btn btn-outline">Tidak</button>
          <button @click="cancelReservation" class="btn btn-error">Ya, Batalkan</button>
        </div>
      </div>
    </div>
  </PasienLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import PasienLayout from '~/Layouts/PasienLayout.vue'
import { PhClock, PhCalendarCheck, PhUser, PhArrowLeft, PhPrinter, PhNotePencil, PhCheckSquare, PhFlask } from '@phosphor-icons/vue'

// Props
const props = defineProps({
  reservation: Object,
})

// Reactive data
const showCancelModal = ref(false)

// Methods
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

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(amount)
}

const getInitials = (name) => {
  return name.split(' ').map(word => word[0]).join('').toUpperCase()
}

const getRoleLabel = (role) => {
  const labels = {
    'dokter': 'Dokter',
    'terapis': 'Terapis'
  }
  return labels[role] || role
}

const confirmCancel = () => {
  showCancelModal.value = true
}

const cancelReservation = () => {
  router.patch(`/my-reservations/${props.reservation.id}/cancel`, {}, {
    onSuccess: () => {
      showCancelModal.value = false
    }
  })
}

const printReservation = () => {
  window.print()
}
</script>

<style>
@media print {

  .btn,
  .modal,
  nav,
  footer {
    display: none !important;
  }
}
</style>
