<template>
  <div>
    <InputLabel for="footer_copyright" value="Copyright Text" :class="themeClasses.label" />
    <div class="mt-2">
      <TextInput
        id="footer_copyright"
        v-model="form.footer_copyright"
        type="text"
        :class="[themeClasses.input, 'block w-full rounded-lg']"
        placeholder=" 2025 Your Company. All rights reserved."
      />
      <p v-if="form.errors.footer_copyright" :class="[themeClasses.statusDanger, 'mt-2 text-sm']">{{ form.errors.footer_copyright }}</p>
    </div>
  </div>
</template>

<script setup>
import { useThemeClasses } from '@/Composables/useThemeClasses'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'

const themeClasses = useThemeClasses()

defineProps({
  form: Object,
})
</script> 