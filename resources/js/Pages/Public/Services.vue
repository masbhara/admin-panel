<template>
  <PublicLayout>
    <Head title="Layanan Kami" />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Header Section -->
      <div class="text-center mb-16">
        <h1 class="text-4xl md:text-5xl font-bold text-primary-600 dark:text-primary-400 mb-4">
          Layanan Kami
        </h1>
        <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
          Kami menyediakan berbagai solusi teknologi untuk membantu bisnis Anda berkembang di era digital.
        </p>
      </div>

      <!-- Main Services Section -->
      <div class="mb-20">
        <div class="grid grid-cols-1 gap-16">
          <!-- Service 1 -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div class="order-2 md:order-1">
              <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Pengembangan Web</h2>
              <p class="text-gray-600 dark:text-gray-300 mb-6">
                Kami menciptakan website yang menarik, responsif, dan fungsional yang membantu bisnis Anda mencapai tujuannya. Tim pengembang kami menggunakan teknologi terbaru untuk menghasilkan pengalaman pengguna yang optimal.
              </p>
              <ul class="space-y-2 text-gray-600 dark:text-gray-300 mb-6">
                <li class="flex items-start">
                  <span class="text-primary-600 dark:text-primary-400 mr-2">•</span>
                  <span>Website Korporat</span>
                </li>
                <li class="flex items-start">
                  <span class="text-primary-600 dark:text-primary-400 mr-2">•</span>
                  <span>E-commerce</span>
                </li>
                <li class="flex items-start">
                  <span class="text-primary-600 dark:text-primary-400 mr-2">•</span>
                  <span>Aplikasi Web Kustom</span>
                </li>
                <li class="flex items-start">
                  <span class="text-primary-600 dark:text-primary-400 mr-2">•</span>
                  <span>Pemeliharaan & Dukungan</span>
                </li>
              </ul>
              <Link href="/public/contact" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
                Pelajari Lebih Lanjut
              </Link>
            </div>
            <div class="order-1 md:order-2 bg-gray-200 dark:bg-gray-700 rounded-lg h-64 flex items-center justify-center">
              <span class="text-gray-500 dark:text-gray-400">Ilustrasi Pengembangan Web</span>
            </div>
          </div>

          <!-- Service 2 -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div class="bg-gray-200 dark:bg-gray-700 rounded-lg h-64 flex items-center justify-center">
              <span class="text-gray-500 dark:text-gray-400">Ilustrasi Aplikasi Mobile</span>
            </div>
            <div>
              <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Pengembangan Aplikasi Mobile</h2>
              <p class="text-gray-600 dark:text-gray-300 mb-6">
                Kami mengembangkan aplikasi mobile yang intuitif dan berperforma tinggi untuk platform iOS dan Android. Aplikasi kami dirancang untuk memberikan pengalaman pengguna yang luar biasa dan membantu bisnis Anda mencapai tujuan digitalnya.
              </p>
              <ul class="space-y-2 text-gray-600 dark:text-gray-300 mb-6">
                <li class="flex items-start">
                  <span class="text-primary-600 dark:text-primary-400 mr-2">•</span>
                  <span>Aplikasi Native (iOS & Android)</span>
                </li>
                <li class="flex items-start">
                  <span class="text-primary-600 dark:text-primary-400 mr-2">•</span>
                  <span>Aplikasi Hybrid</span>
                </li>
                <li class="flex items-start">
                  <span class="text-primary-600 dark:text-primary-400 mr-2">•</span>
                  <span>Aplikasi E-commerce</span>
                </li>
                <li class="flex items-start">
                  <span class="text-primary-600 dark:text-primary-400 mr-2">•</span>
                  <span>Pemeliharaan & Dukungan</span>
                </li>
              </ul>
              <Link href="/public/contact" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
                Pelajari Lebih Lanjut
              </Link>
            </div>
          </div>

          <!-- Service 3 -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div class="order-2 md:order-1">
              <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Konsultasi IT</h2>
              <p class="text-gray-600 dark:text-gray-300 mb-6">
                Tim konsultan kami memberikan panduan strategis untuk membantu bisnis Anda memanfaatkan teknologi dengan efektif. Kami menganalisis kebutuhan Anda dan memberikan solusi yang sesuai dengan tujuan bisnis Anda.
              </p>
              <ul class="space-y-2 text-gray-600 dark:text-gray-300 mb-6">
                <li class="flex items-start">
                  <span class="text-primary-600 dark:text-primary-400 mr-2">•</span>
                  <span>Strategi Transformasi Digital</span>
                </li>
                <li class="flex items-start">
                  <span class="text-primary-600 dark:text-primary-400 mr-2">•</span>
                  <span>Audit Keamanan IT</span>
                </li>
                <li class="flex items-start">
                  <span class="text-primary-600 dark:text-primary-400 mr-2">•</span>
                  <span>Analisis Infrastruktur</span>
                </li>
                <li class="flex items-start">
                  <span class="text-primary-600 dark:text-primary-400 mr-2">•</span>
                  <span>Konsultasi Cloud</span>
                </li>
              </ul>
              <Link href="/public/contact" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
                Pelajari Lebih Lanjut
              </Link>
            </div>
            <div class="order-1 md:order-2 bg-gray-200 dark:bg-gray-700 rounded-lg h-64 flex items-center justify-center">
              <span class="text-gray-500 dark:text-gray-400">Ilustrasi Konsultasi IT</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Additional Services -->
      <div class="mb-20">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white text-center mb-12">
          Layanan Tambahan
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Additional Service 1 -->
          <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition">
            <div class="text-primary-600 dark:text-primary-400 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Analitik Data</h3>
            <p class="text-gray-600 dark:text-gray-300 mb-4">
              Kami membantu Anda memahami data bisnis Anda dengan solusi analitik yang canggih. Dapatkan wawasan yang dapat ditindaklanjuti untuk mengoptimalkan operasi dan meningkatkan ROI.
            </p>
            <Link href="/public/contact" class="text-primary-600 dark:text-primary-400 font-medium hover:text-primary-700 dark:hover:text-primary-300 transition">
              Pelajari lebih lanjut &rarr;
            </Link>
          </div>

          <!-- Additional Service 2 -->
          <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition">
            <div class="text-primary-600 dark:text-primary-400 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Keamanan Cyber</h3>
            <p class="text-gray-600 dark:text-gray-300 mb-4">
              Lindungi bisnis Anda dari ancaman cyber dengan solusi keamanan komprehensif kami. Kami menawarkan audit keamanan, pengujian penetrasi, dan implementasi kebijakan keamanan.
            </p>
            <Link href="/public/contact" class="text-primary-600 dark:text-primary-400 font-medium hover:text-primary-700 dark:hover:text-primary-300 transition">
              Pelajari lebih lanjut &rarr;
            </Link>
          </div>

          <!-- Additional Service 3 -->
          <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition">
            <div class="text-primary-600 dark:text-primary-400 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Solusi Cloud</h3>
            <p class="text-gray-600 dark:text-gray-300 mb-4">
              Tingkatkan skalabilitas dan fleksibilitas dengan solusi cloud kami. Kami membantu migrasi cloud, pengoptimalan, dan pengelolaan infrastruktur cloud Anda.
            </p>
            <Link href="/public/contact" class="text-primary-600 dark:text-primary-400 font-medium hover:text-primary-700 dark:hover:text-primary-300 transition">
              Pelajari lebih lanjut &rarr;
            </Link>
          </div>
        </div>
      </div>

      <!-- Process Section -->
      <div class="mb-20">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white text-center mb-12">
          Proses Kerja Kami
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <!-- Step 1 -->
          <div class="flex flex-col items-center">
            <div class="bg-primary-600 dark:bg-primary-500 text-white rounded-full w-12 h-12 flex items-center justify-center text-xl font-bold mb-4">1</div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 text-center">Konsultasi</h3>
            <p class="text-gray-600 dark:text-gray-300 text-center">
              Kami mendengarkan kebutuhan Anda dan memahami tujuan bisnis Anda.
            </p>
          </div>

          <!-- Step 2 -->
          <div class="flex flex-col items-center">
            <div class="bg-primary-600 dark:bg-primary-500 text-white rounded-full w-12 h-12 flex items-center justify-center text-xl font-bold mb-4">2</div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 text-center">Perencanaan</h3>
            <p class="text-gray-600 dark:text-gray-300 text-center">
              Kami membuat strategi dan roadmap untuk proyek Anda.
            </p>
          </div>

          <!-- Step 3 -->
          <div class="flex flex-col items-center">
            <div class="bg-primary-600 dark:bg-primary-500 text-white rounded-full w-12 h-12 flex items-center justify-center text-xl font-bold mb-4">3</div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 text-center">Eksekusi</h3>
            <p class="text-gray-600 dark:text-gray-300 text-center">
              Tim kami mengembangkan solusi dengan standar kualitas tinggi.
            </p>
          </div>

          <!-- Step 4 -->
          <div class="flex flex-col items-center">
            <div class="bg-primary-600 dark:bg-primary-500 text-white rounded-full w-12 h-12 flex items-center justify-center text-xl font-bold mb-4">4</div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 text-center">Dukungan</h3>
            <p class="text-gray-600 dark:text-gray-300 text-center">
              Kami menyediakan dukungan berkelanjutan dan pemeliharaan.
            </p>
          </div>
        </div>
      </div>

      <!-- CTA Section -->
      <div>
        <div class="bg-primary-600 dark:bg-primary-700 rounded-lg shadow-md p-8 text-center">
          <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">
            Siap Untuk Memulai Proyek Anda?
          </h2>
          <p class="text-white text-opacity-90 mb-8 max-w-3xl mx-auto">
            Hubungi kami sekarang untuk konsultasi gratis dan diskusikan bagaimana kami dapat membantu bisnis Anda berkembang melalui solusi teknologi yang inovatif.
          </p>
          <Link href="/public/contact" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-primary-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-primary-700 focus:ring-white transition">
            Hubungi Kami
          </Link>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script> 