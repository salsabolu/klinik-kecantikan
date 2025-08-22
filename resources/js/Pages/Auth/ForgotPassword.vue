<template>

  <Head title="Lupa Password" />
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

  <div class="min-h-screen flex justify-center items-center bg-primary-bg px-8 md:px-0">
    <div class="card w-96 bg-white shadow-lg">
      <div class="card-body">
        <h2 class="text-primary hover:no-underline text-2xl lg:text-3xl text-center pb-4">
          Lupa Password
        </h2>

        <p class="text-center text-gray-600 mb-4">
          Masukkan email Anda dan kami akan mengirimkan link untuk reset password.
        </p>

        <!-- Success Message -->
        <div v-if="$page.props.status" class="alert alert-success mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>{{ $page.props.status }}</span>
        </div>

        <form @submit.prevent="submit">
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

          <div class="form-control mt-2">
            <button type="submit" class="w-full btn btn-primary btn-sm md:btn-md" :disabled="form.processing">
              <span v-if="form.processing" class="loading loading-spinner loading-sm mr-2"></span>
              {{ form.processing ? 'Memproses...' : 'Kirim Link Reset' }}
            </button>
          </div>
        </form>

        <div class="divider">ATAU</div>

        <div class="text-center">
          <p class="text-sm">
            Sudah ingat password?
            <Link href="/login" class="link link-primary">Kembali ke Login</Link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  email: '',
})

const submit = () => {
  form.post('/forgot-password', {
    onFinish: () => {
      // Don't reset email on error so user doesn't have to retype
      if (!form.hasErrors) {
        form.reset('email')
      }
    },
  })
}
</script>
