<template>
  <AdminLayout title="Template Notifikasi WhatsApp" :user="$page.props.auth?.user">
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-text-primary">
        Template Notifikasi WhatsApp: {{ documentForm.name || documentForm.title }}
      </h2>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Alert untuk feedback -->
        <div v-if="showAlert" :class="[
          'p-4 mb-6 rounded-md',
          alertType === 'success' 
            ? 'bg-green-50 dark:bg-green-900/50 text-green-800 dark:text-green-300' 
            : 'bg-red-50 dark:bg-red-900/50 text-red-800 dark:text-red-300'
        ]">
          <div class="flex">
            <div class="flex-shrink-0">
              <CheckCircleIcon v-if="alertType === 'success'" class="h-5 w-5 text-green-400 dark:text-green-300" />
              <XCircleIcon v-else class="h-5 w-5 text-red-400 dark:text-red-300" />
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium">{{ alertMessage }}</p>
            </div>
            <div class="ml-auto pl-3">
              <div class="-mx-1.5 -my-1.5">
                <button
                  type="button"
                  @click="showAlert = false"
                  class="inline-flex rounded-md p-1.5"
                  :class="[
                    alertType === 'success' 
                      ? 'bg-green-50 dark:bg-green-900/50 text-green-500 dark:text-green-300 hover:bg-green-100 dark:hover:bg-green-900' 
                      : 'bg-red-50 dark:bg-red-900/50 text-red-500 dark:text-red-300 hover:bg-red-100 dark:hover:bg-red-900'
                  ]"
                >
                  <XMarkIcon class="h-5 w-5" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
            Template Notifikasi WhatsApp: {{ documentForm.name || documentForm.title }}
          </h1>
          <div class="flex space-x-2">
            <button 
              @click="openSettingsModal"
              class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
            >
              <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Pengaturan
            </button>
            <button 
              @click="openAddTemplateModal"
              class="inline-flex items-center px-4 py-2 bg-indigo-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
            >
              <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Tambah Template
            </button>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
          <!-- Tabel Template -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                  <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                    Nama
                  </th>
                  <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                    Tipe Event
                  </th>
                  <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                    Status
                  </th>
                  <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                    Dibuat
                  </th>
                  <th scope="col" class="px-6 py-4 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                    Aksi
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="(template, eventType) in notificationSettings.notification_templates" :key="eventType" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                  <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                    {{ template.name }}
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                    {{ eventLabels[eventType] || eventType }}
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full" 
                          :class="{ 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300': true }">
                      Aktif
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                    {{ formatDate(notificationSettings.created_at) }}
                  </td>
                  <td class="px-6 py-4 text-right text-sm font-medium space-x-2 whitespace-nowrap">
                    <button @click="viewTemplate(eventType, template)" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                      </svg>
                    </button>
                    <button @click="editTemplate(eventType, template)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                      </svg>
                    </button>
                    <button @click="confirmDeleteTemplate(eventType)" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                      </svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Pengaturan -->
    <Modal :show="showSettingsModal" @close="closeSettingsModal" max-width="sm:max-w-2xl">
      <div class="p-6 relative">
        <!-- Overlay loading -->
        <div v-if="settingsForm.processing" class="absolute inset-0 bg-gray-900/60 rounded-lg flex items-center justify-center z-10">
          <div class="bg-white dark:bg-gray-800 rounded-lg p-5 flex flex-col items-center">
            <svg class="animate-spin h-10 w-10 text-indigo-600 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-700 dark:text-gray-300 font-medium">Sedang menyimpan...</p>
          </div>
        </div>

        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
          Pengaturan Notifikasi WhatsApp
        </h2>
        
        <!-- Alert status koneksi di dalam modal -->
        <div v-if="showConnectionStatus" :class="[
          'p-4 mb-4 rounded-md',
          connectionStatus === 'success' 
            ? 'bg-green-50 dark:bg-green-900/50 text-green-800 dark:text-green-300' 
            : 'bg-red-50 dark:bg-red-900/50 text-red-800 dark:text-red-300'
        ]">
          <div class="flex">
            <div class="flex-shrink-0">
              <CheckCircleIcon v-if="connectionStatus === 'success'" class="h-5 w-5 text-green-400 dark:text-green-300" />
              <XCircleIcon v-else class="h-5 w-5 text-red-400 dark:text-red-300" />
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium">{{ connectionMessage }}</p>
            </div>
            <div class="ml-auto pl-3">
              <div class="-mx-1.5 -my-1.5">
                <button
                  type="button"
                  @click="showConnectionStatus = false"
                  class="inline-flex rounded-md p-1.5"
                  :class="[
                    connectionStatus === 'success' 
                      ? 'bg-green-50 dark:bg-green-900/50 text-green-500 dark:text-green-300 hover:bg-green-100 dark:hover:bg-green-900' 
                      : 'bg-red-50 dark:bg-red-900/50 text-red-500 dark:text-red-300 hover:bg-red-100 dark:hover:bg-red-900'
                  ]"
                >
                  <XMarkIcon class="h-5 w-5" />
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <form @submit.prevent="saveSettings" class="space-y-4 max-w-xl mx-auto">
          <div>
            <div class="flex items-center mb-4">
              <input
                type="checkbox"
                id="whatsapp_notification_enabled"
                v-model="settingsForm.whatsapp_notification_enabled"
                class="mr-2 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
              />
              <label for="whatsapp_notification_enabled" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                Aktifkan Notifikasi WhatsApp
              </label>
            </div>
          </div>
          
          <div>
            <label for="dripsender_api_key" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              API Key Dripsender
            </label>
            <div class="mt-1 flex rounded-md shadow-sm">
              <input
                type="text"
                id="dripsender_api_key"
                v-model="settingsForm.dripsender_api_key"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                placeholder="Masukkan API Key dari Dripsender"
              />
              <button
                type="button"
                @click="testConnection"
                class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                :disabled="testingConnection"
              >
                <svg v-if="testingConnection" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ testingConnection ? 'Menguji...' : 'Test Koneksi' }}
              </button>
            </div>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              API Key diperlukan untuk mengirim pesan WhatsApp melalui Dripsender.
            </p>
          </div>
          
          <div>
            <label for="dripsender_webhook_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Webhook URL (Opsional)
            </label>
            <input
              type="text"
              id="dripsender_webhook_url"
              v-model="settingsForm.dripsender_webhook_url"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              placeholder="https://example.com/webhook/whatsapp"
            />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              URL untuk menerima webhook dari Dripsender
            </p>
          </div>
          
          <div class="flex justify-end pt-4">
            <button
              type="button"
              @click="closeSettingsModal"
              class="mr-2 inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition"
              :disabled="settingsForm.processing"
            >
              Batal
            </button>
            <button
              type="submit"
              class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition min-w-[180px] justify-center"
              :disabled="settingsForm.processing"
            >
              <svg v-if="settingsForm.processing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ settingsForm.processing ? 'Menyimpan...' : 'Simpan Pengaturan' }}
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Modal Template -->
    <Modal :show="showTemplateModal" @close="closeTemplateModal" max-width="sm:max-w-lg">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
          {{ templateModalMode === 'add' ? 'Tambah Template Notifikasi' : 
             templateModalMode === 'edit' ? 'Edit Template Notifikasi' : 'Detail Template Notifikasi' }}
        </h2>
        
        <form @submit.prevent="saveTemplate" class="space-y-4 max-w-md mx-auto">
          <!-- Form fields -->
          <div v-if="templateModalMode !== 'view'">
            <label for="template_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Nama Template
            </label>
            <input
              type="text"
              id="template_name"
              v-model="templateForm.name"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              :disabled="templateModalMode === 'view'"
            />
          </div>
          
          <div v-if="templateModalMode === 'add'">
            <label for="event_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Tipe Event
            </label>
            <select
              id="event_type"
              v-model="templateForm.eventType"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            >
              <option value="document_uploaded">Dokumen Diunggah</option>
              <option value="document_approved">Dokumen Disetujui</option>
              <option value="document_rejected">Dokumen Ditolak</option>
            </select>
          </div>
          
          <div>
            <label for="template_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Template Pesan
            </label>
            <textarea
              id="template_message"
              v-model="templateForm.template"
              rows="5"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              :disabled="templateModalMode === 'view'"
            ></textarea>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
              Variabel yang tersedia: {{ templateForm.variables ? templateForm.variables.join(', ') : '' }}
            </p>
          </div>
          
          <div class="flex justify-end pt-4">
            <button
              type="button"
              @click="closeTemplateModal"
              class="mr-2 inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition"
            >
              {{ templateModalMode === 'view' ? 'Tutup' : 'Batal' }}
            </button>
            <button
              v-if="templateModalMode !== 'view'"
              type="submit"
              class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition"
              :disabled="templateForm.processing"
            >
              <svg v-if="templateForm.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ templateModalMode === 'add' ? 'Tambah Template' : 'Update Template' }}
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Modal Konfirmasi Hapus -->
    <Modal :show="showDeleteModal" @close="closeDeleteModal" max-width="md">
      <div class="p-6">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
          <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-center text-gray-900 dark:text-gray-100 mb-2">
          Konfirmasi Hapus Template
        </h3>
        <p class="text-sm text-center text-gray-500 dark:text-gray-400 mb-4">
          Apakah Anda yakin ingin menghapus template ini? Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="flex justify-center mt-4 space-x-4">
          <button
            @click="closeDeleteModal"
            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition"
          >
            Batal
          </button>
          <button
            @click="deleteTemplate"
            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring focus:ring-red-300 disabled:opacity-25 transition"
            :disabled="deleteForm.processing"
          >
            <svg v-if="deleteForm.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Hapus Template
          </button>
        </div>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Modal from '@/Components/Modal.vue';
