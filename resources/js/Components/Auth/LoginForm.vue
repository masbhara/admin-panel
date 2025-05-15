<template>
  <form @submit.prevent="submit" class="space-y-4">
    <div>
      <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
      <input
        id="email"
        v-model="form.email"
        type="email"
        required
        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-white"
      />
    </div>

    <div>
      <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
      <input
        id="password"
        v-model="form.password"
        type="password"
        required
        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-white"
      />
    </div>

    <div class="flex items-center justify-between">
      <div class="flex items-center">
        <input
          id="remember"
          v-model="form.remember"
          type="checkbox"
          class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500 dark:bg-gray-700"
        />
        <label for="remember" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Remember me</label>
      </div>

      <Link
        :href="route('password.request')"
        class="text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300"
      >
        Forgot password?
      </Link>
    </div>

    <div>
      <button
        type="submit"
        :disabled="processing"
        class="w-full rounded-md bg-primary-600 dark:bg-primary-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 dark:hover:bg-primary-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 dark:focus-visible:outline-primary-400 disabled:opacity-50"
      >
        {{ processing ? 'Logging in...' : 'Log in' }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

const processing = ref(false)
const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  processing.value = true
  form.post(route('login'), {
    onFinish: () => {
      processing.value = false
    },
  })
}
</script>
