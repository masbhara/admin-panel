<template>
  <AdminLayout>
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-semibold text-gray-900">Detail Template Notifikasi</h1>
          <div class="flex space-x-3">
            <Link :href="route('admin.whatsapp-notifications.edit', notification.id)" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              <PencilIcon class="h-4 w-4 mr-2" />
              Edit Template
            </Link>
            <Link :href="route('admin.whatsapp-notifications.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              <ArrowLeftIcon class="h-4 w-4 mr-2" />
              Kembali
            </Link>
          </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
          <div class="p-6 bg-white border-b border-gray-200">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
              <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Nama Template</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ notification.name }}</dd>
              </div>
              
              <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Tipe Event</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ eventTypeLabel(notification.event_type) }}</dd>
              </div>
              
              <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1 text-sm text-gray-900">
                  <span :class="[
                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                    notification.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                  ]">
                    {{ notification.is_active ? 'Aktif' : 'Tidak Aktif' }}
                  </span>
                </dd>
              </div>
              
              <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Tanggal Dibuat</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(notification.created_at) }}</dd>
              </div>
              
              <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Template Pesan</dt>
                <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-4 rounded whitespace-pre-wrap">{{ notification.template }}</dd>
              </div>
              
              <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Contoh Pesan</dt>
                <dd class="mt-1 text-sm text-gray-900 bg-blue-50 p-4 rounded whitespace-pre-wrap">{{ previewTemplate }}</dd>
              </div>
              
              <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Variabel Tersedia</dt>
                <dd class="mt-1 text-sm text-gray-900">
                  <div class="flex flex-wrap gap-2">
                    <span v-for="(variable, index) in availableVariables" :key="index" class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-gray-100 text-gray-800">
                      {{ variable.name }}
                    </span>
                  </div>
                </dd>
              </div>
            </dl>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { PencilIcon, ArrowLeftIcon } from '@heroicons/vue/solid';

const props = defineProps({
  notification: Object,
});

const availableVariables = [
  { name: '{{name}}', description: 'Nama pengguna' },
  { name: '{{file_name}}', description: 'Nama file dokumen' },
  { name: '{{status}}', description: 'Status dokumen' },
  { name: '{{uploaded_at}}', description: 'Tanggal dan waktu unggah' },
];

// Helper function to format dates
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', { 
    dateStyle: 'long',
    timeStyle: 'short'
  }).format(date);
};

// Helper function to get event type readable label
const eventTypeLabel = (eventType) => {
  const labels = {
    'document_uploaded': 'Dokumen Diunggah',
    'document_approved': 'Dokumen Disetujui',
    'document_rejected': 'Dokumen Ditolak',
    'custom': 'Kustom'
  };
  
  return labels[eventType] || eventType;
};

// Generate a preview with sample data
const previewTemplate = computed(() => {
  let preview = props.notification.template;
  
  const sampleData = {
    'name': 'John Doe',
    'file_name': 'dokumen_penting.pdf',
    'status': 'pending',
    'uploaded_at': '25-05-2023 14:30:00'
  };
  
  for (const [key, value] of Object.entries(sampleData)) {
    preview = preview.replace(new RegExp(`{{${key}}}`, 'g'), value);
  }
  
  return preview;
});
</script> 