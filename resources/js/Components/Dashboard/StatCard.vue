<template>
  <div
    :class="[
      'relative overflow-hidden rounded-lg p-6 shadow-sm',
      bgColor,
      borderColor,
      className
    ]"
  >
    <div :class="['flex', layout === 'row' ? 'items-center' : 'flex-col space-y-4']">
      <!-- Icon -->
      <div
        v-if="icon"
        :class="[
          'flex-shrink-0 rounded-md p-2',
          iconBgColor,
          layout === 'row' ? 'mr-4' : ''
        ]"
      >
        <component
          :is="icon"
          :class="['h-6 w-6', iconColor]"
          aria-hidden="true"
        />
      </div>

      <!-- Content -->
      <div class="flex-1">
        <h3 class="text-sm font-medium text-gray-500">{{ title }}</h3>
        <div class="mt-1 flex items-baseline">
          <p :class="['text-2xl font-semibold', valueColor]">{{ formattedValue }}</p>
          
          <!-- Change indicator -->
          <p
            v-if="showChange && change !== null"
            :class="[
              'ml-2 flex items-baseline text-sm font-semibold',
              change >= 0 ? 'text-green-600' : 'text-red-600'
            ]"
          >
            <component
              :is="change >= 0 ? ArrowUpIcon : ArrowDownIcon"
              class="h-3 w-3 flex-shrink-0 self-center"
              aria-hidden="true"
            />
            <span class="ml-1">{{ Math.abs(change) }}%</span>
          </p>
        </div>
        <p v-if="subtitle" class="mt-1 text-sm text-gray-500">{{ subtitle }}</p>
      </div>
    </div>

    <!-- Footer -->
    <div v-if="$slots.footer" class="mt-4 border-t border-gray-100 pt-4">
      <slot name="footer"></slot>
    </div>

    <!-- Decorative pattern -->
    <div
      v-if="showPattern"
      class="absolute right-0 top-0 -mt-6 -mr-6 h-24 w-24 rounded-full opacity-20"
      :class="patternColor"
    ></div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { ArrowUpIcon, ArrowDownIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [Number, String],
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  icon: {
    type: [Object, Function],
    default: null
  },
  change: {
    type: Number,
    default: null
  },
  showChange: {
    type: Boolean,
    default: true
  },
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'success', 'warning', 'danger', 'info', 'neutral'].includes(value)
  },
  layout: {
    type: String,
    default: 'row',
    validator: (value) => ['row', 'column'].includes(value)
  },
  formatter: {
    type: Function,
    default: null
  },
  showPattern: {
    type: Boolean,
    default: true
  },
  className: {
    type: String,
    default: ''
  }
})

// Computed
const formattedValue = computed(() => {
  if (props.formatter && typeof props.formatter === 'function') {
    return props.formatter(props.value)
  }
  
  if (typeof props.value === 'number') {
    return new Intl.NumberFormat('id-ID').format(props.value)
  }
  
  return props.value
})

// Style variants
const bgColor = computed(() => {
  switch (props.variant) {
    case 'primary':
      return 'bg-white'
    case 'success':
      return 'bg-green-50'
    case 'warning':
      return 'bg-yellow-50'
    case 'danger':
      return 'bg-red-50'
    case 'info':
      return 'bg-blue-50'
    case 'neutral':
    default:
      return 'bg-gray-50'
  }
})

const borderColor = computed(() => {
  switch (props.variant) {
    case 'primary':
      return 'border border-gray-200'
    case 'success':
      return 'border border-green-200'
    case 'warning':
      return 'border border-yellow-200'
    case 'danger':
      return 'border border-red-200'
    case 'info':
      return 'border border-blue-200'
    case 'neutral':
    default:
      return 'border border-gray-200'
  }
})

const iconBgColor = computed(() => {
  switch (props.variant) {
    case 'primary':
      return 'bg-primary-100'
    case 'success':
      return 'bg-green-100'
    case 'warning':
      return 'bg-yellow-100'
    case 'danger':
      return 'bg-red-100'
    case 'info':
      return 'bg-blue-100'
    case 'neutral':
    default:
      return 'bg-gray-100'
  }
})

const iconColor = computed(() => {
  switch (props.variant) {
    case 'primary':
      return 'text-primary-600'
    case 'success':
      return 'text-green-600'
    case 'warning':
      return 'text-yellow-600'
    case 'danger':
      return 'text-red-600'
    case 'info':
      return 'text-blue-600'
    case 'neutral':
    default:
      return 'text-gray-600'
  }
})

const valueColor = computed(() => {
  switch (props.variant) {
    case 'primary':
      return 'text-gray-900'
    case 'success':
      return 'text-green-700'
    case 'warning':
      return 'text-yellow-700'
    case 'danger':
      return 'text-red-700'
    case 'info':
      return 'text-blue-700'
    case 'neutral':
    default:
      return 'text-gray-900'
  }
})

const patternColor = computed(() => {
  switch (props.variant) {
    case 'primary':
      return 'bg-primary-200'
    case 'success':
      return 'bg-green-200'
    case 'warning':
      return 'bg-yellow-200'
    case 'danger':
      return 'bg-red-200'
    case 'info':
      return 'bg-blue-200'
    case 'neutral':
    default:
      return 'bg-gray-200'
  }
})
</script>
