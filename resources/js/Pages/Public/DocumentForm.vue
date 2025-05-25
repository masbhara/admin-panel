<template>
  <PublicLayout>
    <Head :title="documentForm.title" />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 min-h-screen">
      <!-- Hero Section -->
      <div class="text-center mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-primary-600 dark:text-primary-400 mb-4">
          {{ documentForm.title }}
        </h1>
        
        <p v-if="documentForm.description" class="text-lg text-gray-600 dark:text-gray-300 mb-6 max-w-3xl mx-auto">
          {{ documentForm.description }}
        </p>
        
        <!-- Countdown Timer -->
        <div v-if="documentForm.submission_deadline && documentForm.is_active" class="mb-6">
          <div class="text-lg text-gray-600 dark:text-gray-300 mb-2">
            Batas waktu pengumpulan:
          </div>
          <div class="flex justify-center gap-4">
            <div v-for="(unit, index) in timeUnits" :key="index" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
              <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                {{ countdown[unit.key] }}
              </div>
              <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ unit.label }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Form Section -->
      <div v-if="canSubmit" class="py-2">
        <DocumentForm 
          :success-message="$page.props.flash?.success || ''" 
          :document-form-id="documentForm.id"
          :template-type="documentForm.template_type" 
        />
      </div>
      
      <!-- Closed Message -->
      <div v-else class="text-center py-8">
        <div class="max-w-2xl mx-auto bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
          <svg class="mx-auto h-12 w-12 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <h3 class="mt-4 text-lg font-medium text-red-800 dark:text-red-300">
            {{ documentForm.closed_message || 'Pengumpulan dokumen sedang ditutup.' }}
          </h3>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import DocumentForm from '@/Components/DocumentForm.vue';

const props = defineProps({
  documentForm: Object,
});

// Mendeteksi mode development di script
const isDev = computed(() => {
  return process.env.NODE_ENV === 'development' || 
         window.location.hostname === 'localhost' || 
         window.location.hostname === '127.0.0.1';
});

// Memastikan bahwa document form id ada dan valid
if (!props.documentForm?.id) {
  console.error('CRITICAL ERROR: Document form ID tidak tersedia di halaman public!');
} else {
  console.info('Document form ID tersedia: ' + props.documentForm.id);
  console.info('Template type yang digunakan: ' + (props.documentForm.template_type || 'default'));
}

// Logging untuk debugging
console.log('Public DocumentForm page loaded with data:', { 
  documentFormId: props.documentForm?.id,
  title: props.documentForm?.title,
  slug: props.documentForm?.slug,
  isActive: props.documentForm?.is_active,
  templateType: props.documentForm?.template_type
});

const countdown = ref({
  days: '00',
  hours: '00',
  minutes: '00',
  seconds: '00'
});

const timeUnits = [
  { key: 'days', label: 'Hari' },
  { key: 'hours', label: 'Jam' },
  { key: 'minutes', label: 'Menit' },
  { key: 'seconds', label: 'Detik' }
];

const canSubmit = computed(() => {
  if (!props.documentForm.is_active) return false;
  if (!props.documentForm.submission_deadline) return true;
  
  const deadline = new Date(props.documentForm.submission_deadline);
  const now = new Date();
  return now < deadline;
});

const updateCountdown = () => {
  if (!props.documentForm.submission_deadline) return;

  const deadline = new Date(props.documentForm.submission_deadline);
  const now = new Date();
  const diff = deadline - now;

  if (diff <= 0) {
    countdown.value = {
      days: '00',
      hours: '00',
      minutes: '00',
      seconds: '00'
    };
    return;
  }

  const days = Math.floor(diff / (1000 * 60 * 60 * 24));
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((diff % (1000 * 60)) / 1000);

  countdown.value = {
    days: String(days).padStart(2, '0'),
    hours: String(hours).padStart(2, '0'),
    minutes: String(minutes).padStart(2, '0'),
    seconds: String(seconds).padStart(2, '0')
  };
};

onMounted(() => {
  updateCountdown();
  setInterval(updateCountdown, 1000);
});
</script> 