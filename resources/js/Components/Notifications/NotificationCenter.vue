<template>
  <div class="bg-white shadow rounded-lg overflow-hidden">
    <!-- Header -->
    <div class="border-b border-gray-200 px-4 py-3">
      <div class="flex items-center justify-between">
        <h3 class="text-base font-medium text-gray-900">
          Notifikasi
          <span
            v-if="unreadCount > 0"
            class="ml-2 inline-flex items-center rounded-full bg-primary-100 px-2.5 py-0.5 text-xs font-medium text-primary-800"
          >
            {{ unreadCount }} baru
          </span>
        </h3>
        <div class="flex space-x-2">
          <button
            v-if="unreadCount > 0"
            type="button"
            class="text-sm font-medium text-primary-600 hover:text-primary-500"
            @click="markAllAsRead"
          >
            Tandai semua dibaca
          </button>
          <button
            v-if="showSettings"
            type="button"
            class="text-gray-400 hover:text-gray-500"
            @click="$emit('settings')"
          >
            <span class="sr-only">Pengaturan notifikasi</span>
            <Cog6ToothIcon class="h-5 w-5" />
          </button>
        </div>
      </div>
    </div>

    <!-- Tabs -->
    <div v-if="showTabs" class="border-b border-gray-200">
      <div class="flex">
        <button
          v-for="tab in tabs"
          :key="tab.value"
          type="button"
          class="flex-1 py-2.5 text-sm font-medium"
          :class="[
            activeTab === tab.value
              ? 'border-b-2 border-primary-500 text-primary-600'
              : 'text-gray-500 hover:text-gray-700'
          ]"
          @click="activeTab = tab.value"
        >
          {{ tab.label }}
          <span
            v-if="tab.count > 0"
            class="ml-1 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600"
          >
            {{ tab.count }}
          </span>
        </button>
      </div>
    </div>

    <!-- Filter -->
    <div v-if="showFilter && types.length > 0" class="border-b border-gray-200 px-4 py-2">
      <div class="flex flex-wrap gap-2">
        <button
          type="button"
          class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium"
          :class="selectedType === 'all' ? 'bg-primary-100 text-primary-800' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'"
          @click="selectedType = 'all'"
        >
          Semua
        </button>
        <button
          v-for="type in types"
          :key="type.value"
          type="button"
          class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium"
          :class="selectedType === type.value ? 'bg-primary-100 text-primary-800' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'"
          @click="selectedType = type.value"
        >
          <component
            v-if="type.icon"
            :is="type.icon"
            class="mr-1 h-3 w-3"
            :class="selectedType === type.value ? 'text-primary-600' : 'text-gray-500'"
          />
          {{ type.label }}
        </button>
      </div>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="py-12 text-center">
      <div class="inline-flex items-center">
        <svg class="h-5 w-5 animate-spin text-primary-500" viewBox="0 0 24 24">
          <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
          ></circle>
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          ></path>
        </svg>
        <span class="ml-2 text-sm font-medium text-gray-500">Memuat notifikasi...</span>
      </div>
    </div>

    <!-- Empty state -->
    <div
      v-else-if="!loading && filteredNotifications.length === 0"
      class="py-12 text-center"
    >
      <BellSlashIcon class="mx-auto h-12 w-12 text-gray-300" />
      <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada notifikasi</h3>
      <p class="mt-1 text-sm text-gray-500">{{ emptyMessage }}</p>
    </div>

    <!-- Notifications list -->
    <div v-else class="max-h-96 overflow-y-auto">
      <div v-for="(notification, index) in filteredNotifications" :key="notification.id">
        <div
          class="relative px-4 py-3 hover:bg-gray-50"
          :class="{ 'bg-primary-50': !notification.read }"
        >
          <div class="flex">
            <!-- Notification icon -->
            <div class="flex-shrink-0">
              <div
                class="flex h-10 w-10 items-center justify-center rounded-full"
                :class="{
                  'bg-blue-100': notification.type === 'info',
                  'bg-green-100': notification.type === 'success',
                  'bg-yellow-100': notification.type === 'warning',
                  'bg-red-100': notification.type === 'error',
                  'bg-gray-100': !notification.type || notification.type === 'default'
                }"
              >
                <component
                  :is="getNotificationIcon(notification.type)"
                  class="h-6 w-6"
                  :class="{
                    'text-blue-600': notification.type === 'info',
                    'text-green-600': notification.type === 'success',
                    'text-yellow-600': notification.type === 'warning',
                    'text-red-600': notification.type === 'error',
                    'text-gray-600': !notification.type || notification.type === 'default'
                  }"
                />
              </div>
            </div>

            <!-- Notification content -->
            <div class="ml-3 flex-1">
              <div class="flex items-start justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ notification.title }}</p>
                  <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ notification.message }}</p>
                  <p class="mt-1 text-xs text-gray-400">{{ formatTime(notification.time) }}</p>
                </div>
                <div class="ml-4 flex flex-shrink-0">
                  <button
                    v-if="!notification.read"
                    type="button"
                    class="mr-2 inline-flex rounded-md bg-transparent text-gray-400 hover:text-gray-500"
                    @click="markAsRead(notification)"
                  >
                    <span class="sr-only">Tandai dibaca</span>
                    <CheckIcon class="h-5 w-5" aria-hidden="true" />
                  </button>
                  <button
                    type="button"
                    class="inline-flex rounded-md bg-transparent text-gray-400 hover:text-gray-500"
                    @click="removeNotification(notification)"
                  >
                    <span class="sr-only">Hapus</span>
                    <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                  </button>
                </div>
              </div>

              <!-- Action buttons -->
              <div v-if="notification.actions && notification.actions.length > 0" class="mt-3 flex space-x-3">
                <button
                  v-for="(action, actionIndex) in notification.actions"
                  :key="actionIndex"
                  type="button"
                  class="rounded-md px-2.5 py-1.5 text-xs font-medium"
                  :class="action.primary ? 'bg-primary-600 text-white hover:bg-primary-700' : 'bg-white text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50'"
                  @click="handleAction(notification, action)"
                >
                  {{ action.label }}
                </button>
              </div>
            </div>
          </div>
        </div>
        <!-- Divider except for last item -->
        <div v-if="index < filteredNotifications.length - 1" class="mx-4 border-t border-gray-100"></div>
      </div>
    </div>

    <!-- Footer -->
    <div class="border-t border-gray-200 px-4 py-3">
      <div class="flex items-center justify-between">
        <div v-if="showPagination && totalPages > 1" class="flex items-center space-x-2">
          <button
            type="button"
            class="inline-flex items-center rounded-md bg-white px-2 py-1 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
            :disabled="currentPage === 1"
            @click="$emit('page-change', currentPage - 1)"
          >
            <ChevronLeftIcon class="h-4 w-4" />
          </button>
          <span class="text-sm text-gray-500">
            {{ currentPage }} / {{ totalPages }}
          </span>
          <button
            type="button"
            class="inline-flex items-center rounded-md bg-white px-2 py-1 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
            :disabled="currentPage === totalPages"
            @click="$emit('page-change', currentPage + 1)"
          >
            <ChevronRightIcon class="h-4 w-4" />
          </button>
        </div>
        <button
          type="button"
          class="text-sm font-medium text-primary-600 hover:text-primary-500"
          @click="$emit('view-all')"
        >
          Lihat semua
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import {
  BellIcon,
  BellSlashIcon,
  InformationCircleIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon,
  XCircleIcon,
  XMarkIcon,
  CheckIcon,
  Cog6ToothIcon,
  ChevronLeftIcon,
  ChevronRightIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  notifications: {
    type: Array,
    required: true
  },
  loading: {
    type: Boolean,
    default: false
  },
  emptyMessage: {
    type: String,
    default: 'Tidak ada notifikasi yang tersedia.'
  },
  showFilter: {
    type: Boolean,
    default: true
  },
  types: {
    type: Array,
    default: () => []
  },
  showTabs: {
    type: Boolean,
    default: true
  },
  tabs: {
    type: Array,
    default: () => [
      { label: 'Semua', value: 'all', count: 0 },
      { label: 'Belum dibaca', value: 'unread', count: 0 }
    ]
  },
  showSettings: {
    type: Boolean,
    default: true
  },
  showPagination: {
    type: Boolean,
    default: true
  },
  currentPage: {
    type: Number,
    default: 1
  },
  totalPages: {
    type: Number,
    default: 1
  }
})

