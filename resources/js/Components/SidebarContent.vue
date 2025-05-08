<template>
  <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-primary-600 px-6 pb-4">
    <!-- Logo dan Judul Website -->
    <SidebarHeader />
    <nav class="flex flex-1 flex-col">
      <ul role="list" class="flex flex-1 flex-col gap-y-7"> 
        <!-- Primary Navigation -->
        <li>
          <ul role="list" class="-mx-2 space-y-1">
            <li v-for="item in navigation" :key="item.name">
              <Link
                v-if="!item.children"
                :href="item.href"
                :class="[
                  route().current(item.active)
                    ? 'bg-primary-700 text-white'
                    : 'text-primary-200 hover:text-white hover:bg-primary-700',
                  'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold'
                ]"
              >
                <component
                  :is="item.icon"
                  :class="[
                    route().current(item.active)
                      ? 'text-white'
                      : 'text-primary-200 group-hover:text-white',
                    'h-6 w-6 shrink-0'
                  ]"
                  aria-hidden="true"
                />
                {{ item.name }}
              </Link>

              <!-- Dropdown Menu -->
              <div v-else>
                <button
                  @click="item.isOpen = !item.isOpen"
                  :class="[
                    isAnyChildActive(item.children)
                      ? 'bg-primary-700 text-white'
                      : 'text-primary-200 hover:text-white hover:bg-primary-700',
                    'group flex w-full items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold'
                  ]"
                >
                  <component
                    :is="item.icon"
                    :class="[
                      isAnyChildActive(item.children)
                        ? 'text-white'
                        : 'text-primary-200 group-hover:text-white',
                      'h-6 w-6 shrink-0'
                    ]"
                    aria-hidden="true"
                  />
                  {{ item.name }}
                  <ChevronRightIcon
                    :class="[
                      item.isOpen ? 'rotate-90' : '',
                      'ml-auto h-5 w-5 text-primary-200 group-hover:text-white transition-transform'
                    ]"
                  />
                </button>
                <TransitionExpand>
                  <ul v-show="item.isOpen" class="mt-1 px-2">
                    <li v-for="child in item.children" :key="child.name">
                      <Link
                        :href="child.href"
                        :class="[
                          route().current(child.active)
                            ? 'bg-primary-700 text-white'
                            : 'text-primary-200 hover:text-white hover:bg-primary-700',
                          'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold'
                        ]"
                      >
                        <component
                          v-if="child.icon"
                          :is="child.icon"
                          :class="[
                            route().current(child.active)
                              ? 'text-white'
                              : 'text-primary-200 group-hover:text-white',
                            'h-6 w-6 shrink-0'
                          ]"
                          aria-hidden="true"
                        />
                        <span class="truncate">{{ child.name }}</span>
                      </Link>
                    </li>
                  </ul>
                </TransitionExpand>
              </div>
            </li>
          </ul>
        </li>

        <!-- Secondary Navigation -->
        <li class="mt-auto">
        </li>
      </ul>
    </nav>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import {
  HomeIcon,
  UsersIcon,
  ShieldCheckIcon,
  KeyIcon,
  ClockIcon,
  ChartBarIcon,
  DocumentDuplicateIcon,
  Cog6ToothIcon,
  ChevronRightIcon,
  UserGroupIcon,
  BriefcaseIcon,
  DocumentTextIcon,
  PhotoIcon,
  ChatBubbleLeftEllipsisIcon,
} from '@heroicons/vue/24/outline'
import TransitionExpand from '@/Components/TransitionExpand.vue'
import SidebarHeader from '@/Components/SidebarHeader.vue'

const page = usePage()
const settings = computed(() => page.props.settings || {})

// Debugging untuk melihat data settings
console.log('Settings dari sidebar:', settings.value)
console.log('Current route:', route().current())

onMounted(() => {
  // Periksa ketersediaan Echo dan channel method
  if (window.Echo && typeof window.Echo.channel === 'function') {
    try {
      window.Echo.channel('settings')
        .listen('.settings.updated', (e) => {
          console.log('Settings updated:', e.settings)
          // Update settings secara langsung
          page.props.settings = e.settings
        })
    } catch (error) {
      console.error('Error setting up Echo channel:', error)
    }
  } else {
    console.warn('Echo or Echo.channel is not available')
  }
})

onUnmounted(() => {
  // Periksa ketersediaan Echo dan leave method
  if (window.Echo && typeof window.Echo.leave === 'function') {
    try {
      window.Echo.leave('settings')
    } catch (error) {
      console.error('Error leaving Echo channel:', error)
    }
  }
})

const navigation = ref([
  {
    name: 'Dashboard',
    href: route('admin.dashboard'),
    active: 'admin.dashboard',
    icon: HomeIcon,
  },
  {
    name: 'User Management',
    icon: UsersIcon,
    isOpen: true,
    children: [
      {
        name: 'Users',
        href: route('admin.users.index'),
        active: 'admin.users.*',
        icon: UserGroupIcon,
      },
      {
        name: 'Roles',
        href: route('admin.roles.index'),
        active: 'admin.roles.*',
        icon: ShieldCheckIcon,
      },
      {
        name: 'Permissions',
        href: route('admin.permissions.index'),
        active: 'admin.permissions.*',
        icon: KeyIcon,
      },
    ],
  },
  {
    name: 'Manajemen Dokumen',
    href: route('admin.documents.index'),
    active: 'admin.documents.*',
    icon: DocumentTextIcon,
  },
  {
    name: 'Activity Log',
    href: route('admin.activities.index'),
    active: 'admin.activities.*',
    icon: ClockIcon,
  },
  {
    name: 'Settings',
    href: route('admin.settings.index'),
    active: 'admin.settings.*',
    icon: Cog6ToothIcon,
  },
])

const teams = ref([])

const isAnyChildActive = (children) => {
  return children.some(child => route().current(child.active))
}

function getInitials(str) {
  return str.match(/\b\w/g).join('');
}
</script>
