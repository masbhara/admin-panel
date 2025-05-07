// Theme configuration - Semua warna untuk light/dark mode terpusat di sini
export const themeColors = {
  light: {
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
      primary: '#f9f9f9',
      secondary: '#f1f5f9',
      tertiary: '#e2e8f0',
    },
    text: {
      primary: '#0f172a',
      secondary: '#475569',
      tertiary: '#94a3b8',
      inverted: '#f8fafc',
    },
    border: {
      light: '#e2e8f0',
      default: '#cbd5e1',
      dark: '#94a3b8',
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
    // Palette warna primary yang lebih seimbang, sidebar lebih gelap
    primary: {
      50: '#082f49',
      100: '#0c4a6e',
      200: '#075985',
      300: '#0369a1',
      400: '#0284c7', 
      500: '#393E46',
      // Sidebar utama - lebih gelap dari sebelumnya
      600: '#222831', // Diganti dari#023a52 (terlalu terang) ke#051924 (lebih gelap)
      700: '#0c4a6e', // Untuk item aktif, lebih gelap
      800: '#38bdf8', // Untuk elemen interaktif  
      900: '#e0f2fe',
      950: '#f0f9ff',
    },
    // Secondary yang lebih seimbang untuk keterbacaan
    secondary: {
      50: '#020617',
      100: '#0f172a',
      200: '#1e293b',
      300: '#334155',
      400: '#475569',
      500: '#64748b',
      // Peningkatan keterbacaan untuk teks sekunder
      600: '#94a3b8',
      700: '#cbd5e1',
      800: '#e2e8f0',
      900: '#f1f5f9',
      950: '#f8fafc',
    },
    // Background yang lebih netral dan nyaman di mata
    background: {
      primary: '#111827', // Diubah dari #0f172a ke #111827 - abu-abu gelap dengan hint biru, lebih netral
      secondary: '#1e293b', // Warna sekunder untuk kartu
      tertiary: '#283548', // Warna tersier untuk elemen nested yang ditingkatkan kontrasnya
      card: '#1e293b', // Warna khusus untuk kartu/panel
      elevated: '#20304b', // Warna untuk elemen yang ditinggikan
    },
    // Teks dengan kontras yang lebih baik
    text: {
      primary: '#f8fafc', // Putih dengan sedikit hint biru
      secondary: '#e2e8f0', // Abu-abu terang untuk teks sekunder
      tertiary: '#94a3b8', // Teks tersier lebih kontras
      inverted: '#0f172a',
      muted: '#64748b', // Teks yang diredam untuk informasi tidak penting
      highlight: '#38bdf8', // Untuk teks yang perlu disorot
      white: '#f9f9f9',
    },
    // Border dengan lebih banyak variasi
    border: {
      light: '#334155', // Border ringan
      default: '#475569', // Border standar
      dark: '#64748b', // Border yang lebih terlihat
      focus: '#0ea5e9', // Border untuk elemen yang difokuskan
      divider: 'rgba(255, 255, 255, 0.06)', // Untuk pemisah yang lebih halus
    },
    // Status colors untuk badge dan indikator
    status: {
      success: '#10b981', // Hijau emerald untuk sukses
      warning: '#fbbf24', // Kuning yang lebih lembut untuk peringatan
      danger: '#ef4444', // Merah untuk danger/error
      info: '#3b82f6', // Biru untuk informasi
      pending: '#f97316', // Oranye untuk status pending
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