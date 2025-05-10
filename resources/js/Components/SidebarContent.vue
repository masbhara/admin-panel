<template>
  <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-primary-600 px-6 pb-4">
    <!-- Logo dan Judul Website -->
    <SidebarHeader />
    <nav class="flex flex-1 flex-col">
      <ul role="list" class="flex flex-1 flex-col gap-y-7"> 
        <!-- Primary Navigation -->
        <li>
          <ul role="list" class="-mx-2 space-y-1">
            <li v-for="item in filteredNavigation" :key="item.name">
              <Link
                v-if="!item.children"
                :href="item.href"
                :class="[
                  route().current(item.active)
                    ? 'bg-primary-700 text-white'
                    : 'text-primary-300 hover:text-white hover:bg-primary-700',
                  'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition-colors duration-200'
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

              <!-- Dropdown Menu dengan animasi yang lebih baik -->
              <div v-else>
                <button
                  @click="item.isOpen = !item.isOpen"
                  :class="[
                    isAnyChildActive(item.children)
                      ? 'bg-primary-700 text-white'
                      : 'text-primary-200 hover:text-white hover:bg-primary-700',
                    'group flex w-full items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition-colors duration-200'
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
                      'ml-auto h-5 w-5 text-primary-200 group-hover:text-white transition-transform duration-200'
                    ]"
                  />
                </button>
                <TransitionExpand>
                  <ul v-show="item.isOpen" class="mt-1 px-2">
                    <li v-for="child in filteredChildren(item.children)" :key="child.name">
                      <Link
                        :href="child.href"
                        :class="[
                          route().current(child.active)
                            ? 'bg-primary-700 text-white'
                            : 'text-primary-200 hover:text-white hover:bg-primary-700',
                          'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition-colors duration-200'
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

onMounted(() => {
  if (window.Echo && typeof window.Echo.channel === 'function') {
    try {
      window.Echo.channel('settings')
        .listen('.settings.updated', (e) => {
          page.props.settings = e.settings
        })
    } catch (error) {
      console.error('Error setting up Echo channel:', error)
    }
  }
})

onUnmounted(() => {
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
    name: 'Dasbor',
    href: route('admin.dashboard'),
    active: 'admin.dashboard',
    icon: HomeIcon,
  },
  {
    name: 'Dokumen',
    icon: DocumentTextIcon,
    isOpen: true,
    children: [
      {
        name: 'Daftar Dokumen',
        href: route('admin.documents.index'),
        active: 'admin.documents.*',
        icon: DocumentDuplicateIcon,
      },
      {
        name: 'Pengaturan Dokumen',
        href: route('admin.document-settings.index'),
        active: 'admin.document-settings.*',
        icon: Cog6ToothIcon,
      },
    ],
  },
  {
    name: 'Manajemen',
    icon: UsersIcon,
    isOpen: true,
    children: [
      {
        name: 'Pengguna',
        href: route('admin.users.index'),
        active: 'admin.users.*',
        icon: UserGroupIcon,
      },
      {
        name: 'Peran',
        href: route('admin.roles.index'),
        active: 'admin.roles.*',
        icon: ShieldCheckIcon,
      },
      {
        name: 'Izin',
        href: route('admin.permissions.index'),
        active: 'admin.permissions.*',
        icon: KeyIcon,
      },
    ],
  },
  {
    name: 'Aktivitas',
    href: route('admin.activities.index'),
    active: 'admin.activities.*',
    icon: ClockIcon,
  },
  {
    name: 'Pengaturan',
    href: route('admin.settings.index'),
    active: 'admin.settings.*',
    icon: Cog6ToothIcon,
  },
  {
    name: 'Notifikasi WhatsApp',
    href: route('admin.whatsapp-notifications.index'),
    active: 'admin.whatsapp-notifications.*',
    icon: ChatBubbleLeftEllipsisIcon,
  },
])

// Fungsi untuk memeriksa apakah pengguna memiliki izin
const hasPermission = (permission) => {
  if (!permission) return true;
  return !!page.props.auth?.user?.can?.[permission];
}

// Filter menu navigasi berdasarkan izin
const filteredNavigation = computed(() => {
  return navigation.value.filter(item => !item.requirePermission || hasPermission(item.requirePermission));
})

// Filter children pada menu dropdown
const filteredChildren = (children) => {
  if (!children) return [];
  return children.filter(child => !child.requirePermission || hasPermission(child.requirePermission));
}

const teams = ref([])

const isAnyChildActive = (children) => {
  return children.some(child => route().current(child.active))
}

function getInitials(str) {
  return str.match(/\b\w/g).join('');
}
</script>
