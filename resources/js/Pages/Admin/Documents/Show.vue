<template>
  <AdminLayout :title="`Detail Dokumen - ${document.file_name || document.name}`">
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-text-primary">
        Detail Dokumen: {{ document.file_name || document.name }}
      </h2>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Notifikasi flash message -->
        <div v-if="$page.props.flash && $page.props.flash.success" class="mb-6">
          <div class="p-4 text-sm text-green-700 bg-green-100 dark:bg-green-900/30 dark:text-green-200 rounded-lg">
            {{ $page.props.flash.success }}
          </div>
        </div>
        
        <div v-if="$page.props.flash && $page.props.flash.error" class="mb-6">
          <div class="p-4 text-sm text-red-700 bg-red-100 dark:bg-red-900/30 dark:text-red-200 rounded-lg">
            {{ $page.props.flash.error }}
          </div>
        </div>

        <div class="overflow-hidden bg-background-primary shadow rounded-lg border border-border-light">
          <div class="px-4 py-5 sm:px-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div>
                <h3 class="text-lg font-medium leading-6 text-text-primary">
                  Informasi Dokumen
                </h3>
                <p class="max-w-2xl mt-1 text-sm text-text-secondary">
                  Detail dan informasi dokumen.
                </p>
              </div>
              <div class="flex flex-wrap gap-2">
                <button 
                  @click="openPreviewModal" 
                  class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                  Preview
                </button>
                
                <a 
                  :href="route('document.direct', document.id)" 
                  target="_blank"
                  class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                  </svg>
                  Download
                </a>
                
                <Link 
                  :href="route('admin.documents.edit', document.id)" 
                  class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-amber-600 border border-transparent rounded-lg shadow-sm hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-colors"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                  Edit
                </Link>
                
                <button 
                  @click="confirmDelete" 
                  class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                  Hapus
                </button>
                
                <Link 
                  :href="route('admin.documents.index')" 
                  class="inline-flex items-center px-3 py-2 text-sm font-medium text-text-primary bg-background-tertiary border border-border-light rounded-lg shadow-sm hover:bg-background-secondary focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                  </svg>
                  Kembali
                </Link>
              </div>
            </div>
          </div>
          <div class="border-t border-border-light">
            <dl>
              <div class="px-4 py-5 bg-background-secondary sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-text-secondary">
                  Nama File Asli
                </dt>
                <dd class="mt-1 text-sm text-text-primary font-medium sm:col-span-2 sm:mt-0">
                  {{ document.file_name || document.name || 'Tidak Tersedia' }}
                </dd>
              </div>
              <div class="px-4 py-5 bg-background-primary sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-text-secondary">
                  Pengirim
                </dt>
                <dd class="mt-1 text-sm text-text-primary sm:col-span-2 sm:mt-0">
                  {{ getPengirimName(document) }}
                </dd>
              </div>
              <div class="px-4 py-5 bg-background-secondary sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-text-secondary">
                  Deskripsi
                </dt>
                <dd class="mt-1 text-sm text-text-secondary sm:col-span-2 sm:mt-0">
                  {{ document.description || 'Tidak ada deskripsi' }}
                </dd>
              </div>
              <div class="px-4 py-5 bg-background-primary sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-text-secondary">
                  Tipe File
                </dt>
                <dd class="mt-1 text-sm text-text-primary sm:col-span-2 sm:mt-0">
                  {{ document.file_type || getFileTypeFromPath(document.file_path) }}
                </dd>
              </div>
              <div class="px-4 py-5 bg-background-secondary sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-text-secondary">
                  Ukuran File
                </dt>
                <dd class="mt-1 text-sm text-text-primary sm:col-span-2 sm:mt-0">
                  {{ formatFileSize(document.file_size) }}
                </dd>
              </div>
              <div class="px-4 py-5 bg-background-primary sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-text-secondary">
                  Status
                </dt>
                <dd class="mt-1 text-sm sm:col-span-2 sm:mt-0">
                  <span 
                    class="inline-flex px-2.5 py-0.5 text-xs font-semibold rounded-full"
                    :class="{
                      'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': document.status === 'pending',
                      'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': document.status === 'approved',
                      'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': document.status === 'rejected',
                    }"
                  >
                    {{ statusLabel(document.status) }}
                  </span>
                </dd>
              </div>
              <div class="px-4 py-5 bg-background-secondary sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-text-secondary">
                  Tanggal Upload
                </dt>
                <dd class="mt-1 text-sm text-text-primary sm:col-span-2 sm:mt-0">
                  {{ formatDate(document.uploaded_at) }}
                </dd>
              </div>
              <div class="px-4 py-5 bg-background-primary sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-text-secondary">
                  Diproses Oleh
                </dt>
                <dd class="mt-1 text-sm text-text-secondary sm:col-span-2 sm:mt-0">
                  {{ document?.user?.name || 'Sistem' }}
                </dd>
              </div>
            </dl>
          </div>
        </div>

        <!-- Aktivitas Log -->
        <div class="mt-6 overflow-hidden bg-background-primary shadow rounded-lg border border-border-light">
          <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg font-medium leading-6 text-text-primary">
              Aktivitas Dokumen
            </h3>
            <p class="max-w-2xl mt-1 text-sm text-text-secondary">
              Riwayat aktivitas untuk dokumen ini.
            </p>
          </div>
          <div class="border-t border-border-light">
            <div class="flow-root px-4 py-5 sm:px-6">
              <ul role="list" class="-mb-8">
                <li v-for="(activity, index) in activities" :key="activity.id">
                  <div class="relative pb-8">
                    <span v-if="index !== activities.length - 1" class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-border-light" aria-hidden="true"></span>
                    <div class="relative flex items-start space-x-3">
                      <div class="relative">
                        <div class="flex items-center justify-center w-10 h-10 bg-background-secondary rounded-full">
                          <svg v-if="activity.description === 'deleted document'" class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                          <svg v-else-if="activity.description === 'downloaded document'" class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                          </svg>
                          <svg v-else class="w-5 h-5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                          </svg>
                        </div>
                      </div>
                      <div class="min-w-0 flex-1">
                        <div>
                          <div class="text-sm">
                            <a href="#" class="font-medium text-text-primary">{{ activity.causer ? activity.causer.name : 'Sistem' }}</a>
                          </div>
                          <p class="mt-0.5 text-sm text-text-tertiary">
                            {{ formatDateTime(activity.created_at) }}
                          </p>
                        </div>
                        <div class="mt-2 text-sm text-text-secondary">
                          <p>
                            {{ formatActivityDescription(activity) }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li v-if="activities.length === 0">
                  <div class="py-6 text-center text-text-tertiary">
                    Belum ada aktivitas tercatat.
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Preview Modal -->
    <modal-dialog :show="showPreviewModal" @close="showPreviewModal = false" max-width="4xl">
      <div class="p-6 bg-background-primary">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium leading-6 text-text-primary">
            Preview: {{ document.file_name || document.name }}
          </h3>
          <button @click="showPreviewModal = false" class="text-text-tertiary hover:text-text-secondary transition-colors">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <div v-if="isLoading" class="flex items-center justify-center h-96 bg-background-secondary rounded-lg">
          <div class="flex flex-col items-center">
            <svg class="w-10 h-10 text-primary-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-2 text-sm text-text-secondary">Memuat preview...</p>
          </div>
        </div>
        
        <div v-else-if="previewError" class="flex flex-col items-center justify-center h-96 bg-background-secondary rounded-lg">
          <svg class="w-12 h-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ previewError }}</p>
          <p class="mt-1 text-xs text-text-tertiary px-6 text-center">
            Sistem tidak dapat menampilkan pratinjau dokumen ini. Dokumen mungkin rusak atau format tidak didukung untuk pratinjau. 
            Anda tetap dapat mengunduh dokumen aslinya.
          </p>
          <a :href="currentOriginalUrl" target="_blank" class="px-4 py-2 mt-4 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
            Download File Asli
          </a>
        </div>
        
        <div v-else-if="previewType === 'pdf'" class="h-96 rounded-lg overflow-hidden border border-border-light">
          <iframe :src="currentPreviewUrl" frameborder="0" width="100%" height="100%"></iframe>
        </div>
        
        <div v-else-if="previewType === 'external'" class="h-96">
          <div class="flex flex-col h-full">
            <div class="flex justify-between mb-2">
              <div class="flex flex-col w-full">
                <div class="flex space-x-2 mb-2">
                  <button 
                    @click="switchViewer('google')" 
                    class="px-3 py-1 text-xs rounded-md transition-colors"
                    :class="activeViewer === 'google' 
                      ? 'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300' 
                      : 'bg-background-tertiary text-text-secondary hover:bg-background-secondary'"
                  >
                    Google Docs Viewer
                  </button>
                  <button 
                    @click="switchViewer('office')" 
                    class="px-3 py-1 text-xs rounded-md transition-colors"
                    :class="activeViewer === 'office' 
                      ? 'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300' 
                      : 'bg-background-tertiary text-text-secondary hover:bg-background-secondary'"
                  >
                    Microsoft Office Viewer
                  </button>
                </div>
                <p class="text-xs text-text-tertiary mb-2">
                  Sistem menggunakan viewer eksternal (Google/Microsoft) untuk menampilkan dokumen non-PDF. 
                  Keberhasilan preview bergantung pada layanan pihak ketiga dan koneksi internet.
                </p>
              </div>
            </div>
            <iframe :src="currentPreviewUrl" frameborder="0" class="w-full h-full rounded-lg border border-border-light"></iframe>
          </div>
        </div>
        
        <div class="flex justify-end mt-6 space-x-3">
          <a :href="currentOriginalUrl" target="_blank" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
            Download File Asli
          </a>
          <button @click="showPreviewModal = false" class="px-4 py-2 text-sm font-medium text-text-primary bg-background-tertiary border border-border-light rounded-md shadow-sm hover:bg-background-secondary focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
            Tutup
          </button>
        </div>
      </div>
    </modal-dialog>

    <!-- Konfirmasi Hapus Modal -->
    <modal-dialog :show="showDeleteModal" @close="showDeleteModal = false">
      <div class="p-6 bg-background-primary">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium leading-6 text-text-primary">
            Konfirmasi Hapus
          </h3>
          <button @click="showDeleteModal = false" class="text-text-tertiary hover:text-text-secondary transition-colors">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <p class="text-sm text-text-secondary">
          Apakah Anda yakin ingin menghapus dokumen <strong class="text-text-primary font-medium">"{{ document.file_name || document.name }}"</strong>? 
          Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="flex justify-end mt-6 space-x-3">
          <button @click="showDeleteModal = false" class="px-4 py-2 text-sm font-medium text-text-primary bg-background-tertiary border border-border-light rounded-md shadow-sm hover:bg-background-secondary focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
            Batal
          </button>
          <Link
            :href="route('admin.documents.destroy', document.id)"
            method="delete"
            as="button"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors"
            @click="showDeleteModal = false"
          >
            Hapus
          </Link>
        </div>
      </div>
    </modal-dialog>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useForm, Link, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ModalDialog from '@/Components/ModalDialog.vue';

const props = defineProps({
  document: Object,
  activities: Array,
});

const showPreviewModal = ref(false);
const showDeleteModal = ref(false);
const isLoading = ref(false);
const previewError = ref(null);
const previewType = ref(null);
const currentPreviewUrl = ref('');
const currentOriginalUrl = ref('');
const officeViewerUrl = ref('');
const activeViewer = ref('google');
const localEnvironment = ref(false);

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  }).format(date);
};

