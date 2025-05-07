<template>
  <AdminLayout title="Users">
    <div class="py-6">
      <div class="max-w-7xl mx-auto">
        <div class="bg-white dark:bg-primary-600 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white dark:bg-primary-600 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Users</h2>
              <button
                v-if="$page.props.can && $page.props.can.create_users"
                @click="showCreateModal = true"
                class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 active:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                Add User
              </button>
            </div>

            <!-- Users Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-primary-700">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                      Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                      Email
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
                  <tr v-for="user in users.data" :key="user.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="h-10 w-10 flex-shrink-0">
                          <img
                            v-if="user.avatar"
                            :src="user.avatar"
                            class="h-10 w-10 rounded-full"
                          />
                          <div
                            v-else
                            class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-500 flex items-center justify-center"
                          >
                            <span class="text-primary-600 dark:text-white font-medium text-sm">
                              {{ user.name.charAt(0).toUpperCase() }}
                            </span>
                          </div>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ user.name }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900 dark:text-white">{{ user.email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex flex-wrap gap-1">
                        <span
                          v-for="role in user.roles"
                          :key="role.id"
                          class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-500 dark:text-white"
                        >
                          {{ role.name }}
                        </span>
                        <span v-if="user.roles.length === 0" class="text-gray-400 dark:text-gray-300 italic text-xs">No roles assigned</span>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <button
                        @click="editUser(user)"
                        class="text-primary-600 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 mr-3 font-medium"
                      >
                        Edit
                      </button>
                      <button
                        @click="deleteUser(user)"
                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 font-medium"
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
              <Pagination :links="users.links" />
            </div>
          </div>
        </div>
      </div>

      <!-- Create/Edit User Modal -->
      <Modal v-if="showCreateModal || showEditModal" @close="closeModal">
        <div class="p-6 bg-white dark:bg-primary-600">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            {{ showEditModal ? 'Edit User' : 'Create User' }}
          </h3>

          <form @submit.prevent="submitForm" class="mt-6 space-y-6">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 dark:text-white">Name</label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-primary-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
              />
            </div>

            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 dark:text-white">Email</label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-primary-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
              />
            </div>

            <div v-if="!showEditModal">
              <label for="password" class="block text-sm font-medium text-gray-700 dark:text-white">Password</label>
              <input
                id="password"
                v-model="form.password"
                type="password"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-primary-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-white">Roles</label>
              <div class="mt-2 space-y-2">
                <label
                  v-for="role in roles"
                  :key="role.id"
                  class="inline-flex items-center mr-4"
                >
                  <input
                    type="checkbox"
                    :value="role.id"
                    v-model="form.roles"
                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                  />
                  <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">{{ role.name }}</span>
                </label>
              </div>
            </div>

            <div class="mt-6 flex justify-end">
              <button
                type="button"
                @click="closeModal"
                class="mr-3 inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-white uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 active:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                {{ showEditModal ? 'Update' : 'Create' }}
              </button>
            </div>
          </form>
        </div>
      </Modal>

      <!-- Delete Confirmation Modal -->
      <Modal v-if="showDeleteModal" @close="showDeleteModal = false">
        <div class="p-6 bg-white dark:bg-primary-600">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">Delete User</h3>
          <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">
            Are you sure you want to delete this user? This action cannot be undone.
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
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Modal from '@/Components/Modal.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  users: Object,
  roles: Array,
})

const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const selectedUser = ref(null)

const form = useForm({
  name: '',
  email: '',
  password: '',
  roles: [],
})

const editUser = (user) => {
  selectedUser.value = user
  form.name = user.name
  form.email = user.email
  form.roles = user.roles.map(role => role.id)
  showEditModal.value = true
}

const deleteUser = (user) => {
  selectedUser.value = user
  showDeleteModal.value = true
}

const submitForm = () => {
  if (showEditModal.value) {
    form.patch(route('admin.users.update', selectedUser.value.id), {
      onSuccess: () => closeModal(),
    })
  } else {
    form.post(route('admin.users.store'), {
      onSuccess: () => closeModal(),
    })
  }
}

const confirmDelete = () => {
  form.delete(route('admin.users.destroy', selectedUser.value.id), {
    onSuccess: () => {
      showDeleteModal.value = false
      selectedUser.value = null
    },
  })
}

const closeModal = () => {
  form.reset()
  showCreateModal.value = false
  showEditModal.value = false
  selectedUser.value = null
}
</script>
