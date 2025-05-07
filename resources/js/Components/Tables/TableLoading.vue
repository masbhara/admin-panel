<template>
  <div
    :class="[
      'animate-pulse space-y-4',
      fullscreen ? 'fixed inset-0 z-50 flex items-center justify-center bg-white/80' : ''
    ]"
  >
    <div v-if="showHeader" class="space-y-3">
      <div class="h-4 bg-gray-200 rounded w-1/4"></div>
      <div class="h-4 bg-gray-200 rounded w-1/2"></div>
    </div>

    <div class="border border-gray-200 rounded-lg overflow-hidden">
      <!-- Header skeleton -->
      <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
        <div class="flex justify-between items-center">
          <div class="h-4 bg-gray-200 rounded w-1/4"></div>
          <div class="h-4 bg-gray-200 rounded w-1/6"></div>
        </div>
      </div>

      <!-- Rows skeleton -->
      <div class="divide-y divide-gray-200">
        <div
          v-for="i in rows"
          :key="i"
          class="px-4 py-4 sm:px-6"
        >
          <div class="flex justify-between items-center">
            <div class="space-y-3" :class="fullContent ? 'w-full' : 'w-1/2'">
              <div class="h-4 bg-gray-200 rounded" :class="fullContent ? 'w-3/4' : 'w-full'"></div>
              <div v-if="fullContent" class="h-4 bg-gray-200 rounded w-1/2"></div>
            </div>
            <div v-if="!fullContent" class="h-4 bg-gray-200 rounded w-1/4"></div>
          </div>
        </div>
      </div>

      <!-- Footer skeleton -->
      <div v-if="showFooter" class="bg-gray-50 px-4 py-3 border-t border-gray-200">
        <div class="flex justify-between items-center">
          <div class="h-4 bg-gray-200 rounded w-1/4"></div>
          <div class="h-4 bg-gray-200 rounded w-1/3"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  rows: {
    type: Number,
    default: 5
  },
  showHeader: {
    type: Boolean,
    default: false
  },
  showFooter: {
    type: Boolean,
    default: true
  },
  fullContent: {
    type: Boolean,
    default: false
  },
  fullscreen: {
    type: Boolean,
    default: false
  }
})
</script>
