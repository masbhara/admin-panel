<template>
  <UserLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">View Profile</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900">{{ user.name }}'s Profile</h3>
                <p class="mt-1 text-sm text-gray-500">Detailed profile information.</p>
              </div>
              <div class="flex space-x-3">
                <Link
                  :href="route('profile.edit')"
                  class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                >
                  Edit Profile
                </Link>
                <Link
                  :href="route('profile.index')"
                  class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                >
                  Back to Profile
                </Link>
              </div>
            </div>

            <div class="mt-6 border-t border-gray-100">
              <dl class="divide-y divide-gray-100">
                <!-- Basic Information -->
                <div class="px-4 py-6">
                  <h4 class="text-sm font-medium text-gray-900">Basic Information</h4>
                  <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ user.name }}</dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Email</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ user.email }}</dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Role</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ user.roles[0]?.name || 'No role assigned' }}</dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Status</dt>
                      <dd class="mt-1">
                        <span
                          :class="[
                            user.email_verified_at
                              ? 'bg-green-100 text-green-800'
                              : 'bg-yellow-100 text-yellow-800',
                            'inline-flex rounded-full px-2 text-xs font-semibold leading-5'
                          ]"
                        >
                          {{ user.email_verified_at ? 'Verified' : 'Unverified' }}
                        </span>
                      </dd>
                    </div>
                  </div>
                </div>

                <!-- Account Information -->
                <div class="px-4 py-6">
                  <h4 class="text-sm font-medium text-gray-900">Account Information</h4>
                  <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Member Since</dt>
                      <dd class="mt-1 text-sm text-gray-900">
                        {{ new Date(user.created_at).toLocaleDateString() }}
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                      <dd class="mt-1 text-sm text-gray-900">
                        {{ new Date(user.updated_at).toLocaleDateString() }}
                      </dd>
                    </div>
                  </div>
                </div>

                <!-- Permissions -->
                <div class="px-4 py-6">
                  <h4 class="text-sm font-medium text-gray-900">Permissions</h4>
                  <div class="mt-4">
                    <div class="flex flex-wrap gap-2">
                      <span
                        v-for="permission in user.permissions"
                        :key="permission.id"
                        class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800"
                      >
                        {{ permission.name }}
                      </span>
                    </div>
                  </div>
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

defineProps({
  user: Object,
})
</script>
