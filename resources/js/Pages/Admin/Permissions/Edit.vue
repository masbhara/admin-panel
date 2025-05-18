<template>
  <AdminLayout title="Edit Permission">
    <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
      <div class="overflow-hidden bg-background-primary shadow-sm rounded-lg border border-border-light">
        <div class="p-6 bg-background-secondary dark:bg-background-tertiary shadow-sm">
          <div class="space-y-8 divide-y divide-gray-200 dark:divide-gray-700">
            <div class="space-y-6 sm:space-y-5">
              <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Edit Permission</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                  Update permission details.
                </p>
              </div>

              <form @submit.prevent="submit" class="space-y-6">
                <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 dark:border-gray-700 sm:pt-5">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 sm:mt-px sm:pt-2">
                    Permission Name
                  </label>
                  <div class="mt-1 sm:col-span-2 sm:mt-0">
                    <input
                      v-model="form.name"
                      type="text"
                      :disabled="isCriticalPermission"
                      class="block w-full max-w-lg rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:max-w-xs sm:text-sm disabled:bg-gray-100 dark:disabled:bg-gray-800"
                    />
                    <p v-if="form.errors.name" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ form.errors.name }}</p>
                  </div>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 dark:border-gray-700 sm:pt-5">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 sm:mt-px sm:pt-2">
                    Guard Name
                  </label>
                  <div class="mt-1 sm:col-span-2 sm:mt-0">
                    <input
                      v-model="form.guard_name"
                      type="text"
                      :disabled="isCriticalPermission"
                      class="block w-full max-w-lg rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:max-w-xs sm:text-sm disabled:bg-gray-100 dark:disabled:bg-gray-800"
                    />
                    <p v-if="form.errors.guard_name" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ form.errors.guard_name }}</p>
                  </div>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 dark:border-gray-700 sm:pt-5">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 sm:mt-px sm:pt-2">
                    Description
                  </label>
                  <div class="mt-1 sm:col-span-2 sm:mt-0">
                    <textarea
                      v-model="form.description"
                      rows="3"
                      class="block w-full max-w-lg rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                    />
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Brief description of what this permission allows.</p>
                    <p v-if="form.errors.description" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ form.errors.description }}</p>
                  </div>
                </div>

                <div class="pt-5">
                  <div class="flex justify-end gap-3">
                    <Link
                      :href="route('admin.permissions.index')"
                      class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 py-2 px-4 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                    >
                      Cancel
                    </Link>
                    <button
                      type="submit"
                      :disabled="form.processing || isCriticalPermission"
                      class="inline-flex justify-center rounded-md border border-transparent bg-primary-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:bg-gray-300 dark:disabled:bg-gray-600"
                    >
                      Update
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
import { computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  permission: {
    type: Object,
    required: true,
  },
})

const criticalPermissions = [
  'create users',
  'edit users',
  'delete users',
  'manage roles',
  'manage permissions'
]

const isCriticalPermission = computed(() => criticalPermissions.includes(props.permission.name))

const form = useForm({
  name: props.permission.name,
  guard_name: props.permission.guard_name,
  description: props.permission.description,
})

const submit = () => {
  form.put(route('admin.permissions.update', props.permission.id))
}
</script>
