<template>
  <AdminLayout>
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">Pengaturan Notifikasi WhatsApp</h1>
          <div class="flex space-x-3">
            <Link :href="route('admin.whatsapp-notifications.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
              <ArrowLeftIcon class="h-4 w-4 mr-2" />
              Kembali
            </Link>
          </div>
        </div>

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

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
          <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <form @submit.prevent="saveSettings">
              <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-4">
                  <InputLabel for="dripsender_api_key" value="API Key Dripsender" class="text-gray-700 dark:text-gray-300" />
                  <TextInput
                    id="dripsender_api_key"
                    type="text"
                    class="mt-1 block w-full dark:bg-gray-700 dark:text-white dark:border-gray-600"
                    v-model="form.dripsender_api_key"
                    required
                    autofocus
                  />
                  <InputError class="mt-2" :message="form.errors.dripsender_api_key" />
                </div>

                <div class="sm:col-span-6">
                  <InputLabel for="dripsender_webhook_url" value="Webhook URL Dripsender" class="text-gray-700 dark:text-gray-300" />
                  <TextInput
                    id="dripsender_webhook_url"
                    type="text"
                    class="mt-1 block w-full dark:bg-gray-700 dark:text-white dark:border-gray-600"
                    v-model="form.dripsender_webhook_url"
                    required
                  />
                  <InputError class="mt-2" :message="form.errors.dripsender_webhook_url" />
                </div>

                <div class="sm:col-span-4">
                  <div class="flex items-center">
                    <input
                      id="whatsapp_notification_enabled"
                      type="checkbox"
                      v-model="form.whatsapp_notification_enabled"
                      class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:text-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 dark:bg-gray-700"
                    />
                    <label for="whatsapp_notification_enabled" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                      Aktifkan notifikasi WhatsApp
                    </label>
                  </div>
                </div>
              </div>

              <div class="flex justify-end mt-6 space-x-3">
                <SecondaryButton
                  type="button"
                  @click="testConnection"
                  :disabled="form.processing || testingConnection"
                  :class="{ 'opacity-50 cursor-not-allowed': form.processing || testingConnection }"
                >
                  <span v-if="testingConnection" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-700 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Menguji...
                  </span>
                  <span v-else>Test Koneksi</span>
                </SecondaryButton>
                <PrimaryButton
                  type="submit"
                  :disabled="form.processing"
                  :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                >
                  {{ form.processing ? 'Menyimpan...' : 'Simpan Pengaturan' }}
                </PrimaryButton>
              </div>
            </form>
          </div>
        </div>

        <div class="mt-8">
          <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
            <div class="p-6 bg-white dark:bg-gray-800">
              <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Informasi Dripsender</h2>
              
              <div class="prose prose-sm dark:prose-invert max-w-none">
                <h3 class="text-gray-900 dark:text-white">Cara Mendapatkan API Key dan Webhook URL</h3>
                <ol class="text-gray-600 dark:text-gray-300">
                  <li>Login ke akun Dripsender Anda di <a href="https://dripsender.id" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">dripsender.id</a></li>
                  <li>Buka menu Pengaturan atau API</li>
                  <li>Salin API Key dan Webhook URL yang disediakan</li>
                  <li>Tempel nilai tersebut pada form di atas</li>
                </ol>
                
                <h3 class="mt-6 text-gray-900 dark:text-white">Variabel yang Didukung</h3>
                <p class="text-gray-600 dark:text-gray-300">Template notifikasi WhatsApp dapat menyertakan variabel berikut:</p>
                <ul class="text-gray-600 dark:text-gray-300">
                  <li><code class="bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">{{name}}</code> - Nama pengguna/pengunjung</li>
                  <li><code class="bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">{{file_name}}</code> - Nama file dokumen</li>
                  <li><code class="bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">{{status}}</code> - Status dokumen (pending/approved/rejected)</li>
                  <li><code class="bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">{{uploaded_at}}</code> - Tanggal dan waktu unggah</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, watch, inject } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { 
  ArrowLeftIcon,
  CheckCircleIcon,
  XCircleIcon,
  XMarkIcon
} from '@heroicons/vue/24/solid';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

// Inject route function dari plugin Ziggy
const route = inject('route');

const props = defineProps({
  settings: Object,
  flash: Object,
});

// Watch flash messages dari server
watch(() => props.flash, (newFlash) => {
  if (newFlash.success) {
    showAlert.value = true;
    alertType.value = 'success';
    alertMessage.value = newFlash.success;
    setTimeout(() => {
      showAlert.value = false;
    }, 5000);
  }
  
  if (newFlash.error) {
    showAlert.value = true;
    alertType.value = 'error';
    alertMessage.value = newFlash.error;
    setTimeout(() => {
      showAlert.value = false;
    }, 5000);
  }
}, { immediate: true });

const testingConnection = ref(false);
const showAlert = ref(false);
const alertType = ref('success');
const alertMessage = ref('');

const form = useForm({
  dripsender_api_key: props.settings.dripsender_api_key ? props.settings.dripsender_api_key.value : '',
  dripsender_webhook_url: props.settings.dripsender_webhook_url ? props.settings.dripsender_webhook_url.value : '',
  whatsapp_notification_enabled: props.settings.whatsapp_notification_enabled ? props.settings.whatsapp_notification_enabled.value : false,
});

const saveSettings = () => {
  form.post(route('admin.whatsapp-notifications.settings.update'), {
    preserveScroll: true,
    onSuccess: () => {
      showAlert.value = true;
      alertType.value = 'success';
      alertMessage.value = 'Pengaturan berhasil disimpan';
      setTimeout(() => {
        showAlert.value = false;
      }, 5000);
    },
    onError: () => {
      showAlert.value = true;
      alertType.value = 'error';
      alertMessage.value = 'Gagal menyimpan pengaturan';
      setTimeout(() => {
        showAlert.value = false;
      }, 5000);
    },
  });
};

const testConnection = () => {
  testingConnection.value = true;
  form.post(route('admin.whatsapp-notifications.test-connection'), {
    preserveScroll: true,
    onSuccess: () => {
      showAlert.value = true;
      alertType.value = 'success';
      alertMessage.value = 'Koneksi berhasil';
      testingConnection.value = false;
      setTimeout(() => {
        showAlert.value = false;
      }, 5000);
    },
    onError: () => {
      showAlert.value = true;
      alertType.value = 'error';
      alertMessage.value = 'Koneksi gagal';
      testingConnection.value = false;
      setTimeout(() => {
        showAlert.value = false;
      }, 5000);
    },
  });
};
</script> 