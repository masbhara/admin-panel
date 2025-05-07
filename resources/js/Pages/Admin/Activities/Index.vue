<template>
  <AdminLayout :title="'Activity Log'">
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-white">Activity Log</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white dark:bg-primary-600 shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="flow-root">
              <ul role="list" class="-mb-8">
                <li v-for="activity in activities.data" :key="activity.id">
                  <div class="relative pb-8">
                    <span
                      v-if="!isLastItem(activity)"
                      class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700"
                      aria-hidden="true"
                    />
                    <div class="relative flex space-x-3">
                      <div>
                        <span
                          :class="[
                            getActivityColor(activity.type || ''),
                            'h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-primary-600'
                          ]"
                        >
                          <component
                            :is="getActivityIcon(activity.type || '')"
                            class="h-5 w-5 text-white"
                            aria-hidden="true"
                          />
                        </span>
                      </div>
                      <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                        <div>
                          <p class="text-sm text-gray-500 dark:text-gray-300">
                            <span class="font-medium text-gray-900 dark:text-white">
                              {{ activity.causer?.name || 'System' }}
                            </span>
                            {{ activity.description }}
                            <span v-if="activity.subject_type" class="font-medium text-gray-900 dark:text-white">
                              {{ activity.subject_type }}
                            </span>
                          </p>
                          <div class="mt-2 text-sm text-gray-700 dark:text-gray-200" v-if="activity.properties && Object.keys(activity.properties).length > 0">
                            <pre class="whitespace-pre-wrap bg-gray-50 dark:bg-primary-700 p-2 rounded">{{ JSON.stringify(activity.properties, null, 2) }}</pre>
                          </div>
                        </div>
                        <div class="whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-300">
                          <time :datetime="activity.datetime">{{ activity.date }}</time>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
              <Pagination :links="activities.links" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import {
  UserPlusIcon,
  PencilSquareIcon,
  TrashIcon,
  UserMinusIcon,
  ClockIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  activities: Object,
})

const getActivityIcon = (type) => {
  if (!type || typeof type !== 'string') return ClockIcon;
  if (type.includes('created')) return UserPlusIcon;
  if (type.includes('updated')) return PencilSquareIcon;
  if (type.includes('deleted')) return TrashIcon;
  if (type.includes('deactivated')) return UserMinusIcon;
  return ClockIcon;
}

const getActivityColor = (type) => {
  if (!type || typeof type !== 'string') return 'bg-gray-500';
  if (type.includes('created')) return 'bg-green-500';
  if (type.includes('updated')) return 'bg-blue-500';
  if (type.includes('deleted')) return 'bg-red-500';
  if (type.includes('deactivated')) return 'bg-yellow-500';
  return 'bg-gray-500';
}

const isLastItem = (activity) => {
  return props.activities.data.indexOf(activity) === props.activities.data.length - 1;
}
</script>
