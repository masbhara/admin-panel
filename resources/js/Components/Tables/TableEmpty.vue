<template>
  <div class="text-center py-6">
    <component
      :is="icon"
      class="mx-auto h-12 w-12 text-gray-400"
      aria-hidden="true"
    />
    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ title }}</h3>
    <p class="mt-1 text-sm text-gray-500">{{ description }}</p>
    <div v-if="$slots.action" class="mt-6">
      <slot name="action"></slot>
    </div>
  </div>
</template>

<script setup>
import {
  InboxIcon,
  ExclamationTriangleIcon,
  DocumentIcon,
  PhotoIcon,
  UserGroupIcon
} from '@heroicons/vue/24/outline'

const icons = {
  inbox: InboxIcon,
  warning: ExclamationTriangleIcon,
  document: DocumentIcon,
  photo: PhotoIcon,
  users: UserGroupIcon
}

const props = defineProps({
  type: {
    type: String,
    default: 'inbox',
    validator: (value) => ['inbox', 'warning', 'document', 'photo', 'users'].includes(value)
  },
  title: {
    type: String,
    default: 'Tidak ada data'
  },
  description: {
    type: String,
    default: 'Belum ada data yang tersedia.'
  }
})

const icon = computed(() => icons[props.type])
</script>
