<template>
  <div class="bg-white border-b border-gray-200">
    <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
      <!-- Left side: Toggle button and breadcrumb -->
      <div class="flex items-center">
        <button
          type="button"
          class="text-gray-500 hover:text-gray-600 lg:hidden"
          @click="$emit('toggle-sidebar')"
        >
          <span class="sr-only">Buka sidebar</span>
          <Bars3Icon class="h-6 w-6" aria-hidden="true" />
        </button>

        <!-- Breadcrumb (hidden on small screens) -->
        <div class="hidden md:ml-4 md:flex">
          <slot name="breadcrumb"></slot>
        </div>
      </div>

      <!-- Center: Search -->
      <div class="flex flex-1 justify-center px-2 lg:ml-6 lg:justify-end">
        <div class="w-full max-w-lg lg:max-w-xs">
          <label for="search" class="sr-only">Cari</label>
          <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
              <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
            </div>
            <input
              id="search"
              name="search"
              class="block w-full rounded-md border-0 bg-white py-1.5 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
              placeholder="Cari"
              type="search"
              v-model="searchQuery"
              @keyup.enter="performSearch"
              @focus="showSearchResults = true"
              @blur="hideSearchResultsDelayed"
            />
          </div>

          <!-- Search Results Dropdown -->
          <div
            v-if="showSearchResults && searchResults.length > 0"
            class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
          >
            <div v-for="(result, index) in searchResults" :key="index" class="group">
              <a
                :href="result.url"
                class="flex items-center px-4 py-2 hover:bg-gray-100"
                @mousedown.prevent="navigateToResult(result)"
              >
                <component
                  :is="result.icon || DocumentTextIcon"
                  class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500"
                />
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ result.title }}</p>
                  <p class="text-xs text-gray-500">{{ result.category }}</p>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Right side: Notifications and Profile -->
      <div class="flex items-center space-x-4">
        <!-- Quick Actions Button -->
        <div class="relative">
          <button
            type="button"
            class="rounded-full bg-white p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
            @click="toggleQuickActions"
          >
            <span class="sr-only">Quick actions</span>
            <EllipsisHorizontalIcon class="h-6 w-6" aria-hidden="true" />
          </button>

          <!-- Quick Actions Dropdown -->
          <div
            v-if="showQuickActions"
            class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
          >
            <a
              v-for="(action, index) in quickActions"
              :key="index"
              :href="action.url"
              class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              @click.prevent="executeQuickAction(action)"
            >
              <component
                :is="action.icon"
                class="mr-3 h-5 w-5 text-gray-400"
                aria-hidden="true"
              />
              {{ action.label }}
            </a>
          </div>
        </div>

        <!-- Notifications -->
        <div class="relative">
          <button
            type="button"
            class="rounded-full bg-white p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
            @click="toggleNotifications"
          >
            <span class="sr-only">Lihat notifikasi</span>
            <BellIcon class="h-6 w-6" aria-hidden="true" />
            <!-- Notification Badge -->
            <span
              v-if="unreadNotificationsCount > 0"
              class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-medium text-white"
            >
              {{ unreadNotificationsCount > 9 ? '9+' : unreadNotificationsCount }}
            </span>
          </button>

          <!-- Notifications Dropdown -->
          <div
            v-if="showNotifications"
            class="absolute right-0 z-10 mt-2 w-80 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
          >
            <div class="px-4 py-2 border-b border-gray-100">
              <div class="flex items-center justify-between">
                <h3 class="text-sm font-medium text-gray-900">Notifikasi</h3>
                <button
                  v-if="unreadNotificationsCount > 0"
                  type="button"
                  class="text-xs font-medium text-primary-600 hover:text-primary-500"
                  @click="markAllAsRead"
                >
                  Tandai semua sebagai dibaca
                </button>
              </div>
            </div>

            <div class="max-h-72 overflow-y-auto">
              <div v-if="notifications.length === 0" class="px-4 py-6 text-center">
                <BellSlashIcon class="mx-auto h-8 w-8 text-gray-400" />
                <p class="mt-1 text-sm text-gray-500">Tidak ada notifikasi</p>
              </div>

              <a
                v-for="(notification, index) in notifications"
                :key="index"
                :href="notification.url"
                class="block px-4 py-3 hover:bg-gray-50"
                :class="{ 'bg-primary-50': !notification.read }"
                @click.prevent="openNotification(notification)"
              >
                <div class="flex items-start">
                  <div
                    class="flex-shrink-0 rounded-full p-1"
                    :class="{
                      'bg-blue-100': notification.type === 'info',
                      'bg-green-100': notification.type === 'success',
                      'bg-yellow-100': notification.type === 'warning',
                      'bg-red-100': notification.type === 'error'
                    }"
                  >
                    <component
                      :is="getNotificationIcon(notification.type)"
                      class="h-5 w-5"
                      :class="{
                        'text-blue-500': notification.type === 'info',
                        'text-green-500': notification.type === 'success',
                        'text-yellow-500': notification.type === 'warning',
                        'text-red-500': notification.type === 'error'
                      }"
                    />
                  </div>
                  <div class="ml-3 w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ notification.title }}</p>
                    <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ notification.message }}</p>
                    <p class="mt-1 text-xs text-gray-400">{{ formatTime(notification.time) }}</p>
                  </div>
                  <div class="ml-4 flex flex-shrink-0">
                    <button
                      type="button"
                      class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none"
                      @click.stop="removeNotification(notification)"
                    >
                      <span class="sr-only">Tutup</span>
                      <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                    </button>
                  </div>
                </div>
              </a>
            </div>

            <div class="border-t border-gray-100 px-4 py-2">
              <a
                href="#"
                class="block text-center text-xs font-medium text-primary-600 hover:text-primary-500"
                @click.prevent="viewAllNotifications"
              >
                Lihat semua notifikasi
              </a>
            </div>
          </div>
        </div>

        <!-- Profile dropdown -->
        <div class="relative">
          <button
            type="button"
            class="flex rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
            @click="toggleUserMenu"
          >
            <span class="sr-only">Buka menu pengguna</span>
            <img
              v-if="user.avatar"
              class="h-8 w-8 rounded-full"
              :src="user.avatar"
              :alt="user.name"
            />
            <div
              v-else
              class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-100 text-sm font-medium text-primary-600"
            >
              {{ userInitials }}
            </div>
          </button>

          <!-- User Menu Dropdown -->
          <div
            v-if="showUserMenu"
            class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
          >
            <div class="border-b border-gray-100 px-4 py-2">
              <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
              <p class="text-xs text-gray-500">{{ user.email }}</p>
            </div>

            <a
              v-for="(item, index) in userMenuItems"
              :key="index"
              :href="item.url"
              class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              @click.prevent="executeUserMenuItem(item)"
            >
              <component
                :is="item.icon"
                class="mr-3 h-5 w-5 text-gray-400"
                aria-hidden="true"
              />
              {{ item.label }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import {
  Bars3Icon,
  BellIcon,
  BellSlashIcon,
  MagnifyingGlassIcon,
  UserIcon,
  Cog6ToothIcon,
  ArrowRightOnRectangleIcon,
  DocumentTextIcon,
  EllipsisHorizontalIcon,
  XMarkIcon,
  InformationCircleIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon,
  XCircleIcon,
  PlusIcon,
  DocumentPlusIcon,
  UserPlusIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  user: {
    type: Object,
    default: () => ({
      name: 'User',
      email: 'user@example.com',
      avatar: ''
    })
  },
  notifications: {
    type: Array,
    default: () => []
  },
  searchProvider: {
    type: Function,
    default: null
  }
})

