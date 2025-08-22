<template>
  <AppLayout title="Detail Rekam Medis">
    <Head title="Detail Rekam Medis" />
    
    <div class="py-12">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold">Detail Rekam Medis #{{ medicalRecord.id }}</h2>
              <div class="flex space-x-2">
                <Link :href="route('medical-records.index')" class="btn btn-outline">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                  </svg>
                  Kembali
                </Link>
                <Link 
                  :href="route('medical-records.edit', medicalRecord.id)" 
                  class="btn btn-warning"
                  v-if="canEdit"
                >
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                  Edit
                </Link>
              </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <!-- Main Content -->
              <div class="lg:col-span-2 space-y-6">
                <!-- Patient Information -->
                <div class="card bg-base-100 shadow-md">
                  <div class="card-body">
                    <h3 class="card-title">Informasi Pasien</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label class="label">
                          <span class="label-text font-medium">Nama Lengkap</span>
                        </label>
                        <div class="text-lg">{{ medicalRecord.patient.nama_lengkap }}</div>
                      </div>
                      <div>
                        <label class="label">
                          <span class="label-text font-medium">Umur</span>
                        </label>
                        <div class="text-lg">{{ calculateAge(medicalRecord.patient.tanggal_lahir) }} tahun</div>
                      </div>
                      <div>
                        <label class="label">
                          <span class="label-text font-medium">Jenis Kelamin</span>
                        </label>
                        <div class="text-lg">{{ medicalRecord.patient.jenis_kelamin }}</div>
                      </div>
                      <div>
                        <label class="label">
                          <span class="label-text font-medium">Nomor Telepon</span>
                        </label>
                        <div class="text-lg">{{ medicalRecord.patient.nomor_telepon }}</div>
                      </div>
                      <div class="md:col-span-2">
                        <label class="label">
                          <span class="label-text font-medium">Alamat</span>
                        </label>
                        <div class="text-sm text-gray-600">{{ medicalRecord.patient.alamat }}</div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Medical Record Details -->
                <div class="card bg-base-100 shadow-md">
                  <div class="card-body">
                    <h3 class="card-title">Detail Pemeriksaan</h3>
                    <div class="space-y-4">
                      <div>
                        <label class="label">
                          <span class="label-text font-medium">Diagnosa</span>
                        </label>
                        <div class="p-4 bg-base-200 rounded-lg">
                          {{ medicalRecord.diagnosa }}
                        </div>
                      </div>
                      
                      <div>
                        <label class="label">
                          <span class="label-text font-medium">Tindakan</span>
                        </label>
                        <div class="p-4 bg-base-200 rounded-lg">
                          {{ medicalRecord.tindakan }}
                        </div>
                      </div>

                      <div v-if="medicalRecord.catatan">
                        <label class="label">
                          <span class="label-text font-medium">Catatan Tambahan</span>
                        </label>
                        <div class="p-4 bg-base-200 rounded-lg">
                          {{ medicalRecord.catatan }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Allergy and Medication History -->
                <div v-if="medicalRecord.allergies?.length > 0 || medicalRecord.medications?.length > 0" class="card bg-base-100 shadow-md">
                  <div class="card-body">
                    <h3 class="card-title">Riwayat Alergi dan Obat-Obatan</h3>
                    
                    <!-- Allergies Section -->
                    <div v-if="medicalRecord.allergies?.length > 0" class="mb-6">
                      <h4 class="font-semibold text-red-700 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        Riwayat Alergi
                      </h4>
                      <div class="space-y-2">
                        <div 
                          v-for="(allergy, index) in medicalRecord.allergies" 
                          :key="index"
                          class="flex items-center p-3 bg-red-50 border border-red-200 rounded-lg"
                        >
                          <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                          </svg>
                          <span class="text-red-800 font-medium">{{ allergy.name }}</span>
                        </div>
                      </div>
                    </div>

                    <!-- Medications Section -->
                    <div v-if="medicalRecord.medications?.length > 0">
                      <h4 class="font-semibold text-blue-700 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                        Riwayat Obat-Obatan
                      </h4>
                      <div class="space-y-2">
                        <div 
                          v-for="(medication, index) in medicalRecord.medications" 
                          :key="index"
                          class="p-3 bg-blue-50 border border-blue-200 rounded-lg"
                        >
                          <div class="flex items-start">
                            <svg class="w-4 h-4 text-blue-500 mr-2 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                            <div class="flex-1">
                              <div class="font-medium text-blue-800">{{ medication.name }}</div>
                              <div class="text-sm text-blue-600 mt-1">
                                <span v-if="medication.dosage" class="mr-3"><strong>Dosis:</strong> {{ medication.dosage }}</span>
                                <span v-if="medication.frequency"><strong>Frekuensi:</strong> {{ medication.frequency }}</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Patient Medical History -->
                <div v-if="patientHistory.length > 0" class="card bg-base-100 shadow-md">
                  <div class="card-body">
                    <h3 class="card-title">Riwayat Rekam Medis Pasien</h3>
                    <div class="space-y-3">
                      <div 
                        v-for="history in patientHistory" 
                        :key="history.id"
                        class="p-4 border border-base-300 rounded-lg hover:bg-base-50 transition-colors"
                      >
                        <div class="flex justify-between items-start mb-2">
                          <span class="font-medium">{{ formatDate(history.tanggal_pemeriksaan) }}</span>
                          <span class="text-sm text-gray-500">{{ history.user.name }}</span>
                        </div>
                        <div class="space-y-1 text-sm">
                          <div>
                            <span class="font-medium">Diagnosa:</span> {{ history.diagnosa }}
                          </div>
                          <div>
                            <span class="font-medium">Tindakan:</span> {{ history.tindakan }}
                          </div>
                          <div v-if="history.catatan" class="text-gray-600">
                            <span class="font-medium">Catatan:</span> {{ history.catatan }}
                          </div>
                        </div>
                        <div class="mt-2">
                          <Link 
                            :href="route('medical-records.show', history.id)" 
                            class="text-primary hover:text-primary-focus text-sm"
                          >
                            Lihat Detail →
                          </Link>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Attachments Section -->
                <div class="card bg-base-100 shadow-md">
                  <div class="card-body">
                    <h3 class="card-title">Lampiran Dokumen</h3>
                    <div v-if="medicalRecord.attachments && medicalRecord.attachments.length > 0" class="space-y-3">
                      <div 
                        v-for="attachment in medicalRecord.attachments" 
                        :key="attachment.id"
                        class="flex items-center justify-between p-3 bg-base-200 rounded-lg"
                      >
                        <div class="flex items-center">
                          <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                          </svg>
                          <div>
                            <div class="font-medium">{{ attachment.filename }}</div>
                            <div class="text-sm text-gray-500">{{ formatFileSize(attachment.size) }} • {{ formatAttachmentType(attachment.type) }}</div>
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
                        </div>
                      </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                      <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      </svg>
                      <p>Tidak ada lampiran dokumen</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Sidebar -->
              <div class="space-y-6">
                <!-- Examination Info -->
                <div class="card bg-primary text-primary-content shadow-md">
                  <div class="card-body">
                    <h3 class="card-title">Informasi Pemeriksaan</h3>
                    <div class="space-y-3">
                      <div>
                        <div class="text-sm opacity-70">Tanggal Pemeriksaan</div>
                        <div class="text-lg font-bold">{{ formatDate(medicalRecord.tanggal_pemeriksaan) }}</div>
                      </div>
                      <div>
                        <div class="text-sm opacity-70">Dokter/Terapis</div>
                        <div class="text-lg font-semibold">{{ medicalRecord.user.name }}</div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Quick Actions -->
                <div class="card bg-base-100 shadow-md">
                  <div class="card-body">
                    <h3 class="card-title">Aksi Cepat</h3>
                    <div class="space-y-2">
                      <Link 
                        :href="route('medical-records.create', { patient_id: medicalRecord.patient.id })" 
                        class="btn btn-success btn-block"
                        v-if="$page.props.auth.user.role === 'admin' || $page.props.auth.user.role === 'dokter'"
                      >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Rekam Medis Baru
                      </Link>

                      <Link 
                        :href="route('patients.show', medicalRecord.patient.id)" 
                        class="btn btn-outline btn-block"
                        v-if="$page.props.auth.user.role === 'admin' || $page.props.auth.user.role === 'resepsionis'"
                      >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Lihat Profil Pasien
                      </Link>

                      <button 
                        @click="printRecord" 
                        class="btn btn-info btn-block"
                      >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Cetak
                      </button>

                      <button 
                        @click="confirmDelete" 
                        class="btn btn-error btn-block"
                        v-if="canDelete"
                      >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Record Meta -->
                <div class="card bg-base-100 shadow-md">
                  <div class="card-body">
                    <h3 class="card-title">Informasi Sistem</h3>
                    <div class="space-y-2 text-sm">
                      <div>
                        <span class="font-medium">Dibuat:</span>
                        <br>{{ formatDateTime(medicalRecord.created_at) }}
                      </div>
                      <div>
                        <span class="font-medium">Diperbarui:</span>
                        <br>{{ formatDateTime(medicalRecord.updated_at) }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg">Konfirmasi Hapus</h3>
        <p class="py-4">Apakah Anda yakin ingin menghapus rekam medis ini? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="modal-action">
          <button @click="showDeleteModal = false" class="btn">Tidak</button>
          <button @click="deleteRecord" class="btn btn-error">Ya, Hapus</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import { router, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  components: {
    Head,
    Link,
    AppLayout,
  },
  props: {
    medicalRecord: Object,
    patientHistory: Array,
  },
  setup(props) {
    const showDeleteModal = ref(false)
    const page = usePage()

    const canEdit = computed(() => {
      const user = page.props.auth.user
      return user.role === 'admin' || (user.role === 'dokter' && props.medicalRecord.user_id === user.id)
    })

    const canDelete = computed(() => {
      const user = page.props.auth.user
      return user.role === 'admin' || (user.role === 'dokter' && props.medicalRecord.user_id === user.id)
    })

    const confirmDelete = () => {
      showDeleteModal.value = true
    }

    const deleteRecord = () => {
      router.delete(route('medical-records.destroy', props.medicalRecord.id), {
        onSuccess: () => {
          showDeleteModal.value = false
        }
      })
    }

    const printRecord = () => {
      window.print()
    }

    const formatAttachmentType = (type) => {
      const typeMap = {
        'foto_sebelum': 'Foto Sebelum Treatment',
        'foto_sesudah': 'Foto Sesudah Treatment',
        'hasil_lab': 'Hasil Lab/Tes',
        'lainnya': 'Lainnya',
      }
      return typeMap[type] || 'Unknown File Type'
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
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    const formatDateTime = (datetime) => {
      return new Date(datetime).toLocaleString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    const formatFileSize = (bytes) => {
      if (bytes === 0) return '0 Bytes'
      const k = 1024
      const sizes = ['Bytes', 'KB', 'MB', 'GB']
      const i = Math.floor(Math.log(bytes) / Math.log(k))
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
    }

    return {
      showDeleteModal,
      canEdit,
      canDelete,
      confirmDelete,
      deleteRecord,
      printRecord,
      formatAttachmentType,
      calculateAge,
      formatDate,
      formatDateTime,
      formatFileSize,
    }
  }
}
</script>

<style>
@media print {
  .btn, .navbar, .drawer-side {
    display: none !important;
  }
}
</style>
