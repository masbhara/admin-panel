<template>
  <AdminLayout title="Manajemen Dokumen">
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Manajemen Dokumen
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Filter dan pencarian -->
            <div class="pb-4 mb-4 border-b border-gray-200">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <label for="search" class="sr-only">Cari</label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                      <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                      </svg>
                    </div>
                    <input 
                      id="search" 
                      v-model="search" 
                      name="search" 
                      type="text" 
                      class="block w-full py-2 pl-10 pr-3 text-sm border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500" 
                      placeholder="Cari dokumen..." 
                      @keyup.enter="handleSearch"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Tabel dokumen -->
            <div v-if="documents.data.length > 0" class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                      Nama Dokumen
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                      Diupload Oleh
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                      Tipe
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                      Ukuran
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                      Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                      Tanggal Upload
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                      <span class="sr-only">Aksi</span>
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="document in documents.data" :key="document.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ document?.name || 'Tanpa Nama' }}
                      </div>
                      <div class="text-sm text-gray-500" v-if="document?.description">
                        {{ truncate(document?.description, 50) }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">
                        {{ document?.user?.name || 'Tidak Ada User' }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">
                        {{ document?.file_type || 'Tidak Ada' }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">
                        {{ formatFileSize(document?.file_size || 0) }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span 
                        class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full"
                        :class="{
                          'bg-yellow-100 text-yellow-800': document?.status === 'pending',
                          'bg-green-100 text-green-800': document?.status === 'approved',
                          'bg-red-100 text-red-800': document?.status === 'rejected',
                        }"
                      >
                        {{ statusLabel(document?.status || 'pending') }}
                      </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                      {{ formatDate(document?.uploaded_at || new Date()) }}
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                      <div class="flex items-center space-x-2">
                        <Link :href="route('admin.documents.show', document.id)" class="text-indigo-600 hover:text-indigo-900">
                          Detail
                        </Link>
                        <button @click="openPreviewModal(document)" class="text-blue-600 hover:text-blue-900">
                          Preview
                        </button>
                        <Link :href="route('documents.download', document.id)" target="_blank" class="text-green-600 hover:text-green-900">
                          Download
                        </Link>
                        <button @click="confirmDelete(document)" class="text-red-600 hover:text-red-900">
                          Hapus
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else class="py-10 text-center">
              <svg 
                class="w-12 h-12 mx-auto text-gray-400" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24" 
                xmlns="http://www.w3.org/2000/svg"
              >
                <path 
                  stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                ></path>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada dokumen</h3>
              <p class="mt-1 text-sm text-gray-500">
                Belum ada dokumen yang diupload.
              </p>
            </div>

            <!-- Pagination -->
            <div v-if="documents.data.length > 0" class="mt-4">
              <Pagination :links="documents.links" />
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
            Preview Dokumen: {{ previewDocument?.name }}
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
          Apakah Anda yakin ingin menghapus dokumen <strong>"{{ documentToDelete?.name }}"</strong>? 
          Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="flex justify-end mt-6 space-x-3">
          <button @click="showDeleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Batal
          </button>
          <Link
            :href="route('admin.documents.destroy', documentToDelete?.id)"
            method="delete"
            as="button"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
            @click="showDeleteModal = false"
            :preserve-scroll="true"
            :only="['documents']"
            @finish="() => { showDeleteModal = false }"
          >
            Hapus
          </Link>
        </div>
      </div>
    </modal-dialog>
  </AdminLayout>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import ModalDialog from '@/Components/ModalDialog.vue';

const props = defineProps({
  documents: Object,
  filters: Object,
});

const search = ref(props.filters.search || '');
const showPreviewModal = ref(false);
const showDeleteModal = ref(false);
const previewDocument = ref(null);
const documentToDelete = ref(null);
const isLoading = ref(false);
const previewError = ref(null);
const previewType = ref(null);
const currentPreviewUrl = ref('');
const currentOriginalUrl = ref('');
const officeViewerUrl = ref('');
const activeViewer = ref('google');

const handleSearch = () => {
  router.get(route('admin.documents.index'), { search: search.value }, {
    preserveState: true,
    replace: true,
  });
};

const truncate = (text, length) => {
  if (text && text.length > length) {
    return text.substring(0, length) + '...';
  }
  return text;
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    year: 'numeric',
    month: 'short',
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

const openPreviewModal = async (document) => {
  previewDocument.value = document;
  showPreviewModal.value = true;
  isLoading.value = true;
  previewError.value = null;
  previewType.value = null;
  
  try {
    const response = await fetch(route('documents.preview', document.id));
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
      currentOriginalUrl.value = route('documents.download', document.id);
    }
  } catch (error) {
    previewError.value = 'Terjadi kesalahan saat memuat preview';
    currentOriginalUrl.value = route('documents.download', document.id);
  } finally {
    isLoading.value = false;
  }
};

const switchViewer = (viewer) => {
  activeViewer.value = viewer;
  currentPreviewUrl.value = viewer === 'google' ? 
    currentPreviewUrl.value : officeViewerUrl.value;
};

const confirmDelete = (document) => {
  documentToDelete.value = document;
  showDeleteModal.value = true;
};

// Tambahkan watch untuk filter pencarian
watch(search, (value, oldValue) => {
  if (value === '') {
    handleSearch();
  }
});
</script> 