<template>
  <Head>
    <title>{{ title ? `${title} - ${$page.props.settings?.site_title || 'Admin Panel'}` : $page.props.settings?.site_title || 'Admin Panel' }}</title>
  </Head>
  <div class="min-h-screen bg-background-primary text-text-primary">
    <!-- Off-canvas menu for mobile -->
    <TransitionRoot as="template" :show="sidebarOpen">
      <Dialog as="div" class="relative z-50 lg:hidden" @close="sidebarOpen = false">
        <TransitionChild
          as="template"
          enter="transition-opacity ease-linear duration-300"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="transition-opacity ease-linear duration-300"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <div class="fixed inset-0 bg-secondary-800/80" />
        </TransitionChild>

        <div class="fixed inset-0 flex">
          <TransitionChild
            as="template"
            enter="transition ease-in-out duration-300 transform"
            enter-from="-translate-x-full"
            enter-to="translate-x-0"
            leave="transition ease-in-out duration-300 transform"
            leave-from="translate-x-0"
            leave-to="-translate-x-full"
          >
            <DialogPanel class="relative mr-16 flex w-full max-w-xs flex-1">
              <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                <button type="button" class="-m-2.5 p-2.5" @click="sidebarOpen = false">
                  <span class="sr-only">Close sidebar</span>
                  <XMarkIcon class="h-6 w-6 text-white" aria-hidden="true" />
                </button>
              </div>

              <!-- Sidebar component for mobile -->
              <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-primary-600 px-6 pb-4">
                <SidebarContent />
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </Dialog>
    </TransitionRoot>

    <!-- Static sidebar for desktop -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
      <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-primary-600 px-6 pb-4">
        <SidebarContent />
      </div>
    </div>

    <div class="lg:pl-72">
      <!-- Top navbar -->
      <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-border-light bg-background-primary px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
        <button type="button" class="-m-2.5 p-2.5 text-text-primary lg:hidden" @click="sidebarOpen = true">
          <span class="sr-only">Open sidebar</span>
          <Bars3Icon class="h-6 w-6" aria-hidden="true" />
        </button>

        <!-- Separator -->
        <div class="h-6 w-px bg-border-light lg:hidden" aria-hidden="true" />

        <!-- Impersonation Banner -->
        <div v-if="$page.props.impersonating" class="flex-1 bg-yellow-100 p-2 text-sm text-yellow-700">
          <div class="flex items-center justify-between">
            <p v-if="$page.props.auth?.user">You are impersonating {{ $page.props.auth.user.name }}</p>
            <Link
              :href="route('admin.impersonate.leave')"
              class="text-yellow-700 underline hover:text-yellow-800"
            >
              Stop Impersonating
            </Link>
          </div>
        </div>

        <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
          <SearchInput class="flex items-center" />
          <div class="flex items-center gap-x-4 lg:gap-x-6">
            <NotificationDropdown />
            
            <!-- Theme Toggle -->
            <div class="flex items-center">
              <ThemeToggleSimple />
            </div>
            
            <!-- Profile dropdown -->
            <Menu as="div" class="relative ml-3" v-if="$page.props.auth?.user">
              <div>
                <MenuButton class="flex rounded-full bg-background-secondary text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                  <span class="sr-only">Open user menu</span>
                  <img
                    v-if="$page.props.auth.user.avatar_url"
                    class="h-8 w-8 rounded-full"
                    :src="$page.props.auth.user.avatar_url"
                    :alt="$page.props.auth.user.name"
                  />
                  <span
                    v-else
                    class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-secondary-500"
                  >
                    <span class="text-sm font-medium leading-none text-white">
                      {{ $page.props.auth.user.name.charAt(0) }}
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
                <MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-background-primary py-1 shadow-lg ring-1 ring-border-light focus:outline-none">
                  <MenuItem v-slot="{ active }">
                    <Link
                      :href="route('admin.profile.edit')"
                      :class="[active ? 'bg-background-secondary' : '', 'block px-4 py-2 text-sm text-text-primary']"
                    >
                      Your Profile
                    </Link>
                  </MenuItem>
                  <MenuItem v-slot="{ active }">
                    <Link
                      :href="route('admin.settings.index')"
                      :class="[active ? 'bg-background-secondary' : '', 'block px-4 py-2 text-sm text-text-primary']"
                    >
                      Settings
                    </Link>
                  </MenuItem>
                  <MenuItem v-slot="{ active }">
                    <Link
                      :href="route('logout')"
                      method="post"
                      as="button"
                      :class="[active ? 'bg-background-secondary' : '', 'block w-full px-4 py-2 text-left text-sm text-text-primary']"
                    >
                      Sign out
                    </Link>
                  </MenuItem>
                </MenuItems>
              </transition>
            </Menu>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <main class="py-10">
        <div class="px-4 sm:px-6 lg:px-8">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, Head, usePage } from '@inertiajs/vue3'
import {
  Dialog,
  DialogPanel,
  Menu,
  MenuButton,
  MenuItem,
  MenuItems,
  TransitionChild,
  TransitionRoot,
} from '@headlessui/vue'
import {
  Bars3Icon,
  XMarkIcon,
} from '@heroicons/vue/24/outline'
import SidebarContent from '@/Components/SidebarContent.vue'
import SearchInput from '@/Components/SearchInput.vue'
import NotificationDropdown from '@/Components/NotificationDropdown.vue'
import ThemeToggleSimple from '@/Components/ThemeToggleSimple.vue'

const props = defineProps({
  title: {
    type: String,
    default: ''
  }
})

const page = usePage()
const sidebarOpen = ref(false)
</script>
