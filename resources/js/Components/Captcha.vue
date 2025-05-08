<template>
  <div>
    <div class="flex items-center space-x-2">
      <div class="relative">
        <img 
          v-if="captchaImage" 
          :src="captchaImage" 
          alt="CAPTCHA" 
          class="h-12 rounded border border-gray-300 dark:border-gray-600"
        />
        <div v-else class="h-12 w-36 bg-gray-200 dark:bg-gray-700 rounded animate-pulse flex items-center justify-center">
          <span class="text-xs text-gray-500 dark:text-gray-400">Memuat captcha...</span>
        </div>
      </div>
      <button 
        type="button" 
        @click="refreshCaptcha" 
        class="inline-flex items-center justify-center p-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-500 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
        :disabled="isRefreshing"
      >
        <svg 
          xmlns="http://www.w3.org/2000/svg" 
          class="h-5 w-5" 
          :class="{ 'animate-spin': isRefreshing }"
          fill="none" 
          viewBox="0 0 24 24" 
          stroke="currentColor"
        >
          <path 
            stroke-linecap="round" 
            stroke-linejoin="round" 
            stroke-width="2" 
            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" 
          />
        </svg>
      </button>
    </div>
    
    <div class="mt-2">
      <input 
        v-model="localValue"
        type="text" 
        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
        :class="{'border-red-500 dark:border-red-500': error}"
        placeholder="Masukkan kode captcha"
        required
      />
      <p v-if="error" class="mt-1 text-sm text-red-600 dark:text-red-500">{{ error }}</p>
      <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
        Masukkan kode yang ditampilkan pada gambar di atas
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  error: {
    type: String,
    default: ''
  },
  captchaKey: {
    type: String,
    default: ''
  }
});

const emit = defineEmits(['update:modelValue', 'update:captchaKey']);

const captchaImage = ref(null);
const localValue = ref(props.modelValue);
const isRefreshing = ref(false);

watch(localValue, (value) => {
  emit('update:modelValue', value);
});

const loadCaptcha = async () => {
  try {
    const response = await axios.get(route('captcha'));
    captchaImage.value = response.data.image;
    emit('update:captchaKey', response.data.key);
  } catch (error) {
    console.error('Tidak dapat memuat captcha:', error);
  }
};

const refreshCaptcha = async () => {
  try {
    isRefreshing.value = true;
    const response = await axios.get(route('refresh.captcha'));
    captchaImage.value = response.data.image;
    emit('update:captchaKey', response.data.key);
    localValue.value = '';
  } catch (error) {
    console.error('Tidak dapat me-refresh captcha:', error);
  } finally {
    isRefreshing.value = false;
  }
};

onMounted(() => {
  loadCaptcha();
});
</script> 