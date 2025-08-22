<template>
  <div class="min-h-screen bg-primary-bg" :class="{ 'hide-scrollbar': canAccess(['pasien']) }">
    <!-- Main Layout -->
    <div class="drawer lg:drawer-open">
      <input id="drawer-toggle" type="checkbox" class="drawer-toggle" />

      <!-- Page Content -->
      <div class="drawer-content flex flex-col">
        <!-- Navbar -->
        <div class="navbar bg-primary-bg shadow-sm border-b border-gray-400 lg:hidden">
          <div class="flex-none">
            <label for="drawer-toggle" class="btn btn-square btn-ghost">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
              </svg>
            </label>
          </div>
          <div class="flex-1 px-2 mx-2 flex items-center gap-3">
            <img src="/Logo-Klinik.svg" alt="Esthetician Clinique" class="h-6 w-6" />
            <span class="font-serif font-bold text-clinic-green">{{ $page.props.appName || 'Esthetician Clinique'
            }}</span>
          </div>
        </div>

        <!-- Main Content -->
        <main class="flex-1 p-8 bg-primary-bg">
          <slot />
        </main>
      </div>

      <!-- Sidebar -->
      <div class="drawer-side">
        <label for="drawer-toggle" class="drawer-overlay"></label>
        <aside class="w-60 min-h-full bg-primary-bg shadow-lg border-r border-gray-400" :class="{ 'hide-scrollbar': canAccess(['pasien']) }">
          <!-- Logo -->
          <div class="p-4 border-b border-gray-400">
            <div class="flex items-center gap-3">
              <img src="/Logo-Klinik.svg" alt="Esthetician Clinique" class="h-8 w-8" />
              <h2 class="text-xl font-serif font-normal text-clinic-green">Esthetician Clinique</h2>
            </div>
          </div>

          <!-- User Info (Desktop) -->
          <div class="hidden lg:block p-4 border-b border-gray-400">
            <div class="flex items-center space-x-3">
              <div class="avatar">
                <div class="w-10 rounded-full bg-primary text-primary-content flex items-center justify-center">
                  {{ user?.name?.charAt(0).toUpperCase() }}
                </div>
              </div>
              <div class="flex-1">
                <div class="font-medium">{{ user?.name }}</div>
                <div class="text-sm text-base-content/60">{{ getRoleLabel(user?.role) }}</div>
              </div>
            </div>
          </div>

          <!-- Navigation Menu -->
          <ul class="menu w-full p-4">
            <!-- Dashboard -->
            <li class="pb-2">
              <Link href="/dashboard" :class="{ 'active bg-primary text-primary-content': isActiveLink('/dashboard') }">
              <PhHouseSimple :size="24" :color="isActiveLink('/dashboard') ? 'white' : 'black'" weight="regular" />
              Dashboard
              </Link>
            </li>

            <!-- Menu berdasarkan role -->
            <template v-if="canAccess(['admin'])">
              <li class="menu-title">
                <span>Admin</span>
              </li>
              <li>
                <Link href="/users" :class="{ 'active bg-primary text-primary-content': isActiveLink('/users') }">
                <PhUserGear :size="24" :color="isActiveLink('/users') ? 'white' : 'black'" weight="regular" />
                Manajemen Pengguna
                </Link>
              </li>
              <li class="pb-2">
                <Link href="/reports"
                  :class="{ 'active bg-primary text-primary-content': isActiveLink('/reports') && !isActiveLink('/reports/stock') }">
                <PhPresentationChart :size="24" :color="isActiveLink('/reports') ? 'white' : 'black'"
                  weight="regular" />
                Laporan & Analisis
                </Link>
              </li>
            </template>

            <template v-if="canAccess(['admin', 'dokter', 'resepsionis'])">
              <li class="menu-title">
                <span>Modul Data Pasien</span>
              </li>
              <li class="pb-2">
                <Link href="/patients" :class="{ 'active bg-primary text-primary-content': isActiveLink('/patients') }">
                <PhUsers :size="24" :color="isActiveLink('/patients') ? 'white' : 'black'" weight="regular" />
                Data Pasien
                </Link>
              </li>
            </template>

            <template v-if="canAccess(['resepsionis', 'admin'])">
              <li class="menu-title">
                <span>Modul Resepsionis</span>
              </li>
              <li>
                <Link href="/all-schedule"
                  :class="{ 'active bg-primary text-primary-content': isActiveLink('/all-schedule') }">
                <PhClock :size="24" :color="isActiveLink('/all-schedule') ? 'white' : 'black'" weight="regular" />
                Jadwal Reservasi
                </Link>
              </li>
              <li>
                <Link href="/reservations"
                  :class="{ 'active bg-primary text-primary-content': isActiveLink('/reservations') }">
                <PhCalendarDots :size="24" :color="isActiveLink('/reservations') ? 'white' : 'black'"
                  weight="regular" />
                Daftar Reservasi
                </Link>
              </li>
              <li class="pb-2">
                <Link href="/transactions"
                  :class="{ 'active bg-primary text-primary-content': isActiveLink('/transactions') }">
                <PhCurrencyCircleDollar :size="24" :color="isActiveLink('/transactions') ? 'white' : 'black'"
                  weight="regular" />
                Transaksi
                </Link>
              </li>
            </template>

            <template v-if="canAccess(['dokter', 'admin'])">
              <li class="menu-title">
                <span>Modul Dokter/Terapis</span>
              </li>
              <li v-if="!canAccess(['admin'])">
                <Link href="/reservation-schedule"
                  :class="{ 'active bg-primary text-primary-content': isActiveLink('/reservation-schedule') }">
                <PhClock :size="24" :color="isActiveLink('/all-schedule') ? 'white' : 'black'" weight="regular" />
                Jadwal Reservasi
                </Link>
              </li>
              <li v-if="!canAccess(['admin'])">
                <Link href="/reservation-history"
                  :class="{ 'active bg-primary text-primary-content': isActiveLink('/reservation-history') }">
                <PhClockCounterClockwise :size="24" :color="isActiveLink('/reservation-history') ? 'white' : 'black'" weight="regular" />
                Riwayat Reservasi
                </Link>
              </li>
              <li class="pb-2">
                <Link :href="route('medical-records.index')"
                  :class="{ 'active bg-primary text-primary-content': isActiveRoutePattern(['medical-records']) }">
                <PhFileText :size="24" :color="isActiveRoutePattern(['medical-records']) ? 'white' : 'black'" weight="regular" />
                Daftar Rekam Medis
                </Link>
              </li>
            </template>

            <!-- <template v-if="canAccess(['pasien'])">
              <li class="menu-title">
                <span>Pasien</span>
              </li>
              <li>
                <Link href="/my-reservations"
                  :class="{ 'active bg-primary text-primary-content': isActiveLink('/my-reservations') }">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Reservasi Saya
                </Link>
              </li>
              <li>
                <Link href="/my-history"
                  :class="{ 'active bg-primary text-primary-content': isActiveLink('/my-history') }">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Riwayat Kunjungan
                </Link>
              </li>
              <li>
                <Link href="/services-catalog"
                  :class="{ 'active bg-primary text-primary-content': isActiveLink('/services-catalog') }">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                  </path>
                </svg>
                Katalog Layanan
                </Link>
              </li>
              <li class="pb-2">
                <Link href="/products-catalog"
                  :class="{ 'active bg-primary text-primary-content': isActiveLink('/products-catalog') }">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Katalog Produk
                </Link>
              </li>
            </template> -->

            <template v-if="canAccess(['manajer_stok', 'admin'])">
              <li class="menu-title">
                <span>Modul Manajemen Stok</span>
              </li>
              <li>
                <Link href="/products" :class="{ 'active bg-primary text-primary-content': isActiveLink('/products') }">
                <PhCube :size="24" :color="isActiveLink('/products') ? 'white' : 'black'" weight="regular" />
                Produk
                </Link>
              </li>
              <li>
                <Link href="/services" :class="{ 'active bg-primary text-primary-content': isActiveLink('/services') }">
                <PhFlask :size="24" :color="isActiveLink('/services') ? 'white' : 'black'" weight="regular" />
                Layanan
                </Link>
              </li>
              <li class="pb-2">
                <Link href="/reports/stock"
                  :class="{ 'active bg-primary text-primary-content': isActiveLink('/reports/stock') }">
                <PhChartLine :size="24" :color="isActiveLink('/reports/stock') ? 'white' : 'black'" weight="regular" />
                Laporan Stok
                </Link>
              </li>
            </template>

            <!-- Logout -->
            <li class="pt-8">
              <a href="#" @click.prevent="logout" class="text-error">
                <PhSignOut :size="24" color="currentColor" weight="regular" />
                Logout
              </a>
            </li>
          </ul>
        </aside>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { PhHouseSimple, PhUserGear, PhPresentationChart, PhUsers, PhClock, PhClockCounterClockwise, PhCalendarDots, PhCurrencyCircleDollar, PhFileText, PhCube, PhFlask, PhChartLine, PhSignOut } from '@phosphor-icons/vue'

