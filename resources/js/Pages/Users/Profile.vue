<template>
  <Head title="Profile" />

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <!-- Profile Information -->
      <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="max-w-xl">
          <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>
          <p class="mt-1 text-sm text-gray-600">
            Update your account's profile information and email address.
          </p>

          <form @submit.prevent="updateProfile" class="mt-6 space-y-6">
            <div>
              <label class="block font-medium text-sm text-gray-700" for="name">Name</label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm"
              />
            </div>

            <div>
              <label class="block font-medium text-sm text-gray-700" for="email">Email</label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm"
              />
            </div>

            <div>
              <label class="block font-medium text-sm text-gray-700" for="avatar">Avatar</label>
              <input
                id="avatar"
                ref="avatarInput"
                type="file"
                class="mt-1 block w-full"
                @change="updateAvatar"
              />
            </div>

            <div class="flex items-center gap-4">
              <button
                :disabled="form.processing"
                class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 active:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                Save
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Update Password -->
      <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="max-w-xl">
          <h2 class="text-lg font-medium text-gray-900">Update Password</h2>
          <p class="mt-1 text-sm text-gray-600">
            Ensure your account is using a long, random password to stay secure.
          </p>

          <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div>
              <label class="block font-medium text-sm text-gray-700" for="current_password">
                Current Password
              </label>
              <input
                id="current_password"
                v-model="passwordForm.current_password"
                type="password"
                class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm"
              />
            </div>

            <div>
              <label class="block font-medium text-sm text-gray-700" for="new_password">
                New Password
              </label>
              <input
                id="new_password"
                v-model="passwordForm.new_password"
                type="password"
                class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm"
              />
            </div>

            <div>
              <label class="block font-medium text-sm text-gray-700" for="new_password_confirmation">
                Confirm Password
              </label>
              <input
                id="new_password_confirmation"
                v-model="passwordForm.new_password_confirmation"
                type="password"
                class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm"
              />
            </div>

            <div class="flex items-center gap-4">
              <button
                :disabled="passwordForm.processing"
                class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 active:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                Save
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Activity Log -->
      <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="max-w-xl">
          <h2 class="text-lg font-medium text-gray-900">Activity Log</h2>
          <p class="mt-1 text-sm text-gray-600">
            Recent activity on your account.
          </p>

          <div class="mt-6 space-y-6">
            <div v-for="activity in activities" :key="activity.id" class="flex gap-4">
              <div class="flex-shrink-0">
                <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center">
                  <span class="text-primary-600 text-sm">{{ activity.causer.name.charAt(0) }}</span>
                </div>
              </div>
              <div>
                <p class="text-sm text-gray-600">
                  {{ activity.description }}
                </p>
                <p class="text-xs text-gray-500">
                  {{ new Date(activity.created_at).toLocaleDateString() }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps({
  user: Object,
  activities: Array,
})

const avatarInput = ref(null)

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  avatar: null,
})

const passwordForm = useForm({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
})

const updateProfile = () => {
  form.post(route('profile.update'), {
    preserveScroll: true,
  })
}

const updatePassword = () => {
  passwordForm.patch(route('password.update'), {
    preserveScroll: true,
    onSuccess: () => {
      passwordForm.reset()
    },
  })
}

const updateAvatar = () => {
  const file = avatarInput.value.files[0]
  if (file) {
    form.avatar = file
  }
}
</script>
