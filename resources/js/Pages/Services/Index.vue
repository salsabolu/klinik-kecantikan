<template>
  <AppLayout>

    <Head title="Layanan" />
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-semibold">Daftar Layanan</h1>
          <p class="text-gray-600">Kelola layanan klinik kecantikan</p>
        </div>
        <Link v-if="canAccess(['admin', 'manajer_stok'])" href="/services/create" class="btn btn-primary">
        <PhPlus :size="20" color="black" weight="regular" />
        Tambah Layanan
        </Link>
      </div>

      <!-- Filters -->
      <div class="card bg-white shadow-xl">
        <div class="card-body">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
              <label class="label">
                <span class="label-text">Pencarian</span>
              </label>
              <div class="relative">
                <input v-model="filters.search" type="text" placeholder="Nama layanan atau deskripsi..."
                  class="input input-bordered w-full pr-10" @input="search" />
                <button v-if="filters.search" @click="clearSearch"
                  class="absolute inset-y-0 right-0 pr-3 flex items-center">
                  <PhX :size="20" color="grey" weight="regular" />
                </button>
              </div>
            </div>

            <ResetFilterButton @reset="clearFilters" />
          </div>
        </div>
      </div>

      <!-- Services Table -->
      <div class="card bg-white shadow-xl">
        <div class="card-body">
          <div class="overflow-x-auto">
            <table class="table table-zebra">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Layanan</th>
                  <th>Deskripsi</th>
                  <th>Harga</th>
                  <th>Durasi</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(service, index) in services.data" :key="service.id" @click="navigateToService(service.id)"
                  class="cursor-pointer hover:bg-base-300 transition-colors">
                  <td>{{ getRowNumber(index) }}</td>
                  <td>
                    <div class="font-semibold">{{ service.nama_layanan }}</div>
                  </td>
                  <td>
                    <div class="text-sm max-w-xs truncate">
                      {{ service.deskripsi || '-' }}
                    </div>
                  </td>
                  <td>
                    <div class="font-medium">
                      Rp {{ formatNumber(service.harga) }}
                    </div>
                  </td>
                  <td>
                    <div class="badge badge-info">
                      {{ service.durasi_menit }} menit
                    </div>
                  </td>
                  <td @click.stop>
                    <div class="dropdown dropdown-left">
                      <label tabindex="0" class="btn btn-circle btn-ghost btn-sm">
                        <PhDotsThreeVertical :size="20" color="black" weight="bold" />
                      </label>
                      <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-40">
                        <li>
                          <Link :href="`/services/${service.id}`">
                          <PhEye :size="16" color="black" weight="regular" />
                          Detail
                          </Link>
                        </li>
                        <li v-if="canAccess(['admin', 'manajer_stok'])">
                          <Link :href="`/services/${service.id}/edit`">
                          <PhNotePencil :size="16" color="black" weight="regular" />
                          Edit
                          </Link>
                        </li>
                        <li v-if="canAccess(['admin', 'manajer_stok'])">
                          <button @click="confirmDelete(service)" class="text-error">
                            <PhTrashSimple :size="16" color="currentColor" weight="regular" />
                            Hapus
                          </button>
                        </li>
                      </ul>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="services.last_page > 1" class="flex justify-center mt-8">
            <div class="btn-group">
              <Link v-for="page in paginationPages" :key="page" :href="`/services?page=${page}`" class="btn btn-sm"
                :class="{ 'btn-active': page === services.current_page }">
              {{ page }}
              </Link>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="services.data.length === 0" class="text-center py-12">
            <PhFlask :size="80" color="black" weight="regular" />
            <h3 class="text-lg font-medium text-gray-600">Tidak ada layanan</h3>
            <p class="text-base text-gray-600 mb-4">Mulai dengan menambahkan layanan baru.</p>
            <Link href="/services/create" class="btn btn-primary">
            Tambah Layanan
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="modal modal-open">
      <div class="modal-box">
        <p class="text-lg font-semibold">Konfirmasi Hapus</p>
        <p class="mt-4">
          Apakah Anda yakin ingin menghapus layanan "<strong>{{ serviceToDelete?.nama_layanan }}</strong>"?
          Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="modal-action">
          <button @click="showDeleteModal = false" class="btn btn-outline">Batal</button>
          <button @click="deleteService" class="btn btn-error">Hapus</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ResetFilterButton from '@/Components/ResetFilterButton.vue'
import { debounce } from 'lodash'
import { PhPlus, PhX, PhDotsThreeVertical, PhEye, PhNotePencil, PhTrashSimple, PhFlask } from '@phosphor-icons/vue'

// Modal states
const showDeleteModal = ref(false)
const serviceToDelete = ref(null)

// Delete confirmation
const confirmDelete = (service) => {
  serviceToDelete.value = service
  showDeleteModal.value = true
}

// Delete service
const deleteService = () => {
  if (serviceToDelete.value) {
    router.delete(`/services/${serviceToDelete.value.id}`, {
      onSuccess: () => {
        showDeleteModal.value = false
        serviceToDelete.value = null
      }
    })
  }
}

const props = defineProps({
  services: Object,
  filters: Object,
  auth: Object,
})

const canAccess = (roles) => {
  if (!props.auth?.user?.role) return false
  return roles.includes(props.auth.user.role)
}

const filters = ref({
  search: props.filters.search || '',
})

const search = debounce(() => {
  router.get('/services', {
    search: filters.value.search,
  }, {
    preserveState: true,
    replace: true,
  })
}, 300)

const clearSearch = () => {
  filters.value.search = ''
  search()
}

const clearFilters = () => {
  filters.value.search = ''
  router.get('/services', {}, {
    preserveState: true,
    replace: true,
  })
}

const formatNumber = (number) => {
  return new Intl.NumberFormat('id-ID').format(number)
}

// Get row number for pagination
const getRowNumber = (index) => {
  return (props.services.current_page - 1) * props.services.per_page + index + 1
}

// Navigate to service detail
const navigateToService = (id) => {
  router.get(`/services/${id}`)
}

const paginationPages = computed(() => {
  const pages = []
  const current = props.services.current_page
  const last = props.services.last_page

  for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
    pages.push(i)
  }

  return pages
})
</script>
