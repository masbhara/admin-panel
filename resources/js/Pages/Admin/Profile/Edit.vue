<template>
  <AdminLayout title="Edit Profile">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Profile Information -->
        <div class="p-4 sm:p-8 bg-white dark:bg-primary-600 shadow sm:rounded-lg">
          <section>
            <header>
              <h2 class="text-lg font-medium text-gray-900 dark:text-white">Profile Information</h2>
              <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                Update your account's profile information and email address.
              </p>
            </header>

            <form @submit.prevent="updateProfile" class="mt-6 space-y-6">
              <div>
                <InputLabel for="name" value="Name" class="dark:text-white" />
                <TextInput
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="mt-1 block w-full dark:bg-primary-700 dark:text-white dark:border-gray-600"
                  required
                  autofocus
                />
                <InputError :message="form.errors.name" class="mt-2" />
              </div>

              <div>
                <InputLabel for="email" value="Email" class="dark:text-white" />
                <TextInput
                  id="email"
                  v-model="form.email"
                  type="email"
                  class="mt-1 block w-full dark:bg-primary-700 dark:text-white dark:border-gray-600"
                  required
                />
                <InputError :message="form.errors.email" class="mt-2" />
              </div>

              <div>
                <InputLabel for="avatar" value="Profile Photo" class="dark:text-white" />
                <div class="mt-2 flex items-center gap-x-3">
                  <div class="relative">
                    <img
                      v-if="form.avatar_url"
                      :src="form.avatar_url"
                      class="h-16 w-16 rounded-full object-cover"
                      alt="Profile Photo"
                    />
                    <span
                      v-else
                      class="inline-block h-16 w-16 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700"
                    >
                      <svg
                        class="h-full w-full text-gray-300 dark:text-gray-500"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                      </svg>
                    </span>
                    <button
                      type="button"
                      @click="$refs.avatarInput.click()"
                      class="absolute bottom-0 right-0 bg-white dark:bg-primary-800 rounded-full p-1 shadow-sm border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-primary-700"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 dark:text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                      </svg>
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
                <InputError :message="form.errors.avatar" class="mt-2" />
              </div>

              <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                <Transition
                  enter-active-class="transition ease-in-out"
                  enter-from-class="opacity-0"
                  leave-active-class="transition ease-in-out"
                  leave-to-class="opacity-0"
                >
                  <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-300">
                    Saved.
                  </p>
                </Transition>
              </div>
            </form>
          </section>
        </div>

        <!-- Update Password -->
        <div class="p-4 sm:p-8 bg-white dark:bg-primary-600 shadow sm:rounded-lg">
          <section>
            <header>
              <h2 class="text-lg font-medium text-gray-900 dark:text-white">Update Password</h2>
              <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                Ensure your account is using a long, random password to stay secure.
              </p>
            </header>

            <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
              <div>
                <InputLabel for="current_password" value="Current Password" class="dark:text-white" />
                <TextInput
                  id="current_password"
                  v-model="passwordForm.current_password"
                  type="password"
                  class="mt-1 block w-full dark:bg-primary-700 dark:text-white dark:border-gray-600"
                  autocomplete="current-password"
                />
                <InputError :message="passwordForm.errors.current_password" class="mt-2" />
              </div>

              <div>
                <InputLabel for="password" value="New Password" class="dark:text-white" />
                <TextInput
                  id="password"
                  v-model="passwordForm.password"
                  type="password"
                  class="mt-1 block w-full dark:bg-primary-700 dark:text-white dark:border-gray-600"
                  autocomplete="new-password"
                />
                <InputError :message="passwordForm.errors.password" class="mt-2" />
              </div>

              <div>
                <InputLabel for="password_confirmation" value="Confirm Password" class="dark:text-white" />
                <TextInput
                  id="password_confirmation"
                  v-model="passwordForm.password_confirmation"
                  type="password"
                  class="mt-1 block w-full dark:bg-primary-700 dark:text-white dark:border-gray-600"
                  autocomplete="new-password"
                />
                <InputError :message="passwordForm.errors.password_confirmation" class="mt-2" />
              </div>

              <div class="flex items-center gap-4">
                <PrimaryButton :disabled="passwordForm.processing">Save</PrimaryButton>
                <Transition
                  enter-active-class="transition ease-in-out"
                  enter-from-class="opacity-0"
                  leave-active-class="transition ease-in-out"
                  leave-to-class="opacity-0"
                >
                  <p v-if="passwordForm.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-300">
                    Saved.
                  </p>
                </Transition>
              </div>
            </form>
          </section>
        </div>

        <!-- Delete Account -->
        <div class="p-4 sm:p-8 bg-white dark:bg-primary-600 shadow sm:rounded-lg">
          <section class="space-y-6">
            <header>
              <h2 class="text-lg font-medium text-gray-900 dark:text-white">Delete Account</h2>
              <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                Once your account is deleted, all of its resources and data will be permanently deleted.
              </p>
            </header>

            <DangerButton @click="confirmUserDeletion">Delete Account</DangerButton>

            <Modal :show="confirmingUserDeletion" @close="closeModal">
              <div class="p-6 bg-white dark:bg-primary-600">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                  Are you sure you want to delete your account?
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                  Once your account is deleted, all of its resources and data will be permanently deleted.
                  Please enter your password to confirm you would like to permanently delete your account.
                </p>

                <div class="mt-6">
                  <InputLabel for="password" value="Password" class="sr-only" />
                  <TextInput
                    id="password"
                    v-model="passwordForm.password"
                    type="password"
                    class="mt-1 block w-3/4 dark:bg-primary-700 dark:text-white dark:border-gray-600"
                    placeholder="Password"
                    @keyup.enter="deleteUser"
                  />
                  <InputError :message="passwordForm.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                  <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                  <DangerButton
                    class="ml-3"
                    :class="{ 'opacity-25': passwordForm.processing }"
                    :disabled="passwordForm.processing"
                    @click="deleteUser"
                  >
                    Delete Account
                  </DangerButton>
                </div>
              </div>
            </Modal>
          </section>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, nextTick } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import DangerButton from '@/Components/DangerButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  avatar: null,
  avatar_url: props.user.avatar_url || '',
  _method: 'PATCH',
})

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const confirmingUserDeletion = ref(false)

const updateProfile = () => {
  let url = route('admin.profile.update');
  
  form.post(url, {
    onSuccess: (response) => {
      // Reload halaman untuk mendapatkan data terbaru
      window.location.reload();
    },
    forceFormData: true,
    preserveScroll: true,
  });
}

const updatePassword = () => {
  passwordForm.put('/admin/password', {
    preserveScroll: true,
    onSuccess: () => passwordForm.reset(),
  })
}

const confirmUserDeletion = () => {
  confirmingUserDeletion.value = true
}

const closeModal = () => {
  confirmingUserDeletion.value = false
  passwordForm.reset()
}

const deleteUser = () => {
  passwordForm.delete(route('admin.profile.destroy'), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: () => passwordForm.focus('password'),
  })
}

const updateAvatar = (event) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      form.avatar = file;
      form.avatar_url = e.target.result;
    };
    reader.readAsDataURL(file);
  }
}
</script>
