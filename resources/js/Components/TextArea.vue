<template>
  <textarea
    class="border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm"
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    ref="textarea"
  ></textarea>
</template>

<script setup>
import { onMounted, ref } from 'vue'

defineProps(['modelValue'])
defineEmits(['update:modelValue'])

const textarea = ref(null)

defineExpose({ focus: () => textarea.value.focus() })

onMounted(() => {
  if (textarea.value.hasAttribute('autofocus')) {
    textarea.value.focus()
  }
})
</script>
