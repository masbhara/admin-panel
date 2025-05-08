<template>
  <AdminLayout title="Detail Dokumen">
    <Head title="Detail Dokumen" />
    
    <div class="container mx-auto py-6">
      <div class="flex items-center mb-6">
        <Link
          :href="route('admin.documents.index')" 
          class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 flex items-center mr-4"
        >
          <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Kembali
        </Link>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Detail Dokumen</h1>
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

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Detail Informasi -->
        <div class="md:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Informasi Pengirim</h2>
            
            <div v-if="document" class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Lengkap</h3>
                  <p class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ document.name }}</p>
                </div>
                
                <div>
                  <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Nomor WhatsApp</h3>
                  <div class="mt-1 flex items-center">
                    <p class="text-base font-medium text-gray-900 dark:text-white">
                      {{ document.whatsapp }}
                    </p>
                    <a 
                      v-if="document.whatsapp"
                      :href="`https://wa.me/${formatWhatsApp(document.whatsapp)}`"
                      target="_blank"
                      class="ml-2 text-green-600 hover:text-green-700 dark:text-green-500 dark:hover:text-green-400"
                      title="Chat di WhatsApp"
                    >
                      <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                      </svg>
                    </a>
                  </div>
                </div>
              </div>
              
              <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Kota/Kabupaten</h3>
                <p class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ document.city }}</p>
              </div>
              
              <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pengiriman</h3>
                <p class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ formatDate(document.created_at) }}</p>
              </div>
            </div>
            
            <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6 flex justify-end">
              <button 
                @click="confirmDelete"
                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition"
              >
                Hapus Dokumen
              </button>
            </div>
          </div>
        </div>
        
        <!-- Preview Dokumen -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Dokumen</h2>
            
            <div v-if="document && document.media && document.media.length > 0" class="space-y-4">
              <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                <div class="flex items-center mb-3">
                  <svg class="h-8 w-8 text-primary-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                  </svg>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                      {{ document.media[0].file_name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                      {{ formatFileSize(document.media[0].size) }}
                    </p>
                  </div>
                </div>
                
                <div class="mt-2 flex flex-col space-y-2">
                  <a 
                    :href="document.media[0].original_url" 
                    target="_blank"
                    class="inline-flex justify-center items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition"
                  >
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Lihat Dokumen
                  </a>
                  
                  <a 
                    :href="document.media[0].original_url" 
                    download
                    class="inline-flex justify-center items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition"
                  >
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Unduh Dokumen
                  </a>
                </div>
              </div>
              
              <div class="text-xs text-gray-500 dark:text-gray-400">
                Diunggah pada {{ formatDate(document.media[0].created_at) }}
              </div>
            </div>
            
            <div v-else class="text-center py-8">
              <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada dokumen</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Dokumen tidak ditemukan atau telah dihapus.
              </p>
            </div>
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
          Apakah Anda yakin ingin menghapus dokumen dari {{ document && document.name ? document.name : 'pengguna ini' }}?
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
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  document: Object,
});

const showDeleteModal = ref(false);
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

const formatFileSize = (sizeInBytes) => {
  if (!sizeInBytes) return '0 Bytes';
  
  const units = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
  let i = 0;
  let size = parseFloat(sizeInBytes);
  
  while (size >= 1024 && i < units.length - 1) {
    size /= 1024;
    i++;
  }
  
  return `${size.toFixed(2)} ${units[i]}`;
};

const formatWhatsApp = (number) => {
  if (!number) return '';
  
  // Hapus semua karakter non-digit
  let cleaned = number.replace(/\D/g, '');
  
  // Pastikan dimulai dengan kode negara (62 untuk Indonesia)
  if (cleaned.startsWith('0')) {
    cleaned = `62${cleaned.substring(1)}`;
  } else if (!cleaned.startsWith('62')) {
    cleaned = `62${cleaned}`;
  }
  
  return cleaned;
};

const confirmDelete = () => {
  showDeleteModal.value = true;
};

const closeModal = () => {
  showDeleteModal.value = false;
};

const deleteDocument = () => {
  if (!props.document || !props.document.id) {
    closeModal();
    return;
  }
  
  isDeleting.value = true;
  
  router.delete(route('admin.documents.destroy', props.document.id), {
    onSuccess: () => {
      router.visit(route('admin.documents.index'));
    },
    onError: () => {
      isDeleting.value = false;
    },
  });
};
</script> 