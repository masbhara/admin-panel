<template>
  <div class="bg-white shadow-sm rounded-lg overflow-hidden">
    <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
      <h3 class="text-lg font-medium leading-6 text-gray-900">{{ title }}</h3>
      <button
        type="button"
        class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
        @click="$emit('add')"
      >
        + Ajouter une ligne
      </button>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th
              v-for="header in headers"
              :key="header.key"
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              {{ header.label }}
            </th>
            <th scope="col" class="relative px-6 py-3">
              <span class="sr-only">Actions</span>
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="(row, index) in data" :key="index">
            <td
              v-for="header in headers"
              :key="header.key"
              class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
            >
              <div class="flex items-center">
                <span>{{ row[header.key] }}</span>
                <span v-if="header.unit" class="ml-1 text-gray-500">{{ header.unit }}</span>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                type="button"
                class="text-red-600 hover:text-red-900"
                @click="$emit('delete', row)"
              >
                <TrashIcon class="h-5 w-5" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { TrashIcon } from '@heroicons/vue/24/outline'

defineProps({
  title: {
    type: String,
    required: true
  },
  headers: {
    type: Array,
    required: true
  },
  data: {
    type: Array,
    required: true
  }
})

defineEmits(['add', 'delete'])
</script>
