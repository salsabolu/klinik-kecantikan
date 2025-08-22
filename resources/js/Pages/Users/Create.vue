<template>
  <AppLayout title="Tambah Pengguna">
    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold">Tambah Pengguna Baru</h2>
              <button @click="handleBack" type="button" class="btn btn-outline">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali
              </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Nama Lengkap <span class="text-error">*</span></span>
                </label>
                <input 
                  v-model="form.name"
                  type="text" 
                  placeholder="Masukkan nama lengkap" 
                  class="input input-bordered"
                  :class="{ 'input-error': form.errors.name }"
                  required
                />
                <div v-if="form.errors.name" class="label">
                  <span class="label-text-alt text-error">{{ form.errors.name }}</span>
                </div>
              </div>

              <div class="form-control">
                <label class="label">
                  <span class="label-text">Email <span class="text-error">*</span></span>
                </label>
                <input 
                  v-model="form.email"
                  type="email" 
                  placeholder="Masukkan email" 
                  class="input input-bordered"
                  :class="{ 'input-error': form.errors.email }"
                  required
                />
                <div v-if="form.errors.email" class="label">
                  <span class="label-text-alt text-error">{{ form.errors.email }}</span>
                </div>
              </div>

              <div class="form-control">
                <label class="label">
                  <span class="label-text">Role <span class="text-error">*</span></span>
                </label>
                <select 
                  v-model="form.role"
                  class="select select-bordered"
                  :class="{ 'select-error': form.errors.role }"
                  required
                >
                  <option value="">Pilih Role</option>
                  <option value="admin">Administrator</option>
                  <option value="resepsionis">Resepsionis</option>
                  <option value="dokter">Dokter/Terapis</option>
                  <option value="manajer_stok">Manajer Stok</option>
                  <option value="pasien">Pasien</option>
                </select>
                <div v-if="form.errors.role" class="label">
                  <span class="label-text-alt text-error">{{ form.errors.role }}</span>
                </div>
              </div>

              <div class="form-control">
                <label class="label">
                  <span class="label-text">Password <span class="text-error">*</span></span>
                </label>
                <input 
                  v-model="form.password"
                  type="password" 
                  placeholder="Masukkan password" 
                  class="input input-bordered"
                  :class="{ 'input-error': form.errors.password }"
                  required
                />
                <div v-if="form.errors.password" class="label">
                  <span class="label-text-alt text-error">{{ form.errors.password }}</span>
                </div>
              </div>

              <div class="form-control">
                <label class="label">
                  <span class="label-text">Konfirmasi Password <span class="text-error">*</span></span>
                </label>
                <input 
                  v-model="form.password_confirmation"
                  type="password" 
                  placeholder="Konfirmasi password" 
                  class="input input-bordered"
                  :class="{ 'input-error': form.errors.password_confirmation }"
                  required
                />
                <div v-if="form.errors.password_confirmation" class="label">
                  <span class="label-text-alt text-error">{{ form.errors.password_confirmation }}</span>
                </div>
              </div>

              <!-- Patient-specific fields -->
              <template v-if="form.role === 'pasien'">
                <div class="divider">Data Pasien</div>
                
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

                <div class="form-control">
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
              </template>

              <!-- Action Buttons -->
              <div class="flex justify-end space-x-3 pt-6">
                <button @click="handleCancel" type="button" class="btn btn-outline">
                  Batal
                </button>
                <button 
                  type="submit" 
                  class="btn btn-primary"
                  :disabled="form.processing"
                >
                  <span v-if="form.processing" class="loading loading-spinner loading-xs"></span>
                  {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                </button>
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
          Anda telah mengisi beberapa data pada form ini. Jika Anda keluar sekarang, data yang telah diisi tidak akan tersimpan.
        </p>
        <p class="text-sm text-base-content/60 mb-6">
          Apakah Anda yakin ingin kembali tanpa menyimpan data?
        </p>
        <div class="modal-action">
          <button @click="showBackModal = false" class="btn btn-outline">Tetap di Form</button>
          <Link :href="route('users.index')" class="btn btn-error">Ya, Kembali</Link>
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
          Anda telah mengisi beberapa data pada form ini. Jika Anda membatalkan sekarang, data yang telah diisi tidak akan tersimpan.
        </p>
        <p class="text-sm text-base-content/60 mb-6">
          Apakah Anda yakin ingin membatalkan tanpa menyimpan data?
        </p>
        <div class="modal-action">
          <button @click="showCancelModal = false" class="btn btn-outline">Tetap di Form</button>
          <Link :href="route('users.index')" class="btn btn-error">Ya, Batal</Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed, ref, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  components: {
    Head,
    Link,
    AppLayout,
  },
  setup() {
    const showBackModal = ref(false)
    const showCancelModal = ref(false)
    const originalFormData = ref({})
    
    const form = useForm({
      name: '',
      email: '',
      role: '',
      password: '',
      password_confirmation: '',
      // Patient fields
      tanggal_lahir: '',
      jenis_kelamin: '',
      nomor_telepon: '',
      alamat: '',
    })

    // Store original form state for change detection
    onMounted(() => {
      originalFormData.value = JSON.parse(JSON.stringify(form.data()))
    })

    // Check if form has changes
    const hasFormData = computed(() => {
      const current = form.data()
      const original = originalFormData.value
      
      // Check if any field has been filled
      return current.name !== original.name ||
             current.email !== original.email ||
             current.role !== original.role ||
             current.password !== original.password ||
             current.password_confirmation !== original.password_confirmation ||
             current.tanggal_lahir !== original.tanggal_lahir ||
             current.jenis_kelamin !== original.jenis_kelamin ||
             current.nomor_telepon !== original.nomor_telepon ||
             current.alamat !== original.alamat
    })

    const today = computed(() => {
      return new Date().toISOString().split('T')[0]
    })

    const handleBack = () => {
      if (hasFormData.value) {
        showBackModal.value = true
      } else {
        window.location.href = route('users.index')
      }
    }

    const handleCancel = () => {
      if (hasFormData.value) {
        showCancelModal.value = true
      } else {
        window.location.href = route('users.index')
      }
    }

    const submit = () => {
      form.post(route('users.store'), {
        onSuccess: () => {
          // Redirect handled by controller
        }
      })
    }

    return {
      form,
      submit,
      today,
      showBackModal,
      showCancelModal,
      hasFormData,
      handleBack,
      handleCancel,
    }
  }
}
</script>
