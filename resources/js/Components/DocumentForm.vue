<template>
  <div class="w-full max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
    <div class="bg-gradient-to-r from-primary-600 to-primary-800 px-6 py-4">
      <h3 class="text-lg font-bold text-white text-center">Semangat! Antum akan Jadi Penulis Handal</h3>
      <!-- <p class="text-white text-sm text-center">Unggah dokumen Anda, dan kami akan memprosesnya untuk Anda</p> -->
    </div>
    
    <!-- Notification Popup -->
    <transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <div v-if="showNotification" 
        class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50"
        @click.self="showNotification = false">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-md w-full mx-4 overflow-hidden transform transition-all" 
          :class="notificationType === 'success' ? 'border-l-4 border-green-500' : 'border-l-4 border-red-500'">
          <div class="p-5">
            <div class="flex items-start">
              <div v-if="notificationType === 'success'" class="flex-shrink-0">
                <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div v-else class="flex-shrink-0">
                <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-3 w-0 flex-1">
                <h3 class="text-lg font-medium" :class="notificationType === 'success' ? 'text-green-800 dark:text-green-400' : 'text-red-800 dark:text-red-400'">
                  {{ notificationTitle }}
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-700 dark:text-gray-300">
                    {{ notificationMessage }}
                  </p>
                </div>
              </div>
              <div class="ml-4 flex-shrink-0 flex">
                <button @click="showNotification = false" class="rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                  <span class="sr-only">Tutup</span>
                  <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </button>
              </div>
            </div>
            <div class="mt-4 flex">
              <button 
                @click="showNotification = false" 
                class="w-full inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white shadow-sm focus:outline-none"
                :class="notificationType === 'success' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'">
                Tutup
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
    
    <!-- Loading Overlay -->
    <div v-if="processing" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
      <div class="bg-white dark:bg-gray-800 px-8 py-6 rounded-lg shadow-lg text-center">
        <svg class="animate-spin h-12 w-12 text-primary-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">Mengirim Dokumen...</p>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Mohon tunggu sebentar sementara kami memproses dokumen Anda.</p>
      </div>
    </div>
    
    <form @submit.prevent="submitForm" class="p-6 space-y-6">
      <div v-if="successMessage" class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-md p-4 mb-6">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-500 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-green-800 dark:text-green-300">
              {{ successMessage }}
            </p>
          </div>
        </div>
      </div>

      <div class="space-y-4">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap <span class="text-red-500">*</span></label>
          <input 
            id="name" 
            v-model="form.name" 
            type="text" 
            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
            :class="{'border-red-500 dark:border-red-500': nameErrors.length > 0 || form.errors?.name}"
            placeholder="Masukkan nama lengkap Anda"
            required
            @blur="validateName"
          />
          <div v-if="nameErrors.length > 0" class="mt-1 text-sm text-red-600 dark:text-red-500">
            <p v-for="(error, index) in nameErrors" :key="index">{{ error }}</p>
          </div>
          <p v-else-if="form.errors?.name" class="mt-1 text-sm text-red-600 dark:text-red-500">{{ form.errors.name }}</p>
        </div>
        
        <div>
          <label for="whatsapp" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor WhatsApp <span class="text-red-500">*</span></label>
          <input 
            id="whatsapp" 
            v-model="form.whatsapp" 
            type="text" 
            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
            :class="{'border-red-500 dark:border-red-500': whatsappErrors.length > 0 || form.errors?.whatsapp}"
            placeholder="08xxx (gunakan nomor aktif)"
            required
            @blur="validateWhatsapp"
          />
          <div v-if="whatsappErrors.length > 0" class="mt-1 text-sm text-red-600 dark:text-red-500">
            <p v-for="(error, index) in whatsappErrors" :key="index">{{ error }}</p>
          </div>
          <p v-else-if="form.errors?.whatsapp" class="mt-1 text-sm text-red-600 dark:text-red-500">{{ form.errors.whatsapp }}</p>
        </div>
        
        <div>
          <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kota/Kabupaten <span class="text-red-500">*</span></label>
          <div class="relative" ref="cityDropdownRef">
            <div class="relative">
              <input
                type="text" 
                v-model="citySearch"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                :class="{'border-red-500 dark:border-red-500': cityErrors.length > 0 || form.errors?.city}"
                placeholder="Ketik untuk mencari kota/kabupaten..."
                @focus="showCityDropdown = true"
                @blur="validateCity"
                @keydown.down.prevent="navigateDropdown(1)"
                @keydown.up.prevent="navigateDropdown(-1)"
                @keydown.enter.prevent="selectHighlightedCity"
                :disabled="citiesLoading"
                required
              />
              
              <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                <button 
                  type="button" 
                  class="inline-flex items-center justify-center p-1 rounded-md text-gray-400 hover:text-gray-500 focus:outline-none"
                  @click="showCityDropdown = !showCityDropdown"
                >
                  <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm0 14a.75.75 0 01-.55-.24l-3.25-3.5a.75.75 0 111.1-1.02L10 15.148l2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5A.75.75 0 0110 17z" clip-rule="evenodd" />
                  </svg>
                </button>
                <div v-if="citiesLoading" class="ml-1 mr-3">
                  <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                </div>
              </div>
            </div>
            
            <div v-if="showCityDropdown" class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm">
              <div v-if="citiesLoading" class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                <div class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Memuat daftar kota/kabupaten...
                </div>
              </div>
              <div v-else-if="filteredCities.length === 0" class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                Tidak ada kota/kabupaten yang ditemukan
              </div>
              <div 
                v-for="(city, index) in filteredCities" 
                :key="city.id" 
                @click="selectCity(city)"
                @mouseover="highlightedIndex = index"
                class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-gray-100 dark:hover:bg-gray-700"
                :class="{'bg-gray-100 dark:bg-gray-700': index === highlightedIndex}"
              >
                <span class="block truncate text-gray-900 dark:text-white font-medium" :class="{'font-semibold': city.name === form.city}">
                  {{ city.name }}
                </span>
                <span v-if="city.name === form.city" class="absolute inset-y-0 right-0 flex items-center pr-4 text-primary-600 dark:text-primary-400">
                  <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </span>
              </div>
            </div>
          </div>
          <div v-if="cityErrors.length > 0" class="mt-1 text-sm text-red-600 dark:text-red-500">
            <p v-for="(error, index) in cityErrors" :key="index">{{ error }}</p>
          </div>
          <p v-else-if="form.errors?.city" class="mt-1 text-sm text-red-600 dark:text-red-500">{{ form.errors.city }}</p>
          <p v-if="form.city" class="mt-1 text-xs text-gray-500 dark:text-gray-400">Anda memilih: {{ form.city }}</p>
        </div>
        
        <div>
          <label for="document" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unggah Dokumen <span class="text-red-500">*</span></label>
          <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md"
                :class="{'border-red-500 dark:border-red-500': fileErrors.length > 0 || form.errors?.file, 'bg-primary-50 dark:bg-primary-900/20 border-primary-300 dark:border-primary-700': isDragging}"
                @dragover="handleDragOver"
                @dragleave="handleDragLeave"
                @drop="handleDrop">
            <div class="space-y-1 text-center">
              <svg v-if="!selectedFile && !previewName" class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4h-5m-6 0H9.33M28 8v7m0 0v8m0-8h18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <div v-if="selectedFile || previewName" class="flex items-center justify-center">
                <svg class="h-10 w-10 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                </svg>
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">{{ previewName }}</span>
              </div>
              <div v-else class="flex text-sm text-gray-600 dark:text-gray-400">
                <label for="file-upload" class="relative cursor-pointer rounded-md font-medium text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                  <span>Unggah file</span>
                  <input 
                    id="file-upload" 
                    name="file-upload" 
                    type="file" 
                    class="sr-only" 
                    @change="handleFileChange"
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.csv"
                  />
                </label>
                <p class="pl-1">atau seret dan letakkan</p>
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                Format: PDF, Word, maksimal 10MB
              </p>
            </div>
          </div>
          <div v-if="fileErrors.length > 0" class="mt-1 text-sm text-red-600 dark:text-red-500">
            <p v-for="(error, index) in fileErrors" :key="index">{{ error }}</p>
          </div>
          <p v-else-if="form.errors?.file" class="mt-1 text-sm text-red-600 dark:text-red-500">{{ form.errors.file }}</p>
        </div>
        
        <!-- Captcha Component -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Verifikasi (Klik refresh untuk ganti kode) <span class="text-red-500">*</span></label>
          <Captcha 
            v-model="form.captcha"
            v-model:captcha-key="form.captcha_key"
            :error="captchaErrors.length > 0 ? captchaErrors[0] : form.errors?.captcha"
            @blur="validateCaptcha"
          />
          <div v-if="captchaErrors.length > 0" class="mt-1 text-sm text-red-600 dark:text-red-500">
            <p v-for="(error, index) in captchaErrors" :key="index">{{ error }}</p>
          </div>
        </div>
      </div>
      
      <div>
        <button 
          type="submit" 
          class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
          :disabled="processing"
        >
          {{ processing ? 'Sedang Memproses...' : 'Kirim Dokumen' }}
        </button>
      </div>
      
      <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">
        <p>Kolom dengan tanda <span class="text-red-500">*</span> wajib diisi</p>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick, onBeforeUnmount } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import Captcha from '@/Components/Captcha.vue';
