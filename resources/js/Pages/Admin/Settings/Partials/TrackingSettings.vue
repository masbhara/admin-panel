<template>
  <div class="space-y-6">
    <!-- Meta Pixel -->
    <div>
      <InputLabel for="meta_pixel" value="Meta (Facebook) Pixel" :class="themeClasses.label" />
      <div class="mt-2">
        <TextArea
          id="meta_pixel"
          v-model="form.meta_pixel"
          rows="3"
          :class="[themeClasses.input, 'block w-full font-mono text-sm rounded-lg']"
          placeholder="<!-- Meta Pixel Code -->"
        />
        <p v-if="form.errors.meta_pixel" :class="[themeClasses.statusDanger, 'mt-2 text-sm']">{{ form.errors.meta_pixel }}</p>
      </div>
    </div>

    <!-- Google Analytics -->
    <div>
      <InputLabel for="google_analytics" value="Google Analytics" :class="themeClasses.label" />
      <div class="mt-2">
        <TextArea
          id="google_analytics"
          v-model="form.google_analytics"
          rows="3"
          :class="[themeClasses.input, 'block w-full font-mono text-sm rounded-lg']"
          placeholder="<!-- Google Analytics Code -->"
        />
        <p v-if="form.errors.google_analytics" :class="[themeClasses.statusDanger, 'mt-2 text-sm']">{{ form.errors.google_analytics }}</p>
      </div>
    </div>

    <!-- TikTok Pixel -->
    <div>
      <InputLabel for="tiktok_pixel" value="TikTok Pixel" :class="themeClasses.label" />
      <div class="mt-2">
        <TextArea
          id="tiktok_pixel"
          v-model="form.tiktok_pixel"
          rows="3"
          :class="[themeClasses.input, 'block w-full font-mono text-sm rounded-lg']"
          placeholder="<!-- TikTok Pixel Code -->"
        />
        <p v-if="form.errors.tiktok_pixel" :class="[themeClasses.statusDanger, 'mt-2 text-sm']">{{ form.errors.tiktok_pixel }}</p>
      </div>
    </div>

    <!-- Twitter/X Pixel -->
    <div>
      <InputLabel for="twitter_pixel" value="Twitter/X Pixel" :class="themeClasses.label" />
      <div class="mt-2">
        <TextArea
          id="twitter_pixel"
          v-model="form.twitter_pixel"
          rows="3"
          :class="[themeClasses.input, 'block w-full font-mono text-sm rounded-lg']"
          placeholder="<!-- Twitter/X Website Tag -->"
        />
        <p v-if="form.errors.twitter_pixel" :class="[themeClasses.statusDanger, 'mt-2 text-sm']">{{ form.errors.twitter_pixel }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useThemeClasses } from '@/Composables/useThemeClasses'
import InputLabel from '@/Components/InputLabel.vue'
import TextArea from '@/Components/TextArea.vue'

const themeClasses = useThemeClasses()

defineProps({
  form: Object,
})
</script> 