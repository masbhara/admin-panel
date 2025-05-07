<template>
  <button
    :type="type"
    :class="[
      'inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md font-semibold text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150',
      disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
      size === 'sm' ? 'px-2 py-1 text-xs' : '',
      size === 'lg' ? 'px-6 py-3 text-base' : '',
      variant === 'solid' ? 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500' : '',
      variant === 'outline' ? 'border-primary-600 text-primary-600 hover:bg-primary-50' : '',
      variant === 'ghost' ? 'bg-transparent text-primary-600 hover:bg-primary-50' : '',
      className
    ]"
    :disabled="disabled || loading"
    @click="$emit('click', $event)"
  >
    <span v-if="loading" class="mr-2">
      <svg class="animate-spin h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </span>
    <slot v-if="!loading" name="icon"></slot>
    <span :class="{ 'ml-2': $slots.icon }"><slot>{{ text }}</slot></span>
  </button>
</template>

<script setup>
defineProps({
  type: {
    type: String,
    default: 'button'
  },
  variant: {
    type: String,
    default: 'solid',
    validator: (value) => ['solid', 'outline', 'ghost'].includes(value)
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  text: {
    type: String,
    default: ''
  },
  loading: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  className: {
    type: String,
    default: ''
  }
})

defineEmits(['click'])
</script>
