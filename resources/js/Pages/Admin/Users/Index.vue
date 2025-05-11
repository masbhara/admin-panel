<template>
  <AdminLayout title="Pengguna" :user="$page.props.auth?.user">
    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-background-primary shadow-sm rounded-lg border border-border-light">
          <div class="p-6 bg-background-secondary dark:bg-background-tertiary shadow-sm">
            <!-- Flash messages -->
            <div v-if="$page.props.flash && $page.props.flash.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
              <span class="block sm:inline">{{ $page.props.flash.success }}</span>
            </div>
            
            <div v-if="$page.props.flash && $page.props.flash.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
              <span class="block sm:inline">{{ $page.props.flash.error }}</span>
            </div>
            
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Pengguna</h2>
              <button
                v-if="$page.props.can && $page.props.can.create_users"
                @click="showCreateModal = true"
                class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 active:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                Tambah Pengguna
              </button>
            </div>

            <!-- Users Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-primary-700">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                      Nama
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                      Email
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                      Peran
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                      Aksi
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
                        <span v-if="user.roles.length === 0" class="text-gray-400 dark:text-gray-300 italic text-xs">Belum ada peran</span>
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
                        Hapus
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
            {{ showEditModal ? 'Edit Pengguna' : 'Tambah Pengguna' }}
          </h3>

          <form @submit.prevent="submitForm" class="mt-6 space-y-6">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 dark:text-white">Nama</label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-primary-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
              />
              <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
            </div>

            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 dark:text-white">Email</label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-primary-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
              />
              <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</div>
            </div>

            <div v-if="!showEditModal">
              <label for="password" class="block text-sm font-medium text-gray-700 dark:text-white">Password</label>
              <input
                id="password"
                v-model="form.password"
                type="password"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-primary-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
              />
              <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-white">Peran</label>
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
              <div v-if="form.errors.roles" class="mt-1 text-sm text-red-600">{{ form.errors.roles }}</div>
            </div>

            <div class="mt-6 flex justify-end">
              <button
                type="button"
                @click="closeModal"
                class="mr-3 inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-white uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 active:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                {{ showEditModal ? 'Update' : 'Simpan' }}
              </button>
            </div>
          </form>
        </div>
      </Modal>

      <!-- Delete Confirmation Modal -->
      <Modal v-if="showDeleteModal" @close="cancelDelete" :paddingInner="false">
        <div class="bg-gray-800 rounded-t-lg">
          <div class="p-4 text-white">
            <h3 class="text-lg font-medium leading-6 text-white">Hapus Pengguna</h3>
            <p class="mt-2 text-sm text-gray-300">
              Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.
            </p>
            <div v-if="errorMessage" class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
              <span class="block sm:inline">{{ errorMessage }}</span>
            </div>
          </div>
        </div>
        <div class="bg-gray-800 rounded-b-lg px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-700">
          <button
            type="button"
            @click="confirmDelete"
            :disabled="isProcessing"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
          >
            <span v-if="isProcessing">Memproses...</span>
            <span v-else>Hapus</span>
          </button>
          <button
            type="button"
            @click="cancelDelete"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-500 shadow-sm px-4 py-2 bg-gray-800 text-base font-medium text-gray-300 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Batal
          </button>
        </div>
      </Modal>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Modal from '@/Components/Modal.vue'
import Pagination from '@/Components/Pagination.vue'
import axios from 'axios'

const props = defineProps({
  users: Object,
  roles: Array,
})

const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const selectedUser = ref(null)
const isProcessing = ref(false)
const errorMessage = ref('')

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
  errorMessage.value = ''
  selectedUser.value = user
  showDeleteModal.value = true
}

const cancelDelete = () => {
  showDeleteModal.value = false
  errorMessage.value = ''
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
  if (!selectedUser.value || !selectedUser.value.id) {
    errorMessage.value = 'ID pengguna tidak valid'
    return
  }

  isProcessing.value = true
  errorMessage.value = ''
  
  // Pastikan ID tidak kosong dan numerik
  const userId = parseInt(selectedUser.value.id);
  if (isNaN(userId) || userId <= 0) {
    errorMessage.value = 'ID pengguna tidak valid'
    isProcessing.value = false
    return
  }
  
  // Gunakan axios.post dengan method spoofing DELETE yang lebih andal
  axios.post(route('admin.users.destroy', userId), {
    _method: 'DELETE'
  })
  .then(response => {
    showDeleteModal.value = false
    selectedUser.value = null
    
    // Reload halaman untuk memperbarui data
    window.location.reload()
  })
  .catch(error => {
    console.error('Delete errors:', error)
    if (error.response && error.response.data && error.response.data.message) {
      errorMessage.value = error.response.data.message
    } else {
      errorMessage.value = 'Terjadi kesalahan saat menghapus pengguna.'
    }
  })
  .finally(() => {
    isProcessing.value = false
  })
}

const closeModal = () => {
  form.reset()
  showCreateModal.value = false
  showEditModal.value = false
  selectedUser.value = null
  errorMessage.value = ''
}
</script>
