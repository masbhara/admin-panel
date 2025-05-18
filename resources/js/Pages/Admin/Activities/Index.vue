<template>
  <AdminLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-white">
        Aktivitas Sistem
      </h2>
    </template>

    <div class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Activity filter section -->
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
        <div class="flex flex-wrap gap-2 items-center">
          <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter:</span>
          <button 
            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200 dark:hover:bg-indigo-800 transition-colors"
          >
            Semua
          </button>
          <button 
            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
          >
            Dibuat
          </button>
          <button 
            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
          >
            Diupdate
          </button>
          <button 
            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
          >
            Dihapus
          </button>
        </div>
      </div>

      <!-- Activity list -->
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
        <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
          <h3 class="text-base font-medium text-gray-900 dark:text-white">Riwayat Aktivitas</h3>
        </div>

        <div v-if="activities.data.length === 0" class="p-8 text-center">
          <div class="mx-auto w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
            <ClockIcon class="w-6 h-6 text-gray-400 dark:text-gray-500" />
          </div>
          <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada aktivitas</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada aktivitas yang tercatat dalam sistem.</p>
        </div>

        <div v-else>
          <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <div v-for="activity in activities.data" :key="activity.id" class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
              <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                    <component 
                      :is="getActivityIcon(activity.type)" 
                      class="w-5 h-5 text-indigo-600 dark:text-indigo-400" 
                      aria-hidden="true"
                    />
                  </div>
                </div>
                <div class="flex-grow">
                  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-2 mb-1 sm:mb-0">
                      <span class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ activity.causer?.name || 'System' }}
                      </span>
                      <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ activity.description }}
                      </span>
                    </div>
                    <div class="flex items-center">
                      <span class="inline-flex items-center text-xs text-gray-500 dark:text-gray-400">
                        <ClockIcon class="mr-1 h-3 w-3" />
                        {{ activity.date }}
                      </span>
                    </div>
                  </div>
                  <div v-if="activity.properties && Object.keys(activity.properties).length > 0" class="mt-2">
                    <div class="rounded-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                      <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 text-xs font-medium text-gray-800 dark:text-gray-200 flex items-center justify-between">
                        <span>Detail Perubahan</span>
                        <button v-if="!expandedActivities[activity.id]" @click="toggleActivity(activity.id)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                          <ChevronDownIcon class="h-4 w-4" />
                        </button>
                        <button v-else @click="toggleActivity(activity.id)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                          <ChevronUpIcon class="h-4 w-4" />
                        </button>
                      </div>
                      <div v-if="expandedActivities[activity.id]" class="text-xs text-gray-700 dark:text-gray-300">
                        <div v-if="activity.properties.old && Object.keys(activity.properties.old).length > 0" class="border-b border-gray-200 dark:border-gray-700 px-4 py-3">
                          <strong class="block mb-1 text-gray-900 dark:text-gray-100">Sebelum:</strong>
                          <pre class="mt-1 bg-gray-50 dark:bg-gray-700 p-2 rounded overflow-auto max-h-40">{{ formatProperties(activity.properties.old) }}</pre>
                        </div>
                        <div v-if="activity.properties.attributes && Object.keys(activity.properties.attributes).length > 0" class="px-4 py-3">
                          <strong class="block mb-1 text-gray-900 dark:text-gray-100">Sesudah:</strong>
                          <pre class="mt-1 bg-gray-50 dark:bg-gray-700 p-2 rounded overflow-auto max-h-40">{{ formatProperties(activity.properties.attributes) }}</pre>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination with better spacing -->
      <div class="flex justify-center">
        <Pagination :links="activities.links" />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { 
  ClockIcon, 
  PencilIcon, 
  PlusCircleIcon, 
  TrashIcon, 
  UserMinusIcon,
  ChevronDownIcon,
  ChevronUpIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  activities: Object,
})

const expandedActivities = ref({})

const toggleActivity = (id) => {
  expandedActivities.value[id] = !expandedActivities.value[id]
}

const getActivityIcon = (type) => {
  if (type.toLowerCase().includes('created')) {
    return PlusCircleIcon
  } else if (type.toLowerCase().includes('updated')) {
    return PencilIcon
  } else if (type.toLowerCase().includes('deleted')) {
    return TrashIcon
  } else if (type.toLowerCase().includes('deactivated')) {
    return UserMinusIcon
  } else {
    return ClockIcon
  }
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
