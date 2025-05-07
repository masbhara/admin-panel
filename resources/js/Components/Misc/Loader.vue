<template>
  <div
    :class="[
      'flex items-center justify-center',
      fullScreen ? 'fixed inset-0 bg-white/80 z-50' : ''
    ]"
  >
    <div
      :class="[
        'inline-block animate-spin rounded-full border-2 border-solid border-current border-r-transparent motion-reduce:animate-[spin_1.5s_linear_infinite]',
        size === 'sm' ? 'h-4 w-4' : '',
        size === 'md' ? 'h-8 w-8' : '',
        size === 'lg' ? 'h-12 w-12' : '',
        color === 'primary' ? 'text-primary-600' : '',
        color === 'white' ? 'text-white' : '',
        className
      ]"
      role="status"
    >
      <span class="sr-only">Loading...</span>
    </div>
    <span v-if="text" :class="['ml-2', textClass]">{{ text }}</span>
  </div>
</template>

<script setup>
defineProps({
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  color: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'white'].includes(value)
  },
  text: {
    type: String,
    default: ''
  },
  textClass: {
    type: String,
    default: 'text-gray-500'
  },
  fullScreen: {
    type: Boolean,
    default: false
  },
  className: {
    type: String,
    default: ''
  }
})
</script>
