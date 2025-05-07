<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <!-- Logo & Navigation Links -->
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <Link :href="route('dashboard')">
                <ApplicationLogo class="block h-9 w-auto" />
              </Link>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
              <Link
                :href="route('dashboard')"
                class="inline-flex items-center px-1 pt-1 border-b-2"
                :class="[
                  route().current('dashboard')
                    ? 'border-indigo-500 text-gray-900'
                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                ]"
              >
                Dashboard
              </Link>
              <Link
                :href="route('activities')"
                class="inline-flex items-center px-1 pt-1 border-b-2"
                :class="[
                  route().current('activities')
                    ? 'border-indigo-500 text-gray-900'
                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                ]"
              >
                Aktivitas
              </Link>
              <Link
                :href="route('notifications')"
                class="inline-flex items-center px-1 pt-1 border-b-2"
                :class="[
                  route().current('notifications')
                    ? 'border-indigo-500 text-gray-900'
                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                ]"
              >
                Notifikasi
              </Link>
            </div>
          </div>

          <!-- Right Side Menu -->
          <div v-if="user" class="hidden sm:ml-6 sm:flex sm:items-center">
            <!-- Profile Dropdown -->
            <div class="ml-3 relative">
              <Dropdown align="right" width="48">
                <template #trigger>
                  <button
                    class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
                  >
                    <img
                      class="h-8 w-8 rounded-full object-cover"
                      :src="user.profile_photo_url"
                      :alt="user.name"
                    />
                  </button>
                </template>

                <template #content>
                  <DropdownLink :href="route('profile.edit')">
                    Profil Saya
                  </DropdownLink>
                  <DropdownLink :href="route('profile.show', user.id)">
                    Lihat Profil
                  </DropdownLink>
                  <div class="border-t border-gray-200" />
                  <form @submit.prevent="logout">
                    <DropdownLink as="button">
                      Keluar
                    </DropdownLink>
                  </form>
                </template>
              </Dropdown>
            </div>
          </div>

          <!-- Mobile menu button -->
          <div class="-mr-2 flex items-center sm:hidden">
            <button
              @click="showingNavigationDropdown = !showingNavigationDropdown"
              class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition"
            >
              <span class="sr-only">Open main menu</span>
              <svg
                class="h-6 w-6"
                stroke="currentColor"
                fill="none"
                viewBox="0 0 24 24"
              >
                <path
                  :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"
                />
                <path
                  :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Navigation Menu -->
      <div
        :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
        class="sm:hidden"
      >
        <div class="pt-2 pb-3 space-y-1">
          <ResponsiveNavLink
            :href="route('dashboard')"
            :active="route().current('dashboard')"
          >
            Dashboard
          </ResponsiveNavLink>
          <ResponsiveNavLink
            :href="route('activities')"
            :active="route().current('activities')"
          >
            Aktivitas
          </ResponsiveNavLink>
          <ResponsiveNavLink
            :href="route('notifications')"
            :active="route().current('notifications')"
          >
            Notifikasi
          </ResponsiveNavLink>
        </div>

        <!-- Responsive Settings Options -->
        <div v-if="user" class="pt-4 pb-1 border-t border-gray-200">
          <div class="flex items-center px-4">
            <div class="flex-shrink-0">
              <img
                class="h-10 w-10 rounded-full object-cover"
                :src="user.profile_photo_url"
                :alt="user.name"
              />
            </div>
            <div class="ml-3">
              <div class="font-medium text-base text-gray-800">
                {{ user.name }}
              </div>
              <div class="font-medium text-sm text-gray-500">
                {{ user.email }}
              </div>
            </div>
          </div>

          <div class="mt-3 space-y-1">
            <ResponsiveNavLink :href="route('profile.edit')">
              Profil Saya
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('profile.show', user.id)">
              Lihat Profil
            </ResponsiveNavLink>
            <form method="POST" @submit.prevent="logout">
              <ResponsiveNavLink as="button">
                Keluar
              </ResponsiveNavLink>
            </form>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page Heading -->
    <header class="bg-white shadow" v-if="$slots.header">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <slot name="header" />
      </div>
    </header>

    <!-- Page Content -->
    <main>
      <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <slot />
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'

const page = usePage()
const showingNavigationDropdown = ref(false)

const user = computed(() => page.props.auth?.user)

const logout = () => {
  router.post(route('logout'))
}
</script>
