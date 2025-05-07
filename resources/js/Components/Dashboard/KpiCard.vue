<template>
  <div
    class="relative overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm"
    :class="className"
  >
    <!-- Header -->
    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
      <div class="flex items-center justify-between">
        <h3 class="text-sm font-medium text-gray-900">{{ title }}</h3>
        <slot name="header-action"></slot>
      </div>
    </div>

    <!-- Content -->
    <div class="p-4">
      <div class="flex items-baseline">
        <div class="flex-1">
          <!-- Current Value -->
          <div class="flex items-baseline">
            <p class="text-2xl font-semibold text-gray-900">{{ formattedValue }}</p>
            <p
              v-if="target"
              class="ml-2 text-sm text-gray-500"
            >
              / {{ formattedTarget }}
            </p>
          </div>

          <!-- Subtitle -->
          <p v-if="subtitle" class="mt-1 text-sm text-gray-500">{{ subtitle }}</p>
        </div>

        <!-- Change Indicator -->
        <div
          v-if="showChange && change !== null"
          class="flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
          :class="changeClass"
        >
          <component
            :is="changeIcon"
            class="mr-1 h-3 w-3"
            aria-hidden="true"
          />
          {{ formatChange(change) }}
        </div>
      </div>

      <!-- Progress Bar -->
      <div v-if="showProgress && target" class="mt-4">
        <div class="relative pt-1">
          <div class="mb-1 flex items-center justify-between">
            <div>
              <span
                class="inline-block rounded-full px-2 py-1 text-xs font-semibold"
                :class="progressTextClass"
              >
                {{ progressPercentage }}%
              </span>
            </div>
            <div class="text-right">
              <span class="text-xs font-semibold text-gray-600">
                {{ progressLabel }}
              </span>
            </div>
          </div>
          <div class="mb-4 flex h-2 overflow-hidden rounded bg-gray-200">
            <div
              :style="{ width: `${progressPercentage}%` }"
              :class="progressBarClass"
            ></div>
          </div>
        </div>
      </div>

      <!-- Comparison -->
      <div v-if="showComparison && previousValue !== null" class="mt-4">
        <div class="flex items-center justify-between text-sm">
          <div>
            <span class="font-medium text-gray-500">{{ comparisonLabel }}</span>
          </div>
          <div>
            <span class="font-medium text-gray-900">{{ formattedPreviousValue }}</span>
          </div>
        </div>
      </div>

      <!-- Chart -->
      <div v-if="showTrend && trendData && trendData.length > 0" class="mt-4 h-16">
        <svg
          class="h-full w-full"
          viewBox="0 0 100 20"
          preserveAspectRatio="none"
        >
          <path
            :d="trendPath"
            fill="none"
            :stroke="trendColor"
            stroke-width="1.5"
            stroke-linejoin="round"
          />
        </svg>
      </div>
    </div>

    <!-- Footer -->
    <div v-if="$slots.footer" class="border-t border-gray-200 bg-gray-50 px-4 py-3">
      <slot name="footer"></slot>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import {
  ArrowUpIcon,
  ArrowDownIcon,
  MinusIcon
} from '@heroicons/vue/24/solid'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: Number,
    required: true
  },
  target: {
    type: Number,
    default: null
  },
  subtitle: {
    type: String,
    default: ''
  },
  change: {
    type: Number,
    default: null
  },
  showChange: {
    type: Boolean,
    default: true
  },
  previousValue: {
    type: Number,
    default: null
  },
  showComparison: {
    type: Boolean,
    default: true
  },
  comparisonLabel: {
    type: String,
    default: 'Periode sebelumnya'
  },
  showProgress: {
    type: Boolean,
    default: true
  },
  progressLabel: {
    type: String,
    default: 'Pencapaian target'
  },
  trendData: {
    type: Array,
    default: () => []
  },
  showTrend: {
    type: Boolean,
    default: true
  },
  formatter: {
    type: Function,
    default: null
  },
  className: {
    type: String,
    default: ''
  }
})

// Format value
const formatValue = (value) => {
  if (props.formatter && typeof props.formatter === 'function') {
    return props.formatter(value)
  }
  
  if (typeof value === 'number') {
    return new Intl.NumberFormat('id-ID').format(value)
  }
  
  return value
}

// Formatted values
const formattedValue = computed(() => formatValue(props.value))
const formattedTarget = computed(() => formatValue(props.target))
const formattedPreviousValue = computed(() => formatValue(props.previousValue))

// Progress percentage
const progressPercentage = computed(() => {
  if (!props.target || props.target === 0) return 0
  
  const percentage = (props.value / props.target) * 100
  return Math.min(Math.round(percentage), 100)
})

// Progress classes
const progressBarClass = computed(() => {
  if (progressPercentage.value < 30) {
    return 'bg-red-500'
  } else if (progressPercentage.value < 70) {
    return 'bg-yellow-500'
  } else {
    return 'bg-green-500'
  }
})

const progressTextClass = computed(() => {
  if (progressPercentage.value < 30) {
    return 'bg-red-100 text-red-800'
  } else if (progressPercentage.value < 70) {
    return 'bg-yellow-100 text-yellow-800'
  } else {
    return 'bg-green-100 text-green-800'
  }
})

// Change indicator
const changeClass = computed(() => {
  if (props.change === 0) {
    return 'bg-gray-100 text-gray-800'
  } else if (props.change > 0) {
    return 'bg-green-100 text-green-800'
  } else {
    return 'bg-red-100 text-red-800'
  }
})

const changeIcon = computed(() => {
  if (props.change === 0) {
    return MinusIcon
  } else if (props.change > 0) {
    return ArrowUpIcon
  } else {
    return ArrowDownIcon
  }
})

const formatChange = (change) => {
  const absChange = Math.abs(change)
  return `${absChange}%`
}

// Trend line
const trendPath = computed(() => {
  if (!props.trendData || props.trendData.length === 0) {
    return ''
  }

  const data = [...props.trendData]
  const max = Math.max(...data)
  const min = Math.min(...data)
  const range = max - min || 1
  
  // Normalize data to fit in the SVG viewBox
  const normalizedData = data.map(value => 20 - ((value - min) / range) * 18)
  
  // Calculate points for the path
  const points = normalizedData.map((y, x) => {
    const xCoord = (x / (data.length - 1)) * 100
    return `${xCoord},${y}`
  })
  
  return `M${points.join(' L')}`
})

const trendColor = computed(() => {
  if (!props.trendData || props.trendData.length < 2) {
    return '#9CA3AF' // gray-400
  }
  
  const first = props.trendData[0]
  const last = props.trendData[props.trendData.length - 1]
  
  if (last > first) {
    return '#10B981' // green-500
  } else if (last < first) {
    return '#EF4444' // red-500
  } else {
    return '#9CA3AF' // gray-400
  }
})
</script>
