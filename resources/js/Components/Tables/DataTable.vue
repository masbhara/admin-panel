<template>
  <div class="flex flex-col">
    <!-- Table Header with Search and Actions -->
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <!-- Search -->
      <div class="flex-1 max-w-xs">
        <div class="relative">
          <input
            type="text"
            :placeholder="searchPlaceholder"
            v-model="searchQuery"
            class="block w-full rounded-md border-gray-300 pl-10 pr-3 py-2 text-sm focus:border-primary-500 focus:ring-primary-500"
          >
          <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex items-center space-x-2">
        <slot name="actions"></slot>
        <button
          v-if="showRefresh"
          type="button"
          class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
          @click="$emit('refresh')"
        >
          <ArrowPathIcon class="h-5 w-5 text-gray-400" />
        </button>
      </div>
    </div>

    <!-- Table Wrapper -->
    <div class="relative overflow-hidden rounded-lg border border-gray-200 bg-white">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <!-- Table Header -->
          <thead class="bg-gray-50">
            <tr>
              <!-- Checkbox column if selectable -->
              <th v-if="selectable" scope="col" class="relative w-12 px-6 sm:w-16 sm:px-8">
                <input
                  type="checkbox"
                  class="absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                  :checked="isAllSelected"
                  :indeterminate="isIndeterminate"
                  @change="toggleAll"
                >
              </th>
              
              <!-- Column headers -->
              <th
                v-for="column in columns"
                :key="column.key"
                scope="col"
                :class="[
                  'px-3 py-3.5 text-left text-sm font-semibold text-gray-900',
                  column.sortable ? 'cursor-pointer select-none' : '',
                  column.width ? column.width : ''
                ]"
                @click="column.sortable && sort(column.key)"
              >
                <div class="group inline-flex items-center gap-x-2">
                  {{ column.label }}
                  <span
                    v-if="column.sortable"
                    :class="[
                      'ml-2 flex-none rounded text-gray-400',
                      sortKey === column.key ? 'text-gray-900' : ''
                    ]"
                  >
                    <ChevronUpIcon
                      v-if="sortKey === column.key && sortOrder === 'asc'"
                      class="h-4 w-4"
                    />
                    <ChevronDownIcon
                      v-else-if="sortKey === column.key && sortOrder === 'desc'"
                      class="h-4 w-4"
                    />
                    <ChevronUpDownIcon
                      v-else
                      class="h-4 w-4 opacity-0 group-hover:opacity-100"
                    />
                  </span>
                </div>
              </th>

              <!-- Actions column -->
              <th v-if="$slots.actions" scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                <span class="sr-only">Actions</span>
              </th>
            </tr>
          </thead>

          <!-- Table Body -->
          <tbody class="divide-y divide-gray-200 bg-white">
            <!-- Loading state -->
            <tr v-if="loading">
              <td :colspan="totalColumns" class="px-3 py-4 text-center text-sm text-gray-500">
                <div class="flex items-center justify-center">
                  <Loader size="sm" />
                  <span class="ml-2">Loading...</span>
                </div>
              </td>
            </tr>

            <!-- Empty state -->
            <tr v-else-if="!loading && (!items || items.length === 0)">
              <td :colspan="totalColumns" class="px-3 py-4 text-center text-sm text-gray-500">
                <slot name="empty">
                  <div class="text-center py-6">
                    <InboxIcon class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Tidak ada data</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ emptyMessage }}</p>
                  </div>
                </slot>
              </td>
            </tr>

            <!-- Data rows -->
            <tr
              v-else
              v-for="(item, index) in sortedItems"
              :key="getItemKey(item, index)"
              :class="[
                'hover:bg-gray-50',
                selectedItems.includes(getItemKey(item, index)) ? 'bg-primary-50' : ''
              ]"
            >
              <!-- Selection checkbox -->
              <td v-if="selectable" class="relative w-12 px-6 sm:w-16 sm:px-8">
                <input
                  type="checkbox"
                  class="absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                  :value="getItemKey(item, index)"
                  :checked="selectedItems.includes(getItemKey(item, index))"
                  @change="toggleItem($event, item, index)"
                >
              </td>

              <!-- Data cells -->
              <td
                v-for="column in columns"
                :key="column.key"
                class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
              >
                <slot
                  :name="column.key"
                  :item="item"
                  :value="item[column.key]"
                  :index="index"
                >
                  {{ formatColumnValue(item[column.key], column) }}
                </slot>
              </td>

              <!-- Actions -->
              <td
                v-if="$slots.actions"
                class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6"
              >
                <slot name="actions" :item="item" :index="index"></slot>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        v-if="showPagination && !loading && items.length > 0"
        class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6"
      >
        <div class="flex flex-1 justify-between sm:hidden">
          <button
            type="button"
            class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            :disabled="currentPage === 1"
            @click="currentPage--"
          >
            Previous
          </button>
          <button
            type="button"
            class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            :disabled="currentPage === totalPages"
            @click="currentPage++"
          >
            Next
          </button>
        </div>
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Showing
              <span class="font-medium">{{ paginationFrom }}</span>
              to
              <span class="font-medium">{{ paginationTo }}</span>
              of
              <span class="font-medium">{{ totalItems }}</span>
              results
            </p>
          </div>
          <div>
            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
              <button
                type="button"
                class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                :disabled="currentPage === 1"
                @click="currentPage = 1"
              >
                <span class="sr-only">First</span>
                <ChevronDoubleLeftIcon class="h-5 w-5" />
              </button>
              <button
                type="button"
                class="relative inline-flex items-center px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                :disabled="currentPage === 1"
                @click="currentPage--"
              >
                <span class="sr-only">Previous</span>
                <ChevronLeftIcon class="h-5 w-5" />
              </button>
              <button
                v-for="page in visiblePages"
                :key="page"
                type="button"
                :class="[
                  'relative inline-flex items-center px-4 py-2 text-sm font-semibold',
                  page === currentPage
                    ? 'z-10 bg-primary-600 text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600'
                    : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0'
                ]"
                @click="currentPage = page"
              >
                {{ page }}
              </button>
              <button
                type="button"
                class="relative inline-flex items-center px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                :disabled="currentPage === totalPages"
                @click="currentPage++"
              >
                <span class="sr-only">Next</span>
                <ChevronRightIcon class="h-5 w-5" />
              </button>
              <button
                type="button"
                class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                :disabled="currentPage === totalPages"
                @click="currentPage = totalPages"
              >
                <span class="sr-only">Last</span>
                <ChevronDoubleRightIcon class="h-5 w-5" />
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import {
  ChevronUpIcon,
  ChevronDownIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  ChevronUpDownIcon,
  ChevronDoubleLeftIcon,
  ChevronDoubleRightIcon,
  MagnifyingGlassIcon,
  ArrowPathIcon,
  InboxIcon
} from '@heroicons/vue/24/outline'
import Loader from '@/Components/Misc/Loader.vue'

