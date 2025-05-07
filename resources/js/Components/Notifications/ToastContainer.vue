<template>
  <Toast
    ref="toastRef"
    :position="position"
    :max-toasts="maxToasts"
    @action="handleAction"
    @remove="handleRemove"
  />
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import Toast from './Toast.vue'

const props = defineProps({
  position: {
    type: String,
    default: 'top-right'
  },
  maxToasts: {
    type: Number,
    default: 5
  }
})

const emit = defineEmits(['action', 'remove'])

// Refs
const toastRef = ref(null)

// Methods
const showToast = (options) => {
  if (!toastRef.value) return null
  return toastRef.value.addToast(options)
}

const removeToast = (id) => {
  if (!toastRef.value) return
  toastRef.value.removeToast(id)
}

const handleAction = (toast) => {
  emit('action', toast)
}

const handleRemove = (toast) => {
  emit('remove', toast)
}

// Convenience methods
const showSuccessToast = (message, options = {}) => {
  return showToast({
    type: 'success',
    message,
    ...options
  })
}

const showErrorToast = (message, options = {}) => {
  return showToast({
    type: 'error',
    message,
    ...options
  })
}

const showWarningToast = (message, options = {}) => {
  return showToast({
    type: 'warning',
    message,
    ...options
  })
}

const showInfoToast = (message, options = {}) => {
  return showToast({
    type: 'info',
    message,
    ...options
  })
}

// Expose methods to parent component
defineExpose({
  showToast,
  removeToast,
  showSuccessToast,
  showErrorToast,
  showWarningToast,
  showInfoToast
})
</script>
