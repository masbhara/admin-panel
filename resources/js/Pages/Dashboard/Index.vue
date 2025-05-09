<template>
  <UserLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Dashboard Saya
      </h2>
    </template>

    <div class="space-y-6">
      <!-- Profile Overview -->
      <div v-if="user" class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center space-x-4">
          <div class="flex-shrink-0">
            <img 
              :src="user.profile_photo_url" 
              :alt="user.name"
              class="h-16 w-16 rounded-full object-cover"
            >
          </div>
          <div>
            <h2 class="text-xl font-semibold text-gray-800">{{ user.name }}</h2>
            <p class="text-sm text-gray-600">{{ user.email }}</p>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Profil Saya</h3>
            <Link 
              :href="route('profile.edit')"
              class="text-indigo-600 hover:text-indigo-900"
            >
              Edit
            </Link>
          </div>
          <p class="mt-2 text-sm text-gray-600">
            Kelola informasi profil dan preferensi akun Anda
          </p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Keamanan</h3>
            <Link 
              :href="route('profile.edit')"
              class="text-indigo-600 hover:text-indigo-900"
            >
              Pengaturan
            </Link>
          </div>
          <p class="mt-2 text-sm text-gray-600">
            Atur password dan pengaturan keamanan akun
          </p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Notifikasi</h3>
            <Link 
              :href="route('notifications')"
              class="text-indigo-600 hover:text-indigo-900"
            >
              Lihat Semua
            </Link>
          </div>
          <p class="mt-2 text-sm text-gray-600">
            {{ unreadNotifications }} notifikasi belum dibaca
          </p>
        </div>
      </div>

      <!-- Recent Activities -->
      <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900">Aktivitas Terbaru</h3>
          <Link 
            :href="route('activities')"
            class="text-sm text-indigo-600 hover:text-indigo-900"
          >
            Lihat Semua
          </Link>
        </div>
        <div class="space-y-4">
          <div v-for="activity in recentActivities" :key="activity.id" class="flex items-start space-x-3">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center">
                <icon :name="activity.icon" class="w-4 h-4 text-indigo-600" />
              </div>
            </div>
            <div>
              <p class="text-sm text-gray-600">{{ activity.description }}</p>
              <p class="text-xs text-gray-500">{{ activity.created_at }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'

const props = defineProps({
  unreadNotifications: Number,
  recentActivities: Array
})

const page = usePage()
const user = computed(() => page.props.auth?.user)
</script>
