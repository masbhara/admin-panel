<template>
  <div v-show="activeTab === value" class="mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
    <slot></slot>
  </div>
</template>

<script setup>
import { inject } from 'vue'

defineProps({
  value: {
    type: String,
    required: true
  }
})

const activeTab = inject('activeTab')
</script>
