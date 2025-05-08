<template>
  <AdminLayout title="Manajemen Dokumen">
    <Head title="Manajemen Dokumen" />
    
    <div class="container mx-auto py-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Manajemen Dokumen</h1>
      </div>

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

      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
          <div v-if="!documents || !documents.data || documents.data.length === 0" class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">Belum ada dokumen</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              Belum ada dokumen yang dikirimkan oleh pengguna.
            </p>
          </div>

          <div v-else>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">WhatsApp</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kota/Kabupaten</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Dokumen</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                  <tr v-for="document in documents.data" :key="document.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                      {{ document.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                      {{ document.whatsapp }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                      {{ document.city }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                      <a 
                        v-if="document && document.media && document.media.length > 0"
                        :href="document.media[0].original_url" 
                        target="_blank" 
                        class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 flex items-center"
                      >
                        <svg class="h-5 w-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                        </svg>
                        {{ document.media[0].file_name }}
                      </a>
                      <span v-else>-</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                      {{ formatDate(document.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div class="flex justify-end space-x-2">
                        <Link 
                          :href="route('admin.documents.show', document.id)" 
                          class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300"
                        >
                          Detail
                        </Link>
                        <button 
                          @click="confirmDelete(document)"
                          class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                        >
                          Hapus
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            
            <Pagination 
              v-if="documents && documents.links"
              class="mt-6" 
              :links="documents.links"
            />
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal Konfirmasi Hapus -->
    <Modal :show="showDeleteModal" @close="closeModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
          Konfirmasi Hapus
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          Apakah Anda yakin ingin menghapus dokumen dari {{ selectedDocument?.name || '' }}?
          <br>Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="mt-6 flex justify-end space-x-3">
          <button
            type="button"
            class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition"
            @click="closeModal"
          >
            Batal
          </button>
          <button
            type="button"
            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition"
            @click="deleteDocument"
            :disabled="isDeleting"
          >
            <svg v-if="isDeleting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isDeleting ? 'Menghapus...' : 'Hapus' }}
          </button>
        </div>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  documents: Object,
});

const showDeleteModal = ref(false);
const selectedDocument = ref(null);
const isDeleting = ref(false);

const formatDate = (dateString) => {
  if (!dateString) return '-';
  
  const options = { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  };
  
  return new Date(dateString).toLocaleDateString('id-ID', options);
};

const confirmDelete = (document) => {
  selectedDocument.value = document;
  showDeleteModal.value = true;
};

const closeModal = () => {
  showDeleteModal.value = false;
  setTimeout(() => {
    selectedDocument.value = null;
  }, 300);
};

const deleteDocument = () => {
  if (!selectedDocument || !selectedDocument.value || !selectedDocument.value.id) return;
  
  isDeleting.value = true;
  
  router.delete(route('admin.documents.destroy', selectedDocument.value.id), {
    onSuccess: () => {
      closeModal();
      isDeleting.value = false;
    },
    onError: () => {
      isDeleting.value = false;
    },
  });
};
</script> 