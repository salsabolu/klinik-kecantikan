<template>
  <Head title="Register" />
  <!-- Navigation Bar -->
  <div class="navbar fixed z-50 bg-primary-bg border-b border-clinic-green px-6 lg:px-12 py-2 lg:py-4">
    <div class="navbar-start">
      <div class="flex items-center space-x-4">
        <!-- Logo -->
        <Link href="/">
        <div class="flex lg:flex-row items-center gap-3">
          <img src="/Logo-Klinik.svg" alt="Esthetician Clinique" class="h-10 w-10" />
          <h1 class="text-2xl font-serif font-normal text-clinic-green hidden md:block">Esthetician Clinique</h1>
        </div>
        </Link>
      </div>
    </div>

    <div class="navbar-end">
      <Link href="/login" class="btn btn-outline btn-primary btn-sm md:btn-md">
      Masuk
      </Link>
    </div>
  </div>

  <div class="min-h-screen flex justify-center items-center bg-primary-bg pb-10 pt-24 px-8 md:px-0">
    <div class="card w-96 bg-white shadow-lg">
      <div class="card-body">
        <h2 class="text-primary hover:no-underline text-2xl lg:text-3xl text-center pb-4">
          Daftar Akun Baru
        </h2>

        <form @submit.prevent="submit" class="space-y-2">
          <div class="form-control">
            <label class="label">
              <span class="label-text">Nama Lengkap</span>
            </label>
            <input v-model="form.name" type="text" placeholder="Masukkan nama lengkap"
              class="w-full input input-bordered" :class="{ 'input-error': form.errors.name }" required />
            <div v-if="form.errors.name" class="label">
              <span class="label-text-alt text-error">{{ form.errors.name }}</span>
            </div>
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text">Email</span>
            </label>
            <input v-model="form.email" type="email" placeholder="nama@email.com" class="w-full input input-bordered"
              :class="{ 'input-error': form.errors.email }" required />
            <div v-if="form.errors.email" class="label">
              <span class="label-text-alt text-error">{{ form.errors.email }}</span>
            </div>
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text">Role</span>
            </label>
            <select v-model="form.role" class="w-full select select-bordered"
              :class="{ 'select-error': form.errors.role }" required>
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

          <!-- Patient-specific fields -->
          <template v-if="form.role === 'pasien'">
            <div class="form-control">
              <label class="label">
                <span class="label-text">Tanggal Lahir <span class="text-error">*</span></span>
              </label>
              <input v-model="form.tanggal_lahir" type="date" :max="today" class="w-full input input-bordered"
                :class="{ 'input-error': form.errors.tanggal_lahir }" required />
              <div v-if="form.errors.tanggal_lahir" class="label">
                <span class="label-text-alt text-error">{{ form.errors.tanggal_lahir }}</span>
              </div>
            </div>

            <div class="form-control">
              <label class="label">
                <span class="label-text">Jenis Kelamin <span class="text-error">*</span></span>
              </label>
              <select v-model="form.jenis_kelamin" class="w-full select select-bordered"
                :class="{ 'select-error': form.errors.jenis_kelamin }" required>
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
              <input v-model="form.nomor_telepon" type="tel" placeholder="08xxxxxxxxxx"
                class="w-full input input-bordered" :class="{ 'input-error': form.errors.nomor_telepon }" maxlength="15"
                required />
              <div v-if="form.errors.nomor_telepon" class="label">
                <span class="label-text-alt text-error">{{ form.errors.nomor_telepon }}</span>
              </div>
            </div>

            <div class="form-control">
              <label class="label">
                <span class="label-text">Alamat <span class="text-error">*</span></span>
                <span class="label-text-alt">{{ form.alamat.length }}/500 karakter</span>
              </label>
              <textarea v-model="form.alamat" placeholder="Masukkan alamat lengkap"
                class="w-full textarea textarea-bordered h-24" :class="{ 'textarea-error': form.errors.alamat }"
                maxlength="500" required></textarea>
              <div v-if="form.errors.alamat" class="label">
                <span class="label-text-alt text-error">{{ form.errors.alamat }}</span>
              </div>
            </div>
          </template>

          <div class="form-control">
            <label class="label">
              <span class="label-text">Password</span>
            </label>
            <div class="relative">
              <input v-model="form.password" :type="showPassword ? 'text' : 'password'" placeholder="••••••••"
                class="w-full input input-bordered" :class="{ 'input-error': form.errors.password }" required />
              <button type="button" @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <PhEye v-if="showPassword" :size="24" color="grey" weight="regular" />
                <PhEyeClosed v-else :size="24" color="grey" weight="regular" />
              </button>
            </div>
            <div v-if="form.errors.password" class="label">
              <span class="label-text-alt text-error">{{ form.errors.password }}</span>
            </div>
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text">Konfirmasi Password</span>
            </label>
            <div class="relative">
              <input v-model="form.password_confirmation" :type="showPasswordConfirmation ? 'text' : 'password'"
                placeholder="••••••••" class="w-full input input-bordered"
                :class="{ 'input-error': form.errors.password_confirmation }" required />
              <button type="button" @click="showPasswordConfirmation = !showPasswordConfirmation"
                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <PhEye v-if="showPasswordConfirmation" :size="24" color="grey" weight="regular" />
                <PhEyeClosed v-else :size="24" color="grey" weight="regular" />
              </button>
            </div>
            <div v-if="form.errors.password_confirmation" class="label">
              <span class="label-text-alt text-error">{{ form.errors.password_confirmation }}</span>
            </div>
          </div>

          <div class="form-control mt-6">
            <button type="submit" class="w-full btn btn-primary btn-sm md:btn-md" :disabled="form.processing">
              <span v-if="form.processing" class="loading loading-spinner loading-sm mr-2"></span>
              {{ form.processing ? 'Memproses...' : 'Daftar' }}
            </button>
          </div>
        </form>

        <div class="divider">ATAU</div>

        <div class="text-center">
          <p class="text-sm">
            Sudah punya akun?
            <Link href="/login" class="link link-primary">Masuk di sini</Link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { PhEye, PhEyeClosed } from '@phosphor-icons/vue'

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

const showPassword = ref(false)
const showPasswordConfirmation = ref(false)

const today = computed(() => {
  return new Date().toISOString().split('T')[0]
})

const submit = () => {
  form.post('/register', {
    onFinish: () => {
      form.reset('password', 'password_confirmation')
    },
  })
}
</script>
