<template>
  <SimpleLayout>
    <Head title="Rekam Medis Baru" />
    
    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold">Rekam Medis Baru</h2>
              <button 
                @click="handleBackClick"
                class="btn btn-outline">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
              </button>
            </div>

            <!-- Back Confirmation Modal -->
            <div v-if="showBackModal" class="modal modal-open">
              <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">Konfirmasi Kembali</h3>
                <p class="mb-6">Apakah Anda yakin ingin kembali? Data yang telah diisi akan hilang.</p>
                <div class="modal-action">
                  <button @click="showBackModal = false" class="btn btn-outline">Tidak</button>
                  <Link :href="route('medical-records.index')" class="btn btn-error">Ya, Kembali</Link>
                </div>
              </div>
            </div>

            <!-- Patient Quick Info -->
            <div v-if="selectedPatient" class="alert alert-info mb-6">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
              <div>
                <h3 class="font-bold">{{ selectedPatient.nama_lengkap }}</h3>
                <div class="text-xs">
                  {{ selectedPatient.jenis_kelamin }} | {{ calculateAge(selectedPatient.tanggal_lahir) }} tahun | 
                  {{ selectedPatient.nomor_telepon }}
                </div>
              </div>
            </div>

            <!-- Reservation Info (Required) -->
            <div v-if="selectedReservation" class="alert alert-success mb-6">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <div>
                <h3 class="font-bold">Reservasi: {{ selectedReservation.service?.nama_layanan }}</h3>
                <div class="text-xs">
                  {{ formatDate(selectedReservation.tanggal_reservasi) }} | 
                  {{ formatTime(selectedReservation.waktu_mulai) }} - {{ formatTime(selectedReservation.waktu_selesai) }}
                </div>
                <div class="text-xs mt-1">
                  <span class="font-medium">Dokter/Terapis:</span> {{ selectedReservation.user.name }}
                </div>
                <div class="text-xs">
                  <span class="font-medium">Status:</span> {{ selectedReservation.status === 'completed' ? 'Selesai' : 'Dikonfirmasi' }} | 
                  <span class="font-medium">Biaya:</span> {{ formatCurrency(selectedReservation.service?.harga) }}
                </div>
              </div>
            </div>
            <div v-else class="alert alert-error mb-6">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
              </svg>
              <div>
                <h3 class="font-bold">Reservasi Diperlukan</h3>
                <div class="text-xs">
                  Rekam medis harus dibuat dari reservasi yang sudah selesai. Silakan pilih reservasi dari halaman reservasi.
                </div>
                <Link :href="route('reservations.index')" class="btn btn-sm btn-primary mt-2">
                  Lihat Reservasi
                </Link>
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
                    @change="updateSelectedPatient"
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

                <!-- Examination Date -->
                <div class="form-control">
                  <label class="label">
                    <span class="label-text">Tanggal Pemeriksaan <span class="text-red-500">*</span></span>
                  </label>
                  <input 
                    type="date" 
                    v-model="form.tanggal_pemeriksaan" 
                    class="input input-bordered"
                    :class="{ 'input-error': errors.tanggal_pemeriksaan }"
                    :max="today"
                    required
                  />
                  <div v-if="errors.tanggal_pemeriksaan" class="label">
                    <span class="label-text-alt text-red-500">{{ errors.tanggal_pemeriksaan }}</span>
                  </div>
                </div>
              </div>

              <!-- Diagnosis -->
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Diagnosa <span class="text-red-500">*</span></span>
                </label>
                <textarea 
                  v-model="form.diagnosa" 
                  class="textarea textarea-bordered h-24" 
                  :class="{ 'textarea-error': errors.diagnosa }"
                  placeholder="Diagnosa hasil pemeriksaan..."
                  maxlength="1000"
                  required
                ></textarea>
                <div class="label">
                  <span class="label-text-alt">{{ form.diagnosa.length }}/1000 karakter</span>
                  <span v-if="errors.diagnosa" class="label-text-alt text-red-500">{{ errors.diagnosa }}</span>
                </div>
              </div>

              <!-- Treatment/Action -->
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Tindakan <span class="text-red-500">*</span></span>
                </label>
                <textarea 
                  v-model="form.tindakan" 
                  class="textarea textarea-bordered h-24" 
                  :class="{ 'textarea-error': errors.tindakan }"
                  placeholder="Tindakan yang dilakukan..."
                  maxlength="1000"
                  required
                ></textarea>
                <div class="label">
                  <span class="label-text-alt">{{ form.tindakan.length }}/1000 karakter</span>
                  <span v-if="errors.tindakan" class="label-text-alt text-red-500">{{ errors.tindakan }}</span>
                </div>
              </div>

              <!-- Notes -->
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Catatan Tambahan</span>
                </label>
                <textarea 
                  v-model="form.catatan" 
                  class="textarea textarea-bordered h-32" 
                  placeholder="Catatan tambahan, rekomendasi, atau instruksi khusus..."
                  maxlength="2000"
                ></textarea>
                <div class="label">
                  <span class="label-text-alt">{{ form.catatan.length }}/2000 karakter</span>
                </div>
              </div>

              <!-- Allergy and Medication History -->
              <div class="card bg-base-100 shadow-md">
                <div class="card-body">
                  <h3 class="card-title mb-4">Riwayat Alergi dan Obat-Obatan</h3>
                  
                  <!-- Allergies Section -->
                  <div class="mb-6">
                    <div class="flex justify-between items-center mb-3">
                      <label class="label-text font-semibold">Riwayat Alergi</label>
                      <button 
                        type="button" 
                        @click="addAllergy" 
                        class="btn btn-sm btn-outline btn-primary"
                      >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Alergi
                      </button>
                    </div>
                    <div v-if="form.allergies.length === 0" class="text-gray-500 text-sm italic p-4 bg-gray-50 rounded">
                      Belum ada riwayat alergi yang ditambahkan
                    </div>
                    <div v-else class="space-y-2">
                      <div 
                        v-for="(allergy, index) in form.allergies" 
                        :key="index"
                        class="flex items-center space-x-2 p-3 bg-red-50 border border-red-200 rounded"
                      >
                        <input 
                          v-model="allergy.name" 
                          type="text" 
                          placeholder="Nama alergi (contoh: Penisilin, Kacang, dll)" 
                          class="input input-sm input-bordered flex-1"
                        />
                        <button 
                          type="button" 
                          @click="removeAllergy(index)" 
                          class="btn btn-sm btn-ghost text-red-500 hover:bg-red-100"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Medications Section -->
                  <div>
                    <div class="flex justify-between items-center mb-3">
                      <label class="label-text font-semibold">Riwayat Obat-Obatan</label>
                      <button 
                        type="button" 
                        @click="addMedication" 
                        class="btn btn-sm btn-outline btn-primary"
                      >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Obat
                      </button>
                    </div>
                    <div v-if="form.medications.length === 0" class="text-gray-500 text-sm italic p-4 bg-gray-50 rounded">
                      Belum ada riwayat obat-obatan yang ditambahkan
                    </div>
                    <div v-else class="space-y-2">
                      <div 
                        v-for="(medication, index) in form.medications" 
                        :key="index"
                        class="flex items-center space-x-2 p-3 bg-blue-50 border border-blue-200 rounded"
                      >
                        <input 
                          v-model="medication.name" 
                          type="text" 
                          placeholder="Nama obat" 
                          class="input input-sm input-bordered flex-1"
                        />
                        <input 
                          v-model="medication.dosage" 
                          type="text" 
                          placeholder="Dosis" 
                          class="input input-sm input-bordered w-24"
                        />
                        <input 
                          v-model="medication.frequency" 
                          type="text" 
                          placeholder="Frekuensi" 
                          class="input input-sm input-bordered w-32"
                        />
                        <button 
                          type="button" 
                          @click="removeMedication(index)" 
                          class="btn btn-sm btn-ghost text-red-500 hover:bg-red-100"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Medical History (if patient selected) -->
              <div v-if="patientMedicalHistory.length > 0" class="card bg-base-100 shadow-md">
                <div class="card-body">
                  <h3 class="card-title">Riwayat Rekam Medis</h3>
                  <div class="space-y-2 max-h-40 overflow-y-auto">
                    <div 
                      v-for="history in patientMedicalHistory" 
                      :key="history.id"
                      class="p-3 bg-base-200 rounded-lg text-sm"
                    >
                      <div class="flex justify-between items-start mb-1">
                        <span class="font-medium">{{ formatDate(history.tanggal_pemeriksaan) }}</span>
                        <span class="text-xs text-gray-500">{{ history.user.name }}</span>
                      </div>
                      <div class="text-gray-700">
                        <strong>Diagnosa:</strong> {{ history.diagnosa }}
                      </div>
                      <div class="text-gray-700">
                        <strong>Tindakan:</strong> {{ history.tindakan }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Attachments Section -->
              <div class="card bg-base-100 shadow-md">
                <div class="card-body">
                  <h3 class="card-title">Lampiran Dokumen</h3>
                  <div class="space-y-4">
                    <!-- File Upload -->
                    <div class="form-control">
                      <label class="label">
                        <span class="label-text">Tambah Lampiran</span>
                      </label>
                      <input 
                        type="file" 
                        ref="fileInput"
                        @change="handleFileSelect"
                        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                        multiple
                        class="file-input file-input-bordered w-full"
                      />
                      <div class="label">
                        <span class="label-text-alt text-gray-500">
                          Format yang didukung: PDF, JPG, PNG, DOC, DOCX. Maksimal 5MB per file.
                        </span>
                      </div>
                    </div>

                    <!-- Selected Files Preview -->
                    <div v-if="selectedFiles.length > 0" class="space-y-3">
                      <h4 class="font-medium">File yang akan diunggah:</h4>
                      <div 
                        v-for="(fileData, index) in selectedFiles" 
                        :key="index"
                        class="p-4 bg-base-200 rounded-lg"
                      >
                        <div class="flex items-center justify-between mb-3">
                          <div class="flex items-center">
                            <svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <div>
                              <div class="font-medium">{{ fileData.file.name }}</div>
                              <div class="text-sm text-gray-500">{{ formatFileSize(fileData.file.size) }}</div>
                            </div>
                          </div>
                          <button 
                            type="button"
                            @click="removeFile(index)"
                            class="btn btn-sm btn-ghost text-red-500 hover:bg-red-100"
                          >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                          </button>
                        </div>
                        
                        <!-- File Type Selection -->
                        <div class="form-control">
                          <label class="label">
                            <span class="label-text font-medium">Tipe Dokumen</span>
                          </label>
                          <select 
                            v-model="fileData.type" 
                            class="select select-bordered select-sm w-full"
                            required
                          >
                            <option value="">Pilih Tipe</option>
                            <option value="foto_sebelum">Foto Sebelum Treatment</option>
                            <option value="foto_sesudah">Foto Sesudah Treatment</option>
                            <option value="hasil_lab">Hasil Lab/Tes</option>
                            <option value="lainnya">Lainnya</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="flex justify-end space-x-4">
                <button 
                  type="button"
                  @click="handleCancelClick"
                  class="btn btn-outline">
                  Batal
                </button>
                <button 
                  type="submit" 
                  class="btn btn-primary"
                  :disabled="processing"
                >
                  <span v-if="processing" class="loading loading-spinner loading-sm"></span>
                  {{ processing ? 'Menyimpan...' : 'Simpan Rekam Medis' }}
                </button>
              </div>
            </form>

            <!-- Cancel Confirmation Modal -->
            <div v-if="showCancelModal" class="modal modal-open">
              <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">Konfirmasi Pembatalan</h3>
                <p class="mb-6">Apakah Anda yakin ingin membatalkan pembuatan rekam medis? Data yang telah diisi akan hilang.</p>
                <div class="modal-action">
                  <button @click="showCancelModal = false" class="btn btn-outline">Tidak</button>
                  <Link :href="route('medical-records.index')" class="btn btn-error">Ya, Batalkan</Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </SimpleLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import { useForm, router } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import SimpleLayout from '~/Layouts/SimpleLayout.vue'

export default {
  components: {
    Head,
    Link,
    SimpleLayout,
  },
  props: {
    patients: Array,
    selectedPatient: Object,
    selectedReservation: Object,
    errors: Object,
  },
  setup(props) {
    const form = useForm({
      patient_id: props.selectedPatient?.id || '',
      reservation_id: props.selectedReservation?.id || '',
      tanggal_pemeriksaan: new Date().toISOString().split('T')[0],
      diagnosa: '',
      tindakan: '',
      catatan: '',
      allergies: [],
      medications: [],
    })

    const patientMedicalHistory = ref([])
    const currentSelectedPatient = ref(props.selectedPatient)
    const showBackModal = ref(false)
    const showCancelModal = ref(false)
    const selectedFiles = ref([])
    const fileInput = ref(null)

    // Track if form has been modified
    const isDirty = computed(() => {
      return form.patient_id !== (props.selectedPatient?.id || '') ||
             form.reservation_id !== (props.selectedReservation?.id || '') ||
             form.diagnosa !== '' ||
             form.tindakan !== '' ||
             form.catatan !== '' ||
             form.allergies.length > 0 ||
             form.medications.length > 0 ||
             selectedFiles.value.length > 0
    })

    // Functions for managing allergies
    const addAllergy = () => {
      form.allergies.push({ name: '' })
    }

    const removeAllergy = (index) => {
      form.allergies.splice(index, 1)
    }

    // Functions for managing medications
    const addMedication = () => {
      form.medications.push({ name: '', dosage: '', frequency: '' })
    }

    const removeMedication = (index) => {
      form.medications.splice(index, 1)
    }

    // Functions for managing file attachments
    const handleFileSelect = (event) => {
      const files = Array.from(event.target.files)
      const validFiles = []
      const maxSize = 5 * 1024 * 1024 // 5MB
      
      files.forEach(file => {
        if (file.size > maxSize) {
          alert(`File ${file.name} terlalu besar. Maksimal 5MB per file.`)
          return
        }
        
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 
                             'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
        if (!allowedTypes.includes(file.type)) {
          alert(`File ${file.name} tidak didukung. Gunakan format PDF, JPG, PNG, DOC, atau DOCX.`)
          return
        }
        
        // Create file object with metadata
        const fileData = {
          file: file,
          type: ''
        }
        
        validFiles.push(fileData)
      })
      
      selectedFiles.value = [...selectedFiles.value, ...validFiles]
      
      // Reset file input
      if (fileInput.value) {
        fileInput.value.value = ''
      }
    }

    const removeFile = (index) => {
      selectedFiles.value.splice(index, 1)
    }

    const formatFileSize = (bytes) => {
      if (bytes === 0) return '0 Bytes'
      const k = 1024
      const sizes = ['Bytes', 'KB', 'MB', 'GB']
      const i = Math.floor(Math.log(bytes) / Math.log(k))
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
    }

    // Functions to handle modal confirmations
    const handleBackClick = () => {
      if (isDirty.value) {
        showBackModal.value = true
      } else {
        window.location.href = route('medical-records.index')
      }
    }

    const handleCancelClick = () => {
      if (isDirty.value) {
        showCancelModal.value = true
      } else {
        window.location.href = route('medical-records.index')
      }
    }

    const today = computed(() => {
      return new Date().toISOString().split('T')[0]
    })

    const updateSelectedPatient = () => {
      const patient = props.patients.find(p => p.id == form.patient_id)
      currentSelectedPatient.value = patient
      
      if (patient) {
        loadPatientHistory(patient.id)
      } else {
        patientMedicalHistory.value = []
      }
    }

    const loadPatientHistory = async (patientId) => {
      try {
        const response = await axios.get(`/api/patients/${patientId}/records`)
        patientMedicalHistory.value = response.data.medicalRecords.slice(0, 5) // Show last 5 records
      } catch (error) {
        console.error('Error loading patient history:', error)
        patientMedicalHistory.value = []
      }
    }

    const calculateAge = (birthDate) => {
      const today = new Date()
      const birth = new Date(birthDate)
      let age = today.getFullYear() - birth.getFullYear()
      const monthDiff = today.getMonth() - birth.getMonth()
      
      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age--
      }
      
      return age
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
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
      // Validate file document types
      for (let i = 0; i < selectedFiles.value.length; i++) {
        const fileData = selectedFiles.value[i]
        if (!fileData.type) {
          alert(`Mohon pilih tipe dokumen untuk file "${fileData.file.name}"`)
          return
        }
      }
      
      // Create FormData to handle file uploads
      const formData = new FormData()
      
      // Add regular form data
      formData.append('patient_id', form.patient_id)
      formData.append('reservation_id', form.reservation_id || '')
      formData.append('tanggal_pemeriksaan', form.tanggal_pemeriksaan)
      formData.append('diagnosa', form.diagnosa)
      formData.append('tindakan', form.tindakan)
      formData.append('catatan', form.catatan)
      formData.append('allergies', JSON.stringify(form.allergies))
      formData.append('medications', JSON.stringify(form.medications))
      
      // Add files with metadata
      selectedFiles.value.forEach((fileData, index) => {
        formData.append(`attachments[${index}]`, fileData.file)
        formData.append(`attachment_types[${index}]`, fileData.type)
      })
      
      // Submit using router.post with FormData
      router.post(route('medical-records.store'), formData, {
        forceFormData: true,
        onSuccess: () => {
          // Success handled by server redirect
        },
        onError: (errors) => {
          console.error('Validation errors:', errors)
        }
      })
    }

    // Load initial patient history if selectedPatient is provided
    onMounted(() => {
      if (props.selectedPatient) {
        loadPatientHistory(props.selectedPatient.id)
      }
    })

    return {
      form,
      patientMedicalHistory,
      selectedPatient: currentSelectedPatient,
      selectedReservation: computed(() => props.selectedReservation),
      showBackModal,
      showCancelModal,
      isDirty,
      handleBackClick,
      handleCancelClick,
      today,
      updateSelectedPatient,
      addAllergy,
      removeAllergy,
      addMedication,
      removeMedication,
      selectedFiles,
      fileInput,
      handleFileSelect,
      removeFile,
      formatFileSize,
      calculateAge,
      formatDate,
      formatTime,
      formatCurrency,
      submit,
      processing: computed(() => form.processing),
    }
  }
}
</script>
