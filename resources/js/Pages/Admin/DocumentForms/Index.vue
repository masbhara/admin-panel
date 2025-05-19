<template>
  <AdminLayout title="Form Dokumen">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Form Dokumen
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Alert Success -->
        <div v-if="$page.props.flash?.success" class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-green-500 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-green-800 dark:text-green-300">
                {{ $page.props.flash?.success }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Daftar Form Dokumen
              </h3>
              <Link :href="route('admin.document-forms.create')" class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700 transition">
                Buat Form Baru
              </Link>
            </div>

            <!-- Search & Filter -->
            <div class="mb-6">
              <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-grow">
                  <input
                    type="text"
                    v-model="searchQuery"
                    @input="debouncedSearch"
                    placeholder="Cari berdasarkan judul form..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                  />
                </div>
                <div class="flex-shrink-0">
                  <select
                    v-model="statusFilter"
                    @change="filterByStatus"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                  >
                    <option value="all">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Tidak Aktif</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
              <table v-if="documentForms.data.length > 0" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Judul Form
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Slug
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Batas Waktu
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Dokumen
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Aksi
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                  <tr v-for="form in documentForms.data" :key="form.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ form.title }}
                      </div>
                      <div v-if="form.user" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Dibuat oleh: {{ form.user.name }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ form.slug }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div v-if="form.submission_deadline" class="text-sm text-gray-500 dark:text-gray-400">
                        {{ new Date(form.submission_deadline).toLocaleString() }}
                      </div>
                      <div v-else class="text-sm text-gray-500 dark:text-gray-400">
                        Tidak ada batas
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                        :class="{
                          'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': form.is_active,
                          'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': !form.is_active
                        }">
                        {{ form.is_active ? 'Aktif' : 'Tidak Aktif' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ form.document_count || 0 }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div class="flex justify-end space-x-2">
                        <button @click="showPublicUrl(form)" class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                          </svg>
                        </button>
                        <Link :href="route('admin.document-forms.show', form.id)" class="text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-400">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                          </svg>
                        </Link>
                        <Link :href="route('admin.document-forms.edit', form.id)" class="text-yellow-600 hover:text-yellow-900 dark:hover:text-yellow-400">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                          </svg>
                        </Link>
                        <button @click="confirmDelete(form)" class="text-red-600 hover:text-red-900 dark:hover:text-red-400">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="mt-2">Tidak ada form dokumen yang ditemukan.</p>
                <Link :href="route('admin.document-forms.create')" class="inline-flex items-center justify-center mt-4 px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none">
                  Buat Form Dokumen
                </Link>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="documentForms.data.length > 0" class="mt-6">
              <Pagination :links="documentForms.links" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Modal :show="deleteModal" @close="closeDeleteModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
          Konfirmasi Hapus Form
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          Apakah Anda yakin ingin menghapus form dokumen "{{ formToDelete?.title }}"? Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="mt-6 flex justify-end space-x-4">
          <SecondaryButton @click="closeDeleteModal">Batal</SecondaryButton>
          <DangerButton @click="deleteForm" :class="{ 'opacity-25': processing }" :disabled="processing">
            {{ processing ? 'Menghapus...' : 'Hapus Form' }}
          </DangerButton>
        </div>
      </div>
    </Modal>

    <!-- Public URL Modal -->
    <Modal :show="publicUrlModal" @close="closePublicUrlModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
          URL Publik Form
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          URL publik untuk form "{{ selectedForm?.title }}":
        </p>
        <div class="mt-2 flex items-center">
          <input 
            ref="publicUrlInput"
            type="text" 
            :value="publicUrl" 
            readonly 
            class="flex-1 p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100"
          />
          <button 
            @click="copyToClipboard" 
            class="ml-2 p-2 bg-primary-600 text-white rounded hover:bg-primary-700 transition"
            :class="{ 'bg-green-600 hover:bg-green-700': copied }"
          >
            <svg v-if="!copied" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </button>
        </div>
        <div class="mt-6 flex justify-end">
          <SecondaryButton @click="closePublicUrlModal">Tutup</SecondaryButton>
        </div>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { ref, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import axios from 'axios';

const props = defineProps({
  documentForms: Object,
  filters: Object,
});

// Form search and filter
const searchQuery = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || 'all');

// Delete functionality
const deleteModal = ref(false);
const formToDelete = ref(null);
const processing = ref(false);

// Public URL functionality
const publicUrlModal = ref(false);
const selectedForm = ref(null);
const publicUrl = ref('');
const copied = ref(false);
const publicUrlInput = ref(null);

// Custom debounce function implementation
let timeout;
const debouncedSearch = () => {
  if (timeout) clearTimeout(timeout);
  timeout = setTimeout(() => {
    router.get(route('admin.document-forms.index'), { 
      search: searchQuery.value, 
      status: statusFilter.value 
    }, {
      preserveState: true,
      replace: true,
    });
  }, 300);
};

onUnmounted(() => {
  if (timeout) clearTimeout(timeout);
});

// Filter by status
const filterByStatus = () => {
  router.get(route('admin.document-forms.index'), { 
    search: searchQuery.value, 
    status: statusFilter.value 
  }, {
    preserveState: true,
    replace: true,
  });
};

const confirmDelete = (form) => {
  formToDelete.value = form;
  deleteModal.value = true;
};

const closeDeleteModal = () => {
  deleteModal.value = false;
  setTimeout(() => {
    formToDelete.value = null;
  }, 200);
};

const deleteForm = () => {
  if (!formToDelete.value) return;
  
  processing.value = true;
  router.delete(route('admin.document-forms.destroy', formToDelete.value.id), {
    onFinish: () => {
      processing.value = false;
      closeDeleteModal();
    },
  });
};

const showPublicUrl = async (form) => {
  selectedForm.value = form;
  publicUrlModal.value = true;
  copied.value = false;
  
  try {
    const response = await axios.get(route('admin.document-forms.public-url', form.id));
    publicUrl.value = response.data.publicUrl;
  } catch (error) {
    console.error('Error fetching public URL:', error);
    publicUrl.value = 'Error: Tidak dapat mengambil URL publik';
  }
};

const closePublicUrlModal = () => {
  publicUrlModal.value = false;
  setTimeout(() => {
    selectedForm.value = null;
    publicUrl.value = '';
    copied.value = false;
  }, 200);
};

const copyToClipboard = () => {
  if (!publicUrlInput.value) return;
  
  publicUrlInput.value.select();
  document.execCommand('copy');
  copied.value = true;
  
  setTimeout(() => {
    copied.value = false;
  }, 2000);
};
</script>