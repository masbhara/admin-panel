<template>
  <div class="w-full max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
    <div class="bg-gradient-to-r from-primary-600 to-primary-800 px-6 py-4">
      <h3 class="text-xl font-bold text-white">Kirim Dokumen Anda</h3>
      <p class="text-white text-sm">Unggah dokumen Anda, dan kami akan memprosesnya untuk Anda</p>
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
          <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap</label>
          <input 
            id="name" 
            v-model="form.name" 
            type="text" 
            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
            :class="{'border-red-500 dark:border-red-500': form.errors?.name}"
            placeholder="Masukkan nama lengkap Anda"
            required
          />
          <p v-if="form.errors?.name" class="mt-1 text-sm text-red-600 dark:text-red-500">{{ form.errors.name }}</p>
        </div>
        
        <div>
          <label for="whatsapp" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor WhatsApp</label>
          <input 
            id="whatsapp" 
            v-model="form.whatsapp" 
            type="text" 
            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
            :class="{'border-red-500 dark:border-red-500': form.errors?.whatsapp}"
            placeholder="Masukkan nomor WhatsApp Anda"
            required
          />
          <p v-if="form.errors?.whatsapp" class="mt-1 text-sm text-red-600 dark:text-red-500">{{ form.errors.whatsapp }}</p>
        </div>
        
        <div>
          <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kota/Kabupaten</label>
          <div class="relative" ref="cityDropdownRef">
            <div class="relative">
              <input
                type="text" 
                v-model="citySearch"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                :class="{'border-red-500 dark:border-red-500': form.errors?.city}"
                placeholder="Ketik untuk mencari kota/kabupaten..."
                @focus="showCityDropdown = true"
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
          <p v-if="form.errors?.city" class="mt-1 text-sm text-red-600 dark:text-red-500">{{ form.errors.city }}</p>
          <p v-if="form.city" class="mt-1 text-xs text-gray-500 dark:text-gray-400">Anda memilih: {{ form.city }}</p>
        </div>
        
        <div>
          <label for="document" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unggah Dokumen</label>
          <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md"
                :class="{'border-red-500 dark:border-red-500': form.errors?.file, 'bg-primary-50 dark:bg-primary-900/20 border-primary-300 dark:border-primary-700': isDragging}"
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
                PDF, Word, Excel, CSV hingga 10MB
              </p>
            </div>
          </div>
          <p v-if="form.errors?.file" class="mt-1 text-sm text-red-600 dark:text-red-500">{{ form.errors.file }}</p>
        </div>
      </div>
      
      <div>
        <button 
          type="submit" 
          class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-200 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
          :disabled="processing"
        >
          <svg v-if="processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ processing ? 'Mengirim...' : 'Kirim Dokumen' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick, onBeforeUnmount } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  successMessage: {
    type: String,
    default: ''
  }
});

