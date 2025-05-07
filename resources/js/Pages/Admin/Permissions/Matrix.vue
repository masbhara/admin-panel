<template>
  <AdminLayout title="Permission Matrix">
    <div class="py-6">
      <div class="max-w-7xl mx-auto">
        <div class="bg-white dark:bg-primary-600 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white dark:bg-primary-600 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Permission Matrix</h2>
              <div class="flex space-x-2">
                <Link
                  :href="route('admin.roles.index')"
                  class="inline-flex items-center px-4 py-2 border border-primary-300 rounded-md font-semibold text-xs text-primary-700 dark:text-primary-300 uppercase tracking-widest shadow-sm hover:bg-primary-50 dark:hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  Back to Roles
                </Link>
              </div>
            </div>

            <!-- Permission Matrix Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-primary-700">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                      Permission / Role
                    </th>
                    <th
                      v-for="role in roles"
                      :key="role.id"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider"
                    >
                      {{ role.name }}
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-primary-600 divide-y divide-gray-200 dark:divide-gray-700">
                  <tr v-for="permission in permissions" :key="permission.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">{{ permission.name }}</div>
                    </td>
                    <td
                      v-for="role in roles"
                      :key="role.id"
                      class="px-6 py-4 whitespace-nowrap"
                    >
                      <input
                        type="checkbox"
                        :checked="hasPermission(role, permission)"
                        @change="togglePermission(role, permission)"
                        :disabled="!$page.props.can || !$page.props.can.assign_permissions || role.name === 'super-admin'"
                        class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                      />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  roles: Array,
  permissions: Array,
  rolePermissions: Object,
})

const form = useForm({
  role: '',
  permission: '',
  action: '',
})

const hasPermission = (role, permission) => {
  return props.rolePermissions[role.id]?.includes(permission.id)
}

const togglePermission = (role, permission) => {
  if (role.name === 'super-admin') return

  form.role = role.id
  form.permission = permission.id
  form.action = hasPermission(role, permission) ? 'revoke' : 'give'

  form.post(route('admin.roles.toggle-permission'), {
    preserveScroll: true,
    onSuccess: () => {
      // Permission toggled successfully
    },
  })
}
</script>
