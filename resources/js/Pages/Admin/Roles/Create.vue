<template>
  <AdminLayout title="Create Role">
    <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
      <div class="overflow-hidden bg-background-primary shadow-sm rounded-lg border border-border-light">
        <div class="p-6 bg-background-secondary dark:bg-background-tertiary shadow-sm">
          <div class="space-y-8 divide-y divide-gray-200 dark:divide-gray-700">
            <div class="space-y-6 sm:space-y-5">
              <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Create New Role</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-300">
                  Create a new role and assign permissions.
                </p>
              </div>

              <form @submit.prevent="submit" class="space-y-6">
                <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 dark:border-gray-700 sm:pt-5">
                  <label class="block text-sm font-medium text-gray-700 dark:text-white sm:mt-px sm:pt-2">
                    Role Name
                  </label>
                  <div class="mt-1 sm:col-span-2 sm:mt-0">
                    <input
                      v-model="form.name"
                      type="text"
                      class="block w-full max-w-lg rounded-md border-gray-300 dark:border-gray-600 dark:bg-primary-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:max-w-xs sm:text-sm"
                    />
                    <p v-if="form.errors.name" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ form.errors.name }}</p>
                  </div>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 dark:border-gray-700 sm:pt-5">
                  <label class="block text-sm font-medium text-gray-700 dark:text-white sm:mt-px sm:pt-2">
                    Guard Name
                  </label>
                  <div class="mt-1 sm:col-span-2 sm:mt-0">
                    <input
                      v-model="form.guard_name"
                      type="text"
                      class="block w-full max-w-lg rounded-md border-gray-300 dark:border-gray-600 dark:bg-primary-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:max-w-xs sm:text-sm"
                    />
                    <p v-if="form.errors.guard_name" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ form.errors.guard_name }}</p>
                  </div>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 dark:border-gray-700 sm:pt-5">
                  <label class="block text-sm font-medium text-gray-700 dark:text-white sm:mt-px sm:pt-2">
                    Permissions
                  </label>
                  <div class="mt-1 sm:col-span-2 sm:mt-0">
                    <div class="max-h-96 overflow-y-auto space-y-2">
                      <div v-for="permission in permissions" :key="permission.id" class="relative flex items-start">
                        <div class="flex h-5 items-center">
                          <input
                            :id="permission.id"
                            v-model="form.permissions"
                            :value="permission.id"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                          />
                        </div>
                        <div class="ml-3 text-sm">
                          <label :for="permission.id" class="font-medium text-gray-700 dark:text-white">{{ permission.name }}</label>
                          <p class="text-gray-500 dark:text-gray-300">{{ permission.description }}</p>
                        </div>
                      </div>
                    </div>
                    <p v-if="form.errors.permissions" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ form.errors.permissions }}</p>
                  </div>
                </div>

                <div class="pt-5">
                  <div class="flex justify-end gap-3">
                    <Link
                      :href="route('admin.roles.index')"
                      class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-primary-700 py-2 px-4 text-sm font-medium text-gray-700 dark:text-white shadow-sm hover:bg-gray-50 dark:hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                    >
                      Cancel
                    </Link>
                    <button
                      type="submit"
                      :disabled="form.processing"
                      class="inline-flex justify-center rounded-md border border-transparent bg-primary-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                    >
                      Create
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  permissions: {
    type: Array,
    required: true,
  },
})

const form = useForm({
  name: '',
  guard_name: 'web',
  permissions: [],
})

const submit = () => {
  form.post(route('admin.roles.store'))
}
</script>
