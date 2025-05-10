<template>
  <div :class="[
    'min-h-screen font-sans antialiased',
    'transition-colors duration-300 ease-in-out',
    'bg-background-primary text-text-primary'
  ]">
    <slot />
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { themeColors } from '@/theme.js';

// Fungsi untuk mengatur CSS variables berdasarkan tema
const setCSSVariables = (theme) => {
  const themeMode = theme === 'dark' ? 'dark' : 'light';
  const colors = themeColors[themeMode];
  
  // Set semua warna background
  Object.entries(colors.background).forEach(([key, value]) => {
    document.documentElement.style.setProperty(`--background-${key}`, value);
  });
  
  // Set semua warna text
  Object.entries(colors.text).forEach(([key, value]) => {
    document.documentElement.style.setProperty(`--text-${key}`, value);
  });
  
  // Set semua warna border
  Object.entries(colors.border).forEach(([key, value]) => {
    document.documentElement.style.setProperty(`--border-${key}`, value);
  });
  
  // Set semua warna primary
  Object.entries(colors.primary).forEach(([key, value]) => {
    document.documentElement.style.setProperty(`--primary-${key}`, value);
  });
  
  // Set semua warna secondary
  Object.entries(colors.secondary).forEach(([key, value]) => {
    document.documentElement.style.setProperty(`--secondary-${key}`, value);
  });
  
  // Set semua warna status
  Object.entries(colors.status).forEach(([key, value]) => {
    document.documentElement.style.setProperty(`--status-${key}`, value);
  });
};

onMounted(() => {
  // Cek tema saat ini dan terapkan CSS variables
  const isDark = document.documentElement.classList.contains('dark');
  setCSSVariables(isDark ? 'dark' : 'light');
  
  // Observer untuk perubahan tema
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (
        mutation.type === 'attributes' &&
        mutation.attributeName === 'class'
      ) {
        const isDark = document.documentElement.classList.contains('dark');
        setCSSVariables(isDark ? 'dark' : 'light');
      }
    });
  });
  
  observer.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['class'],
  });

  // Listen untuk event perubahan tema
  window.addEventListener('theme-changed', (event) => {
    setCSSVariables(event.detail.theme);
  });
});
</script>

<style>
/* Transisi untuk perubahan tema */
.theme-transitioning,
.theme-transitioning * {
  transition-property: background-color, border-color, color, fill, stroke;
  transition-duration: 300ms;
  transition-timing-function: ease-in-out;
}

/* Reset transisi setelah selesai */
.theme-transitioning-done,
.theme-transitioning-done * {
  transition: none !important;
}
</style> 