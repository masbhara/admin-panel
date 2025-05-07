<template>
  <PublicLayout>
    <Head title="Tentang Kami" />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Header Section -->
      <div class="text-center mb-16">
        <h1 class="text-4xl md:text-5xl font-bold text-primary-600 dark:text-primary-400 mb-4">
          Tentang Kami
        </h1>
        <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
          Kenali lebih dalam perusahaan kami yang telah melayani berbagai klien dengan solusi teknologi informasi terbaik.
        </p>
      </div>

      <!-- Company Story -->
      <div class="mb-16">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
          <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Sejarah Perusahaan</h2>
          <div class="space-y-6">
            <p class="text-gray-600 dark:text-gray-300">
              Perusahaan kami didirikan pada tahun 2013 oleh sekelompok pengembang yang memiliki visi untuk membantu bisnis memanfaatkan teknologi secara efektif. Bermula dari sebuah ruang kerja kecil dengan 5 orang tim, kami telah berkembang menjadi perusahaan teknologi informasi terkemuka dengan lebih dari 50 profesional berbakat.
            </p>
            <p class="text-gray-600 dark:text-gray-300">
              Selama 10 tahun perjalanan kami, kami telah menyelesaikan ratusan proyek bagi klien dari berbagai industri, mulai dari startup hingga perusahaan Fortune 500. Komitmen kami terhadap kualitas, inovasi, dan kepuasan pelanggan telah membangun reputasi yang kuat di pasar.
            </p>
            <p class="text-gray-600 dark:text-gray-300">
              Saat ini, kami beroperasi dengan kantor pusat di Jakarta dan cabang di Bandung dan Surabaya, melayani klien di seluruh Indonesia dan Asia Tenggara.
            </p>
          </div>
        </div>
      </div>

      <!-- Vision & Mission -->
      <div class="mb-16">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <!-- Vision -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
            <div class="text-primary-600 dark:text-primary-400 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Visi</h3>
            <p class="text-gray-600 dark:text-gray-300">
              Menjadi pemimpin dalam transformasi digital yang membantu bisnis mencapai potensi penuh mereka melalui solusi teknologi yang inovatif dan berkelanjutan.
            </p>
          </div>

          <!-- Mission -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
            <div class="text-primary-600 dark:text-primary-400 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Misi</h3>
            <ul class="space-y-2 text-gray-600 dark:text-gray-300">
              <li class="flex items-start">
                <span class="text-primary-600 dark:text-primary-400 mr-2">•</span>
                <span>Menyediakan solusi teknologi berkualitas tinggi yang disesuaikan dengan kebutuhan spesifik klien</span>
              </li>
              <li class="flex items-start">
                <span class="text-primary-600 dark:text-primary-400 mr-2">•</span>
                <span>Mendorong inovasi melalui penelitian dan pengembangan berkelanjutan</span>
              </li>
              <li class="flex items-start">
                <span class="text-primary-600 dark:text-primary-400 mr-2">•</span>
                <span>Membangun hubungan jangka panjang dengan klien melalui layanan dan dukungan yang luar biasa</span>
              </li>
              <li class="flex items-start">
                <span class="text-primary-600 dark:text-primary-400 mr-2">•</span>
                <span>Berkontribusi pada masyarakat melalui praktik bisnis yang bertanggung jawab dan berkelanjutan</span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Team Section -->
      <div class="mb-16">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white text-center mb-12">
          Tim Kami
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Team Member 1 -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="bg-gray-200 dark:bg-gray-700 h-64 flex items-center justify-center">
              <span class="text-gray-500 dark:text-gray-400">Foto Direktur</span>
            </div>
            <div class="p-6">
              <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Budi Santoso</h3>
              <p class="text-primary-600 dark:text-primary-400 mb-4">Direktur Utama</p>
              <p class="text-gray-600 dark:text-gray-300 mb-4">
                Berpengalaman lebih dari 15 tahun dalam industri teknologi informasi dengan keahlian dalam strategi bisnis dan kepemimpinan.
              </p>
              <div class="flex space-x-4">
                <a href="#" class="text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
                  <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                  </svg>
                </a>
                <a href="#" class="text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
                  <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                  </svg>
                </a>
              </div>
            </div>
          </div>

          <!-- Team Member 2 -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="bg-gray-200 dark:bg-gray-700 h-64 flex items-center justify-center">
              <span class="text-gray-500 dark:text-gray-400">Foto CTO</span>
            </div>
            <div class="p-6">
              <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Dewi Lestari</h3>
              <p class="text-primary-600 dark:text-primary-400 mb-4">Chief Technology Officer</p>
              <p class="text-gray-600 dark:text-gray-300 mb-4">
                Ahli dalam arsitektur software dengan latar belakang di Google dan Microsoft. Membimbing tim teknis untuk mengembangkan solusi inovatif.
              </p>
              <div class="flex space-x-4">
                <a href="#" class="text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
                  <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                  </svg>
                </a>
                <a href="#" class="text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
                  <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                  </svg>
                </a>
              </div>
            </div>
          </div>

          <!-- Team Member 3 -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="bg-gray-200 dark:bg-gray-700 h-64 flex items-center justify-center">
              <span class="text-gray-500 dark:text-gray-400">Foto CMO</span>
            </div>
            <div class="p-6">
              <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Agus Pranoto</h3>
              <p class="text-primary-600 dark:text-primary-400 mb-4">Chief Marketing Officer</p>
              <p class="text-gray-600 dark:text-gray-300 mb-4">
                Spesialis pemasaran digital dengan pengalaman 10 tahun dalam mengembangkan strategi pemasaran untuk produk teknologi.
              </p>
              <div class="flex space-x-4">
                <a href="#" class="text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
                  <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                  </svg>
                </a>
                <a href="#" class="text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
                  <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                  </svg>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Values -->
      <div class="mb-16">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white text-center mb-12">
          Nilai-Nilai Kami
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <!-- Value 1 -->
          <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition">
            <div class="text-primary-600 dark:text-primary-400 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Integritas</h3>
            <p class="text-gray-600 dark:text-gray-300">
              Kami selalu menjunjung tinggi kejujuran dan etika dalam setiap aspek bisnis kami.
            </p>
          </div>

          <!-- Value 2 -->
          <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition">
            <div class="text-primary-600 dark:text-primary-400 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Inovasi</h3>
            <p class="text-gray-600 dark:text-gray-300">
              Kami terus berinovasi untuk menghadirkan solusi teknologi terbaik bagi klien kami.
            </p>
          </div>

          <!-- Value 3 -->
          <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition">
            <div class="text-primary-600 dark:text-primary-400 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Kolaborasi</h3>
            <p class="text-gray-600 dark:text-gray-300">
              Kami percaya bahwa hasil terbaik dicapai melalui kerja sama tim dan kolaborasi dengan klien.
            </p>
          </div>

          <!-- Value 4 -->
          <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition">
            <div class="text-primary-600 dark:text-primary-400 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Kualitas</h3>
            <p class="text-gray-600 dark:text-gray-300">
              Kami berkomitmen untuk memberikan layanan dan produk berkualitas tinggi yang melampaui harapan.
            </p>
          </div>
        </div>
      </div>

      <!-- CTA Section -->
      <div>
        <div class="bg-primary-600 dark:bg-primary-700 rounded-lg shadow-md p-8 text-center">
          <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">
            Siap Bekerja Sama Dengan Kami?
          </h2>
          <p class="text-white text-opacity-90 mb-8 max-w-3xl mx-auto">
            Kami selalu mencari peluang untuk bekerja sama dengan klien baru dan membantu mereka mencapai tujuan bisnis mereka melalui solusi teknologi yang inovatif.
          </p>
          <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
            <Link href="/public/services" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-primary-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-primary-700 focus:ring-white transition">
              Lihat Layanan Kami
            </Link>
            <Link href="/public/contact" class="inline-flex items-center justify-center px-6 py-3 border border-white text-base font-medium rounded-md text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-primary-700 focus:ring-white transition">
              Hubungi Kami
            </Link>
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