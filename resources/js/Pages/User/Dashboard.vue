<template>
  <UserLayout :user="auth?.user">
    <div class="space-y-6">
      <!-- Welcome Banner -->
      <div v-if="auth?.user" class="bg-white shadow-sm rounded-lg p-6">
        <h2 class="text-2xl font-semibold text-gray-800">Welcome, {{ auth.user.name }}!</h2>
        <p class="mt-2 text-gray-600">Here's your personal dashboard.</p>
      </div>

      <!-- User Profile Summary -->
      <div v-if="auth?.user" class="bg-white shadow rounded-lg">
        <div class="p-6">
          <div class="flex items-center space-x-6">
            <div class="flex-shrink-0">
              <img class="h-24 w-24 rounded-full" :src="auth.user.avatar_url" :alt="auth.user.name" />
            </div>
            <div>
              <h3 class="text-lg font-medium text-gray-900">{{ auth.user.name }}</h3>
              <div class="mt-1 space-y-1">
                <p class="text-sm text-gray-500">{{ auth.user.email }}</p>
                <p class="text-sm text-gray-500">{{ auth.user.phone }}</p>
                <div class="flex items-center space-x-2">
                  <StatusBadge :status="auth.user.status" />
                  <span v-for="role in auth.user.roles" :key="role" class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                    {{ role }}
                  </span>
                </div>
              </div>
              <div class="mt-4">
                <Link
                  :href="route('profile.edit')"
                  class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                >
                  <PencilIcon class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
                  Edit Profile
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="bg-white shadow rounded-lg">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900">Your Recent Activity</h3>
          <div class="mt-6 flow-root">
            <ul role="list" class="-mb-8">
              <li v-for="(activity, activityIdx) in userActivities" :key="activity.id">
                <div class="relative pb-8">
                  <span
                    v-if="activityIdx !== userActivities.length - 1"
                    class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200"
                    aria-hidden="true"
                  />
                  <div class="relative flex space-x-3">
                    <div>
                      <span
                        :class="[
                          activity.type.bgColor,
                          'h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white'
                        ]"
                      >
                        <component
                          :is="activity.type.icon"
                          class="h-5 w-5 text-white"
                          aria-hidden="true"
                        />
                      </span>
                    </div>
                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                      <div>
                        <p class="text-sm text-gray-500">{{ activity.content }}</p>
                      </div>
                      <div class="whitespace-nowrap text-right text-sm text-gray-500">
                        <time :datetime="activity.datetime">{{ activity.date }}</time>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div v-for="action in quickActions" :key="action.name" class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-primary-500 rounded-lg shadow">
          <div>
            <span
              :class="[
                action.iconBackground,
                action.iconForeground,
                'rounded-lg inline-flex p-3 ring-4 ring-white'
              ]"
            >
              <component :is="action.icon" class="h-6 w-6" aria-hidden="true" />
            </span>
          </div>
          <div class="mt-8">
            <h3 class="text-lg font-medium">
              <Link :href="action.href" class="focus:outline-none">
                <span class="absolute inset-0" aria-hidden="true" />
                {{ action.name }}
              </Link>
            </h3>
            <p class="mt-2 text-sm text-gray-500">
              {{ action.description }}
            </p>
          </div>
          <span
            class="pointer-events-none absolute top-6 right-6 text-gray-300 group-hover:text-gray-400"
            aria-hidden="true"
          >
            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
              <path d="M20 4h1a1 1 0 00-1-1v1zm-1 12a1 1 0 102 0h-2zM8 3a1 1 0 000 2V3zM3.293 19.293a1 1 0 101.414 1.414l-1.414-1.414zM19 4v12h2V4h-2zm1-1H8v2h12V3zm-.707.293l-16 16 1.414 1.414 16-16-1.414-1.414z" />
            </svg>
          </span>
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import {
  PencilIcon,
  UserIcon,
  CogIcon,
  DocumentTextIcon,
  ClockIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  auth: Object,
  userActivities: {
    type: Array,
    default: () => [],
  },
})

const quickActions = [
  {
    name: 'Edit Profile',
    description: 'Update your personal information and preferences.',
    href: route('profile.edit'),
    icon: UserIcon,
    iconBackground: 'bg-primary-50',
    iconForeground: 'text-primary-700',
  },
  {
    name: 'Account Settings',
    description: 'Manage your account settings and security preferences.',
    href: '#',
    icon: CogIcon,
    iconBackground: 'bg-yellow-50',
    iconForeground: 'text-yellow-700',
  },
  {
    name: 'View Activity Log',
    description: 'See a detailed history of your account activity.',
    href: '#',
    icon: ClockIcon,
    iconBackground: 'bg-green-50',
    iconForeground: 'text-green-700',
  },
]

const activityTypes = {
  profile_updated: {
    icon: PencilIcon,
    bgColor: 'bg-blue-500',
  },
  document_created: {
    icon: DocumentTextIcon,
    bgColor: 'bg-green-500',
  },
}
</script>
