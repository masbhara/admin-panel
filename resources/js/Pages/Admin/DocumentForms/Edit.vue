<template>
  <AdminLayout title="Edit Form Dokumen">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Edit Form Dokumen
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <!-- Alert Success -->
            <div v-if="$page.props.flash && $page.props.flash.success" class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-md p-4 mb-6">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-green-500 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-green-800 dark:text-green-300">
                    {{ $page.props.flash.success }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Alert Error -->
            <div v-if="$page.props.flash && $page.props.flash.error" class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-md p-4 mb-6">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-red-500 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-red-800 dark:text-red-300">
                    {{ $page.props.flash.error }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Error Message Display -->
            <div v-if="errorMessage" class="mb-6 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded relative">
              <span class="block sm:inline">{{ errorMessage }}</span>
            </div>

            <form @submit.prevent="submit">
              <!-- Template Selection -->
              <div class="mb-6">
                <InputLabel for="template_type" value="Pilih Template Form" />
                <select
                  id="template_type"
                  v-model="form.template_type"
                  class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white sm:text-sm rounded-md"
                  @change="handleTemplateChange"
                >
                  <option value="default">Form Dokumen Default</option>
                  <option value="article">Form Artikel Media</option>
                  <option value="multiple_article">Form Artikel Media (2 Screenshot)</option>
                </select>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                  {{ getTemplateDescription(form.template_type) }}
                </p>
                <InputError :message="form.errors.template_type" class="mt-2" />
              </div>

              <!-- Title -->
              <div class="mb-4">
                <InputLabel for="title" value="Judul Form" />
                <TextInput
                  id="title"
                  type="text"
                  class="mt-1 block w-full"
                  v-model="form.title"
                  required
                  autofocus
                />
                <InputError :message="form.errors.title" class="mt-2" />
              </div>

              <!-- Description -->
              <div class="mb-4">
                <InputLabel for="description" value="Deskripsi (opsional)" />
                <TextArea
                  id="description"
                  class="mt-1 block w-full"
                  v-model="form.description"
                  rows="3"
                />
                <InputError :message="form.errors.description" class="mt-2" />
              </div>

              <!-- Slug -->
              <div class="mb-4">
                <InputLabel for="slug" value="Slug URL (opsional)" />
                <TextInput
                  id="slug"
                  type="text"
                  class="mt-1 block w-full"
                  v-model="form.slug"
                  placeholder="akan-dibuat-otomatis-dari-judul"
                />
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                  Slug hanya boleh berisi huruf kecil, angka, dan tanda hubung. Jika dibiarkan kosong, akan dibuat otomatis dari judul.
                </p>
                <InputError :message="form.errors.slug" class="mt-2" />
              </div>

              <!-- Submission Deadline -->
              <div class="mb-4">
                <InputLabel for="submission_deadline" value="Batas Waktu Pengumpulan (opsional)" />
                <TextInput
                  id="submission_deadline"
                  type="datetime-local"
                  class="mt-1 block w-full"
                  v-model="form.submission_deadline"
                />
                <InputError :message="form.errors.submission_deadline" class="mt-2" />
              </div>

              <!-- Closed Message -->
              <div class="mb-4">
                <InputLabel for="closed_message" value="Pesan Ketika Form Ditutup (opsional)" />
                <TextArea
                  id="closed_message"
                  class="mt-1 block w-full"
                  v-model="form.closed_message"
                  rows="2"
                  placeholder="Pengumpulan dokumen telah ditutup."
                />
                <InputError :message="form.errors.closed_message" class="mt-2" />
              </div>

              <!-- Active Status -->
              <div class="mb-4">
                <div class="flex items-center">
                  <Checkbox id="is_active" v-model="form.is_active" />
                  <InputLabel for="is_active" class="ml-2" value="Aktifkan Form" />
                </div>
                <InputError :message="form.errors.is_active" class="mt-2" />
              </div>

              <!-- Captcha Status -->
              <div class="mb-6">
                <div class="flex items-center">
                  <Checkbox id="captcha_enabled" v-model="form.captcha_enabled" />
                  <InputLabel for="captcha_enabled" class="ml-2" value="Aktifkan Captcha" />
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                  Aktifkan verifikasi captcha untuk mencegah spam dan bot.
                </p>
                <InputError :message="form.errors.captcha_enabled" class="mt-2" />
              </div>

              <!-- Preview Fields -->
              <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Preview Fields</h3>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                  <div v-for="field in previewFields" :key="field.name" class="mb-4 last:mb-0">
                    <div class="flex items-center justify-between">
                      <div>
                        <span class="font-medium">{{ field.label }}</span>
                        <span v-if="field.is_required" class="text-red-500 ml-1">*</span>
                      </div>
                      <span class="text-sm text-gray-500">{{ getFieldTypeLabel(field.type) }}</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">{{ field.help_text }}</p>
                  </div>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="flex items-center justify-end mt-4">
                <Link
                  :href="route('admin.document-forms.index')"
                  class="mr-4 px-4 py-2 bg-gray-300 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-600 focus:bg-gray-400 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                >
                  Batal
                </Link>
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                  Simpan Perubahan
                </PrimaryButton>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  documentForm: {
    type: Object,
    required: true
  }
});

