<template>
  <UserLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Profile</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="flex justify-between mb-6">
              <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900">Profile Information</h3>
                <p class="mt-1 text-sm text-gray-500">View and manage your profile information.</p>
              </div>
              <Link
                :href="route('profile.edit')"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
              >
                Edit Profile
              </Link>
            </div>

            <div class="mt-6 border-t border-gray-100">
              <dl class="divide-y divide-gray-100">
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                  <dt class="text-sm font-medium leading-6 text-gray-900">Full name</dt>
                  <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    {{ $page.props.auth.user.name }}
                  </dd>
                </div>
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                  <dt class="text-sm font-medium leading-6 text-gray-900">Email address</dt>
                  <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    {{ $page.props.auth.user.email }}
                  </dd>
                </div>
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                  <dt class="text-sm font-medium leading-6 text-gray-900">Role</dt>
                  <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    {{ $page.props.auth.user.roles[0]?.name || 'No role assigned' }}
                  </dd>
                </div>
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                  <dt class="text-sm font-medium leading-6 text-gray-900">Account created</dt>
                  <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    {{ new Date($page.props.auth.user.created_at).toLocaleDateString() }}
                  </dd>
                </div>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'
</script>
