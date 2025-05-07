<template>
  <input
    class="border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm"
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