import { indonesiaCities } from '@/data/indonesiaCities';

const props = defineProps({
  successMessage: {
    type: String,
    default: ''
  },
  documentFormId: {
    type: Number,
    default: null
  }
});

// Logging untuk debugging
console.log('DocumentForm component initialized with props:', {
  documentFormId: props.documentFormId,
  successMessage: props.successMessage
});

// Notification states
const showNotification = ref(false);
const notificationType = ref('success');
const notificationTitle = ref('');
const notificationMessage = ref('');

// Form validation states
const nameErrors = ref([]);
const whatsappErrors = ref([]);
const cityErrors = ref([]);
const fileErrors = ref([]);
const captchaErrors = ref([]);
const touchedFields = ref({
  name: false,
  whatsapp: false,
  city: false,
  file: false,
  captcha: false
});

// Watch untuk successMessage dari props - otomatis tampilkan notif jika ada flash message
watch(() => props.successMessage, (newVal) => {
  if (newVal) {
    showNotificationPopup(
      'success', 
      'Dokumen Berhasil Terkirim!', 
      'Terima kasih! Dokumen Anda telah berhasil dikirim. Silakan periksa notifikasi WhatsApp Anda untuk informasi lebih lanjut.'
    );
  }
}, { immediate: true }); // Tambah waktunya jadi 10 detik agar user punya waktu membaca

