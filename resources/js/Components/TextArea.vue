<template>
  <div>
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <div class="relative">
      <textarea
        :id="id"
        :value="modelValue"
        :class="[
          'block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-gray-700 dark:text-white',
          error ? 'border-red-300 text-red-900 dark:border-red-500 dark:text-red-300 placeholder-red-300 dark:placeholder-red-400 focus:border-red-500 focus:ring-red-500' : '',
          autoResize ? 'resize-none' : 'resize-y',
          className
        ]"
        :rows="rows"
        :maxlength="maxLength"
        :disabled="disabled"
        :required="required"
        :placeholder="placeholder"
        @input="handleInput"
        v-bind="$attrs"
      ></textarea>
      <div v-if="showCount" class="absolute bottom-2 right-2">
        <span class="text-xs text-gray-500 dark:text-gray-400">
          {{ currentLength }}/{{ maxLength }}
        </span>
      </div>
    </div>
    <p v-if="error" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ error }}</p>
    <p v-if="hint && !error" class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ hint }}</p>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, nextTick } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  rows: {
    type: Number,
    default: 3
  },
  maxLength: {
    type: Number,
    default: 500
  },
  showCount: {
    type: Boolean,
    default: false
  },
  autoResize: {
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
  placeholder: {
    type: String,
    default: ''
  },
  className: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue'])
const textareaRef = ref(null)
const currentLength = computed(() => props.modelValue?.length || 0)
const id = computed(() => `textarea-${Math.random().toString(36).substr(2, 9)}`)

const handleInput = (event) => {
  const value = event.target.value
  emit('update:modelValue', value)
  
  if (props.autoResize) {
    nextTick(() => {
      event.target.style.height = 'auto'
      event.target.style.height = `${event.target.scrollHeight}px`
    })
  }
}

onMounted(() => {
  if (props.autoResize && textareaRef.value) {
    textareaRef.value.style.height = 'auto'
    textareaRef.value.style.height = `${textareaRef.value.scrollHeight}px`
  }
})
</script>
