<template>
  <div 
    v-if="!imageUrl"
    class="h-10 w-10 rounded-full flex items-center justify-center text-white font-medium"
    :class="bgColorClass"
  >
    {{ initials }}
  </div>
  <img
    v-else
    :src="imageUrl"
    :alt="name"
    class="h-10 w-10 rounded-full object-cover border border-gray-200 dark:border-gray-700"
  />
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  name: {
    type: String,
    required: true
  },
  imageUrl: {
    type: String,
    default: null
  }
})

const initials = computed(() => {
  if (!props.name) return '??'
  
  return props.name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .substring(0, 2)
})

const bgColorClass = computed(() => {
  const colors = [
    'bg-primary-500',
    'bg-pink-500',
    'bg-purple-500',
    'bg-indigo-500',
    'bg-blue-500',
    'bg-green-500',
    'bg-yellow-500',
    'bg-red-500'
  ]
  
  if (!props.name) return colors[0]
  
  const index = props.name
    .split('')
    .reduce((acc, char) => acc + char.charCodeAt(0), 0) % colors.length
  
  return colors[index]
})
</script> 