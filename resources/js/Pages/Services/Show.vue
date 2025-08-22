<template>
  <Head title="Detail Layanan" />
  <component :is="layoutComponent">
    <div class="w-full mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-semibold">{{ service.nama_layanan }}</h1>
          <p class="text-gray-600">Detail informasi layanan</p>
        </div>
        <div class="flex">
          <Link :href="backUrl" class="btn btn-outline">
          <PhArrowLeft :size="20" color="black" weight="regular" />
          Kembali
          </Link>
        </div>
      </div>

      <!-- Content -->
      <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 lg:p-8">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Service Information -->
            <div class="space-y-6">
              <div>
                <p class="text-lg font-medium mb-4">
                  Informasi Layanan
                </p>

                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-600">
                      Nama Layanan
                    </label>
                    <p class="mt-1 text-sm">
                      {{ service.nama_layanan }}
                    </p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-600">
                      Deskripsi
                    </label>
                    <p class="mt-1 text-sm">
                      {{ service.deskripsi || 'Tidak ada deskripsi' }}
                    </p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-600">
                      Harga
                    </label>
                    <p class="mt-1 text-lg font-semibold">
                      Rp {{ formatNumber(service.harga) }}
                    </p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-600">
                      Durasi
                    </label>
                    <div class="mt-1">
                      <span class="badge badge-secondary">
                        {{ service.durasi_menit }} Menit
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Additional Information -->
            <div v-if="!canAccess(['pasien'])" class="space-y-6">
              <div>
                <p class="text-lg font-medium mb-4">
                  Informasi Sistem
                </p>

                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-600">
                      Dibuat Pada
                    </label>
                    <p class="mt-1 text-sm">
                      {{ formatDate(service.created_at) }}
                    </p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-600">
                      Diperbarui Pada
                    </label>
                    <p class="mt-1 text-sm">
                      {{ formatDate(service.updated_at) }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end">
            <Link v-if="canAccess(['admin', 'manajer_stok'])" :href="route('services.edit', service.id)"
              class="btn btn-outline btn-primary">
            <PhNotePencil :size="20" color="black" weight="regular" />
            Edit Layanan
            </Link>
          </div>
        </div>
      </div>
    </div>
  </component>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PasienLayout from '@/Layouts/PasienLayout.vue'
import { PhArrowLeft, PhNotePencil } from '@phosphor-icons/vue'

const props = defineProps({
  service: Object,
  auth: Object,
})

// Get user from page props
const { props: pageProps } = usePage()
const user = computed(() => pageProps.auth?.user)

// Choose layout and back URL based on user role
const layoutComponent = computed(() => {
  return user.value?.role === 'pasien' ? PasienLayout : AppLayout
})

const backUrl = computed(() => {
  return user.value?.role === 'pasien' ? '/services-catalog' : route('services.index')
})

const canAccess = (roles) => {
  if (!props.auth?.user?.role) return false
  return roles.includes(props.auth.user.role)
}

const formatNumber = (number) => {
  return new Intl.NumberFormat('id-ID').format(number)
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>