const { props } = usePage()
const user = computed(() => props.auth?.user)

// Safe user data with fallbacks
const safeUserName = computed(() => {
  const userName = user.value?.name
  return userName && userName.length > 0 ? userName : 'User'
})

const safeUserInitial = computed(() => {
  return safeUserName.value.charAt(0).toUpperCase()
})

const isActiveRoute = (routeName) => {
  return route().current(routeName)
}

// Enhanced active route checking for more specific matching
const isActiveLink = (href) => {
  const currentUrl = window.location.pathname

  // For exact matches
  if (href === currentUrl) return true

  // For nested routes (e.g., /users/create should highlight /users)
  if (currentUrl.startsWith(href) && href !== '/') {
    return true
  }

  return false
}

// Check if current route matches any of the provided patterns
const isActiveRoutePattern = (patterns) => {
  const currentRoute = route().current()
  return patterns.some(pattern => {
    if (typeof pattern === 'string') {
      return currentRoute === pattern || currentRoute?.startsWith(pattern)
    }
    return false
  })
}

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
  return labels[role] || role || 'User'
}

const logout = () => {
  router.post('/logout', {}, {
    onSuccess: () => {
      window.location.href = '/login';
    },
    onError: () => {
      window.location.href = '/login';
    }
  });
}

</script>

<style scoped>
/* Hide scrollbar for admin role while keeping scroll functionality */
.hide-scrollbar {
  scrollbar-width: none;
  /* Firefox */
  -ms-overflow-style: none;
  /* Internet Explorer 10+ */
}

.hide-scrollbar::-webkit-scrollbar {
  display: none;
  /* WebKit */
}

.hide-scrollbar * {
  scrollbar-width: none;
  /* Firefox */
  -ms-overflow-style: none;
  /* Internet Explorer 10+ */
}

.hide-scrollbar *::-webkit-scrollbar {
  display: none;
  /* WebKit */
}
</style>
