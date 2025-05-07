<template>
  <div>
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
  
  // Background colors
  document.documentElement.style.setProperty('--color-bg-primary', 
    colors.background.primary);
  document.documentElement.style.setProperty('--color-bg-secondary', 
    colors.background.secondary);
  document.documentElement.style.setProperty('--color-bg-tertiary', 
    colors.background.tertiary);
    
  // Text colors
  document.documentElement.style.setProperty('--color-text-primary', 
    colors.text.primary);
  document.documentElement.style.setProperty('--color-text-secondary', 
    colors.text.secondary);
  document.documentElement.style.setProperty('--color-text-tertiary', 
    colors.text.tertiary);
  document.documentElement.style.setProperty('--color-text-inverted', 
    colors.text.inverted);
    
  // Border colors
  document.documentElement.style.setProperty('--color-border-light', 
    colors.border.light);
  document.documentElement.style.setProperty('--color-border-default', 
    colors.border.default);
  document.documentElement.style.setProperty('--color-border-dark', 
    colors.border.dark);
  
  // Primary colors - ambil dari objek colors.primary
  for (const [key, value] of Object.entries(colors.primary)) {
    document.documentElement.style.setProperty(`--color-primary-${key}`, value);
  }
  
  // Secondary colors - ambil dari objek colors.secondary
  for (const [key, value] of Object.entries(colors.secondary)) {
    document.documentElement.style.setProperty(`--color-secondary-${key}`, value);
  }

  // Status colors (jika ada di UI)
  for (const [key, value] of Object.entries(colors.status)) {
    document.documentElement.style.setProperty(`--color-status-${key}`, value);
  }
  
  console.log(`Theme applied: ${themeMode}`, colors);
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

  // Debug untuk memastikan nilai diambil dengan benar
  window.addEventListener('theme-changed', (event) => {
    console.log('Theme changed event received', event.detail);
    setCSSVariables(event.detail.theme);
  });
});
</script> 