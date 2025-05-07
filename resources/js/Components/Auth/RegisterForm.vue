<template>
  <form @submit.prevent="submit" class="space-y-4">
    <div>
      <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
      <input
        id="name"
        v-model="form.name"
        type="text"
        required
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
      />
      <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
    </div>

    <div>
      <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
      <input
        id="email"
        v-model="form.email"
        type="email"
        required
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
      />
      <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
    </div>

    <div>
      <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
      <input
        id="password"
        v-model="form.password"
        type="password"
        required
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
      />
      <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
    </div>

    <div>
      <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
        Confirm Password
      </label>
      <input
        id="password_confirmation"
        v-model="form.password_confirmation"
        type="password"
        required
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
      />
    </div>

    <div>
      <button
        type="submit"
        :disabled="processing"
        class="w-full rounded-md bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600"
      >
        {{ processing ? 'Registering...' : 'Register' }}
      </button>
    </div>

    <div class="text-center">
      <Link
        :href="route('login')"
        class="text-sm font-medium text-primary-600 hover:text-primary-500"
      >
        Already have an account? Log in
      </Link>
    </div>
  </form>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

const processing = ref(false)
const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  processing.value = true
  form.post(route('register'), {
    onFinish: () => {
      processing.value = false
    },
  })
}
</script>
