<template>
  <div
    :class="[
      'flex h-full flex-col border-r border-gray-200 bg-white transition-all duration-300',
      expanded ? 'w-64' : 'w-16'
    ]"
  >
    <!-- Logo -->
    <div class="flex h-16 shrink-0 items-center border-b border-gray-200 px-4">
      <img v-if="expanded" :src="logo" alt="Logo" class="h-8 w-auto" />
      <img v-else :src="logoSmall || logo" alt="Logo" class="h-8 w-auto" />
    </div>

    <!-- Sidebar Content -->
    <div class="flex flex-1 flex-col overflow-y-auto">
      <nav class="flex-1 space-y-1 px-2 py-4"> 
        <template v-for="(item, index) in filteredItems" :key="index">
          <!-- Menu Item with Submenu -->
          <div v-if="item.children && item.children.length > 0">
            <button
              @click="toggleSubmenu(item)"
              :class="[
                'group flex w-full items-center rounded-md py-2 text-sm font-medium',
                isActive(item) ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                expanded ? 'px-2' : 'justify-center px-3'
              ]"
            >
              <component
                :is="item.icon"
                :class="[
                  'h-5 w-5 shrink-0',
                  isActive(item) ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500'
                ]"
                aria-hidden="true"
              />
              <span v-if="expanded" class="ml-3 flex-1">{{ item.label }}</span>
              <ChevronRightIcon
                v-if="expanded"
                :class="[
                  'ml-3 h-4 w-4 shrink-0 transition-transform',
                  item.expanded ? 'rotate-90 transform' : ''
                ]"
              />
            </button>

            <!-- Submenu -->
            <div v-if="expanded && item.expanded" class="mt-1 space-y-1 pl-6">
              <a
                v-for="(child, childIndex) in item.children"
                :key="childIndex"
                :href="child.to"
                :class="[
                  'group flex items-center rounded-md py-2 pl-4 pr-2 text-sm font-medium',
                  isActive(child) ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                ]"
                @click.prevent="navigateTo(child)"
              >
                <div
                  :class="[
                    'mr-3 h-1.5 w-1.5 rounded-full',
                    isActive(child) ? 'bg-primary-500' : 'bg-gray-300'
                  ]"
                ></div>
                {{ child.label }}
              </a>
            </div>
          </div>

          <!-- Regular Menu Item -->
          <a
            v-else
            :href="item.to"
            :class="[
              'group flex items-center rounded-md py-2 text-sm font-medium',
              isActive(item) ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
              expanded ? 'px-2' : 'justify-center px-3'
            ]"
            @click.prevent="navigateTo(item)"
          >
            <component
              :is="item.icon"
              :class="[
                'h-5 w-5 shrink-0',
                isActive(item) ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500'
              ]"
              aria-hidden="true"
            />
            <span v-if="expanded" class="ml-3">{{ item.label }}</span>
          </a>
        </template>
      </nav>
    </div>

    <!-- Toggle Button -->
    <div class="shrink-0 border-t border-gray-200 p-2">
      <button
        type="button"
        class="group flex w-full items-center rounded-md py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900"
        :class="expanded ? 'px-2' : 'justify-center px-3'"
        @click="toggleSidebar"
      >
        <ChevronLeftIcon
          v-if="expanded"
          class="h-5 w-5 shrink-0 text-gray-400 group-hover:text-gray-500"
          aria-hidden="true"
        />
        <ChevronRightIcon
          v-else
          class="h-5 w-5 shrink-0 text-gray-400 group-hover:text-gray-500"
          aria-hidden="true"
        />
        <span v-if="expanded" class="ml-3">Collapse</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
  ChevronRightIcon,
  ChevronLeftIcon,
  HomeIcon,
  UsersIcon,
  CogIcon,
  DocumentTextIcon,
  ChartBarIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  items: {
    type: Array,
    default: () => []
  },
  logo: {
    type: String,
    default: '/img/logo.svg'
  },
  logoSmall: {
    type: String,
    default: ''
  },
  defaultExpanded: {
    type: Boolean,
    default: true
  },
  userPermissions: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['navigate', 'toggle'])
const route = useRoute()
const router = useRouter()

// State
const expanded = ref(props.defaultExpanded)
const menuItems = ref(props.items.map(item => ({
  ...item,
  expanded: false
})))

// Filter items based on permissions
const filteredItems = computed(() => {
  return menuItems.value.filter(item => {
    // If no permission specified, show the item
    if (!item.permission) return true
    
    // Check if user has the required permission
    return props.userPermissions.includes(item.permission)
  }).map(item => {
    // Also filter children if any
    if (item.children && item.children.length > 0) {
      return {
        ...item,
        children: item.children.filter(child => {
          if (!child.permission) return true
          return props.userPermissions.includes(child.permission)
        })
      }
    }
    return item
  })
})

// Check if a menu item is active
const isActive = (item) => {
  if (!item.to) return false
  
  // Check if the current route matches the item's route
  if (typeof item.to === 'string') {
    return route.path === item.to || route.path.startsWith(`${item.to}/`)
  }
  
  // For objects (like { name: 'route-name' })
  if (item.to.name) {
    return route.name === item.to.name
  }
  
  return false
}

// Toggle submenu
const toggleSubmenu = (item) => {
  menuItems.value = menuItems.value.map(menuItem => {
    if (menuItem === item) {
      return { ...menuItem, expanded: !menuItem.expanded }
    }
    return menuItem
  })
}

// Toggle sidebar expanded state
const toggleSidebar = () => {
  expanded.value = !expanded.value
  emit('toggle', expanded.value)
  
  // Save preference to localStorage
  localStorage.setItem('sidebarExpanded', expanded.value.toString())
}

// Navigate to route
const navigateTo = (item) => {
  if (!item.to) return
  
  emit('navigate', item)
  
  // Use router to navigate
  if (typeof item.to === 'string') {
    router.push(item.to)
  } else {
    router.push(item.to)
  }
}

// Expand parent menu of active item
const expandActiveParent = () => {
  const currentPath = route.path
  
  menuItems.value = menuItems.value.map(item => {
    if (item.children && item.children.some(child => 
      (typeof child.to === 'string' && (child.to === currentPath || currentPath.startsWith(`${child.to}/`))) ||
      (child.to && child.to.name && route.name === child.to.name)
    )) {
      return { ...item, expanded: true }
    }
    return item
  })
}

// Load expanded state from localStorage
onMounted(() => {
  const savedExpanded = localStorage.getItem('sidebarExpanded')
  if (savedExpanded !== null) {
    expanded.value = savedExpanded === 'true'
  }
  
  expandActiveParent()
})

// Watch for route changes to update active state
watch(
  () => route.path,
  () => {
    expandActiveParent()
  }
)

// Watch for items prop changes
watch(
  () => props.items,
  (newItems) => {
    menuItems.value = newItems.map(item => ({
      ...item,
      expanded: menuItems.value.find(i => i.label === item.label)?.expanded || false
    }))
    expandActiveParent()
  }
)
</script>
