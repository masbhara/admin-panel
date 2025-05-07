<template>
  <div 
    class="fixed z-50 top-4 right-4 flex flex-col gap-2 max-w-md w-full"
    aria-live="assertive"
  >
    <TransitionGroup 
      name="toast"
      tag="div"
      class="flex flex-col gap-2"
    >
      <div
        v-for="toast in toasts" 
        :key="toast.id"
        :class="[
          'rounded-md shadow-lg p-4 flex items-start',
          'transform transition-all duration-300',
          toastTypeClasses[toast.type] || toastTypeClasses.info
        ]"
        role="alert"
      >
        <!-- Icon berdasarkan tipe toast -->
        <div class="flex-shrink-0 mr-3 pt-0.5">
          <CheckCircleIcon v-if="toast.type === 'success'" class="h-5 w-5" />
          <ExclamationCircleIcon v-else-if="toast.type === 'error'" class="h-5 w-5" />
          <ExclamationTriangleIcon v-else-if="toast.type === 'warning'" class="h-5 w-5" />
          <InformationCircleIcon v-else class="h-5 w-5" />
        </div>
        
        <!-- Konten toast -->
        <div class="flex-1 pt-0.5">
          <p class="text-sm font-medium">{{ toast.message }}</p>
        </div>
        
        <!-- Tombol close -->
        <div class="ml-4 flex-shrink-0 flex">
          <button
            @click="removeToast(toast.id)"
            class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none"
          >
            <span class="sr-only">Close</span>
            <XMarkIcon class="h-5 w-5" />
          </button>
        </div>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useToast } from '@/Composables/useToast'
import {
  CheckCircleIcon,
  ExclamationCircleIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon,
  XMarkIcon
} from '@heroicons/vue/24/solid'

const { toasts, remove: removeToast } = useToast()

// Kelas warna berdasarkan tipe toast
const toastTypeClasses = {
  success: 'bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-200',
  error: 'bg-red-50 dark:bg-red-900/30 text-red-800 dark:text-red-200',
  warning: 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200',
  info: 'bg-blue-50 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200'
}
</script>

<style scoped>
/* Animasi untuk TransitionGroup */
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(30px);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style> 