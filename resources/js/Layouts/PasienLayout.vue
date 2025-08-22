<template>
    <div class="min-h-screen bg-primary-bg">
        <!-- Horizontal Navigation for Pasien -->
        <div class="navbar bg-primary-bg border-b border-clinic-green px-6 lg:px-12 py-2 lg:py-4">
            <div class="flex-1">
                <div class="flex items-center space-x-1 md:space-x-6">
                    <!-- Logo -->
                    <Link href="/dashboard" class="flex flex-row md:flex-none items-center gap-3">
                    <img src="/Logo-Klinik.svg" alt="Esthetician Clinique" class="h-10 w-10" />
                    <!-- <h1 class="text-2xl font-serif font-normal text-clinic-green">Esthetician Clinique</h1> -->
                    </Link>

                    <!-- Navigation Links -->
                    <div class="hidden lg:flex space-x-6">
                        <Link href="/dashboard"
                            :class="isActiveRoute('dashboard') ? 'text-primary underline' : 'text-primary transition-colors'">
                        Dashboard
                        </Link>
                        <Link href="/my-reservations"
                            :class="isActiveRoute('my-reservations') ? 'text-primary underline' : 'text-primary transition-colors'">
                        Reservasi Saya
                        </Link>
                        <Link href="/services-catalog"
                            :class="isActiveRoute('services-catalog') ? 'text-primary underline' : 'text-primary transition-colors'">
                        Katalog Layanan
                        </Link>
                        <Link href="/products-catalog"
                            :class="isActiveRoute('products-catalog') ? 'text-primary underline' : 'text-primary transition-colors'">
                        Katalog Produk
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Profile Section -->
            <div class="flex items-center">
                <div class="flex-none">
                    <div class="dropdown dropdown-end">
                        <!-- User Info -->
                        <label tabindex="0" class="btn btn-ghost btn-circle avatar">
                            <div
                                class="w-10 rounded-full bg-clinic-yellow text-clinic-green flex items-center justify-center font-serif font-semibold">
                                {{ safeUserInitial }}
                            </div>
                        </label>
                        <ul tabindex="0"
                            class="dropdown-content menu p-2 shadow bg-white rounded-box w-52 border border-gray-200">
                            <li><a class="text-sm font-sans">{{ safeUserName }}</a></li>
                            <li><a class="text-xs text-gray-500 font-sans">{{ getRoleLabel(user?.role) }}</a></li>
                            <li>
                                <hr class="my-1">
                            </li>
                            <!-- Settings -->
                            <li>
                                <Link href="/settings" class="font-sans">
                                <PhGearSix :size="24" color="black" weight="regular" />
                                Pengaturan
                                </Link>
                            </li>
                            <!-- Logout -->
                            <li>
                                <a href="#" @click.prevent="logout" class="flex flex-1 gap-2 text-error">
                                    <PhSignOut :size="24" color="currentColor" weight="regular" />
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="lg:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="btn btn-square btn-ghost">
                    <PhList :size="24" color="black" weight="regular" />
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div v-show="mobileMenuOpen" class="lg:hidden bg-white shadow-md border-t">
            <div class="px-6 py-4 space-y-2">
                <Link href="/dashboard" class="block py-2 text-primary">Dashboard</Link>
                <Link href="/my-reservations" class="block py-2 text-primary">Reservasi Saya</Link>
                <Link href="/services-catalog" class="block py-2 text-primary">Katalog Layanan</Link>
                <Link href="/products-catalog" class="block py-2 text-primary">Katalog Produk</Link>
                <hr class="my-2">
                <Link href="/settings" class="block py-2 text-primary">
                <div class="flex flex-1 gap-2">
                    <PhGearSix :size="24" color="black" weight="regular" />
                    Pengaturan
                </div>
                </Link>
            </div>
        </div>

        <!-- Main Content -->
        <main class="p-12">
            <slot />
        </main>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { PhGearSix, PhList, PhSignOut } from '@phosphor-icons/vue'

const { props } = usePage()
const user = computed(() => props.auth?.user)

const mobileMenuOpen = ref(false)

// Check if route is active
const isActiveRoute = (routeName) => {
    return route().current()?.includes(routeName) ||
        window.location.pathname.includes(routeName)
}

// Safe user data with fallbacks
const safeUserName = computed(() => {
    const userName = user.value?.name
    return userName && userName.length > 0 ? userName : 'User'
})

const safeUserInitial = computed(() => {
    return safeUserName.value.charAt(0).toUpperCase()
})

const getRoleLabel = (role) => {
    const labels = {
        admin: 'Administrator',
        resepsionis: 'Resepsionis',
        dokter: 'Dokter/Terapis',
        manajer_stok: 'Manajer Stok',
        pasien: 'Pasien'
    }
    return labels[role] || role || 'User'
}

// Logout function
const logout = () => {
    router.post('/logout')
}
</script>
