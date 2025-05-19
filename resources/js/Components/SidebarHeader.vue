<template>
  <div class="flex h-16 shrink-0 items-center gap-x-3">
    <Link :href="route('admin.dashboard')" class="flex items-center gap-x-3">
      <img 
        v-if="settingsToUse?.logo" 
        :src="settingsToUse.logo" 
        :alt="settingsToUse?.site_title || 'Admin Panel'" 
        class="h-10 w-auto object-contain bg-white rounded-md p-1"
      />
      <div v-else class="flex h-10 w-10 items-center justify-center rounded-md bg-primary-700">
        <span class="text-lg font-bold text-white">{{ getInitials(settingsToUse?.site_title || 'AP') }}</span>
      </div>
      <span class="text-lg font-semibold text-white">{{ settingsToUse?.site_title || 'Admin Panel' }}</span>
    </Link>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const props = defineProps({
  settings: {
    type: Object,
    default: null
  }
})

const page = usePage()
const settingsToUse = computed(() => {
  // Prioritaskan settings dari props jika ada
  const fromProps = props.settings
  const fromGlobal = page.props.settings || {}
  const result = fromProps || fromGlobal
  
  return result
})

const getInitials = (text) => {
  return text
    .split(' ')
    .map(word => word[0])
    .join('')
    .substring(0, 2)
    .toUpperCase()
}
</script> 