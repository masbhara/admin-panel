<template>
  <TransitionGroup
    tag="div"
    :class="[
      'fixed z-50 flex flex-col gap-2',
      positionClasses
    ]"
    enter-active-class="transform ease-out duration-300 transition"
    enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-for="toast in toasts"
      :key="toast.id"
      :class="[
        'pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg shadow-lg ring-1',
        toast.type === 'success' ? 'bg-green-50 ring-green-200' : '',
        toast.type === 'error' ? 'bg-red-50 ring-red-200' : '',
        toast.type === 'warning' ? 'bg-yellow-50 ring-yellow-200' : '',
        toast.type === 'info' ? 'bg-blue-50 ring-blue-200' : '',
        toast.type === 'default' ? 'bg-white ring-gray-200' : ''
      ]"
    >
      <div class="p-4">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <CheckCircleIcon
              v-if="toast.type === 'success'"
              class="h-6 w-6 text-green-500"
              aria-hidden="true"
            />
            <XCircleIcon
              v-else-if="toast.type === 'error'"
              class="h-6 w-6 text-red-500"
              aria-hidden="true"
            />
            <ExclamationTriangleIcon
              v-else-if="toast.type === 'warning'"
              class="h-6 w-6 text-yellow-500"
              aria-hidden="true"
            />
            <InformationCircleIcon
              v-else-if="toast.type === 'info'"
              class="h-6 w-6 text-blue-500"
              aria-hidden="true"
            />
            <BellIcon
              v-else
              class="h-6 w-6 text-gray-500"
              aria-hidden="true"
            />
          </div>
          <div class="ml-3 w-0 flex-1 pt-0.5">
            <p v-if="toast.title" class="text-sm font-medium text-gray-900">
              {{ toast.title }}
            </p>
            <p class="mt-1 text-sm text-gray-500">
              {{ toast.message }}
            </p>
            <div v-if="toast.action" class="mt-3 flex space-x-4">
              <button
                type="button"
                class="rounded-md bg-white text-sm font-medium text-primary-600 hover:text-primary-500"
                @click="handleAction(toast)"
              >
                {{ toast.action.text }}
              </button>
            </div>
          </div>
          <div class="ml-4 flex flex-shrink-0">
            <button
              type="button"
              class="inline-flex rounded-md bg-transparent text-gray-400 hover:text-gray-500"
              @click="removeToast(toast.id)"
            >
              <span class="sr-only">Tutup</span>
              <XMarkIcon class="h-5 w-5" aria-hidden="true" />
            </button>
          </div>
        </div>
      </div>
      <!-- Progress bar for auto-dismiss -->
      <div
        v-if="toast.duration && toast.duration > 0"
        class="h-1 bg-gray-200"
      >
        <div
          class="h-full transition-all duration-100"
          :class="[
            toast.type === 'success' ? 'bg-green-500' : '',
            toast.type === 'error' ? 'bg-red-500' : '',
            toast.type === 'warning' ? 'bg-yellow-500' : '',
            toast.type === 'info' ? 'bg-blue-500' : '',
            toast.type === 'default' ? 'bg-gray-500' : ''
          ]"
          :style="{ width: `${getProgressWidth(toast)}%` }"
        ></div>
      </div>
    </div>
  </TransitionGroup>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { TransitionGroup } from 'vue'
import {
  CheckCircleIcon,
  XCircleIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon,
  BellIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  position: {
    type: String,
    default: 'top-right',
    validator: (value) => [
      'top-right',
      'top-left',
      'top-center',
      'bottom-right',
      'bottom-left',
      'bottom-center'
    ].includes(value)
  },
  maxToasts: {
    type: Number,
    default: 5
  }
})

const emit = defineEmits(['action', 'remove'])

// State
const toasts = ref([])
const toastTimeouts = ref({})
const toastStartTimes = ref({})

// Position classes
const positionClasses = computed(() => {
  switch (props.position) {
    case 'top-right':
      return 'top-4 right-4 sm:top-6 sm:right-6'
    case 'top-left':
      return 'top-4 left-4 sm:top-6 sm:left-6'
    case 'top-center':
      return 'top-4 left-1/2 -translate-x-1/2 sm:top-6'
    case 'bottom-right':
      return 'bottom-4 right-4 sm:bottom-6 sm:right-6'
    case 'bottom-left':
      return 'bottom-4 left-4 sm:bottom-6 sm:left-6'
    case 'bottom-center':
      return 'bottom-4 left-1/2 -translate-x-1/2 sm:bottom-6'
    default:
      return 'top-4 right-4 sm:top-6 sm:right-6'
  }
})

// Methods
const addToast = (toast) => {
  const id = Date.now().toString()
  const newToast = {
    id,
    type: toast.type || 'default',
    title: toast.title || '',
    message: toast.message || '',
    duration: toast.duration !== undefined ? toast.duration : 5000,
    action: toast.action || null
  }
  
  // Add toast to the array
  toasts.value.unshift(newToast)
  
  // Limit the number of toasts
  if (toasts.value.length > props.maxToasts) {
    const oldestToast = toasts.value.pop()
    clearTimeout(toastTimeouts.value[oldestToast.id])
    delete toastTimeouts.value[oldestToast.id]
    delete toastStartTimes.value[oldestToast.id]
  }
  
  // Set auto-dismiss timeout
  if (newToast.duration && newToast.duration > 0) {
    toastStartTimes.value[id] = Date.now()
    toastTimeouts.value[id] = setTimeout(() => {
      removeToast(id)
    }, newToast.duration)
  }
  
  return id
}

const removeToast = (id) => {
  const index = toasts.value.findIndex(toast => toast.id === id)
  if (index !== -1) {
    const toast = toasts.value[index]
    toasts.value.splice(index, 1)
    
    // Clear timeout
    if (toastTimeouts.value[id]) {
      clearTimeout(toastTimeouts.value[id])
      delete toastTimeouts.value[id]
    }
    
    // Remove start time
    if (toastStartTimes.value[id]) {
      delete toastStartTimes.value[id]
    }
    
    emit('remove', toast)
  }
}

const handleAction = (toast) => {
  if (toast.action && typeof toast.action.onClick === 'function') {
    toast.action.onClick()
  }
  
  emit('action', toast)
  
  if (toast.action && toast.action.dismissOnClick) {
    removeToast(toast.id)
  }
}

const getProgressWidth = (toast) => {
  if (!toast.duration || toast.duration <= 0) return 100
  
  const startTime = toastStartTimes.value[toast.id] || Date.now()
  const elapsedTime = Date.now() - startTime
  const remainingTime = toast.duration - elapsedTime
  const percentage = (remainingTime / toast.duration) * 100
  
  return Math.max(0, Math.min(100, percentage))
}

// Update progress bars
let progressInterval = null

const updateProgress = () => {
  // Only update if there are toasts with duration
  const hasTimedToasts = toasts.value.some(toast => toast.duration && toast.duration > 0)
  if (!hasTimedToasts) return
  
  // Force a re-render to update progress bars
  toasts.value = [...toasts.value]
}

// Expose methods to parent component
defineExpose({
  addToast,
  removeToast
})

// Lifecycle hooks
onMounted(() => {
  // Start progress update interval
  progressInterval = setInterval(updateProgress, 100)
})

onBeforeUnmount(() => {
  // Clear all timeouts
  Object.values(toastTimeouts.value).forEach(timeout => {
    clearTimeout(timeout)
  })
  
  // Clear progress interval
  if (progressInterval) {
    clearInterval(progressInterval)
  }
})
</script>
