<template>
  <div v-if="links.length > 3">
    <div class="flex flex-wrap -mb-1 justify-center">
      <template v-for="(link, key) in links" :key="key">
        <div
          v-if="link.url === null"
          class="mr-1 mb-1 px-4 py-2 text-sm leading-4 text-gray-400 dark:text-gray-500 border border-gray-200 dark:border-gray-700 rounded-md"
          v-html="link.label"
        />
        <Link
          v-else
          :href="link.url"
          class="mr-1 mb-1 px-4 py-2 text-sm leading-4 border rounded-md transition-colors duration-150"
          :class="[
            link.active 
              ? 'bg-indigo-50 dark:bg-indigo-900/30 border-indigo-500 dark:border-indigo-500 text-indigo-600 dark:text-indigo-400' 
              : 'border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'
          ]"
          v-html="link.label"
        />
      </template>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  links: {
    type: Array,
    required: true,
  },
})
</script>
