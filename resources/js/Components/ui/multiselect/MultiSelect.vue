<template>
  <div class="relative w-full" :class="wrapperClass">
    <Listbox v-model="selectedItems" multiple as="div" :disabled="disabled">
      <div class="relative">
        <!-- Label -->
        <ListboxLabel v-if="label" class="block text-sm font-medium text-text-secondary pb-1">
          {{ label }}
          <span v-if="required" class="text-red-500">*</span>
        </ListboxLabel>
        
        <!-- Input & button -->
        <div class="relative">
          <ListboxButton
            class="relative w-full cursor-default rounded-md bg-background-secondary border border-border-light py-2 pl-3 pr-10 text-left shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 sm:text-sm"
            :class="[disabled ? 'opacity-50 cursor-not-allowed' : '', inputClass]"
          >
            <span v-if="selectedItems.length === 0" class="block truncate text-text-tertiary">
              {{ placeholder }}
            </span>
            <div v-else class="flex flex-wrap gap-1">
              <div
                v-for="item in selectedItems"
                :key="getItemKey(item)"
                class="inline-flex items-center rounded-md bg-primary-100 dark:bg-primary-700/20 px-2 py-1 text-xs font-medium text-primary-700 dark:text-primary-300"
              >
                {{ displayValue ? item[displayValue] : item }}
                <button 
                  v-if="!disabled" 
                  @click.stop="removeItem(item)" 
                  class="ml-1 hover:text-primary-500"
                >
                  <XMarkIcon class="h-3 w-3" />
                </button>
              </div>
            </div>
            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
              <ChevronUpDownIcon class="h-5 w-5 text-text-tertiary" aria-hidden="true" />
            </span>
          </ListboxButton>
          
          <!-- Options dropdown -->
          <transition
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <ListboxOptions
              class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-background-primary py-1 text-base shadow-lg ring-1 ring-border-default focus:outline-none sm:text-sm"
              :class="optionsClass"
            >
              <ListboxOption
                v-for="item in items"
                :key="getItemKey(item)"
                :value="item"
                v-slot="{ active, selected }"
                as="template"
              >
                <li
                  :class="[
                    'relative cursor-default select-none py-2 pl-10 pr-4',
                    active ? 'bg-primary-600 text-white' : 'text-text-primary'
                  ]"
                >
                  <span :class="[
                    'block truncate',
                    selected ? 'font-medium' : 'font-normal'
                  ]">
                    {{ displayValue ? item[displayValue] : item }}
                  </span>
                  <span
                    v-if="selected"
                    :class="[
                      'absolute inset-y-0 left-0 flex items-center pl-3',
                      active ? 'text-white' : 'text-primary-600'
                    ]"
                  >
                    <CheckIcon class="h-5 w-5" aria-hidden="true" />
                  </span>
                </li>
              </ListboxOption>
            </ListboxOptions>
          </transition>
        </div>
      </div>
    </Listbox>
    
    <!-- Error message -->
    <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    <!-- Hint text -->
    <p v-else-if="hint" class="mt-1 text-sm text-text-tertiary">{{ hint }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import {
  Listbox,
  ListboxButton,
  ListboxOptions,
  ListboxOption,
  ListboxLabel
} from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon, XMarkIcon } from '@heroicons/vue/20/solid'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  items: {
    type: Array,
    required: true
  },
  displayValue: {
    type: String,
    default: 'label'
  },
  valueKey: {
    type: String,
    default: 'id'
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Pilih opsi'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  required: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  },
  hint: {
    type: String,
    default: ''
  },
  wrapperClass: {
    type: String,
    default: ''
  },
  inputClass: {
    type: String,
    default: ''
  },
  optionsClass: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue', 'change'])

const selectedItems = computed({
  get: () => props.modelValue,
  set: (value) => {
    emit('update:modelValue', value)
    emit('change', value)
  }
})

// Hapus item dari seleksi
const removeItem = (itemToRemove) => {
  selectedItems.value = selectedItems.value.filter(
    item => getItemKey(item) !== getItemKey(itemToRemove)
  )
}

// Mendapatkan key unik untuk setiap item
const getItemKey = (item) => {
  if (typeof item === 'object' && item !== null) {
    return props.valueKey ? item[props.valueKey] : JSON.stringify(item)
  }
  return String(item)
}
</script> 