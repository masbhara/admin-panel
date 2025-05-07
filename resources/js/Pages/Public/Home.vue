<template>
  <PublicLayout>
    <Head title="Beranda" />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Hero Section -->
      <div class="text-center mb-16">
        <h1 class="text-4xl md:text-5xl font-bold text-primary-600 dark:text-primary-400 mb-4">
          Selamat Datang di Perusahaan Kami
        </h1>
        <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
          Kami menyediakan solusi terbaik untuk kebutuhan bisnis Anda dengan layanan profesional dan produk berkualitas tinggi.
        </p>
        <div class="mt-8">
          <a href="#layanan" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
            Layanan Kami
          </a>
          <a href="#kontak" class="ml-4 inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
            Hubungi Kami
          </a>
        </div>
      </div>

      <!-- Features Section -->
      <div id="layanan" class="py-12">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white text-center mb-12">
          Layanan Unggulan
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Feature 1 -->
          <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition">
            <div class="text-primary-600 dark:text-primary-400 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Pengembangan Web</h3>
            <p class="text-gray-600 dark:text-gray-300">
              Kami menyediakan layanan pengembangan web profesional dengan teknologi terkini untuk kebutuhan bisnis Anda.
            </p>
          </div>

          <!-- Feature 2 -->
          <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition">
            <div class="text-primary-600 dark:text-primary-400 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Aplikasi Mobile</h3>
            <p class="text-gray-600 dark:text-gray-300">
              Kembangkan aplikasi mobile yang responsif dan intuitif untuk platform Android dan iOS.
            </p>
          </div>

          <!-- Feature 3 -->
          <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition">
            <div class="text-primary-600 dark:text-primary-400 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Konsultasi IT</h3>
            <p class="text-gray-600 dark:text-gray-300">
              Dapatkan solusi terbaik dengan layanan konsultasi IT profesional dari tim ahli kami.
            </p>
          </div>
        </div>
      </div>

      <!-- About Section -->
      <div class="py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
          <div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Tentang Kami</h2>
            <p class="text-gray-600 dark:text-gray-300 mb-4">
              Perusahaan kami telah berpengalaman selama 10 tahun dalam industri teknologi informasi, menyediakan solusi inovatif bagi berbagai klien dari berbagai sektor.
            </p>
            <p class="text-gray-600 dark:text-gray-300 mb-6">
              Dengan tim profesional yang berdedikasi, kami berkomitmen untuk memberikan layanan terbaik dan membangun hubungan jangka panjang dengan setiap klien.
            </p>
            <Link href="/public/about" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
              Pelajari Lebih Lanjut
            </Link>
          </div>
          <div class="bg-gray-200 dark:bg-gray-700 rounded-lg h-80 flex items-center justify-center">
            <span class="text-gray-500 dark:text-gray-400">Gambar Perusahaan</span>
          </div>
        </div>
      </div>

      <!-- Contact Section -->
      <div id="kontak" class="py-12">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white text-center mb-12">
          Hubungi Kami
        </h2>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
              <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Informasi Kontak</h3>
              <div class="space-y-4">
                <div class="flex items-start">
                  <div class="flex-shrink-0 text-primary-600 dark:text-primary-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                  </div>
                  <div class="ml-3 text-gray-600 dark:text-gray-300">
                    Jl. Contoh No. 123, Jakarta Selatan, Indonesia
                  </div>
                </div>
                <div class="flex items-start">
                  <div class="flex-shrink-0 text-primary-600 dark:text-primary-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                  </div>
                  <div class="ml-3 text-gray-600 dark:text-gray-300">
                    info@perusahaancontoh.com
                  </div>
                </div>
                <div class="flex items-start">
                  <div class="flex-shrink-0 text-primary-600 dark:text-primary-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                  </div>
                  <div class="ml-3 text-gray-600 dark:text-gray-300">
                    +62 123 4567 890
                  </div>
                </div>
              </div>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Kirim Pesan</h3>
              <form>
                <div class="mb-4">
                  <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama</label>
                  <input type="text" id="name" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" />
                </div>
                <div class="mb-4">
                  <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                  <input type="email" id="email" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" />
                </div>
                <div class="mb-4">
                  <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pesan</label>
                  <textarea id="message" rows="4" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"></textarea>
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
                  Kirim Pesan
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script> 