<template>
  <AppLayout>
    <Head title="Daftar Reservasi" />
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold">Daftar Reservasi</h2>
              <Link 
                :href="route('reservations.create')" 
                class="btn btn-primary"
                v-if="$page.props.auth.user.role === 'admin' || $page.props.auth.user.role === 'resepsionis'"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Reservasi Baru
              </Link>
            </div>

            <!-- Filter & Search -->
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Cari Pasien</span>
                </label>
                <input 
                  type="text" 
                  v-model="filters.search" 
                  placeholder="Nama pasien..." 
                  class="input input-bordered"
                  @input="filterReservations"
                />
              </div>
              
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Status</span>
                </label>
                <select 
                  v-model="filters.status" 
                  class="select select-bordered"
                  @change="filterReservations"
                >
                  <option value="">Semua Status</option>
                  <option value="pending">Pending</option>
                  <option value="confirmed">Confirmed</option>
                  <option value="completed">Completed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>

              <div class="form-control">
                <label class="label">
                  <span class="label-text">Tanggal Dari</span>
                </label>
                <input 
                  type="date" 
                  v-model="filters.start_date" 
                  class="input input-bordered"
                  @change="filterReservations"
                />
              </div>

              <div class="form-control">
                <label class="label">
                  <span class="label-text">Tanggal Sampai</span>
                </label>
                <input 
                  type="date" 
                  v-model="filters.end_date" 
                  class="input input-bordered"
                  @change="filterReservations"
                />
              </div>

              <div class="form-control">
                <label class="label">
                  <span class="label-text">Dokter</span>
                </label>
                <select 
                  v-model="filters.doctor" 
                  class="select select-bordered"
                  @change="filterReservations"
                >
                  <option value="">Semua Dokter</option>
                  <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">
                    {{ doctor.name }}
                  </option>
                </select>
              </div>

              <div class="form-control">
                <label class="label">
                  <span class="label-text">Reset</span>
                </label>
                <button @click="clearFilters" class="btn btn-outline">
                  Reset Filter
                </button>
              </div>
            </div>

            <!-- Reservations Table -->
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Pasien</th>
                    <th>Layanan</th>
                    <th>Dokter</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr 
                    v-for="reservation in filteredReservations" 
                    :key="reservation.id"
                    @click="goToShow(reservation.id)"
                    class="cursor-pointer hover:bg-gray-50 transition-colors"
                  >
                    <td>{{ (reservations.current_page - 1) * reservations.per_page + filteredReservations.indexOf(reservation) + 1 }}</td>
                    <td>
                      <div class="font-medium">{{ reservation.patient.nama_lengkap }}</div>
                      <div class="text-sm text-gray-500">{{ reservation.patient.nomor_telepon }}</div>
                    </td>
                    <td>
                      <div class="font-medium">{{ reservation.service.nama_layanan }}</div>
                      <div class="text-sm text-gray-500">{{ formatCurrency(reservation.service.harga) }}</div>
                    </td>
                    <td>{{ reservation.user.name }}</td>
                    <td>{{ formatDate(reservation.tanggal_reservasi) }}</td>
                    <td>{{ formatTime(reservation.waktu_mulai) }} - {{ formatTime(reservation.waktu_selesai) }}</td>
                    <td @click.stop>
                      <div v-if="$page.props.auth.user.role === 'admin' || $page.props.auth.user.role === 'resepsionis'" class="dropdown dropdown-bottom">
                        <label tabindex="0" class="badge cursor-pointer" :class="getStatusClass(reservation.status)">
                          {{ getStatusText(reservation.status) }}
                          <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                          </svg>
                        </label>
                        <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-48 z-10">
                          <li v-if="reservation.status !== 'confirmed'">
                            <a @click="confirmStatusChange(reservation, 'confirmed')">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                              </svg>
                              Konfirmasi
                            </a>
                          </li>
                          <li v-if="reservation.status !== 'completed'">
                            <a @click="confirmStatusChange(reservation, 'completed')">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                              </svg>
                              Selesai
                            </a>
                          </li>
                          <li v-if="reservation.status !== 'cancelled'">
                            <a @click="confirmStatusChange(reservation, 'cancelled')">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                              </svg>
                              Batalkan
                            </a>
                          </li>
                        </ul>
                      </div>
                      <div v-else class="badge" :class="getStatusClass(reservation.status)">
                        {{ getStatusText(reservation.status) }}
                      </div>
                    </td>
                    <td @click.stop>
                      <div class="dropdown dropdown-left">
                        <label tabindex="0" class="btn btn-sm btn-circle btn-ghost">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                          </svg>
                        </label>
                        <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
                          <li>
                            <Link :href="route('reservations.show', reservation.id)">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                              </svg>
                              Detail
                            </Link>
                          </li>
                          <li v-if="$page.props.auth.user.role === 'admin' || $page.props.auth.user.role === 'resepsionis'">
                            <Link :href="route('reservations.edit', reservation.id)">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                              </svg>
                              Edit
                            </Link>
                          </li>
                          <li v-if="$page.props.auth.user.role === 'admin' || $page.props.auth.user.role === 'resepsionis'">
                            <a @click="confirmDelete(reservation)">
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
            <div v-if="filteredReservations.length === 0" class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada reservasi</h3>
              <p class="mt-1 text-sm text-gray-500">Mulai dengan membuat reservasi baru.</p>
              <div class="mt-6">
                <Link 
                  :href="route('reservations.create')" 
                  class="btn btn-primary"
                  v-if="$page.props.auth.user.role === 'admin' || $page.props.auth.user.role === 'resepsionis'"
                >
                  Reservasi Baru
                </Link>
              </div>
            </div>

            <!-- Pagination -->
            <div class="flex justify-center mt-6" v-if="reservations.data && reservations.data.length > 0">
              <div class="btn-group">
                <template v-for="link in reservations.links" :key="link.label">
                  <Link 
                    v-if="link.url"
                    :href="link.url"
                    class="btn"
                    :class="{ 'btn-active': link.active }"
                    v-html="link.label"
                  ></Link>
                  <span 
                    v-else
                    class="btn btn-disabled"
                    v-html="link.label"
                  ></span>
                </template>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg">Konfirmasi Hapus</h3>
        <p class="py-4">Apakah Anda yakin ingin menghapus reservasi ini? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="modal-action">
          <button @click="showDeleteModal = false" class="btn">Batal</button>
          <button @click="deleteReservation" class="btn btn-error">Hapus</button>
        </div>
      </div>
    </div>

    <!-- Status Change Confirmation Modal -->
    <div v-if="showStatusModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">
          <svg class="w-6 h-6 inline-block mr-2 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          Konfirmasi Perubahan Status
        </h3>
        <p class="py-4 text-base-content/80">
          Apakah Anda yakin ingin mengubah status reservasi dari 
          <span class="font-semibold">{{ getStatusText(statusToChange?.currentStatus) }}</span> 
          menjadi 
          <span class="font-semibold">{{ getStatusText(statusToChange?.newStatus) }}</span>?
        </p>
        <div class="modal-action">
          <button @click="showStatusModal = false" class="btn btn-ghost">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            Batal
          </button>
          <button @click="updateStatus" class="btn btn-primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Ya, Ubah Status
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import AppLayout from '~/Layouts/AppLayout.vue'