const formatDateTime = (dateString) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(date);
};

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes';
  
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
  
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const statusLabel = (status) => {
  const labels = {
    'pending': 'Menunggu',
    'approved': 'Disetujui',
    'rejected': 'Ditolak'
  };
  
  return labels[status] || status;
};

const formatActivityDescription = (activity) => {
  switch (activity.description) {
    case 'created':
      return 'Membuat dokumen';
    case 'updated':
      return 'Mengupdate dokumen';
    case 'deleted document':
      return 'Menghapus dokumen';
    case 'downloaded document':
      return 'Mengunduh dokumen';
    default:
      return activity.description;
  }
};

// Fungsi untuk mendapatkan nama pengirim
const getPengirimName = (document) => {
  // Prioritaskan pengirim dari metadata
  if (document?.metadata?.pengirim) {
    return document.metadata.pengirim;
  }
  
  // Cek jika deskripsi berisi informasi pengirim (format lama)
  if (document?.description?.includes('Dari pengunjung:')) {
    return document.description.replace('Dari pengunjung:', '').trim();
  }

  // Cek jika deskripsi berisi nama pengirim dengan format lainnya
  if (document?.description?.includes('pengunjung:')) {
    return document.description.replace(/.*pengunjung:/, '').trim();
  }
  
  // Alternatif lain: cek dalam deskripsi jika ada format "dokumen dari [nama]"
  if (document?.description?.includes('dokumen dari')) {
    const match = document.description.match(/dokumen dari\s+([^\.]+)/i);
    if (match && match[1]) {
      return match[1].trim();
    }
  }
  
  // Sebagai fallback terakhir, gunakan nama uploader
  if (document?.user?.name && !document.user.name.includes('Admin')) {
    return document.user.name;
  }
  
  return 'Pengunjung';
};

