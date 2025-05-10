// Theme configuration - Semua warna untuk light/dark mode terpusat di sini
export const themeColors = {
  light: {
    primary: {
      50: '#f0f9ff ',
      100: '#e0f2fe',
      200: '#bae6fd',
      300: '#7dd3fc',
      400: '#38bdf8',
      500: '#0ea5e9',
      600: '#0284c7',
      700: '#0369a1',
      800: '#075985',
      900: '#0c4a6e',
      950: '#082f49',
    },
    secondary: {
      50: '#f8fafc',
      100: '#f1f5f9',
      200: '#e2e8f0',
      300: '#cbd5e1',
      400: '#94a3b8',
      500: '#64748b',
      600: '#475569',
      700: '#334155',
      800: '#1e293b',
      900: '#0f172a',
      950: '#020617',
    },
    background: {
      primary: '#F5F5F5',
      secondary: '#f8fafc',
      tertiary: '#f1f5f9',
      card: '#ffffff',
      elevated: '#ffffff',
    },
    text: {
      primary: '#0f172a',
      secondary: '#475569',
      tertiary: '#64748b',
      inverted: '#f8fafc',
      muted: '#94a3b8',
      highlight: '#0ea5e9',
    },
    border: {
      light: '#f1f5f9',
      default: '#e2e8f0',
      dark: '#cbd5e1',
      focus: '#0ea5e9',
      divider: 'rgba(0, 0, 0, 0.06)',
    },
    status: {
      success: '#10b981',
      warning: '#f59e0b',
      danger: '#ef4444',
      info: '#3b82f6',
      pending: '#f97316',
    },
  },
  dark: {
    primary: {
      50: '#f0f9ff',
      100: '#e0f2fe',
      200: '#bae6fd',
      300: '#7dd3fc',
      400: '#38bdf8',
      500: '#0ea5e9',
      600: '#0284c7',
      700: '#0369a1',
      800: '#075985',
      900: '#0c4a6e',
      950: '#082f49',
    },
    secondary: {
      50: '#f8fafc',
      100: '#f1f5f9',
      200: '#e2e8f0',
      300: '#cbd5e1',
      400: '#94a3b8',
      500: '#64748b',
      600: '#475569',
      700: '#334155',
      800: '#1e293b',
      900: '#0f172a',
      950: '#020617',
    },
    background: {
      primary: '#111827',
      secondary: '#1e293b',
      tertiary: '#283548',
      card: '#1e293b',
      elevated: '#20304b',
      input: 'rgba(31, 41, 55, 0.5)',
    },
    text: {
      primary: '#f8fafc',
      secondary: '#e2e8f0',
      tertiary: '#94a3b8',
      inverted: '#0f172a',
      muted: '#64748b',
      highlight: '#38bdf8',
    },
    border: {
      light: 'rgba(75, 85, 99, 0.5)',
      default: 'rgba(75, 85, 99, 0.5)',
      dark: 'rgba(75, 85, 99, 0.7)',
      focus: '#0ea5e9',
      divider: 'rgba(255, 255, 255, 0.06)',
    },
    status: {
      success: '#059669',
      warning: '#d97706',
      danger: '#dc2626',
      info: '#2563eb',
      pending: '#ea580c',
    },
  },
};

// Fungsi untuk mendapatkan warna dari tema
export function getThemeColor(theme, path) {
  const keys = path.split('.');
  let value = themeColors[theme];
  
  for (const key of keys) {
    value = value[key];
  }
  
  return value;
}

// Event untuk memberitahu komponen lain jika tema berubah
export const themeChangeEvent = 'theme-changed';

// Fungsi untuk mengatur tema
export function useTheme() {
  // Mendapatkan tema dari localStorage atau default ke light
  const getInitialTheme = () => {
    if (typeof window !== 'undefined' && window.localStorage) {
      const storedTheme = window.localStorage.getItem('theme');
      if (storedTheme === 'dark' || storedTheme === 'light') {
        return storedTheme;
      }
      
      // Jika preferensi sistem menggunakan dark mode
      const userMedia = window.matchMedia('(prefers-color-scheme: dark)');
      if (userMedia.matches) {
        return 'dark';
      }
    }
    
    return 'light'; // default theme
  };
  
  const toggleTheme = () => {
    if (typeof window === 'undefined') return;
    
    const htmlEl = document.documentElement;
    const bodyEl = document.body;
    const currentTheme = htmlEl.classList.contains('dark') ? 'dark' : 'light';
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    // Tambahkan class transisi untuk animasi yang lebih mulus
    bodyEl.classList.add('theme-transitioning');
    
    // Toggle class dark pada element html
    if (newTheme === 'dark') {
      htmlEl.classList.add('dark');
    } else {
      htmlEl.classList.remove('dark');
    }
    
    // Simpan tema di localStorage
    localStorage.setItem('theme', newTheme);
    
    // Dispatch event untuk memberitahu komponen lain bahwa tema berubah
    const event = new CustomEvent(themeChangeEvent, { detail: { theme: newTheme } });
    window.dispatchEvent(event);
    
    // Hapus class transitioning setelah transisi selesai
    setTimeout(() => {
      bodyEl.classList.remove('theme-transitioning');
    }, 350); // Durasi transisi ditingkatkan sedikit untuk animasi lebih mulus
  };
  
  // Fungsi untuk inisialisasi tema
  const initTheme = () => {
    if (typeof window === 'undefined') return;
    
    const theme = getInitialTheme();
    
    // Set tema pada element html
    if (theme === 'dark') {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
    
    localStorage.setItem('theme', theme);
    
    // Dispatch event untuk memberitahu komponen lain bahwa tema diinisialisasi
    const event = new CustomEvent(themeChangeEvent, { detail: { theme } });
    window.dispatchEvent(event);
  };
  
  return {
    initTheme,
    toggleTheme,
    getInitialTheme,
  };
} 