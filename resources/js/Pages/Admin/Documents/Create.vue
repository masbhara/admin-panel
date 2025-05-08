<template>
  <AdminLayout title="Tambah Dokumen Baru">
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="mb-6">
              <h2 class="text-xl font-semibold text-gray-800">Tambah Dokumen Baru</h2>
              <p class="mt-1 text-sm text-gray-600">
                Unggah dokumen baru ke sistem
              </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
              <!-- Nama Dokumen -->
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Dokumen</label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  required
                />
                <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
              </div>

              <!-- Deskripsi -->
              <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="3"
                  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                ></textarea>
                <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</div>
              </div>

              <!-- File Upload -->
              <div>
                <label for="file" class="block text-sm font-medium text-gray-700">File <span class="text-red-500">*</span></label>
                <div class="mt-1">
                  <input
                    id="file"
                    ref="fileInput"
                    type="file"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                    @change="handleFileUpload"
                    required
                  />
                </div>
                <div v-if="form.errors.file" class="mt-1 text-sm text-red-600">{{ form.errors.file }}</div>
                <p class="mt-1 text-xs text-gray-500">
                  Ukuran maksimum: 10MB. Format yang didukung: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT.
                </p>
              </div>

              <!-- Metadata (Opsional) -->
              <div>
                <label for="metadata" class="block text-sm font-medium text-gray-700">Metadata (Opsional)</label>
                <div class="mt-1 space-y-2">
                  <div v-for="(field, index) in metadataFields" :key="index" class="flex space-x-2">
                    <input
                      v-model="field.key"
                      type="text"
                      placeholder="Kunci"
                      class="block w-1/3 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    />
                    <input
                      v-model="field.value"
                      type="text"
                      placeholder="Nilai"
                      class="block w-2/3 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    />
                    <button
                      type="button"
                      @click="removeMetadataField(index)"
                      class="p-2 text-red-600 hover:text-red-800"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </div>
                  <button
                    type="button"
                    @click="addMetadataField"
                    class="px-4 py-2 text-xs font-medium text-indigo-700 bg-indigo-100 border border-transparent rounded-md hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                  >
                    Tambah Metadata
                  </button>
                </div>
              </div>

              <!-- Tombol Submit -->
              <div class="flex items-center justify-end space-x-3">
                <Link
                  :href="route('admin.documents.index')"
                  class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-gray-800 border border-transparent rounded-md hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                  Kembali
                </Link>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                  {{ form.processing ? 'Mengunggah...' : 'Unggah Dokumen' }}
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
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

// Form untuk mengunggah dokumen
const form = useForm({
  name: '',
  description: '',
  file: null,
  metadata: {},
});

// Metadata fields
const metadataFields = ref([{ key: '', value: '' }]);

// File input reference
const fileInput = ref(null);

// Handling file upload
const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.file = file;
  }
};

// Menambahkan metadata field
const addMetadataField = () => {
  metadataFields.value.push({ key: '', value: '' });
};

// Menghapus metadata field
const removeMetadataField = (index) => {
  metadataFields.value.splice(index, 1);
};

// Submit form
const submit = () => {
  // Validasi file sebelum submit
  if (!form.file) {
    alert('File dokumen wajib diupload');
    return;
  }

  // Mengumpulkan metadata
  const metadata = {};
  metadataFields.value.forEach((field) => {
    if (field.key && field.key.trim() !== '' && field.value !== undefined) {
      metadata[field.key.trim()] = field.value;
    }
  });
  
  form.metadata = Object.keys(metadata).length > 0 ? metadata : null;
  
  form.post(route('admin.documents.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      fileInput.value.value = '';
      metadataFields.value = [{ key: '', value: '' }];
    },
  });
};
</script> 