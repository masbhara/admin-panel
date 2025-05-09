<template>
  <AdminLayout :user="auth?.user">
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">View Setting</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900">Setting Details</h3>
                <p class="mt-1 text-sm text-gray-500">Detailed information about this setting.</p>
              </div>
              <div class="flex space-x-3">
                <Link
                  :href="route('admin.settings.edit', setting.id)"
                  class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                >
                  Edit Setting
                </Link>
                <Link
                  :href="route('admin.settings.index')"
                  class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                >
                  Back to Settings
                </Link>
              </div>
            </div>

            <div class="mt-6 border-t border-gray-100">
              <dl class="divide-y divide-gray-100">
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                  <dt class="text-sm font-medium leading-6 text-gray-900">Setting Key</dt>
                  <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    {{ setting.key }}
                  </dd>
                </div>

                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                  <dt class="text-sm font-medium leading-6 text-gray-900">Type</dt>
                  <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                      {{ setting.type }}
                    </span>
                  </dd>
                </div>

                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                  <dt class="text-sm font-medium leading-6 text-gray-900">Value</dt>
                  <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <div v-if="setting.type === 'boolean'" class="flex h-6 items-center">
                      <input
                        type="checkbox"
                        :checked="setting.value"
                        disabled
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600"
                      />
                    </div>
                    <pre v-else-if="setting.type === 'textarea'" class="mt-1 whitespace-pre-wrap text-sm text-gray-900">{{ setting.value }}</pre>
                    <p v-else class="mt-1 text-sm text-gray-900">{{ setting.value }}</p>
                  </dd>
                </div>

                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                  <dt class="text-sm font-medium leading-6 text-gray-900">Description</dt>
                  <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    {{ setting.description }}
                  </dd>
                </div>

                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                  <dt class="text-sm font-medium leading-6 text-gray-900">Last Updated</dt>
                  <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    {{ new Date(setting.updated_at).toLocaleString() }}
                  </dd>
                </div>
              </dl>
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

defineProps({
  setting: Object,
})
</script>
