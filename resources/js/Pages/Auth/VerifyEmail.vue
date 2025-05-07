<template>
  <Head title="Email Verification" />

  <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
      <div v-if="verificationLinkSent" class="mb-4 font-medium text-sm text-green-600">
        A new verification link has been sent to your email address.
      </div>

      <div class="mb-4 text-sm text-gray-600">
        Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
      </div>

      <div class="mt-4 flex items-center justify-between">
        <button
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 active:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150"
          @click="submit"
        >
          Resend Verification Email
        </button>

        <Link
          :href="route('logout')"
          method="post"
          as="button"
          class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
        >
          Log Out
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'

defineProps({
  status: String,
})

const form = useForm({})
const verificationLinkSent = computed(() => status === 'verification-link-sent')

const submit = () => {
  form.post(route('verification.send'))
}
</script>
