<template>
  <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
    <!-- Logo -->
    <div class="col-span-1">
      <InputLabel for="logo" value="Logo Website" :class="themeClasses.label" />
      <div class="mt-2">
        <div v-if="logoPreviewUrl" class="mb-4 flex items-center justify-center rounded-lg border-2 border-dashed border-border-light bg-background-secondary p-4">
          <img :src="logoPreviewUrl" alt="Logo Preview" class="max-h-16 w-auto object-contain" />
        </div>
        <div class="relative">
          <FileInput
            id="logo"
            @input="handleLogoChange"
            type="file"
            accept="image/*"
            :class="[
              'block w-full rounded-lg border-2 border-dashed',
              'focus:border-primary-500 focus:ring-primary-500',
              'text-sm text-text-secondary file:mr-4 file:py-2 file:px-4',
              'file:rounded-lg file:border-0 file:bg-primary-500/10',
              'file:text-primary-600 file:text-sm file:font-medium',
              'hover:file:bg-primary-500/20 cursor-pointer'
            ]"
          />
        </div>
        <p v-if="form.errors.logo" class="mt-2 text-sm text-status-danger">{{ form.errors.logo }}</p>
        <p class="mt-1 text-sm text-text-secondary">Upload logo website dalam format PNG atau SVG.</p>
      </div>
    </div>

    <!-- Favicon -->
    <div class="col-span-1">
      <InputLabel for="favicon" value="Favicon" :class="themeClasses.label" />
      <div class="mt-2">
        <div v-if="faviconPreviewUrl" class="mb-4 flex items-center justify-center rounded-lg border-2 border-dashed border-border-light bg-background-secondary p-4">
          <img :src="faviconPreviewUrl" alt="Favicon Preview" class="max-h-12 w-auto object-contain" />
        </div>
        <div class="relative">
          <FileInput
            id="favicon"
            @input="handleFaviconChange"
            type="file"
            accept="image/*"
            :class="[
              'block w-full rounded-lg border-2 border-dashed',
              'focus:border-primary-500 focus:ring-primary-500',
              'text-sm text-text-secondary file:mr-4 file:py-2 file:px-4',
              'file:rounded-lg file:border-0 file:bg-primary-500/10',
              'file:text-primary-600 file:text-sm file:font-medium',
              'hover:file:bg-primary-500/20 cursor-pointer'
            ]"
          />
        </div>
        <p v-if="form.errors.favicon" class="mt-2 text-sm text-status-danger">{{ form.errors.favicon }}</p>
        <p class="mt-1 text-sm text-text-secondary">Upload favicon dalam format ICO, PNG, atau SVG.</p>
      </div>
    </div>

    <!-- Site Title -->
    <div class="col-span-1">
      <InputLabel for="site_title" value="Judul Website" :class="themeClasses.label" />
      <div class="mt-2">
        <TextInput
          id="site_title"
          v-model="form.site_title"
          type="text"
          :class="[
            'block w-full rounded-lg',
            'border-border-light focus:border-primary-500 focus:ring-primary-500',
            'placeholder-text-secondary/50'
          ]"
          placeholder="Masukkan judul website"
          required
        />
        <p v-if="form.errors.site_title" class="mt-2 text-sm text-status-danger">{{ form.errors.site_title }}</p>
      </div>
    </div>

    <!-- Site Subtitle -->
    <div class="col-span-1">
      <InputLabel for="site_subtitle" value="Sub Judul Website" :class="themeClasses.label" />
      <div class="mt-2">
        <TextInput
          id="site_subtitle"
          v-model="form.site_subtitle"
          type="text"
          :class="[
            'block w-full rounded-lg',
            'border-border-light focus:border-primary-500 focus:ring-primary-500',
            'placeholder-text-secondary/50'
          ]"
          placeholder="Masukkan sub judul website"
        />
        <p v-if="form.errors.site_subtitle" class="mt-2 text-sm text-status-danger">{{ form.errors.site_subtitle }}</p>
      </div>
    </div>

    <!-- Site Description -->
    <div class="col-span-2">
      <InputLabel for="site_description" value="Deskripsi Website" :class="themeClasses.label" />
      <div class="mt-2">
        <TextArea
          id="site_description"
          v-model="form.site_description"
          rows="3"
          :class="[
            'block w-full rounded-lg',
            'border-border-light focus:border-primary-500 focus:ring-primary-500',
            'placeholder-text-secondary/50'
          ]"
          placeholder="Masukkan deskripsi website"
        />
        <p v-if="form.errors.site_description" class="mt-2 text-sm text-status-danger">{{ form.errors.site_description }}</p>
      </div>
    </div>

    <!-- Global Thumbnail -->
    <div class="col-span-2">
      <InputLabel for="thumbnail" value="Thumbnail Global" :class="themeClasses.label" />
      <div class="mt-2">
        <div class="mb-4 flex items-center justify-center rounded-lg border-2 border-dashed border-border-light bg-background-secondary p-4 min-h-[96px]">
          <img v-if="thumbnailPreviewUrl" :src="thumbnailPreviewUrl" alt="Thumbnail Preview" class="max-h-48 w-auto object-contain" />
        </div>
        <div class="relative">
          <FileInput
            id="thumbnail"
            @input="handleThumbnailChange"
            type="file"
            accept="image/*"
            :class="[
              'block w-full rounded-lg border-2 border-dashed',
              'focus:border-primary-500 focus:ring-primary-500',
              'text-sm text-text-secondary file:mr-4 file:py-2 file:px-4',
              'file:rounded-lg file:border-0 file:bg-primary-500/10',
              'file:text-primary-600 file:text-sm file:font-medium',
              'hover:file:bg-primary-500/20 cursor-pointer'
            ]"
          />
        </div>
        <p v-if="form.errors.thumbnail" class="mt-2 text-sm text-status-danger">{{ form.errors.thumbnail }}</p>
        <p class="mt-1 text-sm text-text-secondary">Upload thumbnail default untuk sharing di media sosial.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useThemeClasses } from '@/Composables/useThemeClasses'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import FileInput from '@/Components/FileInput.vue'

const themeClasses = useThemeClasses()

const props = defineProps({
  form: Object,
  logo: [String, Object],
  favicon: [String, Object],
  thumbnail: [String, Object],
  logoPreview: [String, Object],
  faviconPreview: [String, Object],
  thumbnailPreview: [String, Object],
  handleLogoChange: Function,
  handleFaviconChange: Function,
  handleThumbnailChange: Function,
})

// Helper agar bisa handle ref atau string
const logoPreviewUrl = computed(() => props.logoPreview?.value ?? props.logoPreview ?? props.logo ?? '')
const faviconPreviewUrl = computed(() => props.faviconPreview?.value ?? props.faviconPreview ?? props.favicon ?? '')
const thumbnailPreviewUrl = computed(() => props.thumbnailPreview?.value ?? props.thumbnailPreview ?? props.thumbnail ?? '')
</script> 