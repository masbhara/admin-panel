<template>
  <input
    type="file"
    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
    @change="handleFileChange"
    ref="input"
  >
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  modelValue: File,
})

const emit = defineEmits(['update:modelValue'])

const input = ref(null)

const handleFileChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    emit('update:modelValue', file)
  }
}

defineExpose({ input })
</script>
