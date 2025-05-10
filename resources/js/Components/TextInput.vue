<template>
  <input
    class="border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm transition-colors duration-200
    bg-white text-gray-900 placeholder-gray-400
    dark:bg-background-secondary dark:text-text-primary dark:placeholder-text-secondary/60 dark:border-border-dark"
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    ref="input"
  >
</template>

<script setup>
import { onMounted, ref } from 'vue'

defineProps(['modelValue'])
defineEmits(['update:modelValue'])

const input = ref(null)

defineExpose({ focus: () => input.value.focus() })

onMounted(() => {
  if (input.value.hasAttribute('autofocus')) {
    input.value.focus()
  }
})
</script>