// State management
const processing = ref(false);
const errorMessage = ref('');

const defaultFields = {
  default: [
    {
      label: 'Nama Lengkap',
      name: 'name',
      type: 'text',
      is_required: true,
      is_enabled: true,
      help_text: 'Masukkan nama lengkap Anda',
      order: 0
    },
    {
      label: 'Nomor WhatsApp',
      name: 'whatsapp',
      type: 'text',
      is_required: true,
      is_enabled: true,
      help_text: '08xxx (gunakan nomor aktif)',
      order: 1
    },
    {
      label: 'Kota/Kabupaten',
      name: 'city',
      type: 'text',
      is_required: true,
      is_enabled: true,
      help_text: 'Ketik untuk mencari kota/kabupaten...',
      order: 2
    },
    {
      label: 'Unggah Dokumen',
      name: 'document',
      type: 'file',
      is_required: true,
      is_enabled: true,
      help_text: 'Format: PDF, Word, maksimal 10MB',
      validation_rules: {
        mimes: 'pdf,doc,docx',
        max: 10240
      },
      order: 3
    }
  ],
  article: [
    {
      label: 'Nama Lengkap',
      name: 'name',
      type: 'text',
      is_required: true,
      is_enabled: true,
      help_text: 'Masukkan nama lengkap Anda',
      order: 0
    },
    {
      label: 'Nomor WhatsApp',
      name: 'whatsapp',
      type: 'text',
      is_required: true,
      is_enabled: true,
      help_text: '08xxx (gunakan nomor aktif)',
      order: 1
    },
    {
      label: 'Kota/Kabupaten',
      name: 'city',
      type: 'text',
      is_required: true,
      is_enabled: true,
      help_text: 'Ketik untuk mencari kota/kabupaten...',
      order: 2
    },
    {
      label: 'Unggah Dokumen',
      name: 'document',
      type: 'file',
      is_required: true,
      is_enabled: true,
      help_text: 'Format: PDF, Word, maksimal 10MB',
      validation_rules: {
        mimes: 'pdf,doc,docx',
        max: 10240
      },
      order: 3
    },
    {
      label: 'Tautan / Link Media',
      name: 'media_link',
      type: 'url',
      is_required: true,
      is_enabled: true,
      help_text: 'Masukkan link artikel yang sudah dipublikasi',
      order: 4
    },
    {
      label: 'Unggah Screenshot',
      name: 'screenshot',
      type: 'file',
      is_required: true,
      is_enabled: true,
      help_text: 'Format: JPG, PNG, maksimal 5MB',
      validation_rules: {
        mimes: 'jpg,jpeg,png',
        max: 5120
      },
      order: 5
    }
  ],
  multiple_article: [
    {
      label: 'Nama Lengkap',
      name: 'name',
      type: 'text',
      is_required: true,
      is_enabled: true,
      help_text: 'Masukkan nama lengkap Anda',
      order: 0
    },
    {
      label: 'Nomor WhatsApp',
      name: 'whatsapp',
      type: 'text',
      is_required: true,
      is_enabled: true,
      help_text: '08xxx (gunakan nomor aktif)',
      order: 1
    },
    {
      label: 'Kota/Kabupaten',
      name: 'city',
      type: 'text',
      is_required: true,
      is_enabled: true,
      help_text: 'Ketik untuk mencari kota/kabupaten...',
      order: 2
    },
    {
      label: 'Unggah Dokumen',
      name: 'document',
      type: 'file',
      is_required: true,
      is_enabled: true,
      help_text: 'Format: PDF, Word, maksimal 10MB',
      validation_rules: {
        mimes: 'pdf,doc,docx',
        max: 10240
      },
      order: 3
    },
    {
      label: 'Tautan / Link Media',
      name: 'media_link',
      type: 'text',
      is_required: true,
      is_enabled: true,
      help_text: 'Masukkan link artikel yang sudah dipublikasi',
      order: 4
    },
    {
      label: 'Unggah Screenshot SS Plagiat',
      name: 'screenshot',
      type: 'file',
      is_required: true,
      is_enabled: true,
      help_text: 'Format: JPG, PNG, maksimal 5MB',
      validation_rules: {
        mimes: 'jpg,jpeg,png',
        max: 5120
      },
      order: 5
    },
    {
      label: 'Unggah Screenshot Kirim ke Media',
      name: 'screenshot_media',
      type: 'file',
      is_required: true,
      is_enabled: true,
      help_text: 'Format: JPG, PNG, maksimal 5MB',
      validation_rules: {
        mimes: 'jpg,jpeg,png',
        max: 5120
      },
      order: 6
    }
  ]
};

