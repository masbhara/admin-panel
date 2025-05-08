<template>
  <AdminLayout :title="`Detail Dokumen - ${document.name}`">
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Detail Dokumen
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Notifikasi flash message -->
        <div v-if="$page.props.flash && $page.props.flash.success" class="mb-6">
          <div class="p-4 text-sm text-green-700 bg-green-100 rounded-md">
            {{ $page.props.flash.success }}
          </div>
        </div>
        
        <div v-if="$page.props.flash && $page.props.flash.error" class="mb-6">
          <div class="p-4 text-sm text-red-700 bg-red-100 rounded-md">
            {{ $page.props.flash.error }}
          </div>
        </div>

        <div class="overflow-hidden bg-white shadow sm:rounded-lg">
          <div class="px-4 py-5 sm:px-6">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900">
                  Informasi Dokumen
                </h3>
                <p class="max-w-2xl mt-1 text-sm text-gray-500">
                  Detail dan informasi dokumen.
                </p>
              </div>
              <div class="flex space-x-3">
                <button @click="showPreviewModal = true" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                  Preview Dokumen
                </button>
                
                <!-- Download super langsung dengan readfile -->
                <a 
                  :href="route('document.direct', document.id)" 
                  target="_blank"
                  class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                  </svg>
                  Download Super
                </a>
                
                <button 
                  @click="confirmDelete" 
                  class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                  Hapus
                </button>
                
                <Link 
                  :href="route('admin.documents.index')" 
                  class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                  Kembali
                </Link>
              </div>
            </div>
          </div>
          <div class="border-t border-gray-200">
            <dl>
              <div class="px-4 py-5 bg-gray-50 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Nama Dokumen
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                  {{ document.name }}
                </dd>
              </div>
              <div class="px-4 py-5 bg-white sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Deskripsi
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                  {{ document.description || 'Tidak ada deskripsi' }}
                </dd>
              </div>
              <div class="px-4 py-5 bg-gray-50 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Diupload Oleh
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                  {{ document.user.name }}
                </dd>
              </div>
              <div class="px-4 py-5 bg-white sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Tipe File
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                  {{ document.file_type }}
                </dd>
              </div>
              <div class="px-4 py-5 bg-gray-50 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Ukuran File
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                  {{ formatFileSize(document.file_size) }}
                </dd>
              </div>
              <div class="px-4 py-5 bg-white sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Status
                </dt>
                <dd class="mt-1 text-sm sm:col-span-2 sm:mt-0">
                  <span 
                    class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full"
                    :class="{
                      'bg-yellow-100 text-yellow-800': document.status === 'pending',
                      'bg-green-100 text-green-800': document.status === 'approved',
                      'bg-red-100 text-red-800': document.status === 'rejected',
                    }"
                  >
                    {{ statusLabel(document.status) }}
                  </span>
                </dd>
              </div>
              <div class="px-4 py-5 bg-gray-50 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Tanggal Upload
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                  {{ formatDate(document.uploaded_at) }}
                </dd>
              </div>
              <div class="px-4 py-5 bg-white sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Terakhir Diupdate
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                  {{ formatDate(document.updated_at) }}
                </dd>
              </div>
            </dl>
          </div>
        </div>

        <!-- Aktivitas Log -->
        <div class="mt-8 overflow-hidden bg-white shadow sm:rounded-lg">
          <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
              Aktivitas Dokumen
            </h3>
            <p class="max-w-2xl mt-1 text-sm text-gray-500">
              Riwayat aktivitas untuk dokumen ini.
            </p>
          </div>
          <div class="border-t border-gray-200">
            <div class="flow-root px-4 py-5 sm:px-6">
              <ul role="list" class="-mb-8">
                <li v-for="(activity, index) in activities" :key="activity.id">
                  <div class="relative pb-8">
                    <span v-if="index !== activities.length - 1" class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                    <div class="relative flex items-start space-x-3">
                      <div class="relative">
                        <div class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-full">
                          <svg v-if="activity.description === 'deleted document'" class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                          <svg v-else-if="activity.description === 'downloaded document'" class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                          </svg>
                          <svg v-else class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                          </svg>
                        </div>
                      </div>
                      <div class="min-w-0 flex-1">
                        <div>
                          <div class="text-sm">
                            <a href="#" class="font-medium text-gray-900">{{ activity.causer ? activity.causer.name : 'Sistem' }}</a>
                          </div>
                          <p class="mt-0.5 text-sm text-gray-500">
                            {{ formatDateTime(activity.created_at) }}
                          </p>
                        </div>
                        <div class="mt-2 text-sm text-gray-700">
                          <p>
                            {{ formatActivityDescription(activity) }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li v-if="activities.length === 0">
                  <div class="py-4 text-center text-gray-500">
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
      <div class="p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium leading-6 text-gray-900">
            Preview Dokumen: {{ document.name }}
          </h3>
          <button @click="showPreviewModal = false" class="text-gray-400 hover:text-gray-500">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <div v-if="isLoading" class="flex items-center justify-center h-96">
          <div class="flex flex-col items-center">
            <svg class="w-10 h-10 text-indigo-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-2 text-sm text-gray-500">Memuat preview...</p>
          </div>
        </div>
        
        <div v-else-if="previewError" class="flex flex-col items-center justify-center h-96">
          <svg class="w-12 h-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="mt-2 text-sm text-red-500">{{ previewError }}</p>
          <Link :href="currentOriginalUrl" class="px-4 py-2 mt-4 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Download File Asli
          </Link>
        </div>
        
        <div v-else-if="previewType === 'pdf'" class="h-96">
          <iframe :src="currentPreviewUrl" frameborder="0" width="100%" height="100%"></iframe>
        </div>
        
        <div v-else-if="previewType === 'external'" class="h-96">
          <div class="flex flex-col h-full">
            <div class="flex justify-between mb-2">
              <div class="flex space-x-2">
                <button 
                  @click="switchViewer('google')" 
                  class="px-3 py-1 text-xs text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200"
                  :class="{ 'bg-indigo-100 text-indigo-700': activeViewer === 'google' }"
                >
                  Google Docs Viewer
                </button>
                <button 
                  @click="switchViewer('office')" 
                  class="px-3 py-1 text-xs text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200"
                  :class="{ 'bg-indigo-100 text-indigo-700': activeViewer === 'office' }"
                >
                  Microsoft Office Viewer
                </button>
              </div>
            </div>
            <iframe :src="currentPreviewUrl" frameborder="0" width="100%" height="100%"></iframe>
          </div>
        </div>
        
        <div class="flex justify-end mt-4 space-x-3">
          <Link :href="currentOriginalUrl" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Download File Asli
          </Link>
          <button @click="showPreviewModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Tutup
          </button>
        </div>
      </div>
    </modal-dialog>

    <!-- Konfirmasi Hapus Modal -->
    <modal-dialog :show="showDeleteModal" @close="showDeleteModal = false">
      <div class="p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium leading-6 text-gray-900">
            Konfirmasi Hapus
          </h3>
          <button @click="showDeleteModal = false" class="text-gray-400 hover:text-gray-500">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <p class="text-sm text-gray-500">
          Apakah Anda yakin ingin menghapus dokumen <strong>"{{ document.name }}"</strong>? 
          Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="flex justify-end mt-6 space-x-3">
          <button @click="showDeleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Batal
          </button>
          <Link
            :href="route('admin.documents.destroy', document.id)"
            method="delete"
            as="button"
            type="button"
            class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
            @click.prevent="confirmDelete"
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

const openPreviewModal = async () => {
  showPreviewModal.value = true;
  isLoading.value = true;
  previewError.value = null;
  previewType.value = null;
  
  try {
    const response = await fetch(route('documents.preview', props.document.id));
    const data = await response.json();
    
    if (response.ok) {
      previewType.value = data.type;
      currentPreviewUrl.value = data.previewUrl;
      currentOriginalUrl.value = data.originalUrl;
      
      if (data.type === 'external') {
        officeViewerUrl.value = data.officeViewerUrl;
        activeViewer.value = 'google';
      }
    } else {
      previewError.value = data.error || 'Tidak dapat memuat preview';
      currentOriginalUrl.value = route('documents.download', props.document.id);
    }
  } catch (error) {
    previewError.value = 'Terjadi kesalahan saat memuat preview';
    currentOriginalUrl.value = route('documents.download', props.document.id);
  } finally {
    isLoading.value = false;
  }
};

const switchViewer = (viewer) => {
  activeViewer.value = viewer;
  currentPreviewUrl.value = viewer === 'google' ? 
    currentPreviewUrl.value : officeViewerUrl.value;
};

const confirmDelete = () => {
  showDeleteModal.value = true;
};
</script> 