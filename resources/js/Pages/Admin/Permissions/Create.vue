<template>
  <AdminLayout title="Create Permission">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="space-y-8 divide-y divide-gray-200">
        <div class="space-y-6 sm:space-y-5">
          <div>
            <h3 class="text-lg font-medium leading-6 text-gray-900">Create New Permission</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
              Create a new permission that can be assigned to roles.
            </p>
          </div>

          <form @submit.prevent="submit" class="space-y-6">
            <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
              <label class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                Permission Name
              </label>
              <div class="mt-1 sm:col-span-2 sm:mt-0">
                <input
                  v-model="form.name"
                  type="text"
                  class="block w-full max-w-lg rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:max-w-xs sm:text-sm"
                />
                <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
              </div>
            </div>

            <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
              <label class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                Guard Name
              </label>
              <div class="mt-1 sm:col-span-2 sm:mt-0">
                <input
                  v-model="form.guard_name"
                  type="text"
                  class="block w-full max-w-lg rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:max-w-xs sm:text-sm"
                />
                <p v-if="form.errors.guard_name" class="mt-2 text-sm text-red-600">{{ form.errors.guard_name }}</p>
              </div>
            </div>

            <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
              <label class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                Description
              </label>
              <div class="mt-1 sm:col-span-2 sm:mt-0">
                <textarea
                  v-model="form.description"
                  rows="3"
                  class="block w-full max-w-lg rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                />
                <p class="mt-2 text-sm text-gray-500">Brief description of what this permission allows.</p>
                <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">{{ form.errors.description }}</p>
              </div>
            </div>

            <div class="pt-5">
              <div class="flex justify-end gap-3">
                <Link
                  :href="route('admin.permissions.index')"
                  class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
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
  </AdminLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const form = useForm({
  name: '',
  guard_name: 'web',
  description: '',
})

const submit = () => {
  form.post(route('admin.permissions.store'))
}
</script>
