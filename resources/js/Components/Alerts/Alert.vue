<template>
  <div
    v-if="show"
    :class="[
      'rounded-md p-4',
      type === 'success' ? 'bg-green-50 text-green-800' : '',
      type === 'error' ? 'bg-red-50 text-red-800' : '',
      type === 'warning' ? 'bg-yellow-50 text-yellow-800' : '',
      type === 'info' ? 'bg-blue-50 text-blue-800' : '',
      className
    ]"
  >
    <div class="flex">
      <div class="flex-shrink-0">
        <component
          :is="icon"
          :class="[
            'h-5 w-5',
            type === 'success' ? 'text-green-400' : '',
            type === 'error' ? 'text-red-400' : '',
            type === 'warning' ? 'text-yellow-400' : '',
            type === 'info' ? 'text-blue-400' : ''
          ]"
        />
      </div>
      <div class="ml-3">
        <h3 v-if="title" class="text-sm font-medium">{{ title }}</h3>
        <div class="text-sm" :class="{ 'mt-2': title }">
          <slot>{{ message }}</slot>
        </div>
      </div>
      <div v-if="dismissible" class="ml-auto pl-3">
        <div class="-mx-1.5 -my-1.5">
          <button
            type="button"
            :class="[
              'inline-flex rounded-md p-1.5',
              type === 'success' ? 'bg-green-50 text-green-500 hover:bg-green-100' : '',
              type === 'error' ? 'bg-red-50 text-red-500 hover:bg-red-100' : '',
              type === 'warning' ? 'bg-yellow-50 text-yellow-500 hover:bg-yellow-100' : '',
              type === 'info' ? 'bg-blue-50 text-blue-500 hover:bg-blue-100' : ''
            ]"
            @click="dismiss"
          >
            <span class="sr-only">Dismiss</span>
            <XMarkIcon class="h-5 w-5" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import {
  CheckCircleIcon,
  ExclamationCircleIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  },
  title: {
    type: String,
    default: ''
  },
  message: {
    type: String,
    default: ''
  },
  dismissible: {
    type: Boolean,
    default: true
  },
  autoClose: {
    type: Number,
    default: 0
  },
  className: {
    type: String,
    default: ''
  }
})

const show = ref(true)

const icon = computed(() => {
  switch (props.type) {
    case 'success':
      return CheckCircleIcon
    case 'error':
      return ExclamationCircleIcon
    case 'warning':
      return ExclamationTriangleIcon
    default:
      return InformationCircleIcon
  }
})

const dismiss = () => {
  show.value = false
}

if (props.autoClose > 0) {
  setTimeout(dismiss, props.autoClose)
}

defineExpose({ dismiss })
</script>
