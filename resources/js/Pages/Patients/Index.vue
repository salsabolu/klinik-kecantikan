<template>
  <AppLayout>
    <Head title="Data Pasien" />
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-serif font-bold text-clinic-green">Data Pasien</h1>
          <p class="text-gray-600 mt-1 font-sans">Kelola data pasien klinik</p>
        </div>
        <Link 
          href="/patients/create" 
          class="btn btn-outline btn-primary"
          v-if="canAccess(['admin', 'resepsionis'])"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Tambah Pasien
        </Link>
      </div>

      <!-- Filters -->
      <div class="card bg-white shadow-lg rounded-xl border border-gray-200">
        <div class="card-body bg-primary-bg">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="form-control">
              <label class="label">
                <span class="label-text font-sans text-clinic-green">Pencarian</span>
              </label>
              <input
                v-model="filters.search"
                type="text"
                placeholder="Nama, telepon, atau email..."
                class="input input-bordered"
                @input="search"
              />
            </div>
            
            <div class="form-control">
              <label class="label">
                <span class="label-text font-sans text-clinic-green">Jenis Kelamin</span>
              </label>
              <select
                v-model="filters.jenis_kelamin"
                class="select select-bordered"
                @change="search"
              >
                <option value="">Semua</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
              </select>
            </div>

            <ResetFilterButton @reset="clearFilters" />
          </div>
        </div>
      </div>

      <!-- Patients Table -->
      <div class="card bg-white shadow-lg rounded-xl border border-gray-200">
        <div class="card-body bg-primary-bg">
          <div class="overflow-x-auto">
            <table class="table table-zebra">
              <thead>
                <tr>
                  <th class="font-serif text-clinic-green">No</th>
                  <th class="font-serif text-clinic-green">Nama Lengkap</th>
                  <th class="font-serif text-clinic-green">Jenis Kelamin</th>
                  <th class="font-serif text-clinic-green">Tanggal Lahir</th>
                  <th class="font-serif text-clinic-green">Nomor Telepon</th>
                  <th class="font-serif text-clinic-green">Email</th>
                  <th class="font-serif text-clinic-green">Status Akun</th>
                  <th class="font-serif text-clinic-green">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr 
                  v-for="(patient, index) in patients.data" 
                  :key="patient.id" 
                  class="hover cursor-pointer transition-colors" 
                  @click="navigateToPatient(patient.id)"
                >
                  <td>{{ getRowNumber(index) }}</td>
                  <td class="font-semibold">{{ patient.nama_lengkap }}</td>
                  <td>
                    <span :class="patient.jenis_kelamin === 'Laki-laki' ? 'badge-info' : 'badge-secondary'" class="badge">
                      {{ patient.jenis_kelamin }}
                    </span>
                  </td>
                  <td>{{ formatDate(patient.tanggal_lahir) }}</td>
                  <td>{{ patient.nomor_telepon }}</td>
                  <td>{{ patient.email || '-' }}</td>
                  <td>
                    <span v-if="patient.user" class="badge badge-success">
                      Ada Akun
                    </span>
                    <span v-else class="badge badge-warning">
                      Tidak Ada Akun
                    </span>
                  </td>
                  <td @click.stop>
                    <div class="dropdown dropdown-end">
                      <label tabindex="0" class="btn btn-ghost btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                        </svg>
                      </label>
                      <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
                        <li>
                          <Link :href="`/patients/${patient.id}`">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.543 7-1.275 4.057-5.065 7-9.543 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Detail
                          </Link>
                        </li>
                        <li v-if="canAccess(['admin', 'resepsionis'])">
                          <Link :href="`/patients/${patient.id}/edit`">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                          </Link>
                        </li>
                        <li v-if="canAccess(['admin', 'resepsionis'])">
                          <a @click="deletePatient(patient)" class="text-error">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus
                          </a>
                        </li>
                      </ul>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Empty State -->
          <div v-if="patients.data.length === 0" class="text-center py-8">
            <svg class="w-16 h-16 mx-auto text-base-content/30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <p class="text-base-content/60">Belum ada data pasien</p>
          </div>

          <!-- Pagination -->
          <div v-if="patients.last_page > 1" class="flex justify-center mt-6">
            <div class="btn-group">
              <Link
                v-for="page in paginationPages"
                :key="page"
                :href="patients.path"
                :data="{ ...filters, page }"
                :class="['btn', page === patients.current_page ? 'btn-active' : '']"
                preserve-scroll
              >
                {{ page }}
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg">Konfirmasi Hapus</h3>
        <p class="py-4">
          Apakah Anda yakin ingin menghapus data pasien <strong>{{ patientToDelete?.nama_lengkap }}</strong>?
          Data yang sudah dihapus tidak dapat dikembalikan.
        </p>
        <div class="modal-action">
          <button @click="showDeleteModal = false" class="btn">Batal</button>
          <button @click="confirmDelete" class="btn btn-error">Hapus</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, reactive } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AppLayout from '~/Layouts/AppLayout.vue'
import ResetFilterButton from '~/Components/ResetFilterButton.vue'
import { debounce } from 'lodash'

const { props } = usePage()

defineProps({
  patients: Object,
  filters: Object,
})

const filters = reactive({
  search: props.filters?.search || '',
  jenis_kelamin: props.filters?.jenis_kelamin || '',
})

const showDeleteModal = ref(false)
const patientToDelete = ref(null)

const paginationPages = computed(() => {
  const pages = []
  const current = props.patients.current_page
  const last = props.patients.last_page
  
  for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
    pages.push(i)
  }
  
  return pages
})

const search = debounce(() => {
  router.get('/patients', filters, {
    preserveState: true,
    replace: true,
  })
}, 300)

// Get row number for pagination
const getRowNumber = (index) => {
  return (props.patients.current_page - 1) * props.patients.per_page + index + 1
}

// Navigate to patient detail
const navigateToPatient = (id) => {
  router.get(`/patients/${id}`)
}

const clearFilters = () => {
  filters.search = ''
  filters.jenis_kelamin = ''
  search()
}

const deletePatient = (patient) => {
  patientToDelete.value = patient
  showDeleteModal.value = true
}

const confirmDelete = () => {
  router.delete(`/patients/${patientToDelete.value.id}`, {
    onSuccess: () => {
      showDeleteModal.value = false
      patientToDelete.value = null
    }
  })
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

// Check access permissions
const canAccess = (roles) => {
  const userRole = props.auth?.user?.role
  return roles.includes(userRole)
}
</script>
