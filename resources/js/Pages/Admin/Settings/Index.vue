<template>
  <AdminLayout :title="'Pengaturan Website'">
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-white">Pengaturan Website</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white dark:bg-primary-600 shadow-sm sm:rounded-lg">
          <!-- Alert Message -->
          <div v-if="localFlash" :class="`mb-4 p-4 rounded-md ${localFlash.type === 'error' ? 'bg-red-50 dark:bg-red-900 text-red-700 dark:text-red-200' : 'bg-green-50 dark:bg-green-900 text-green-700 dark:text-green-200'}`">
            {{ localFlash.message }}
          </div>

          <form @submit.prevent="submitForm" class="p-6">
            <Tabs defaultValue="general" class="w-full">
              <TabsList class="grid w-full grid-cols-3">
                <TabsTrigger value="general">Pengaturan Umum</TabsTrigger>
                <TabsTrigger value="tracking">Script & Tracking</TabsTrigger>
                <TabsTrigger value="footer">Footer</TabsTrigger>
              </TabsList>

              <!-- General Settings Tab -->
              <TabsContent value="general">
                <Card>
                  <CardHeader>
                    <CardTitle class="dark:text-white">Pengaturan Umum</CardTitle>
                    <CardDescription class="dark:text-gray-300">
                      Pengaturan dasar website seperti logo, favicon, judul, dan deskripsi.
                    </CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                      <!-- Logo -->
                      <div>
                        <InputLabel for="logo" value="Logo Website" class="dark:text-white" />
                        <div class="mt-2">
                          <div v-if="logoPreview || logo" class="mb-2">
                            <img :src="logoPreview || logo" alt="Logo Preview" class="h-12 w-auto object-contain" />
                          </div>
                          <FileInput
                            id="logo"
                            @input="handleLogoChange"
                            type="file"
                            accept="image/*"
                            class="block w-full dark:bg-primary-700 dark:text-white dark:border-gray-600"
                          />
                          <p v-if="form.errors.logo" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ form.errors.logo }}</p>
                        </div>
                      </div>

                      <!-- Favicon -->
                      <div>
                        <InputLabel for="favicon" value="Favicon" class="dark:text-white" />
                        <div class="mt-2">
                          <div v-if="faviconPreview || favicon" class="mb-2">
                            <img :src="faviconPreview || favicon" alt="Favicon Preview" class="h-8 w-auto object-contain" />
                          </div>
                          <FileInput
                            id="favicon"
                            @input="handleFaviconChange"
                            type="file"
                            accept="image/*"
                            class="block w-full dark:bg-primary-700 dark:text-white dark:border-gray-600"
                          />
                          <p v-if="form.errors.favicon" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ form.errors.favicon }}</p>
                        </div>
                      </div>

                      <!-- Site Title -->
                      <div>
                        <InputLabel for="site_title" value="Judul Website" class="dark:text-white" />
                        <div class="mt-2">
                          <TextInput
                            id="site_title"
                            v-model="form.site_title"
                            type="text"
                            class="block w-full dark:bg-primary-700 dark:text-white dark:border-gray-600"
                            required
                          />
                          <p v-if="form.errors.site_title" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ form.errors.site_title }}</p>
                        </div>
                      </div>

                      <!-- Site Subtitle -->
                      <div>
                        <InputLabel for="site_subtitle" value="Sub Judul Website" class="dark:text-white" />
                        <div class="mt-2">
                          <TextInput
                            id="site_subtitle"
                            v-model="form.site_subtitle"
                            type="text"
                            class="block w-full dark:bg-primary-700 dark:text-white dark:border-gray-600"
                          />
                          <p v-if="form.errors.site_subtitle" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ form.errors.site_subtitle }}</p>
                        </div>
                      </div>

                      <!-- Site Description -->
                      <div class="sm:col-span-2">
                        <InputLabel for="site_description" value="Deskripsi Website" class="dark:text-white" />
                        <div class="mt-2">
                          <TextArea
                            id="site_description"
                            v-model="form.site_description"
                            rows="3"
                            class="block w-full dark:bg-primary-700 dark:text-white dark:border-gray-600"
                          />
                          <p v-if="form.errors.site_description" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ form.errors.site_description }}</p>
                        </div>
                      </div>

                      <!-- Global Thumbnail -->
                      <div class="sm:col-span-2">
                        <InputLabel for="thumbnail" value="Thumbnail Global" class="dark:text-white" />
                        <div class="mt-2">
                          <div v-if="thumbnailPreview || thumbnail" class="mb-2">
                            <img :src="thumbnailPreview || thumbnail" alt="Thumbnail Preview" class="h-32 w-auto object-contain" />
                          </div>
                          <FileInput
                            id="thumbnail"
                            @input="handleThumbnailChange"
                            type="file"
                            accept="image/*"
                            class="block w-full dark:bg-primary-700 dark:text-white dark:border-gray-600"
                          />
                          <p v-if="form.errors.thumbnail" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ form.errors.thumbnail }}</p>
                        </div>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </TabsContent>

              <!-- Tracking Scripts Tab -->
              <TabsContent value="tracking">
                <Card>
                  <CardHeader>
                    <CardTitle class="dark:text-white">Script Tracking & Pixel</CardTitle>
                    <CardDescription class="dark:text-gray-300">
                      Konfigurasi script tracking dan pixel untuk analitik website.
                    </CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div class="space-y-6">
                      <!-- Meta Pixel -->
                      <div>
                        <InputLabel for="meta_pixel" value="Meta (Facebook) Pixel" class="dark:text-white" />
                        <div class="mt-2">
                          <TextArea
                            id="meta_pixel"
                            v-model="form.meta_pixel"
                            rows="3"
                            class="block w-full font-mono text-sm dark:bg-primary-700 dark:text-white dark:border-gray-600"
                            placeholder="<!-- Meta Pixel Code -->"
                          />
                          <p v-if="form.errors.meta_pixel" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ form.errors.meta_pixel }}</p>
                        </div>
                      </div>

                      <!-- Google Analytics -->
                      <div>
                        <InputLabel for="google_analytics" value="Google Analytics" class="dark:text-white" />
                        <div class="mt-2">
                          <TextArea
                            id="google_analytics"
                            v-model="form.google_analytics"
                            rows="3"
                            class="block w-full font-mono text-sm dark:bg-primary-700 dark:text-white dark:border-gray-600"
                            placeholder="<!-- Google Analytics Code -->"
                          />
                          <p v-if="form.errors.google_analytics" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ form.errors.google_analytics }}</p>
                        </div>
                      </div>

                      <!-- TikTok Pixel -->
                      <div>
                        <InputLabel for="tiktok_pixel" value="TikTok Pixel" class="dark:text-white" />
                        <div class="mt-2">
                          <TextArea
                            id="tiktok_pixel"
                            v-model="form.tiktok_pixel"
                            rows="3"
                            class="block w-full font-mono text-sm dark:bg-primary-700 dark:text-white dark:border-gray-600"
                            placeholder="<!-- TikTok Pixel Code -->"
                          />
                          <p v-if="form.errors.tiktok_pixel" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ form.errors.tiktok_pixel }}</p>
                        </div>
                      </div>

                      <!-- Twitter/X Pixel -->
                      <div>
                        <InputLabel for="twitter_pixel" value="Twitter/X Pixel" class="dark:text-white" />
                        <div class="mt-2">
                          <TextArea
                            id="twitter_pixel"
                            v-model="form.twitter_pixel"
                            rows="3"
                            class="block w-full font-mono text-sm dark:bg-primary-700 dark:text-white dark:border-gray-600"
                            placeholder="<!-- Twitter/X Website Tag -->"
                          />
                          <p v-if="form.errors.twitter_pixel" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ form.errors.twitter_pixel }}</p>
                        </div>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </TabsContent>

              <!-- Footer Settings Tab -->
              <TabsContent value="footer">
                <Card>
                  <CardHeader>
                    <CardTitle>Pengaturan Footer</CardTitle>
                    <CardDescription>
                      Konfigurasi teks dan informasi yang ditampilkan di footer website.
                    </CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div>
                      <InputLabel for="footer_copyright" value="Copyright Text" />
                      <div class="mt-2">
                        <TextInput
                          id="footer_copyright"
                          v-model="form.footer_copyright"
                          type="text"
                          class="block w-full"
                          placeholder=" 2025 Your Company. All rights reserved."
                        />
                        <p v-if="form.errors.footer_copyright" class="mt-2 text-sm text-red-600">{{ form.errors.footer_copyright }}</p>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </TabsContent>
            </Tabs>

            <!-- Save Button -->
            <div class="mt-6 flex justify-end">
              <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Simpan Perubahan
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
import { useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import FileInput from '@/Components/FileInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/Components/ui/tabs'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'

const props = defineProps({
  auth: Object,
  flash: Object,
})

const page = usePage()
const settings = computed(() => page.props.settings || {})

// Debugging untuk melihat data settings
console.log('Settings di halaman Settings:', page.props.settings)
console.log('General di halaman Settings:', settings.value)
console.log('Auth di halaman Settings:', props.auth)
console.log('Logo di halaman Settings:', settings.value?.logo)

const logoPreview = ref(null)
const faviconPreview = ref(null)
const thumbnailPreview = ref(null)

// Untuk notifikasi hanya setelah submit
const showFlash = ref(false)

// Tab aktif
const activeTab = ref('general')

const localFlash = ref(null)
let lastFlashMessage = null

onMounted(() => {
  if (settings.value?.logo) logoPreview.value = null // biar preview hanya muncul jika upload baru
  if (settings.value?.favicon) faviconPreview.value = null
  if (settings.value?.thumbnail) thumbnailPreview.value = null
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
  form.post(route('admin.settings.update'), {
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
</script>
