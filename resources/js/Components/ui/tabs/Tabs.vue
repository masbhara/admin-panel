<template>
  <div class="w-full">
    <slot></slot>
  </div>
</template>

<script setup>
import { provide, ref } from 'vue'

const props = defineProps({
  defaultValue: {
    type: String,
    required: true
  }
})

const activeTab = ref(props.defaultValue)

provide('activeTab', activeTab)
provide('setActiveTab', (value) => {
  activeTab.value = value
})
</script>
