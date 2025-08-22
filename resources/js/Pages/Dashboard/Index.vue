<template>

  <Head title="Dashboard" />
  <component :is="layoutComponent">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <p class="card-title">Dashboard</p>
          <p class="card-description">
            Selamat datang, {{ user.name }}!
            <span class="badge badge-primary ml-2">{{ getRoleLabel(user.role) }}</span>
          </p>
        </div>
        <div class="flex flex-row items-center space-x-3 text-right">
          <div class="text-sm text-base-content/60">{{ getCurrentDate() }}</div>

          <!-- Notification Dropdown (only for Dokter and Pasien) -->
          <div v-if="canAccess(['dokter', 'pasien'])" class="dropdown dropdown-end">
            <label tabindex="0" class="btn btn-ghost btn-circle indicator">
              <PhBellSimple :size="24" color="black" weight="regular" />
              <span v-if="unreadCount > 0" class="badge badge-xs badge-primary indicator-item">{{ unreadCount }}</span>
            </label>
            <div tabindex="0"
              class="dropdown-content menu p-0 shadow bg-white rounded-box w-96 border border-gray-400 max-h-96 overflow-y-auto">
              <div class="p-4 border-b border-gray-200 sticky top-0 bg-white">
                <div class="flex justify-between items-center">
                  <p class="card-description">Notifikasi</p>
                  <button v-if="unreadCount > 0" @click="markAllAsRead" class="btn btn-xs btn-outline">
                    Tandai Semua Dibaca
                  </button>
                </div>
              </div>

              <div v-if="notifications.length === 0" class="text-center p-6 text-gray-600">
                <p>Tidak ada notifikasi</p>
              </div>

              <div v-else class="max-h-80 overflow-y-auto">
                <div v-for="notification in notifications" :key="notification.id" :class="[
                  'p-4 hover:bg-gray-50 cursor-pointer transition-colors border-b border-gray-100 last:border-b-0',
                  !notification.is_read ? 'bg-blue-50 border-l-4 border-l-primary' : ''
                ]" @click="markAsRead(notification.id)">
                  <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 mt-1">
                      <span :class="notification.type === 'h-1' ? 'badge-warning' : 'badge-info'"
                        class="badge badge-sm">
                        {{ notification.type === 'h-1' ? 'H-1' : 'H-0' }}
                      </span>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm text-gray-800 leading-relaxed break-words">{{ notification.message }}</p>
                      <div class="flex justify-between items-center mt-2">
                        <span class="text-xs text-gray-600">
                          {{ formatDate(notification.created_at) }}
                        </span>
                        <div v-if="!notification.is_read" class="w-2 h-2 bg-primary rounded-full flex-shrink-0"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div v-if="stats && !canAccess(['pasien'])" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Admin/Resepsionis Stats -->
        <template v-if="canAccess(['admin', 'resepsionis'])">
          <div class="stats shadow bg-white">
            <div class="stat">
              <div class="stat-figure text-primary">
                <PhUsers :size="40" color="currentColor" weight="regular" />
              </div>
              <div class="stat-title">Total Pasien</div>
              <div class="stat-value text-primary hover:no-underline">{{ stats.total_patients }}</div>
              <div class="stat-desc">Terdaftar di sistem</div>
            </div>
          </div>

          <div class="stats shadow bg-white">
            <div class="stat">
              <div class="stat-figure text-secondary">
                <PhClock :size="40" color="currentColor" weight="regular" />
              </div>
              <div class="stat-title">Reservasi Hari Ini</div>
              <div class="stat-value text-secondary">{{ stats.total_reservations_today }}</div>
              <div class="stat-desc">{{ getCurrentDateDesc() }}</div>
            </div>
          </div>

          <div class="stats shadow bg-white">
            <div class="stat">
              <div class="stat-figure text-accent">
                <PhCurrencyCircleDollar :size="40" color="currentColor" weight="regular" />
              </div>
              <div class="stat-title">Transaksi Hari Ini</div>
              <div class="stat-value text-accent">{{ stats.total_transactions_today }}</div>
              <div class="stat-desc">{{ getCurrentDateDesc() }}</div>
            </div>
          </div>
        </template>

        <!-- Manajer Stok Stats -->
        <template v-if="canAccess(['manajer_stok']) && !canAccess(['admin'])">
          <div class="stats shadow bg-white">
            <div class="stat">
              <div class="stat-figure text-primary">
                <PhCube :size="40" color="currentColor" weight="regular" />
              </div>
              <div class="stat-title">Total Produk</div>
              <div class="stat-value text-primary hover:no-underline">{{ stats.total_products }}</div>
              <div class="stat-desc">Produk tersedia</div>
            </div>
          </div>

          <div class="stats shadow bg-white">
            <div class="stat">
              <div class="stat-figure text-secondary">
                <PhFlask :size="40" color="currentColor" weight="regular" />
              </div>
              <div class="stat-title">Total Layanan</div>
              <div class="stat-value text-secondary">{{ stats.total_services }}</div>
              <div class="stat-desc">Layanan tersedia</div>
            </div>
          </div>

          <div class="stats shadow bg-white">
            <div class="stat">
              <div class="stat-figure text-warning">
                <PhExclamationMark :size="40" color="currentColor" weight="regular" />
              </div>
              <div class="stat-title">Stok Rendah</div>
              <div class="stat-value text-warning">{{ stats.low_stock_products }}</div>
              <div class="stat-desc">Produk perlu restok</div>
            </div>
          </div>

          <div class="stats shadow bg-white">
            <div class="stat">
              <div class="stat-figure text-error">
                <PhX :size="40" color="currentColor" weight="regular" />
              </div>
              <div class="stat-title">Stok Habis</div>
              <div class="stat-value text-error">{{ stats.out_of_stock_products }}</div>
              <div class="stat-desc">Produk tidak tersedia</div>
            </div>
          </div>
        </template>

        <!-- Dokter Stats -->
        <template v-if="canAccess(['dokter']) && !canAccess(['admin'])">
          <div class="stats shadow bg-white">
            <div class="stat">
              <div class="stat-figure text-primary">
                <PhClock :size="40" color="currentColor" weight="regular" />
              </div>
              <div class="stat-title">Reservasi Hari Ini</div>
              <div class="stat-value text-primary hover:no-underline">{{ stats.reservations_today }}</div>
              <div class="stat-desc">{{ getCurrentDateDesc() }}</div>
            </div>
          </div>

          <div class="stats shadow bg-white">
            <div class="stat">
              <div class="stat-figure text-secondary">
                <PhCalendarDots :size="40" color="currentColor" weight="regular" />
              </div>
              <div class="stat-title">Reservasi Mendatang</div>
              <div class="stat-value text-secondary">{{ stats.upcoming_reservations || 0 }}</div>
              <div class="stat-desc">7 hari ke depan</div>
            </div>
          </div>

          <div class="stats shadow bg-white">
            <div class="stat">
              <div class="stat-figure text-accent">
                <PhCheck :size="40" color="currentColor" weight="regular" />
              </div>
              <div class="stat-title">Treatment Selesai</div>
              <div class="stat-value text-accent">{{ stats.completed_reservations }}</div>
              <div class="stat-desc">Total keseluruhan</div>
            </div>
          </div>
        </template>
      </div>

      <!-- Katalog untuk Pasien -->
      <div v-if="canAccess(['pasien']) && (products || services)" class="space-y-6">
        <!-- Produk -->
        <div v-if="products && products.length > 0" class="card bg-white border border-clinic-green shadow-xl">
          <div class="card-body">
            <div class="flex justify-between items-center mb-4">
              <h2 class="card-title">Produk Kami</h2>
              <Link href="/products-catalog" class="btn btn-outline btn-sm">
              Lihat Semua
              <PhArrowRight :size="20" color="#1A3300" weight="regular" />
              </Link>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
              <div v-for="product in products" :key="product.id"
                class="card bg-white border border-clinic-green shadow-lg hover:shadow-xl transition-shadow">
                <div class="card-body">
                  <p class="card-title">{{ product.nama_produk }}</p>
                  <p class="card-description">{{ product.deskripsi }}</p>
                  <div class="flex justify-between items-center pt-4">
                    <span class="card-subtitle">{{ formatCurrency(product.harga) }}</span>
                    <Link :href="route('products.show.pasien', product.id)" class="btn btn-primary btn-base">
                    Detail
                    </Link>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Layanan -->
        <div v-if="services && services.length > 0" class="card bg-white border border-clinic-green shadow-xl">
          <div class="card-body">
            <div class="flex justify-between items-center mb-4">
              <h2 class="card-title">Layanan Kami</h2>
              <Link href="/services-catalog" class="btn btn-outline btn-sm">
              Lihat Semua
              <PhArrowRight :size="20" color="#1A3300" weight="regular" />
              </Link>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
              <div v-for="service in services" :key="service.id" class="card bg-white border border-clinic-green shadow-lg hover:shadow-xl transition-shadow">
                <div class="card-body">
                  <p class="card-title">{{ service.nama_layanan }}</p>
                  <p class="card-description">{{ service.deskripsi }}</p>
                  <div class="flex justify-between items-center pt-4">
                    <span class="card-subtitle">{{ formatCurrency(service.harga) }}</span>
                    <span class="badge badge-secondary">{{ service.durasi_menit }} menit</span>
                  </div>
                  <div class="flex justify-end mt-2">
                    <Link :href="route('services.show.pasien', service.id)" class="btn btn-primary btn-base">
                    Detail
                    </Link>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div v-if="!canAccess(['pasien'])" class="card bg-white shadow-xl">
        <div class="card-body">
          <h2 class="card-title">Aksi Cepat</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <template v-if="canAccess(['resepsionis', 'admin'])">
              <Link href="/patients/create" class="btn btn-primary">
              <PhUsers :size="20" color="currentColor" weight="regular" />
              Tambah Pasien Baru
              </Link>
              <Link href="/reservations/create" class="btn btn-secondary">
              <PhCalendarDots :size="20" color="currentColor" weight="regular" />
              Buat Reservasi
              </Link>
              <Link href="/transactions/create" class="btn btn-accent">
              <PhCurrencyCircleDollar :size="20" color="currentColor" weight="regular" />
              Transaksi Baru
              </Link>
            </template>

            <template v-if="canAccess(['manajer_stok', 'admin'])">
              <Link href="/products/create" class="btn btn-primary">
              <PhCube :size="20" color="currentColor" weight="regular" />
              Tambah Produk
              </Link>
              <Link href="/services/create" class="btn btn-secondary">
              <PhFlask :size="20" color="currentColor" weight="regular" />
              Tambah Layanan
              </Link>
            </template>

            <template v-if="canAccess(['dokter'])">
              <Link href="/medical-records/create" class="btn btn-primary">
              <PhFileText :size="20" color="currentColor" weight="regular" />
              Catat Rekam Medis
              </Link>
            </template>
          </div>
        </div>
      </div>
    </div>
  </component>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { Link, usePage, Head } from '@inertiajs/vue3'
