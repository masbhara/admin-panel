<template>
  <AdminLayout title="Manajemen Dokumen">
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-text-primary">
        Manajemen Dokumen
      </h2>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-background-primary shadow-sm rounded-lg border border-border-light">
          <div class="p-6 bg-background-primary">
            <!-- Filter dan pencarian -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
              <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                  <svg class="w-5 h-5 text-text-tertiary" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                  </svg>
                </div>
                <input 
                  id="search" 
                  v-model="search" 
                  name="search" 
                  type="text" 
                  class="block w-full py-2 pl-10 pr-3 text-sm bg-background-tertiary border-border-light rounded-lg focus:ring-primary-500 focus:border-primary-500" 
                  placeholder="Cari dokumen..." 
                  @keyup.enter="handleSearch"
                />
              </div>

              <Link 
                :href="route('admin.documents.create')" 
                class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors"
              >
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Dokumen Baru
              </Link>
            </div>

            <!-- Tabel dokumen -->
            <div v-if="documents.data.length > 0" class="overflow-x-auto rounded-lg border border-border-light relative">
              <table class="min-w-full divide-y divide-border-light">
                <thead class="bg-background-secondary">
                  <tr>
                    <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-text-secondary">
                      Nama Dokumen
                    </th>
                    <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-text-secondary">
                      Pengirim
                    </th>
                    <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-text-secondary">
                      WhatsApp
                    </th>
                    <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-text-secondary">
                      Asal Kota
                    </th>
                    <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-text-secondary">
                      Status
                    </th>
                    <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-text-secondary">
                      Tanggal Upload
                    </th>
                    <th scope="col" class="relative px-4 py-3 w-10">
                      <span class="sr-only">Aksi</span>
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-background-primary divide-y divide-border-light">
                  <tr v-for="document in documents.data" :key="document.id" class="hover:bg-background-secondary transition-colors">
                    <td class="px-4 py-3">
                      <div class="text-sm font-medium text-text-primary break-words max-w-[200px]">
                        {{ document?.file_name || document?.name || 'Tanpa Nama' }}
                      </div>
                    </td>
                    <td class="px-4 py-3">
                      <div class="text-sm text-text-secondary break-words">
                        {{ getPengirimName(document) }}
                      </div>
                    </td>
                    <td class="px-4 py-3">
                      <div class="text-sm text-text-secondary break-words">
                        {{ document?.metadata?.whatsapp || '-' }}
                      </div>
                    </td>
                    <td class="px-4 py-3">
                      <div class="text-sm text-text-secondary break-words max-w-[150px]">
                        {{ document?.metadata?.city || '-' }}
                      </div>
                    </td>
                    <td class="px-4 py-3">
                      <span 
                        class="inline-flex px-2.5 py-0.5 text-xs font-medium rounded-full"
                        :class="{
                          'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': document?.status === 'pending',
                          'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': document?.status === 'approved',
                          'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': document?.status === 'rejected',
                        }"
                      >
                        {{ statusLabel(document?.status || 'pending') }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-text-secondary whitespace-nowrap">
                      {{ formatDate(document?.uploaded_at || new Date()) }}
                    </td>
                    <td class="px-2 py-3 text-sm font-medium text-right" style="position: relative;">
                      <div class="static" v-click-outside="() => closeMenu(document.id)">
                        <button 
                          @click="toggleActionMenu(document.id, $event)" 
                          class="p-1.5 rounded-md text-text-secondary hover:bg-background-tertiary transition-colors"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                          </svg>
                        </button>
                        
                        <div 
                          v-if="activeActionMenu === document.id"
                          class="fixed z-50 w-56 bg-background-primary rounded-md shadow-lg border border-border-light"
                          :style="{
                            position: 'fixed', 
                            top: menuPosition.y + 'px', 
                            left: menuPosition.x + 'px',
                            boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)'
                          }"
                        >
                          <div class="py-1 divide-y divide-border-light">
                            <Link 
                              :href="route('admin.documents.show', document.id)" 
                              class="flex items-center px-4 py-2 text-sm text-text-primary hover:bg-background-secondary transition-colors"
                            >
                              <svg class="mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                              </svg>
                              <span>Detail</span>
                            </Link>
                            
                            <button
                              @click="openPreviewModal(document)"
                              class="w-full flex items-center px-4 py-2 text-sm text-text-primary hover:bg-background-secondary transition-colors"
                            >
                              <svg class="mr-3 h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                              </svg>
                              <span>Preview</span>
                            </button>
                            
                            <a 
                              :href="route('document.direct', document.id)" 
                              target="_blank" 
                              class="flex items-center px-4 py-2 text-sm text-text-primary hover:bg-background-secondary transition-colors"
                            >
                              <svg class="mr-3 h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                              </svg>
                              <span>Download</span>
                            </a>
                            
                            <Link 
                              :href="route('admin.documents.edit', document.id)" 
                              class="flex items-center px-4 py-2 text-sm text-text-primary hover:bg-background-secondary transition-colors"
                            >
                              <svg class="mr-3 h-5 w-5 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                              </svg>
                              <span>Edit</span>
                            </Link>
                            
                            <button 
                              @click="confirmDelete(document)" 
                              class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-background-secondary transition-colors"
                            >
                              <svg class="mr-3 h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                              </svg>
                              <span>Hapus</span>
                            </button>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else class="flex flex-col items-center justify-center py-12 bg-background-tertiary rounded-lg border border-border-light">
              <svg 
                class="w-16 h-16 text-text-tertiary" 
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
              <h3 class="mt-4 text-lg font-medium text-text-primary">Tidak ada dokumen</h3>
              <p class="mt-1 text-sm text-text-secondary">
                Belum ada dokumen yang diupload.
              </p>
              <Link 
                :href="route('admin.documents.create')" 
                class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors"
              >
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Dokumen Baru
              </Link>
            </div>

            <!-- Pagination -->
            <div v-if="documents.data.length > 0" class="mt-6">
              <Pagination :links="documents.links" />
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
            Preview Dokumen: {{ previewDocument?.file_name || previewDocument?.name }}
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
              <div class="flex space-x-2">
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
            </div>
            <iframe :src="currentPreviewUrl" frameborder="0" class="w-full h-full rounded-lg border border-border-light"></iframe>
          </div>
        </div>
        
        <div class="flex justify-end mt-6 space-x-3">
          <a :href="currentOriginalUrl" target="_blank" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
            Download File Asli
          </a>
          <button @click="showPreviewModal = false" class="px-4 py-2 text-sm font-medium text-text-primary bg-background-tertiary rounded-md shadow-sm hover:bg-background-secondary focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
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
          Apakah Anda yakin ingin menghapus dokumen <strong class="text-text-primary font-medium">"{{ documentToDelete?.file_name || documentToDelete?.name }}"</strong>? 
          Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="flex justify-end mt-6 space-x-3">
          <button @click="showDeleteModal = false" class="px-4 py-2 text-sm font-medium text-text-primary bg-background-tertiary border border-border-light rounded-md shadow-sm hover:bg-background-secondary focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
            Batal
          </button>
          <Link
            :href="route('admin.documents.destroy', documentToDelete?.id)"
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
const activeActionMenu = ref(null);
const menuPosition = ref({ x: 0, y: 0 });

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
  currentPreviewUrl.value = '';
  officeViewerUrl.value = '';
  
  try {
    const timestamp = new Date().getTime();
    const response = await fetch(`${route('documents.preview', document.id)}?t=${timestamp}`);
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
          console.warn(data.warning);
        }
      } else {
        currentPreviewUrl.value = data.previewUrl;
      }
      
      currentOriginalUrl.value = data.originalUrl || route('document.direct', document.id);
    } else {
      previewError.value = data.error || 'Tidak dapat memuat preview dokumen';
      currentOriginalUrl.value = route('document.direct', document.id);
    }
  } catch (error) {
    console.error('Preview error:', error);
    previewError.value = 'Terjadi kesalahan saat memuat preview dokumen. Silakan coba lagi nanti.';
    currentOriginalUrl.value = route('document.direct', document.id);
  } finally {
    isLoading.value = false;
  }
};

