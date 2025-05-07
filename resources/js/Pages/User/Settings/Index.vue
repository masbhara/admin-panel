<template>
  <UserLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">User Settings</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6">
            <form @submit.prevent="form.post(route('user.settings.update'))">
              <!-- Profile Section -->
              <div class="mb-8">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Profile</h3>
                <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                  <div class="sm:col-span-4">
                    <InputLabel for="name" value="Name" />
                    <div class="mt-1">
                      <TextInput
                        id="name"
                        v-model="form.profile.name"
                        type="text"
                        class="block w-full"
                        disabled
                      />
                    </div>
                  </div>

                  <div class="sm:col-span-4">
                    <InputLabel for="email" value="Email" />
                    <div class="mt-1">
                      <TextInput
                        id="email"
                        v-model="form.profile.email"
                        type="email"
                        class="block w-full"
                        disabled
                      />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Notifications Section -->
              <div class="mb-8">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Notifications</h3>
                <div class="mt-6 space-y-6">
                  <div class="flex items-start">
                    <div class="flex h-6 items-center">
                      <Switch
                        v-model="form.notifications.email_notifications"
                        :class="[
                          form.notifications.email_notifications ? 'bg-primary-600' : 'bg-gray-200',
                          'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2'
                        ]"
                      >
                        <span
                          :class="[
                            form.notifications.email_notifications ? 'translate-x-5' : 'translate-x-0',
                            'pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                          ]"
                        >
                          <span
                            :class="[
                              form.notifications.email_notifications
                                ? 'opacity-0 duration-100 ease-out'
                                : 'opacity-100 duration-200 ease-in',
                              'absolute inset-0 flex h-full w-full items-center justify-center transition-opacity'
                            ]"
                            aria-hidden="true"
                          >
                            <svg class="h-3 w-3 text-gray-400" fill="none" viewBox="0 0 12 12">
                              <path
                                d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                              />
                            </svg>
                          </span>
                          <span
                            :class="[
                              form.notifications.email_notifications
                                ? 'opacity-100 duration-200 ease-in'
                                : 'opacity-0 duration-100 ease-out',
                              'absolute inset-0 flex h-full w-full items-center justify-center transition-opacity'
                            ]"
                            aria-hidden="true"
                          >
                            <svg class="h-3 w-3 text-primary-600" fill="currentColor" viewBox="0 0 12 12">
                              <path d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z" />
                            </svg>
                          </span>
                        </span>
                      </Switch>
                    </div>
                    <div class="ml-3">
                      <label for="email-notifications" class="text-sm font-medium text-gray-700">
                        Email notifications
                      </label>
                      <p class="text-sm text-gray-500">Receive email notifications about your account.</p>
                    </div>
                  </div>

                  <div class="flex items-start">
                    <div class="flex h-6 items-center">
                      <Switch
                        v-model="form.notifications.push_notifications"
                        :class="[
                          form.notifications.push_notifications ? 'bg-primary-600' : 'bg-gray-200',
                          'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2'
                        ]"
                      >
                        <span
                          :class="[
                            form.notifications.push_notifications ? 'translate-x-5' : 'translate-x-0',
                            'pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                          ]"
                        >
                          <span
                            :class="[
                              form.notifications.push_notifications
                                ? 'opacity-0 duration-100 ease-out'
                                : 'opacity-100 duration-200 ease-in',
                              'absolute inset-0 flex h-full w-full items-center justify-center transition-opacity'
                            ]"
                            aria-hidden="true"
                          >
                            <svg class="h-3 w-3 text-gray-400" fill="none" viewBox="0 0 12 12">
                              <path
                                d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                              />
                            </svg>
                          </span>
                          <span
                            :class="[
                              form.notifications.push_notifications
                                ? 'opacity-100 duration-200 ease-in'
                                : 'opacity-0 duration-100 ease-out',
                              'absolute inset-0 flex h-full w-full items-center justify-center transition-opacity'
                            ]"
                            aria-hidden="true"
                          >
                            <svg class="h-3 w-3 text-primary-600" fill="currentColor" viewBox="0 0 12 12">
                              <path d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z" />
                            </svg>
                          </span>
                        </span>
                      </Switch>
                    </div>
                    <div class="ml-3">
                      <label for="push-notifications" class="text-sm font-medium text-gray-700">
                        Push notifications
                      </label>
                      <p class="text-sm text-gray-500">Receive push notifications in your browser.</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Preferences Section -->
              <div class="mb-8">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Preferences</h3>
                <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                  <div class="sm:col-span-3">
                    <InputLabel for="language" value="Language" />
                    <div class="mt-1">
                      <Select
                        id="language"
                        v-model="form.preferences.language"
                        class="block w-full"
                      >
                        <option value="id">Bahasa Indonesia</option>
                        <option value="en">English</option>
                      </Select>
                    </div>
                  </div>

                  <div class="sm:col-span-3">
                    <InputLabel for="timezone" value="Timezone" />
                    <div class="mt-1">
                      <Select
                        id="timezone"
                        v-model="form.preferences.timezone"
                        class="block w-full"
                      >
                        <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                        <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                        <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                      </Select>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Security Section -->
              <div class="mb-8">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Security</h3>
                <div class="mt-6">
                  <div class="flex items-start">
                    <div class="flex h-6 items-center">
                      <Switch
                        v-model="form.security.two_factor_enabled"
                        :class="[
                          form.security.two_factor_enabled ? 'bg-primary-600' : 'bg-gray-200',
                          'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2'
                        ]"
                        disabled
                      >
                        <span
                          :class="[
                            form.security.two_factor_enabled ? 'translate-x-5' : 'translate-x-0',
                            'pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                          ]"
                        />
                      </Switch>
                    </div>
                    <div class="ml-3">
                      <label for="two-factor" class="text-sm font-medium text-gray-700">
                        Two-factor authentication
                      </label>
                      <p class="text-sm text-gray-500">Add an extra layer of security to your account.</p>
                    </div>
                  </div>

                  <div class="mt-6">
                    <p class="text-sm text-gray-500">
                      Last password change:
                      <span class="font-medium text-gray-900">{{ settings.security.last_password_change || 'Never' }}</span>
                    </p>
                  </div>
                </div>
              </div>

              <!-- Save Button -->
              <div class="flex justify-end">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                  Save Changes
                </PrimaryButton>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import Select from '@/Components/Select.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { Switch } from '@headlessui/vue'

const props = defineProps({
  settings: Object,
})

const form = useForm({
  profile: { ...props.settings.profile },
  notifications: { ...props.settings.notifications },
  preferences: { ...props.settings.preferences },
  security: { ...props.settings.security },
})
</script>
