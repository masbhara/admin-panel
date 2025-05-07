<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <script>
      // Script untuk inisialisasi tema sebelum rendering (mencegah flash of incorrect theme)
      (function() {
        try {
          // Periksa tema yang disimpan
          var storedTheme = localStorage.getItem('theme');
          
          // Periksa preferensi sistem
          var prefersDarkMode = window.matchMedia && 
                               window.matchMedia('(prefers-color-scheme: dark)').matches;
          
          // Tentukan tema yang digunakan
          var useDarkMode = storedTheme === 'dark' || (!storedTheme && prefersDarkMode);
          
          // Terapkan tema ke document
          if (useDarkMode) {
            document.documentElement.classList.add('dark');
          } else {
            document.documentElement.classList.remove('dark');
          }
          
          // Simpan tema di local storage jika belum ada
          if (!storedTheme) {
            localStorage.setItem('theme', useDarkMode ? 'dark' : 'light');
          }
        } catch (e) {
          console.error('Error initializing theme:', e);
        }
      })();
    </script>
    @routes
    @vite(['resources/js/app.js'])
    @inertiaHead
  </head>
  <body class="font-sans antialiased">
    @inertia
  </body>
</html>
