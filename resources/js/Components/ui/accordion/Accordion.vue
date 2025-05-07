<template>
  <div :class="['divide-y divide-border-light', wrapperClass]">
    <div
      v-for="(item, index) in items"
      :key="index"
      class="accordion-item"
    >
      <Disclosure v-slot="{ open }" :defaultOpen="defaultOpen && index === 0">
        <DisclosureButton
          class="flex w-full items-center justify-between py-3 text-left text-text-primary"
          :class="[buttonClass, open ? 'font-medium' : '']"
        >
          <span class="text-sm font-medium">{{ item.title }}</span>
          <ChevronRightIcon
            :class="['h-5 w-5 text-text-tertiary transition-transform', open ? 'rotate-90 transform' : '']"
          />
        </DisclosureButton>
        <TransitionExpand>
          <DisclosurePanel 
            v-show="open" 
            class="pb-3 pt-1 text-sm text-text-secondary"
            :class="panelClass"
          >
            <div v-if="$slots.default" class="slot-content">
              <slot :item="item" :index="index"></slot>
            </div>
            <div v-else v-html="item.content"></div>
          </DisclosurePanel>
        </TransitionExpand>
      </Disclosure>
    </div>
  </div>
</template>

<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { ChevronRightIcon } from '@heroicons/vue/24/outline'
import TransitionExpand from '@/Components/TransitionExpand.vue'

const props = defineProps({
  items: {
    type: Array,
    required: true,
    default: () => []
  },
  defaultOpen: {
    type: Boolean,
    default: false
  },
  wrapperClass: {
    type: String,
    default: ''
  },
  buttonClass: {
    type: String,
    default: ''
  },
  panelClass: {
    type: String,
    default: ''
  }
})
</script>

<style scoped>
.accordion-item:first-child {
  border-top-width: 0;
}
</style> 