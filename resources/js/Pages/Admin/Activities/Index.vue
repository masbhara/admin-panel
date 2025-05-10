<template>
  <AdminLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-white">
        Aktivitas Sistem
      </h2>
    </template>

    <div class="space-y-6">
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg divide-y divide-gray-200 dark:divide-gray-700">
        <div v-if="activities.data.length === 0" class="p-6 text-center text-gray-500 dark:text-gray-400">
          Tidak ada aktivitas
        </div>

        <div v-for="activity in activities.data" :key="activity.id" class="p-6">
          <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                <icon :name="getActivityIcon(activity.type)" class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
              </div>
            </div>
            <div class="flex-grow">
              <div class="flex items-center justify-between">
                <div class="flex flex-col">
                  <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ activity.causer?.name || 'System' }}
                    </span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                      {{ activity.description }}
                    </span>
                  </div>
                  <span class="text-xs text-gray-500 dark:text-gray-400">{{ activity.date }}</span>
                </div>
              </div>
              <div v-if="activity.properties && Object.keys(activity.properties).length > 0" class="mt-2">
                <div class="text-xs text-gray-500 dark:text-gray-400">
                  <div v-if="activity.properties.old && Object.keys(activity.properties.old).length > 0" class="mt-1">
                    <strong>Sebelum:</strong>
                    <pre class="mt-1 text-xs bg-gray-50 dark:bg-gray-700 p-2 rounded">{{ formatProperties(activity.properties.old) }}</pre>
                  </div>
                  <div v-if="activity.properties.attributes && Object.keys(activity.properties.attributes).length > 0" class="mt-1">
                    <strong>Sesudah:</strong>
                    <pre class="mt-1 text-xs bg-gray-50 dark:bg-gray-700 p-2 rounded">{{ formatProperties(activity.properties.attributes) }}</pre>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <Pagination :links="activities.links" />
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  activities: Object,
})

const getActivityIcon = (type) => {
  const icons = {
    'created': 'plus-circle',
    'updated': 'pencil',
    'deleted': 'trash',
    'deactivated': 'user-minus',
    'default': 'clock'
  }
  
  for (const [key, icon] of Object.entries(icons)) {
    if (type.toLowerCase().includes(key)) {
      return icon
    }
  }
  return icons.default
}

const formatProperties = (properties) => {
  const formatted = {}
  for (const [key, value] of Object.entries(properties)) {
    if (key !== 'updated_at' && key !== 'created_at') {
      formatted[key] = value
    }
  }
  return JSON.stringify(formatted, null, 2)
}
</script>
