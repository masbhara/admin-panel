<template>
  <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
      <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
        Forgot your password?
      </h2>
      
      <p class="mt-2 text-center text-sm text-gray-600">
        Enter your email address and we'll send you a link to reset your password.
      </p>
      
      <div class="mt-8">
        <ForgotPasswordForm />
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import ForgotPasswordForm from '@/Components/Auth/ForgotPasswordForm.vue'

defineOptions({
  layout: null,
})
</script>