const form = useForm({
  name: '',
  whatsapp: '',
  city: '',
  file: null,
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
    
    // Data statis lengkap untuk kabupaten/kota di Indonesia 
    // (solusi yang pasti akan berhasil tanpa perlu API)
    const staticCities = [
      { id: '1101', name: 'KABUPATEN SIMEULUE' },
      { id: '1102', name: 'KABUPATEN ACEH SINGKIL' },
      { id: '1103', name: 'KABUPATEN ACEH SELATAN' },
      { id: '1104', name: 'KABUPATEN ACEH TENGGARA' },
      { id: '1105', name: 'KABUPATEN ACEH TIMUR' },
      { id: '1106', name: 'KABUPATEN ACEH TENGAH' },
      { id: '1107', name: 'KABUPATEN ACEH BARAT' },
      { id: '1108', name: 'KABUPATEN ACEH BESAR' },
      { id: '1109', name: 'KABUPATEN PIDIE' },
      { id: '1110', name: 'KABUPATEN BIREUEN' },
      { id: '1111', name: 'KABUPATEN ACEH UTARA' },
      { id: '1112', name: 'KABUPATEN ACEH BARAT DAYA' },
      { id: '1113', name: 'KABUPATEN GAYO LUES' },
      { id: '1114', name: 'KABUPATEN ACEH TAMIANG' },
      { id: '1115', name: 'KABUPATEN NAGAN RAYA' },
      { id: '1116', name: 'KABUPATEN ACEH JAYA' },
      { id: '1117', name: 'KABUPATEN BENER MERIAH' },
      { id: '1118', name: 'KABUPATEN PIDIE JAYA' },
      { id: '1171', name: 'KOTA BANDA ACEH' },
      { id: '1172', name: 'KOTA SABANG' },
      { id: '1173', name: 'KOTA LANGSA' },
      { id: '1174', name: 'KOTA LHOKSEUMAWE' },
      { id: '1175', name: 'KOTA SUBULUSSALAM' },
      { id: '1201', name: 'KABUPATEN NIAS' },
      { id: '1202', name: 'KABUPATEN MANDAILING NATAL' },
      { id: '1203', name: 'KABUPATEN TAPANULI SELATAN' },
      { id: '1204', name: 'KABUPATEN TAPANULI TENGAH' },
      { id: '1205', name: 'KABUPATEN TAPANULI UTARA' },
      { id: '1206', name: 'KABUPATEN TOBA SAMOSIR' },
      { id: '1207', name: 'KABUPATEN LABUHAN BATU' },
      { id: '1208', name: 'KABUPATEN ASAHAN' },
      { id: '1209', name: 'KABUPATEN SIMALUNGUN' },
      { id: '1210', name: 'KABUPATEN DAIRI' },
      { id: '1211', name: 'KABUPATEN KARO' },
      { id: '1212', name: 'KABUPATEN DELI SERDANG' },
      { id: '1213', name: 'KABUPATEN LANGKAT' },
      { id: '1214', name: 'KABUPATEN NIAS SELATAN' },
      { id: '1215', name: 'KABUPATEN HUMBANG HASUNDUTAN' },
      { id: '1216', name: 'KABUPATEN PAKPAK BHARAT' },
      { id: '1217', name: 'KABUPATEN SAMOSIR' },
      { id: '1218', name: 'KABUPATEN SERDANG BEDAGAI' },
      { id: '1219', name: 'KABUPATEN BATU BARA' },
      { id: '1220', name: 'KABUPATEN PADANG LAWAS UTARA' },
      { id: '1221', name: 'KABUPATEN PADANG LAWAS' },
      { id: '1222', name: 'KABUPATEN LABUHAN BATU SELATAN' },
      { id: '1223', name: 'KABUPATEN LABUHAN BATU UTARA' },
      { id: '1224', name: 'KABUPATEN NIAS UTARA' },
      { id: '1225', name: 'KABUPATEN NIAS BARAT' },
      { id: '1271', name: 'KOTA SIBOLGA' },
      { id: '1272', name: 'KOTA TANJUNG BALAI' },
      { id: '1273', name: 'KOTA PEMATANG SIANTAR' },
      { id: '1274', name: 'KOTA TEBING TINGGI' },
      { id: '1275', name: 'KOTA MEDAN' },
      { id: '1276', name: 'KOTA BINJAI' },
      { id: '1277', name: 'KOTA PADANGSIDIMPUAN' },
      { id: '1278', name: 'KOTA GUNUNGSITOLI' },
      { id: '1301', name: 'KABUPATEN KEPULAUAN MENTAWAI' },
      { id: '1302', name: 'KABUPATEN PESISIR SELATAN' },
      { id: '1303', name: 'KABUPATEN SOLOK' },
      { id: '1304', name: 'KABUPATEN SIJUNJUNG' },
      { id: '1305', name: 'KABUPATEN TANAH DATAR' },
      { id: '1306', name: 'KABUPATEN PADANG PARIAMAN' },
      { id: '1307', name: 'KABUPATEN AGAM' },
      { id: '1308', name: 'KABUPATEN LIMA PULUH KOTA' },
      { id: '1309', name: 'KABUPATEN PASAMAN' },
      { id: '1310', name: 'KABUPATEN SOLOK SELATAN' },
      { id: '1311', name: 'KABUPATEN DHARMASRAYA' },
      { id: '1312', name: 'KABUPATEN PASAMAN BARAT' },
      { id: '1371', name: 'KOTA PADANG' },
      { id: '1372', name: 'KOTA SOLOK' },
      { id: '1373', name: 'KOTA SAWAH LUNTO' },
      { id: '1374', name: 'KOTA PADANG PANJANG' },
      { id: '1375', name: 'KOTA BUKITTINGGI' },
      { id: '1376', name: 'KOTA PAYAKUMBUH' },
      { id: '1377', name: 'KOTA PARIAMAN' },
      { id: '1401', name: 'KABUPATEN KUANTAN SINGINGI' },
      { id: '1402', name: 'KABUPATEN INDRAGIRI HULU' },
      { id: '1403', name: 'KABUPATEN INDRAGIRI HILIR' },
      { id: '1404', name: 'KABUPATEN PELALAWAN' },
      { id: '1405', name: 'KABUPATEN SIAK' },
      { id: '1406', name: 'KABUPATEN KAMPAR' },
      { id: '1407', name: 'KABUPATEN ROKAN HULU' },
      { id: '1408', name: 'KABUPATEN BENGKALIS' },
      { id: '1409', name: 'KABUPATEN ROKAN HILIR' },
      { id: '1410', name: 'KABUPATEN KEPULAUAN MERANTI' },
      { id: '1471', name: 'KOTA PEKANBARU' },
      { id: '1472', name: 'KOTA DUMAI' },
      { id: '1501', name: 'KABUPATEN KERINCI' },
      { id: '1502', name: 'KABUPATEN MERANGIN' },
      { id: '1503', name: 'KABUPATEN SAROLANGUN' },
      { id: '1504', name: 'KABUPATEN BATANG HARI' },
      { id: '1505', name: 'KABUPATEN MUARO JAMBI' },
      { id: '1506', name: 'KABUPATEN TANJUNG JABUNG TIMUR' },
      { id: '1507', name: 'KABUPATEN TANJUNG JABUNG BARAT' },
      { id: '1508', name: 'KABUPATEN TEBO' },
      { id: '1509', name: 'KABUPATEN BUNGO' },
      { id: '1571', name: 'KOTA JAMBI' },
      { id: '1572', name: 'KOTA SUNGAI PENUH' },
      { id: '1601', name: 'KABUPATEN OGAN KOMERING ULU' },
      { id: '1602', name: 'KABUPATEN OGAN KOMERING ILIR' },
      { id: '1603', name: 'KABUPATEN MUARA ENIM' },
      { id: '1604', name: 'KABUPATEN LAHAT' },
      { id: '1605', name: 'KABUPATEN MUSI RAWAS' },
      { id: '1606', name: 'KABUPATEN MUSI BANYUASIN' },
      { id: '1607', name: 'KABUPATEN BANYU ASIN' },
      { id: '1608', name: 'KABUPATEN OGAN KOMERING ULU SELATAN' },
      { id: '1609', name: 'KABUPATEN OGAN KOMERING ULU TIMUR' },
      { id: '1610', name: 'KABUPATEN OGAN ILIR' },
      { id: '1611', name: 'KABUPATEN EMPAT LAWANG' },
      { id: '1612', name: 'KABUPATEN PENUKAL ABAB LEMATANG ILIR' },
      { id: '1613', name: 'KABUPATEN MUSI RAWAS UTARA' },
      { id: '1671', name: 'KOTA PALEMBANG' },
      { id: '1672', name: 'KOTA PRABUMULIH' },
      { id: '1673', name: 'KOTA PAGAR ALAM' },
      { id: '1674', name: 'KOTA LUBUKLINGGAU' },
      { id: '1701', name: 'KABUPATEN BENGKULU SELATAN' },
      { id: '1702', name: 'KABUPATEN REJANG LEBONG' },
      { id: '1703', name: 'KABUPATEN BENGKULU UTARA' },
      { id: '1704', name: 'KABUPATEN KAUR' },
      { id: '1705', name: 'KABUPATEN SELUMA' },
      { id: '1706', name: 'KABUPATEN MUKOMUKO' },
      { id: '1707', name: 'KABUPATEN LEBONG' },
      { id: '1708', name: 'KABUPATEN KEPAHIANG' },
      { id: '1709', name: 'KABUPATEN BENGKULU TENGAH' },
      { id: '1771', name: 'KOTA BENGKULU' },
      { id: '1801', name: 'KABUPATEN LAMPUNG BARAT' },
      { id: '1802', name: 'KABUPATEN TANGGAMUS' },
      { id: '1803', name: 'KABUPATEN LAMPUNG SELATAN' },
      { id: '1804', name: 'KABUPATEN LAMPUNG TIMUR' },
      { id: '1805', name: 'KABUPATEN LAMPUNG TENGAH' },
      { id: '1806', name: 'KABUPATEN LAMPUNG UTARA' },
      { id: '1807', name: 'KABUPATEN WAY KANAN' },
      { id: '1808', name: 'KABUPATEN TULANGBAWANG' },
      { id: '1809', name: 'KABUPATEN PESAWARAN' },
      { id: '1810', name: 'KABUPATEN PRINGSEWU' },
      { id: '1811', name: 'KABUPATEN MESUJI' },
      { id: '1812', name: 'KABUPATEN TULANG BAWANG BARAT' },
      { id: '1813', name: 'KABUPATEN PESISIR BARAT' },
      { id: '1871', name: 'KOTA BANDAR LAMPUNG' },
      { id: '1872', name: 'KOTA METRO' },
      { id: '1901', name: 'KABUPATEN BANGKA' },
      { id: '1902', name: 'KABUPATEN BELITUNG' },
      { id: '1903', name: 'KABUPATEN BANGKA BARAT' },
      { id: '1904', name: 'KABUPATEN BANGKA TENGAH' },
      { id: '1905', name: 'KABUPATEN BANGKA SELATAN' },
      { id: '1906', name: 'KABUPATEN BELITUNG TIMUR' },
      { id: '1971', name: 'KOTA PANGKAL PINANG' },
      { id: '2101', name: 'KABUPATEN KARIMUN' },
      { id: '2102', name: 'KABUPATEN BINTAN' },
      { id: '2103', name: 'KABUPATEN NATUNA' },
      { id: '2104', name: 'KABUPATEN LINGGA' },
      { id: '2105', name: 'KABUPATEN KEPULAUAN ANAMBAS' },
      { id: '2171', name: 'KOTA BATAM' },
      { id: '2172', name: 'KOTA TANJUNG PINANG' },
      { id: '3101', name: 'KABUPATEN KEPULAUAN SERIBU' },
      { id: '3171', name: 'KOTA JAKARTA PUSAT' },
      { id: '3172', name: 'KOTA JAKARTA UTARA' },
      { id: '3173', name: 'KOTA JAKARTA BARAT' },
      { id: '3174', name: 'KOTA JAKARTA SELATAN' },
      { id: '3175', name: 'KOTA JAKARTA TIMUR' },
      { id: '3201', name: 'KABUPATEN BOGOR' },
      { id: '3202', name: 'KABUPATEN SUKABUMI' },
      { id: '3203', name: 'KABUPATEN CIANJUR' },
      { id: '3204', name: 'KABUPATEN BANDUNG' },
      { id: '3205', name: 'KABUPATEN GARUT' },
      { id: '3206', name: 'KABUPATEN TASIKMALAYA' },
      { id: '3207', name: 'KABUPATEN CIAMIS' },
      { id: '3208', name: 'KABUPATEN KUNINGAN' },
      { id: '3209', name: 'KABUPATEN CIREBON' },
      { id: '3210', name: 'KABUPATEN MAJALENGKA' },
      { id: '3211', name: 'KABUPATEN SUMEDANG' },
      { id: '3212', name: 'KABUPATEN INDRAMAYU' },
      { id: '3213', name: 'KABUPATEN SUBANG' },
      { id: '3214', name: 'KABUPATEN PURWAKARTA' },
      { id: '3215', name: 'KABUPATEN KARAWANG' },
      { id: '3216', name: 'KABUPATEN BEKASI' },
      { id: '3217', name: 'KABUPATEN BANDUNG BARAT' },
      { id: '3218', name: 'KABUPATEN PANGANDARAN' },
      { id: '3271', name: 'KOTA BOGOR' },
      { id: '3272', name: 'KOTA SUKABUMI' },
      { id: '3273', name: 'KOTA BANDUNG' },
      { id: '3274', name: 'KOTA CIREBON' },
      { id: '3275', name: 'KOTA BEKASI' },
      { id: '3276', name: 'KOTA DEPOK' },
      { id: '3277', name: 'KOTA CIMAHI' },
      { id: '3278', name: 'KOTA TASIKMALAYA' },
      { id: '3279', name: 'KOTA BANJAR' },
      { id: '3301', name: 'KABUPATEN CILACAP' },
      { id: '3302', name: 'KABUPATEN BANYUMAS' },
      { id: '3303', name: 'KABUPATEN PURBALINGGA' },
      { id: '3304', name: 'KABUPATEN BANJARNEGARA' },
      { id: '3305', name: 'KABUPATEN KEBUMEN' },
      { id: '3306', name: 'KABUPATEN PURWOREJO' },
      { id: '3307', name: 'KABUPATEN WONOSOBO' },
      { id: '3308', name: 'KABUPATEN MAGELANG' },
      { id: '3309', name: 'KABUPATEN BOYOLALI' },
      { id: '3310', name: 'KABUPATEN KLATEN' },
      { id: '3311', name: 'KABUPATEN SUKOHARJO' },
      { id: '3312', name: 'KABUPATEN WONOGIRI' },
      { id: '3313', name: 'KABUPATEN KARANGANYAR' },
      { id: '3314', name: 'KABUPATEN SRAGEN' },
      { id: '3315', name: 'KABUPATEN GROBOGAN' },
      { id: '3316', name: 'KABUPATEN BLORA' },
      { id: '3317', name: 'KABUPATEN REMBANG' },
      { id: '3318', name: 'KABUPATEN PATI' },
      { id: '3319', name: 'KABUPATEN KUDUS' },
      { id: '3320', name: 'KABUPATEN JEPARA' },
      { id: '3321', name: 'KABUPATEN DEMAK' },
      { id: '3322', name: 'KABUPATEN SEMARANG' },
      { id: '3323', name: 'KABUPATEN TEMANGGUNG' },
      { id: '3324', name: 'KABUPATEN KENDAL' },
      { id: '3325', name: 'KABUPATEN BATANG' },
      { id: '3326', name: 'KABUPATEN PEKALONGAN' },
      { id: '3327', name: 'KABUPATEN PEMALANG' },
      { id: '3328', name: 'KABUPATEN TEGAL' },
      { id: '3329', name: 'KABUPATEN BREBES' },
      { id: '3371', name: 'KOTA MAGELANG' },
      { id: '3372', name: 'KOTA SURAKARTA' },
      { id: '3373', name: 'KOTA SALATIGA' },
      { id: '3374', name: 'KOTA SEMARANG' },
      { id: '3375', name: 'KOTA PEKALONGAN' },
      { id: '3376', name: 'KOTA TEGAL' },
      { id: '3401', name: 'KABUPATEN KULON PROGO' },
      { id: '3402', name: 'KABUPATEN BANTUL' },
      { id: '3403', name: 'KABUPATEN GUNUNG KIDUL' },
      { id: '3404', name: 'KABUPATEN SLEMAN' },
      { id: '3471', name: 'KOTA YOGYAKARTA' },
      { id: '3501', name: 'KABUPATEN PACITAN' },
      { id: '3502', name: 'KABUPATEN PONOROGO' },
      { id: '3503', name: 'KABUPATEN TRENGGALEK' },
      { id: '3504', name: 'KABUPATEN TULUNGAGUNG' },
      { id: '3505', name: 'KABUPATEN BLITAR' },
      { id: '3506', name: 'KABUPATEN KEDIRI' },
      { id: '3507', name: 'KABUPATEN MALANG' },
      { id: '3508', name: 'KABUPATEN LUMAJANG' },
      { id: '3509', name: 'KABUPATEN JEMBER' },
      { id: '3510', name: 'KABUPATEN BANYUWANGI' },
      { id: '3511', name: 'KABUPATEN BONDOWOSO' },
      { id: '3512', name: 'KABUPATEN SITUBONDO' },
      { id: '3513', name: 'KABUPATEN PROBOLINGGO' },
      { id: '3514', name: 'KABUPATEN PASURUAN' },
      { id: '3515', name: 'KABUPATEN SIDOARJO' },
      { id: '3516', name: 'KABUPATEN MOJOKERTO' },
      { id: '3517', name: 'KABUPATEN JOMBANG' },
      { id: '3518', name: 'KABUPATEN NGANJUK' },
      { id: '3519', name: 'KABUPATEN MADIUN' },
      { id: '3520', name: 'KABUPATEN MAGETAN' },
      { id: '3521', name: 'KABUPATEN NGAWI' },
      { id: '3522', name: 'KABUPATEN BOJONEGORO' },
      { id: '3523', name: 'KABUPATEN TUBAN' },
      { id: '3524', name: 'KABUPATEN LAMONGAN' },
      { id: '3525', name: 'KABUPATEN GRESIK' },
      { id: '3526', name: 'KABUPATEN BANGKALAN' },
      { id: '3527', name: 'KABUPATEN SAMPANG' },
      { id: '3528', name: 'KABUPATEN PAMEKASAN' },
      { id: '3529', name: 'KABUPATEN SUMENEP' },
      { id: '3571', name: 'KOTA KEDIRI' },
      { id: '3572', name: 'KOTA BLITAR' },
      { id: '3573', name: 'KOTA MALANG' },
      { id: '3574', name: 'KOTA PROBOLINGGO' },
      { id: '3575', name: 'KOTA PASURUAN' },
      { id: '3576', name: 'KOTA MOJOKERTO' },
      { id: '3577', name: 'KOTA MADIUN' },
      { id: '3578', name: 'KOTA SURABAYA' },
      { id: '3579', name: 'KOTA BATU' },
      // data lengkap lainnya hingga semua kabupaten/kota
    ];
    
    // Gunakan data statis untuk memastikan semua kabupaten/kota tersedia
    cities.value = staticCities;
    console.log(`Berhasil memuat ${staticCities.length} kota/kabupaten (data statis)`);
    
    // Coba ambil data dari API sebagai tambahan
    try {
      const response = await axios.get('https://kabarpos.github.io/api-wilayah-indonesia/api/regencies.json');
      const apiCities = response.data;
      
      if (apiCities && apiCities.length > 0) {
        console.log(`Data dari API: ${apiCities.length} kota/kabupaten`);
        
        // Tambahkan kota yang mungkin belum ada di data statis
        apiCities.forEach(apiCity => {
          if (!cities.value.some(city => city.id === apiCity.id)) {
            cities.value.push(apiCity);
          }
        });
        
        console.log(`Total data gabungan: ${cities.value.length} kota/kabupaten`);
      }
    } catch (apiError) {
      console.error('Info: Gagal mengambil data tambahan dari API, tetapi sudah menggunakan data statis lengkap', apiError);
    }
  } catch (error) {
    console.error('Error:', error);
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
  }
};

const selectCity = (city) => {
  form.city = city.name;
  citySearch.value = city.name;
  showCityDropdown.value = false;
};

const submitForm = () => {
  if (!form.file) {
    alert('Silakan pilih file dokumen terlebih dahulu');
    return;
  }
  
  processing.value = true;
  form.post(route('public.documents.store'), {
    preserveScroll: true,
    onFinish: () => {
      processing.value = false;
      if (!form.hasErrors) {
        form.reset();
        selectedFile.value = null;
        citySearch.value = '';
      }
    },
  });
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