import { themeColors } from '@/theme'

// Fungsi untuk mengatur CSS variables
function setCSSVariables(theme = 'light') {
  const colors = themeColors[theme]
  
  // Set primary colors
  Object.entries(colors.primary).forEach(([key, value]) => {
    document.documentElement.style.setProperty(`--primary-${key}`, value)
  })
  
  // Set secondary colors
  Object.entries(colors.secondary).forEach(([key, value]) => {
    document.documentElement.style.setProperty(`--secondary-${key}`, value)
  })
  
  // Set background colors
  Object.entries(colors.background).forEach(([key, value]) => {
    document.documentElement.style.setProperty(`--background-${key}`, value)
  })
  
  // Set text colors
  Object.entries(colors.text).forEach(([key, value]) => {
    document.documentElement.style.setProperty(`--text-${key}`, value)
  })
  
  // Set border colors
  Object.entries(colors.border).forEach(([key, value]) => {
    document.documentElement.style.setProperty(`--border-${key.toLowerCase()}`, value)
  })
  
  // Set status colors
  Object.entries(colors.status).forEach(([key, value]) => {
    document.documentElement.style.setProperty(`--status-${key}`, value)
  })
}

// Plugin Vue
export default {
  install(app) {
    // Inisialisasi theme
    const theme = localStorage.getItem('theme') || 'light'
    setCSSVariables(theme)
    
    // Listen untuk perubahan theme
    window.addEventListener('theme-changed', (e) => {
      setCSSVariables(e.detail.theme)
    })
    
    // Provide theme utilities ke komponen
    app.provide('setTheme', (theme) => {
      localStorage.setItem('theme', theme)
      setCSSVariables(theme)
      document.documentElement.classList.toggle('dark', theme === 'dark')
    })
  }
} 