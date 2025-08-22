<template>
  <AppLayout>
    <Head title="Produk" />
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-semibold">Daftar Produk</h1>
          <p class="text-gray-600">Kelola produk klinik kecantikan</p>
        </div>
        <Link v-if="canAccess(['admin', 'manajer_stok'])" href="/products/create" class="btn btn-primary">
        <PhPlus :size="20" color="black" weight="regular" />
        Tambah Produk
        </Link>
      </div>

      <!-- Filters -->
      <div class="card bg-white shadow-xl">
        <div class="card-body">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div class="form-control">
              <label class="label">
                <span class="label-text">Pencarian</span>
              </label>
              <div class="relative">
                <input v-model="filters.search" type="text" placeholder="Nama produk atau deskripsi..."
                  class="input input-bordered w-full" @input="search" />
                <button v-if="filters.search" @click="clearSearch"
                  class="absolute inset-y-0 right-0 pr-3 flex items-center">
                  <PhX :size="20" color="grey" weight="regular" />
                </button>
              </div>
            </div>

            <!-- Status Stok -->
            <div class="form-control">
              <label class="label">
                <span class="label-text">Status Stok</span>
              </label>
              <select v-model="filters.stok_status" class="select select-bordered" @change="search">
                <option value="">Semua</option>
                <option value="available">Tersedia (>0)</option>
                <option value="low">Stok Rendah (≤10)</option>
                <option value="empty">Habis (0)</option>
              </select>
            </div>

            <ResetFilterButton @reset="clearFilters" />
          </div>
        </div>
      </div>

      <!-- Products Table -->
      <div class="card bg-white shadow-xl">
        <div class="card-body">
          <div class="overflow-x-auto">
            <table class="table table-zebra">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Produk</th>
                  <th>Deskripsi</th>
                  <th>Harga</th>
                  <th>Stok</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(product, index) in products.data" :key="product.id" @click="navigateToProduct(product.id)"
                  class="cursor-pointer hover:bg-base-300 transition-colors">
                  <td>{{ getRowNumber(index) }}</td>
                  <td>
                    <div class="font-semibold max-w-28">{{ product.nama_produk }}</div>
                  </td>
                  <td>
                    <div class="text-sm max-w-xs truncate">
                      {{ product.deskripsi || '-' }}
                    </div>
                  </td>
                  <td>
                    <div class="font-medium">
                      Rp {{ formatCurrency(product.harga) }}
                    </div>
                  </td>
                  <td>
                    <div class="flex items-center gap-2">
                      <div class="badge" :class="getStockBadgeClass(product.stok)">
                        {{ product.stok }}
                      </div>
                      <button @click.stop="showStockModal(product)" class="btn btn-outline btn-xs"
                        title="Sesuaikan Stok">
                        <PhPlus :size="12" color="black" weight="bold" />
                      </button>
                    </div>
                  </td>
                  <td @click.stop>
                    <div class="dropdown dropdown-left">
                      <label tabindex="0" class="btn btn-circle btn-ghost btn-sm">
                        <PhDotsThreeVertical :size="20" color="black" weight="bold" />
                      </label>
                      <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-white rounded-box w-40">
                        <li>
                          <Link :href="`/products/${product.id}`">
                          <PhEye :size="16" color="black" weight="regular" />
                          Detail
                          </Link>
                        </li>
                        <li v-if="canAccess(['admin', 'manajer_stok'])">
                          <Link :href="`/products/${product.id}/edit`">
                          <PhNotePencil :size="16" color="black" weight="regular" />
                          Edit
                          </Link>
                        </li>
                        <li v-if="canAccess(['admin', 'manajer_stok'])">
                          <button @click="confirmDelete(product)" class="text-error">
                            <PhTrashSimple :size="16" color="currentColor" weight="regular" />
                            Hapus
                          </button>
                        </li>
                      </ul>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="products.last_page > 1" class="flex justify-center mt-8">
            <div class="btn-group">
              <Link v-for="page in paginationPages" :key="page" :href="`/products?page=${page}`" class="btn btn-sm"
                :class="{ 'btn-active': page === products.current_page }">
              {{ page }}
              </Link>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="products.data.length === 0" class="flex flex-col justify-center items-center text-center py-12">
            <PhCube :size="80" color="black" weight="regular" />
            <p class="text-lg font-medium text-gray-600">Tidak ada produk</p>
            <p class="text-base text-gray-600 mb-4">Mulai dengan menambahkan produk baru.</p>
            <Link href="/products/create" class="btn btn-primary">
            Tambah Produk
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="modal modal-open">
      <div class="modal-box">
        <p class="text-lg font-semibold">Konfirmasi Hapus</p>
        <p class="mt-4">
          Apakah Anda yakin ingin menghapus produk "<strong>{{ productToDelete?.nama_produk }}</strong>"?
          Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="modal-action">
          <button @click="showDeleteModal = false" class="btn btn-outline">Batal</button>
          <button @click="deleteProduct" class="btn btn-error">Hapus</button>
        </div>
      </div>
    </div>

    <!-- Stock Adjustment Modal -->
    <div v-if="showStockAdjustment" class="modal modal-open">
      <div class="modal-box">
        <p class="text-lg font-semibold">Sesuaikan Stok</p>
        <p class="mt-2">Produk: {{ selectedProduct?.nama_produk }}</p>
        <p class="mb-4">Stok saat ini: {{ selectedProduct?.stok }}</p>

        <div class="form-control space-x-2">
          <label class="label">
            <span class="label-text">Jumlah Stok Baru</span>
          </label>
          <input v-model="stockForm.new_stock" type="number" min="0" class="input input-bordered"
            placeholder="Masukkan jumlah stok baru" />
        </div>

        <div class="modal-action">
          <button @click="closeStockModal" class="btn btn-outline">Batal</button>
          <button @click="adjustStock" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ResetFilterButton from '@/Components/ResetFilterButton.vue'
