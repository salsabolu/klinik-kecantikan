<template>
  <SimpleLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-base-content">Tambah Pasien Baru</h1>
          <p class="text-base-content/60 mt-1">Daftarkan pasien baru ke sistem</p>
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
          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Nama Lengkap -->
              <div class="form-control md:col-span-2">
                <label class="label">
                  <span class="label-text">Nama Lengkap <span class="text-error">*</span></span>
                </label>
                <input
                  v-model="form.nama_lengkap"
                  type="text"
                  placeholder="Masukkan nama lengkap pasien"
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
                  class="input input-bordered"
                  :class="{ 'input-error': form.errors.tanggal_lahir }"
                  :max="today"
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
                  <option value="">Pilih Jenis Kelamin</option>
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
                  <span class="label-text-alt">{{ form.nomor_telepon.length }}/15 karakter</span>
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
                  <span class="label-text-alt">{{ form.email.length }}/100 karakter</span>
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
                  <span class="label-text-alt">{{ form.alamat.length }}/500 karakter</span>
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

              <!-- Divider -->
              <div class="divider md:col-span-2">Pengaturan Akun</div>

              <!-- Create User Account -->
              <div class="form-control md:col-span-2">
                <label class="label cursor-pointer">
                  <span class="label-text">Buat akun pengguna untuk pasien ini</span>
                  <input
                    v-model="form.create_user_account"
                    type="checkbox"
                    class="checkbox checkbox-primary"
                  />
                </label>
                <div class="label">
                  <span class="label-text-alt text-base-content/60">
                    Jika dicentang, pasien dapat login untuk melihat riwayat dan membuat reservasi online
                  </span>
                </div>
              </div>

              <!-- User Password -->
              <div v-if="form.create_user_account" class="form-control md:col-span-2">
                <label class="label">
                  <span class="label-text">Password Akun <span class="text-error">*</span></span>
                  <span class="label-text-alt">{{ form.user_password.length }}/20 karakter</span>
                </label>
                <div class="relative">
                  <input
                    v-model="form.user_password"
                    :type="showPassword ? 'text' : 'password'"
                    placeholder="Minimal 8 karakter, maksimal 20 karakter"
                    class="input input-bordered w-full pr-12"
                    :class="{ 'input-error': form.errors.user_password }"
                    minlength="8"
                    maxlength="20"
                    pattern="^[a-zA-Z0-9@$!%*?&_-]+$"
                    :required="form.create_user_account"
                  />
                  <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                  >
                    <svg v-if="showPassword" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                    </svg>
                    <svg v-else class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.543 7-1.275 4.057-5.065 7-9.543 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                  </button>
                </div>
                <div class="label">
                  <span class="label-text-alt text-base-content/60">
                    Password hanya boleh mengandung huruf, angka, dan karakter khusus (@$!%*?&_-)
                  </span>
                </div>
                <div v-if="form.errors.user_password" class="label">
                  <span class="label-text-alt text-error">{{ form.errors.user_password }}</span>
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="form-control mt-8">
              <div class="flex gap-4">
                <button
                  type="submit"
                  class="btn btn-primary flex-1"
                  :class="{ 'loading': form.processing }"
                  :disabled="form.processing"
                >
                  <span v-if="!form.processing">Simpan Data Pasien</span>
                  <span v-else>Menyimpan...</span>
                </button>
                <button
                  type="button"
                  @click="confirmCancel"
                  class="btn btn-outline"
                  :disabled="form.processing"
                >
                  Batal
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Email Notification Info -->
      <div v-if="form.create_user_account" class="alert alert-info">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span>Email notifikasi pembuatan akun akan dikirim ke alamat email pasien setelah data berhasil disimpan.</span>
      </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div class="modal" :class="{ 'modal-open': showCancelModal }">
      <div class="modal-box">
        <h3 class="font-bold text-lg">Konfirmasi Pembatalan</h3>
        <p class="py-4">
          Apakah Anda yakin ingin membatalkan pendaftaran pasien? 
          Data yang telah diisi akan hilang.
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
import { Link, useForm, router } from '@inertiajs/vue3'
import SimpleLayout from '../../Layouts/SimpleLayout.vue'

const showPassword = ref(false)
const showCancelModal = ref(false)

const today = computed(() => {
  return new Date().toISOString().split('T')[0]
})

const form = useForm({
  nama_lengkap: '',
  tanggal_lahir: '',
  jenis_kelamin: '',
  alamat: '',
  nomor_telepon: '',
  email: '',
  create_user_account: false,
  user_password: '',
})

const submit = () => {
  form.post('/patients', {
    onSuccess: () => {
      // Will redirect to patients.index via controller
    },
  })
}

const confirmCancel = () => {
  // Check if form has any data
  const hasData = form.nama_lengkap || form.tanggal_lahir || form.jenis_kelamin || 
                  form.alamat || form.nomor_telepon || form.email || form.user_password

  if (hasData) {
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
