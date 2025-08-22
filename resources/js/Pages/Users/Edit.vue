<template>
  <AppLayout title="Edit Pengguna">
    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold">Edit Pengguna</h2>
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

              <div class="divider">Ubah Password (Opsional)</div>

              <div class="form-control">
                <label class="label">
                  <span class="label-text">Password Baru</span>
                </label>
                <input 
                  v-model="form.password"
                  type="password" 
                  placeholder="Kosongkan jika tidak ingin mengubah password" 
                  class="input input-bordered"
                  :class="{ 'input-error': form.errors.password }"
                />
                <div v-if="form.errors.password" class="label">
                  <span class="label-text-alt text-error">{{ form.errors.password }}</span>
                </div>
              </div>

              <div class="form-control">
                <label class="label">
                  <span class="label-text">Konfirmasi Password Baru</span>
                </label>
                <input 
                  v-model="form.password_confirmation"
                  type="password" 
                  placeholder="Konfirmasi password baru" 
                  class="input input-bordered"
                  :class="{ 'input-error': form.errors.password_confirmation }"
                />
                <div v-if="form.errors.password_confirmation" class="label">
                  <span class="label-text-alt text-error">{{ form.errors.password_confirmation }}</span>
                </div>
              </div>

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
                  {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
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
          Anda telah melakukan beberapa perubahan pada form pengguna ini. Jika Anda keluar sekarang, perubahan tidak akan tersimpan.
        </p>
        <p class="text-sm text-base-content/60 mb-6">
          Apakah Anda yakin ingin kembali tanpa menyimpan perubahan?
        </p>
        <div class="modal-action">
          <button @click="showBackModal = false" class="btn btn-outline">Tetap Edit</button>
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
          Anda telah melakukan beberapa perubahan pada form pengguna ini. Jika Anda membatalkan sekarang, perubahan tidak akan tersimpan.
        </p>
        <p class="text-sm text-base-content/60 mb-6">
          Apakah Anda yakin ingin membatalkan tanpa menyimpan perubahan?
        </p>
        <div class="modal-action">
          <button @click="showCancelModal = false" class="btn btn-outline">Tetap Edit</button>
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
  props: {
    user: Object,
  },
  setup(props) {
    const showBackModal = ref(false)
    const showCancelModal = ref(false)
    const originalFormData = ref({})
    
    const form = useForm({
      name: props.user.name,
      email: props.user.email,
      role: props.user.role,
      password: '',
      password_confirmation: '',
    })

    // Store original form state for change detection
    onMounted(() => {
      originalFormData.value = {
        name: props.user.name,
        email: props.user.email,
        role: props.user.role,
        password: '',
        password_confirmation: '',
      }
    })

    // Check if form has changes
    const hasFormData = computed(() => {
      const current = form.data()
      const original = originalFormData.value
      
      return current.name !== original.name ||
             current.email !== original.email ||
             current.role !== original.role ||
             current.password !== original.password ||
             current.password_confirmation !== original.password_confirmation
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
      form.put(route('users.update', props.user.id), {
        onSuccess: () => {
          // Redirect handled by controller
        }
      })
    }

    return {
      form,
      submit,
      showBackModal,
      showCancelModal,
      hasFormData,
      handleBack,
      handleCancel,
    }
  }
}
</script>
