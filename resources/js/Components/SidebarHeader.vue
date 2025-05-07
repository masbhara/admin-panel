<template>
  <div class="flex h-16 shrink-0 items-center gap-x-3">
    <Link :href="route('admin.dashboard')" class="flex items-center gap-x-3">
      <img 
        v-if="settings?.logo" 
        :src="settings.logo" 
        :alt="settings?.site_title || 'Admin Panel'" 
        class="h-10 w-auto object-contain bg-white rounded-md p-1"
      />
      <div v-else class="flex h-10 w-10 items-center justify-center rounded-md bg-primary-700">
        <span class="text-lg font-bold text-white">{{ getInitials(settings?.site_title || 'AP') }}</span>
      </div>
      <span class="text-lg font-semibold text-white">{{ settings?.site_title || 'Admin Panel' }}</span>
    </Link>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const settings = computed(() => page.props.settings || {})

const getInitials = (text) => {
  return text
    .split(' ')
    .map(word => word[0])
    .join('')
    .substring(0, 2)
    .toUpperCase()
}
</script> 