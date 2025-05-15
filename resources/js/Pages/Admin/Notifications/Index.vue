<template>
  <AdminLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Notifikasi Admin
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Header dengan tombol mark all as read -->
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-medium text-gray-900">
                Daftar Notifikasi
                <span v-if="unreadCount > 0" class="ml-2 text-sm text-primary-600">
                  ({{ unreadCount }} belum dibaca)
                </span>
              </h3>
              <button
                v-if="unreadCount > 0"
                @click="markAllAsRead"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
              >
                Tandai Semua Dibaca
              </button>
            </div>

            <!-- Daftar Notifikasi -->
            <div v-if="notifications.data.length === 0" class="text-center py-8">
              <BellSlashIcon class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada notifikasi</h3>
              <p class="mt-1 text-sm text-gray-500">
                Anda akan melihat notifikasi di sini ketika ada aktivitas baru.
              </p>
            </div>

            <div v-else class="space-y-4">
              <div
                v-for="notification in notifications.data"
                :key="notification.id"
                :class="[
                  'p-4 rounded-lg',
                  notification.read_at ? 'bg-white' : 'bg-primary-50'
                ]"
              >
                <div class="flex items-start">
                  <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                      <DocumentIcon class="h-5 w-5 text-primary-600" />
                    </div>
                  </div>
                  <div class="ml-4 flex-1">
                    <div class="flex items-center justify-between">
                      <p class="text-sm font-medium text-gray-900">
                        {{ notification.data?.sender_name || 'Pengirim tidak diketahui' }}
                      </p>
                      <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500">
                          {{ formatTime(notification.data?.time) }}
                        </span>
                        <button
                          v-if="!notification.read_at"
                          @click="markAsRead(notification.id)"
                          class="text-primary-600 hover:text-primary-900"
                          title="Tandai sudah dibaca"
                        >
                          <CheckIcon class="h-5 w-5" />
                        </button>
                        <button
                          @click="removeNotification(notification.id)"
                          class="text-gray-400 hover:text-gray-600"
                          title="Hapus notifikasi"
                        >
                          <XMarkIcon class="h-5 w-5" />
                        </button>
                      </div>
                    </div>
                    <p class="mt-1 text-sm text-gray-600">
                      {{ notification.data?.message || 'Tidak ada pesan' }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="notifications.data.length > 0" class="mt-6">
              <Pagination :links="notifications.links" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { BellSlashIcon, DocumentIcon, CheckIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import Pagination from '@/Components/Pagination.vue'
import axios from 'axios'

const props = defineProps({
  notifications: Object
})

const unreadCount = computed(() => {
  return props.notifications.data.filter(n => !n.read_at).length
})

const formatTime = (time) => {
  if (!time) return ''
  
  const date = new Date(time)
  const now = new Date()
  const diffMs = now - date
  const diffSec = Math.floor(diffMs / 1000)
  const diffMin = Math.floor(diffSec / 60)
  const diffHour = Math.floor(diffMin / 60)
  const diffDay = Math.floor(diffHour / 24)

  if (diffSec < 60) {
    return 'Baru saja'
  } else if (diffMin < 60) {
    return `${diffMin} menit yang lalu`
  } else if (diffHour < 24) {
    return `${diffHour} jam yang lalu`
  } else if (diffDay < 7) {
    return `${diffDay} hari yang lalu`
  } else {
    return date.toLocaleDateString('id-ID', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    })
  }
}

const markAsRead = async (id) => {
  try {
    await axios.post(`/admin/notifications/${id}/mark-as-read`)
    window.location.reload()
  } catch (error) {
    console.error('Error marking notification as read:', error)
  }
}

const markAllAsRead = async () => {
  try {
    await axios.post('/admin/notifications/mark-all-as-read')
    window.location.reload()
  } catch (error) {
    console.error('Error marking all notifications as read:', error)
  }
}

const removeNotification = async (id) => {
  try {
    await axios.delete(`/admin/notifications/${id}`)
    window.location.reload()
  } catch (error) {
    console.error('Error removing notification:', error)
  }
}
</script> 