<template>
  <Teleport to="body">
    <Transition
      enter-active-class="duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-50 bg-gray-500/75 transition-opacity dark:bg-gray-900/80"
        @click="close"
      />
    </Transition>

    <Transition
      enter-active-class="duration-300 ease-out"
      enter-from-class="translate-y-4 opacity-0 sm:translate-y-0 sm:scale-95"
      enter-to-class="translate-y-0 opacity-100 sm:scale-100"
      leave-active-class="duration-200 ease-in"
      leave-from-class="translate-y-0 opacity-100 sm:scale-100"
      leave-to-class="translate-y-4 opacity-0 sm:translate-y-0 sm:scale-95"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click="close"
      >
        <div 
          :class="[
            'flex min-h-full items-end justify-center text-center sm:items-center',
            paddingOuter ? 'p-4 sm:p-0' : 'p-0'
          ]"
        >
          <div
            :class="[
              'relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full',
              maxWidth,
              paddingInner ? 'px-4 pb-4 pt-5 sm:p-6' : '',
              contentClass
            ]"
            @click.stop
          >
            <slot />
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: true,
  },
  paddingOuter: {
    type: Boolean,
    default: true,
  },
  paddingInner: {
    type: Boolean,
    default: true,
  },
  maxWidth: {
    type: String,
    default: 'sm:max-w-lg',
  },
  contentClass: {
    type: String,
    default: '',
  }
})

const emit = defineEmits(['close'])

const close = () => {
  emit('close')
}

const show = ref(props.show)

watch(() => props.show, (value) => {
  show.value = value
})

const closeOnEscape = (e) => {
  if (e.key === 'Escape' && show.value) {
    close()
  }
}

onMounted(() => {
  document.addEventListener('keydown', closeOnEscape)
})

onUnmounted(() => {
  document.removeEventListener('keydown', closeOnEscape)
})
</script>
