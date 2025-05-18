<template>
  <AdminLayout title="Halaman Kosong" :user="$page.props.auth?.user">
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-text-primary">
        Halaman Kosong
      </h2>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-background-primary shadow-sm rounded-lg border border-border-light">
          <div class="p-6 bg-background-secondary dark:bg-background-tertiary shadow-sm">
            <!-- Debug Info -->
            <div class="mb-6 p-4 bg-yellow-100 border border-yellow-200 text-yellow-800 dark:bg-yellow-900/30 dark:border-yellow-700 dark:text-yellow-300 rounded-lg">
              <h3 class="font-bold">Debug Info:</h3>
              <div>Settings tersedia: {{ $page.props.settings ? 'Ya' : 'Tidak' }}</div>
              <div v-if="$page.props.settings">
                <div>Logo: {{ $page.props.settings.logo || 'Tidak ada' }}</div>
                <div>Site Title: {{ $page.props.settings.site_title || 'Tidak ada' }}</div>
              </div>
              <div>URL saat ini: {{ $page.url }}</div>
              <div>Route saat ini: {{ route().current() }}</div>
            </div>

            <!-- Flash Messages -->
            <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-100 border border-green-200 text-green-800 dark:bg-green-900/30 dark:border-green-700 dark:text-green-300 rounded-lg">
              {{ $page.props.flash.success }}
            </div>

            <!-- Content -->
            <div class="bg-white dark:bg-background-secondary p-6 rounded-lg border border-border-light shadow-sm">
              <h3 class="text-lg font-semibold text-text-primary mb-4 dark:text-white">
                Ini Adalah Halaman Kosong
              </h3>
              <p>Halaman ini menduplikasi struktur dari halaman Documents untuk memastikan sidebar menampilkan logo dengan benar.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

onMounted(() => {
  console.log('EmptyPage: Component mounted');
  console.log('EmptyPage Props:', usePage().props);
  console.log('Settings available:', usePage().props.settings);
  console.log('Auth User:', usePage().props.auth?.user);
  console.log('Current route:', route().current());
});
</script> 