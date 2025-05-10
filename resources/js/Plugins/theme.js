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

const ThemePlugin = {
    install: (app) => {
        app.config.globalProperties.$theme = {
            isDark: false,
            toggle() {
                this.isDark = !this.isDark;
                document.documentElement.classList.toggle('dark');
            },
            setDark() {
                this.isDark = true;
                document.documentElement.classList.add('dark');
            },
            setLight() {
                this.isDark = false;
                document.documentElement.classList.remove('dark');
            }
        };
    }
};

export default ThemePlugin; 