<template>
  <div class="flow-root">
    <!-- Header -->
    <div v-if="title || $slots.header" class="mb-4 flex items-center justify-between">
      <div>
        <h3 v-if="title" class="text-base font-medium text-gray-900">{{ title }}</h3>
        <p v-if="subtitle" class="mt-1 text-sm text-gray-500">{{ subtitle }}</p>
      </div>
      <slot name="header"></slot>
    </div>

    <!-- Filter -->
    <div v-if="showFilter && types.length > 0" class="mb-4">
      <div class="flex flex-wrap gap-2">
        <button
          type="button"
          class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium"
          :class="selectedType === 'all' ? 'bg-primary-100 text-primary-800' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'"
          @click="selectedType = 'all'"
        >
          Semua
        </button>
        <button
          v-for="type in types"
          :key="type.value"
          type="button"
          class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium"
          :class="selectedType === type.value ? 'bg-primary-100 text-primary-800' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'"
          @click="selectedType = type.value"
        >
          <component
            v-if="type.icon"
            :is="type.icon"
            class="mr-1.5 h-4 w-4"
            :class="selectedType === type.value ? 'text-primary-600' : 'text-gray-500'"
          />
          {{ type.label }}
        </button>
      </div>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="py-8 text-center">
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
        <span class="ml-2 text-sm font-medium text-gray-500">Memuat aktivitas...</span>
      </div>
    </div>

    <!-- Empty state -->
    <div
      v-else-if="!loading && filteredActivities.length === 0"
      class="py-8 text-center"
    >
      <ClockIcon class="mx-auto h-12 w-12 text-gray-300" />
      <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada aktivitas</h3>
      <p class="mt-1 text-sm text-gray-500">{{ emptyMessage }}</p>
      <slot name="empty-action"></slot>
    </div>

    <!-- Timeline -->
    <ul v-else role="list" class="-mb-8">
      <li v-for="(activity, activityIdx) in filteredActivities" :key="activity.id">
        <div class="relative pb-8">
          <!-- Connector line -->
          <span
            v-if="activityIdx !== filteredActivities.length - 1"
            class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200"
            aria-hidden="true"
          ></span>

          <div class="relative flex space-x-3">
            <!-- Icon -->
            <div>
              <span
                class="flex h-8 w-8 items-center justify-center rounded-full ring-8 ring-white"
                :class="getActivityIconClass(activity.type)"
              >
                <component
                  :is="getActivityIcon(activity.type)"
                  class="h-5 w-5 text-white"
                  aria-hidden="true"
                />
              </span>
            </div>

            <!-- Content -->
            <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
              <div>
                <p class="text-sm text-gray-800">
                  <span class="font-medium">{{ activity.user }}</span>
                  {{ activity.action }}
                  <a
                    v-if="activity.target"
                    :href="activity.targetUrl"
                    class="font-medium text-primary-600 hover:text-primary-800"
                    @click.prevent="handleTargetClick(activity)"
                  >
                    {{ activity.target }}
                  </a>
                </p>
                <p v-if="activity.description" class="mt-1 text-sm text-gray-500">
                  {{ activity.description }}
                </p>
              </div>
              <div class="whitespace-nowrap text-right text-sm text-gray-500">
                <time :datetime="activity.date">{{ formatDate(activity.date) }}</time>
              </div>
            </div>
          </div>
        </div>
      </li>
    </ul>

    <!-- Load more -->
    <div
      v-if="hasMore && !loading && filteredActivities.length > 0"
      class="mt-4 text-center"
    >
      <button
        type="button"
        class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
        @click="$emit('load-more')"
      >
        <ArrowPathIcon class="mr-1.5 h-4 w-4 text-gray-400" />
        Muat lebih banyak
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import {
  UserIcon,
  DocumentTextIcon,
  PencilIcon,
  TrashIcon,
  ClockIcon,
  ArrowPathIcon,
  CheckCircleIcon,
  ExclamationCircleIcon,
  LockClosedIcon,
  BellIcon,
  ChatBubbleLeftIcon,
  CogIcon,
  ArrowUpCircleIcon,
  ArrowDownCircleIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  activities: {
    type: Array,
    required: true
  },
  title: {
    type: String,
    default: 'Aktivitas Terbaru'
  },
  subtitle: {
    type: String,
    default: ''
  },
  loading: {
    type: Boolean,
    default: false
  },
  hasMore: {
    type: Boolean,
    default: false
  },
  emptyMessage: {
    type: String,
    default: 'Belum ada aktivitas yang tercatat.'
  },
  showFilter: {
    type: Boolean,
    default: true
  },
  types: {
    type: Array,
    default: () => []
  },
  dateFormatter: {
    type: Function,
    default: null
  }
})

const emit = defineEmits(['target-click', 'load-more'])

// State
const selectedType = ref('all')

// Reset filter when activities change
watch(
  () => props.activities,
  () => {
    selectedType.value = 'all'
  }
)

// Filtered activities
const filteredActivities = computed(() => {
  if (selectedType.value === 'all') {
    return props.activities
  }
  
  return props.activities.filter(activity => activity.type === selectedType.value)
})

// Activity icons and colors
const getActivityIcon = (type) => {
  const typeConfig = props.types.find(t => t.value === type)
  if (typeConfig && typeConfig.icon) {
    return typeConfig.icon
  }
  
  // Default icons by type
  switch (type) {
    case 'create':
      return DocumentTextIcon
    case 'update':
      return PencilIcon
    case 'delete':
      return TrashIcon
    case 'login':
      return ArrowUpCircleIcon
    case 'logout':
      return ArrowDownCircleIcon
    case 'approve':
      return CheckCircleIcon
    case 'reject':
      return ExclamationCircleIcon
    case 'lock':
      return LockClosedIcon
    case 'notification':
      return BellIcon
    case 'comment':
      return ChatBubbleLeftIcon
    case 'settings':
      return CogIcon
    default:
      return UserIcon
  }
}

const getActivityIconClass = (type) => {
  const typeConfig = props.types.find(t => t.value === type)
  if (typeConfig && typeConfig.color) {
    return typeConfig.color
  }
  
  // Default colors by type
  switch (type) {
    case 'create':
      return 'bg-green-500'
    case 'update':
      return 'bg-blue-500'
    case 'delete':
      return 'bg-red-500'
    case 'login':
      return 'bg-primary-500'
    case 'logout':
      return 'bg-gray-500'
    case 'approve':
      return 'bg-green-500'
    case 'reject':
      return 'bg-red-500'
    case 'lock':
      return 'bg-yellow-500'
    case 'notification':
      return 'bg-purple-500'
    case 'comment':
      return 'bg-indigo-500'
    case 'settings':
      return 'bg-gray-500'
    default:
      return 'bg-gray-500'
  }
}

// Format date
const formatDate = (date) => {
  if (props.dateFormatter && typeof props.dateFormatter === 'function') {
    return props.dateFormatter(date)
  }
  
  if (!date) return ''
  
  const dateObj = new Date(date)
  const now = new Date()
  const diffMs = now - dateObj
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
    return dateObj.toLocaleDateString('id-ID', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    })
  }
}

// Handle target click
const handleTargetClick = (activity) => {
  emit('target-click', activity)
}
</script>
