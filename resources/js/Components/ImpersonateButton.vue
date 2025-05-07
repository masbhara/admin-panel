<template>
    <div>
        <!-- Tombol Impersonate untuk admin -->
        <Link v-if="!isImpersonating && canImpersonate" 
            :href="route('admin.impersonate', user.id)"
            class="inline-flex items-center px-3 py-1 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <UserIcon class="w-4 h-4 mr-2" />
            Impersonate
        </Link>

        <!-- Tombol Stop Impersonating -->
        <Link v-if="isImpersonating" 
            :href="route('admin.impersonate.leave')"
            class="inline-flex items-center px-3 py-1 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            <ArrowLeftOnRectangleIcon class="w-4 h-4 mr-2" />
            Stop Impersonating
        </Link>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { UserIcon, ArrowLeftOnRectangleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    user: {
        type: Object,
        required: true
    },
    canImpersonate: {
        type: Boolean,
        default: false
    },
    isImpersonating: {
        type: Boolean,
        default: false
    }
})
</script>
