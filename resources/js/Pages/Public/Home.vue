<template>
  <PublicLayout>
    <Head title="Beranda" />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 min-h-screen">
      <!-- Hero Section -->
      <div class="text-center mb-12">
        <h1 class="text-3xl md:text-4xl font-bold text-primary-600 dark:text-primary-400 mb-4">
          {{ appTitle }}
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
          Silakan pilih salah satu form pengiriman dokumen di bawah ini untuk mulai mengunggah dokumen Anda.
        </p>
      </div>

      <!-- Form Listing Section -->
      <div v-if="documentForms.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="form in documentForms" :key="form.id" class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700 transition-all hover:shadow-lg">
          <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">{{ form.title }}</h2>
            <p v-if="form.description" class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-2">{{ form.description }}</p>
            
            <!-- Status -->
            <div class="mb-4">
              <span v-if="isFormOpen(form)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Terbuka
              </span>
              <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                Ditutup
              </span>
            </div>
            
            <!-- Deadline if exists -->
            <div v-if="form.submission_deadline" class="mb-5 text-sm text-gray-500 dark:text-gray-400">
              <div class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Batas waktu: {{ formatDate(form.submission_deadline) }}</span>
              </div>
            </div>
            
            <Link :href="route('public.document-form', form.slug)" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
              Buka Form
            </Link>
          </div>
        </div>
      </div>
      
      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <div class="max-w-md mx-auto">
          <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
          </svg>
          <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">Belum ada form dokumen yang tersedia</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Silakan cek kembali nanti untuk pengiriman dokumen baru.
          </p>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import axios from 'axios';

const documentForms = ref([]);
const appTitle = ref('Pengiriman Dokumen Online');
const loading = ref(true);

// Fetch active document forms
const fetchDocumentForms = async () => {
  try {
    loading.value = true;
    const response = await axios.get('/api/document-forms/active');
    documentForms.value = response.data;
  } catch (error) {
    console.error('Failed to fetch document forms:', error);
  } finally {
    loading.value = false;
  }
};

// Format date for display
const formatDate = (dateString) => {
  const options = { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  };
  return new Date(dateString).toLocaleDateString('id-ID', options);
};

// Check if form is open for submissions
const isFormOpen = (form) => {
  if (!form.is_active) return false;
  if (!form.submission_deadline) return true;
  
  const deadline = new Date(form.submission_deadline);
  const now = new Date();
  return now < deadline;
};

// Fetch application name
const fetchAppName = async () => {
  try {
    const response = await axios.get('/api/settings/app-name');
    if (response.data && response.data.value) {
      appTitle.value = response.data.value;
    }
  } catch (error) {
    console.error('Failed to fetch app name:', error);
  }
};

onMounted(async () => {
  await Promise.all([fetchDocumentForms(), fetchAppName()]);
});
</script> 