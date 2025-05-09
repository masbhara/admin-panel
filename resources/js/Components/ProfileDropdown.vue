<template>
  <Menu as="div" class="relative ml-3" v-if="user">
    <div>
      <MenuButton class="flex rounded-full border-2 border-primary-500 shadow-md bg-background-secondary text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
        <span class="sr-only">Buka menu pengguna</span>
        <img
          v-if="user.avatar_url"
          class="h-8 w-8 rounded-full"
          :src="user.avatar_url"
          :alt="user.name"
        />
        <span
          v-else
          class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-primary-600"
        >
          <span class="text-sm font-medium leading-none text-white">
            {{ user.name.charAt(0).toUpperCase() }}
          </span>
        </span>
      </MenuButton>
    </div>
    <transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <MenuItems class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-md bg-white dark:bg-gray-800 py-1 shadow-lg ring-1 ring-gray-200 dark:ring-gray-700 focus:outline-none">
        <MenuItem v-slot="{ active }">
          <Link 
            :href="isAdmin ? route('admin.profile.edit') : route('profile.edit')" 
            :class="[active ? 'bg-gray-100 dark:bg-gray-700' : '', 'block px-4 py-2 text-sm text-gray-700 dark:text-gray-200']"
          >
            Profil Saya
          </Link>
        </MenuItem>
        <MenuItem v-slot="{ active }" v-if="isAdmin">
          <Link 
            :href="route('admin.settings.index')" 
            :class="[active ? 'bg-gray-100 dark:bg-gray-700' : '', 'block px-4 py-2 text-sm text-gray-700 dark:text-gray-200']"
          >
            Pengaturan
          </Link>
        </MenuItem>
        <div class="border-t border-gray-200 dark:border-gray-700" />
        <MenuItem v-slot="{ active }">
          <Link 
            :href="route('logout')" 
            method="post" 
            as="button"
            :class="[active ? 'bg-gray-100 dark:bg-gray-700' : '', 'block w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-200']"
          >
            Keluar
          </Link>
        </MenuItem>
      </MenuItems>
    </transition>
  </Menu>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'

const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  isAdmin: {
    type: Boolean,
    default: false
  }
})
</script> 