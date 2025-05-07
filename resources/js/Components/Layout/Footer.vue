<template>
  <footer class="bg-white border-t border-gray-200">
    <div class="mx-auto px-4 py-4 sm:px-6 lg:px-8">
      <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
        <!-- Copyright and Info -->
        <div class="flex flex-col items-center sm:items-start">
          <p class="text-sm text-gray-500">
            &copy; {{ currentYear }} {{ companyName }}. Hak cipta dilindungi.
          </p>
          <p v-if="showVersion" class="mt-1 text-xs text-gray-400">
            Versi {{ version }}
            <span v-if="environment && environment !== 'production'" class="ml-2 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800">
              {{ environment }}
            </span>
          </p>
        </div>

        <!-- Links -->
        <div class="flex space-x-6">
          <a
            v-for="(link, index) in links"
            :key="index"
            :href="link.url"
            class="text-sm text-gray-500 hover:text-gray-900"
            @click.prevent="handleLinkClick(link)"
          >
            {{ link.label }}
          </a>
        </div>
      </div>

      <!-- Additional Info -->
      <div v-if="showAdditionalInfo" class="mt-4 border-t border-gray-100 pt-4 text-center">
        <p class="text-xs text-gray-400">
          <slot name="additional-info">
            {{ additionalInfo }}
          </slot>
        </p>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'

const props = defineProps({
  companyName: {
    type: String,
    default: 'Panel LaraVue'
  },
  version: {
    type: String,
    default: '1.0.0'
  },
  showVersion: {
    type: Boolean,
    default: true
  },
  environment: {
    type: String,
    default: ''
  },
  links: {
    type: Array,
    default: () => [
      {
        label: 'Kebijakan Privasi',
        url: '/privacy-policy',
        internal: true
      },
      {
        label: 'Syarat dan Ketentuan',
        url: '/terms',
        internal: true
      },
      {
        label: 'Bantuan',
        url: '/help',
        internal: true
      },
      {
        label: 'Kontak',
        url: '/contact',
        internal: true
      }
    ]
  },
  additionalInfo: {
    type: String,
    default: ''
  },
  showAdditionalInfo: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['link-click'])
const router = useRouter()

// Computed
const currentYear = computed(() => {
  return new Date().getFullYear()
})

// Methods
const handleLinkClick = (link) => {
  emit('link-click', link)
  
  if (link.internal) {
    router.push(link.url)
  } else {
    window.open(link.url, '_blank')
  }
}
</script>
