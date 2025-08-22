<template>
  <AppLayout>
    <Head title="Laporan & Analisis"  />
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 lg:p-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
              <div>
                <h1 class="text-3xl font-bold text-gray-900">Laporan & Analisis</h1>
                <p class="text-gray-600 mt-2">Dashboard laporan untuk analisis bisnis klinik</p>
              </div>
              <div class="flex items-center space-x-3">
                <!-- Export Button -->
                <div class="dropdown dropdown-end">
                  <label tabindex="0" class="btn btn-outline btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export
                  </label>
                  <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                    <li><a @click="exportReport('revenue')">Laporan Pendapatan</a></li>
                    <li><a @click="exportReport('transactions')">Laporan Transaksi</a></li>
                    <li><a @click="exportReport('services')">Laporan Layanan</a></li>
                    <li><a @click="exportReport('products')">Laporan Produk</a></li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Filter Section -->
            <div class="bg-base-200 p-4 rounded-lg mb-8">
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-semibold">Periode</span>
                  </label>
                  <select class="select select-bordered select-sm" v-model="form.period" @change="updateDateRange">
                    <option value="daily">Harian</option>
                    <option value="weekly">Mingguan</option>
                    <option value="monthly">Bulanan</option>
                    <option value="yearly">Tahunan</option>
                    <option value="custom">Kustom</option>
                  </select>
                </div>
                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-semibold">Tanggal Mulai</span>
                  </label>
                  <input type="date" class="input input-bordered input-sm" v-model="form.start_date" />
                </div>
                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-semibold">Tanggal Selesai</span>
                  </label>
                  <input type="date" class="input input-bordered input-sm" v-model="form.end_date" />
                </div>
                <div class="form-control">
                  <label class="label">
                    <span class="label-text">&nbsp;</span>
                  </label>
                  <button type="button" class="btn btn-primary btn-sm" @click="filterReports">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                  </button>
                </div>
              </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
              <!-- Total Revenue -->
              <div class="card bg-gradient-to-br from-blue-500 to-blue-600 text-white">
                <div class="card-body">
                  <div class="flex items-center justify-between">
                    <div>
                      <h3 class="card-title text-sm opacity-90">Total Pendapatan</h3>
                      <p class="text-2xl font-bold">{{ formatCurrency(revenue.total) }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 p-3 rounded-full">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                      </svg>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Total Transactions -->
              <div class="card bg-gradient-to-br from-green-500 to-green-600 text-white">
                <div class="card-body">
                  <div class="flex items-center justify-between">
                    <div>
                      <h3 class="card-title text-sm opacity-90">Total Transaksi</h3>
                      <p class="text-2xl font-bold">{{ transactions.total }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 p-3 rounded-full">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                      </svg>
                    </div>
                  </div>
                </div>
              </div>

              <!-- New Patients -->
              <div class="card bg-gradient-to-br from-purple-500 to-purple-600 text-white">
                <div class="card-body">
                  <div class="flex items-center justify-between">
                    <div>
                      <h3 class="card-title text-sm opacity-90">Pasien Baru</h3>
                      <p class="text-2xl font-bold">{{ patients.new }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 p-3 rounded-full">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Active Patients -->
              <div class="card bg-gradient-to-br from-orange-500 to-orange-600 text-white">
                <div class="card-body">
                  <div class="flex items-center justify-between">
                    <div>
                      <h3 class="card-title text-sm opacity-90">Pasien Aktif</h3>
                      <p class="text-2xl font-bold">{{ patients.active }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 p-3 rounded-full">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
              <!-- Revenue Chart -->
              <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                  <h3 class="card-title text-lg mb-4">Pendapatan Harian</h3>
                  <div v-if="revenue?.daily && revenue.daily.length > 0" class="h-64">
                    <!-- Simple SVG Chart -->
                    <div class="w-full h-full p-4">
                      <div class="flex items-end h-full space-x-1">
                        <div v-for="(item, index) in revenue.daily" :key="index"
                          class="flex-1 bg-blue-500 rounded-t transition-all hover:bg-blue-600 relative group"
                          :style="{ height: getBarHeight(item.revenue, revenue.daily) + '%' }">
                          <div
                            class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-black text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 whitespace-nowrap">
                            {{ formatCurrency(item.revenue) }}
                          </div>
                        </div>
                      </div>
                      <div class="flex justify-between mt-2 text-xs text-gray-500">
                        <span v-for="(item, index) in revenue.daily.slice(0, 7)" :key="index">
                          {{ formatDate(item.date) }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div v-else class="h-64 flex items-center justify-center bg-base-200 rounded-lg">
                    <div class="text-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-2" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                      </svg>
                      <p class="text-gray-500">Tidak ada data pendapatan untuk periode ini</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Payment Methods -->
              <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                  <h3 class="card-title text-lg mb-4">Metode Pembayaran</h3>
                  <div class="space-y-3">
                    <div v-for="method in transactions?.payment_methods" :key="method.payment_method"
                      class="flex items-center justify-between">
                      <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full mr-3" :class="getPaymentMethodColor(method.payment_method)">
                        </div>
                        <span class="font-medium">{{ getPaymentMethodLabel(method.payment_method) }}</span>
                      </div>
                      <div class="text-right">
                        <div class="font-bold">{{ formatCurrency(method.total_amount) }}</div>
                        <div class="text-sm text-gray-500">{{ method.count }} transaksi</div>
                      </div>
                    </div>
                    <div v-if="!transactions?.payment_methods || transactions.payment_methods.length === 0"
                      class="text-center py-4 text-gray-500">
                      Tidak ada data transaksi untuk periode ini
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Top Services and Products -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
              <!-- Top Services -->
              <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                  <h3 class="card-title text-lg mb-4">Layanan Terpopuler</h3>
                  <div class="overflow-x-auto">
                    <table class="table table-sm">
                      <thead>
                        <tr>
                          <th>Layanan</th>
                          <th>Terjual</th>
                          <th>Pendapatan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(service, index) in topServices" :key="index">
                          <td>
                            <div class="font-medium">{{ service.nama_layanan }}</div>
                          </td>
                          <td>
                            <span class="badge badge-outline">{{ service.total_quantity }}x</span>
                          </td>
                          <td>
                            <span class="font-semibold text-green-600">{{ formatCurrency(service.total_revenue)
                              }}</span>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <!-- Top Products -->
              <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                  <h3 class="card-title text-lg mb-4">Produk Terlaris</h3>
                  <div class="overflow-x-auto">
                    <table class="table table-sm">
                      <thead>
                        <tr>
                          <th>Produk</th>
                          <th>Terjual</th>
                          <th>Pendapatan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(product, index) in topProducts" :key="index">
                          <td>
                            <div class="font-medium">{{ product.nama_produk }}</div>
                          </td>
                          <td>
                            <span class="badge badge-outline">{{ product.total_quantity }}x</span>
                          </td>
                          <td>
                            <span class="font-semibold text-green-600">{{ formatCurrency(product.total_revenue)
                              }}</span>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <!-- Transaction Status -->
            <div class="card bg-base-100 shadow-xl mt-8">
              <div class="card-body">
                <h3 class="card-title text-lg mb-4">Status Pembayaran</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div class="stat">
                    <div class="stat-figure text-green-500">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                    <div class="stat-title">Lunas</div>
                    <div class="stat-value text-green-500">{{ transactions.paid }}</div>
                    <div class="stat-desc">{{ ((transactions.paid / transactions.total) * 100).toFixed(1) }}% dari total
                    </div>
                  </div>

                  <div class="stat">
                    <div class="stat-figure text-yellow-500">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                    <div class="stat-title">Sebagian</div>
                    <div class="stat-value text-yellow-500">{{ transactions.partial }}</div>
                    <div class="stat-desc">{{ ((transactions.partial / transactions.total) * 100).toFixed(1) }}% dari
                      total</div>
                  </div>

                  <div class="stat">
                    <div class="stat-figure text-red-500">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                    <div class="stat-title">Belum Lunas</div>
                    <div class="stat-value text-red-500">{{ transactions.unpaid }}</div>
                    <div class="stat-desc">{{ ((transactions.unpaid / transactions.total) * 100).toFixed(1) }}% dari
                      total</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Patient Visits Report -->
            <div class="card bg-base-100 shadow-xl mt-8">
              <div class="card-body">
                <h3 class="card-title text-lg mb-4">📊 Laporan Kunjungan Pasien</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                  <div class="stat bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg">
                    <div class="stat-figure">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 opacity-80" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                      </svg>
                    </div>
                    <div class="stat-title opacity-90">Total Kunjungan</div>
                    <div class="stat-value">{{ patientVisits?.total_visits || 0 }}</div>
                  </div>

                  <div class="stat bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg">
                    <div class="stat-figure">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 opacity-80" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                      </svg>
                    </div>
                    <div class="stat-title opacity-90">Pasien Baru</div>
                    <div class="stat-value">{{ patientVisits?.new_patients || 0 }}</div>
                  </div>

                  <div class="stat bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg">
                    <div class="stat-figure">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 opacity-80" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                    </div>
                    <div class="stat-title opacity-90">Pasien Kembali</div>
                    <div class="stat-value">{{ patientVisits?.returning_patients || 0 }}</div>
                  </div>
                </div>

                <!-- Frequent Patients -->
                <div v-if="patientVisits?.frequent_patients?.length > 0">
                  <h4 class="font-semibold text-base mb-3">Pasien yang Paling Sering Berkunjung</h4>
                  <div class="space-y-3">
                    <div v-for="(patient, index) in patientVisits.frequent_patients.slice(0, 3)"
                      :key="patient.nama_lengkap"
                      class="flex items-center justify-between p-3 bg-base-100 rounded-lg border">
                      <div class="flex items-center">
                        <div
                          class="w-8 h-8 bg-primary text-primary-content rounded-full flex items-center justify-center text-sm font-bold mr-3">
                          {{ index + 1 }}
                        </div>
                        <div>
                          <div class="font-medium">{{ patient.nama_lengkap }}</div>
                          <div class="text-sm text-base-content/70">{{ patient.email }}</div>
                        </div>
                      </div>
                      <div class="text-right">
                        <div class="font-semibold text-primary">{{ patient.total_visits }} kunjungan</div>
                        <div class="text-sm text-base-content/70">
                          Terakhir: {{ formatPatientLastVisit(patient.last_visit) }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-else class="text-center py-4 text-base-content/50">
                  Belum ada data pasien yang sering berkunjung
                </div>
              </div>
            </div>

            <!-- Doctor Performance Report -->
            <div class="card bg-base-100 shadow-xl mt-8">
              <div class="card-body">
                <h3 class="card-title text-lg mb-4">👩‍⚕️ Laporan Kinerja Dokter/Terapis</h3>

                <div class="overflow-x-auto">
                  <table class="table table-sm">
                    <thead>
                      <tr>
                        <th>Dokter/Terapis</th>
                        <th>Total Pasien</th>
                        <th>Total Pendapatan</th>
                        <th>Rata-rata per Transaksi</th>
                        <th>Layanan Favorit</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="doctor in doctorPerformance?.statistics || []" :key="doctor.doctor_name" class="hover">
                        <td>
                          <div class="font-medium">{{ doctor.doctor_name }}</div>
                        </td>
                        <td>
                          <span class="badge badge-primary badge-outline">{{ doctor.total_patients }}</span>
                        </td>
                        <td>
                          <span class="font-semibold text-green-600">
                            {{ formatCurrency(getRevenueByDoctor(doctor.doctor_name)) }}
                          </span>
                        </td>
                        <td>
                          <span class="text-base-content/70">
                            {{ formatCurrency(getAvgRevenueByDoctor(doctor.doctor_name)) }}
                          </span>
                        </td>
                        <td>
                          <div class="text-sm">
                            <div v-for="service in getTopServicesByDoctor(doctor.doctor_name)"
                              :key="service.nama_layanan" class="badge badge-ghost badge-xs mr-1 mb-1">
                              {{ service.nama_layanan }} ({{ service.service_count }})
                            </div>
                            <div v-if="!getTopServicesByDoctor(doctor.doctor_name).length" class="text-base-content/50">
                              Tidak ada data
                            </div>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div v-if="!doctorPerformance?.statistics?.length" class="text-center py-8 text-base-content/50">
                  Belum ada data kinerja dokter
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '~/Layouts/AppLayout.vue'

// Props
const props = defineProps({
  filters: Object,
  revenue: Object,
  transactions: Object,
  topServices: Array,
  topProducts: Array,
  patients: Object,
  patientVisits: Object,
  doctorPerformance: Object,
})

// Reactive form
const form = reactive({
  period: props.filters?.period || 'monthly',
  start_date: props.filters?.start_date || '',
  end_date: props.filters?.end_date || '',
})

// Methods
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(amount)
}

const updateDateRange = () => {
  const today = new Date()
  const year = today.getFullYear()
  const month = String(today.getMonth() + 1).padStart(2, '0')
  const day = String(today.getDate()).padStart(2, '0')

  switch (form.period) {
    case 'daily':
      form.start_date = `${year}-${month}-${day}`
      form.end_date = `${year}-${month}-${day}`
      break
    case 'weekly':
      const startOfWeek = new Date(today.setDate(today.getDate() - today.getDay()))
      const endOfWeek = new Date(today.setDate(today.getDate() - today.getDay() + 6))
      form.start_date = startOfWeek.toISOString().split('T')[0]
      form.end_date = endOfWeek.toISOString().split('T')[0]
      break
    case 'monthly':
      form.start_date = `${year}-${month}-01`
      form.end_date = new Date(year, today.getMonth() + 1, 0).toISOString().split('T')[0]
      break
    case 'yearly':
      form.start_date = `${year}-01-01`
      form.end_date = `${year}-12-31`
      break
  }
}

const filterReports = () => {
  router.get('/reports', form, {
    preserveState: true,
    preserveScroll: true,
  })
}

const exportReport = (type) => {
  const params = new URLSearchParams({
    type,
    period: form.period,
    start_date: form.start_date,
    end_date: form.end_date,
  })

  window.open(`/reports/export?${params.toString()}`, '_blank')
}

const getPaymentMethodLabel = (method) => {
  const labels = {
    'cash': 'Tunai',
    'debit_card': 'Kartu Debit',
    'credit_card': 'Kartu Kredit',
    'transfer': 'Transfer',
    'other': 'Lainnya',
    // backward compatibility
    'debit': 'Kartu Debit',
    'credit': 'Kartu Kredit',
    'tunai': 'Tunai',
    'kartu': 'Kartu'
  }
  return labels[method] || method
}

const getPaymentMethodColor = (method) => {
  const colors = {
    'tunai': 'bg-green-500',
    'kartu_debit': 'bg-blue-500',
    'kartu_kredit': 'bg-purple-500',
    'transfer_bank': 'bg-indigo-500',
    'qris': 'bg-orange-500',
  }
  return colors[method] || 'bg-gray-500'
}

const getRevenueByDoctor = (doctorName) => {
  const doctor = props.doctorPerformance?.revenue?.find(d => d.doctor_name === doctorName)
  return doctor?.total_revenue || 0
}

const getAvgRevenueByDoctor = (doctorName) => {
  const doctor = props.doctorPerformance?.revenue?.find(d => d.doctor_name === doctorName)
  return doctor?.avg_revenue_per_transaction || 0
}

const getTopServicesByDoctor = (doctorName) => {
  return props.doctorPerformance?.top_services?.[doctorName] || []
}

// Helper function for simple chart
const getBarHeight = (value, data) => {
  if (!data || data.length === 0) return 0
  const maxValue = Math.max(...data.map(item => item.revenue))
  return maxValue > 0 ? (value / maxValue) * 90 : 0
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', { month: 'short', day: 'numeric' })
}

const formatPatientLastVisit = (dateString) => {
  if (!dateString) return 'Tidak ada data'
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}

// Initialize revenue chart
const initializeRevenueChart = async () => {
  // Simple implementation without Chart.js
  // Chart functionality is now handled by template
}

onMounted(() => {
  // Initialize date range if needed
  if (!form.start_date || !form.end_date) {
    updateDateRange()
  }
})
</script>
