<template>
  <PublicLayout>
    <Head title="Beranda" />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Hero Section -->
      <!-- <div class="text-center mb-16">
        <h1 class="text-4xl md:text-5xl font-bold text-primary-600 dark:text-primary-400 mb-4">
          Pengiriman Dokumen Online
        </h1>
        <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
          Kirim dokumen Anda dengan mudah melalui platform kami. Kami akan memprosesnya dengan cepat dan profesional.
        </p>
      </div> -->

      <!-- Form Section -->
      <div class="py-8">
        <DocumentForm :success-message="$page.props.flash?.success || ''" />
      </div>

    </div>
  </PublicLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import DocumentForm from '@/Components/DocumentForm.vue';
</script> 