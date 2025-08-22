<template>
  <AppLayout title="Detail Pengguna">
    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold">Detail Pengguna</h2>
              <div class="flex space-x-3">
                <Link :href="route('users.edit', user.id)" class="btn btn-warning">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                  Edit
                </Link>
                <Link :href="route('users.index')" class="btn btn-outline">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                  </svg>
                  Kembali
                </Link>
              </div>
            </div>

            <!-- User Info Card -->
            <div class="card bg-base-100 shadow-md mb-6">
              <div class="card-body">
                <div class="flex items-start space-x-4">
                  <div class="avatar placeholder">
                    <div class="bg-neutral text-neutral-content rounded-full w-16">
                      <span class="text-xl">{{ user.name.charAt(0).toUpperCase() }}</span>
                    </div>
                  </div>
                  
                  <div class="flex-1">
                    <h3 class="text-xl font-semibold">{{ user.name }}</h3>
                    <p class="text-gray-600">{{ user.email }}</p>
                    <div class="mt-2">
                      <div 
                        class="badge badge-lg"
                        :class="getRoleClass(user.role)"
                      >
                        {{ getRoleLabel(user.role) }}
                      </div>
                    </div>
                  </div>
                  
                  <div class="text-right">
                    <div class="text-sm text-gray-500">Bergabung</div>
                    <div class="font-medium">{{ formatDate(user.created_at) }}</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Patient Info (if role is pasien) -->
            <div v-if="user.role === 'pasien' && user.patient" class="card bg-base-100 shadow-md">
              <div class="card-body">
                <div class="flex justify-between items-center mb-4">
                  <h3 class="card-title">Informasi Pasien</h3>
                  <div v-if="user.is_patient_without_account" class="badge badge-info">
                    Belum Memiliki Akun
                  </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <div class="text-sm text-gray-500">Nama Lengkap</div>
                    <div class="font-medium">{{ user.patient.nama_lengkap }}</div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Tanggal Lahir</div>
                    <div class="font-medium">{{ formatDate(user.patient.tanggal_lahir) }}</div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Jenis Kelamin</div>
                    <div class="font-medium">{{ user.patient.jenis_kelamin }}</div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Nomor Telepon</div>
                    <div class="font-medium">{{ user.patient.nomor_telepon }}</div>
                  </div>
                  <div class="md:col-span-2">
                    <div class="text-sm text-gray-500">Alamat</div>
                    <div class="font-medium">{{ user.patient.alamat }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  components: {
    Head,
    Link,
    AppLayout,
  },
  props: {
    user: Object,
  },
  setup() {
    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    const getRoleLabel = (role) => {
      const labels = {
        'admin': 'Administrator',
        'resepsionis': 'Resepsionis',
        'dokter': 'Dokter/Terapis',
        'manajer_stok': 'Manajer Stok',
        'pasien': 'Pasien'
      }
      return labels[role] || role
    }

    const getRoleClass = (role) => {
      const classes = {
        'admin': 'badge-error',
        'resepsionis': 'badge-warning',
        'dokter': 'badge-info',
        'manajer_stok': 'badge-success',
        'pasien': 'badge-neutral'
      }
      return classes[role] || 'badge-neutral'
    }

    return {
      formatDate,
      getRoleLabel,
      getRoleClass,
    }
  }
}
</script>
