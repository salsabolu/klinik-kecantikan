<template>

  <Head title="Reservasi Saya" />
  <PasienLayout>
    <div class="max-w-7xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-serif font-bold text-clinic-green">Reservasi Saya</h1>
          <p class="text-gray-600 mt-1">Kelola dan pantau reservasi layanan Anda</p>
        </div>
        <div class="text-right">
          <p class="text-sm text-gray-600">Butuh reservasi baru?</p>
          <p class="text-sm text-gray-600 mb-1">Hubungi resepsionis kami di</p>
          <a href="tel:+6281234567890" class="btn btn-outline btn-sm">
            <PhPhone :size="20" color="black" weight="regular" />
            Telepon
          </a>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div class="stat bg-gradient-to-br from-clinic-green to-green-800 text-white rounded-lg shadow-lg">
          <div class="stat-figure">
            <PhCalendarDots :size="40" color="white" weight="regular" />
          </div>
          <div class="stat-title text-green-100">Total Reservasi</div>
          <div class="stat-value">{{ (reservations.data || []).length }}</div>
        </div>

        <div class="stat bg-gradient-to-br from-clinic-yellow to-yellow-500 text-clinic-green rounded-lg shadow-lg">
          <div class="stat-figure">
            <PhClock :size="40" color="currentColor" weight="regular" />
          </div>
          <div class="stat-title text-green-800">Menunggu</div>
          <div class="stat-value">{{ getStatusCount('pending') }}</div>
        </div>

        <div class="stat bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg shadow-lg">
          <div class="stat-figure">
            <PhCalendarCheck :size="40" color="white" weight="regular" />
          </div>
          <div class="stat-title text-green-100">Dikonfirmasi</div>
          <div class="stat-value">{{ getStatusCount('confirmed') }}</div>
        </div>

        <div class="stat bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg">
          <div class="stat-figure">
            <PhSparkle :size="40" color="white" weight="regular" />
          </div>
          <div class="stat-title text-purple-100">Selesai</div>
          <div class="stat-value">{{ getStatusCount('completed') }}</div>
        </div>
      </div>

      <!-- Filter Section -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div class="flex flex-col">
            <label class="label">
              <span class="label-text font-semibold">Status</span>
            </label>
            <select class="select select-bordered select-base" v-model="filters.status" @change="applyFilters">
              <option value="">Semua Status</option>
              <option value="pending">Menunggu</option>
              <option value="confirmed">Dikonfirmasi</option>
              <option value="completed">Selesai</option>
              <option value="cancelled">Dibatalkan</option>
            </select>
          </div>
          <div class="flex flex-col">
            <label class="label">
              <span class="label-text font-semibold">Tanggal Mulai</span>
            </label>
            <input type="date" class="input input-bordered input-base" v-model="filters.start_date"
              @change="applyFilters" />
          </div>
          <div class="flex flex-col">
            <label class="label">
              <span class="label-text font-semibold">Tanggal Selesai</span>
            </label>
            <input type="date" class="input input-bordered input-base" v-model="filters.end_date"
              @change="applyFilters" />
          </div>
        </div>

      <!-- Reservations List -->
      <div class="space-y-4">
        <div v-for="reservation in reservations.data" :key="reservation.id"
          class="card bg-white shadow-lg border-l-4" :class="getStatusBorderClass(reservation.status)">
          <div class="card-body">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center space-x-3 mb-3">
                  <p class="card-title">{{ reservation.service.nama_layanan }}</p>
                  <div class="badge" :class="getStatusBadgeClass(reservation.status)">
                    {{ getStatusLabel(reservation.status) }}
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                  <div class="flex items-center gap-2">
                    <PhUser :size="20" color="black" weight="regular" />
                    <span>{{ reservation.user.name }}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <PhCalendarCheck :size="20" color="black" weight="regular" />
                    <span>{{ formatDate(reservation.tanggal_reservasi) }}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <PhClock :size="20" color="black" weight="regular" />
                    <span>{{ reservation.waktu_mulai }} - {{ reservation.waktu_selesai }}</span>
                  </div>
                </div>

                <div class="mt-2">
                  <div class="flex items-center gap-2">
                    <PhCurrencyCircleDollar :size="20" color="black" weight="regular" />
                    <span class="font-semibold">{{ formatCurrency(reservation.service.harga) }}</span>
                  </div>
                </div>

                <div v-if="reservation.catatan" class="mt-4 p-3 bg-base-200 rounded">
                  <p class="text-sm">
                    <strong>Catatan:</strong> {{ reservation.catatan }}
                  </p>
                </div>
              </div>

              <div class="flex flex-col space-y-2">
                <Link :href="`/my-reservations/${reservation.id}`" class="btn btn-outline btn-sm">
                <PhEye :size="20" color="black" weight="regular" />
                Detail
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="(reservations.data || []).length === 0" class="py-12">
          <div class="flex flex-col justify-center items-center text-center">
            <PhCalendarDots :size="80" color="black" weight="regular" />
            <p class="text-lg font-medium text-gray-600">Belum ada reservasi</p>
            <p class="text-base text-gray-600 mb-4">Anda belum memiliki reservasi. Hubungi resepsionis kami untuk membuat
              reservasi!</p>
          </div>
          <div class="flex justify-center gap-4">
            <a href="tel:+6281234567890" class="btn btn-primary">
              <PhPhone :size="20" color="black" weight="regular" />
              Hubungi Resepsionis
            </a>
            <Link href="/services-catalog" class="btn btn-outline">
            Lihat Layanan
            </Link>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="flex justify-between items-center mt-8" v-if="(reservations.data || []).length > 0">
        <div class="text-sm text-gray-600">
          Menampilkan {{ reservations.from }} - {{ reservations.to }} dari {{ reservations.total }} reservasi
        </div>
        <div class="join">
          <Link v-for="(link, index) in reservations.links" :key="index" :href="link.url || '#'"
            class="join-item btn btn-sm" :class="{ 'btn-active': link.active, 'btn-disabled': !link.url }"
            v-html="link.label" />
        </div>
      </div>
    </div>
  </PasienLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import PasienLayout from '~/Layouts/PasienLayout.vue'
import { PhPhone, PhCalendarDots, PhClock, PhCalendarCheck, PhSparkle, PhUser, PhCurrencyCircleDollar, PhEye } from '@phosphor-icons/vue'

// Props
const props = defineProps({
  reservations: Object,
  filters: Object,
})

// Reactive data
const filters = reactive({
  status: props.filters?.status || '',
  start_date: props.filters?.start_date || '',
  end_date: props.filters?.end_date || '',
})

const showCancelModal = ref(false)

// Methods
const getStatusCount = (status) => {
  return (props.reservations.data || []).filter(reservation => reservation.status === status).length
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

const getStatusBorderClass = (status) => {
  const classes = {
    'pending': 'border-l-warning',
    'confirmed': 'border-l-info',
    'completed': 'border-l-success',
    'cancelled': 'border-l-error'
  }
  return classes[status] || 'border-l-neutral'
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

const applyFilters = () => {
  router.get('/my-reservations', filters, {
    preserveState: true,
    preserveScroll: true,
  })
}
</script>
