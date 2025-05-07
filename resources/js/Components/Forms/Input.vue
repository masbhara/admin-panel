<template>
  <div>
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <div class="relative">
      <div v-if="$slots.prefix" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <slot name="prefix"></slot>
      </div>
      <input
        :id="id"
        :type="type"
        :value="modelValue"
        :class="[
          'block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm',
          error ? 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500' : '',
          $slots.prefix ? 'pl-10' : '',
          $slots.suffix ? 'pr-10' : '',
          className
        ]"
        :disabled="disabled"
        :required="required"
        :placeholder="placeholder"
        @input="$emit('update:modelValue', $event.target.value)"
        v-bind="$attrs"
      >
      <div v-if="$slots.suffix" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
        <slot name="suffix"></slot>
      </div>
    </div>
    <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    <p v-if="hint && !error" class="mt-1 text-sm text-gray-500">{{ hint }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  error: {
    type: String,
    default: ''
  },
  hint: {
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
  placeholder: {
    type: String,
    default: ''
  },
  className: {
    type: String,
    default: ''
  }
})

const id = computed(() => `input-${Math.random().toString(36).substr(2, 9)}`)

defineEmits(['update:modelValue'])
</script>
