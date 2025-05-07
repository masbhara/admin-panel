<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-2">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <div class="space-y-4" :class="inline ? 'space-y-0 space-x-6' : ''">
      <div
        v-for="option in options"
        :key="option[valueKey]"
        class="flex items-center"
        :class="inline ? 'inline-flex' : ''"
      >
        <input
          :id="`${id}-${option[valueKey]}`"
          :name="name || id"
          type="radio"
          :checked="modelValue === option[valueKey]"
          :value="option[valueKey]"
          :class="[
            'h-4 w-4 border-gray-300 text-primary-600 focus:ring-primary-500',
            error ? 'border-red-300' : '',
            className
          ]"
          :disabled="disabled"
          :required="required"
          @change="$emit('update:modelValue', option[valueKey])"
        >
        <label
          :for="`${id}-${option[valueKey]}`"
          :class="['ml-3 block text-sm', disabled ? 'text-gray-400' : 'text-gray-700']"
        >
          {{ option[labelKey] }}
          <p v-if="option.description" class="text-gray-500">{{ option.description }}</p>
        </label>
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
  options: {
    type: Array,
    required: true
  },
  valueKey: {
    type: String,
    default: 'value'
  },
  labelKey: {
    type: String,
    default: 'label'
  },
  name: {
    type: String,
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  inline: {
    type: Boolean,
    default: false
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
  className: {
    type: String,
    default: ''
  }
})

const id = computed(() => `radio-${Math.random().toString(36).substr(2, 9)}`)

defineEmits(['update:modelValue'])
</script>
