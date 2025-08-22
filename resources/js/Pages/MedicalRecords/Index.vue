<template>
  <AppLayout>

    <Head title="Daftar Rekam Medis" />
    <div class="space-y-6">
      <!-- Header -->
      <div>
        <h1 class="text-3xl font-semibold">Daftar Rekam Medis</h1>
        <p class="text-gray-600">Kelola rekam medis pasien</p>
      </div>

      <!-- Filters -->
      <div class="card bg-white shadow-xl">
        <div class="card-body">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="form-control">
              <label class="label">
                <span class="label-text">Pencarian</span>
              </label>
              <input type="text" v-model="filters.search" placeholder="Nama pasien..." class="input input-bordered"
                @input="filterRecords" />
            </div>

            <!-- Date Filters -->
            <div class="form-control">
              <label class="label">
                <span class="label-text">Tanggal Dari</span>
              </label>
              <input type="date" v-model="filters.date_from" class="input input-bordered" @change="filterRecords" />
            </div>

            <div class="form-control">
              <label class="label">
                <span class="label-text">Tanggal Sampai</span>
              </label>
              <input type="date" v-model="filters.date_to" class="input input-bordered" @change="filterRecords" />
            </div>

            <!-- Reset Filters -->
            <ResetFilterButton @reset="clearFilters" />
          </div>
        </div>
      </div>

      <!-- Medical Records Table -->
      <div class="card bg-white shadow-xl">
        <div class="card-body">
          <div class="overflow-x-auto">
            <table class="table table-zebra">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Pasien</th>
                  <th>Tanggal Pemeriksaan</th>
                  <th>Diagnosa</th>
                  <th>Dokter</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(record, index) in medicalRecords.data" :key="record.id" @click="goToDetail(record.id)"
                  class="cursor-pointer hover:bg-base-300 transition-colors">
                  <td>{{ getRowNumber(index) }}</td>
                  <td>
                    <div class="font-semibold">{{ record.patient.nama_lengkap }}</div>
                    <div class="text-sm text-gray-600">{{ record.patient.nomor_telepon }}</div>
                  </td>
                  <td>{{ formatDate(record.tanggal_pemeriksaan) }}</td>
                  <td>
                    <div class="text-sm max-w-xs truncate" :title="record.diagnosa">
                      {{ record.diagnosa }}
                    </div>
                  </td>
                  <td class="text-sm">{{ record.user.name }}</td>
                  <td @click.stop>
                    <div class="dropdown dropdown-left">
                      <label tabindex="0" class="btn btn-circle btn-ghost btn-sm">
                        <PhDotsThreeVertical :size="20" color="black" weight="bold" />
                      </label>
                      <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-white rounded-box w-40">
                        <li>
                          <Link :href="route('medical-records.show', record.id)">
                          <PhEye :size="16" color="black" weight="regular" />
                          Detail
                          </Link>
                        </li>
                        <li v-if="canEdit(record)">
                          <Link :href="route('medical-records.edit', record.id)" @click.stop>
                          <PhNotePencil :size="16" color="black" weight="regular" />
                          Edit
                          </Link>
                        </li>
                        <li v-if="canDelete(record)">
                          <a href="#" @click.prevent.stop="confirmDelete(record)" class="text-error">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                              </path>
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
          <div v-if="medicalRecords.data.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
              </path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada rekam medis</h3>
            <p class="mt-1 text-sm text-gray-500">Mulai dengan membuat rekam medis baru.</p>
          </div>

          <!-- Pagination -->
          <div class="flex justify-center mt-6" v-if="medicalRecords.data && medicalRecords.data.length > 0">
            <div class="btn-group">
              <Link v-for="(link, index) in medicalRecords.links" :key="index" :href="link.url || '#'" class="btn"
                :class="{ 'btn-active': link.active, 'btn-disabled': !link.url }" v-html="link.label">
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
        <p class="py-4">Apakah Anda yakin ingin menghapus rekam medis ini? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="modal-action">
          <button @click="showDeleteModal = false" class="btn">Batal</button>
          <button @click="deleteRecord" class="btn btn-error">Hapus</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import { router, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import ResetFilterButton from '~/Components/ResetFilterButton.vue'
import { PhNotePencil, PhTrashSimple, PhDotsThreeVertical, PhEye } from '@phosphor-icons/vue'

export default {
  components: {
    Head,
    Link,
    AppLayout,
    ResetFilterButton,
  },
  props: {
    medicalRecords: Object,
    patients: Array,
    filters: Object,
  },
  setup(props) {
    const page = usePage()

    const filters = ref({
      search: props.filters?.search || '',
      patient_id: props.filters?.patient_id || '',
      date_from: props.filters?.date_from || '',
      date_to: props.filters?.date_to || '',
    })

    const showDeleteModal = ref(false)
    const recordToDelete = ref(null)

    const filterRecords = () => {
      router.get(route('medical-records.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
      })
    }

    const clearFilters = () => {
      filters.value = {
        search: '',
        patient_id: '',
        date_from: '',
        date_to: '',
      }
      filterRecords()
    }

    const confirmDelete = (record) => {
      recordToDelete.value = record
      showDeleteModal.value = true
    }

    const deleteRecord = () => {
      router.delete(route('medical-records.destroy', recordToDelete.value.id), {
        onSuccess: () => {
          showDeleteModal.value = false
          recordToDelete.value = null
        }
      })
    }

    const canEdit = (record) => {
      const user = page.props.auth.user
      return user.role === 'admin' || (user.role === 'dokter' && record.user_id === user.id)
    }

    const canDelete = (record) => {
      const user = page.props.auth.user
      return user.role === 'admin' || (user.role === 'dokter' && record.user_id === user.id)
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    const getRowNumber = (index) => {
      const currentPage = props.medicalRecords.current_page || 1
      const perPage = props.medicalRecords.per_page || 15
      return (currentPage - 1) * perPage + index + 1
    }

    const goToDetail = (recordId) => {
      router.visit(route('medical-records.show', recordId))
    }

    return {
      filters,
      showDeleteModal,
      recordToDelete,
      filterRecords,
      clearFilters,
      confirmDelete,
      deleteRecord,
      canEdit,
      canDelete,
      formatDate,
      getRowNumber,
      goToDetail,
    }
  }
}
</script>
