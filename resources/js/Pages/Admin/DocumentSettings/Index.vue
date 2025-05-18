<template>
  <AdminLayout title="Pengaturan Dokumen" :user="$page.props.auth?.user">
    <Head title="Pengaturan Dokumen" />
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-text-primary">
        Pengaturan Dokumen
      </h2>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-background-primary shadow-sm rounded-lg border border-border-light">
          <div class="p-6 bg-background-secondary dark:bg-background-tertiary shadow-sm">
            <!-- Flash Messages -->
            <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-100 border border-green-200 text-green-800 dark:bg-green-900/30 dark:border-green-700 dark:text-green-300 rounded-lg">
              {{ $page.props.flash.success }}
            </div>
            <div v-if="successMessage" class="mb-6 p-4 bg-green-100 border border-green-200 text-green-800 dark:bg-green-900/30 dark:border-green-700 dark:text-green-300 rounded-lg">
              {{ successMessage }}
            </div>
            <div v-if="errorMessage" class="mb-6 p-4 bg-red-100 border border-red-200 text-red-800 dark:bg-red-900/30 dark:border-red-700 dark:text-red-300 rounded-lg">
              {{ errorMessage }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">
              <div class="bg-white dark:bg-background-secondary p-6 rounded-lg border border-border-light shadow-sm">
                <h3 class="text-lg font-semibold text-text-primary mb-4 dark:text-white">
                  Pengaturan Waktu Pengumpulan Dokumen
                </h3>

                <!-- Batas Waktu -->
                <div class="mb-4">
                  <Input
                    v-model="form.submission_deadline"
                    type="datetime-local"
                    label="Batas Waktu Pengumpulan"
                    required
                    :error="form.errors.submission_deadline"
                  />
                </div>

                <!-- Pesan Ketika Ditutup -->
                <div class="mb-4">
                  <Textarea
                    v-model="form.closed_message"
                    label="Pesan Ketika Ditutup"
                    required
                    :error="form.errors.closed_message"
                    placeholder="Masukkan pesan yang akan ditampilkan ketika waktu pengumpulan telah berakhir"
                    rows="4"
                  />
                </div>

                <!-- Status Aktif -->
                <div class="mb-4">
                  <Checkbox
                    v-model="form.is_active"
                    label="Aktifkan Pengumpulan Dokumen"
                    :error="form.errors.is_active"
                    description="Jika dinonaktifkan, form pengumpulan akan ditutup terlepas dari batas waktu"
                  />
                </div>

                <!-- Judul Halaman Home -->
                <div class="mb-4">
                  <Input
                    v-model="form.document_home_title"
                    type="text"
                    label="Judul Halaman Home (Public)"
                    required
                    :error="form.errors.document_home_title"
                    placeholder="Masukkan judul halaman Home yang akan tampil di publik"
                  />
                </div>
              </div>

              <!-- Submit Button -->
              <div class="flex justify-end">
                <button
                  type="submit"
                  class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-primary-500 dark:hover:bg-primary-400 dark:focus:ring-primary-400 dark:ring-offset-gray-800 transition-colors"
                  :disabled="isSubmitting"
                >
                  <span v-if="isSubmitting">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Menyimpan...
                  </span>
                  <span v-else>Simpan Pengaturan</span>
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
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Input from '@/Components/Forms/Input.vue';
import Textarea from '@/Components/Forms/Textarea.vue';
import Checkbox from '@/Components/Forms/Checkbox.vue';
import axios from 'axios';
import { ref } from 'vue';

const props = defineProps({
  documentSettings: {
    type: Object,
    required: true
  },
  document_home_title: {
    type: String,
    required: false,
    default: 'Pengiriman Dokumen Online'
  }
});

const form = useForm({
  submission_deadline: props.documentSettings.formatted_submission_deadline || '',
  closed_message: props.documentSettings.closed_message || 'Maaf, waktu pengumpulan dokumen telah berakhir.',
  is_active: props.documentSettings.is_active ?? true,
  document_home_title: props.document_home_title || 'Pengiriman Dokumen Online',
});

const isSubmitting = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const submit = () => {
  isSubmitting.value = true;
  successMessage.value = '';
  errorMessage.value = '';
  
  // Gunakan axios.post dengan method spoofing PUT
  axios.post(route('admin.document-settings.update'), {
    _method: 'PUT',
    submission_deadline: form.submission_deadline,
    closed_message: form.closed_message,
    is_active: form.is_active,
    document_home_title: form.document_home_title
  })
  .then(response => {
    successMessage.value = 'Pengaturan berhasil disimpan';
    // Reset form errors
    form.clearErrors();
  })
  .catch(error => {
    errorMessage.value = 'Terjadi kesalahan saat menyimpan pengaturan';
    
    // Handle validation errors
    if (error.response && error.response.data && error.response.data.errors) {
      const errors = error.response.data.errors;
      Object.keys(errors).forEach(key => {
        form.errors[key] = errors[key][0];
      });
    }
    
    console.error('Error updating document settings:', error);
  })
  .finally(() => {
    isSubmitting.value = false;
  });
};
</script> 