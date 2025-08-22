<template>
  <div class="min-h-screen bg-primary-bg">
    <!-- Simple Header -->
    <div class="navbar bg-primary-bg border-b border-clinic-green px-6 lg:px-12 py-2 lg:py-4">
      <!-- Logo -->
      <div class="flex-1 flex items-center gap-3">
        <img src="/Logo-Klinik.svg" alt="Esthetician Clinique" class="h-10 w-10" />
        <h1 class="text-2xl font-serif font-normal text-clinic-green">{{ page.props?.appName || 'Esthetician Clinique' }}</h1>
      </div>
      <div class="flex-none">
        <div class="dropdown dropdown-end">
          <label tabindex="0" class="btn btn-ghost btn-circle avatar">
            <div class="w-10 rounded-full bg-clinic-yellow text-clinic-green flex items-center justify-center font-serif font-semibold">
              {{ safeUserInitial }}
            </div>
          </label>
          <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-white rounded-box w-52 border border-gray-200">
            <li><a class="text-sm font-sans">{{ safeUserName }}</a></li>
            <li><a class="text-xs text-gray-500 font-sans">{{ getRoleLabel(user?.role) }}</a></li>
            <li><hr class="my-1"></li>
            <li><a href="#" @click.prevent="logout" class="font-sans">Logout</a></li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto p-4 bg-primary-bg min-h-screen">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props?.auth?.user)

// Safe user data with fallbacks
const safeUserName = computed(() => {
  const userName = user.value?.name
  return userName && userName.length > 0 ? userName : 'User'
})

const safeUserInitial = computed(() => {
  return safeUserName.value.charAt(0).toUpperCase()
})

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
