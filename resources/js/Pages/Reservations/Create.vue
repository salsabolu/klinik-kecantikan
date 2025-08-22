<template>
  <SimpleLayout>

    <Head title="Reservasi Baru" />

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold">Reservasi Baru</h2>
              <button @click="handleBack" type="button" class="btn btn-outline">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                  </path>
                </svg>
                Kembali
              </button>
            </div>  

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Patient Selection -->
                <div class="form-control">
                  <label class="label">
                    <span class="label-text">Pasien <span class="text-red-500">*</span></span>
                  </label>
                  <select v-model="form.patient_id" class="select select-bordered"
                    :class="{ 'select-error': errors.patient_id }" required>
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
                  <select v-model="form.service_id" class="select select-bordered"
                    :class="{ 'select-error': errors.service_id }" @change="updateServiceInfo" required>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
                  <input type="date" v-model="form.tanggal_reservasi" class="input input-bordered"
                    :class="{ 'input-error': errors.tanggal_reservasi }" :min="minDate" @change="loadAvailableSlots"
                    required />
                  <div v-if="errors.tanggal_reservasi" class="label">
                    <span class="label-text-alt text-red-500">{{ errors.tanggal_reservasi }}</span>
                  </div>
                </div>

                <!-- Doctor Selection -->
                <div class="form-control">
                  <label class="label">
                    <span class="label-text">Dokter/Terapis <span class="text-red-500">*</span></span>
                  </label>
                  <select v-model="form.user_id" class="select select-bordered"
                    :class="{ 'select-error': errors.user_id }" @change="loadAvailableSlots" required>
                    <option value="">Pilih Dokter/Terapis</option>
                    <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">
                      {{ doctor.name }}
                    </option>
                  </select>
                  <div v-if="errors.user_id" class="label">
                    <span class="label-text-alt text-red-500">{{ errors.user_id }}</span>
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
                  <button v-for="slot in availableSlots" :key="slot.waktu_mulai" type="button"
                    @click="!slot.disabled ? selectTimeSlot(slot) : null" 
                    :disabled="slot.disabled"
                    class="btn btn-sm" 
                    :class="{
                      'btn-primary': selectedTimeSlot && selectedTimeSlot.waktu_mulai === slot.waktu_mulai,
                      'btn-outline': !selectedTimeSlot || selectedTimeSlot.waktu_mulai !== slot.waktu_mulai,
                      'btn-disabled': slot.disabled,
                      'opacity-50 cursor-not-allowed': slot.disabled
                    }">
                    {{ formatTime(slot.waktu_mulai) }}
                  </button>
                </div>

                <!-- No Slots Available -->
                <div v-else class="alert alert-warning">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                    </path>
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
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
                  <span class="label-text-alt">{{ (form.catatan || '').length }}/500</span>
                </label>
                <textarea v-model="form.catatan" class="textarea textarea-bordered h-24"
                  :class="{ 'textarea-error': (form.catatan || '').length > 500 }"
                  placeholder="Catatan tambahan untuk reservasi..." maxlength="500"></textarea>
                <div v-if="(form.catatan || '').length > 500" class="label">
                  <span class="label-text-alt text-red-500">Catatan tidak boleh lebih dari 500 karakter</span>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="flex justify-end space-x-4">
                <button @click="handleCancel" type="button" class="btn btn-outline">
                  Batal
                </button>
                <button type="submit" class="btn btn-primary" :disabled="processing || !selectedTimeSlot">
                  <span v-if="processing" class="loading loading-spinner loading-sm"></span>
                  {{ processing ? 'Menyimpan...' : 'Simpan Reservasi' }}
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
          Anda telah mengisi beberapa data pada form reservasi ini. Jika Anda keluar sekarang, semua data akan hilang.
        </p>
        <p class="text-sm text-base-content/60 mb-6">
          Apakah Anda yakin ingin meninggalkan halaman ini tanpa menyimpan?
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
  </SimpleLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import SimpleLayout from '~/Layouts/SimpleLayout.vue'
