<template>
  <button 
    type="button"
    @click="toggleTheme"
    class="relative inline-flex h-[36px] w-[72px] shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-secondary-200 transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:bg-secondary-800"
    :aria-checked="isDark"
    role="switch"
    aria-labelledby="theme-toggle"
  >
    <span class="sr-only">Toggle Dark Mode</span>
    
    <!-- Sun and Moon icons with smooth transition -->
    <span 
      aria-hidden="true" 
      class="pointer-events-none absolute inset-0 flex items-center justify-around"
    >
      <!-- Sun icon (visible in light mode) -->
      <svg 
        xmlns="http://www.w3.org/2000/svg" 
        class="h-5 w-5 text-amber-500 transition-opacity duration-300 ease-in"
        :class="isDark ? 'opacity-0' : 'opacity-100'"
        fill="none" 
        viewBox="0 0 24 24" 
        stroke="currentColor"
      >
        <path 
          stroke-linecap="round" 
          stroke-linejoin="round" 
          stroke-width="2" 
          d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" 
        />
      </svg>
      
      <!-- Moon icon (visible in dark mode) -->
      <svg 
        xmlns="http://www.w3.org/2000/svg" 
        class="h-5 w-5 text-indigo-200 transition-opacity duration-300 ease-in" 
        :class="isDark ? 'opacity-100' : 'opacity-0'"
        fill="none" 
        viewBox="0 0 24 24" 
        stroke="currentColor"
      >
        <path 
          stroke-linecap="round" 
          stroke-linejoin="round" 
          stroke-width="2" 
          d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" 
        />
      </svg>
    </span>
    
    <!-- Toggle Slider -->
    <span 
      aria-hidden="true" 
      class="pointer-events-none inline-block h-[32px] w-[32px] transform rounded-full bg-white shadow-lg ring-0 transition duration-300 ease-in-out dark:bg-secondary-100"
      :class="isDark ? 'translate-x-[38px]' : 'translate-x-0'"
    />
  </button>
</template>

<script setup>
import { ref, onMounted } from 'vue';

// State to track dark mode
const isDark = ref(false);

// Function to update dark mode state based on HTML class
const updateDarkModeState = () => {
  isDark.value = document.documentElement.classList.contains('dark');
};

// Function to toggle theme
const toggleTheme = () => {
  // Get current theme and toggle it
  const htmlEl = document.documentElement;
  const currentTheme = htmlEl.classList.contains('dark') ? 'dark' : 'light';
  const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
  
  // Toggle class dark pada element html
  if (newTheme === 'dark') {
    htmlEl.classList.add('dark');
  } else {
    htmlEl.classList.remove('dark');
  }
  
  // Update dark mode state
  isDark.value = newTheme === 'dark';
  
  // Save theme preference
  localStorage.setItem('theme', newTheme);
  
  // Apply theme switching animation to body
  document.body.classList.add('theme-transitioning');
  setTimeout(() => {
    document.body.classList.remove('theme-transitioning');
  }, 300);
  
  // Trigger CSS variables update
  updateCSSVariables(newTheme);
};

// Update CSS variables based on theme
const updateCSSVariables = (theme) => {
  const cssVars = window.getComputedStyle(document.documentElement);
  const isDarkMode = theme === 'dark';
  
  // Background colors
  document.documentElement.style.setProperty('--color-bg-primary', 
    isDarkMode ? '#0f172a' : '#ffffff');
  document.documentElement.style.setProperty('--color-bg-secondary', 
    isDarkMode ? '#1e293b' : '#f1f5f9');
  document.documentElement.style.setProperty('--color-bg-tertiary', 
    isDarkMode ? '#334155' : '#e2e8f0');
    
  // Text colors
  document.documentElement.style.setProperty('--color-text-primary', 
    isDarkMode ? '#f8fafc' : '#0f172a');
  document.documentElement.style.setProperty('--color-text-secondary', 
    isDarkMode ? '#e2e8f0' : '#475569');
  document.documentElement.style.setProperty('--color-text-tertiary', 
    isDarkMode ? '#94a3b8' : '#94a3b8');
  document.documentElement.style.setProperty('--color-text-inverted', 
    isDarkMode ? '#0f172a' : '#f8fafc');
    
  // Border colors
  document.documentElement.style.setProperty('--color-border-light', 
    isDarkMode ? '#334155' : '#e2e8f0');
  document.documentElement.style.setProperty('--color-border-default', 
    isDarkMode ? '#475569' : '#cbd5e1');
  document.documentElement.style.setProperty('--color-border-dark', 
    isDarkMode ? '#64748b' : '#94a3b8');
};

// Initialize
onMounted(() => {
  // Set initial dark mode state
  updateDarkModeState();
  
  // Set initial CSS variables based on current theme
  updateCSSVariables(isDark.value ? 'dark' : 'light');
  
  // Watch for changes to dark mode in the DOM
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (
        mutation.type === 'attributes' &&
        mutation.attributeName === 'class'
      ) {
        updateDarkModeState();
      }
    });
  });

  observer.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['class'],
  });
});
</script> 