<template>
  <Head title="Edit Transaksi" />
  
  <SimpleLayout title="Edit Transaksi">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold">Edit Transaksi #{{ transaction.id }}</h2>
              <button @click="handleBack" type="button" class="btn btn-outline">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
              </button>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
              <!-- Error Display -->
              <div v-if="Object.keys(errors).length > 0" class="alert alert-error">
                <div>
                  <h4 class="font-bold">Error:</h4>
                  <ul>
                    <li v-for="(error, key) in errors" :key="key">
                      <strong>{{ key }}:</strong> {{ error }}
                    </li>
                  </ul>
                </div>
              </div>

              <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Form -->
                <div class="lg:col-span-2 space-y-6">
                  <!-- Informasi Transaksi -->
                  <div class="card bg-base-100 shadow">
                    <div class="card-body">
                      <h3 class="card-title">Informasi Transaksi</h3>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control">
                          <label class="label">
                            <span class="label-text">Pasien *</span>
                          </label>
                          <select 
                            v-model="form.patient_id"
                            class="select select-bordered"
                            :class="{ 'select-error': errors.patient_id }"
                            required
                          >
                            <option value="">Pilih Pasien</option>
                            <option 
                              v-for="patient in patients" 
                              :key="patient.id" 
                              :value="patient.id"
                            >
                              {{ patient.nama_lengkap }} - {{ patient.nomor_telepon }}
                            </option>
                          </select>
                          <div v-if="errors.patient_id" class="label">
                            <span class="label-text-alt text-error">{{ errors.patient_id }}</span>
                          </div>
                        </div>

                        <div class="form-control">
                          <label class="label">
                            <span class="label-text">Tanggal Transaksi *</span>
                          </label>
                          <input 
                            v-model="form.tanggal_transaksi"
                            type="date" 
                            class="input input-bordered"
                            :class="{ 'input-error': errors.tanggal_transaksi }"
                            required
                          />
                          <div v-if="errors.tanggal_transaksi" class="label">
                            <span class="label-text-alt text-error">{{ errors.tanggal_transaksi }}</span>
                          </div>
                        </div>

                        <div class="form-control">
                          <label class="label">
                            <span class="label-text">Metode Pembayaran *</span>
                          </label>
                          <select 
                            v-model="form.payment_method"
                            class="select select-bordered"
                            :class="{ 'select-error': errors.payment_method }"
                            required
                          >
                            <option value="cash">Tunai</option>
                            <option value="debit_card">Kartu Debit</option>
                            <option value="credit_card">Kartu Kredit</option>
                            <option value="transfer">Transfer</option>
                          </select>
                          <div v-if="errors.payment_method" class="label">
                            <span class="label-text-alt text-error">{{ errors.payment_method }}</span>
                          </div>
                        </div>

                        <div class="form-control">
                          <label class="label">
                            <span class="label-text">Status Pembayaran *</span>
                          </label>
                          <select 
                            v-model="form.payment_status"
                            class="select select-bordered"
                            :class="{ 'select-error': errors.payment_status }"
                            required
                          >
                            <option value="paid">Lunas</option>
                            <option value="pending">Belum Lunas</option>
                            <option value="partially_paid">Sebagian Lunas</option>
                            <option value="cancelled">Dibatalkan</option>
                          </select>
                          <div v-if="errors.payment_status" class="label">
                            <span class="label-text-alt text-error">{{ errors.payment_status }}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Items Transaksi -->
                  <div class="card bg-base-100 shadow">
                    <div class="card-body">
                      <div class="flex justify-between items-center mb-4">
                        <h3 class="card-title">Item Transaksi</h3>
                        <button 
                          @click="addItem"
                          type="button"
                          class="btn btn-sm btn-primary"
                        >
                          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                          </svg>
                          Tambah Item
                        </button>
                      </div>

                      <div v-if="errors.items" class="alert alert-error mb-4">
                        <svg class="stroke-current shrink-0 w-6 h-6" fill="none" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ errors.items }}</span>
                      </div>

                      <div v-if="form.items && form.items.length > 0" class="space-y-4">
                        <div 
                          v-for="(item, index) in form.items" 
                          :key="index"
                          class="border border-base-300 rounded-lg p-4"
                        >
                          <div class="flex justify-between items-center mb-4">
                            <h4 class="font-semibold">Item {{ index + 1 }}</h4>
                            <button 
                              v-if="form.items.length > 1"
                              @click="removeItem(index)"
                              type="button"
                              class="btn btn-sm btn-error"
                            >
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                              </svg>
                            </button>
                          </div>

                          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Service Selection -->
                            <div class="form-control">
                              <label class="label">
                                <span class="label-text">Layanan</span>
                              </label>
                              <select 
                                v-model="item.service_id"
                                @change="item.product_id = null; updatePrice(index)"
                                class="select select-bordered select-sm"
                              >
                                <option value="">Pilih Layanan</option>
                                <option 
                                  v-for="service in services" 
                                  :key="service.id" 
                                  :value="service.id"
                                >
                                  {{ service.nama_layanan }} - {{ formatCurrency(service.harga) }}
                                </option>
                              </select>
                            </div>

                            <!-- Product Selection -->
                            <div class="form-control">
                              <label class="label">
                                <span class="label-text">Produk</span>
                              </label>
                              <select 
                                v-model="item.product_id"
                                @change="item.service_id = null; updatePrice(index)"
                                class="select select-bordered select-sm"
                              >
                                <option value="">Pilih Produk</option>
                                <option 
                                  v-for="product in products" 
                                  :key="product.id" 
                                  :value="product.id"
                                >
                                  {{ product.nama_produk }} - {{ formatCurrency(product.harga) }} (Stok: {{ product.stok }})
                                </option>
                              </select>
                            </div>

                            <!-- Quantity -->
                            <div class="form-control">
                              <label class="label">
                                <span class="label-text">Jumlah *</span>
                              </label>
                              <input 
                                v-model.number="item.quantity"
                                @input="calculateSubtotal(index)"
                                type="number"
                                min="1"
                                class="input input-bordered input-sm"
                                placeholder="Jumlah"
                                required
                              />
                            </div>

                            <!-- Price -->
                            <div class="form-control">
                              <label class="label">
                                <span class="label-text">Harga Satuan</span>
                              </label>
                              <input 
                                v-model.number="item.price"
                                @input="calculateSubtotal(index)"
                                type="number"
                                min="0"
                                class="input input-bordered input-sm"
                                placeholder="Harga satuan"
                                readonly
                              />
                            </div>

                            <!-- Subtotal -->
                            <div class="form-control">
                              <label class="label">
                                <span class="label-text">Subtotal</span>
                              </label>
                              <input 
                                :value="formatCurrency(item.subtotal)"
                                type="text"
                                class="input input-bordered input-sm"
                                readonly
                              />
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <!-- Empty state when no items -->
                      <div v-else class="text-center py-8 text-gray-500">
                        <p>Belum ada item transaksi</p>
                        <button 
                          type="button"
                          @click="addItem"
                          class="btn btn-primary btn-sm mt-2"
                        >
                          Tambah Item Pertama
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                  <!-- Ringkasan -->
                  <div class="card bg-base-100 shadow sticky top-4">
                    <div class="card-body">
                      <h3 class="card-title">Ringkasan</h3>
                      <div class="space-y-2">
                        <div class="flex justify-between">
                          <span>Jumlah Item:</span>
                          <span>{{ form.items ? form.items.length : 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                          <span>Total Qty:</span>
                          <span>{{ totalQuantity }}</span>
                        </div>
                        <div class="divider my-2"></div>
                        <div class="flex justify-between text-lg font-semibold">
                          <span>Total Harga:</span>
                          <span>{{ formatCurrency(totalPrice) }}</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Submit Button -->
                  <div class="card bg-base-100 shadow">
                    <div class="card-body">
                      <button 
                        type="submit"
                        class="btn btn-primary w-full"
                        :disabled="form.processing || (form.items && form.items.length === 0)"
                      >
                        <span v-if="form.processing" class="loading loading-spinner"></span>
                        {{ form.processing ? 'Menyimpan...' : 'Update Transaksi' }}
                      </button>
                      
                      <button 
                        type="button" 
                        @click="handleCancel"
                        class="btn btn-outline w-full mt-3"
                        :disabled="form.processing"
                      >
                        Batal
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Back Confirmation Modal -->
    <div v-if="showBackModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">
          <svg class="w-6 h-6 inline-block mr-2 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
          </svg>
          Konfirmasi Kembali
        </h3>
        <p class="py-4 text-base-content/80">
          Anda telah melakukan beberapa perubahan pada form transaksi ini. Jika Anda keluar sekarang, perubahan tidak akan tersimpan.
        </p>
        <p class="text-sm text-base-content/60 mb-6">
          Apakah Anda yakin ingin kembali tanpa menyimpan perubahan?
        </p>
        <div class="modal-action">
          <button @click="showBackModal = false" class="btn btn-outline">Tetap Edit</button>
          <Link :href="route('transactions.index')" class="btn btn-error">Ya, Kembali</Link>
        </div>
      </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div v-if="showCancelModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">
          <svg class="w-6 h-6 inline-block mr-2 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
          </svg>
          Konfirmasi Batal
        </h3>
        <p class="py-4 text-base-content/80">
          Anda telah melakukan beberapa perubahan pada form transaksi ini. Jika Anda membatalkan sekarang, perubahan tidak akan tersimpan.
        </p>
        <p class="text-sm text-base-content/60 mb-6">
          Apakah Anda yakin ingin membatalkan tanpa menyimpan perubahan?
        </p>
        <div class="modal-action">
          <button @click="showCancelModal = false" class="btn btn-outline">Tetap Edit</button>
          <Link :href="route('transactions.show', transaction.id)" class="btn btn-error">Ya, Batal</Link>
        </div>
      </div>
    </div>
  </SimpleLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import { useForm, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted, watch } from 'vue'
import SimpleLayout from '@/Layouts/SimpleLayout.vue'

export default {
  components: {
    Head,
    Link,
    SimpleLayout
  },
  
  props: {
    transaction: Object,
    patients: Array,
    services: Array,
    products: Array,
    errors: Object
  },

  setup(props) {
    const showBackModal = ref(false)
    const showCancelModal = ref(false)
    const originalFormData = ref({})
    
    // Initialize form with transaction data
    const form = useForm({
      patient_id: props.transaction.patient_id || '',
      tanggal_transaksi: props.transaction.tanggal_transaksi ? 
        new Date(props.transaction.tanggal_transaksi).toISOString().split('T')[0] : '',
      payment_method: props.transaction.payment_method || 'cash',
      payment_status: props.transaction.payment_status || 'paid',
      items: props.transaction.transaction_details && props.transaction.transaction_details.length > 0
        ? props.transaction.transaction_details.map(detail => ({
            service_id: detail.service_id || null,
            product_id: detail.product_id || null,
            quantity: detail.quantity || 1,
            price: parseFloat(detail.unit_price) || 0,
            subtotal: parseFloat(detail.total_price) || 0
          }))
        : [{
            service_id: null,
            product_id: null,
            quantity: 1,
            price: 0,
            subtotal: 0,
          }]
    })

    // Store original form state for change detection
    onMounted(() => {
      originalFormData.value = JSON.parse(JSON.stringify(form.data()))
    })

    // Computed properties
    const hasFormData = computed(() => {
      return JSON.stringify(form.data()) !== JSON.stringify(originalFormData.value)
    })

    const totalQuantity = computed(() => {
      if (!form.items || !Array.isArray(form.items)) return 0
      return form.items.reduce((total, item) => total + (parseInt(item.quantity) || 0), 0)
    })

    const totalPrice = computed(() => {
      if (!form.items || !Array.isArray(form.items)) return 0
      return form.items.reduce((total, item) => total + (parseFloat(item.subtotal) || 0), 0)
    })

    // Methods
    const formatCurrency = (amount) => {
      if (isNaN(amount) || amount === null || amount === undefined) {
        return 'Rp 0'
      }
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
      }).format(amount)
    }

    const addItem = () => {
      form.items.push({
        service_id: null,
        product_id: null,
        quantity: 1,
        price: 0,
        subtotal: 0,
      })
    }

    const removeItem = (index) => {
      if (form.items.length > 1) {
        form.items.splice(index, 1)
      }
    }

    const resetItemSelection = (index) => {
      const item = form.items[index]
      item.service_id = null
      item.product_id = null
      item.price = 0
      item.subtotal = 0
    }

    const updatePrice = (index) => {
      const item = form.items[index]
      if (item.service_id) {
        const service = props.services.find(s => s.id == item.service_id)
        if (service) {
          item.price = parseFloat(service.harga)
          calculateSubtotal(index)
        }
      } else if (item.product_id) {
        const product = props.products.find(p => p.id == item.product_id)
        if (product) {
          item.price = parseFloat(product.harga)
          calculateSubtotal(index)
        }
      }
    }

    const calculateSubtotal = (index) => {
      const item = form.items[index]
      item.subtotal = parseFloat((item.quantity || 0) * (item.price || 0))
    }

    const handleBack = () => {
      if (hasFormData.value) {
        showBackModal.value = true
      } else {
        window.location.href = route('transactions.index')
      }
    }

    const handleCancel = () => {
      if (hasFormData.value) {
        showCancelModal.value = true
      } else {
        window.location.href = route('transactions.show', props.transaction.id)
      }
    }

    const submit = () => {
      // Additional validation before submission
      for (let i = 0; i < form.items.length; i++) {
        const item = form.items[i]
        if (!item.service_id && !item.product_id) {
          alert(`Item ${i + 1}: Pilih layanan atau produk`)
          return
        }
        if (!item.quantity || item.quantity < 1) {
          alert(`Item ${i + 1}: Jumlah harus diisi dan minimal 1`)
          return
        }
      }

      // Basic form validation
      if (!form.patient_id) {
        alert('Pilih pasien terlebih dahulu')
        return
      }

      console.log('Form data before submission:', form.data())
      
      form.put(route('transactions.update', props.transaction.id), {
        onSuccess: () => {
          console.log('Transaction updated successfully')
        },
        onError: (errors) => {
          console.error('Update failed:', errors)
        }
      })
    }

    return {
      form,
      showBackModal,
      showCancelModal,
      hasFormData,
      totalQuantity,
      totalPrice,
      formatCurrency,
      addItem,
      removeItem,
      resetItemSelection,
      updatePrice,
      calculateSubtotal,
      handleBack,
      handleCancel,
      submit
    }
  }
}
</script>
