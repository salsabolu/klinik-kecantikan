<template>
  <AppLayout title="Detail Transaksi">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold">Detail Transaksi #{{ transaction.id }}</h2>
              <div class="flex space-x-2">
                <Link :href="route('transactions.index')" class="btn btn-outline">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                  </svg>
                  Kembali
                </Link>
                <Link 
                  v-if="canEdit"
                  :href="route('transactions.edit', transaction.id)"
                  class="btn btn-warning"
                >
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                  Edit
                </Link>
              </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <!-- Main Content -->
              <div class="lg:col-span-2 space-y-6">
                <!-- Informasi Transaksi -->
                <div class="card bg-base-100 shadow">
                  <div class="card-body">
                    <h3 class="card-title">Informasi Transaksi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <span class="font-medium text-gray-600">ID Transaksi:</span>
                        <p class="text-lg">#{{ transaction.id }}</p>
                      </div>
                      <div>
                        <span class="font-medium text-gray-600">Tanggal Transaksi:</span>
                        <p class="text-lg">{{ formatDate(transaction.tanggal_transaksi) }}</p>
                      </div>
                      <div>
                        <span class="font-medium text-gray-600">Total Harga:</span>
                        <p class="text-2xl font-bold text-primary">{{ formatCurrency(transaction.total_amount) }}</p>
                      </div>
                      <div>
                        <span class="font-medium text-gray-600">Status Pembayaran:</span>
                        <div class="mt-1">
                          <div 
                            class="badge badge-lg"
                            :class="getStatusClass(transaction.payment_status)"
                          >
                            {{ formatStatus(transaction.payment_status) }}
                          </div>
                        </div>
                      </div>
                      <div>
                        <span class="font-medium text-gray-600">Metode Pembayaran:</span>
                        <div class="mt-1">
                          <div class="badge badge-outline badge-lg">
                            {{ formatMetode(transaction.payment_method) }}
                          </div>
                        </div>
                      </div>
                      <div>
                        <span class="font-medium text-gray-600">Kasir:</span>
                        <p class="text-lg">{{ transaction.user?.name }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Informasi Pasien -->
                <div class="card bg-base-100 shadow">
                  <div class="card-body">
                    <h3 class="card-title">Informasi Pasien</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <span class="font-medium text-gray-600">Nama Lengkap:</span>
                        <p class="text-lg">{{ transaction.patient?.nama_lengkap }}</p>
                      </div>
                      <div>
                        <span class="font-medium text-gray-600">Nomor Telepon:</span>
                        <p class="text-lg">{{ transaction.patient?.nomor_telepon }}</p>
                      </div>
                      <div>
                        <span class="font-medium text-gray-600">Email:</span>
                        <p class="text-lg">{{ transaction.patient?.email || '-' }}</p>
                      </div>
                      <div>
                        <span class="font-medium text-gray-600">Tanggal Lahir:</span>
                        <p class="text-lg">{{ formatDate(transaction.patient?.tanggal_lahir) }}</p>
                      </div>
                      <div class="md:col-span-2">
                        <span class="font-medium text-gray-600">Alamat:</span>
                        <p class="text-lg">{{ transaction.patient?.alamat || '-' }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Detail Items -->
                <div class="card bg-base-100 shadow">
                  <div class="card-body">
                    <h3 class="card-title">Detail Item</h3>
                    <div class="overflow-x-auto">
                      <table class="table w-full">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Item</th>
                            <th>Harga Satuan</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(detail, index) in transaction.transaction_details" :key="detail.id">
                            <td>{{ index + 1 }}</td>
                            <td>
                              <div>
                                <div class="font-medium">{{ detail.item_name }}</div>
                                <div class="text-sm opacity-60">
                                  {{ detail.service?.nama_layanan || detail.product?.nama_produk || '' }}
                                </div>
                              </div>
                            </td>
                            <td>{{ formatCurrency(detail.unit_price) }}</td>
                            <td>{{ detail.quantity }}</td>
                            <td class="font-medium">{{ formatCurrency(detail.total_price) }}</td>
                          </tr>
                        </tbody>
                        <tfoot>
                          <tr class="font-bold">
                            <td colspan="4" class="text-right">Total:</td>
                            <td>{{ formatCurrency(transaction.total_amount) }}</td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Sidebar -->
              <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="card bg-base-100 shadow">
                  <div class="card-body">
                    <h3 class="card-title">Aksi Cepat</h3>
                    <div class="space-y-2">
                      <div class="dropdown dropdown-end w-full" v-if="transaction.payment_status !== 'paid'">
                        <label tabindex="0" class="btn btn-success w-full">
                          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                          </svg>
                          Update Status
                        </label>
                        <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
                          <li v-if="transaction.payment_status !== 'paid'">
                            <a @click="updateStatus('paid')">
                              <span class="badge badge-success badge-sm">Lunas</span>
                            </a>
                          </li>
                          <li v-if="transaction.payment_status !== 'pending'">
                            <a @click="updateStatus('pending')">
                              <span class="badge badge-warning badge-sm">Belum Lunas</span>
                            </a>
                          </li>
                          <li v-if="transaction.payment_status !== 'partially_paid'">
                            <a @click="updateStatus('partially_paid')">
                              <span class="badge badge-info badge-sm">Sebagian Lunas</span>
                            </a>
                          </li>
                          <li v-if="transaction.payment_status !== 'cancelled'">
                            <a @click="updateStatus('cancelled')">
                              <span class="badge badge-error badge-sm">Dibatalkan</span>
                            </a>
                          </li>
                        </ul>
                      </div>

                      <button 
                        @click="printInvoice"
                        class="btn btn-info w-full"
                      >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Cetak Invoice
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Statistik -->
                <div class="card bg-base-100 shadow">
                  <div class="card-body">
                    <h3 class="card-title">Statistik</h3>
                    <div class="space-y-3">
                      <div class="flex justify-between">
                        <span class="text-gray-600">Jumlah Item:</span>
                        <span class="font-medium">{{ transaction.transaction_details?.length }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-gray-600">Total Qty:</span>
                        <span class="font-medium">{{ totalQuantity }}</span>
                      </div>
                      <div class="divider my-2"></div>
                      <div class="flex justify-between text-lg">
                        <span class="font-medium">Total Bayar:</span>
                        <span class="font-bold text-primary">{{ formatCurrency(transaction.total_amount) }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Timeline -->
                <div class="card bg-base-100 shadow">
                  <div class="card-body">
                    <h3 class="card-title">Informasi Sistem</h3>
                    <div class="space-y-3 text-sm">
                      <div>
                        <span class="font-medium">Dibuat pada:</span>
                        <br>{{ formatDateTime(transaction.created_at) }}
                      </div>
                      <div>
                        <span class="font-medium">Terakhir diperbarui:</span>
                        <br>{{ formatDateTime(transaction.updated_at) }}
                      </div>
                    </div>
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
import { router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  components: {
    Head,
    Link,
    AppLayout,
  },
  props: {
    transaction: Object,
  },
  setup(props) {
    const page = usePage()

    const canEdit = computed(() => {
      const user = page.props.auth.user
      return user.role === 'admin' || user.role === 'resepsionis' || props.transaction.user_id === user.id
    })

    const totalQuantity = computed(() => {
      return props.transaction.transaction_details?.reduce((total, detail) => total + (detail.quantity || 0), 0) || 0
    })

    const updateStatus = (status) => {
      router.patch(route('transactions.update-status', props.transaction.id), {
        payment_status: status
      })
    }

    const printInvoice = () => {
      window.print()
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    const formatDateTime = (datetime) => {
      return new Date(datetime).toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
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
      canEdit,
      totalQuantity,
      updateStatus,
      printInvoice,
      formatDate,
      formatDateTime,
      formatCurrency,
      formatMetode,
      formatStatus,
      getStatusClass,
    }
  }
}
</script>

<style>
@media print {
  .btn, .navbar, .footer, .breadcrumbs {
    display: none !important;
  }
}
</style>
