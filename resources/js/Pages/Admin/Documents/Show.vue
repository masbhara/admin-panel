<template>
  <AdminLayout :title="'Detail Dokumen: ' + document.name">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Detail Dokumen
      </h2>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <div class="mb-6 flex items-center text-sm">
          <Link :href="route('admin.document-forms.show', document.document_form_id)" class="text-primary-600 hover:underline">
            {{ document.document_form?.title || 'Form Dokumen' }}
          </Link>
          <svg class="mx-2 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
          </svg>
          <span class="text-gray-500 dark:text-gray-400">Detail Dokumen</span>
        </div>
        
        <!-- Flash Messages -->
        <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-100 border border-green-200 text-green-800 rounded-lg">
          {{ $page.props.flash.success }}
        </div>
        
        <div v-if="$page.props.errors?.error" class="mb-6 p-4 bg-red-100 border border-red-200 text-red-800 rounded-lg">
          {{ $page.props.errors.error }}
        </div>
        
        <!-- Content -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
          <div class="p-6">
            <div class="flex justify-between items-start mb-6">
              <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ document.name || document.file_name }}
              </h1>
              
              <div class="flex space-x-2">
                <a 
                  :href="route('documents.download', document.id)" 
                  target="_blank" 
                  class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
                >
                  <svg class="mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                  </svg>
                  Download
                </a>
                
                <a 
                  :href="`/admin/documents/${document.id}/edit`" 
                  class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-lg font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors"
                >
                  <svg class="mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                  </svg>
                  Edit
                </a>
                
                <button 
                  @click="showDeleteModal = true" 
                  class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                >
                  <svg class="mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                  Hapus
                </button>
              </div>
            </div>
            
            <!-- Document Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Informasi Dasar</h3>
                
                <dl class="grid grid-cols-1 gap-3">
                  <div class="py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID Dokumen</dt>
                    <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ document.id }}</dd>
                  </div>
                  
                  <div class="py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama</dt>
                    <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ document.name }}</dd>
                  </div>
                  
                  <div class="py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Form</dt>
                    <dd class="mt-1 text-gray-900 dark:text-gray-100">
                      <Link :href="route('admin.document-forms.show', document.document_form_id)" class="text-primary-600 hover:underline">
                        {{ document.document_form?.title || 'Form Dokumen' }}
                      </Link>
                    </dd>
                  </div>
                  
                  <div class="py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1">
                      <span 
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border border-transparent"
                        :class="{
                          'bg-yellow-50 text-yellow-800 dark:bg-yellow-900/60 dark:text-yellow-200': document.status === 'pending',
                          'bg-green-50 text-green-700 dark:bg-green-900/60 dark:text-green-200': document.status === 'approved',
                          'bg-red-50 text-red-700 dark:bg-red-900/60 dark:text-red-200': document.status === 'rejected'
                        }"
                      >
                        {{ statusLabel(document.status || 'pending') }}
                      </span>
                    </dd>
                  </div>
                  
                  <div class="py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Upload</dt>
                    <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ formatDate(document.uploaded_at || document.created_at) }}</dd>
                  </div>
                  
                  <div v-if="document.updated_at" class="py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Terakhir Diperbarui</dt>
                    <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ formatDate(document.updated_at) }}</dd>
                  </div>
                </dl>
              </div>
              
              <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Informasi Kontak</h3>
                
                <dl class="grid grid-cols-1 gap-3">
                  <div class="py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">WhatsApp</dt>
                    <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ document.metadata?.whatsapp || '-' }}</dd>
                  </div>
                  
                  <div class="py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Kota</dt>
                    <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ document.metadata?.city || '-' }}</dd>
                  </div>
                </dl>
                
                <!-- Informasi Template Artikel -->
                <div v-if="document.metadata?.template_type === 'article'">
                  <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mt-6 mb-4">Informasi Artikel Media</h3>
                  
                  <dl class="grid grid-cols-1 gap-3">
                    <div class="py-2 border-b border-gray-100 dark:border-gray-700">
                      <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Link Media</dt>
                      <dd class="mt-1 text-gray-900 dark:text-gray-100">
                        <a 
                          :href="document.metadata?.media_link" 
                          target="_blank" 
                          class="text-primary-600 dark:text-primary-400 hover:underline"
                        >
                          {{ document.metadata?.media_link || '-' }}
                        </a>
                      </dd>
                    </div>
                    
                    <div v-if="document.metadata?.screenshot_path" class="py-2 border-b border-gray-100 dark:border-gray-700">
                      <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Screenshot Media</dt>
                      <dd class="mt-2">
                        <button 
                          @click="previewScreenshot"
                          class="inline-flex items-center px-3 py-1.5 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                        >
                          <svg class="mr-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                          </svg>
                          Lihat Screenshot
                        </button>
                      </dd>
                    </div>
                  </dl>
                </div>
                
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mt-6 mb-4">Informasi File</h3>
                
                <dl class="grid grid-cols-1 gap-3">
                  <div class="py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama File</dt>
                    <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ document.file_name || '-' }}</dd>
                  </div>
                  
                  <div class="py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipe File</dt>
                    <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ document.file_type || '-' }}</dd>
                  </div>
                  
                  <div class="py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Ukuran File</dt>
                    <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ formatFileSize(document.file_size) }}</dd>
                  </div>
                </dl>
              </div>
            </div>
            
            <!-- Notes Section -->
            <div class="mt-8">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Catatan</h3>
              <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <p v-if="document.notes" class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ document.notes }}</p>
                <p v-else class="text-gray-500 dark:text-gray-400 italic">Tidak ada catatan</p>
              </div>
            </div>
            
            <!-- Document Preview Section -->
            <div class="mt-8">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Preview Dokumen</h3>
              <div class="flex justify-center">
                <button 
                  @click="openPreviewModal"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                >
                  <svg class="mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                  </svg>
                  Preview Dokumen
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Preview Modal -->
    <modal-dialog :show="showPreviewModal" @close="showPreviewModal = false" max-width="4xl">
      <div class="bg-white dark:bg-gray-800 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Preview Dokumen
          </h3>
          <button @click="showPreviewModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <div v-if="isLoading" class="flex flex-col items-center justify-center py-12">
          <svg class="animate-spin h-10 w-10 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <p class="mt-4 text-gray-600 dark:text-gray-400">Memuat preview dokumen...</p>
        </div>
        
        <div v-else-if="previewError" class="flex flex-col items-center justify-center py-12">
          <svg class="h-16 w-16 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="mt-4 text-red-600 dark:text-red-400">{{ previewError }}</p>
          <a 
            :href="currentOriginalUrl" 
            target="_blank" 
            class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
          >
            Download File
          </a>
        </div>
        
        <div v-else>
          <div v-if="previewType === 'external'" class="mb-4 flex justify-between items-center">
            <div class="flex space-x-2">
              <button 
                @click="switchViewer('google')" 
                class="px-3 py-1 text-sm font-medium rounded transition-colors"
                :class="activeViewer === 'google' ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'"
              >
                Google Viewer
              </button>
              <button 
                v-if="officeViewerUrl"
                @click="switchViewer('office')" 
                class="px-3 py-1 text-sm font-medium rounded transition-colors"
                :class="activeViewer === 'office' ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'"
              >
                Office Viewer
              </button>
            </div>
            <a 
              :href="currentOriginalUrl" 
              target="_blank" 
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
            >
              <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
              Download
            </a>
          </div>
          
          <div class="mt-2 border border-gray-300 dark:border-gray-600 rounded-md h-[70vh] overflow-hidden">
            <iframe 
              :src="currentPreviewUrl" 
              class="w-full h-full"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              frameborder="0" 
              allowfullscreen
            ></iframe>
          </div>
        </div>
      </div>
    </modal-dialog>
    
    <!-- Delete Confirmation Modal -->
    <modal-dialog :show="showDeleteModal" @close="showDeleteModal = false">
      <div class="p-6 bg-white dark:bg-gray-800">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
            Konfirmasi Hapus Dokumen
          </h3>
          <button @click="showDeleteModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
          Apakah Anda yakin ingin menghapus dokumen "{{ document.name || document.file_name }}"? Aksi ini tidak dapat dibatalkan.
        </p>
        <div class="mt-6 flex justify-end gap-3">
          <button
            @click="showDeleteModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors"
          >
            Batal
          </button>
          <button
            @click="deleteDocument"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors"
            :disabled="processingDelete"
          >
            <span v-if="processingDelete">Menghapus...</span>
            <span v-else>Ya, Hapus Dokumen</span>
          </button>
        </div>
      </div>
    </modal-dialog>
    
    <!-- Screenshot Preview Modal -->
    <modal-dialog :show="showScreenshotModal" @close="showScreenshotModal = false" max-width="3xl">
      <div class="bg-white dark:bg-gray-800 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Screenshot Media
          </h3>
          <button @click="showScreenshotModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-600">
          <img :src="screenshotUrl" alt="Screenshot Media" class="w-full h-auto max-h-[70vh] object-contain" />
        </div>
        
        <div class="mt-4 flex justify-end">
          <a 
            :href="screenshotUrl" 
            target="_blank" 
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
          >
            <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Buka di Tab Baru
          </a>
        </div>
      </div>
    </modal-dialog>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ModalDialog from '@/Components/ModalDialog.vue';
