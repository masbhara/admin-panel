<template>
  <AdminLayout title="Profile">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Profile</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
          <section>
            <header>
              <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>
              <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information and email address.
              </p>
            </header>

            <form @submit.prevent="updateProfile" class="mt-6 space-y-6">
              <div>
                <div class="flex items-center gap-4">
                  <div class="relative">
                    <img
                      :src="form.avatar_url || user.avatar_url"
                      class="w-16 h-16 rounded-full object-cover"
                      alt="Profile"
                    />
                    <button
                      type="button"
                      @click="$refs.avatarInput.click()"
                      class="absolute bottom-0 right-0 bg-white rounded-full p-1 shadow-sm border border-gray-200 hover:bg-gray-50"
                    >
                      <PencilIcon class="w-4 h-4 text-gray-500" />
                    </button>
                    <input
                      ref="avatarInput"
                      type="file"
                      class="hidden"
                      @change="updateAvatar"
                      accept="image/*"
                    />
                  </div>
                </div>
              </div>

              <div>
                <InputLabel for="name" value="Name" />
                <TextInput
                  id="name"
                  type="text"
                  class="mt-1 block w-full"
                  v-model="form.name"
                  required
                  autofocus
                />
                <InputError class="mt-2" :message="form.errors.name" />
              </div>

              <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                  id="email"
                  type="email"
                  class="mt-1 block w-full"
                  v-model="form.email"
                  required
                />
                <InputError class="mt-2" :message="form.errors.email" />
              </div>

              <div>
                <InputLabel for="phone" value="Phone" />
                <TextInput
                  id="phone"
                  type="tel"
                  class="mt-1 block w-full"
                  v-model="form.phone"
                />
                <InputError class="mt-2" :message="form.errors.phone" />
              </div>

              <div>
                <InputLabel for="address" value="Address" />
                <TextArea
                  id="address"
                  class="mt-1 block w-full"
                  v-model="form.address"
                  rows="3"
                />
                <InputError class="mt-2" :message="form.errors.address" />
              </div>

              <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                <Transition
                  enter-active-class="transition ease-in-out"
                  enter-from-class="opacity-0"
                  leave-active-class="transition ease-in-out"
                  leave-to-class="opacity-0"
                >
                  <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                </Transition>
              </div>
            </form>
          </section>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
          <section>
            <header>
              <h2 class="text-lg font-medium text-gray-900">Update Password</h2>
              <p class="mt-1 text-sm text-gray-600">
                Ensure your account is using a long, random password to stay secure.
              </p>
            </header>

            <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
              <div>
                <InputLabel for="current_password" value="Current Password" />
                <TextInput
                  id="current_password"
                  type="password"
                  class="mt-1 block w-full"
                  v-model="passwordForm.current_password"
                  autocomplete="current-password"
                />
                <InputError class="mt-2" :message="passwordForm.errors.current_password" />
              </div>

              <div>
                <InputLabel for="password" value="New Password" />
                <TextInput
                  id="password"
                  type="password"
                  class="mt-1 block w-full"
                  v-model="passwordForm.password"
                  autocomplete="new-password"
                />
                <InputError class="mt-2" :message="passwordForm.errors.password" />
              </div>

              <div>
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <TextInput
                  id="password_confirmation"
                  type="password"
                  class="mt-1 block w-full"
                  v-model="passwordForm.password_confirmation"
                  autocomplete="new-password"
                />
                <InputError class="mt-2" :message="passwordForm.errors.password_confirmation" />
              </div>

              <div class="flex items-center gap-4">
                <PrimaryButton :disabled="passwordForm.processing">Save</PrimaryButton>
                <Transition
                  enter-active-class="transition ease-in-out"
                  enter-from-class="opacity-0"
                  leave-active-class="transition ease-in-out"
                  leave-to-class="opacity-0"
                >
                  <p v-if="passwordForm.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                </Transition>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import { PencilIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
  user: Object,
})

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  phone: props.user.phone,
  address: props.user.address,
  avatar: null,
  avatar_url: props.user.avatar_url,
})

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const updateProfile = () => {
  form.post(route('profile.update'), {
    preserveScroll: true,
    onSuccess: () => form.reset('password'),
  })
}

const updatePassword = () => {
  passwordForm.put(route('password.update'), {
    preserveScroll: true,
    onSuccess: () => passwordForm.reset(),
  })
}

const updateAvatar = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.avatar = file
    form.avatar_url = URL.createObjectURL(file)
    form.post(route('profile.update-avatar'), {
      preserveScroll: true,
      onSuccess: () => {
        form.avatar = null
      },
    })
  }
}
</script>
