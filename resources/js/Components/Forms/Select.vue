<template>
  <div>
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <div class="relative">
      <select
        :id="id"
        :value="modelValue"
        :class="[
          'block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm',
          error ? 'border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500' : '',
          className
        ]"
        :disabled="disabled"
        :required="required"
        @change="$emit('update:modelValue', $event.target.value)"
        v-bind="$attrs"
      >
        <option v-if="placeholder" value="" disabled selected>{{ placeholder }}</option>
        <option
          v-for="option in options"
          :key="option[valueKey]"
          :value="option[valueKey]"
        >
          {{ option[labelKey] }}
        </option>
      </select>
      <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
        <ChevronUpDownIcon class="h-4 w-4 text-gray-400" />
      </div>
    </div>
    <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    <p v-if="hint && !error" class="mt-1 text-sm text-gray-500">{{ hint }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { ChevronUpDownIcon } from '@heroicons/vue/24/outline'

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
  label: {
    type: String,
    default: ''
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
    default: 'Pilih opsi'
  },
  className: {
    type: String,
    default: ''
  }
})

const id = computed(() => `select-${Math.random().toString(36).substr(2, 9)}`)

defineEmits(['update:modelValue'])
</script>
