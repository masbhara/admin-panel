<template>
  <AdminLayout :title="'Dashboard'">
    <div class="space-y-6 max-w-7xl mx-auto">
      <!-- Welcome Banner dengan gradient yang lebih menarik -->
      <div class="bg-gradient-to-r from-primary-600 via-primary-500 to-primary-700 shadow-lg rounded-lg p-6 border border-primary-400/20">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-white">Selamat datang, {{ auth?.user?.name || 'Admin' }}!</h2>
            <p class="mt-2 text-primary-100">Berikut ringkasan aktivitas aplikasi Anda saat ini.</p>
          </div>
          <div class="hidden md:flex items-center justify-center bg-white/10 p-3 rounded-full">
            <CalendarIcon class="h-12 w-12 text-white" />
          </div>
        </div>
      </div>

      <!-- Stats Grid dengan desain card yang ditingkatkan -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div v-for="stat in stats" :key="stat.name" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-all duration-200 border border-gray-100 dark:border-gray-700">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-primary-100 dark:bg-primary-900 p-3 rounded-lg">
                <component :is="stat.icon" class="h-6 w-6 text-primary-600 dark:text-primary-400" aria-hidden="true" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ stat.name }}</dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stat.stat }}</div>
                    <div :class="[
                      stat.changeType === 'increase' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400',
                      'ml-2 flex items-baseline text-sm font-semibold'
                    ]">
                      <component
                        :is="stat.changeType === 'increase' ? ArrowUpIcon : ArrowDownIcon"
                        class="self-center flex-shrink-0 h-5 w-5"
                        aria-hidden="true"
                      />
                      <span class="sr-only">
                        {{ stat.changeType === 'increase' ? 'Meningkat' : 'Menurun' }} sebesar
                      </span>
                      {{ stat.change }}
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 dark:bg-gray-700 px-5 py-2 border-t border-gray-100 dark:border-gray-600">
            <Link 
              :href="stat.href" 
              class="text-sm text-primary-600 dark:text-primary-400 font-medium hover:text-primary-800 dark:hover:text-primary-300 flex items-center"
            >
              Lihat Detail
              <ArrowRightIcon class="ml-1 h-4 w-4" />
            </Link>
          </div>
        </div>
      </div>

      <!-- Activity List dengan UI yang ditingkatkan -->
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Recent Document Submissions -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700">
          <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Pengiriman Dokumen Terbaru</h3>
              <span class="bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-300 px-2.5 py-0.5 rounded-full text-xs font-medium">
                {{ activities.length }} dokumen
              </span>
            </div>
          </div>
          <div class="p-6">
            <div class="flow-root">
              <ul role="list" class="-mb-8">
                <li v-for="activity in activities" :key="activity.id" class="relative pb-8">
                  <span v-if="activities.indexOf(activity) !== activities.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true" />
                  <div class="relative flex space-x-3">
                    <div>
                      <UserAvatar 
                        :name="activity.user?.name || 'Unknown User'" 
                        :image-url="activity.user?.avatar_url" 
                      />
                    </div>
                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                      <div>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                          <span class="font-medium text-gray-900 dark:text-white">{{ activity.user?.name || 'Unknown User' }}</span>
                          mengirim dokumen
                          <Link :href="activity.href" class="font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300">
                            {{ activity.target }}
                          </Link>
                        </p>
                      </div>
                      <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                        <time :datetime="activity.datetime">{{ activity.time }}</time>
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
            <div class="mt-6">
              <Link :href="route('admin.document-forms.index')" class="flex w-full items-center justify-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                Lihat semua dokumen
                <ArrowRightIcon class="ml-2 h-4 w-4" />
              </Link>
            </div>
          </div>
        </div>

        <!-- Recent Users dengan UI yang ditingkatkan -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700">
          <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Pengguna Terbaru</h3>
              <span class="bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-300 px-2.5 py-0.5 rounded-full text-xs font-medium">
                {{ recentUsers.length }} pengguna
              </span>
            </div>
          </div>
          <div class="p-6">
            <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
              <li v-for="user in recentUsers" :key="user.id" class="py-4">
                <div class="flex items-center space-x-4">
                  <div class="flex-shrink-0">
                    <UserAvatar 
                      :name="user.name || 'Unknown User'" 
                      :image-url="user.avatar_url" 
                    />
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-gray-900 dark:text-white">{{ user.name || 'Unknown User' }}</p>
                    <p class="truncate text-sm text-gray-500 dark:text-gray-400">{{ user.email || '-' }}</p>
                  </div>
                  <StatusBadge :status="user.status" />
                </div>
              </li>
            </ul>
            <div class="mt-6">
              <Link :href="route('admin.users.index')" class="flex w-full items-center justify-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                Lihat semua pengguna
                <ArrowRightIcon class="ml-2 h-4 w-4" />
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import UserAvatar from '@/Components/UserAvatar.vue'
import {
  ArrowUpIcon,
  ArrowDownIcon,
  UsersIcon,
  DocumentCheckIcon,
  ShieldCheckIcon,
  ClockIcon,
  ArrowRightIcon,
  CalendarIcon
} from '@heroicons/vue/24/solid'
import {
  UserPlusIcon,
  UserMinusIcon,
  PencilSquareIcon,
  TrashIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  auth: Object,
  stats: {
    type: Array,
    default: () => [
      {
        name: 'Total Pengguna',
        stat: '0',
        change: '0%',
        changeType: 'increase',
        icon: UsersIcon,
        href: route('admin.users.index'),
      },
      {
        name: 'Pengguna Aktif',
        stat: '0',
        change: '0%',
        changeType: 'increase',
        icon: DocumentCheckIcon,
        href: route('admin.users.index'),
      },
      {
        name: 'Peran',
        stat: '0',
        change: '0%',
        changeType: 'increase',
        icon: ShieldCheckIcon,
        href: route('admin.roles.index'),
      },
      {
        name: 'Aktivitas Terbaru',
        stat: '0',
        change: '0%',
        changeType: 'increase',
        icon: ClockIcon,
        href: route('admin.activities.index'),
      },
    ],
  },
  activities: {
    type: Array,
    default: () => [],
  },
  recentUsers: {
    type: Array,
    default: () => [],
  },
})

const getActivityIcon = (type) => {
  if (type.includes('created')) return UserPlusIcon;
  if (type.includes('updated')) return PencilSquareIcon;
  if (type.includes('deleted')) return TrashIcon;
  if (type.includes('deactivated')) return UserMinusIcon;
  return ClockIcon;
}

const getActivityColor = (type) => {
  if (type.includes('created')) return 'bg-green-600 dark:bg-green-700';
  if (type.includes('updated')) return 'bg-blue-600 dark:bg-blue-700';
  if (type.includes('deleted')) return 'bg-red-600 dark:bg-red-700';
  if (type.includes('deactivated')) return 'bg-yellow-600 dark:bg-yellow-700';
  return 'bg-gray-600 dark:bg-gray-700';
}
</script> 