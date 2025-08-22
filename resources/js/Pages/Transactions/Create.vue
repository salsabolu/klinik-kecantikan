<template>
  <SimpleLayout title="Transaksi Baru">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold">Transaksi Baru</h2>
              <button @click="handleBack" type="button" class="btn btn-outline">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
              </button>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
              <!-- Debug Info -->
              <!-- <div v-if="form.patient_id && form.items.length > 0" class="alert alert-info">
                <div>
                  <h4 class="font-bold">Debug Form Data:</h4>
                  <p>Patient ID: {{ form.patient_id }}</p>
                  <p>Reservation ID: {{ form.reservation_id || 'Tidak ada' }}</p>
                  <p>Total Items: {{ form.items.length }}</p>
                  <p>Total: {{ formatCurrency(form.total) }}</p>
                </div>
              </div> -->

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

                        <!-- Today's Reservations -->
                        <div class="form-control">
                          <label class="label">
                            <span class="label-text">Reservasi Hari Ini (Opsional)</span>
                          </label>
                          <select 
                            v-model="form.reservation_id"
                            class="select select-bordered"
                            @change="onReservationSelected"
                          >
                            <option value="">Pilih reservasi hari ini (opsional)</option>
                            <option 
                              v-for="reservation in todaysReservations" 
                              :key="reservation.id" 
                              :value="reservation.id"
                            >
                              {{ reservation.patient.nama_lengkap }} - 
                              {{ reservation.service.nama_layanan }} 
                              ({{ formatTime(reservation.waktu_mulai) }} - {{ formatTime(reservation.waktu_selesai) }})
                              - {{ reservation.status === 'completed' ? 'Selesai' : 'Dikonfirmasi' }}
                            </option>
                          </select>
                          <div class="label">
                            <span class="label-text-alt text-gray-500">
                              Memilih reservasi akan otomatis mengisi data pasien dan menambahkan layanan ke transaksi
                            </span>
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

                      <div class="space-y-4">
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
                                :class="{ 
                                  'input-error': isStockExceeded(item, index),
                                  'input-warning': isStockWarning(item, index)
                                }"
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

                          <!-- Stock Warning -->
                          <div v-if="item.product_id" class="mt-2">
                            <div v-if="isStockExceeded(item, index)" class="text-error text-sm">
                              {{ getStockMessage(item, index) }}
                            </div>
                            <div v-else-if="isStockWarning(item, index)" class="text-warning text-sm">
                              {{ getStockMessage(item, index) }}
                            </div>
                            <div v-else class="text-info text-sm">
                              {{ getStockMessage(item, index) }}
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Summary -->
                <div class="space-y-6">
                  <div class="card bg-base-100 shadow">
                    <div class="card-body">
                      <h3 class="card-title">Ringkasan</h3>
                      <div class="space-y-2">
                        <div class="flex justify-between">
                          <span>Total Item:</span>
                          <span class="font-semibold">{{ totalQuantity }}</span>
                        </div>
                        <div class="divider my-2"></div>
                        <div class="flex justify-between text-lg font-semibold">
                          <span>Total:</span>
                          <span class="text-primary">{{ formatCurrency(form.total) }}</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Submit Button -->
                  <div class="card bg-base-100 shadow">
                    <div class="card-body">
                      <div class="flex gap-4">
                        <button @click="handleCancel" type="button" class="btn btn-outline flex-1">
                          Batal
                        </button>
                        <button 
                          type="submit"
                          :disabled="form.processing || form.items.length === 0"
                          class="btn btn-primary flex-1"
                        >
                          <svg v-if="!form.processing" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                          </svg>
                          <span class="loading loading-spinner loading-sm mr-2" v-if="form.processing"></span>
                          {{ form.processing ? 'Menyimpan...' : 'Simpan Transaksi' }}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div v-if="showCancelModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">
          <svg class="w-6 h-6 inline-block mr-2 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z">
            </path>
          </svg>
          Konfirmasi Perubahan
        </h3>
        <p class="py-4 text-base-content/80">
          Anda telah mengisi beberapa data pada form transaksi ini. Jika Anda keluar sekarang, semua data akan hilang.
        </p>
        <p class="text-sm text-base-content/60 mb-6">
          Apakah Anda yakin ingin meninggalkan halaman ini tanpa menyimpan?
        </p>
        <div class="modal-action">
          <button @click="showCancelModal = false" class="btn btn-ghost">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
            </svg>
            Tetap di Halaman
          </button>
          <Link :href="route('transactions.index')" class="btn btn-warning">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3v1"></path>
            </svg>
            Ya, Keluar
          </Link>
        </div>
      </div>
    </div>
  </SimpleLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import SimpleLayout from '@/Layouts/SimpleLayout.vue'