import { 
  CheckCircleIcon,
  XCircleIcon,
  XMarkIcon
} from '@heroicons/vue/24/solid';

const props = defineProps({
  documentForm: Object,
  notificationSettings: Object,
  flash: Object,
});

// Alert state
const showAlert = ref(false);
const alertType = ref('success');
const alertMessage = ref('');

// Test connection state
const testingConnection = ref(false);

// State variables
const showSettingsModal = ref(false);
const showTemplateModal = ref(false);
const showDeleteModal = ref(false);
const templateModalMode = ref('view'); // 'view', 'add', 'edit'
const selectedEventType = ref('');

// State untuk notifikasi koneksi dalam modal
const showConnectionStatus = ref(false);
const connectionStatus = ref('success');
const connectionMessage = ref('');

// Label untuk tipe event
const eventLabels = {
  'document_uploaded': 'Notifikasi Dokumen Diunggah',
  'document_approved': 'Notifikasi Dokumen Disetujui',
  'document_rejected': 'Notifikasi Dokumen Ditolak'
};

// Form untuk pengaturan notifikasi
const settingsForm = useForm({
  whatsapp_notification_enabled: props.notificationSettings.whatsapp_notification_enabled,
  dripsender_api_key: props.notificationSettings.dripsender_api_key,
  dripsender_webhook_url: props.notificationSettings.dripsender_webhook_url,
});

