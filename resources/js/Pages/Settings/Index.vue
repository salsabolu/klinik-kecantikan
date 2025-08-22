<template>
  <PasienLayout>
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-serif font-bold text-clinic-green">Pengaturan Akun</h1>
        <p class="text-gray-600 mt-2 font-sans">Kelola pengaturan akun dan keamanan Anda</p>
      </div>

      <!-- Settings Cards -->
      <div class="grid gap-6">
        <!-- Change Password Card -->
        <div class="card bg-white shadow-lg">
          <div class="card-body">
            <h2 class="card-title text-clinic-green font-serif">Ubah Password</h2>
            <p class="text-gray-600 mb-6">Pastikan akun Anda menggunakan password yang kuat dan aman</p>
            
            <form @submit.prevent="updatePassword" class="space-y-4">
              <!-- Current Password -->
              <div class="form-control">
                <label class="label">
                  <span class="label-text font-semibold">Password Saat Ini</span>
                </label>
                <div class="relative">
                  <input
                    v-model="form.current_password"
                    :type="showCurrentPassword ? 'text' : 'password'"
                    class="input input-bordered w-full pr-12"
                    :class="{'input-error': form.errors.current_password}"
                    placeholder="Masukkan password saat ini"
                    required
                  />
                  <button
                    type="button"
                    @click="showCurrentPassword = !showCurrentPassword"
                    class="btn btn-ghost btn-sm absolute right-1 top-1/2 -translate-y-1/2"
                  >
                    <svg v-if="!showCurrentPassword" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464M14.12 14.12l1.415 1.415M14.12 14.12L9.878 9.878" />
                    </svg>
                  </button>
                </div>
                <label v-if="form.errors.current_password" class="label">
                  <span class="label-text-alt text-error">{{ form.errors.current_password }}</span>
                </label>
              </div>

              <!-- New Password -->
              <div class="form-control">
                <label class="label">
                  <span class="label-text font-semibold">Password Baru</span>
                </label>
                <div class="relative">
                  <input
                    v-model="form.new_password"
                    :type="showNewPassword ? 'text' : 'password'"
                    class="input input-bordered w-full pr-12"
                    :class="{'input-error': form.errors.new_password}"
                    placeholder="Masukkan password baru (min. 8 karakter)"
                    required
                    minlength="8"
                  />
                  <button
                    type="button"
                    @click="showNewPassword = !showNewPassword"
                    class="btn btn-ghost btn-sm absolute right-1 top-1/2 -translate-y-1/2"
                  >
                    <svg v-if="!showNewPassword" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464M14.12 14.12l1.415 1.415M14.12 14.12L9.878 9.878" />
                    </svg>
                  </button>
                </div>
                <label v-if="form.errors.new_password" class="label">
                  <span class="label-text-alt text-error">{{ form.errors.new_password }}</span>
                </label>
              </div>

              <!-- Confirm New Password -->
              <div class="form-control">
                <label class="label">
                  <span class="label-text font-semibold">Konfirmasi Password Baru</span>
                </label>
                <div class="relative">
                  <input
                    v-model="form.new_password_confirmation"
                    :type="showConfirmPassword ? 'text' : 'password'"
                    class="input input-bordered w-full pr-12"
                    :class="{'input-error': form.errors.new_password_confirmation || !passwordsMatch}"
                    placeholder="Konfirmasi password baru"
                    required
                  />
                  <button
                    type="button"
                    @click="showConfirmPassword = !showConfirmPassword"
                    class="btn btn-ghost btn-sm absolute right-1 top-1/2 -translate-y-1/2"
                  >
                    <svg v-if="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464M14.12 14.12l1.415 1.415M14.12 14.12L9.878 9.878" />
                    </svg>
                  </button>
                </div>
                <label v-if="form.errors.new_password_confirmation" class="label">
                  <span class="label-text-alt text-error">{{ form.errors.new_password_confirmation }}</span>
                </label>
                <label v-else-if="!passwordsMatch && form.new_password_confirmation" class="label">
                  <span class="label-text-alt text-error">Password tidak sama</span>
                </label>
              </div>

              <!-- Password Requirements -->
              <div class="bg-base-200 p-4 rounded-lg">
                <h4 class="font-semibold text-sm mb-2">Persyaratan Password:</h4>
                <ul class="text-sm text-gray-600 space-y-1">
                  <li class="flex items-center gap-2">
                    <svg :class="form.new_password.length >= 8 ? 'text-success' : 'text-gray-400'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Minimal 8 karakter
                  </li>
                  <li class="flex items-center gap-2">
                    <svg :class="/[A-Z]/.test(form.new_password) ? 'text-success' : 'text-gray-400'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Mengandung huruf besar
                  </li>
                  <li class="flex items-center gap-2">
                    <svg :class="/[a-z]/.test(form.new_password) ? 'text-success' : 'text-gray-400'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Mengandung huruf kecil
                  </li>
                  <li class="flex items-center gap-2">
                    <svg :class="/[0-9]/.test(form.new_password) ? 'text-success' : 'text-gray-400'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Mengandung angka
                  </li>
                </ul>
              </div>

              <!-- Submit Button -->
              <div class="card-actions justify-end pt-4">
                <button 
                  type="button" 
                  @click="resetForm"
                  class="btn btn-ghost"
                  :disabled="form.processing"
                >
                  Reset
                </button>
                <button 
                  type="submit" 
                  class="btn btn-primary"
                  :disabled="form.processing || !passwordsMatch || !isFormValid"
                >
                  <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                  {{ form.processing ? 'Memproses...' : 'Ubah Password' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Account Information Card -->
        <div class="card bg-white shadow-lg">
          <div class="card-body">
            <h2 class="card-title text-clinic-green font-serif">Informasi Akun</h2>
            <p class="text-gray-600 mb-4">Detail akun dan profil Anda</p>
            
            <div class="grid md:grid-cols-2 gap-4">
              <div>
                <label class="label">
                  <span class="label-text font-semibold">Nama Lengkap</span>
                </label>
                <input type="text" :value="user?.name" class="input input-bordered w-full" readonly />
              </div>
              
              <div>
                <label class="label">
                  <span class="label-text font-semibold">Email</span>
                </label>
                <input type="email" :value="user?.email" class="input input-bordered w-full" readonly />
              </div>
              
              <div>
                <label class="label">
                  <span class="label-text font-semibold">Role</span>
                </label>
                <input type="text" value="Pasien" class="input input-bordered w-full" readonly />
              </div>
              
              <div>
                <label class="label">
                  <span class="label-text font-semibold">Status Akun</span>
                </label>
                <div class="flex items-center gap-2">
                  <span class="badge badge-success">Aktif</span>
                </div>
              </div>
            </div>
            
            <div class="alert alert-info mt-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <span>Untuk mengubah informasi profil, silakan hubungi resepsionis klinik.</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="modal modal-open">
      <div class="modal-box">
        <div class="flex items-center gap-3 mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <h3 class="font-bold text-lg">Password Berhasil Diubah!</h3>
        </div>
        <p class="py-4">Password Anda telah berhasil diperbarui. Pastikan untuk mengingat password baru Anda.</p>
        <div class="modal-action">
          <button @click="showSuccessModal = false" class="btn btn-primary">OK</button>
        </div>
      </div>
    </div>
  </PasienLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import PasienLayout from '@/Layouts/PasienLayout.vue'

const { props } = usePage()
const user = computed(() => props.auth?.user)

// Form state
const form = useForm({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

// UI state
const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)
const showSuccessModal = ref(false)

// Computed properties
const passwordsMatch = computed(() => {
  return form.new_password === form.new_password_confirmation
})

const isFormValid = computed(() => {
  return form.current_password.length > 0 && 
         form.new_password.length >= 8 && 
         passwordsMatch.value
})

// Methods
const updatePassword = () => {
  form.post('/settings/change-password', {
    onSuccess: () => {
      showSuccessModal.value = true
      resetForm()
    },
    onError: () => {
      // Errors will be handled by Inertia automatically
    }
  })
}

const resetForm = () => {
  form.reset()
  form.clearErrors()
  showCurrentPassword.value = false
  showNewPassword.value = false
  showConfirmPassword.value = false
}
</script>
