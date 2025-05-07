<template>
  <div class="flex items-center space-x-4">
    <button
      v-for="action in actions"
      :key="action.name"
      :class="[
        'inline-flex items-center px-3 py-2 text-sm font-medium rounded-md',
        action.primary
          ? 'bg-green-600 text-white hover:bg-green-700'
          : 'text-gray-700 bg-white hover:bg-gray-50'
      ]"
      @click="action.onClick"
    >
      <component
        :is="action.icon"
        class="h-5 w-5 mr-2"
        :class="action.primary ? 'text-white' : 'text-gray-400'"
      />
      {{ action.name }}
    </button>
  </div>
</template>

<script setup>
import {
  BellAlertIcon
} from '@heroicons/vue/24/outline'

const actions = [
  {
    name: 'Alertes',
    icon: BellAlertIcon,
    primary: false,
    onClick: () => {}
  }
]
</script>