export default {
  components: {
    Head,
    Link,
    AppLayout,
  },
  props: {
    reservations: Object,
    doctors: Array,
    flash: Object,
  },
  setup(props) {
    const filters = ref({
      search: '',
      status: '',
      start_date: '',
      end_date: '',
      doctor: '',
    })

    const showDeleteModal = ref(false)
    const reservationToDelete = ref(null)
    
    const showStatusModal = ref(false)
    const statusToChange = ref(null)

    const filteredReservations = computed(() => {
      if (!props.reservations.data) return []
      
      return props.reservations.data.filter(reservation => {
        const matchesSearch = !filters.value.search || 
          reservation.patient.nama_lengkap.toLowerCase().includes(filters.value.search.toLowerCase())
        
        const matchesStatus = !filters.value.status || 
          reservation.status === filters.value.status
        
        const reservationDate = new Date(reservation.tanggal_reservasi)
        
        const matchesStartDate = !filters.value.start_date || 
          reservationDate >= new Date(filters.value.start_date)
        
        const matchesEndDate = !filters.value.end_date || 
          reservationDate <= new Date(filters.value.end_date)
        
        const matchesDoctor = !filters.value.doctor || 
          reservation.user_id == filters.value.doctor

        return matchesSearch && matchesStatus && matchesStartDate && matchesEndDate && matchesDoctor
      })
    })

    const filterReservations = () => {
      router.get(route('reservations.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
      })
    }

    const clearFilters = () => {
      filters.value = {
        search: '',
        status: '',
        start_date: '',
        end_date: '',
        doctor: '',
      }
      filterReservations()
    }

    const confirmDelete = (reservation) => {
      reservationToDelete.value = reservation
      showDeleteModal.value = true
    }

    const deleteReservation = () => {
      router.delete(route('reservations.destroy', reservationToDelete.value.id), {
        onSuccess: () => {
          showDeleteModal.value = false
          reservationToDelete.value = null
        }
      })
    }

    const confirmStatusChange = (reservation, newStatus) => {
      statusToChange.value = {
        reservation: reservation,
        currentStatus: reservation.status,
        newStatus: newStatus
      }
      showStatusModal.value = true
    }

    const updateStatus = () => {
      if (!statusToChange.value) return
      
      router.patch(route('reservations.update', statusToChange.value.reservation.id), { 
        status: statusToChange.value.newStatus 
      }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
          showStatusModal.value = false
          statusToChange.value = null
        }
      })
    }

    const goToShow = (id) => {
      router.visit(route('reservations.show', id))
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    const formatTime = (time) => {
      return new Date(`2000-01-01 ${time}`).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
      }).format(amount)
    }

    const getStatusClass = (status) => {
      const classes = {
        pending: 'badge-warning',
        confirmed: 'badge-info',
        completed: 'badge-success',
        cancelled: 'badge-error'
      }
      return classes[status] || 'badge-ghost'
    }

    const getStatusText = (status) => {
      const texts = {
        pending: 'Pending',
        confirmed: 'Dikonfirmasi',
        completed: 'Selesai',
        cancelled: 'Dibatalkan'
      }
      return texts[status] || status
    }

    return {
      filters,
      filteredReservations,
      showDeleteModal,
      reservationToDelete,
      showStatusModal,
      statusToChange,
      filterReservations,
      clearFilters,
      confirmDelete,
      deleteReservation,
      confirmStatusChange,
      updateStatus,
      goToShow,
      formatDate,
      formatTime,
      formatCurrency,
      getStatusClass,
      getStatusText,
    }
  }
}
</script>