const switchViewer = (viewer) => {
  activeViewer.value = viewer;
  
  if (viewer === 'google') {
    currentPreviewUrl.value = previewType.value === 'external' ? 
      (currentPreviewUrl.value || route('document.direct', previewDocument.value.id)) : 
      currentPreviewUrl.value;
  } else if (viewer === 'office') {
    currentPreviewUrl.value = officeViewerUrl.value || route('document.direct', previewDocument.value.id);
  }
};

const confirmDelete = (document) => {
  documentToDelete.value = document;
  showDeleteModal.value = true;
};

const toggleActionMenu = (documentId, event) => {
  if (activeActionMenu.value === documentId) {
    activeActionMenu.value = null;
  } else {
    // Hitung posisi menu berdasarkan event click
    const button = event.currentTarget;
    const rect = button.getBoundingClientRect();
    // Posisikan menu di sebelah kanan bawah tombol
    menuPosition.value = {
      x: rect.right - 170, // 170 adalah lebar menu (w-56 ~= 14rem)
      y: rect.bottom + 5
    };
    activeActionMenu.value = documentId;
  }
};

const closeMenu = (documentId) => {
  if (activeActionMenu.value === documentId) {
    activeActionMenu.value = null;
  }
};

// Direktif kustom untuk click outside
const vClickOutside = {
  mounted(el, binding) {
    el._clickOutside = (event) => {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value(event);
      }
    };
    document.body.addEventListener('click', el._clickOutside);
  },
  unmounted(el) {
    document.body.removeEventListener('click', el._clickOutside);
  }
};

// Tambahkan watch untuk filter pencarian
watch(search, (value, oldValue) => {
  if (value === '') {
    handleSearch();
  }
});

// Fungsi untuk mendapatkan nama pengirim
const getPengirimName = (document) => {
  // Prioritaskan pengirim dari metadata
  if (document?.metadata?.pengirim) {
    return document.metadata.pengirim;
  }
  
  // Gunakan metadata.name jika tersedia
  if (document?.metadata?.name) {
    return document.metadata.name;
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
</script> 