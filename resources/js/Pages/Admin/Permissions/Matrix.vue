<template>
  <AdminLayout title="Permission Matrix">
    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-background-primary shadow-sm rounded-lg border border-border-light">
          <div class="p-6 bg-background-secondary dark:bg-background-tertiary shadow-sm">
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

            <!-- Status message -->
            <div v-if="statusMessage" class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300 rounded-md">
              {{ statusMessage }}
            </div>

            <!-- Permission Matrix Table -->
            <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Permission / Role
                    </th>
                    <th
                      v-for="role in roles"
                      :key="role.id"
                      scope="col"
                      class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      <div class="flex flex-col items-center">
                        <span>{{ role.name }}</span>
                        <div 
                          v-if="$page.props.can?.assign_permissions && role.name !== 'super-admin'"
                          class="flex mt-2"
                        >
                          <input
                            type="checkbox"
                            :checked="isAllSelected(role.id)"
                            @change="toggleSelectAll(role.id)"
                            :disabled="roleProcessing"
                            class="rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:bg-gray-700 dark:checked:bg-primary-500"
                          />
                          <span class="ml-2 text-xs text-gray-500 dark:text-gray-300">Select All</span>
                        </div>
                      </div>
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                  <tr v-for="permission in permissions" :key="permission.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ permission.name }}</div>
                    </td>
                    <td
                      v-for="role in roles"
                      :key="role.id"
                      class="px-6 py-4 whitespace-nowrap text-center"
                    >
                      <input
                        type="checkbox"
                        :checked="hasPermission(role, permission)"
                        @change="togglePermission(role, permission)"
                        :disabled="!$page.props.can || !$page.props.can.assign_permissions || role.name === 'super-admin' || roleProcessing"
                        class="rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:bg-gray-700 dark:checked:bg-primary-500"
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
import { ref, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  roles: Array,
  permissions: Array,
  rolePermissions: Object,
})

// Set up form for individual permission toggles
const form = useForm({
  role: '',
  permission: '',
  action: '',
})

// Status messages
const statusMessage = ref('');
const roleProcessing = ref(false);

const hasPermission = (role, permission) => {
  return props.rolePermissions[role.id]?.includes(permission.id)
}

const togglePermission = (role, permission) => {
  if (role.name === 'super-admin' || roleProcessing.value) return

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

// Check if all permissions are selected for a role
const isAllSelected = (roleId) => {
  if (!props.permissions || !props.permissions.length) return false
  return props.permissions.every(permission => 
    props.rolePermissions[roleId]?.includes(permission.id)
  )
}

// Toggle all permissions for a role
const toggleSelectAll = async (roleId) => {
  // Skip for super-admin
  if (props.roles.find(role => role.id === roleId)?.name === 'super-admin') return
  
  // Skip if no permissions or no role permissions
  if (!props.permissions || !props.permissions.length || !props.rolePermissions) return
  
  const allSelected = isAllSelected(roleId)
  const action = allSelected ? 'revoke' : 'give'
  
  // Disable UI during processing
  roleProcessing.value = true
  statusMessage.value = `Memproses izin, harap tunggu...`
  
  try {
    await axios.post(route('admin.roles.toggle-all-permissions'), {
      role: roleId,
      action: action
    })
    
    // Refresh the page to get updated data
    statusMessage.value = 'Berhasil memperbarui izin'
    router.reload({ preserveScroll: true })
  } catch (error) {
    console.error('Error toggling all permissions:', error)
    statusMessage.value = 'Gagal memperbarui izin. Silakan coba lagi.'
    // Reload to ensure UI is in sync with server state
    router.reload({ preserveScroll: true })
  } finally {
    setTimeout(() => {
      roleProcessing.value = false
    }, 500)
  }
}
</script>

<style scoped>
/* Add any additional styling here */
</style>
