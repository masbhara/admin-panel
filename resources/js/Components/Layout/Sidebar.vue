<template>
  <div
    :class="[
      'flex h-full flex-col border-r transition-all duration-300',
      expanded ? 'w-64' : 'w-16',
      'dark:bg-gray-900 bg-white dark:border-gray-800 border-gray-200 shadow-sm'
    ]"
  >
    <!-- Logo -->
    <div class="flex h-16 shrink-0 items-center justify-center border-b border-gray-200 dark:border-gray-800 px-4">
      <img v-if="expanded" :src="logo" alt="Logo" class="h-8 w-auto" />
      <img v-else :src="logoSmall || logo" alt="Logo" class="h-8 w-auto" />
    </div>

    <!-- Sidebar Content -->
    <div class="flex flex-1 flex-col overflow-y-auto py-2">
      <nav class="flex-1 space-y-1 px-2 py-2"> 
        <template v-for="(item, index) in filteredItems" :key="index">
          <!-- Menu Item with Submenu -->
          <div v-if="item.children && item.children.length > 0" class="mb-2">
            <button
              @click="toggleSubmenu(item)"
              :class="[
                'group flex w-full items-center rounded-md py-2 text-sm font-medium transition-all duration-200',
                isActive(item) 
                  ? 'bg-primary-50 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300' 
                  : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white',
                expanded ? 'px-3' : 'justify-center px-3'
              ]"
            >
              <component
                :is="item.icon"
                :class="[
                  'h-5 w-5 shrink-0 transition-colors duration-200',
                  isActive(item) 
                    ? 'text-primary-600 dark:text-primary-400' 
                    : 'text-gray-500 dark:text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300'
                ]"
                aria-hidden="true"
              />
              <span v-if="expanded" class="ml-3 flex-1 truncate">{{ item.label }}</span>
              <ChevronRightIcon
                v-if="expanded"
                :class="[
                  'ml-3 h-4 w-4 shrink-0 transition-transform duration-200',
                  item.expanded ? 'rotate-90 transform' : ''
                ]"
              />
            </button>

            <!-- Submenu dengan transisi yang lebih baik -->
            <div v-if="expanded && item.expanded" 
                 class="mt-1 space-y-1 pl-6 overflow-hidden transition-all duration-200">
              <a
                v-for="(child, childIndex) in item.children"
                :key="childIndex"
                :href="child.to"
                :class="[
                  'group flex items-center rounded-md py-2 pl-4 pr-2 text-sm font-medium transition-all duration-200',
                  isActive(child) 
                    ? 'bg-primary-50 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300' 
                    : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white'
                ]"
                @click.prevent="navigateTo(child)"
              >
                <div
                  :class="[
                    'mr-3 h-1.5 w-1.5 rounded-full',
                    isActive(child) ? 'bg-primary-600 dark:bg-primary-400' : 'bg-gray-400 dark:bg-gray-600'
                  ]"
                ></div>
                <span class="truncate">{{ child.label }}</span>
              </a>
            </div>
          </div>

          <!-- Regular Menu Item dengan efek hover yang ditingkatkan -->
          <a
            v-else
            :href="item.to"
            :class="[
              'group flex items-center rounded-md py-2 text-sm font-medium transition-all duration-200 mb-2',
              isActive(item) 
                ? 'bg-primary-50 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300' 
                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white',
              expanded ? 'px-3' : 'justify-center px-3'
            ]"
            @click.prevent="navigateTo(item)"
          >
            <component
              :is="item.icon"
              :class="[
                'h-5 w-5 shrink-0 transition-colors duration-200',
                isActive(item) 
                  ? 'text-primary-600 dark:text-primary-400' 
                  : 'text-gray-500 dark:text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300'
              ]"
              aria-hidden="true"
            />
            <span v-if="expanded" class="ml-3 truncate">{{ item.label }}</span>
          </a>
        </template>
      </nav>
    </div>

    <!-- Toggle Button dengan styling yang lebih baik -->
    <div class="shrink-0 border-t border-gray-200 dark:border-gray-800 p-2">
      <button
        type="button"
        class="group flex w-full items-center rounded-md py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white transition-all duration-200"
        :class="expanded ? 'px-3' : 'justify-center px-3'"
        @click="toggleSidebar"
      >
        <ChevronLeftIcon
          v-if="expanded"
          class="h-5 w-5 shrink-0 text-gray-500 dark:text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300"
          aria-hidden="true"
        />
        <ChevronRightIcon
          v-else
          class="h-5 w-5 shrink-0 text-gray-500 dark:text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300"
          aria-hidden="true"
        />
        <span v-if="expanded" class="ml-3">{{ expanded ? 'Tutup Menu' : 'Buka Menu' }}</span>
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

// Fungsi untuk menutup semua submenu
const closeAllSubmenus = () => {
  menuItems.value = menuItems.value.map(item => ({
    ...item,
    expanded: false
  }))
}

// Panggil closeAllSubmenus saat komponen dimount
onMounted(() => {
  const savedExpanded = localStorage.getItem('sidebarExpanded')
  if (savedExpanded !== null) {
    expanded.value = savedExpanded === 'true'
  }
  
  // Pastikan semua submenu tertutup saat komponen dimount
  closeAllSubmenus()
})

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

// Toggle submenu dengan animasi yang lebih baik
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

// Watch for route changes
watch(
  () => route.path,
  () => {
    // Tidak perlu memanggil expandActiveParent() lagi
    // Biarkan user yang mengontrol submenu secara manual
  }
)

// Watch for items prop changes
watch(
  () => props.items,
  (newItems) => {
    menuItems.value = newItems.map(item => ({
      ...item,
      expanded: false // Selalu set expanded ke false untuk item baru
    }))
  }
)
</script>