// Form untuk template notifikasi
const templateForm = useForm({
  eventType: '',
  name: '',
  template: '',
  variables: ['name', 'file_name', 'status', 'uploaded_at'],
});

// Form untuk menghapus template
const deleteForm = useForm({});

// Format tanggal
const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const date = new Date(dateStr);
  return date.toLocaleDateString('id-ID');
};

// Helper untuk menampilkan alert
const showAlertMessage = (message, type = 'success', timeout = 5000) => {
  alertMessage.value = message;
  alertType.value = type;
  showAlert.value = true;
  
  if (timeout > 0) {
    setTimeout(() => {
      showAlert.value = false;
    }, timeout);
  }
};

// Cek flash messages saat komponen dimount
onMounted(() => {
  if (props.flash?.success) {
    showAlertMessage(props.flash.success);
  }
  
  if (props.flash?.error) {
    showAlertMessage(props.flash.error, 'error');
  }
});

// Fungsi untuk membuka modal pengaturan
const openSettingsModal = () => {
  showSettingsModal.value = true;
  settingsForm.whatsapp_notification_enabled = props.notificationSettings.whatsapp_notification_enabled;
  settingsForm.dripsender_api_key = props.notificationSettings.dripsender_api_key;
  settingsForm.dripsender_webhook_url = props.notificationSettings.dripsender_webhook_url;
};

// Fungsi untuk menutup modal pengaturan
const closeSettingsModal = () => {
  showSettingsModal.value = false;
  showConnectionStatus.value = false; // Reset status koneksi saat modal ditutup
};

// Fungsi untuk menyimpan pengaturan
const saveSettings = () => {
  settingsForm.put(route('admin.document-forms.notifications.update', props.documentForm.id), {
    preserveScroll: true,
    onSuccess: () => {
      showSettingsModal.value = false;
      showAlertMessage('Pengaturan notifikasi WhatsApp berhasil disimpan.');
    },
    onError: (errors) => {
      const errorMessage = Object.values(errors).join(', ');
      showAlertMessage(errorMessage || 'Gagal menyimpan pengaturan.', 'error');
    }
  });
};

// Fungsi untuk test koneksi Dripsender
const testConnection = () => {
  const apiKey = settingsForm.dripsender_api_key;
  if (!apiKey) {
    connectionStatus.value = 'error';
    connectionMessage.value = 'Silakan masukkan API Key terlebih dahulu.';
    showConnectionStatus.value = true;
    return;
  }

  testingConnection.value = true;
  showConnectionStatus.value = false; // Reset status saat memulai test baru
  
  const form = useForm({
    api_key: apiKey
  });
  
  form.post(route('admin.document-forms.notifications.test-connection', props.documentForm.id), {
    preserveScroll: true,
    onSuccess: (response) => {
      testingConnection.value = false;
      connectionStatus.value = 'success';
      
      if (response?.props?.flash?.success) {
        connectionMessage.value = response.props.flash.success;
      } else {
        connectionMessage.value = 'Koneksi ke Dripsender berhasil.';
      }
      
      showConnectionStatus.value = true;
    },
    onError: (errors) => {
      testingConnection.value = false;
      connectionStatus.value = 'error';
      const errorMessage = Object.values(errors).join(', ');
      connectionMessage.value = errorMessage || 'Koneksi ke Dripsender gagal.';
      showConnectionStatus.value = true;
    },
    onFinish: () => {
      testingConnection.value = false;
    }
  });
};

