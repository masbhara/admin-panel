<template>
  <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
    <!-- Mobile pagination -->
    <div class="flex flex-1 justify-between sm:hidden">
      <button
        type="button"
        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
        :disabled="currentPage === 1"
        @click="$emit('update:modelValue', currentPage - 1)"
      >
        Previous
      </button>
      <button
        type="button"
        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
        :disabled="currentPage === totalPages"
        @click="$emit('update:modelValue', currentPage + 1)"
      >
        Next
      </button>
    </div>

    <!-- Desktop pagination -->
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <!-- Info -->
      <div>
        <p class="text-sm text-gray-700">
          Menampilkan
          <span class="font-medium">{{ from }}</span>
          sampai
          <span class="font-medium">{{ to }}</span>
          dari
          <span class="font-medium">{{ total }}</span>
          hasil
        </p>
      </div>

      <!-- Per page selector -->
      <div v-if="showPerPage" class="mx-4">
        <select
          v-model="selectedPerPage"
          class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6"
          @change="handlePerPageChange"
        >
          <option
            v-for="option in perPageOptions"
            :key="option"
            :value="option"
          >
            {{ option }} per halaman
          </option>
        </select>
      </div>

      <!-- Navigation -->
      <div>
        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
          <!-- First page -->
          <button
            type="button"
            class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
            :disabled="currentPage === 1"
            @click="$emit('update:modelValue', 1)"
          >
            <span class="sr-only">First</span>
            <ChevronDoubleLeftIcon class="h-5 w-5" aria-hidden="true" />
          </button>

          <!-- Previous page -->
          <button
            type="button"
            class="relative inline-flex items-center px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
            :disabled="currentPage === 1"
            @click="$emit('update:modelValue', currentPage - 1)"
          >
            <span class="sr-only">Previous</span>
            <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
          </button>

          <!-- Page numbers -->
          <template v-for="page in visiblePages" :key="page">
            <span
              v-if="page === '...'"
              class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0"
            >
              ...
            </span>
            <button
              v-else
              type="button"
              :class="[
                'relative inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20',
                page === currentPage
                  ? 'z-10 bg-primary-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600'
                  : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0'
              ]"
              @click="$emit('update:modelValue', page)"
            >
              {{ page }}
            </button>
          </template>

          <!-- Next page -->
          <button
            type="button"
            class="relative inline-flex items-center px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
            :disabled="currentPage === totalPages"
            @click="$emit('update:modelValue', currentPage + 1)"
          >
            <span class="sr-only">Next</span>
            <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
          </button>

          <!-- Last page -->
          <button
            type="button"
            class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
            :disabled="currentPage === totalPages"
            @click="$emit('update:modelValue', totalPages)"
          >
            <span class="sr-only">Last</span>
            <ChevronDoubleRightIcon class="h-5 w-5" aria-hidden="true" />
          </button>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import {
  ChevronLeftIcon,
  ChevronRightIcon,
  ChevronDoubleLeftIcon,
  ChevronDoubleRightIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  modelValue: {
    type: Number,
    required: true
  },
  total: {
    type: Number,
    required: true
  },
  perPage: {
    type: Number,
    default: 10
  },
  showPerPage: {
    type: Boolean,
    default: true
  },
  perPageOptions: {
    type: Array,
    default: () => [10, 25, 50, 100]
  }
})

const emit = defineEmits(['update:modelValue', 'per-page-change'])

const currentPage = computed(() => props.modelValue)
const selectedPerPage = ref(props.perPage)

// Computed values
const totalPages = computed(() => Math.ceil(props.total / props.perPage))
const from = computed(() => ((currentPage.value - 1) * props.perPage) + 1)
const to = computed(() => Math.min(currentPage.value * props.perPage, props.total))

// Visible pages logic
const visiblePages = computed(() => {
  const delta = 2
  const range = []
  const rangeWithDots = []
  let l

  range.push(1)

  for (let i = currentPage.value - delta; i <= currentPage.value + delta; i++) {
    if (i < totalPages.value && i > 1) {
      range.push(i)
    }
  }

  range.push(totalPages.value)

  for (let i of range) {
    if (l) {
      if (i - l === 2) {
        rangeWithDots.push(l + 1)
      } else if (i - l !== 1) {
        rangeWithDots.push('...')
      }
    }
    rangeWithDots.push(i)
    l = i
  }

  return rangeWithDots
})

// Methods
const handlePerPageChange = () => {
  emit('per-page-change', selectedPerPage.value)
}
</script>