const emit = defineEmits([
  'mark-read',
  'mark-all-read',
  'remove',
  'action',
  'view-all',
  'settings',
  'page-change',
  'tab-change',
  'type-change'
])

// State
const activeTab = ref('all')
const selectedType = ref('all')

// Computed
const filteredNotifications = computed(() => {
  let filtered = [...props.notifications]
  
  // Filter by tab
  if (activeTab.value === 'unread') {
    filtered = filtered.filter(notification => !notification.read)
  }
  
  // Filter by type
  if (selectedType.value !== 'all') {
    filtered = filtered.filter(notification => notification.type === selectedType.value)
  }
  
  return filtered
})

const unreadCount = computed(() => {
  return props.notifications.filter(notification => !notification.read).length
})

// Update tab counts
const updatedTabs = computed(() => {
  const allCount = props.notifications.length
  const unreadCount = props.notifications.filter(notification => !notification.read).length
  
  return props.tabs.map(tab => {
    if (tab.value === 'all') {
      return { ...tab, count: allCount }
    } else if (tab.value === 'unread') {
      return { ...tab, count: unreadCount }
    }
    return tab
  })
})

// Watch for tab changes
watch(activeTab, (newValue) => {
  emit('tab-change', newValue)
})

// Watch for type changes
watch(selectedType, (newValue) => {
  emit('type-change', newValue)
})

// Methods
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

const markAsRead = (notification) => {
  emit('mark-read', notification)
}

const markAllAsRead = () => {
  emit('mark-all-read')
}

const removeNotification = (notification) => {
  emit('remove', notification)
}

const handleAction = (notification, action) => {
  emit('action', { notification, action })
  
  if (action.markAsRead) {
    markAsRead(notification)
  }
}
</script>