import axios from 'axios';

const props = defineProps({
  document: Object,
});

// State for modals
const showPreviewModal = ref(false);
const showDeleteModal = ref(false);
const processingDelete = ref(false);

// State for preview
const isLoading = ref(false);
const previewError = ref(null);
const previewType = ref(null);
const currentPreviewUrl = ref('');
const currentOriginalUrl = ref('');
const officeViewerUrl = ref('');
const activeViewer = ref('google');

// State untuk preview screenshot artikel
const showScreenshotModal = ref(false);
const screenshotUrl = ref('');

// Fungsi format tanggal
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

// Fungsi format ukuran file
const formatFileSize = (bytes) => {
  if (!bytes) return '-';
  if (bytes === 0) return '0 Bytes';
  
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
  
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

// Fungsi label status
const statusLabel = (status) => {
  const labels = {
    'pending': 'Menunggu',
    'approved': 'Disetujui',
    'rejected': 'Ditolak'
  };
  
  return labels[status] || status;
};

// Function to open preview modal
const openPreviewModal = async () => {
  showPreviewModal.value = true;
  isLoading.value = true;
  previewError.value = null;
  
  try {
    const timestamp = new Date().getTime();
    const response = await fetch(`${route('documents.preview', props.document.id)}?t=${timestamp}`);
    const data = await response.json();
    
    if (response.ok && data.success) {
      previewType.value = data.type;
      
      if (data.type === 'pdf') {
        currentPreviewUrl.value = data.previewUrl;
      } else if (data.type === 'external') {
        currentPreviewUrl.value = data.googleViewerUrl || data.previewUrl;
        officeViewerUrl.value = data.officeViewerUrl || '';
        activeViewer.value = 'google';
      } else {
        currentPreviewUrl.value = data.previewUrl;
      }
      
      currentOriginalUrl.value = data.originalUrl || route('documents.download', props.document.id);
    } else {
      previewError.value = data.error || 'Tidak dapat memuat preview dokumen';
      currentOriginalUrl.value = route('documents.download', props.document.id);
    }
  } catch (error) {
    console.error('Preview error:', error);
    previewError.value = 'Terjadi kesalahan saat memuat preview dokumen';
    currentOriginalUrl.value = route('documents.download', props.document.id);
  } finally {
    isLoading.value = false;
  }
};

// Function to switch preview viewer
const switchViewer = (viewer) => {
  activeViewer.value = viewer;
  
  if (viewer === 'google') {
    currentPreviewUrl.value = previewType.value === 'external' ? 
      (currentPreviewUrl.value || route('documents.download', props.document.id)) : 
      currentPreviewUrl.value;
  } else if (viewer === 'office') {
    currentPreviewUrl.value = officeViewerUrl.value || route('documents.download', props.document.id);
  }
};

// Function to delete document
const deleteDocument = async () => {
  processingDelete.value = true;
  
  try {
    const response = await axios.delete(`/admin/documents/${props.document.id}`);
    
    // Redirect back to document form page
    window.location.href = `/admin/document-forms/${props.document.document_form_id}`;
  } catch (error) {
    console.error('Error deleting document:', error);
    processingDelete.value = false;
    showDeleteModal.value = false;
  }
};

// Preview screenshot untuk artikel media
const previewScreenshot = () => {
  if (props.document.metadata?.screenshot_path) {
    // Gunakan URL API langsung daripada mengubah path
    const url = `/screenshots/${props.document.id}`;
    
    // Debug info
    console.log('Screenshot path:', {
      original: props.document.metadata.screenshot_path,
      newUrl: url
    });
    
    screenshotUrl.value = url;
    showScreenshotModal.value = true;
  }
};
</script> 