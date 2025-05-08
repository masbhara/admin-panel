<template>
  <AdminLayout :title="'Dashboard'">
    <div class="space-y-6">
      <!-- Welcome Banner -->
      <div class="bg-primary-600 shadow-sm rounded-lg p-6">
        <h2 class="text-2xl font-semibold text-white">Welcome back, {{ auth.user.name }}!</h2>
        <p class="mt-2 text-gray-600">Here's what's happening with your application today.</p>
      </div>

      <!-- Debug Permission -->
      <PermissionDebug />

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div v-for="stat in stats" :key="stat.name" class="bg-white dark:bg-primary-600 overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <component :is="stat.icon" class="h-6 w-6 text-gray-400" aria-hidden="true" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 dark:text-white truncate">{{ stat.name }}</dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stat.stat }}</div>
                    <div :class="[
                      stat.changeType === 'increase' ? 'text-green-500' : 'text-red-600',
                      'ml-2 flex items-baseline text-sm font-semibold'
                    ]">
                      <component
                        :is="stat.changeType === 'increase' ? ArrowUpIcon : ArrowDownIcon"
                        class="self-center flex-shrink-0 h-5 w-5"
                        aria-hidden="true"
                      />
                      <span class="sr-only">
                        {{ stat.changeType === 'increase' ? 'Increased' : 'Decreased' }} by
                      </span>
                      {{ stat.change }}
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Activity List -->
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Recent Activities -->
        <div class="bg-white dark:bg-primary-600 shadow rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Recent Activities</h3>
            <div class="mt-6 flow-root">
              <ul role="list" class="-mb-8">
                <li v-for="activity in activities" :key="activity.id" class="relative pb-8">
                  <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true" />
                  <div class="relative flex space-x-3">
                    <div>
                      <span
                        :class="[
                          getActivityColor(activity.type),
                          'h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white'
                        ]"
                      >
                        <component
                          :is="getActivityIcon(activity.type)"
                          class="h-5 w-5 text-white"
                          aria-hidden="true"
                        />
                      </span>
                    </div>
                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                      <div>
                        <p class="text-sm text-gray-500 dark:text-white">
                          {{ activity.description }}
                          <a :href="activity.href" class="font-medium text-gray-900 dark:text-white">
                            {{ activity.target }}
                          </a>
                        </p>
                      </div>
                      <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-white">
                        <time :datetime="activity.datetime">{{ activity.time }}</time>
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
            <div class="mt-6">
              <Link :href="route('admin.activities.index')" class="flex w-full items-center justify-center rounded-md bg-white dark:bg-primary-600 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                View all activities
              </Link>
            </div>
          </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white dark:bg-primary-600 shadow rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Recent Users</h3>
            <div class="mt-6 flow-root">
              <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                <li v-for="user in recentUsers" :key="user.id" class="py-4">
                  <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                      <img class="h-8 w-8 rounded-full" :src="user.avatar_url" :alt="user.name" />
                    </div>
                    <div class="min-w-0 flex-1">
                      <p class="truncate text-sm font-medium text-gray-900 dark:text-white">{{ user.name }}</p>
                      <p class="truncate text-sm text-gray-500">{{ user.email }}</p>
                    </div>
                    <StatusBadge :status="user.status" />
                  </div>
                </li>
              </ul>
            </div>
            <div class="mt-6">
              <Link :href="route('admin.users.index')" class="flex w-full items-center justify-center rounded-md bg-white dark:bg-primary-600 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                View all users
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
import PermissionDebug from '@/Components/PermissionDebug.vue'
import {
  ArrowUpIcon,
  ArrowDownIcon,
  UsersIcon,
  DocumentCheckIcon,
  ShieldCheckIcon,
  ClockIcon,
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
        name: 'Total Users',
        stat: '0',
        change: '0%',
        changeType: 'increase',
        icon: UsersIcon,
        href: '#',
      },
      {
        name: 'Active Users',
        stat: '0',
        change: '0%',
        changeType: 'increase',
        icon: DocumentCheckIcon,
        href: '#',
      },
      {
        name: 'Roles',
        stat: '0',
        change: '0%',
        changeType: 'increase',
        icon: ShieldCheckIcon,
        href: '#',
      },
      {
        name: 'Recent Activities',
        stat: '0',
        change: '0%',
        changeType: 'increase',
        icon: ClockIcon,
        href: '#',
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
  if (type.includes('created')) return 'bg-green-500';
  if (type.includes('updated')) return 'bg-blue-500';
  if (type.includes('deleted')) return 'bg-red-500';
  if (type.includes('deactivated')) return 'bg-yellow-500';
  return 'bg-gray-500';
}
</script> 