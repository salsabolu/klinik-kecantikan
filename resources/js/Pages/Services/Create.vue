<template>

  <Head title="Tambah Layanan" />
  <SimpleLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-semibold">Tambah Layanan</h1>
          <p class="text-gray-600">Tambahkan layanan baru ke dalam sistem</p>
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
            <!-- Nama Layanan -->
            <div class="form-control w-full">
              <label class="label">
                <span class="label-text">Nama Layanan <span class="text-red-500">*</span></span>
                <span class="label-text-alt">{{ (form.nama_layanan || '').length }}/255</span>
              </label>
              <input type="text" v-model="form.nama_layanan" class="input input-bordered w-full"
                :class="{ 'input-error': errors.nama_layanan || (form.nama_layanan || '').length > 255 }"
                placeholder="Masukkan nama layanan" maxlength="255" @input="clearError('nama_layanan')" />
              <div v-if="errors.nama_layanan" class="label">
                <span class="label-text-alt text-red-500">{{ errors.nama_layanan }}</span>
              </div>
              <div v-else-if="(form.nama_layanan || '').length > 255" class="label">
                <span class="label-text-alt text-red-500">Nama layanan tidak boleh lebih dari 255 karakter</span>
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
              <textarea v-model="form.deskripsi" class="textarea textarea-bordered w-full"
                :class="{ 'textarea-error': errors.deskripsi || (form.deskripsi || '').length > 1000 }"
                placeholder="Masukkan deskripsi layanan" rows="3" maxlength="1000"
                @input="clearError('deskripsi')"></textarea>
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

            <!-- Durasi -->
            <div class="form-control w-full">
              <label class="label">
                <span class="label-text">Durasi (Menit) <span class="text-red-500">*</span></span>
              </label>
              <input type="number" v-model="form.durasi_menit" class="input input-bordered w-full"
                :class="{ 'input-error': errors.durasi_menit }" placeholder="0" min="0" step="15"
                @input="clearError('durasi_menit')" @keypress="preventNegative" />
              <div v-if="errors.durasi_menit" class="label">
                <span class="label-text-alt text-red-500">{{ errors.durasi_menit }}</span>
              </div>
              <div class="label">
                <span class="label-text-alt">Durasi dalam menit (contoh: 30, 60, 90)</span>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="card-actions justify-end mt-6">
            <button type="button" @click="handleCancel" class="btn btn-outline">
              Batal
            </button>
            <button type="submit" class="btn btn-primary" :class="{ 'loading': form.processing }"
              :disabled="form.processing">
              <span v-if="!form.processing">Simpan Layanan</span>
              <span v-else>Menyimpan...</span>
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
          Anda telah mengisi beberapa data pada form ini. Jika Anda keluar sekarang, semua data akan hilang.
        </p>
        <p class="mt-4">
          Apakah Anda yakin ingin meninggalkan halaman ini tanpa menyimpan?
        </p>
        <div class="modal-action">
          <button @click="showCancelModal = false" class="btn btn-outline">
            <PhArrowLeft :size="20" color="currentColor" weight="regular" />
            Tetap di Halaman
          </button>
          <Link href="/services" class="btn btn-error">
          <PhSignOut :size="20" color="currentColor" weight="regular" />
          Ya, Keluar
          </Link>
        </div>
      </div>
    </div>
  </SimpleLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import SimpleLayout from '@/Layouts/SimpleLayout.vue'
import { PhArrowLeft, PhSignOut, PhWarning } from '@phosphor-icons/vue'

defineProps({
  errors: Object,
})

// Form data
const form = useForm({
  nama_layanan: '',
  deskripsi: '',
  harga: '',
  durasi_menit: '',
})

// Error handling
const errors = ref({})

// Modal states
const showCancelModal = ref(false)

// Check if form has any data
const hasFormData = computed(() => {
  return form.nama_layanan ||
    form.deskripsi ||
    form.harga ||
    form.durasi_menit
})

// Computed validation
const hasValidationErrors = computed(() => {
  return !form.nama_layanan ||
    (form.nama_layanan || '').length > 255 ||
    (form.deskripsi || '').length > 1000 ||
    !form.harga ||
    form.harga <= 0 ||
    !form.durasi_menit ||
    form.durasi_menit <= 0
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
  if (hasFormData.value) {
    showCancelModal.value = true
  } else {
    window.location.href = '/services'
  }
}

// Handle cancel button
const handleCancel = () => {
  if (hasFormData.value) {
    showCancelModal.value = true
  } else {
    window.location.href = '/services'
  }
}

const submitForm = () => {
  form.post('/services', {
    onSuccess: () => {
      // Success handled by redirect
    },
    onError: (formErrors) => {
      errors.value = formErrors
    }
  })
}
</script>
