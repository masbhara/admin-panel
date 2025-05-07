<template>
  <div class="flex items-center" :class="{ 'opacity-50': disabled }">
    <button
      type="button"
      :class="[
        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2',
        modelValue ? 'bg-primary-600' : 'bg-gray-200',
        disabled ? 'cursor-not-allowed' : '',
        className
      ]"
      :disabled="disabled"
      @click="toggle"
      role="switch"
      :aria-checked="modelValue"
    >
      <span
        :class="[
          'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
          modelValue ? 'translate-x-5' : 'translate-x-0'
        ]"
      />
    </button>
    <div v-if="label || $slots.default" class="ml-3">
      <span class="text-sm font-medium text-gray-900">
        <slot>{{ label }}</slot>
      </span>
      <span v-if="description" class="text-sm text-gray-500">
        {{ description }}
      </span>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  label: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
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

const emit = defineEmits(['update:modelValue', 'change'])

const toggle = () => {
  if (!props.disabled) {
    const newValue = !props.modelValue
    emit('update:modelValue', newValue)
    emit('change', newValue)
  }
}
</script>
