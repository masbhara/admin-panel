<template>
  <Menu as="div" class="relative">
    <MenuButton class="-m-1.5 flex items-center p-1.5">
      <span class="sr-only">View notifications</span>
      <div class="relative">
        <BellIcon class="h-6 w-6 text-gray-400" aria-hidden="true" />
        <div v-if="notifications.length > 0" class="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
          {{ notifications.length }}
        </div>
      </div>
    </MenuButton>
    <transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <MenuItems class="absolute right-0 z-10 mt-2.5 w-80 origin-top-right rounded-md bg-white dark:bg-primary-700 py-2 shadow-lg ring-1 ring-gray-900/5 dark:ring-primary-900/60 focus:outline-none border border-gray-100 dark:border-primary-600">
        <div class="px-4 py-2 border-b border-gray-100 dark:border-primary-600">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</h3>
        </div>
        <div class="max-h-96 overflow-y-auto">
          <div v-if="notifications.length === 0" class="px-4 py-8 text-center">
            <BellIcon class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" />
            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No notifications</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">You're all caught up!</p>
          </div>
          <MenuItem v-for="notification in notifications" :key="notification.id" v-slot="{ active }">
            <button
              :class="[
                active ? 'bg-gray-50 dark:bg-primary-600' : '',
                'flex w-full items-start gap-x-2.5 px-4 py-3 text-left'
              ]"
            >
              <img
                :src="notification.user.avatar_url"
                alt=""
                class="h-10 w-10 flex-none rounded-full bg-gray-50 dark:bg-primary-800"
              />
              <div class="flex-auto">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ notification.user.name }}</p>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300 line-clamp-2">{{ notification.message }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ notification.time }}</p>
              </div>
            </button>
          </MenuItem>
        </div>
        <div class="p-4 border-t border-gray-100 dark:border-primary-600">
          <Link
            href="#"
            class="block w-full rounded-md bg-white dark:bg-primary-800 px-3 py-2 text-center text-sm font-semibold text-gray-900 dark:text-gray-200 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-primary-700 hover:bg-gray-50 dark:hover:bg-primary-600"
          >
            View all
          </Link>
        </div>
      </MenuItems>
    </transition>
  </Menu>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { BellIcon } from '@heroicons/vue/24/outline'

// Sample notifications data
const notifications = ref([
  {
    id: 1,
    user: {
      name: 'John Doe',
      avatar_url: 'https://ui-avatars.com/api/?name=John+Doe',
    },
    message: 'Commented on your post',
    time: '5m ago',
  },
  {
    id: 2,
    user: {
      name: 'Sarah Smith',
      avatar_url: 'https://ui-avatars.com/api/?name=Sarah+Smith',
    },
    message: 'Mentioned you in a comment',
    time: '1h ago',
  },
])
</script>