// Fungsi untuk mendapatkan tipe file dari path jika tidak ada di properti file_type
const getFileTypeFromPath = (filePath) => {
  if (!filePath) return 'Tidak diketahui';
  
  const extension = filePath.split('.').pop().toLowerCase();
  const types = {
    'pdf': 'PDF Document',
    'doc': 'Word Document',
    'docx': 'Word Document',
    'xls': 'Excel Spreadsheet',
    'xlsx': 'Excel Spreadsheet',
    'ppt': 'PowerPoint Presentation',
    'pptx': 'PowerPoint Presentation',
    'txt': 'Text Document',
    'jpg': 'JPEG Image',
    'jpeg': 'JPEG Image',
    'png': 'PNG Image',
    'gif': 'GIF Image',
    'zip': 'ZIP Archive',
    'rar': 'RAR Archive'
  };
  
  return types[extension] || `${extension.toUpperCase()} File`;
};

const openPreviewModal = async () => {
  showPreviewModal.value = true;
  isLoading.value = true;
  previewError.value = null;
  previewType.value = null;
  currentPreviewUrl.value = '';
  officeViewerUrl.value = '';
  localEnvironment.value = false;
  
  try {
    // Tambahkan timestamp untuk mencegah cache
    const timestamp = new Date().getTime();
    const response = await fetch(`${route('documents.preview', props.document.id)}?t=${timestamp}`);
    const data = await response.json();
    
    console.log('Preview response:', data);
    
    if (response.ok && data.success) {
      previewType.value = data.type;
      
      if (data.type === 'pdf') {
        currentPreviewUrl.value = data.previewUrl;
      } else if (data.type === 'external') {
        currentPreviewUrl.value = data.googleViewerUrl || data.previewUrl;
        officeViewerUrl.value = data.officeViewerUrl || '';
        activeViewer.value = 'google';
        
        // Cek jika ada warning untuk local environment
        if (data.localEnvironment) {
          localEnvironment.value = true;
          console.warn(data.warning);
        }
      } else {
        currentPreviewUrl.value = data.previewUrl;
      }
      
      currentOriginalUrl.value = data.originalUrl || route('document.direct', props.document.id);
    } else {
      previewError.value = data.error || 'Tidak dapat memuat preview dokumen';
      currentOriginalUrl.value = route('document.direct', props.document.id);
    }
  } catch (error) {
    console.error('Preview error:', error);
    previewError.value = 'Terjadi kesalahan saat memuat preview dokumen. Silakan coba lagi nanti.';
    currentOriginalUrl.value = route('document.direct', props.document.id);
  } finally {
    isLoading.value = false;
  }
};

const switchViewer = (viewer) => {
  activeViewer.value = viewer;
  
  if (viewer === 'google') {
    currentPreviewUrl.value = previewType.value === 'external' ? 
      (currentPreviewUrl.value || route('document.direct', props.document.id)) : 
      currentPreviewUrl.value;
  } else if (viewer === 'office') {
    currentPreviewUrl.value = officeViewerUrl.value || route('document.direct', props.document.id);
  }
};

const confirmDelete = () => {
  showDeleteModal.value = true;
};
</script> 