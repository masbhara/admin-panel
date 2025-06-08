<template>
  <AdminLayout title="Buat Form Dokumen">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Buat Form Dokumen
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
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
              <div class="mb-6">
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
                  Buat Form
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
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';

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
  ]
};

const form = useForm({
  title: '',
  description: '',
  slug: '',
  submission_deadline: '',
  closed_message: 'Pengumpulan dokumen telah ditutup.',
  is_active: true,
  captcha_enabled: true,
  template_type: 'default',
  fields: defaultFields.default
});

const getTemplateDescription = (templateType) => {
  const descriptions = {
    default: 'Template standar untuk pengumpulan dokumen (PDF/Word)',
    article: 'Template untuk pengumpulan artikel media dengan screenshot dan tautan'
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
  console.log('Submitting form with data:', form.data());
  form.post(route('admin.document-forms.store'), {
    onSuccess: () => {
      console.log('Form submitted successfully');
      form.reset();
    },
    onError: (errors) => {
      console.error('Form submission failed:', errors);
    }
  });
};
</script> 