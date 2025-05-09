<template>
  <AdminLayout title="Permissions" :user="$page.props.auth?.user">
    <div class="py-6">
      <div class="max-w-7xl mx-auto">
        <div class="bg-white dark:bg-primary-600 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white dark:bg-primary-600 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Permissions</h2>
              <div class="flex space-x-2">
                <Link
                  :href="route('admin.roles.matrix')"
                  v-if="$page.props.can && $page.props.can.assign_permissions"
                  class="inline-flex items-center px-4 py-2 border border-primary-300 rounded-md font-semibold text-xs text-primary-700 dark:text-primary-300 uppercase tracking-widest shadow-sm hover:bg-primary-50 dark:hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  Permissions Matrix
                </Link>
                <Link
                  :href="route('admin.permissions.create')"
                  v-if="$page.props.can && $page.props.can.create_permissions"
                  class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 active:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  Add Permission
                </Link>
              </div>
            </div>

            <!-- Permissions Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-primary-700">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                      Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                      Roles
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-primary-600 divide-y divide-gray-200 dark:divide-gray-700">
                  <tr v-for="permission in permissions.data" :key="permission.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">{{ permission.name }}</div>
                    </td>
                    <td class="px-6 py-4">
                      <div class="flex flex-wrap gap-1">
                        <span
                          v-for="role in permission.roles"
                          :key="role.id"
                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-500 dark:text-white mb-1"
                        >
                          {{ role.name }}
                        </span>
                        <span v-if="permission.roles.length === 0" class="text-gray-500 dark:text-gray-300 text-sm">No roles</span>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <Link
                        v-if="$page.props.can && $page.props.can.edit_permissions"
                        :href="route('admin.permissions.edit', permission)"
                        class="text-primary-600 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 mr-3"
                      >
                        Edit
                      </Link>
                      <button
                        v-if="$page.props.can && $page.props.can.delete_permissions"
                        @click="deletePermission(permission)"
                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
              <Pagination :links="permissions.links" />
            </div>
          </div>
        </div>
      </div>

      <!-- Delete Confirmation Modal -->
      <Modal v-if="showDeleteModal" @close="showDeleteModal = false">
        <div class="p-6 bg-white dark:bg-primary-600">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">Delete Permission</h3>
          <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">
            Are you sure you want to delete this permission? This action cannot be undone.
          </p>
          <div class="mt-6 flex justify-end">
            <button
              type="button"
              @click="showDeleteModal = false"
              class="mr-3 inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-white uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
            >
              Cancel
            </button>
            <button
              type="button"
              @click="confirmDelete"
              :disabled="form.processing"
              class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
              Delete
            </button>
          </div>
        </div>
      </Modal>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Modal from '@/Components/Modal.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  permissions: Object,
})

const selectedPermission = ref(null)
const showDeleteModal = ref(false)

const form = useForm({})

const deletePermission = (permission) => {
  selectedPermission.value = permission
  showDeleteModal.value = true
}

const confirmDelete = () => {
  form.delete(route('admin.permissions.destroy', selectedPermission.value), {
    onSuccess: () => {
      showDeleteModal.value = false
      selectedPermission.value = null
    },
  })
}
</script>