import { debounce } from 'lodash'
import { PhPlus, PhX, PhCube, PhDotsThreeVertical, PhEye, PhNotePencil, PhTrashSimple } from '@phosphor-icons/vue'

const props = defineProps({
  products: Object,
  filters: Object,
  auth: Object,
})

const canAccess = (roles) => {
  if (!props.auth?.user?.role) return false
  return roles.includes(props.auth.user.role)
}

// Reactive filters
const filters = ref({
  search: props.filters.search || '',
  stok_status: props.filters.stok_status || '',
})

// Modal states
const showDeleteModal = ref(false)
const showStockAdjustment = ref(false)
const productToDelete = ref(null)
const selectedProduct = ref(null)

// Stock adjustment form
const stockForm = ref({
  new_stock: ''
})

// Debounced search function
const search = debounce(() => {
  router.get('/products', filters.value, {
    preserveState: true,
    replace: true,
  })
}, 300)

// Clear search
const clearSearch = () => {
  filters.value.search = ''
  search()
}

// Clear all filters
const clearFilters = () => {
  filters.value = {
    search: '',
    stok_status: '',
  }
  search()
}

// Format currency
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID').format(amount)
}

// Get row number for pagination
const getRowNumber = (index) => {
  return (props.products.current_page - 1) * props.products.per_page + index + 1
}

// Navigate to product detail
const navigateToProduct = (id) => {
  router.get(`/products/${id}`)
}

// Get stock badge class
const getStockBadgeClass = (stock) => {
  if (stock === 0) return 'badge-error'
  if (stock <= 10) return 'badge-warning'
  return 'badge-success'
}

// Pagination pages computation
const paginationPages = computed(() => {
  const pages = []
  const current = props.products.current_page
  const last = props.products.last_page

  // Always show first page
  if (current > 3) pages.push(1)
  if (current > 4) pages.push('...')

  // Show pages around current page
  for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
    pages.push(i)
  }

  // Show last page
  if (current < last - 3) pages.push('...')
  if (current < last - 2) pages.push(last)

  return pages.filter((page, index, array) => array.indexOf(page) === index)
})

// Delete confirmation
const confirmDelete = (product) => {
  productToDelete.value = product
  showDeleteModal.value = true
}

// Delete product
const deleteProduct = () => {
  if (productToDelete.value) {
    router.delete(`/products/${productToDelete.value.id}`, {
      onSuccess: () => {
        showDeleteModal.value = false
        productToDelete.value = null
      }
    })
  }
}

// Show stock modal
const showStockModal = (product) => {
  selectedProduct.value = product
  stockForm.value.new_stock = product.stok
  showStockAdjustment.value = true
}

// Close stock modal
const closeStockModal = () => {
  showStockAdjustment.value = false
  selectedProduct.value = null
  stockForm.value = {
    new_stock: ''
  }
}

// Adjust stock
const adjustStock = () => {
  if (selectedProduct.value && stockForm.value.new_stock >= 0) {
    const oldStock = selectedProduct.value.stok
    const newStock = stockForm.value.new_stock

    if (confirm(`Apakah Anda yakin ingin mengubah stok dari ${oldStock} menjadi ${newStock}?`)) {
      router.put(`/products/${selectedProduct.value.id}/adjust-stock`, {
        new_stock: newStock
      }, {
        onSuccess: () => {
          closeStockModal()
          // Show success message
          alert(`Stok berhasil diubah dari ${oldStock} menjadi ${newStock}`)
        }
      })
    }
  }
}
</script>
