// Script untuk menerapkan tema sebelum konten halaman dirender
// Ini mencegah "flash of incorrect theme" pada load pertama
(function() {
  try {
    // Cek tema dari localStorage
    var storedTheme = localStorage.getItem('theme');
    var prefersDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    // Tentukan tema yang akan dipakai
    var theme = (storedTheme === 'dark' || (!storedTheme && prefersDarkMode)) ? 'dark' : 'light';
    
    // Terapkan tema
    if (theme === 'dark') {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
    
    // Jika belum ada tema tersimpan, simpan preferensi
    if (!storedTheme) {
      localStorage.setItem('theme', theme);
    }
  } catch (e) {
    console.error('Error saat menerapkan tema:', e);
  }
})(); 