import AppLayout from '~/Layouts/AppLayout.vue'
import PasienLayout from '~/Layouts/PasienLayout.vue'
import axios from 'axios'
import { PhBellSimple, PhUsers, PhClock, PhCurrencyCircleDollar, PhCube, PhFlask, PhExclamationMark, PhX, PhCalendarDots, PhCheck, PhFileText, PhArrowRight } from '@phosphor-icons/vue'

const { props } = usePage()
const user = computed(() => props.user)
const stats = computed(() => props.stats)
const products = computed(() => props.products)
const services = computed(() => props.services)

// Notification state
const notifications = ref([])

// Computed property for unread count
const unreadCount = computed(() => {
  return notifications.value.filter(notification => !notification.is_read).length
})

// Choose layout based on user role
const layoutComponent = computed(() => {
  return user.value?.role === 'pasien' ? PasienLayout : AppLayout
})

const canAccess = (roles) => {
  return user.value && roles.includes(user.value.role)
}

const getRoleLabel = (role) => {
  const labels = {
    admin: 'Administrator',
    resepsionis: 'Resepsionis',
    dokter: 'Dokter/Terapis',
    manajer_stok: 'Manajer Stok',
    pasien: 'Pasien'
  }
  return labels[role] || role
}

const getCurrentDate = () => {
  return new Date().toLocaleDateString('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getCurrentDateDesc = () => {
  return new Date().toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(amount)
}

// Notification functions
const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const fetchNotifications = async () => {
  try {
    const response = await axios.get('/notifications')
    notifications.value = response.data.notifications
  } catch (error) {
    console.error('Error fetching notifications:', error)
  }
}

const markAsRead = async (notificationId) => {
  try {
    await axios.put(`/notifications/${notificationId}/read`)
    // Update local state
    const notification = notifications.value.find(n => n.id === notificationId)
    if (notification) {
      notification.is_read = true
    }
  } catch (error) {
    console.error('Error marking notification as read:', error)
  }
}

const markAllAsRead = async () => {
  try {
    await axios.put('/notifications/mark-all-read')
    // Update local state
    notifications.value.forEach(notification => {
      notification.is_read = true
    })
  } catch (error) {
    console.error('Error marking all notifications as read:', error)
  }
}

// Load notifications when component mounts
onMounted(() => {
  if (user.value) {
    fetchNotifications()

    // Refresh notifications every 5 minutes
    setInterval(fetchNotifications, 5 * 60 * 1000)
  }
})
</script>