const emit = defineEmits([
  'toggle-sidebar',
  'search',
  'notification-click',
  'mark-all-read',
  'remove-notification',
  'view-all-notifications',
  'user-menu-action',
  'quick-action'
])

const router = useRouter()

// State
const searchQuery = ref('')
const searchResults = ref([])
const showSearchResults = ref(false)
const showNotifications = ref(false)
const showUserMenu = ref(false)
const showQuickActions = ref(false)
const searchResultsTimeout = ref(null)

// Computed
const userInitials = computed(() => {
  if (!props.user.name) return ''
  return props.user.name
    .split(' ')
    .map(part => part.charAt(0))
    .join('')
    .toUpperCase()
    .substring(0, 2)
})

const unreadNotificationsCount = computed(() => {
  return props.notifications.filter(notification => !notification.read).length
})

// Quick Actions
const quickActions = [
  {
    label: 'Tambah Pengguna',
    icon: UserPlusIcon,
    url: '/users/create',
    action: () => router.push('/users/create')
  },
  {
    label: 'Buat Dokumen',
    icon: DocumentPlusIcon,
    url: '/documents/create',
    action: () => router.push('/documents/create')
  },
  {
    label: 'Tambah Item',
    icon: PlusIcon,
    url: '/items/create',
    action: () => router.push('/items/create')
  }
]

