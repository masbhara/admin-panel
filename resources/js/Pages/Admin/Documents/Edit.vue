<template>
  <AdminLayout :title="'Edit Dokumen: ' + document.name">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Edit Dokumen
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
          <Link :href="`/admin/documents/${document.id}`" class="text-primary-600 hover:underline">
            Detail Dokumen
          </Link>
          <svg class="mx-2 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
          </svg>
          <span class="text-gray-500 dark:text-gray-400">Edit</span>
        </div>
        
        <!-- Flash Messages -->
        <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-100 border border-green-200 text-green-800 rounded-lg">
          {{ $page.props.flash.success }}
        </div>
        
        <div v-if="Object.keys($page.props.errors || {}).length > 0" class="mb-6 p-4 bg-red-100 border border-red-200 text-red-800 rounded-lg">
          <ul class="list-disc ml-4">
            <li v-for="(error, key) in $page.props.errors" :key="key">
              {{ error }}
            </li>
          </ul>
        </div>
        
        <!-- Form Section -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
          <div class="p-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
              Edit Dokumen
            </h1>
            
            <form @submit.prevent="submitForm" enctype="multipart/form-data">
              <!-- Informasi Dasar -->
              <div class="mb-10">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                  Informasi Dasar
                </h3>
                
                <!-- Nama -->
                <div class="mb-4">
                  <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Nama <span class="text-red-500">*</span>
                  </label>
                  <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                    required
                  />
                </div>
                
                <!-- Status -->
                <div class="mb-4">
                  <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Status
                  </label>
                  <select
                    id="status"
                    v-model="form.status"
                    class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                  >
                    <option value="pending">Menunggu</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                  </select>
                </div>
              </div>
              
              <!-- Informasi Kontak -->
              <div class="mb-10">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                  Informasi Kontak
                </h3>
                
                <!-- WhatsApp -->
                <div class="mb-4">
                  <label for="whatsapp" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    WhatsApp
                  </label>
                  <input
                    id="whatsapp"
                    v-model="form.whatsapp"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                  />
                </div>
                
                <!-- Kota -->
                <div class="mb-4">
                  <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Kota
                  </label>
                  <input
                    id="city"
                    v-model="form.city"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                  />
                </div>
              </div>
              
              <!-- Upload File -->
              <div class="mb-10">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                  File Dokumen
                </h3>
                
                <!-- Nama File Saat Ini -->
                <div class="mb-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-700">
                  <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    File saat ini:
                  </p>
                  <div class="flex items-center gap-3">
                    <div>
                      <p class="font-medium text-gray-900 dark:text-gray-100">
                        {{ document.file_name || 'Tidak ada file' }}
                      </p>
                      <p v-if="document.file_size" class="text-xs text-gray-500">
                        {{ formatFileSize(document.file_size) }}
                      </p>
                    </div>
                    <a 
                      v-if="document.file_name"
                      :href="route('documents.download', document.id)" 
                      target="_blank" 
                      class="ml-auto inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-primary-600 border border-transparent rounded hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
                    >
                      <svg class="mr-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                      </svg>
                      Download
                    </a>
                  </div>
                </div>
                
                <!-- Upload File Baru -->
                <div class="mb-4">
                  <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Upload File Baru (opsional)
                  </label>
                  <input
                    id="file"
                    type="file"
                    @change="handleFileChange"
                    class="mt-1 block w-full px-3 py-2 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                  />
                  <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Ukuran maksimum: 10MB
                  </p>
                </div>
              </div>
              
              <!-- Catatan -->
              <div class="mb-10">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                  Catatan
                </h3>
                
                <div class="mb-4">
                  <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Catatan (opsional)
                  </label>
                  <textarea
                    id="notes"
                    v-model="form.notes"
                    rows="4"
                    class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                  ></textarea>
                </div>
              </div>
              
              <!-- Form Actions -->
              <div class="flex justify-end mt-8 space-x-3">
                <Link 
                  :href="`/admin/documents/${document.id}`" 
                  class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
                >
                  Batal
                </Link>
                <button 
                  type="submit" 
                  class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
                  :disabled="processing"
                >
                  <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import axios from 'axios';

const props = defineProps({
  document: Object,
});

// Form state
const form = ref({
  name: props.document.name || '',
  whatsapp: props.document.metadata?.whatsapp || '',
  city: props.document.metadata?.city || '',
  notes: props.document.notes || '',
  status: props.document.status || 'pending',
  file: null,
});

const processing = ref(false);
const fileInput = ref(null);

// Fungsi format ukuran file
const formatFileSize = (bytes) => {
  if (!bytes) return '-';
  if (bytes === 0) return '0 Bytes';
  
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
  
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

// Handler for file input change
const handleFileChange = (e) => {
  if (e.target.files.length > 0) {
    form.value.file = e.target.files[0];
  } else {
    form.value.file = null;
  }
};

// Submit form function
const submitForm = async () => {
  processing.value = true;
  
  try {
    // Create form data
    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('whatsapp', form.value.whatsapp);
    formData.append('city', form.value.city);
    formData.append('notes', form.value.notes);
    formData.append('status', form.value.status);
    
    // Add file if available
    if (form.value.file) {
      formData.append('file', form.value.file);
    }
    
    // Add method spoofing for PUT request
    formData.append('_method', 'PUT');
    
    // Send request
    const response = await axios.post(`/admin/documents/${props.document.id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    
    // Redirect to document details
    window.location.href = `/admin/documents/${props.document.id}`;
  } catch (error) {
    console.error('Error updating document:', error);
    processing.value = false;
    
    // Show error message if validation errors
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors;
      Object.keys(errors).forEach(key => {
        alert(`Error: ${errors[key].join(', ')}`);
      });
    } else {
      alert('Terjadi kesalahan saat memperbarui dokumen. Silakan coba lagi.');
    }
  }
};
</script> 