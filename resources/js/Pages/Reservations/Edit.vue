<template>
  <div>
    <Head title="Edit Reservasi" />
    
    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold">Edit Reservasi #{{ reservation.id }}</h2>
              <div class="flex space-x-2">
                <button @click="handleBack" type="button" class="btn btn-outline">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                  </svg>
                  Kembali
                </button>
              </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Patient Selection -->
                <div class="form-control">
                  <label class="label">
                    <span class="label-text">Pasien <span class="text-red-500">*</span></span>
                  </label>
                  <select 
                    v-model="form.patient_id" 
                    class="select select-bordered"
                    :class="{ 'select-error': errors.patient_id }"
                    required
                  >
                    <option value="">Pilih Pasien</option>
                    <option v-for="patient in patients" :key="patient.id" :value="patient.id">
                      {{ patient.nama_lengkap }} - {{ patient.nomor_telepon }}
                    </option>
                  </select>
                  <div v-if="errors.patient_id" class="label">
                    <span class="label-text-alt text-red-500">{{ errors.patient_id }}</span>
                  </div>
                </div>

                <!-- Service Selection -->
                <div class="form-control">
                  <label class="label">
                    <span class="label-text">Layanan <span class="text-red-500">*</span></span>
                  </label>
                  <select 
                    v-model="form.service_id" 
                    class="select select-bordered"
                    :class="{ 'select-error': errors.service_id }"
                    @change="updateServiceInfo"
                    required
                  >
                    <option value="">Pilih Layanan</option>
                    <option v-for="service in services" :key="service.id" :value="service.id">
                      {{ service.nama_layanan }} - {{ formatCurrency(service.harga) }}
                    </option>
                  </select>
                  <div v-if="errors.service_id" class="label">
                    <span class="label-text-alt text-red-500">{{ errors.service_id }}</span>
                  </div>
                  <!-- Service Info -->
                  <div v-if="selectedService" class="alert alert-info mt-2">
                    <div>
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      <div>
                        <h3 class="font-bold">{{ selectedService.nama_layanan }}</h3>
                        <div class="text-xs">
                          Durasi: {{ selectedService.durasi_menit }} menit | 
                          Harga: {{ formatCurrency(selectedService.harga) }}
                        </div>
                        <p class="text-xs mt-1">{{ selectedService.deskripsi }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Date -->
                <div class="form-control">
                  <label class="label">
                    <span class="label-text">Tanggal Reservasi <span class="text-red-500">*</span></span>
                  </label>
                  <input 
                    type="date" 
                    v-model="form.tanggal_reservasi" 
                    class="input input-bordered"
                    :class="{ 'input-error': errors.tanggal_reservasi }"
                    :min="minDate"
                    @change="loadAvailableSlots"
                    required
                  />
                  <div v-if="errors.tanggal_reservasi" class="label">
                    <span class="label-text-alt text-red-500">{{ errors.tanggal_reservasi }}</span>
                  </div>
                </div>

                <!-- Doctor Selection -->
                <div class="form-control">
                  <label class="label">
                    <span class="label-text">Dokter/Terapis <span class="text-red-500">*</span></span>
                  </label>
                  <select 
                    v-model="form.user_id" 
                    class="select select-bordered"
                    :class="{ 'select-error': errors.user_id }"
                    @change="loadAvailableSlots"
                    required
                  >
                    <option value="">Pilih Dokter/Terapis</option>
                    <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">
                      {{ doctor.name }}
                    </option>
                  </select>
                  <div v-if="errors.user_id" class="label">
                    <span class="label-text-alt text-red-500">{{ errors.user_id }}</span>
                  </div>
                </div>

                <!-- Status -->
                <div class="form-control">
                  <label class="label">
                    <span class="label-text">Status <span class="text-red-500">*</span></span>
                  </label>
                  <select 
                    v-model="form.status" 
                    class="select select-bordered"
                    :class="{ 'select-error': errors.status }"
                    required
                  >
                    <option value="pending">Pending</option>
                    <option value="confirmed">Dikonfirmasi</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                  </select>
                  <div v-if="errors.status" class="label">
                    <span class="label-text-alt text-red-500">{{ errors.status }}</span>
                  </div>
                </div>
              </div>

              <!-- Current Schedule Info -->
              <div class="alert alert-info">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                  <h3 class="font-bold">Jadwal Saat Ini</h3>
                  <div class="text-xs">
                    {{ formatDate(reservation.tanggal_reservasi) }} | 
                    {{ formatTime(reservation.waktu_mulai) }} - {{ formatTime(reservation.waktu_selesai) }}
                  </div>
                </div>
              </div>

              <!-- Available Time Slots -->
              <div v-if="form.tanggal_reservasi && form.user_id && form.service_id" class="form-control">
                <label class="label">
                  <span class="label-text">Waktu Tersedia <span class="text-red-500">*</span></span>
                </label>
                
                <!-- Loading -->
                <div v-if="loadingSlots" class="flex justify-center py-4">
                  <span class="loading loading-spinner loading-md"></span>
                </div>

                <!-- Time Slots Grid -->
                <div v-else-if="availableSlots.length > 0" class="grid grid-cols-3 md:grid-cols-6 gap-2">
                  <button
                    v-for="slot in availableSlots"
                    :key="slot.waktu_mulai"
                    type="button"
                    @click="selectTimeSlot(slot)"
                    class="btn btn-sm"
                    :class="{ 
                      'btn-primary': selectedTimeSlot && selectedTimeSlot.waktu_mulai === slot.waktu_mulai,
                      'btn-outline': !selectedTimeSlot || selectedTimeSlot.waktu_mulai !== slot.waktu_mulai,
                      'btn-success': slot.waktu_mulai === reservation.waktu_mulai && slot.waktu_selesai === reservation.waktu_selesai
                    }"
                  >
                    {{ formatTime(slot.waktu_mulai) }}
                    <span v-if="slot.waktu_mulai === reservation.waktu_mulai && slot.waktu_selesai === reservation.waktu_selesai" class="text-xs ml-1">(saat ini)</span>
                  </button>
                </div>

                <!-- No Slots Available -->
                <div v-else class="alert alert-warning">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                  </svg>
                  <span>Tidak ada slot waktu tersedia untuk tanggal dan dokter yang dipilih.</span>
                </div>

                <div v-if="errors.waktu_mulai" class="label">
                  <span class="label-text-alt text-red-500">{{ errors.waktu_mulai }}</span>
                </div>
              </div>

              <!-- Selected Time Info -->
              <div v-if="selectedTimeSlot" class="alert alert-success">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                  <h3 class="font-bold">Waktu Terpilih</h3>
                  <div class="text-xs">
                    {{ formatTime(selectedTimeSlot.waktu_mulai) }} - {{ formatTime(selectedTimeSlot.waktu_selesai) }}
                    ({{ selectedService?.durasi_menit }} menit)
                  </div>
                </div>
              </div>

              <!-- Notes -->
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Catatan (Opsional)</span>
                </label>
                <textarea 
                  v-model="form.catatan" 
                  class="textarea textarea-bordered h-24" 
                  placeholder="Catatan tambahan untuk reservasi..."
                ></textarea>
              </div>

              <!-- Submit Button -->
              <div class="flex justify-end space-x-4">
                <button @click="handleCancel" type="button" class="btn btn-outline">
                  Batal
                </button>
                <button 
                  type="submit" 
                  class="btn btn-primary"
                  :disabled="processing"
                >
                  <span v-if="processing" class="loading loading-spinner loading-sm"></span>
                  {{ processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div v-if="showCancelModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">
          <svg class="w-6 h-6 inline-block mr-2 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z">
            </path>
          </svg>
          Konfirmasi Perubahan
        </h3>
        <p class="py-4 text-base-content/80">
          Anda telah mengisi beberapa data pada form reservasi ini. Jika Anda keluar sekarang, semua perubahan akan hilang.
        </p>
        <p class="text-sm text-base-content/60 mb-6">
          Apakah Anda yakin ingin meninggalkan halaman ini tanpa menyimpan perubahan?
        </p>
        <div class="modal-action">
          <button @click="showCancelModal = false" class="btn btn-ghost">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12">
              </path>
            </svg>
            Tetap di Halaman
          </button>
          <Link :href="route('reservations.index')" class="btn btn-warning">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013 3v1"></path>
            </svg>
            Ya, Keluar
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link, router } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

export default {
  components: {
    Head,
    Link,
  },
  props: {
    reservation: Object,
    patients: Array,
    services: Array,
    doctors: Array,
    errors: Object,
  },
  setup(props) {
    // Format date for HTML input (YYYY-MM-DD)
    const formatDateForInput = (dateString) => {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toISOString().split('T')[0]
    }

    const form = useForm({
      patient_id: props.reservation.patient_id,
      service_id: props.reservation.service_id,
      user_id: props.reservation.user_id,
      tanggal_reservasi: formatDateForInput(props.reservation.tanggal_reservasi),
      waktu_mulai: props.reservation.waktu_mulai,
      waktu_selesai: props.reservation.waktu_selesai,
      status: props.reservation.status,
      catatan: props.reservation.catatan || '',
    })

    const availableSlots = ref([])
    const loadingSlots = ref(false)
    const selectedTimeSlot = ref({
      waktu_mulai: props.reservation.waktu_mulai,
      waktu_selesai: props.reservation.waktu_selesai,
    })

    const selectedService = computed(() => {
      return props.services.find(service => service.id == form.service_id)
    })

    const minDate = computed(() => {
      return new Date().toISOString().split('T')[0]
    })

    const updateServiceInfo = () => {
      if (form.tanggal_reservasi && form.user_id) {
        loadAvailableSlots()
      }
    }

    const loadAvailableSlots = async () => {
      if (!form.tanggal_reservasi || !form.user_id || !form.service_id) {
        availableSlots.value = []
        return
      }

      loadingSlots.value = true

      try {
        const response = await axios.get('/api/available-slots', {
          params: {
            date: form.tanggal_reservasi,
            doctor_id: form.user_id,
            service_id: form.service_id,
            exclude_reservation_id: props.reservation.id, // Exclude current reservation
          },
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
          withCredentials: true
        })
        availableSlots.value = response.data.slots || []
        
        // Add current time slot to available slots if not already there
        const currentSlot = {
          waktu_mulai: props.reservation.waktu_mulai,
          waktu_selesai: props.reservation.waktu_selesai,
        }
        
        const hasCurrentSlot = availableSlots.value.some(slot => 
          slot.waktu_mulai === currentSlot.waktu_mulai && 
          slot.waktu_selesai === currentSlot.waktu_selesai
        )
        
        if (!hasCurrentSlot) {
          availableSlots.value.push(currentSlot)
          availableSlots.value.sort((a, b) => a.waktu_mulai.localeCompare(b.waktu_mulai))
        }
      } catch (error) {
        console.error('Error loading available slots:', error)
        availableSlots.value = []
      } finally {
        loadingSlots.value = false
      }
    }

    const selectTimeSlot = (slot) => {
      selectedTimeSlot.value = slot
      form.waktu_mulai = slot.waktu_mulai
      form.waktu_selesai = slot.waktu_selesai
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    const formatTime = (time) => {
      return new Date(`2000-01-01 ${time}`).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
      }).format(amount)
    }

    const submit = () => {
      form.put(route('reservations.update', props.reservation.id))
    }

    // Modal state and functions
    const showCancelModal = ref(false)

    const hasFormData = () => {
      const original = {
        patient_id: props.reservation.patient_id,
        service_id: props.reservation.service_id,
        user_id: props.reservation.user_id,
        tanggal_reservasi: formatDateForInput(props.reservation.tanggal_reservasi),
        waktu_mulai: props.reservation.waktu_mulai,
        waktu_selesai: props.reservation.waktu_selesai,
        status: props.reservation.status,
        catatan: props.reservation.catatan || '',
      }

      return (
        form.patient_id !== original.patient_id ||
        form.service_id !== original.service_id ||
        form.user_id !== original.user_id ||
        form.tanggal_reservasi !== original.tanggal_reservasi ||
        form.waktu_mulai !== original.waktu_mulai ||
        form.waktu_selesai !== original.waktu_selesai ||
        form.status !== original.status ||
        form.catatan !== original.catatan
      )
    }

    const handleBack = () => {
      if (hasFormData()) {
        showCancelModal.value = true
      } else {
        router.visit(route('reservations.index'))
      }
    }

    const handleCancel = () => {
      if (hasFormData()) {
        showCancelModal.value = true
      } else {
        router.visit(route('reservations.index'))
      }
    }

    const confirmCancel = () => {
      router.visit(route('reservations.index'))
    }

    onMounted(() => {
      loadAvailableSlots()
    })

    return {
      form,
      availableSlots,
      loadingSlots,
      selectedTimeSlot,
      selectedService,
      minDate,
      updateServiceInfo,
      loadAvailableSlots,
      selectTimeSlot,
      formatDate,
      formatTime,
      formatCurrency,
      submit,
      processing: computed(() => form.processing),
      showCancelModal,
      handleBack,
      handleCancel,
      confirmCancel,
    }
  }
}
</script>