const props = defineProps({
  // Data
  items: {
    type: Array,
    required: true
  },
  columns: {
    type: Array,
    required: true,
    validator: (columns) => columns.every(col => 'key' in col && 'label' in col)
  },
  loading: {
    type: Boolean,
    default: false
  },

  // Selection
  selectable: {
    type: Boolean,
    default: false
  },
  modelValue: {
    type: Array,
    default: () => []
  },

  // Sorting
  defaultSort: {
    type: Object,
    default: () => ({
      key: '',
      order: 'asc'
    })
  },

  // Pagination
  showPagination: {
    type: Boolean,
    default: true
  },
  perPage: {
    type: Number,
    default: 10
  },
  totalItems: {
    type: Number,
    default: 0
  },

  // Search
  searchPlaceholder: {
    type: String,
    default: 'Cari...'
  },
  showRefresh: {
    type: Boolean,
    default: true
  },
  emptyMessage: {
    type: String,
    default: 'Tidak ada data yang tersedia.'
  },

  // Item key
  itemKey: {
    type: [String, Function],
    default: 'id'
  }
})

const emit = defineEmits([
  'update:modelValue',
  'sort',
  'page-change',
  'search',
  'refresh',
  'select'
])

// Search
const searchQuery = ref('')
const debouncedSearch = ref(null)

watch(searchQuery, (newVal) => {
  if (debouncedSearch.value) clearTimeout(debouncedSearch.value)
  debouncedSearch.value = setTimeout(() => {
    emit('search', newVal)
  }, 300)
})

// Sorting
const sortKey = ref(props.defaultSort.key)
const sortOrder = ref(props.defaultSort.order)

const sort = (key) => {
  if (sortKey.value === key) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortKey.value = key
    sortOrder.value = 'asc'
  }
  emit('sort', { key: sortKey.value, order: sortOrder.value })
}

// Selection
const selectedItems = ref(props.modelValue)

watch(() => props.modelValue, (newVal) => {
  selectedItems.value = newVal
})

const isAllSelected = computed(() => {
  return props.items.length > 0 && selectedItems.value.length === props.items.length
})

const isIndeterminate = computed(() => {
  return selectedItems.value.length > 0 && !isAllSelected.value
})

const toggleAll = (e) => {
  const checked = e.target.checked
  const newSelection = checked
    ? props.items.map((item, index) => getItemKey(item, index))
    : []
  selectedItems.value = newSelection
  emit('update:modelValue', newSelection)
  emit('select', newSelection)
}

const toggleItem = (e, item, index) => {
  const itemKey = getItemKey(item, index)
  const checked = e.target.checked
  const newSelection = checked
    ? [...selectedItems.value, itemKey]
    : selectedItems.value.filter(key => key !== itemKey)
  selectedItems.value = newSelection
  emit('update:modelValue', newSelection)
  emit('select', newSelection)
}

// Pagination
const currentPage = ref(1)

watch(currentPage, (newVal) => {
  emit('page-change', newVal)
})

const totalPages = computed(() => Math.ceil(props.totalItems / props.perPage))
const paginationFrom = computed(() => ((currentPage.value - 1) * props.perPage) + 1)
const paginationTo = computed(() => Math.min(currentPage.value * props.perPage, props.totalItems))

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

// Helpers
const getItemKey = (item, index) => {
  if (typeof props.itemKey === 'function') {
    return props.itemKey(item)
  }
  return item[props.itemKey] || index
}

const formatColumnValue = (value, column) => {
  if (column.format && typeof column.format === 'function') {
    return column.format(value)
  }
  return value
}

const totalColumns = computed(() => {
  let count = props.columns.length
  if (props.selectable) count++
  if (props.$slots.actions) count++
  return count
})

// Sorted items
const sortedItems = computed(() => {
  if (!sortKey.value) return props.items

  return [...props.items].sort((a, b) => {
    const aVal = a[sortKey.value]
    const bVal = b[sortKey.value]

    if (aVal === bVal) return 0
    
    const result = aVal > bVal ? 1 : -1
    return sortOrder.value === 'asc' ? result : -result
  })
})
</script>
