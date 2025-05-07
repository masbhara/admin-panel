<template>
  <div class="relative w-full" :class="wrapperClass">
    <Combobox v-model="selectedItem" as="div" :disabled="disabled">
      <div class="relative">
        <!-- Label -->
        <ComboboxLabel v-if="label" class="block text-sm font-medium text-text-secondary pb-1">
          {{ label }}
          <span v-if="required" class="text-red-500">*</span>
        </ComboboxLabel>
        
        <!-- Input dan tombol -->
        <div class="relative">
          <div :class="[
            'relative w-full cursor-default overflow-hidden rounded-md text-left',
            'bg-background-secondary border border-border-light',
            'focus-within:border-primary-500 focus-within:ring-1 focus-within:ring-primary-500',
            'shadow-sm',
            disabled ? 'opacity-50 cursor-not-allowed' : '',
            inputClass
          ]">
            <ComboboxInput 
              class="w-full border-none py-2 pl-3 pr-10 text-sm text-text-primary bg-transparent focus:outline-none disabled:cursor-not-allowed"
              :placeholder="placeholder"
              :disabled="disabled"
              :display-value="(item) => displayValue ? item[displayValue] : item"
              @change="onQueryChange"
              autocomplete="off"
            />
            <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-2">
              <ChevronUpDownIcon 
                class="h-5 w-5 text-text-tertiary" 
                aria-hidden="true" 
              />
            </ComboboxButton>
          </div>
          
          <!-- Options dropdown -->
          <TransitionRoot
            leave="transition ease-in duration-100"
            leaveFrom="opacity-100"
            leaveTo="opacity-0"
            @after-leave="query = ''"
          >
            <ComboboxOptions 
              class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-background-primary py-1 text-base shadow-lg ring-1 ring-border-default focus:outline-none sm:text-sm"
              :class="optionsClass"
            >
              <div v-if="filteredItems.length === 0 && query !== ''" class="relative cursor-default select-none py-2 px-4 text-text-tertiary">
                {{ noResultsText }}
              </div>
              
              <ComboboxOption
                v-for="item in filteredItems"
                :key="getItemKey(item)"
                :value="item"
                v-slot="{ selected, active }"
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
              </ComboboxOption>
            </ComboboxOptions>
          </TransitionRoot>
        </div>
      </div>
    </Combobox>
    
    <!-- Error message -->
    <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    <!-- Hint text -->
    <p v-else-if="hint" class="mt-1 text-sm text-text-tertiary">{{ hint }}</p>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { 
  Combobox, 
  ComboboxInput, 
  ComboboxButton, 
  ComboboxOptions, 
  ComboboxOption,
  ComboboxLabel,
  TransitionRoot
} from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'

const props = defineProps({
  modelValue: {
    type: [Object, String, Number],
    default: null
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
    default: 'Pilih opsi atau ketik untuk mencari'
  },
  noResultsText: {
    type: String,
    default: 'Tidak ada hasil ditemukan'
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
  searchable: {
    type: Boolean,
    default: true
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

const emit = defineEmits(['update:modelValue', 'change', 'search'])

const query = ref('')
const selectedItem = computed({
  get: () => props.modelValue,
  set: (value) => {
    emit('update:modelValue', value)
    emit('change', value)
  }
})

// Filter items berdasarkan query pencarian
const filteredItems = computed(() => {
  if (!props.searchable || query.value === '') {
    return props.items
  }
  
  const lowercaseQuery = query.value.toLowerCase()
  return props.items.filter(item => {
    const displayText = props.displayValue ? item[props.displayValue] : item
    return String(displayText).toLowerCase().includes(lowercaseQuery)
  })
})

// Handler untuk perubahan input
const onQueryChange = (event) => {
  query.value = event.target.value
  emit('search', query.value)
}

// Mendapatkan key unik untuk setiap item
const getItemKey = (item) => {
  if (typeof item === 'object' && item !== null) {
    return props.valueKey ? item[props.valueKey] : JSON.stringify(item)
  }
  return String(item)
}
</script> 