// Show notification popup
const showNotificationPopup = (type, title, message) => {
  notificationType.value = type;
  notificationTitle.value = title;
  notificationMessage.value = message;
  showNotification.value = true;
  
  // Auto close after 8 seconds
  setTimeout(() => {
    showNotification.value = false;
  }, 10000); // Tambah waktunya jadi 10 detik agar user punya waktu membaca
};

const form = useForm({
  name: '',
  whatsapp: '',
  city: '',
  file: null,
  captcha: '',
  captcha_key: '',
  document_form_id: props.documentFormId,
});

// Log dokumen form ID saat komponen dibuat 
console.log('DocumentForm initialized with document_form_id:', props.documentFormId);

// Validate name field
const validateName = () => {
  nameErrors.value = [];
  touchedFields.value.name = true;
  
  if (!form.name) {
    nameErrors.value.push('Nama lengkap wajib diisi');
  } else if (form.name.length > 255) {
    nameErrors.value.push('Nama tidak boleh lebih dari 255 karakter');
  }
  
  return nameErrors.value.length === 0;
};

// Validate whatsapp field
const validateWhatsapp = () => {
  whatsappErrors.value = [];
  touchedFields.value.whatsapp = true;
  
  if (!form.whatsapp) {
    whatsappErrors.value.push('Nomor WhatsApp wajib diisi');
  } else if (!/^08[0-9]{8,11}$/.test(form.whatsapp)) {
    whatsappErrors.value.push('Format nomor WhatsApp tidak valid (harus diawali dengan 08)');
  } else if (form.whatsapp.length > 20) {
    whatsappErrors.value.push('Nomor WhatsApp tidak boleh lebih dari 20 digit');
  }
  
  return whatsappErrors.value.length === 0;
};

// Validate city field
const validateCity = () => {
  cityErrors.value = [];
  touchedFields.value.city = true;
  
  if (!form.city) {
    cityErrors.value.push('Kota/Kabupaten wajib diisi');
  }
  
  return cityErrors.value.length === 0;
};

