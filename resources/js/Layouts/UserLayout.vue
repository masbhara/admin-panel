<template>
  <div class="min-h-screen bg-background-primary">
    <!-- Navigation -->
    <nav class="bg-background-primary shadow border-b border-border-light">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
          <!-- Logo and Navigation Links -->
          <div class="flex">
            <div class="flex flex-shrink-0 items-center">
              <Link :href="route('dashboard')">
                <img class="h-8 w-auto" src="/logo.svg" alt="Your Company" />
              </Link>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
              <Link
                v-for="item in navigation"
                :key="item.name"
                :href="item.href"
                :class="[
                  route().current(item.routeName)
                    ? 'border-primary-500 text-text-primary'
                    : 'border-transparent text-text-secondary hover:border-secondary-300 hover:text-text-primary',
                  'inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium'
                ]"
              >
                {{ item.name }}
              </Link>
            </div>
          </div>

          <!-- Search -->
          <div class="flex flex-1 items-center justify-center px-2 lg:ml-6 lg:justify-end">
            <div class="w-full max-w-lg lg:max-w-xs">
              <SearchInput />
            </div>
          </div>

          <!-- Right Navigation -->
          <div class="flex items-center">
            <NotificationDropdown class="ml-4" />
            
            <!-- Theme Toggle -->
            <div class="flex items-center ml-4">
              <ThemeToggleSimple />
            </div>

            <!-- Profile dropdown -->
            <Menu as="div" class="relative ml-4">
              <div>
                <MenuButton class="flex rounded-full bg-background-secondary text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                  <span class="sr-only">Open user menu</span>
                  <img
                    class="h-8 w-8 rounded-full"
                    :src="$page.props.auth.user.avatar_url"
                    :alt="$page.props.auth.user.name"
                  />
                </MenuButton>
              </div>
              <transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
              >
                <MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-background-primary py-1 shadow-lg ring-1 ring-border-light focus:outline-none">
                  <MenuItem v-for="item in userNavigation" :key="item.name" v-slot="{ active }">
                    <Link
                      :href="item.href"
                      :method="item.method"
                      :as="item.as"
                      :class="[
                        active ? 'bg-background-secondary' : '',
                        'block px-4 py-2 text-sm text-text-primary'
                      ]"
                    >
                      {{ item.name }}
                    </Link>
                  </MenuItem>
                </MenuItems>
              </transition>
            </Menu>
          </div>
        </div>
      </div>

      <!-- Mobile menu -->
      <Disclosure as="nav" class="sm:hidden" v-slot="{ open }">
        <DisclosureButton class="inline-flex items-center justify-center rounded-md p-2 text-text-secondary hover:bg-background-secondary hover:text-text-primary focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500">
          <span class="sr-only">Open main menu</span>
          <Bars3Icon v-if="!open" class="block h-6 w-6" aria-hidden="true" />
          <XMarkIcon v-else class="block h-6 w-6" aria-hidden="true" />
        </DisclosureButton>
        <DisclosurePanel class="sm:hidden">
          <div class="space-y-1 pb-3 pt-2">
            <Link
              v-for="item in navigation"
              :key="item.name"
              :href="item.href"
              :class="[
                route().current(item.routeName)
                  ? 'bg-primary-50 border-primary-500 text-primary-700 dark:bg-primary-900 dark:text-primary-300'
                  : 'border-transparent text-text-secondary hover:bg-background-secondary hover:border-border-light hover:text-text-primary',
                'block border-l-4 py-2 pl-3 pr-4 text-base font-medium'
              ]"
            >
              {{ item.name }}
            </Link>
          </div>
        </DisclosurePanel>
      </Disclosure>
    </nav>

    <!-- Page Content -->
    <main>
      <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { Menu, MenuButton, MenuItem, MenuItems, Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline'
import SearchInput from '@/Components/SearchInput.vue'
import NotificationDropdown from '@/Components/NotificationDropdown.vue'
import ThemeToggleSimple from '@/Components/ThemeToggleSimple.vue'

const navigation = [
  { name: 'Dashboard', href: route('dashboard'), routeName: 'dashboard' },
  { name: 'Profile', href: route('profile.edit'), routeName: 'profile.edit' },
  { name: 'Settings', href: route('user.settings.index'), routeName: 'user.settings.index' },
]

const userNavigation = [
  { name: 'Your Profile', href: route('profile.edit') },
  { name: 'Settings', href: route('user.settings.index') },
  { name: 'Sign out', href: route('logout'), method: 'post', as: 'button' },
]
</script>