const form = useForm({
  title: props.documentForm.title,
  description: props.documentForm.description,
  slug: props.documentForm.slug,
  submission_deadline: props.documentForm.formatted_submission_deadline,
  closed_message: props.documentForm.closed_message,
  is_active: props.documentForm.is_active,
  captcha_enabled: props.documentForm.captcha_enabled ?? true,
  template_type: props.documentForm.template_type || 'default',
  fields: props.documentForm.fields || defaultFields.default
});

const getTemplateDescription = (templateType) => {
  const descriptions = {
    default: 'Template standar untuk pengumpulan dokumen (PDF/Word)',
    article: 'Template untuk pengumpulan artikel media dengan screenshot dan tautan',
    multiple_article: 'Template artikel media dengan 2 screenshot: SS Plagiat dan Kirim ke Media'
  };
  return descriptions[templateType] || '';
};

const getFieldTypeLabel = (type) => {
  const labels = {
    text: 'Teks',
    textarea: 'Teks Panjang',
    select: 'Pilihan',
    radio: 'Pilihan Radio',
    checkbox: 'Kotak Centang',
    file: 'Unggah File',
    date: 'Tanggal',
    number: 'Angka',
    url: 'Tautan URL'
  };
  return labels[type] || type;
};

const previewFields = computed(() => {
  return defaultFields[form.template_type] || [];
});

const handleTemplateChange = () => {
  form.fields = defaultFields[form.template_type];
  console.log('Template changed to:', form.template_type);
  console.log('Fields updated:', form.fields);
};

const submit = () => {
  processing.value = true;
  errorMessage.value = '';
  
  const formData = {
    _method: 'PUT', // Method spoofing
    title: form.title,
    description: form.description,
    slug: form.slug,
    submission_deadline: form.submission_deadline,
    closed_message: form.closed_message,
    is_active: form.is_active,
    captcha_enabled: form.captcha_enabled,
    template_type: form.template_type,
    fields: form.fields
  };

  // Dapatkan CSRF token dari meta tag
  const token = document.head.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  // Gunakan axios.post dengan CSRF token
  axios.post(route('admin.document-forms.update', props.documentForm.id), formData, {
    headers: {
      'X-CSRF-TOKEN': token,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    }
  })
  .then(response => {
    console.log('Form submitted successfully:', response);
    router.visit(route('admin.document-forms.index'));
  })
  .catch(error => {
    console.error('Form submission failed:', error.response);
    errorMessage.value = error.response?.data?.message || 'Terjadi kesalahan saat menyimpan form.';
  })
  .finally(() => {
    processing.value = false;
  });
};
</script> 