<template>
  <div v-if="links.length > 3">
    <div class="flex flex-wrap -mb-1">
      <template v-for="(link, key) in links" :key="key">
        <div
          v-if="link.url === null"
          class="mr-1 mb-1 px-4 py-2 text-sm leading-4 text-gray-400 border rounded"
          v-html="link.label"
        />
        <Link
          v-else
          :href="link.url"
          class="mr-1 mb-1 px-4 py-2 text-sm leading-4 border rounded hover:bg-primary-50 focus:border-primary-500 focus:text-primary-700"
          :class="{ 'bg-primary-50': link.active }"
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
