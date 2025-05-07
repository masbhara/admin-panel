<template>
  <div class="relative">
    <!-- Chart Title -->
    <div v-if="title || $slots.header" class="mb-4 flex items-center justify-between">
      <div>
        <h3 v-if="title" class="text-base font-medium text-gray-900">{{ title }}</h3>
        <p v-if="subtitle" class="mt-1 text-sm text-gray-500">{{ subtitle }}</p>
      </div>
      <slot name="header"></slot>
    </div>

    <!-- Loading Overlay -->
    <div
      v-if="loading"
      class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75 z-10"
    >
      <div class="flex items-center space-x-2">
        <svg class="h-5 w-5 animate-spin text-primary-500" viewBox="0 0 24 24">
          <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
          ></circle>
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          ></path>
        </svg>
        <span class="text-sm font-medium text-gray-500">Memuat data...</span>
      </div>
    </div>

    <!-- No Data Message -->
    <div
      v-if="!loading && (!chartData || !chartData.datasets || chartData.datasets.length === 0)"
      class="flex h-64 flex-col items-center justify-center rounded-lg border border-gray-200 bg-white p-6"
    >
      <ChartBarIcon class="h-12 w-12 text-gray-300" />
      <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
      <p class="mt-1 text-sm text-gray-500">{{ noDataMessage }}</p>
      <slot name="no-data-action"></slot>
    </div>

    <!-- Chart Container -->
    <div
      v-else
      :class="[
        'relative overflow-hidden rounded-lg border border-gray-200 bg-white',
        height ? '' : 'h-64',
        className
      ]"
      :style="height ? { height } : {}"
    >
      <component
        :is="chartComponent"
        :data="chartData"
        :options="mergedOptions"
        :style="{ position: 'relative' }"
      />
    </div>

    <!-- Footer -->
    <div v-if="$slots.footer" class="mt-4">
      <slot name="footer"></slot>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch, onMounted } from 'vue'
import { Chart, registerables } from 'chart.js'
import { Bar, Line, Pie, Doughnut, PolarArea, Radar, Bubble, Scatter } from 'vue-chart-3'
import { ChartBarIcon } from '@heroicons/vue/24/outline'

// Register Chart.js components
Chart.register(...registerables)

const props = defineProps({
  type: {
    type: String,
    default: 'bar',
    validator: (value) => ['bar', 'line', 'pie', 'doughnut', 'polarArea', 'radar', 'bubble', 'scatter'].includes(value)
  },
  chartData: {
    type: Object,
    default: () => ({
      labels: [],
      datasets: []
    })
  },
  options: {
    type: Object,
    default: () => ({})
  },
  height: {
    type: String,
    default: ''
  },
  title: {
    type: String,
    default: ''
  },
  subtitle: {
    type: String,
    default: ''
  },
  loading: {
    type: Boolean,
    default: false
  },
  noDataMessage: {
    type: String,
    default: 'Tidak ada data yang tersedia untuk ditampilkan.'
  },
  className: {
    type: String,
    default: ''
  },
  colors: {
    type: Array,
    default: () => [
      'rgba(59, 130, 246, 0.5)',   // blue
      'rgba(16, 185, 129, 0.5)',   // green
      'rgba(245, 158, 11, 0.5)',   // yellow
      'rgba(239, 68, 68, 0.5)',    // red
      'rgba(139, 92, 246, 0.5)',   // purple
      'rgba(14, 165, 233, 0.5)',   // sky
      'rgba(249, 115, 22, 0.5)',   // orange
      'rgba(236, 72, 153, 0.5)'    // pink
    ]
  },
  borderColors: {
    type: Array,
    default: () => [
      'rgb(59, 130, 246)',   // blue
      'rgb(16, 185, 129)',   // green
      'rgb(245, 158, 11)',   // yellow
      'rgb(239, 68, 68)',    // red
      'rgb(139, 92, 246)',   // purple
      'rgb(14, 165, 233)',   // sky
      'rgb(249, 115, 22)',   // orange
      'rgb(236, 72, 153)'    // pink
    ]
  },
  responsive: {
    type: Boolean,
    default: true
  },
  maintainAspectRatio: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['chart-click', 'chart-hover'])

// Computed
const chartComponent = computed(() => {
  switch (props.type) {
    case 'bar':
      return Bar
    case 'line':
      return Line
    case 'pie':
      return Pie
    case 'doughnut':
      return Doughnut
    case 'polarArea':
      return PolarArea
    case 'radar':
      return Radar
    case 'bubble':
      return Bubble
    case 'scatter':
      return Scatter
    default:
      return Bar
  }
})

// Apply colors to datasets if not specified
const processedChartData = computed(() => {
  if (!props.chartData || !props.chartData.datasets) {
    return props.chartData
  }

  const datasets = props.chartData.datasets.map((dataset, index) => {
    const colorIndex = index % props.colors.length
    const newDataset = { ...dataset }

    if (!newDataset.backgroundColor) {
      newDataset.backgroundColor = props.colors[colorIndex]
    }

    if (!newDataset.borderColor && ['line', 'radar'].includes(props.type)) {
      newDataset.borderColor = props.borderColors[colorIndex]
    }

    return newDataset
  })

  return {
    ...props.chartData,
    datasets
  }
})

// Merge default options with user options
const mergedOptions = computed(() => {
  const defaultOptions = {
    responsive: props.responsive,
    maintainAspectRatio: props.maintainAspectRatio,
    plugins: {
      legend: {
        display: ['pie', 'doughnut', 'polarArea'].includes(props.type),
        position: 'top'
      },
      tooltip: {
        enabled: true,
        mode: ['pie', 'doughnut', 'polarArea'].includes(props.type) ? 'nearest' : 'index',
        intersect: false
      }
    },
    onClick: (event, elements, chart) => {
      if (elements.length > 0) {
        const element = elements[0]
        const datasetIndex = element.datasetIndex
        const index = element.index
        
        emit('chart-click', {
          event,
          element,
          datasetIndex,
          index,
          label: props.chartData.labels[index],
          value: props.chartData.datasets[datasetIndex].data[index]
        })
      }
    },
    onHover: (event, elements, chart) => {
      if (elements.length > 0) {
        const element = elements[0]
        const datasetIndex = element.datasetIndex
        const index = element.index
        
        emit('chart-hover', {
          event,
          element,
          datasetIndex,
          index,
          label: props.chartData.labels[index],
          value: props.chartData.datasets[datasetIndex].data[index]
        })
      }
    }
  }

  // Add specific options based on chart type
  if (props.type === 'bar') {
    defaultOptions.scales = {
      y: {
        beginAtZero: true
      }
    }
  } else if (props.type === 'line') {
    defaultOptions.elements = {
      line: {
        tension: 0.4
      }
    }
  }

  return { ...defaultOptions, ...props.options }
})
</script>