// User Menu Items
const userMenuItems = [
  {
    label: 'Profil',
    icon: UserIcon,
    url: '/profile',
    action: () => router.push('/profile')
  },
  {
    label: 'Pengaturan',
    icon: Cog6ToothIcon,
    url: '/settings',
    action: () => router.push('/settings')
  },
  {
    label: 'Keluar',
    icon: ArrowRightOnRectangleIcon,
    url: '/logout',
    action: () => {
      // Implement logout logic
      router.push('/login')
    }
  }
]

// Methods
const performSearch = async () => {
  if (!searchQuery.value.trim()) {
    searchResults.value = []
    return
  }

  emit('search', searchQuery.value)

  // If a search provider is provided, use it
  if (props.searchProvider && typeof props.searchProvider === 'function') {
    try {
      const results = await props.searchProvider(searchQuery.value)
      searchResults.value = results
    } catch (error) {
      console.error('Search error:', error)
      searchResults.value = []
    }
  } else {
    // Mock search results
    searchResults.value = [
      {
        title: 'Hasil pencarian untuk "' + searchQuery.value + '"',
        category: 'Dokumen',
        url: '/search?q=' + encodeURIComponent(searchQuery.value),
        icon: DocumentTextIcon
      }
    ]
  }
}

const navigateToResult = (result) => {
  if (result.action && typeof result.action === 'function') {
    result.action()
  } else if (result.url) {
    router.push(result.url)
  }
  showSearchResults.value = false
}

const hideSearchResultsDelayed = () => {
  searchResultsTimeout.value = setTimeout(() => {
    showSearchResults.value = false
  }, 200)
}

// Toggle functions
const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value
  if (showNotifications.value) {
    showUserMenu.value = false
    showQuickActions.value = false
  }
}

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
  if (showUserMenu.value) {
    showNotifications.value = false
    showQuickActions.value = false
  }
}

const toggleQuickActions = () => {
  showQuickActions.value = !showQuickActions.value
  if (showQuickActions.value) {
    showNotifications.value = false
    showUserMenu.value = false
  }
}

// Notification functions
const getNotificationIcon = (type) => {
  switch (type) {
    case 'info':
      return InformationCircleIcon
    case 'success':
      return CheckCircleIcon
    case 'warning':
      return ExclamationTriangleIcon
    case 'error':
      return XCircleIcon
    default:
      return BellIcon
  }
}

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

const openNotification = (notification) => {
  emit('notification-click', notification)
  showNotifications.value = false
}

const markAllAsRead = () => {
  emit('mark-all-read')
}

const removeNotification = (notification) => {
  emit('remove-notification', notification)
}

const viewAllNotifications = () => {
  emit('view-all-notifications')
  showNotifications.value = false
  router.push('/notifications')
}

// User menu functions
const executeUserMenuItem = (item) => {
  if (item.action && typeof item.action === 'function') {
    item.action()
  }
  emit('user-menu-action', item)
  showUserMenu.value = false
}

// Quick actions functions
const executeQuickAction = (action) => {
  if (action.action && typeof action.action === 'function') {
    action.action()
  }
  emit('quick-action', action)
  showQuickActions.value = false
}

// Click outside handler
const handleClickOutside = (event) => {
  // Close dropdowns when clicking outside
  if (showNotifications.value || showUserMenu.value || showQuickActions.value) {
    const isOutside = !event.target.closest('.relative')
    if (isOutside) {
      showNotifications.value = false
      showUserMenu.value = false
      showQuickActions.value = false
    }
  }
}

// Lifecycle hooks
onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
  if (searchResultsTimeout.value) {
    clearTimeout(searchResultsTimeout.value)
  }
})
</script>