// Validate file field
const validateFile = () => {
  fileErrors.value = [];
  touchedFields.value.file = true;
  
  if (!form.file) {
    fileErrors.value.push('File dokumen wajib diunggah');
    return false;
  }
  
  const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
  if (!allowedTypes.includes(form.file.type)) {
    fileErrors.value.push('Tipe file tidak valid. Hanya menerima file PDF atau Word (.doc, .docx)');
    return false;
  }
  
  if (form.file.size > 10 * 1024 * 1024) { // 10MB
    fileErrors.value.push('Ukuran file tidak boleh lebih dari 10MB');
    return false;
  }
  
  return true;
};

// Validate captcha field
const validateCaptcha = () => {
  captchaErrors.value = [];
  touchedFields.value.captcha = true;
  
  if (!form.captcha) {
    captchaErrors.value.push('Kode captcha wajib diisi');
  } else if (form.captcha.length < 4) {
    captchaErrors.value.push('Kode captcha tidak valid');
  }
  
  return captchaErrors.value.length === 0;
};

// Validate all fields
const validateForm = () => {
  const isNameValid = validateName();
  const isWhatsappValid = validateWhatsapp();
  const isCityValid = validateCity();
  const isFileValid = validateFile();
  const isCaptchaValid = validateCaptcha();
  
  return isNameValid && isWhatsappValid && isCityValid && isFileValid && isCaptchaValid;
};

// Watch for changes in form fields to perform live validation
watch(() => form.name, () => {
  if (touchedFields.value.name) validateName();
});

watch(() => form.whatsapp, () => {
  if (touchedFields.value.whatsapp) validateWhatsapp();
});

watch(() => form.city, () => {
  if (touchedFields.value.city) validateCity();
});

watch(() => form.captcha, () => {
  if (touchedFields.value.captcha) validateCaptcha();
});

const selectedFile = ref(null);
const isDragging = ref(false);
const processing = ref(false);
const cities = ref([]);
const citiesLoading = ref(true);
const citySearch = ref('');
const showCityDropdown = ref(false);
const cityDropdownRef = ref(null);
const highlightedIndex = ref(null);

const previewName = computed(() => {
  return selectedFile.value ? selectedFile.value.name : '';
});

const filteredCities = computed(() => {
  const search = citySearch.value.toLowerCase().trim();
  if (!search) return cities.value;
  
  return cities.value.filter(city => {
    // Normalisasi nama kota (hapus kata KABUPATEN/KOTA untuk pencarian)
    const normalizedCityName = city.name.replace(/^(KABUPATEN|KOTA)\s+/i, '').toLowerCase();
    const searchQuery = search.replace(/^(kabupaten|kota)\s+/i, '').toLowerCase();
    
    return normalizedCityName.includes(searchQuery) || city.name.toLowerCase().includes(search);
  });
});

// Watch perubahan pada citySearch untuk mengupdate form.city
watch(citySearch, (newValue) => {
  form.city = newValue;
  if (showCityDropdown.value && filteredCities.value.length > 0) {
    highlightedIndex.value = 0;
  }
  if (touchedFields.value.city) validateCity();
});

// Watch untuk showCityDropdown untuk mengatur highlightedIndex
watch(showCityDropdown, (isOpen) => {
  if (isOpen && filteredCities.value.length > 0) {
    highlightedIndex.value = 0;
  }
});

const fetchCities = async () => {
  try {
    citiesLoading.value = true;
    cities.value = indonesiaCities;
  } catch (error) {
    console.error('Failed to load cities:', error);
  } finally {
    citiesLoading.value = false;
  }
};

// Handler untuk menutup dropdown saat klik di luar
const handleClickOutside = (event) => {
  if (cityDropdownRef.value && !cityDropdownRef.value.contains(event.target)) {
    showCityDropdown.value = false;
  }
};

onMounted(() => {
  fetchCities();
  document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
});

const handleFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    selectedFile.value = file;
    form.file = file;
    touchedFields.value.file = true;
    validateFile();
  }
};

// Mengatur drag and drop
const handleDragOver = (event) => {
  event.preventDefault();
  isDragging.value = true;
};

const handleDragLeave = () => {
  isDragging.value = false;
};

const handleDrop = (event) => {
  event.preventDefault();
  isDragging.value = false;
  
  if (event.dataTransfer.files.length) {
    const file = event.dataTransfer.files[0];
    selectedFile.value = file;
    form.file = file;
    touchedFields.value.file = true;
    validateFile();
  }
};

const selectCity = (city) => {
  form.city = city.name;
  citySearch.value = city.name;
  showCityDropdown.value = false;
  touchedFields.value.city = true;
  validateCity();
};

