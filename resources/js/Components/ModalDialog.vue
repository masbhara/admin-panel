<template>
  <teleport to="body">
    <transition
      enter-active-class="ease-out duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto transition-opacity bg-gray-500 bg-opacity-75"
      >
        <div 
          :class="{
            'sm:max-w-sm': maxWidth === 'sm',
            'sm:max-w-md': maxWidth === 'md',
            'sm:max-w-lg': maxWidth === 'lg',
            'sm:max-w-xl': maxWidth === 'xl',
            'sm:max-w-2xl': maxWidth === '2xl',
            'sm:max-w-3xl': maxWidth === '3xl',
            'sm:max-w-4xl': maxWidth === '4xl',
            'sm:max-w-5xl': maxWidth === '5xl',
            'sm:max-w-6xl': maxWidth === '6xl',
            'sm:max-w-7xl': maxWidth === '7xl',
          }"
          class="w-full px-4 pt-5 pb-4 mx-auto overflow-hidden transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:p-6"
          @click.stop
        >
          <slot />
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script setup>
import { onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  maxWidth: {
    type: String,
    default: '2xl',
  },
  closeable: {
    type: Boolean,
    default: true,
  },
});

const emit = defineEmits(['close']);

watch(
  () => props.show,
  (value) => {
    if (value) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = null;
    }
  }
);

const close = () => {
  if (props.closeable) {
    emit('close');
  }
};

const closeOnEscape = (e) => {
  if (e.key === 'Escape' && props.show && props.closeable) {
    close();
  }
};

onMounted(() => {
  document.addEventListener('keydown', closeOnEscape);
  
  if (props.show) {
    document.body.style.overflow = 'hidden';
  }
});

onUnmounted(() => {
  document.removeEventListener('keydown', closeOnEscape);
  document.body.style.overflow = null;
});
</script> 