<template>
  <select
    :value="modelValue"
    @change="$emit('update:modelValue', $event.target.value)"
    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
  >
    <slot></slot>
  </select>
</template>

<script setup>
defineProps({
  modelValue: {
    type: [String, Number],
    required: true,
  },
})

defineEmits(['update:modelValue'])
</script> 