<template>
  <div :class="[
    'bg-white rounded-lg shadow',
    padding ? 'p-6' : '',
    className
  ]">
    <div v-if="$slots.header" :class="['flex items-center justify-between', padding ? '-mt-6 -mx-6 px-6 py-4' : 'p-4', 'border-b']">
      <slot name="header"></slot>
    </div>
    <div :class="{ 'p-4': !padding && !$slots.header }">
      <slot></slot>
    </div>
    <div v-if="$slots.footer" :class="['border-t', padding ? '-mb-6 -mx-6 px-6 py-4' : 'p-4']">
      <slot name="footer"></slot>
    </div>
  </div>
</template>

<script setup>
defineProps({
  padding: {
    type: Boolean,
    default: true
  },
  className: {
    type: String,
    default: ''
  }
})
</script>
