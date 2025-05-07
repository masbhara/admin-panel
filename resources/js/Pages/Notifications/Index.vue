<template>
  <UserLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Notifikasi
      </h2>
    </template>

    <div class="space-y-6">
      <div class="bg-white shadow-sm rounded-lg divide-y divide-gray-200">
        <div class="p-4 sm:p-6">
          <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">
              Daftar Notifikasi
            </h3>
            <button
              v-if="hasUnread"
              @click="markAllAsRead"
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Tandai Semua Sudah Dibaca
            </button>
          </div>
        </div>

        <div v-if="notifications.data.length === 0" class="p-6 text-center text-gray-500">
          Tidak ada notifikasi
        </div>

        <div v-for="notification in notifications.data" :key="notification.id" 
          :class="[
            'p-6',
            { 'bg-indigo-50': !notification.read_at }
          ]"
        >
          <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                <icon :name="getNotificationIcon(notification.type)" class="w-6 h-6 text-indigo-600" />
              </div>
            </div>
            <div class="flex-grow">
              <div class="flex items-center justify-between">
                <h3 class="text-sm font-medium text-gray-900">
                  {{ getNotificationTitle(notification.type) }}
                </h3>
                <div class="flex items-center space-x-2">
                  <span class="text-sm text-gray-500">{{ notification.created_at }}</span>
                  <button
                    v-if="!notification.read_at"
                    @click="markAsRead(notification.id)"
                    class="text-indigo-600 hover:text-indigo-900"
                  >
                    Tandai Dibaca
                  </button>
                  <button
                    @click="deleteNotification(notification.id)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Hapus
                  </button>
                </div>
              </div>
              <p class="mt-1 text-sm text-gray-600">
                {{ notification.data.message }}
              </p>
              <div v-if="notification.read_at" class="mt-1 text-xs text-gray-500">
                Dibaca {{ notification.read_at }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <Pagination :links="notifications.links" />
    </div>
  </UserLayout>
</template>

<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  notifications: Object,
})

const hasUnread = computed(() => {
  return props.notifications.data.some(notification => !notification.read_at)
})

const getNotificationIcon = (type) => {
  const icons = {
    'ProfileUpdated': 'user',
    'SecurityAlert': 'shield-check',
    'default': 'bell'
  }
  return icons[type] || icons.default
}

const getNotificationTitle = (type) => {
  const titles = {
    'ProfileUpdated': 'Profil Diperbarui',
    'SecurityAlert': 'Pemberitahuan Keamanan',
    'default': 'Notifikasi Baru'
  }
  return titles[type] || titles.default
}

const markAsRead = (id) => {
  router.post(route('notifications.mark-as-read', id))
}

const markAllAsRead = () => {
  router.post(route('notifications.mark-all-as-read'))
}

const deleteNotification = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')) {
    router.delete(route('notifications.destroy', id))
  }
}
</script> 