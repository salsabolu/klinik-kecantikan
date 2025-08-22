<template>
  <SimpleLayout>
    <Head title="Edit Rekam Medis" />
    
    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold">Edit Rekam Medis #{{ medicalRecord?.id || 'Loading...' }}</h2>
              <button 
                type="button"
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
                <p class="mb-6">Apakah Anda yakin ingin kembali? Perubahan yang belum disimpan akan hilang.</p>
                <div class="modal-action">
                  <button type="button" @click="showBackModal = false" class="btn btn-outline">Tidak</button>
                  <Link :href="medicalRecord?.id ? route('medical-records.show', medicalRecord.id) : '#'" class="btn btn-error">Ya, Kembali</Link>
                </div>
              </div>
            </div>

            <!-- Patient Info -->
            <div class="alert alert-info mb-6" v-if="selectedPatient">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
              <div>
                <h3 class="font-bold">{{ selectedPatient?.nama_lengkap || 'Loading...' }}</h3>
                <div class="text-xs">
                  {{ selectedPatient?.jenis_kelamin || '' }} | {{ calculateAge(selectedPatient?.tanggal_lahir) }} tahun | 
                  {{ selectedPatient?.nomor_telepon || '' }}
                </div>
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
                    :class="{ 'select-error': errors?.patient_id }"
                    @change="updateSelectedPatient"
                    required
                  >
                    <option value="">Pilih Pasien</option>
                    <option v-for="patient in patients" :key="patient.id" :value="patient.id">
                      {{ patient.nama_lengkap }} - {{ patient.nomor_telepon }}
                    </option>
                  </select>
                  <div v-if="errors?.patient_id" class="label">
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
                    <div class="flex justify-between items-center mb-2">
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
                        class="flex items-center space-x-2"
                      >
                        <input 
                          type="text" 
                          v-model="allergy.name" 
                          class="input input-bordered flex-1" 
                          placeholder="Nama alergi (contoh: Penisilin, Kacang, dll)" 
                        />
                        <button 
                          type="button" 
                          @click="removeAllergy(index)" 
                          class="btn btn-sm btn-ghost text-red-500"
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
                    <div class="flex justify-between items-center mb-2">
                      <label class="label-text font-semibold">Riwayat Obat-Obatan</label>
                      <button 
                        type="button"
                        @click="addMedication" 
                        class="btn btn-sm btn-outline btn-success"
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
                        class="flex items-center space-x-2"
                      >
                        <input 
                          type="text" 
                          v-model="medication.name" 
                          class="input input-bordered flex-1" 
                          placeholder="Nama obat" 
                        />
                        <input 
                          type="text" 
                          v-model="medication.dosage" 
                          class="input input-bordered w-32" 
                          placeholder="Dosis" 
                        />
                        <input 
                          type="text" 
                          v-model="medication.frequency" 
                          class="input input-bordered w-32" 
                          placeholder="Frekuensi" 
                        />
                        <button 
                          type="button" 
                          @click="removeMedication(index)" 
                          class="btn btn-sm btn-ghost text-red-500"
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

              <!-- Current vs Original Info -->
              <div class="card bg-base-100 shadow-md">
                <div class="card-body">
                  <h3 class="card-title">Informasi Asli</h3>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                      <span class="font-medium">Tanggal Pemeriksaan:</span>
                      <br>{{ formatDate(medicalRecord?.tanggal_pemeriksaan) }}
                    </div>
                    <div>
                      <span class="font-medium">Dibuat oleh:</span>
                      <br>{{ medicalRecord?.user?.name || 'Loading...' }}
                    </div>
                    <div>
                      <span class="font-medium">Dibuat pada:</span>
                      <br>{{ formatDateTime(medicalRecord?.created_at) }}
                    </div>
                    <div>
                      <span class="font-medium">Terakhir diperbarui:</span>
                      <br>{{ formatDateTime(medicalRecord?.updated_at) }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Existing Attachments -->
              <div v-if="medicalRecord?.attachments && medicalRecord.attachments.length > 0" class="card bg-base-100 shadow-md">
                <div class="card-body">
                  <h3 class="card-title">Lampiran yang Ada</h3>
                  <div class="space-y-3">
                    <div 
                      v-for="attachment in medicalRecord.attachments" 
                      :key="attachment.id"
                      class="flex items-center justify-between p-3 bg-base-200 rounded-lg"
                    >
                      <div class="flex items-center">
                        <svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <div>
                          <div class="font-medium">{{ attachment.filename }}</div>
                          <div class="text-sm text-gray-500">{{ formatFileSize(attachment.size) }}</div>
                        </div>
                      </div>
                      <div class="flex space-x-2">
                        <a 
                          :href="route('attachments.view', attachment.id)"
                          target="_blank"
                          class="btn btn-sm btn-outline btn-primary"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                          </svg>
                          Lihat
                        </a>
                        <a 
                          :href="route('attachments.download', attachment.id)"
                          class="btn btn-sm btn-outline btn-success"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                          </svg>
                          Unduh
                        </a>
                        <button
                          type="button"
                          @click="deleteAttachment(attachment.id)"
                          class="btn btn-sm btn-outline btn-error"
                          :disabled="deletingAttachments.includes(attachment.id)"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                          Hapus
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Add New Attachments -->
              <div class="card bg-base-100 shadow-md">
                <div class="card-body">
                  <h3 class="card-title">Tambah Lampiran Baru</h3>
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
                  {{ processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </button>
              </div>
            </form>

            <!-- Cancel Confirmation Modal -->
            <div v-if="showCancelModal" class="modal modal-open">
              <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">Konfirmasi Pembatalan</h3>
                <p class="mb-6">Apakah Anda yakin ingin membatalkan perubahan? Data yang telah diubah akan hilang.</p>
                <div class="modal-action">
                  <button type="button" @click="showCancelModal = false" class="btn btn-outline">Tidak</button>
                  <Link :href="medicalRecord?.id ? route('medical-records.show', medicalRecord.id) : '#'" class="btn btn-error">Ya, Batalkan</Link>
                </div>
              </div>
            </div>

            <!-- Delete Attachment Confirmation Modal -->
            <div v-if="showDeleteAttachmentModal" class="modal modal-open">
              <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">Konfirmasi Hapus Lampiran</h3>
                <p class="mb-6">Apakah Anda yakin ingin menghapus lampiran ini? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="modal-action">
                  <button type="button" @click="showDeleteAttachmentModal = false; attachmentToDelete = null" class="btn btn-outline">Tidak</button>
                  <button type="button" @click="confirmDeleteAttachment" class="btn btn-error">Ya, Hapus</button>
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
import SimpleLayout from '~/Layouts/SimpleLayout.vue'

export default {
  components: {
    Head,
    Link,
    SimpleLayout,
  },
  props: {
    medicalRecord: Object,
    patients: Array,
    errors: Object,
  },
  setup(props) {
    // Convert the date to proper format for input[type="date"]
    const formatDateForInput = (dateString) => {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toISOString().split('T')[0]
    }

    const form = useForm({
      patient_id: props.medicalRecord?.patient_id || '',
      tanggal_pemeriksaan: formatDateForInput(props.medicalRecord?.tanggal_pemeriksaan) || '',
      diagnosa: props.medicalRecord?.diagnosa || '',
      tindakan: props.medicalRecord?.tindakan || '',
      catatan: props.medicalRecord?.catatan || '',
      allergies: props.medicalRecord?.allergies || [],
      medications: props.medicalRecord?.medications || [],
    })

    const selectedPatient = ref(null)
    const showBackModal = ref(false)
    const showCancelModal = ref(false)
    const showDeleteAttachmentModal = ref(false)
    const attachmentToDelete = ref(null)
    const selectedFiles = ref([])
    const fileInput = ref(null)
    const deletingAttachments = ref([])

    // Store original values for dirty state checking
    const originalValues = {
      patient_id: props.medicalRecord?.patient_id || '',
      tanggal_pemeriksaan: formatDateForInput(props.medicalRecord?.tanggal_pemeriksaan) || '',
      diagnosa: props.medicalRecord?.diagnosa || '',
      tindakan: props.medicalRecord?.tindakan || '',
      catatan: props.medicalRecord?.catatan || '',
      allergies: props.medicalRecord?.allergies || [],
      medications: props.medicalRecord?.medications || [],
    }

    // Check if form has changes
    const isDirty = computed(() => {
      return form.patient_id !== originalValues.patient_id ||
             form.tanggal_pemeriksaan !== originalValues.tanggal_pemeriksaan ||
             form.diagnosa !== originalValues.diagnosa ||
             form.tindakan !== originalValues.tindakan ||
             form.catatan !== originalValues.catatan ||
             JSON.stringify(form.allergies) !== JSON.stringify(originalValues.allergies) ||
             JSON.stringify(form.medications) !== JSON.stringify(originalValues.medications) ||
             selectedFiles.value.length > 0 ||
             deletingAttachments.value.length > 0
    })

    const today = computed(() => {
      return new Date().toISOString().split('T')[0]
    })

    const updateSelectedPatient = () => {
      if (props.patients && form.patient_id) {
        const patient = props.patients.find(p => p.id == form.patient_id)
        selectedPatient.value = patient
      }
    }

    const calculateAge = (birthDate) => {
      if (!birthDate) return 0
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
      if (!date) return 'Loading...'
      return new Date(date).toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    const formatDateTime = (datetime) => {
      if (!datetime) return 'Loading...'
      return new Date(datetime).toLocaleString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    // File handling functions
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
        
        // Create file object with metadata like in Create page
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

    const deleteAttachment = (attachmentId) => {
      attachmentToDelete.value = attachmentId
      showDeleteAttachmentModal.value = true
    }

    const confirmDeleteAttachment = async () => {
      if (!attachmentToDelete.value) return

      deletingAttachments.value.push(attachmentToDelete.value)

      try {
        // Use axios with proper headers and credentials like in Reservations module
        const response = await window.axios.delete(`/api/attachments/${attachmentToDelete.value}`, {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
          withCredentials: true
        })
        
        if (response.data.success) {
          // Remove attachment from the list
          if (props.medicalRecord?.attachments) {
            const index = props.medicalRecord.attachments.findIndex(a => a.id === attachmentToDelete.value)
            if (index > -1) {
              props.medicalRecord.attachments.splice(index, 1)
            }
          }
          showDeleteAttachmentModal.value = false
          attachmentToDelete.value = null
        } else {
          throw new Error(response.data.message || 'Failed to delete attachment')
        }
      } catch (error) {
        console.error('Error deleting attachment:', error)
        if (error.response) {
          // Server responded with error status
          const message = error.response.data?.message || 'Gagal menghapus lampiran. Silakan coba lagi.'
          alert(`Error: ${message}`)
        } else if (error.request) {
          // Network error
          alert('Gagal terhubung ke server. Periksa koneksi internet Anda.')
        } else {
          // Other error
          alert('Gagal menghapus lampiran. Silakan coba lagi.')
        }
      } finally {
        const index = deletingAttachments.value.indexOf(attachmentToDelete.value)
        if (index > -1) {
          deletingAttachments.value.splice(index, 1)
        }
        showDeleteAttachmentModal.value = false
        attachmentToDelete.value = null
      }
    }

    // Allergy management
    const addAllergy = () => {
      form.allergies.push({ name: '' })
    }

    const removeAllergy = (index) => {
      form.allergies.splice(index, 1)
    }

    // Medication management
    const addMedication = () => {
      form.medications.push({ name: '', dosage: '', frequency: '' })
    }

    const removeMedication = (index) => {
      form.medications.splice(index, 1)
    }

    const submit = () => {
      if (!props.medicalRecord?.id) return

      // Validate file document types if there are new files
      for (let i = 0; i < selectedFiles.value.length; i++) {
        const fileData = selectedFiles.value[i]
        if (!fileData.type) {
          alert(`Mohon pilih tipe dokumen untuk file "${fileData.file.name}"`)
          return
        }
      }

      // Always use FormData to handle both regular data and files consistently
      const formData = new FormData()
      
      // Add regular form data
      formData.append('_method', 'PUT')
      formData.append('patient_id', form.patient_id)
      formData.append('tanggal_pemeriksaan', form.tanggal_pemeriksaan)
      formData.append('diagnosa', form.diagnosa)
      formData.append('tindakan', form.tindakan)
      formData.append('catatan', form.catatan || '')
      
      // Add allergies and medications as JSON
      formData.append('allergies', JSON.stringify(form.allergies))
      formData.append('medications', JSON.stringify(form.medications))
      
      // Add new files with metadata if any
      selectedFiles.value.forEach((fileData, index) => {
        formData.append(`attachments[${index}]`, fileData.file)
        formData.append(`attachment_types[${index}]`, fileData.type)
      })
      
      // Submit using router.post with FormData
      router.post(route('medical-records.update', props.medicalRecord.id), formData, {
        forceFormData: true,
        onSuccess: () => {
          selectedFiles.value = []
        },
        onError: (errors) => {
          console.error('Validation errors:', errors)
        }
      })
    }

    // Handle back button click with conditional modal
    const handleBackClick = () => {
      if (isDirty.value) {
        showBackModal.value = true
      } else {
        // Navigate directly if no changes
        window.location.href = route('medical-records.show', props.medicalRecord?.id)
      }
    }

    // Handle cancel button click with conditional modal
    const handleCancelClick = () => {
      if (isDirty.value) {
        showCancelModal.value = true
      } else {
        // Navigate directly if no changes
        window.location.href = route('medical-records.show', props.medicalRecord?.id)
      }
    }

    // Initialize selected patient
    onMounted(() => {
      updateSelectedPatient()
    })

    return {
      form,
      selectedPatient,
      showBackModal,
      showCancelModal,
      showDeleteAttachmentModal,
      attachmentToDelete,
      selectedFiles,
      fileInput,
      deletingAttachments,
      today,
      isDirty,
      updateSelectedPatient,
      handleFileSelect,
      removeFile,
      formatFileSize,
      deleteAttachment,
      confirmDeleteAttachment,
      addAllergy,
      removeAllergy,
      addMedication,
      removeMedication,
      calculateAge,
      formatDate,
      formatDateTime,
      submit,
      handleBackClick,
      handleCancelClick,
      processing: computed(() => form.processing),
    }
  }
}
</script>
