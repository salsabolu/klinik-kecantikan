<template>

  <Head title="Login" />
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
      <Link href="/register" class="btn btn-outline btn-primary btn-sm md:btn-md">
      Daftar
      </Link>
    </div>
  </div>

  <div class="min-h-screen flex justify-center items-center bg-primary-bg px-8 md:px-0">
    <div class="card w-96 bg-white shadow-lg">
      <div class="card-body">
        <h2 class="text-primary hover:no-underline text-2xl lg:text-3xl text-center pb-4">
          Masuk ke Esthetician Clinique
        </h2>

        <form @submit.prevent="submit" class="space-y-2">
          <div class="form-control">
            <label class="label">
              <span class="label-text">Email</span>
            </label>
            <input v-model="form.email" type="email" placeholder="nama@email.com" class="input input-bordered w-full"
              :class="{ 'input-error': form.errors.email }" required />
            <div v-if="form.errors.email" class="label">
              <span class="label-text-alt text-error">{{ form.errors.email }}</span>
            </div>
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text">Password</span>
            </label>
            <div class="relative">
              <input v-model="form.password" :type="showPassword ? 'text' : 'password'" placeholder="••••••••"
                class="input input-bordered w-full pr-12" :class="{ 'input-error': form.errors.password }" required />
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

          <div class="form-control text-right">
            <div class="label">
              <Link href="/forgot-password" class="label-text-alt link link-hover link-primary">Lupa password?</Link>
            </div>
          </div>

          <div class="form-control pt-2">
            <button type="submit" class="w-full btn btn-primary btn-sm md:btn-md" :disabled="form.processing">
              <span v-if="form.processing" class="loading loading-spinner loading-sm mr-2"></span>
              {{ form.processing ? 'Memproses...' : 'Masuk' }}
            </button>
          </div>
        </form>

        <div class="divider">
          <p>
            ATAU
          </p>
        </div>

        <div class="text-center">
          <p class="text-sm">
            Belum punya akun?
            <Link href="/register" class="link link-primary">Daftar di sini</Link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { PhEye, PhEyeClosed } from '@phosphor-icons/vue'

const form = useForm({
  email: '',
  password: '',
})

const showPassword = ref(false)

const submit = () => {
  form.post('/login', {
    onFinish: () => {
      form.reset('password')
    },
  })
}
</script>
