<template>
  <AdminLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Create New User</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6">
            <form @submit.prevent="form.post(route('admin.users.store'))">
              <div class="space-y-6">
                <!-- Basic Information -->
                <div>
                  <h3 class="text-lg font-medium leading-6 text-gray-900">Basic Information</h3>
                  <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                      <InputLabel for="name" value="Name" />
                      <div class="mt-1">
                        <TextInput
                          id="name"
                          v-model="form.name"
                          type="text"
                          class="block w-full"
                          required
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                      </div>
                    </div>

                    <div class="sm:col-span-3">
                      <InputLabel for="email" value="Email" />
                      <div class="mt-1">
                        <TextInput
                          id="email"
                          v-model="form.email"
                          type="email"
                          class="block w-full"
                          required
                        />
                        <InputError :message="form.errors.email" class="mt-2" />
                      </div>
                    </div>

                    <div class="sm:col-span-3">
                      <InputLabel for="password" value="Password" />
                      <div class="mt-1">
                        <TextInput
                          id="password"
                          v-model="form.password"
                          type="password"
                          class="block w-full"
                          required
                        />
                        <InputError :message="form.errors.password" class="mt-2" />
                      </div>
                    </div>

                    <div class="sm:col-span-3">
                      <InputLabel for="password_confirmation" value="Confirm Password" />
                      <div class="mt-1">
                        <TextInput
                          id="password_confirmation"
                          v-model="form.password_confirmation"
                          type="password"
                          class="block w-full"
                          required
                        />
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Role Selection -->
                <div>
                  <h3 class="text-lg font-medium leading-6 text-gray-900">Role</h3>
                  <div class="mt-6">
                    <Select
                      id="role"
                      v-model="form.role"
                      class="block w-full"
                      required
                    >
                      <option value="">Select a role</option>
                      <option v-for="role in roles" :key="role.id" :value="role.name">
                        {{ role.name }}
                      </option>
                    </Select>
                    <InputError :message="form.errors.role" class="mt-2" />
                  </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                  <Link
                    :href="route('admin.users.index')"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 mr-4"
                  >
                    Cancel
                  </Link>
                  <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Create User
                  </PrimaryButton>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import Select from '@/Components/Select.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
  roles: Array,
})

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: '',
})
</script>
