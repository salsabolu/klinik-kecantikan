<template>
  <AppLayout>
    <Head title="Manajemen Pengguna" />
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold">Manajemen Pengguna</h2>
              <Link 
                :href="route('users.create')" 
                class="btn btn-primary"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Pengguna Baru
              </Link>
            </div>

            <!-- Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
              <div class="stat bg-base-200 rounded-lg">
                <div class="stat-title">Total Admin</div>
                <div class="stat-value text-xl">{{ statistics.admin }}</div>
              </div>
              <div class="stat bg-base-200 rounded-lg">
                <div class="stat-title">Total Resepsionis</div>
                <div class="stat-value text-xl">{{ statistics.resepsionis }}</div>
              </div>
              <div class="stat bg-base-200 rounded-lg">
                <div class="stat-title">Total Dokter</div>
                <div class="stat-value text-xl">{{ statistics.dokter }}</div>
              </div>
              <div class="stat bg-base-200 rounded-lg">
                <div class="stat-title">Total Manajer Stok</div>
                <div class="stat-value text-xl">{{ statistics.manajer_stok }}</div>
              </div>
              <div class="stat bg-base-200 rounded-lg">
                <div class="stat-title">Total Pasien</div>
                <div class="stat-value text-xl">{{ statistics.pasien }}</div>
              </div>
            </div>

            <!-- Filter & Search -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Cari Pengguna</span>
                </label>
                <input 
                  v-model="filters.search"
                  type="text" 
                  placeholder="Nama atau email..." 
                  class="input input-bordered"
                  @keyup.enter="applyFilters"
                />
              </div>

              <div class="form-control">
                <label class="label">
                  <span class="label-text">Filter Role</span>
                </label>
                <select 
                  v-model="filters.role"
                  class="select select-bordered"
                  @change="applyFilters"
                >
                  <option value="">Semua Role</option>
                  <option value="admin">Administrator</option>
                  <option value="resepsionis">Resepsionis</option>
                  <option value="dokter">Dokter/Terapis</option>
                  <option value="manajer_stok">Manajer Stok</option>
                  <option value="pasien">Pasien</option>
                </select>
              </div>

              <div class="form-control">
                <label class="label">
                  <span class="label-text">&nbsp;</span>
                </label>
                <button @click="clearFilters" class="btn btn-outline">
                  Reset Filter
                </button>
              </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr 
                    v-for="(user, index) in users.data" 
                    :key="user.id" 
                    class="hover cursor-pointer transition-colors"
                    @click="navigateToUser(user.id)"
                  >
                    <td>{{ getRowNumber(index) }}</td>
                    <td>
                      <div>
                        <div class="font-medium">{{ user.name }}</div>
                        <div v-if="user.is_patient_without_account" class="text-xs text-gray-500">
                          Belum memiliki akun
                        </div>
                      </div>
                    </td>
                    <td>{{ user.email }}</td>
                    <td>
                      <div 
                        class="badge badge-lg"
                        :class="getRoleClass(user.role)"
                      >
                        {{ getRoleLabel(user.role) }}
                      </div>
                    </td>
                    <td>{{ formatDate(user.created_at) }}</td>
                    <td @click.stop>
                      <div class="dropdown dropdown-end">
                        <label tabindex="0" class="btn btn-ghost btn-sm">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                          </svg>
                        </label>
                        <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
                          <li>
                            <Link :href="route('users.show', user.id)" title="Lihat Detail">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                              </svg>
                              Detail
                            </Link>
                          </li>
                          <li v-if="!user.is_patient_without_account">
                            <Link :href="route('users.edit', user.id)" title="Edit">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                              </svg>
                              Edit
                            </Link>
                          </li>
                          <li v-if="canDelete(user) && !user.is_patient_without_account">
                            <a href="#" @click.prevent="confirmDelete(user)" class="text-error" title="Hapus">
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

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-6">
              <div class="text-sm text-gray-500">
                Menampilkan {{ users.from }} - {{ users.to }} dari {{ users.total }} pengguna
              </div>
              <div class="join">
                <Link 
                  v-for="(link, index) in users.links" 
                  :key="index"
                  :href="link.url || '#'" 
                  class="join-item btn btn-sm"
                  :class="{ 'btn-active': link.active, 'btn-disabled': !link.url }"
                  v-html="link.label"
                />
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
        <p class="py-4">Apakah Anda yakin ingin menghapus pengguna <strong>{{ userToDelete?.name }}</strong>? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="modal-action">
          <button @click="showDeleteModal = false" class="btn">Batal</button>
          <button @click="deleteUser" class="btn btn-error">Hapus</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import { router, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  components: {
    Head,
    Link,
    AppLayout,
  },
  props: {
    users: Object,
    filters: Object,
    statistics: Object,
  },
  setup(props) {
    const page = usePage()
    
    const filters = ref({
      search: props.filters?.search || '',
      role: props.filters?.role || '',
    })

    const showDeleteModal = ref(false)
    const userToDelete = ref(null)

    const applyFilters = () => {
      router.get(route('users.index'), filters.value, {
        preserveState: true,
        replace: true,
      })
    }

    const clearFilters = () => {
      filters.value = {
        search: '',
        role: '',
      }
      applyFilters()
    }

    // Get row number for pagination
    const getRowNumber = (index) => {
      return (props.users.current_page - 1) * props.users.per_page + index + 1
    }

    // Navigate to user detail
    const navigateToUser = (id) => {
      router.get(route('users.show', id))
    }

    const confirmDelete = (user) => {
      userToDelete.value = user
      showDeleteModal.value = true
    }

    const deleteUser = () => {
      if (userToDelete.value) {
        router.delete(route('users.destroy', userToDelete.value.id), {
          onSuccess: () => {
            showDeleteModal.value = false
            userToDelete.value = null
          }
        })
      }
    }

    const canDelete = (user) => {
      const currentUser = page.props.auth.user
      return user.id !== currentUser.id
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

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    // Computed property for safe statistics access
    const safeStatistics = computed(() => {
      return props.statistics || {
        admin: 0,
        resepsionis: 0,
        dokter: 0,
        manajer_stok: 0,
        pasien: 0
      }
    })

    return {
      filters,
      showDeleteModal,
      userToDelete,
      applyFilters,
      clearFilters,
      getRowNumber,
      navigateToUser,
      confirmDelete,
      deleteUser,
      canDelete,
      getRoleLabel,
      getRoleClass,
      formatDate,
      statistics: safeStatistics,
    }
  }
}
</script>