// Fungsi untuk melihat template
const viewTemplate = (eventType, template) => {
  selectedEventType.value = eventType;
  templateModalMode.value = 'view';
  templateForm.eventType = eventType;
  templateForm.name = template.name;
  templateForm.template = template.template;
  templateForm.variables = template.variables;
  showTemplateModal.value = true;
};

// Fungsi untuk menambahkan template baru
const openAddTemplateModal = () => {
  templateModalMode.value = 'add';
  templateForm.reset();
  templateForm.eventType = 'document_uploaded';
  templateForm.variables = ['name', 'file_name', 'status', 'uploaded_at'];
  showTemplateModal.value = true;
};

// Fungsi untuk mengedit template
const editTemplate = (eventType, template) => {
  selectedEventType.value = eventType;
  templateModalMode.value = 'edit';
  templateForm.eventType = eventType;
  templateForm.name = template.name;
  templateForm.template = template.template;
  templateForm.variables = template.variables;
  showTemplateModal.value = true;
};

// Fungsi untuk menyimpan template
const saveTemplate = () => {
  // Validasi
  if (!templateForm.name || !templateForm.template) {
    showAlertMessage('Nama template dan template pesan harus diisi.', 'error');
    return;
  }

  // Buat salinan dari notification_templates yang ada
  const updatedTemplates = { ...props.notificationSettings.notification_templates };
  
  if (templateModalMode.value === 'add') {
    // Tambahkan template baru
    updatedTemplates[templateForm.eventType] = {
      name: templateForm.name,
      template: templateForm.template,
      variables: templateForm.variables,
    };
  } else {
    // Update template yang ada
    updatedTemplates[selectedEventType.value] = {
      name: templateForm.name,
      template: templateForm.template,
      variables: templateForm.variables,
    };
  }
  
  // Update pengaturan dengan menggunakan form baru untuk menghindari masalah
  const updateForm = useForm({
    notification_templates: updatedTemplates,
  });
  
  // Kirim request ke server
  updateForm.put(route('admin.document-forms.notifications.update', props.documentForm.id), {
    preserveScroll: true,
    onSuccess: () => {
      showTemplateModal.value = false;
      showAlertMessage(templateModalMode.value === 'add' 
        ? 'Template notifikasi berhasil ditambahkan.' 
        : 'Template notifikasi berhasil diperbarui.');
      
      // Refresh halaman untuk memperbarui data
      window.location.reload();
    },
    onError: (errors) => {
      const errorMessage = Object.values(errors).join(', ');
      showAlertMessage(errorMessage || 'Gagal menyimpan template.', 'error');
    }
  });
};

// Fungsi untuk menutup modal template
const closeTemplateModal = () => {
  showTemplateModal.value = false;
  templateForm.reset();
};

// Fungsi untuk konfirmasi hapus template
const confirmDeleteTemplate = (eventType) => {
  selectedEventType.value = eventType;
  showDeleteModal.value = true;
};

// Fungsi untuk menghapus template
const deleteTemplate = () => {
  // Buat salinan dari notification_templates yang ada
  const updatedTemplates = { ...props.notificationSettings.notification_templates };
  
  // Hapus template
  if (updatedTemplates[selectedEventType.value]) {
    delete updatedTemplates[selectedEventType.value];
    
    // Update pengaturan
    deleteForm.notification_templates = updatedTemplates;
    deleteForm.put(route('admin.document-forms.notifications.update', props.documentForm.id), {
      preserveScroll: true,
      onSuccess: () => {
        showDeleteModal.value = false;
        showAlertMessage('Template notifikasi berhasil dihapus.');
      },
      onError: (errors) => {
        const errorMessage = Object.values(errors).join(', ');
        showAlertMessage(errorMessage || 'Gagal menghapus template.', 'error');
      }
    });
  }
};

// Fungsi untuk menutup modal konfirmasi hapus
const closeDeleteModal = () => {
  showDeleteModal.value = false;
};
</script>

<style scoped>
/* Dark theme untuk background */
.bg-gray-900 {
  background-color: #111827;
}
</style> 