export default {
  components: {
    Head,
    Link,
    SimpleLayout,
  },
  props: {
    patients: Array,
    services: Array,
    products: Array,
    todaysReservations: Array,
    errors: Object,
  },
  setup(props) {
    const showCancelModal = ref(false)

    const form = useForm({
      patient_id: '',
      reservation_id: '',
      payment_method: 'cash',
      payment_status: 'paid',
      total: 0,
      items: [
        {
          type: '',
          service_id: null,
          product_id: null,
          quantity: 1,
          price: 0,
          subtotal: 0,
        }
      ]
    })

    // Check if form has data to determine if we should show warning
    const hasFormData = computed(() => {
      return form.patient_id ||
        form.payment_method !== 'cash' ||
        form.payment_status !== 'paid' ||
        form.items.some(item => 
          item.service_id || 
          item.product_id || 
          item.quantity !== 1 || 
          item.price !== 0
        )
    })

    const addItem = () => {
      form.items.push({
        type: '',
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
        // Recalculate total
        form.total = form.items.reduce((total, item) => total + (item.subtotal || 0), 0)
      }
    }

    const updatePrice = (index) => {
      const item = form.items[index]
      if (item.service_id) {
        const service = props.services.find(s => s.id == item.service_id)
        if (service) {
          item.price = service.harga
          calculateSubtotal(index)
        }
      } else if (item.product_id) {
        const product = props.products.find(p => p.id == item.product_id)
        if (product) {
          item.price = product.harga
          calculateSubtotal(index)
        }
      }
    }

    const resetItemSelection = (index) => {
      const item = form.items[index]
      item.service_id = null
      item.product_id = null
      item.price = 0
      item.subtotal = 0
    }

    const calculateSubtotal = (index) => {
      const item = form.items[index]
      item.subtotal = (item.quantity || 0) * (item.price || 0)
      // Update total
      form.total = form.items.reduce((total, item) => total + (item.subtotal || 0), 0)
    }

    const totalQuantity = computed(() => {
      return form.items.reduce((total, item) => total + (item.quantity || 0), 0)
    })

    const totalPrice = computed(() => {
      return form.items.reduce((total, item) => total + (item.subtotal || 0), 0)
    })

    const formatCurrency = (amount) => {
      if (isNaN(amount) || amount === null || amount === undefined) {
        return 'Rp 0'
      }
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
      }).format(amount)
    }

    const formatTime = (time) => {
      if (!time) return ''
      // If time is in HH:MM:SS format, take only HH:MM
      return time.slice(0, 5)
    }

    const onReservationSelected = () => {
      if (form.reservation_id) {
        const reservation = props.todaysReservations.find(r => r.id == form.reservation_id)
        if (reservation) {
          // Set patient
          form.patient_id = reservation.patient.id
          
          // Clear existing items first
          form.items = []
          
          // Add service from reservation to first item
          const newItem = {
            type: 'service',
            service_id: reservation.service.id,
            product_id: null,
            quantity: 1,
            price: reservation.service.harga,
            subtotal: reservation.service.harga
          }
          
          form.items.push(newItem)
          
          // Update total
          form.total = reservation.service.harga
        }
      }
    }

    const submit = () => {
      console.log('Submitting form:', form.data())
      
      // Additional validation before submission
      for (let i = 0; i < form.items.length; i++) {
        const item = form.items[i]
        
        // Validate required fields
        if (!item.service_id && !item.product_id) {
          alert(`Item ${i + 1}: Silakan pilih layanan atau produk`)
          return
        }
        
        if (!item.quantity || item.quantity < 1) {
          alert(`Item ${i + 1}: Jumlah harus lebih dari 0`)
          return
        }
        
        if (!item.price || item.price < 0) {
          alert(`Item ${i + 1}: Harga tidak boleh kosong atau negatif`)
          return
        }
        
        // Stock validation with dynamic calculation
        if (item.product_id && item.quantity) {
          const availableStock = getAvailableStock(item, i)
          if (item.quantity > availableStock) {
            alert(`Item ${i + 1}: Jumlah yang diminta (${item.quantity}) melebihi stok yang tersedia (${availableStock})`)
            return // Prevent form submission
          }
        }
      }
      
      // Basic form validation
      if (!form.patient_id) {
        alert('Silakan pilih pasien')
        return
      }
      
      console.log('Form data before submission:', form.data())
      
      // Submit using form.post
      form.post(route('transactions.store'), {
        onSuccess: () => {
          console.log('Transaction created successfully')
        },
        onError: (errors) => {
          console.error('Form submission errors:', errors)
        }
      })
    }

    const handleBack = () => {
      if (hasFormData.value) {
        showCancelModal.value = true
      } else {
        window.location.href = route('transactions.index')
      }
    }

    const handleCancel = () => {
      if (hasFormData.value) {
        showCancelModal.value = true
      } else {
        window.location.href = route('transactions.index')
      }
    }

    // Stock validation helpers with dynamic stock calculation
    const getProductStock = (item) => {
      if (item.product_id) {
        const product = props.products.find(p => p.id == item.product_id)
        return product ? product.stok : 0
      }
      return 0
    }

    const getAvailableStock = (currentItem, currentIndex) => {
      if (!currentItem.product_id) {
        return 0
      }

      const product = props.products.find(p => p.id == currentItem.product_id)
      if (!product) return 0

      let baseStock = product.stok
      
      // Calculate total quantity used for this product in other items (excluding current item)
      let usedQuantity = 0
      form.items.forEach((item, index) => {
        if (index !== currentIndex && 
            item.product_id == currentItem.product_id && 
            item.quantity) {
          usedQuantity += parseInt(item.quantity)
        }
      })
      
      return Math.max(0, baseStock - usedQuantity)
    }

    const isStockExceeded = (item, index) => {
      if (item.product_id && item.quantity) {
        const availableStock = getAvailableStock(item, index)
        return parseInt(item.quantity) > availableStock
      }
      return false
    }

    const isStockWarning = (item, index) => {
      if (item.product_id && item.quantity) {
        const availableStock = getAvailableStock(item, index)
        const quantity = parseInt(item.quantity)
        return quantity <= availableStock && availableStock <= 5 && !isStockExceeded(item, index)
      }
      return false
    }

    const getStockMessage = (item, index) => {
      if (!item.product_id) return ''
      
      const availableStock = getAvailableStock(item, index)
      const originalStock = getProductStock(item)
      const quantity = parseInt(item.quantity) || 0
      
      if (quantity > availableStock) {
        return `Stok tidak mencukupi! Tersedia: ${availableStock} (dari total ${originalStock})`
      } else if (availableStock <= 5) {
        return `Perhatian: Stok tersisa ${availableStock} (dari total ${originalStock})`
      } else {
        return `Stok tersedia: ${availableStock} (dari total ${originalStock})`
      }
    }

    return {
      form,
      showCancelModal,
      hasFormData,
      addItem,
      removeItem,
      updatePrice,
      resetItemSelection,
      calculateSubtotal,
      totalQuantity,
      totalPrice,
      formatCurrency,
      formatTime,
      onReservationSelected,
      submit,
      handleBack,
      handleCancel,
      getProductStock,
      getAvailableStock,
      isStockExceeded,
      isStockWarning,
      getStockMessage,
    }
  }
}
</script>
