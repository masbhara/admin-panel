<template>
  <AdminLayout :title="'Pengaturan Website'">
    <template #header>
      <h2 :class="[themeClasses.heading, 'text-xl']">Pengaturan Website</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div :class="[themeClasses.card, 'overflow-hidden shadow-sm sm:rounded-lg']">
          <!-- Alert Message -->
          <div v-if="localFlash" :class="[
            'mx-6 mt-6 p-4 rounded-lg border',
            localFlash.type === 'error' 
              ? 'bg-status-danger/10 text-status-danger border-status-danger/20' 
              : 'bg-status-success/10 text-status-success border-status-success/20'
          ]">
            {{ localFlash.message }}
          </div>

          <form @submit.prevent="submitForm" class="p-6">
            <!-- Tab Navigation -->
            <div class="mb-8 border-b border-border-light">
              <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button
                  v-for="tab in ['general', 'tracking', 'footer']"
                  :key="tab"
                  @click="activeTab = tab"
                  type="button"
                  :class="[
                    activeTab === tab
                      ? 'border-primary-500 text-primary-600'
                      : 'border-transparent text-text-secondary hover:border-border-light hover:text-text-primary',
                    'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-all duration-200'
                  ]"
                >
                  {{ tabLabels[tab] }}
                </button>
              </nav>
            </div>

            <!-- Tab Content -->
            <div class="mt-6">
              <!-- General Settings -->
              <div v-show="activeTab === 'general'" class="space-y-6">
                <div class="max-w-2xl">
                  <h3 :class="[themeClasses.heading, 'text-lg mb-1']">{{ tabTitles.general }}</h3>
                  <p :class="[themeClasses.textSecondary, 'text-sm']">{{ tabDescriptions.general }}</p>
                </div>
                <GeneralSettings v-bind="tabProps.general" />
              </div>

              <!-- Tracking Settings -->
              <div v-show="activeTab === 'tracking'" class="space-y-6">
                <div class="max-w-2xl">
                  <h3 :class="[themeClasses.heading, 'text-lg mb-1']">{{ tabTitles.tracking }}</h3>
                  <p :class="[themeClasses.textSecondary, 'text-sm']">{{ tabDescriptions.tracking }}</p>
                </div>
                <TrackingSettings v-bind="tabProps.tracking" />
              </div>

              <!-- Footer Settings -->
              <div v-show="activeTab === 'footer'" class="space-y-6">
                <div class="max-w-2xl">
                  <h3 :class="[themeClasses.heading, 'text-lg mb-1']">{{ tabTitles.footer }}</h3>
                  <p :class="[themeClasses.textSecondary, 'text-sm']">{{ tabDescriptions.footer }}</p>
                </div>
                <FooterSettings v-bind="tabProps.footer" />
              </div>
            </div>

            <!-- Save Button -->
            <div class="mt-10 flex items-center justify-end border-t border-border-light pt-6">
              <PrimaryButton 
                :class="[
                  'min-w-[150px] justify-center',
                  { 'opacity-75 cursor-not-allowed': form.processing }
                ]" 
                :disabled="form.processing"
              >
                {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useForm, usePage, router } from '@inertiajs/vue3'
import { useThemeClasses } from '@/Composables/useThemeClasses'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import GeneralSettings from './Partials/GeneralSettings.vue'
import TrackingSettings from './Partials/TrackingSettings.vue'
import FooterSettings from './Partials/FooterSettings.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const themeClasses = useThemeClasses()

const props = defineProps({
  auth: Object,
  flash: Object,
})

const page = usePage()
const settings = computed(() => page.props.settings || {})

const logoPreview = ref(null)
const faviconPreview = ref(null)
const thumbnailPreview = ref(null)

// Tab aktif
const activeTab = ref('general')

const localFlash = ref(null)
let lastFlashMessage = null

onMounted(() => {
  if (settings.value?.logo) logoPreview.value = settings.value.logo
  if (settings.value?.favicon) faviconPreview.value = settings.value.favicon
  if (settings.value?.thumbnail) thumbnailPreview.value = settings.value.thumbnail
})

const logo = computed(() => settings.value?.logo)
const favicon = computed(() => settings.value?.favicon)
const thumbnail = computed(() => settings.value?.thumbnail)

const form = useForm({
  site_title: settings.value?.site_title || '',
  site_subtitle: settings.value?.site_subtitle || '',
  site_description: settings.value?.site_description || '',
  footer_copyright: settings.value?.footer_copyright || '',
  meta_pixel: settings.value?.meta_pixel || '',
  google_analytics: settings.value?.google_analytics || '',
  tiktok_pixel: settings.value?.tiktok_pixel || '',
  twitter_pixel: settings.value?.twitter_pixel || '',
  logo: null,
  favicon: null,
  thumbnail: null,
})

const handleLogoChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.logo = file
    logoPreview.value = URL.createObjectURL(file)
  }
}

const handleFaviconChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.favicon = file
    faviconPreview.value = URL.createObjectURL(file)
  }
}

const handleThumbnailChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.thumbnail = file
    thumbnailPreview.value = URL.createObjectURL(file)
  }
}

const submitForm = () => {
  form.post(router.route('admin.settings.update'), {
    preserveScroll: true,
    onSuccess: () => {
      if (page.props.flash && page.props.flash.message && page.props.flash.message !== lastFlashMessage) {
        localFlash.value = page.props.flash
        lastFlashMessage = page.props.flash.message
        setTimeout(() => {
          localFlash.value = null
        }, 3000)
      }
    },
  })
}

// Reset notifikasi saat tab berubah
watch(activeTab, () => {
  localFlash.value = null
})

// Tab configurations
const tabLabels = {
  general: 'Pengaturan Umum',
  tracking: 'Script & Tracking',
  footer: 'Footer'
}

const tabTitles = {
  general: 'Pengaturan Umum',
  tracking: 'Script Tracking & Pixel',
  footer: 'Pengaturan Footer'
}

const tabDescriptions = {
  general: 'Pengaturan dasar website seperti logo, favicon, judul, dan deskripsi.',
  tracking: 'Konfigurasi script tracking dan pixel untuk analitik website.',
  footer: 'Konfigurasi teks dan informasi yang ditampilkan di footer website.'
}

const tabProps = {
  general: {
    form: form,
    logo: logo,
    favicon: favicon,
    logoPreview: logoPreview,
    faviconPreview: faviconPreview,
    thumbnail: thumbnail,
    thumbnailPreview: thumbnailPreview,
    handleLogoChange: handleLogoChange,
    handleFaviconChange: handleFaviconChange,
    handleThumbnailChange: handleThumbnailChange,
  },
  tracking: {
    form: form,
    meta_pixel: settings.value?.meta_pixel || '',
    google_analytics: settings.value?.google_analytics || '',
    tiktok_pixel: settings.value?.tiktok_pixel || '',
    twitter_pixel: settings.value?.twitter_pixel || '',
  },
  footer: {
    form: form,
    footer_copyright: settings.value?.footer_copyright || '',
  },
}
</script>
