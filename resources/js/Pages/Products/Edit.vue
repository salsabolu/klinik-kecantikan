<template>
  <Head title="Edit Produk" />
  <SimpleLayout>
    <div class="w-full mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-semibold">Edit Produk</h1>
          <p class="text-gray-600">Perbarui informasi produk</p>
        </div>
        <button @click="handleBack" type="button" class="btn btn-outline">
          <PhArrowLeft :size="20" color="black" weight="regular" />
          Kembali
        </button>
      </div>

      <!-- Form -->
      <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <form @submit.prevent="submitForm" class="p-6 lg:p-8">
          <div class="space-y-6">
            <!-- Nama Produk -->
            <div class="form-control w-full">
              <label class="label">
                <span class="label-text">Nama Produk <span class="text-red-500">*</span></span>
                <span class="label-text-alt">{{ (form.nama_produk || '').length }}/255</span>
              </label>
              <input type="text" v-model="form.nama_produk" class="input input-bordered w-full"
                :class="{ 'input-error': errors.nama_produk || (form.nama_produk || '').length > 255 }"
                placeholder="Masukkan nama produk..." maxlength="255" @input="clearError('nama_produk')" />
              <div v-if="errors.nama_produk" class="label">
                <span class="label-text-alt text-red-500">{{ errors.nama_produk }}</span>
              </div>
              <div v-else-if="(form.nama_produk || '').length > 255" class="label">
                <span class="label-text-alt text-red-500">Nama produk tidak boleh lebih dari 255 karakter</span>
              </div>
            </div>

            <!-- Deskripsi -->
            <div class="form-control w-full">
              <label class="label">
                <span class="label-text">Deskripsi</span>
                <span class="label-text-alt" :class="{ 'text-red-500': (form.deskripsi || '').length > 1000 }">
                  {{ (form.deskripsi || '').length }}/1000
                </span>
              </label>
              <textarea v-model="form.deskripsi" class="textarea textarea-bordered w-full h-32"
                :class="{ 'textarea-error': errors.deskripsi || (form.deskripsi || '').length > 1000 }"
                placeholder="Masukkan deskripsi produk..." maxlength="1000" @input="clearError('deskripsi')"></textarea>
              <div v-if="errors.deskripsi" class="label">
                <span class="label-text-alt text-red-500">{{ errors.deskripsi }}</span>
              </div>
              <div v-else-if="(form.deskripsi || '').length > 1000" class="label">
                <span class="label-text-alt text-red-500">Deskripsi tidak boleh lebih dari 1000 karakter</span>
              </div>
            </div>

            <!-- Harga -->
            <div class="form-control w-full">
              <label class="label">
                <span class="label-text">Harga <span class="text-red-500">*</span></span>
              </label>
              <div class="join w-full">
                <span class="join-item btn btn-disabled">Rp</span>
                <input type="number" v-model="form.harga" class="input input-bordered join-item flex-1"
                  :class="{ 'input-error': errors.harga }" placeholder="0" min="0" step="1000"
                  @input="clearError('harga')" @keypress="preventNegative" />
              </div>
              <div v-if="errors.harga" class="label">
                <span class="label-text-alt text-red-500">{{ errors.harga }}</span>
              </div>
              <div v-else class="label">
                <span class="label-text-alt">{{ formatPreviewCurrency(form.harga) }}</span>
              </div>
            </div>

            <!-- Stok -->
            <div class="form-control w-full">
              <label class="label">
                <span class="label-text">Stok <span class="text-red-500">*</span></span>
              </label>
              <input type="number" v-model="form.stok" class="input input-bordered w-full"
                :class="{ 'input-error': errors.stok }" placeholder="0" min="0" @input="clearError('stok')"
                @keypress="preventNegative" />
              <div v-if="errors.stok" class="label">
                <span class="label-text-alt text-red-500">{{ errors.stok }}</span>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="card-actions justify-end mt-6">
            <button type="button" @click="handleCancel" class="btn btn-outline">
              Batal
            </button>
            <button 
              type="submit" 
              class="btn btn-primary" 
              :class="{ 'loading': form.processing }"
              :disabled="form.processing || hasValidationErrors">
              {{ form.processing ? 'Menyimpan...' : 'Perbarui Produk' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div v-if="showCancelModal" class="modal modal-open">
      <div class="modal-box">
        <div class="flex items-start gap-2">
          <PhWarning :size="30" color="currentColor" weight="regular" />
          <p class="text-lg font-semibold mb-4">
            Konfirmasi Perubahan
          </p>
        </div>
        <p class="mt-4">
          Anda telah melakukan perubahan pada form ini. Jika Anda keluar sekarang, semua perubahan akan hilang.
        </p>
        <p class="mt-4">
          Apakah Anda yakin ingin meninggalkan halaman ini tanpa menyimpan?
        </p>
        <div class="modal-action">
          <button @click="showCancelModal = false" class="btn btn-outline">
            <PhArrowLeft :size="20" color="currentColor" weight="regular" />
            Tetap di Halaman
          </button>
          <Link href="/products" class="btn btn-error">
          <PhSignOut :size="20" color="currentColor" weight="regular" />
          Ya, Keluar
          </Link>
        </div>
      </div>
    </div>
  </SimpleLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import SimpleLayout from '@/Layouts/SimpleLayout.vue'
import { PhArrowLeft, PhSignOut, PhWarning } from '@phosphor-icons/vue'

const props = defineProps({
  product: Object,
  errors: Object,
})

// Form data - initialize with product data
const form = useForm({
  nama_produk: props.product?.nama_produk || '',
  deskripsi: props.product?.deskripsi || '',
  harga: props.product?.harga || '',
  stok: props.product?.stok || '',
})

// Watch for prop changes and update form (for cases where product loads asynchronously)
watch(() => props.product, (newProduct) => {
  if (newProduct) {
    form.nama_produk = newProduct.nama_produk || ''
    form.deskripsi = newProduct.deskripsi || ''
    form.harga = newProduct.harga || ''
    form.stok = newProduct.stok || ''
  }
}, { immediate: true })

// Error handling
const errors = ref({})

// Modal states
const showCancelModal = ref(false)

// Original form values for comparison - these should be reactive to props changes
const originalValues = computed(() => ({
  nama_produk: props.product?.nama_produk || '',
  deskripsi: props.product?.deskripsi || '',
  harga: props.product?.harga || '',
  stok: props.product?.stok || '',
}))

// Check if form has changes
const hasFormChanges = computed(() => {
  // Only check for changes if the product data is loaded
  if (!props.product) return false

  return String(form.nama_produk || '') !== String(originalValues.value.nama_produk || '') ||
    String(form.deskripsi || '') !== String(originalValues.value.deskripsi || '') ||
    String(form.harga || '') !== String(originalValues.value.harga || '') ||
    String(form.stok || '') !== String(originalValues.value.stok || '')
})

// Computed validation
const hasValidationErrors = computed(() => {
  return !form.nama_produk ||
    (form.nama_produk || '').length > 255 ||
    (form.deskripsi || '').length > 1000 ||
    !form.harga ||
    form.harga <= 0 ||
    form.stok === '' ||
    form.stok < 0
})

// Methods
const clearError = (field) => {
  if (errors.value[field]) {
    delete errors.value[field]
  }
}

const preventNegative = (e) => {
  if (e.key === '-' || e.key === 'e' || e.key === 'E') {
    e.preventDefault()
  }
}

const formatPreviewCurrency = (amount) => {
  if (!amount) return 'Rp 0'
  return `Rp ${new Intl.NumberFormat('id-ID').format(amount)}`
}

// Handle back button
const handleBack = () => {
  if (hasFormChanges.value) {
    showCancelModal.value = true
  } else {
    window.location.href = '/products'
  }
}

// Handle cancel button
const handleCancel = () => {
  if (hasFormChanges.value) {
    showCancelModal.value = true
  } else {
    window.location.href = '/products'
  }
}

const submitForm = () => {
  // Ensure product exists before submitting
  if (!props.product?.id) {
    console.error('Product ID not found')
    return
  }

  form.put(`/products/${props.product.id}`, {
    onSuccess: () => {
      console.log('Form submission successful')
      // Success handled by redirect
    },
    onError: (formErrors) => {
      console.error('Form submission failed:', formErrors)
      errors.value = formErrors
    }
  })
}
</script>