import axios from 'axios'

export default {
  components: {
    Head,
    Link,
    SimpleLayout,
  },
  props: {
    patients: Array,
    services: Array,
    doctors: Array,
    errors: Object,
  },
  setup(props) {
    console.log('Create reservation component initialized with props:', props)

    const form = useForm({
      patient_id: '',
      service_id: '',
      user_id: '',
      tanggal_reservasi: '',
      waktu_mulai: '',
      waktu_selesai: '',
      catatan: '',
    })

    const availableSlots = ref([])
    const loadingSlots = ref(false)
    const selectedTimeSlot = ref(null)

    const selectedService = computed(() => {
      return props.services.find(service => service.id == form.service_id)
    })

    // Modal state
    const showCancelModal = ref(false)

    // Check if form has any data
    const hasFormData = computed(() => {
      return form.patient_id ||
        form.service_id ||
        form.user_id ||
        form.tanggal_reservasi ||
        form.waktu_mulai ||
        form.waktu_selesai ||
        form.catatan
    })

    const minDate = computed(() => {
      return new Date().toISOString().split('T')[0]
    })

    const updateServiceInfo = () => {
      console.log('Service changed, resetting time slots...')
      selectedTimeSlot.value = null
      form.waktu_mulai = ''
      form.waktu_selesai = ''
      if (form.tanggal_reservasi && form.user_id) {
        console.log('Date and doctor selected, loading slots...')
        loadAvailableSlots()
      } else {
        console.log('Date or doctor not selected yet')
      }
    }

    const loadAvailableSlots = async () => {
      console.log('Loading available slots...', {
        tanggal_reservasi: form.tanggal_reservasi,
        user_id: form.user_id,
        service_id: form.service_id
      })

      if (!form.tanggal_reservasi || !form.user_id || !form.service_id) {
        availableSlots.value = []
        console.log('Missing required fields, clearing slots')
        return
      }

      loadingSlots.value = true
      selectedTimeSlot.value = null
      form.waktu_mulai = ''
      form.waktu_selesai = ''

      try {
        // Generate all possible time slots with clinic operating hours logic
        const serviceDuration = selectedService.value?.durasi_menit || 30
        const allSlots = generateTimeSlots(serviceDuration, form.tanggal_reservasi)
        
        // Get existing reservations from API for filtering
        try {
          const response = await axios.get('/api/available-slots', {
            params: {
              date: form.tanggal_reservasi,
              doctor_id: form.user_id,
              service_id: form.service_id,
            },
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
            withCredentials: true
          })

          // Filter out existing reservations
          const existingReservations = response.data.existing_reservations || []
          const filteredSlots = allSlots.filter(slot => {
            return !existingReservations.some(reservation => {
              return (
                slot.waktu_mulai >= reservation.waktu_mulai && 
                slot.waktu_mulai < reservation.waktu_selesai
              ) || (
                slot.waktu_selesai > reservation.waktu_mulai && 
                slot.waktu_selesai <= reservation.waktu_selesai
              ) || (
                slot.waktu_mulai <= reservation.waktu_mulai && 
                slot.waktu_selesai >= reservation.waktu_selesai
              )
            })
          })

          availableSlots.value = filteredSlots
        } catch (apiError) {
          console.log('API call failed, using generated slots without filtering:', apiError)
          // If API fails, use all generated slots
          availableSlots.value = allSlots
        }

        console.log('Generated available slots:', availableSlots.value)

      } catch (error) {
        console.error('Error in loadAvailableSlots:', error)
        availableSlots.value = []
      } finally {
        loadingSlots.value = false
      }
    }

    // Generate time slots with clinic operating hours logic
    const generateTimeSlots = (serviceDurationMinutes, selectedDate) => {
      const slots = []
      
      // Clinic operating hours: 09:00-18:00
      // Lunch break: 12:00-13:00
      const openTime = 9 * 60 // 09:00 in minutes
      const closeTime = 18 * 60 // 18:00 in minutes
      const lunchStart = 12 * 60 // 12:00 in minutes
      const lunchEnd = 13 * 60 // 13:00 in minutes

      // Check if selected date is today
      const today = new Date()
      const selectedDateObj = new Date(selectedDate)
      const isToday = selectedDateObj.toDateString() === today.toDateString()
      
      // If today, calculate current time in minutes
      let currentTimeMinutes = 0
      if (isToday) {
        currentTimeMinutes = today.getHours() * 60 + today.getMinutes()
      }

      // Generate 30-minute intervals
      const slotInterval = 30 // 30 minutes
      
      for (let startMinutes = openTime; startMinutes < closeTime; startMinutes += slotInterval) {
        const endMinutes = startMinutes + serviceDurationMinutes

        // Skip if slot would end after clinic closes
        if (endMinutes > closeTime) {
          continue
        }

        // Skip if slot conflicts with lunch break
        const conflictsWithLunch = (
          (startMinutes >= lunchStart && startMinutes < lunchEnd) || // Starts during lunch
          (endMinutes > lunchStart && endMinutes <= lunchEnd) ||     // Ends during lunch
          (startMinutes <= lunchStart && endMinutes >= lunchEnd)     // Spans across lunch
        )

        if (conflictsWithLunch) {
          continue
        }

        // Skip if slot is in the past (for today only)
        if (isToday && startMinutes <= currentTimeMinutes) {
          continue
        }

        // Convert minutes to time string
        const startTime = minutesToTimeString(startMinutes)
        const endTime = minutesToTimeString(endMinutes)

        slots.push({
          waktu_mulai: startTime,
          waktu_selesai: endTime,
          disabled: false
        })
      }

      return slots
    }

    // Helper function to convert minutes to HH:MM format
    const minutesToTimeString = (minutes) => {
      const hours = Math.floor(minutes / 60)
      const mins = minutes % 60
      return `${hours.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}`
    }

    const selectTimeSlot = (slot) => {
      console.log('Selecting time slot:', slot)
      selectedTimeSlot.value = slot
      form.waktu_mulai = slot.waktu_mulai
      form.waktu_selesai = slot.waktu_selesai
      console.log('Form updated with slot:', {
        waktu_mulai: form.waktu_mulai,
        waktu_selesai: form.waktu_selesai
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

    // Handle cancel button
    const handleCancel = () => {
      if (hasFormData.value) {
        showCancelModal.value = true
      } else {
        window.location.href = route('reservations.index')
      }
    }

    // Handle back button
    const handleBack = () => {
      if (hasFormData.value) {
        showCancelModal.value = true
      } else {
        window.location.href = route('reservations.index')
      }
    }

    const submit = () => {
      console.log('Submitting form with data:', form.data())

      // Basic validation
      if (!form.patient_id) {
        alert('Silakan pilih pasien')
        return
      }
      if (!form.service_id) {
        alert('Silakan pilih layanan')
        return
      }
      if (!form.tanggal_reservasi) {
        alert('Silakan pilih tanggal')
        return
      }
      if (!form.user_id) {
        alert('Silakan pilih dokter')
        return
      }
      if (!form.waktu_mulai || !form.waktu_selesai) {
        alert('Silakan pilih waktu slot')
        return
      }

      form.post(route('reservations.store'), {
        onSuccess: () => {
          console.log('Reservation created successfully')
        },
        onError: (errors) => {
          console.error('Form submission errors:', errors)
        }
      })
    }

    return {
      form,
      availableSlots,
      loadingSlots,
      selectedTimeSlot,
      selectedService,
      showCancelModal,
      hasFormData,
      minDate,
      updateServiceInfo,
      loadAvailableSlots,
      selectTimeSlot,
      formatTime,
      formatCurrency,
      handleCancel,
      handleBack,
      submit,
      processing: computed(() => form.processing),
      errors: computed(() => form.errors),
    }
  }
}
</script>
