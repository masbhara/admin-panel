<template>
  <button
    :class="[
      'inline-flex items-center justify-center whitespace-nowrap rounded-md px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50',
      activeTab === value ? 'bg-background text-foreground shadow-sm' : 'hover:bg-muted/50'
    ]"
    @click="setActiveTab(value)"
  >
    <slot></slot>
  </button>
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
const setActiveTab = inject('setActiveTab')
</script>
