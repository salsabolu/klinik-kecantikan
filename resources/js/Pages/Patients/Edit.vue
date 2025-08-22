<template>
  <SimpleLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-base-content">Edit Data Pasien</h1>
          <p class="text-base-content/60 mt-1">Perbarui informasi data pasien</p>
        </div>
        <div class="flex gap-2">
          <button 
            @click="confirmCancel"
            class="btn btn-outline"
          >
            Kembali
          </button>
        </div>
      </div>

      <!-- Form -->
      <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Personal Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Nama Lengkap -->
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Nama Lengkap <span class="text-error">*</span></span>
                  <span class="label-text-alt">{{ (form.nama_lengkap || '').length }}/100 karakter</span>
                </label>
                <input
                  v-model="form.nama_lengkap"
                  type="text"
                  placeholder="Masukkan nama lengkap"
                  class="input input-bordered"
                  :class="{ 'input-error': form.errors.nama_lengkap }"
                  maxlength="100"
                  required
                />
                <div v-if="form.errors.nama_lengkap" class="label">
                  <span class="label-text-alt text-error">{{ form.errors.nama_lengkap }}</span>
                </div>
              </div>

              <!-- Tanggal Lahir -->
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Tanggal Lahir <span class="text-error">*</span></span>
                </label>
                <input
                  v-model="form.tanggal_lahir"
                  type="date"
                  :max="today"
                  class="input input-bordered"
                  :class="{ 'input-error': form.errors.tanggal_lahir }"
                  required
                />
                <div v-if="form.errors.tanggal_lahir" class="label">
                  <span class="label-text-alt text-error">{{ form.errors.tanggal_lahir }}</span>
                </div>
              </div>

              <!-- Jenis Kelamin -->
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Jenis Kelamin <span class="text-error">*</span></span>
                </label>
                <select
                  v-model="form.jenis_kelamin"
                  class="select select-bordered"
                  :class="{ 'select-error': form.errors.jenis_kelamin }"
                  required
                >
                  <option value="">Pilih jenis kelamin</option>
                  <option value="Laki-laki">Laki-laki</option>
                  <option value="Perempuan">Perempuan</option>
                </select>
                <div v-if="form.errors.jenis_kelamin" class="label">
                  <span class="label-text-alt text-error">{{ form.errors.jenis_kelamin }}</span>
                </div>
              </div>

              <!-- Nomor Telepon -->
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Nomor Telepon <span class="text-error">*</span></span>
                  <span class="label-text-alt">{{ (form.nomor_telepon || '').length }}/15 karakter</span>
                </label>
                <input
                  v-model="form.nomor_telepon"
                  type="tel"
                  placeholder="08xxxxxxxxxx"
                  class="input input-bordered"
                  :class="{ 'input-error': form.errors.nomor_telepon }"
                  maxlength="15"
                  required
                />
                <div v-if="form.errors.nomor_telepon" class="label">
                  <span class="label-text-alt text-error">{{ form.errors.nomor_telepon }}</span>
                </div>
              </div>

              <!-- Email -->
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Email</span>
                  <span class="label-text-alt">{{ (form.email || '').length }}/100 karakter</span>
                </label>
                <input
                  v-model="form.email"
                  type="email"
                  placeholder="nama@email.com"
                  class="input input-bordered"
                  :class="{ 'input-error': form.errors.email }"
                  maxlength="100"
                />
                <div v-if="form.errors.email" class="label">
                  <span class="label-text-alt text-error">{{ form.errors.email }}</span>
                </div>
              </div>

              <!-- Alamat -->
              <div class="form-control md:col-span-2">
                <label class="label">
                  <span class="label-text">Alamat <span class="text-error">*</span></span>
                  <span class="label-text-alt">{{ (form.alamat || '').length }}/500 karakter</span>
                </label>
                <textarea
                  v-model="form.alamat"
                  placeholder="Masukkan alamat lengkap"
                  class="textarea textarea-bordered h-24"
                  :class="{ 'textarea-error': form.errors.alamat }"
                  maxlength="500"
                  required
                ></textarea>
                <div v-if="form.errors.alamat" class="label">
                  <span class="label-text-alt text-error">{{ form.errors.alamat }}</span>
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-4 pt-6">
              <button 
                type="button"
                @click="confirmCancel"
                class="btn btn-outline"
              >
                Batal
              </button>
              <button 
                type="submit" 
                class="btn btn-primary"
                :class="{ 'loading': form.processing }"
                :disabled="form.processing"
              >
                <span v-if="!form.processing">Simpan Perubahan</span>
                <span v-else>Menyimpan...</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div v-if="showCancelModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg">Konfirmasi Pembatalan</h3>
        <p class="py-4">
          Apakah Anda yakin ingin membatalkan perubahan? 
          Data yang telah diubah akan hilang.
        </p>
        <div class="modal-action">
          <button @click="showCancelModal = false" class="btn">Tidak</button>
          <button @click="cancelForm" class="btn btn-error">Ya, Batalkan</button>
        </div>
      </div>
    </div>
  </SimpleLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import SimpleLayout from '~/Layouts/SimpleLayout.vue'

const props = defineProps({
  patient: Object,
})

const showCancelModal = ref(false)

const today = computed(() => {
  return new Date().toISOString().split('T')[0]
})

const form = useForm({
  nama_lengkap: props.patient.nama_lengkap,
  tanggal_lahir: props.patient.tanggal_lahir,
  jenis_kelamin: props.patient.jenis_kelamin,
  alamat: props.patient.alamat,
  nomor_telepon: props.patient.nomor_telepon,
  email: props.patient.email || '',
})

const submit = () => {
  form.put(`/patients/${props.patient.id}`, {
    onSuccess: () => {
      // Will redirect to patients.index via controller
    },
  })
}

const confirmCancel = () => {
  // Check if form has any changes
  const hasChanges = form.nama_lengkap !== props.patient.nama_lengkap ||
                     form.tanggal_lahir !== props.patient.tanggal_lahir ||
                     form.jenis_kelamin !== props.patient.jenis_kelamin ||
                     form.alamat !== props.patient.alamat ||
                     form.nomor_telepon !== props.patient.nomor_telepon ||
                     form.email !== (props.patient.email || '')

  if (hasChanges) {
    showCancelModal.value = true
  } else {
    cancelForm()
  }
}

const cancelForm = () => {
  showCancelModal.value = false
  router.visit('/patients')
}
</script>
