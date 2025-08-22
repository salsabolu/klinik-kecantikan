<template>
  <AppLayout>
    <Head title="Transaksi" />
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold">Daftar Transaksi</h2>
              <Link 
                :href="route('transactions.create')" 
                class="btn btn-primary"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Transaksi Baru
              </Link>
            </div>

            <!-- Filter & Search -->
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Cari Pasien</span>
                </label>
                <input 
                  v-model="filters.search"
                  type="text" 
                  placeholder="Nama atau nomor telepon..." 
                  class="input input-bordered"
                  @keyup.enter="applyFilters"
                />
              </div>

              <div class="form-control">
                <label class="label">
                  <span class="label-text">Dari Tanggal</span>
                </label>
                <input 
                  v-model="filters.date_from"
                  type="date" 
                  class="input input-bordered"
                  @change="applyFilters"
                />
              </div>

              <div class="form-control">
                <label class="label">
                  <span class="label-text">Sampai Tanggal</span>
                </label>
                <input 
                  v-model="filters.date_to"
                  type="date" 
                  class="input input-bordered"
                  @change="applyFilters"
                />
              </div>

              <div class="form-control">
                <label class="label">
                  <span class="label-text">Status Pembayaran</span>
                </label>
                <select 
                  v-model="filters.status"
                  class="select select-bordered"
                  @change="applyFilters"
                >
                  <option value="">Semua Status</option>
                  <option value="paid">Lunas</option>
                  <option value="pending">Belum Lunas</option>
                  <option value="partially_paid">Sebagian Lunas</option>
                  <option value="cancelled">Dibatalkan</option>
                </select>
              </div>

              <div class="form-control">
                <label class="label">
                  <span class="label-text">Metode Pembayaran</span>
                </label>
                <select 
                  v-model="filters.metode"
                  class="select select-bordered"
                  @change="applyFilters"
                >
                  <option value="">Semua Metode</option>
                  <option value="cash">Tunai</option>
                  <option value="debit_card">Kartu Debit</option>
                  <option value="credit_card">Kartu Kredit</option>
                  <option value="transfer">Transfer</option>
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
                    <th>Nomor</th>
                    <th>Tanggal</th>
                    <th>Pasien</th>
                    <th>Total</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th>Kasir</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr 
                    v-for="(transaction, index) in transactions.data" 
                    :key="transaction.id"
                    class="hover:bg-gray-50 cursor-pointer"
                    @click="router.visit(route('transactions.show', transaction.id))"
                  >
                    <td>{{ getTransactionNumber(index) }}</td>
                    <td>{{ formatDate(transaction.tanggal_transaksi) }}</td>
                    <td>
                      <div>
                        <div class="font-medium">{{ transaction.patient?.nama_lengkap }}</div>
                        <div class="text-sm opacity-50">{{ transaction.patient?.nomor_telepon }}</div>
                      </div>
                    </td>
                    <td>{{ formatCurrency(transaction.total_amount) }}</td>
                    <td>
                      <div class="badge badge-outline">
                        {{ formatMetode(transaction.payment_method) }}
                      </div>
                    </td>
                    <td @click.stop>
                      <div class="dropdown dropdown-end">
                        <label 
                          tabindex="0" 
                          class="badge cursor-pointer"
                          :class="getStatusClass(transaction.payment_status)"
                        >
                          {{ formatStatus(transaction.payment_status) }}
                          <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                          </svg>
                        </label>
                        <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-40 z-10">
                          <li v-if="transaction.payment_status !== 'paid'">
                            <a @click.stop="updateStatus(transaction, 'paid')" class="text-sm">
                              <span class="badge badge-success badge-sm">Lunas</span>
                            </a>
                          </li>
                          <li v-if="transaction.payment_status !== 'pending'">
                            <a @click.stop="updateStatus(transaction, 'pending')" class="text-sm">
                              <span class="badge badge-warning badge-sm">Belum Lunas</span>
                            </a>
                          </li>
                          <li v-if="transaction.payment_status !== 'partially_paid'">
                            <a @click.stop="updateStatus(transaction, 'partially_paid')" class="text-sm">
                              <span class="badge badge-info badge-sm">Sebagian Lunas</span>
                            </a>
                          </li>
                          <li v-if="transaction.payment_status !== 'cancelled'">
                            <a @click.stop="updateStatus(transaction, 'cancelled')" class="text-sm">
                              <span class="badge badge-error badge-sm">Dibatalkan</span>
                            </a>
                          </li>
                        </ul>
                      </div>
                    </td>
                    <td>{{ transaction.user?.name }}</td>
                    <td @click.stop>
                      <div class="dropdown dropdown-left">
                        <label tabindex="0" class="btn btn-sm btn-circle btn-ghost">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                          </svg>
                        </label>
                        <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
                          <li>
                            <Link :href="route('transactions.show', transaction.id)">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                              </svg>
                              Detail
                            </Link>
                          </li>
                          <li v-if="canEdit(transaction)">
                            <Link :href="route('transactions.edit', transaction.id)">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                              </svg>
                              Edit
                            </Link>
                          </li>
                          <li v-if="canDelete(transaction)">
                            <a @click="confirmDelete(transaction)">
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
                Menampilkan {{ transactions.from }} - {{ transactions.to }} dari {{ transactions.total }} transaksi
              </div>
              <div class="join">
                <Link 
                  v-for="(link, index) in transactions.links" 
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
        <p class="py-4">Apakah Anda yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="modal-action">
          <button @click="showDeleteModal = false" class="btn">Batal</button>
          <button @click="deleteTransaction" class="btn btn-error">Hapus</button>
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
    transactions: Object,
    filters: Object,
  },
  setup(props) {
    const filters = ref({
      search: props.filters?.search || '',
      date_from: props.filters?.date_from || '',
      date_to: props.filters?.date_to || '',
      status: props.filters?.status || '',
      metode: props.filters?.metode || '',
    })

    const showDeleteModal = ref(false)
    const transactionToDelete = ref(null)

    const applyFilters = () => {
      router.get(route('transactions.index'), filters.value, {
        preserveState: true,
        replace: true,
      })
    }

    const clearFilters = () => {
      filters.value = {
        search: '',
        date_from: '',
        date_to: '',
        status: '',
        metode: '',
      }
      applyFilters()
    }

    const getTransactionNumber = (index) => {
      const currentPage = props.transactions.current_page || 1
      const perPage = props.transactions.per_page || 10
      return (currentPage - 1) * perPage + index + 1
    }

    const confirmDelete = (transaction) => {
      transactionToDelete.value = transaction
      showDeleteModal.value = true
    }

    const deleteTransaction = () => {
      if (transactionToDelete.value) {
        router.delete(route('transactions.destroy', transactionToDelete.value.id), {
          onSuccess: () => {
            showDeleteModal.value = false
            transactionToDelete.value = null
          }
        })
      }
    }

    const updateStatus = (transaction, status) => {
      router.patch(route('transactions.update-status', transaction.id), {
        payment_status: status
      })
    }

    const canEdit = (transaction) => {
      const user = usePage().props.auth.user
      return user.role === 'admin' || user.role === 'resepsionis' || transaction.user_id === user.id
    }

    const canDelete = (transaction) => {
      const user = usePage().props.auth.user
      return user.role === 'admin' || transaction.user_id === user.id
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
      }).format(amount)
    }

    const formatMetode = (metode) => {
      const metodeMap = {
        'cash': 'Tunai',
        'debit_card': 'Kartu Debit',
        'credit_card': 'Kartu Kredit',
        'transfer': 'Transfer',
        'other': 'Lainnya',
        // backward compatibility
        'debit': 'Kartu Debit',
        'credit': 'Kartu Kredit',
        'tunai': 'Tunai',
        'kartu': 'Kartu'
      }
      return metodeMap[metode] || metode
    }

    const formatStatus = (status) => {
      const statusMap = {
        'paid': 'Lunas',
        'pending': 'Belum Lunas',
        'partially_paid': 'Sebagian Lunas',
        'cancelled': 'Dibatalkan',
        // backward compatibility
        'failed': 'Gagal'
      }
      return statusMap[status] || status
    }

    const getStatusClass = (status) => {
      const classMap = {
        'paid': 'badge-success',
        'pending': 'badge-warning',
        'partially_paid': 'badge-info',
        'cancelled': 'badge-error',
        // backward compatibility
        'failed': 'badge-error'
      }
      return classMap[status] || 'badge-neutral'
    }

    return {
      router,
      filters,
      showDeleteModal,
      transactionToDelete,
      getTransactionNumber,
      applyFilters,
      clearFilters,
      confirmDelete,
      deleteTransaction,
      updateStatus,
      canEdit,
      canDelete,
      formatDate,
      formatCurrency,
      formatMetode,
      formatStatus,
      getStatusClass,
    }
  }
}
</script>
