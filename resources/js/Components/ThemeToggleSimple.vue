<template>
  <button 
    type="button" 
    @click="toggleTheme" 
    class="inline-flex items-center justify-center p-2 rounded-md text-text-secondary hover:text-text-primary focus:outline-none"
  >
    <!-- Sun icon (Light mode) -->
    <svg 
      v-if="isDark" 
      xmlns="http://www.w3.org/2000/svg" 
      class="h-6 w-6 text-amber-500" 
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
    
    <!-- Moon icon (Dark mode) -->
    <svg 
      v-else 
      xmlns="http://www.w3.org/2000/svg" 
      class="h-6 w-6 text-gray-800 dark:text-gray-200" 
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
  </button>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const isDark = ref(false);

// Update state
const updateThemeState = () => {
  isDark.value = document.documentElement.classList.contains('dark');
};

// Toggle theme function - versi sederhana dan andal
const toggleTheme = () => {
  try {
    // Toggle class dark pada document
    document.documentElement.classList.toggle('dark');
    
    // Simpan di localStorage
    const newTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
    localStorage.setItem('theme', newTheme);
    
    // Update state
    isDark.value = newTheme === 'dark';
    
    console.log("Theme toggled to:", newTheme);
  } catch (error) {
    console.error("Error toggling theme:", error);
  }
};

onMounted(() => {
  // Set state awal
  updateThemeState();
  
  // Observer untuk mendeteksi perubahan tema dari komponen lain
  try {
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (mutation.attributeName === 'class') {
          updateThemeState();
        }
      });
    });
    
    observer.observe(document.documentElement, {
      attributes: true,
      attributeFilter: ['class']
    });
  } catch (error) {
    console.error("Error setting up theme observer:", error);
  }
});
</script> 