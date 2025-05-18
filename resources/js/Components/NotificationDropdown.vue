<template>
  <Menu as="div" class="relative">
    <MenuButton class="-m-1.5 flex items-center p-1.5">
      <span class="sr-only">Lihat notifikasi</span>
      <div class="relative">
        <BellIcon class="h-6 w-6 text-gray-400" aria-hidden="true" />
        <div v-if="unreadCount > 0" class="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
          {{ unreadCount }}
        </div>
      </div>
    </MenuButton>

    <transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <MenuItems class="absolute right-0 z-10 mt-2 w-80 origin-top-right rounded-md bg-white dark:bg-gray-800 py-2 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
        <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Notifikasi</h3>
            <button
              v-if="unreadCount > 0"
              @click="markAllAsRead"
              class="text-xs font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300"
            >
              Tandai semua dibaca
            </button>
          </div>
        </div>

        <div class="max-h-96 overflow-y-auto">
          <div v-if="!notificationList || notificationList.length === 0" class="px-4 py-6 text-center">
            <BellSlashIcon class="mx-auto h-8 w-8 text-gray-400 dark:text-gray-500" />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tidak ada notifikasi</p>
          </div>

          <MenuItem v-for="notification in notificationList" :key="notification.id" v-slot="{ active }">
            <div
              :class="[
                active ? 'bg-gray-50 dark:bg-gray-700/50' : '',
                !notification.read_at ? 'bg-primary-50 dark:bg-primary-900/20' : '',
                'block px-4 py-3'
              ]"
            >
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-800 flex items-center justify-center">
                    <DocumentIcon class="h-4 w-4 text-primary-600 dark:text-primary-400" />
                  </div>
                </div>
                <div class="ml-3 flex-1">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ notification.data?.sender_name || 'Pengirim tidak diketahui' }}
                  </p>
                  <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">
                    {{ notification.data?.message || 'Tidak ada pesan' }}
                  </p>
                  <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                    {{ formatTime(notification.data?.time) }}
                  </p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                  <button
                    v-if="!notification.read_at"
                    @click.stop="markAsRead(notification.id)"
                    class="text-primary-600 dark:text-primary-400 hover:text-primary-900 dark:hover:text-primary-300"
                  >
                    <CheckIcon class="h-4 w-4" />
                  </button>
                  <button
                    @click.stop="removeNotification(notification.id)"
                    class="ml-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400"
                  >
                    <XMarkIcon class="h-4 w-4" />
                  </button>
                </div>
              </div>
            </div>
          </MenuItem>
        </div>

        <div class="border-t border-gray-100 dark:border-gray-700 px-4 py-2">
          <Link
            :href="notificationsRoute"
            class="block text-center text-xs font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300"
          >
            Lihat semua notifikasi
          </Link>
        </div>
      </MenuItems>
    </transition>
  </Menu>
</template>

<script setup>
import { computed, onMounted, onUnmounted } from 'vue'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { BellIcon, BellSlashIcon, DocumentIcon, CheckIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { Link, usePage } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  notifications: {
    type: [Array, Object],
    default: () => [],
    required: false
  }
})

const page = usePage()
const isAdmin = computed(() => {
  return page.props.auth?.user?.roles?.some(role => role.name === 'admin') ?? false
})

const notificationsRoute = computed(() => {
  return route(isAdmin.value ? 'admin.notifications.index' : 'notifications')
})

const getApiEndpoint = (endpoint) => {
  const prefix = isAdmin.value ? '/admin' : ''
  return `${prefix}${endpoint}`
}

// Convert notifications to array if needed
const notificationList = computed(() => {
  if (!props.notifications) return [];
  if (Array.isArray(props.notifications)) return props.notifications;
  if (props.notifications.data && Array.isArray(props.notifications.data)) return props.notifications.data;
  return [];
})

const unreadCount = computed(() => {
  return notificationList.value.filter(n => !n.read_at).length
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
    await axios.post(`${getApiEndpoint('/notifications')}/${id}/mark-as-read`)
    window.location.reload()
  } catch (error) {
    console.error('Error marking notification as read:', error)
  }
}

const markAllAsRead = async () => {
  try {
    await axios.post(`${getApiEndpoint('/notifications')}/mark-all-as-read`)
    window.location.reload()
  } catch (error) {
    console.error('Error marking all notifications as read:', error)
  }
}

const removeNotification = async (id) => {
  try {
    await axios.delete(`${getApiEndpoint('/notifications')}/${id}`)
    window.location.reload()
  } catch (error) {
    console.error('Error removing notification:', error)
  }
}
</script>