const submitForm = () => {
  // Validasi form sebelum submit
  if (!validateForm()) {
    // Tampilkan notifikasi jika ada error
    showNotificationPopup('error', 'Validasi Gagal', 'Mohon periksa kembali data yang dimasukkan.');
    return;
  }
  
  // Validasi document_form_id ada
  if (!props.documentFormId) {
    console.error('Error: document_form_id tidak tersedia');
    showNotificationPopup('error', 'Gagal Mengirim', 'ID Form tidak tersedia. Silakan reload halaman dan coba lagi.');
    return;
  }
  
  processing.value = true;
  
  // Pastikan document_form_id terisi, baik dari props maupun dari form
  form.document_form_id = props.documentFormId;
  console.log('Setting document_form_id:', props.documentFormId);
  
  // Pastikan document_form_id adalah integer
  if (typeof form.document_form_id === 'string') {
    form.document_form_id = parseInt(form.document_form_id, 10);
    console.log('Mengkonversi document_form_id ke integer:', form.document_form_id);
  }

  console.log('Form data sebelum submit:', { 
    name: form.name,
    whatsapp: form.whatsapp,
    city: form.city,
    hasFile: !!form.file,
    captcha: form.captcha?.length,
    document_form_id: form.document_form_id,
    document_form_id_type: typeof form.document_form_id
  });

  try {
    // Tambahkan debug untuk memastikan route dan form data benar
    console.log('Submitting to route:', route('public.documents.store'));
    
    // Buat FormData untuk debugging
    const formData = new FormData();
    formData.append('name', form.name);
    formData.append('whatsapp', form.whatsapp);
    formData.append('city', form.city);
    formData.append('captcha', form.captcha);
    formData.append('captcha_key', form.captcha_key);
    formData.append('document_form_id', form.document_form_id);
    if (form.file) {
      formData.append('file', form.file);
    }
    
    // Log FormData untuk debugging
    console.log('FormData entries:');
    for (let [key, value] of formData.entries()) {
      console.log(`${key}: ${value instanceof File ? value.name : value}`);
    }

    form.post(route('public.documents.store'), {
      preserveScroll: true,
      onSuccess: (response) => {
        console.log('Form submission successful, response:', response);
        processing.value = false;
        showNotificationPopup(
          'success', 
          'Dokumen Berhasil Terkirim!', 
          'Terima kasih! Dokumen Anda telah berhasil dikirim. Silakan periksa notifikasi WhatsApp Anda untuk informasi lebih lanjut.'
        );
        form.reset();
        selectedFile.value = null;
        citySearch.value = '';
        // Reset touched fields
        Object.keys(touchedFields.value).forEach(field => {
          touchedFields.value[field] = false;
        });
        nameErrors.value = [];
        whatsappErrors.value = [];
        cityErrors.value = [];
        fileErrors.value = [];
        captchaErrors.value = [];
      },
      onError: (errors) => {
        processing.value = false;
        let errorMessage = 'Terjadi kesalahan saat mengunggah dokumen.';
        
        console.error('Form submission error:', errors);
        
        // Cek error spesifik
        if (errors.captcha) {
          errorMessage = errors.captcha;
        } else if (errors.file) {
          errorMessage = errors.file;
        } else if (errors.document_form_id) {
          errorMessage = errors.document_form_id;
        } else if (errors.error) {
          errorMessage = errors.error;
        }
        
        showNotificationPopup('error', 'Gagal Mengirim', errorMessage);
      },
      onFinish: () => {
        processing.value = false;
      },
    });
  } catch (error) {
    console.error('Error saat mengirim form:', error);
    processing.value = false;
    showNotificationPopup('error', 'Kesalahan Sistem', 'Terjadi kesalahan saat mengirim data. Silakan coba lagi nanti.');
  }
};

const navigateDropdown = (direction) => {
  if (highlightedIndex.value !== null && highlightedIndex.value + direction >= 0 && highlightedIndex.value + direction < filteredCities.value.length) {
    highlightedIndex.value += direction;
  }
};

const selectHighlightedCity = () => {
  if (highlightedIndex.value !== null && highlightedIndex.value >= 0 && highlightedIndex.value < filteredCities.value.length) {
    selectCity(filteredCities.value[highlightedIndex.value]);
  }
};
</script> 