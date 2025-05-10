<template>
  <AdminLayout>
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-semibold text-gray-900">Template Notifikasi WhatsApp</h1>
          <div class="flex space-x-3">
            <Link :href="route('admin.whatsapp-notifications.settings')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
              <CogIcon class="h-4 w-4 mr-2" />
              Pengaturan
            </Link>
            <Link :href="route('admin.whatsapp-notifications.create')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              <PlusIcon class="h-4 w-4 mr-2" />
              Tambah Template
            </Link>
          </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
          <div class="p-6 bg-white border-b border-gray-200">
            <div v-if="notifications.data.length === 0" class="text-center py-8">
              <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100">
                <BellIcon class="h-6 w-6 text-gray-600" />
              </div>
              <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada template</h3>
              <p class="mt-1 text-sm text-gray-500">
                Mulai dengan membuat template notifikasi WhatsApp pertama Anda.
              </p>
              <div class="mt-6">
                <Link :href="route('admin.whatsapp-notifications.create')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  <PlusIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                  Tambah Template
                </Link>
              </div>
            </div>

            <div v-else>
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe Event</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="notification in notifications.data" :key="notification.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">{{ notification.name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-500">{{ notification.event_type }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="[
                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                        notification.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                      ]">
                        {{ notification.is_active ? 'Aktif' : 'Tidak Aktif' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ new Date(notification.created_at).toLocaleDateString('id-ID') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div class="flex justify-end space-x-2">
                        <Link :href="route('admin.whatsapp-notifications.show', notification.id)" class="text-blue-600 hover:text-blue-900">
                          <EyeIcon class="h-5 w-5" />
                        </Link>
                        <Link :href="route('admin.whatsapp-notifications.edit', notification.id)" class="text-indigo-600 hover:text-indigo-900">
                          <PencilIcon class="h-5 w-5" />
                        </Link>
                        <button @click="confirmDelete(notification)" class="text-red-600 hover:text-red-900">
                          <TrashIcon class="h-5 w-5" />
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>

              <Pagination :links="notifications.links" class="mt-6" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <Modal :show="deleteModalOpen" @close="deleteModalOpen = false">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">Hapus Template Notifikasi</h2>
        <p class="mt-2 text-sm text-gray-500">
          Apakah Anda yakin ingin menghapus template "{{ selectedNotification ? selectedNotification.name : '' }}"? 
          Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="mt-6 flex justify-end space-x-3">
          <SecondaryButton @click="deleteModalOpen = false">Batal</SecondaryButton>
          <DangerButton @click="deleteNotification" :disabled="isDeleting" :class="{ 'opacity-50 cursor-not-allowed': isDeleting }">
            {{ isDeleting ? 'Menghapus...' : 'Hapus' }}
          </DangerButton>
        </div>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { 
  PlusIcon, 
  PencilIcon, 
  TrashIcon, 
  EyeIcon,
  BellIcon,
  CogIcon
} from '@heroicons/vue/solid';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
  notifications: Object,
});

const deleteModalOpen = ref(false);
const selectedNotification = ref(null);
const isDeleting = ref(false);

const deleteForm = useForm({});

const confirmDelete = (notification) => {
  selectedNotification.value = notification;
  deleteModalOpen.value = true;
};

const deleteNotification = () => {
  isDeleting.value = true;
  deleteForm.delete(route('admin.whatsapp-notifications.destroy', selectedNotification.value.id), {
    onSuccess: () => {
      deleteModalOpen.value = false;
      isDeleting.value = false;
    },
    onError: () => {
      isDeleting.value = false;
    }
  });
};
</script> 