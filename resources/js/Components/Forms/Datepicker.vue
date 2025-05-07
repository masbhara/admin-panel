<template>
  <div class="relative">
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <div class="relative">
      <input
        :id="id"
        ref="inputRef"
        type="text"
        :value="formattedValue"
        :placeholder="placeholder"
        :class="[
          'block w-full rounded-md border-gray-300 pr-10 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm',
          error ? 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500' : '',
          className
        ]"
        :disabled="disabled"
        :required="required"
        readonly
        @click="toggleCalendar"
        v-bind="$attrs"
      >
      <div class="absolute inset-y-0 right-0 flex items-center pr-3">
        <CalendarIcon class="h-5 w-5 text-gray-400" @click="toggleCalendar" />
      </div>
    </div>
    <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    <p v-if="hint && !error" class="mt-1 text-sm text-gray-500">{{ hint }}</p>

    <!-- Calendar Dropdown -->
    <div
      v-if="showCalendar"
      class="absolute z-10 mt-1 w-full rounded-md bg-white shadow-lg"
      @click.stop
    >
      <div class="p-2">
        <!-- Header -->
        <div class="flex items-center justify-between mb-2">
          <button
            type="button"
            class="p-1 hover:bg-gray-100 rounded-full"
            @click="previousMonth"
          >
            <ChevronLeftIcon class="h-5 w-5 text-gray-600" />
          </button>
          <span class="text-sm font-medium text-gray-900">
            {{ format(currentDate, 'MMMM yyyy') }}
          </span>
          <button
            type="button"
            class="p-1 hover:bg-gray-100 rounded-full"
            @click="nextMonth"
          >
            <ChevronRightIcon class="h-5 w-5 text-gray-600" />
          </button>
        </div>

        <!-- Week days -->
        <div class="grid grid-cols-7 gap-1 mb-1">
          <span
            v-for="day in weekDays"
            :key="day"
            class="text-xs font-medium text-gray-500 text-center py-1"
          >
            {{ day }}
          </span>
        </div>

        <!-- Calendar days -->
        <div class="grid grid-cols-7 gap-1">
          <button
            v-for="date in calendarDays"
            :key="date.toISOString()"
            type="button"
            :class="[
              'text-sm rounded-full w-8 h-8 flex items-center justify-center',
              isToday(date) ? 'font-semibold' : '',
              isSameMonth(date, currentDate) ? 'text-gray-900' : 'text-gray-400',
              !isSameMonth(date, currentDate) ? 'hover:bg-gray-50' : 'hover:bg-gray-100',
              isSelected(date) ? 'bg-primary-600 text-white hover:bg-primary-700' : ''
            ]"
            @click="selectDate(date)"
          >
            {{ format(date, 'd') }}
          </button>
        </div>

        <!-- Footer -->
        <div class="mt-2 flex justify-between border-t border-gray-100 pt-2">
          <button
            type="button"
            class="text-sm text-gray-500 hover:text-gray-700"
            @click="setToday"
          >
            Hari ini
          </button>
          <button
            type="button"
            class="text-sm text-primary-600 hover:text-primary-700"
            @click="showCalendar = false"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { CalendarIcon, ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'
import {
  format,
  parse,
  isValid,
  startOfMonth,
  endOfMonth,
  eachDayOfInterval,
  addMonths,
  subMonths,
  isToday as isDateToday,
  isSameMonth as isSameMonthFn,
  isSameDay,
  startOfWeek,
  endOfWeek
} from 'date-fns'
import { id } from 'date-fns/locale'

const props = defineProps({
  modelValue: {
    type: [Date, String],
    default: null
  },
  label: {
    type: String,
    default: ''
  },
  format: {
    type: String,
    default: 'dd/MM/yyyy'
  },
  placeholder: {
    type: String,
    default: 'Pilih tanggal'
  },
  error: {
    type: String,
    default: ''
  },
  hint: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  className: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue'])
const inputRef = ref(null)
const showCalendar = ref(false)
const currentDate = ref(props.modelValue ? new Date(props.modelValue) : new Date())
const id = computed(() => `datepicker-${Math.random().toString(36).substr(2, 9)}`)

// Format the selected date for display
const formattedValue = computed(() => {
  if (!props.modelValue) return ''
  const date = new Date(props.modelValue)
  return isValid(date) ? format(date, props.format, { locale: id }) : ''
})

// Generate week days header
const weekDays = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']

// Generate calendar days
const calendarDays = computed(() => {
  const start = startOfWeek(startOfMonth(currentDate.value))
  const end = endOfWeek(endOfMonth(currentDate.value))
  return eachDayOfInterval({ start, end })
})

// Helper functions
const isToday = (date) => isDateToday(date)
const isSameMonth = (date1, date2) => isSameMonthFn(date1, date2)
const isSelected = (date) => props.modelValue && isSameDay(date, new Date(props.modelValue))

// Calendar navigation
const previousMonth = () => {
  currentDate.value = subMonths(currentDate.value, 1)
}

const nextMonth = () => {
  currentDate.value = addMonths(currentDate.value, 1)
}

const setToday = () => {
  const today = new Date()
  currentDate.value = today
  selectDate(today)
}

// Date selection
const selectDate = (date) => {
  emit('update:modelValue', date)
  showCalendar.value = false
}

const toggleCalendar = () => {
  if (!props.disabled) {
    showCalendar.value = !showCalendar.value
  }
}

// Click outside handler
const handleClickOutside = (event) => {
  if (inputRef.value && !inputRef.value.contains(event.target)) {
    showCalendar.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
