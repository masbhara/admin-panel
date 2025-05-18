<template>
  <div class="relative flex items-start">
    <div class="flex h-5 items-center">
      <input
        :id="id"
        :checked="modelValue"
        type="checkbox"
        :class="[
          'h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500 dark:bg-gray-700 dark:checked:bg-primary-500',
          error ? 'border-red-300 dark:border-red-500' : '',
          className
        ]"
        :disabled="disabled"
        :required="required"
        @change="$emit('update:modelValue', $event.target.checked)"
        v-bind="$attrs"
      >
    </div>
    <div class="ml-3 text-sm">
      <label v-if="label" :for="id" :class="['font-medium', disabled ? 'text-gray-400' : 'text-gray-700 dark:text-gray-300']">
        {{ label }}
        <span v-if="required" class="text-red-500">*</span>
      </label>
      <p v-if="description" :class="['text-gray-500 dark:text-gray-400', { 'mt-1': label }]">{{ description }}</p>
    </div>
  </div>
  <p v-if="error" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ error }}</p>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  label: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  },
  error: {
    type: String,
    default: ''
  },
  required: {
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

const id = computed(() => `checkbox-${Math.random().toString(36).substr(2, 9)}`)

defineEmits(['update:modelValue'])
</script>
