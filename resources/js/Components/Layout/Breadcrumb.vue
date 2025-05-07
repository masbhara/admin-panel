<template>
  <nav class="flex" aria-label="Breadcrumb">
    <ol role="list" class="flex items-center space-x-2">
      <li>
        <div>
          <a
            :href="homeLink"
            class="text-gray-400 hover:text-gray-500"
            @click.prevent="navigateTo(homeLink)"
          >
            <HomeIcon class="h-5 w-5 flex-shrink-0" aria-hidden="true" />
            <span class="sr-only">{{ homeLabel }}</span>
          </a>
        </div>
      </li>
      <li
        v-for="(item, index) in items"
        :key="index"
      >
        <div class="flex items-center">
          <ChevronRightIcon class="h-5 w-5 flex-shrink-0 text-gray-400" aria-hidden="true" />
          <a
            v-if="index < items.length - 1 || !current"
            :href="item.to"
            class="ml-2 text-sm font-medium text-gray-500 hover:text-gray-700"
            @click.prevent="navigateTo(item.to)"
          >
            {{ item.label }}
          </a>
          <span
            v-else
            class="ml-2 text-sm font-medium text-gray-700"
            aria-current="page"
          >
            {{ item.label }}
          </span>
        </div>
      </li>
    </ol>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { HomeIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  items: {
    type: Array,
    default: () => []
  },
  homeLink: {
    type: String,
    default: '/'
  },
  homeLabel: {
    type: String,
    default: 'Beranda'
  },
  current: {
    type: Boolean,
    default: true
  },
  autoGenerate: {
    type: Boolean,
    default: false
  },
  routeNameMapping: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['navigate'])
const router = useRouter()
const route = useRoute()

// Methods
const navigateTo = (path) => {
  if (!path) return
  
  emit('navigate', path)
  router.push(path)
}

// Auto-generate breadcrumb items based on current route
const autoBreadcrumbItems = computed(() => {
  if (!props.autoGenerate) return props.items
  
  const paths = route.path.split('/').filter(Boolean)
  const items = []
  let currentPath = ''
  
  paths.forEach((path, index) => {
    currentPath += `/${path}`
    
    // Get the route name from mapping or capitalize the path
    const routeName = props.routeNameMapping[currentPath] || 
      path.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')
    
    items.push({
      label: routeName,
      to: currentPath
    })
  })
  
  return items
})

// Final breadcrumb items
const breadcrumbItems = computed(() => {
  return props.autoGenerate ? autoBreadcrumbItems.value : props.items
})
</script>
