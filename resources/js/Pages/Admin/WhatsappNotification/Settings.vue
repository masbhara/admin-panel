<template>
  <AdminLayout>
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-semibold text-gray-900">Pengaturan Notifikasi WhatsApp</h1>
          <div class="flex space-x-3">
            <Link :href="route('admin.whatsapp-notifications.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              <ArrowLeftIcon class="h-4 w-4 mr-2" />
              Kembali
            </Link>
          </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
          <div class="p-6 bg-white border-b border-gray-200">
            <form @submit.prevent="saveSettings">
              <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-4">
                  <InputLabel for="dripsender_api_key" value="API Key Dripsender" />
                  <TextInput
                    id="dripsender_api_key"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.dripsender_api_key"
                    required
                    autofocus
                  />
                  <InputError class="mt-2" :message="form.errors.dripsender_api_key" />
                </div>

                <div class="sm:col-span-6">
                  <InputLabel for="dripsender_webhook_url" value="Webhook URL Dripsender" />
                  <TextInput
                    id="dripsender_webhook_url"
                    type="text"
                    class="mt-1 block w-full"
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
                      class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    />
                    <label for="whatsapp_notification_enabled" class="ml-2 block text-sm text-gray-900">Aktifkan notifikasi WhatsApp</label>
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
                  {{ testingConnection ? 'Menguji...' : 'Test Koneksi' }}
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
          <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="p-6 bg-white">
              <h2 class="text-lg font-medium text-gray-900 mb-4">Informasi Dripsender</h2>
              
              <div class="prose prose-sm max-w-none">
                <h3>Cara Mendapatkan API Key dan Webhook URL</h3>
                <ol>
                  <li>Login ke akun Dripsender Anda di <a href="https://dripsender.id" target="_blank" class="text-indigo-600 hover:text-indigo-900">dripsender.id</a></li>
                  <li>Buka menu Pengaturan atau API</li>
                  <li>Salin API Key dan Webhook URL yang disediakan</li>
                  <li>Tempel nilai tersebut pada form di atas</li>
                </ol>
                
                <h3 class="mt-6">Variabel yang Didukung</h3>
                <p>Template notifikasi WhatsApp dapat menyertakan variabel berikut:</p>
                <ul>
                  <li><code>{{name}}</code> - Nama pengguna/pengunjung</li>
                  <li><code>{{file_name}}</code> - Nama file dokumen</li>
                  <li><code>{{status}}</code> - Status dokumen (pending/approved/rejected)</li>
                  <li><code>{{uploaded_at}}</code> - Tanggal dan waktu unggah</li>
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
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon } from '@heroicons/vue/solid';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
  settings: Object,
});

const testingConnection = ref(false);

const form = useForm({
  dripsender_api_key: props.settings.dripsender_api_key ? props.settings.dripsender_api_key.value : '',
  dripsender_webhook_url: props.settings.dripsender_webhook_url ? props.settings.dripsender_webhook_url.value : '',
  whatsapp_notification_enabled: props.settings.whatsapp_notification_enabled ? 
    (props.settings.whatsapp_notification_enabled.value === true || 
     props.settings.whatsapp_notification_enabled.value === '1' || 
     props.settings.whatsapp_notification_enabled.value === 'true') : false,
});

const saveSettings = () => {
  form.post(route('admin.whatsapp-notifications.settings.update'));
};

const testConnection = () => {
  testingConnection.value = true;
  
  axios.post(route('admin.whatsapp-notifications.test-connection'))
    .then(response => {
      if (response.data.success) {
        alert('Koneksi ke Dripsender berhasil!');
      } else {
        alert('Gagal terhubung ke Dripsender: ' + response.data.message);
      }
      testingConnection.value = false;
    })
    .catch(error => {
      alert('Error: ' + (error.response?.data?.message || 'Terjadi kesalahan saat menguji koneksi'));
      testingConnection.value = false;
    });
};
</script> 