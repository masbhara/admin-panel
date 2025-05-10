<template>
  <AdminLayout :title="`Detail Dokumen - ${document.file_name || document.name}`" :user="$page.props.auth?.user">
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-text-primary">
        Detail Dokumen: {{ document.file_name || document.name }}
      </h2>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Notifikasi flash message -->
        <div v-if="$page.props.flash && $page.props.flash.success" class="mb-6">
          <div class="p-4 text-sm text-green-700 bg-green-100 dark:bg-green-900/30 dark:text-green-200 rounded-lg">
            {{ $page.props.flash.success }}
          </div>
        </div>
        
        <div v-if="$page.props.flash && $page.props.flash.error" class="mb-6">
          <div class="p-4 text-sm text-red-700 bg-red-100 dark:bg-red-900/30 dark:text-red-200 rounded-lg">
            {{ $page.props.flash.error }}
          </div>
        </div>

        <div class="overflow-hidden bg-background-primary shadow-sm rounded-lg border border-border-light">
          <div class="p-6 bg-background-secondary dark:bg-background-tertiary shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div>
                <h3 class="text-lg font-medium leading-6 text-text-primary">
                  Informasi Dokumen
                </h3>
                <p class="max-w-2xl mt-1 text-sm text-text-secondary">
                  Detail dan informasi dokumen.
                </p>
              </div>
              <div class="flex flex-wrap gap-2">
                <button 
                  @click="openPreviewModal" 
                  class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                  Preview
                </button>
                
                <a 
                  :href="route('documents.download', document.id)" 
                  target="_blank"
                  class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                  </svg>
                  Download
                </a>
                
                <Link 
                  :href="route('admin.documents.edit', document.id)" 
                  class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-amber-600 border border-transparent rounded-lg shadow-sm hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-colors"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                  Edit
                </Link>
                
                <button 
                  @click="confirmDelete" 
                  class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                  Hapus
                </button>
                
                <Link 
                  :href="route('admin.documents.index')" 
                  class="inline-flex items-center px-3 py-2 text-sm font-medium text-text-primary bg-background-tertiary border border-border-light rounded-lg shadow-sm hover:bg-background-secondary focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                  </svg>
                  Kembali
                </Link>
              </div>
            </div>
          </div>
          <div class="border-t border-border-light">
            <dl>
              <div class="px-4 py-5 bg-background-secondary sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-text-secondary">
                  Nama File Asli
                </dt>
                <dd class="mt-1 text-sm text-text-primary font-medium sm:col-span-2 sm:mt-0">
                  {{ document.file_name || document.name || 'Tidak Tersedia' }}
                </dd>
              </div>
              <div class="px-4 py-5 bg-background-primary sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-text-secondary">
                  Pengirim
                </dt>
                <dd class="mt-1 text-sm text-text-primary sm:col-span-2 sm:mt-0">
                  {{ getPengirimName(document) }}
                </dd>
              </div>
              <!-- Tambahkan informasi WhatsApp jika ada -->
              <div v-if="document?.metadata?.whatsapp" class="px-4 py-5 bg-background-secondary sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-text-secondary">
                  WhatsApp Pengirim
                </dt>
                <dd class="mt-1 text-sm text-text-primary sm:col-span-2 sm:mt-0">
                  {{ document.metadata.whatsapp }}
                </dd>
              </div>
              <!-- Tambahkan informasi Kota jika ada -->
              <div v-if="document?.metadata?.city" class="px-4 py-5 bg-background-primary sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-text-secondary">
                  Asal Kota
                </dt>
                <dd class="mt-1 text-sm text-text-primary sm:col-span-2 sm:mt-0">
                  {{ document.metadata.city }}
                </dd>
              </div>
              <div class="px-4 py-5 bg-background-secondary sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-text-secondary">
                  Deskripsi
                </dt>
                <dd class="mt-1 text-sm text-text-secondary sm:col-span-2 sm:mt-0">
                  {{ document.description || 'Tidak ada deskripsi' }}
                </dd>
              </div>
              <div class="px-4 py-5 bg-background-primary sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-text-secondary">
                  Tipe File
                </dt>
                <dd class="mt-1 text-sm text-text-primary sm:col-span-2 sm:mt-0">
                  {{ document.file_type || getFileTypeFromPath(document.file_path) }}
                </dd>
              </div>
              <div class="px-4 py-5 bg-background-secondary sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-text-secondary">
                  Ukuran File
                </dt>
                <dd class="mt-1 text-sm text-text-primary sm:col-span-2 sm:mt-0">
                  {{ formatFileSize(document.file_size) }}
                </dd>
              </div>
              <div class="px-4 py-5 bg-background-primary sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-text-secondary">
                  Status
                </dt>
                <dd class="mt-1 text-sm sm:col-span-2 sm:mt-0">
                  <span 
                    class="inline-flex px-2.5 py-0.5 text-xs font-semibold rounded-full"
                    :class="{
                      'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': document.status === 'pending',
                      'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': document.status === 'approved',
                      'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': document.status === 'rejected',
                    }"
                  >
                    {{ statusLabel(document.status) }}
                  </span>
                </dd>
              </div>
              <div class="px-4 py-5 bg-background-secondary sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-text-secondary">
                  Tanggal Upload
                </dt>
                <dd class="mt-1 text-sm text-text-primary sm:col-span-2 sm:mt-0">
                  {{ formatDate(document.uploaded_at) }}
                </dd>
              </div>
              <div class="px-4 py-5 bg-background-primary sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-text-secondary">
                  Diproses Oleh
                </dt>
                <dd class="mt-1 text-sm text-text-secondary sm:col-span-2 sm:mt-0">
                  {{ document?.user?.name || 'Sistem' }}
                </dd>
              </div>
              
              <!-- Metadata Tambahan -->
              <div v-if="hasAdditionalMetadata" class="px-4 py-5 bg-background-secondary sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-text-secondary">
                  Metadata Lainnya
                </dt>
                <dd class="mt-1 text-sm text-text-secondary sm:col-span-2 sm:mt-0">
                  <details>
                    <summary class="cursor-pointer text-primary-600 dark:text-primary-400 hover:underline">
                      Lihat semua metadata ({{ Object.keys(filteredMetadata).length }} item)
                    </summary>
                    <div class="mt-3 border border-border-light rounded-md overflow-hidden">
                      <div class="grid grid-cols-1 divide-y divide-border-light">
                        <div v-for="(value, key) in filteredMetadata" :key="key" class="px-4 py-3 grid grid-cols-3 gap-4">
                          <div class="text-sm font-medium text-text-secondary">{{ formatMetadataKey(key) }}</div>
                          <div class="text-sm text-text-primary col-span-2">
                            {{ formatMetadataValue(value) }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </details>
                </dd>
              </div>
            </dl>
          </div>
        </div>

        <!-- Aktivitas Log -->
        <div class="mt-6 overflow-hidden bg-background-primary shadow-sm rounded-lg border border-border-light">
          <div class="p-6 bg-background-secondary dark:bg-background-tertiary shadow-sm flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
              <h3 class="text-lg font-medium leading-6 text-text-primary">
                Aktivitas Dokumen
              </h3>
              <p class="max-w-2xl mt-1 text-sm text-text-secondary">
                Riwayat aktivitas untuk dokumen ini. Total: {{ props.activities.length }} entri
              </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
              <select 
                v-model="activityType" 
                class="py-1.5 px-3 text-sm border-border-light rounded-lg bg-background-tertiary focus:ring-primary-500 focus:border-primary-500"
              >
                <option value="all">Semua Aktivitas</option>
                <option value="downloaded document">Download</option>
                <option value="updated">Update/Edit</option>
                <option value="created">Pembuatan</option>
                <option value="deleted document">Penghapusan</option>
              </select>
              <select 
                v-model="activityLimit" 
                class="py-1.5 px-3 text-sm border-border-light rounded-lg bg-background-tertiary focus:ring-primary-500 focus:border-primary-500"
              >
                <option :value="5">5 per halaman</option>
                <option :value="10">10 per halaman</option>
                <option :value="20">20 per halaman</option>
                <option :value="50">50 per halaman</option>
                <option :value="0">Tampilkan semua</option>
              </select>
              <button 
                @click="expandActivities = !expandActivities" 
                class="p-1.5 rounded-md text-text-secondary hover:bg-background-tertiary transition-colors"
                :title="expandActivities ? 'Tutup aktivitas' : 'Perluas aktivitas'"
              >
                <svg v-if="expandActivities" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
            </div>
          </div>
          <div v-if="expandActivities" class="border-t border-border-light">
            <div class="flow-root px-4 py-5 sm:px-6">
              <div v-if="filteredActivities.length === 0" class="py-6 text-center text-text-tertiary">
                <p v-if="activityType !== 'all'">Tidak ada aktivitas tipe "{{ getActivityTypeName(activityType) }}" untuk dokumen ini.</p>
                <p v-else>Belum ada aktivitas tercatat untuk dokumen ini.</p>
              </div>
              
              <ul v-else role="list" class="-mb-8">
                <li v-for="(activity, index) in displayedActivities" :key="activity.id">
                  <div class="relative pb-8">
                    <span v-if="index !== displayedActivities.length - 1" class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-border-light" aria-hidden="true"></span>
                    <div class="relative flex items-start space-x-3">
                      <div class="relative">
                        <div class="flex items-center justify-center w-10 h-10 bg-background-secondary rounded-full">
                          <svg v-if="activity.description === 'deleted document'" class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                          <svg v-else-if="activity.description === 'downloaded document'" class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                          </svg>
                          <svg v-else-if="activity.description === 'updated'" class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                          </svg>
                          <svg v-else-if="activity.description === 'created'" class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                          </svg>
                          <svg v-else class="w-5 h-5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                          </svg>
                        </div>
                      </div>
                      <div class="min-w-0 flex-1">
                        <div>
                          <div class="text-sm">
                            <span class="font-medium text-text-primary">{{ activity.causer ? activity.causer.name : 'Sistem' }}</span>
                          </div>
                          <p class="mt-0.5 text-sm text-text-tertiary">
                            {{ formatDateTime(activity.created_at) }}
                          </p>
                        </div>
                        <div class="mt-2 text-sm text-text-secondary">
                          <p>
                            {{ formatActivityDescription(activity) }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
              
              <!-- Paginasi untuk aktivitas -->
              <div v-if="activityPages > 1" class="mt-4 flex justify-center">
                <nav class="inline-flex shadow-sm -space-x-px" aria-label="Pagination">
                  <button 
                    @click="activityPage = Math.max(1, activityPage - 1)" 
                    :disabled="activityPage === 1"
                    class="relative inline-flex items-center px-2 py-1 rounded-l-md border border-border-light bg-background-tertiary text-sm font-medium text-text-secondary hover:bg-background-secondary disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <span class="sr-only">Sebelumnya</span>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                      <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                  </button>
                  <span class="relative inline-flex items-center px-4 py-1 border border-border-light bg-background-tertiary text-sm font-medium text-text-secondary">
                    {{ activityPage }} / {{ activityPages }}
                  </span>
                  <button 
                    @click="activityPage = Math.min(activityPages, activityPage + 1)" 
                    :disabled="activityPage === activityPages"
                    class="relative inline-flex items-center px-2 py-1 rounded-r-md border border-border-light bg-background-tertiary text-sm font-medium text-text-secondary hover:bg-background-secondary disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <span class="sr-only">Berikutnya</span>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                  </button>
                </nav>
              </div>
            </div>
          </div>
          <div v-else class="border-t border-border-light py-4 px-6 text-center text-text-tertiary text-sm">
            <p v-if="filteredActivities.length > 0">
              {{ filteredActivities.length }} aktivitas tersedia. Klik tombol panah untuk menampilkan.
            </p>
            <p v-else>
              <span v-if="activityType !== 'all'">Tidak ada aktivitas tipe "{{ getActivityTypeName(activityType) }}" untuk dokumen ini.</span>
              <span v-else>Belum ada aktivitas tercatat.</span>
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Preview Modal -->
    <modal-dialog :show="showPreviewModal" @close="showPreviewModal = false" max-width="4xl">
      <div class="p-6 bg-background-primary">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium leading-6 text-text-primary">
            Preview: {{ document.file_name || document.name }}
          </h3>
          <button @click="showPreviewModal = false" class="text-text-tertiary hover:text-text-secondary transition-colors">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <div v-if="isLoading" class="flex items-center justify-center h-96 bg-background-secondary rounded-lg">
          <div class="flex flex-col items-center">
            <svg class="w-10 h-10 text-primary-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-2 text-sm text-text-secondary">Memuat preview...</p>
          </div>
        </div>
        
        <div v-else-if="previewError" class="flex flex-col items-center justify-center h-96 bg-background-secondary rounded-lg">
          <svg class="w-12 h-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ previewError }}</p>
          <p class="mt-1 text-xs text-text-tertiary px-6 text-center">
            Sistem tidak dapat menampilkan pratinjau dokumen ini. Dokumen mungkin rusak atau format tidak didukung untuk pratinjau. 
            Anda tetap dapat mengunduh dokumen aslinya.
          </p>
          <a :href="currentOriginalUrl" target="_blank" class="px-4 py-2 mt-4 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
            Download File Asli
          </a>
        </div>
        
        <div v-else-if="previewType === 'pdf'" class="h-96 rounded-lg overflow-hidden border border-border-light">
          <iframe :src="currentPreviewUrl" frameborder="0" width="100%" height="100%"></iframe>
        </div>
        
        <div v-else-if="previewType === 'external'" class="h-96">
          <div class="flex flex-col h-full">
            <div class="flex justify-between mb-2">
              <div class="flex flex-col w-full">
                <div class="flex space-x-2 mb-2">
                  <button 
                    @click="switchViewer('google')" 
                    class="px-3 py-1 text-xs rounded-md transition-colors"
                    :class="activeViewer === 'google' 
                      ? 'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300' 
                      : 'bg-background-tertiary text-text-secondary hover:bg-background-secondary'"
                  >
                    Google Docs Viewer
                  </button>
                  <button 
                    @click="switchViewer('office')" 
                    class="px-3 py-1 text-xs rounded-md transition-colors"
                    :class="activeViewer === 'office' 
                      ? 'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300' 
                      : 'bg-background-tertiary text-text-secondary hover:bg-background-secondary'"
                  >
                    Microsoft Office Viewer
                  </button>
                </div>
                <p class="text-xs text-text-tertiary mb-2">
                  Sistem menggunakan viewer eksternal (Google/Microsoft) untuk menampilkan dokumen non-PDF. 
                  Keberhasilan preview bergantung pada layanan pihak ketiga dan koneksi internet.
                </p>
              </div>
            </div>
            <iframe :src="currentPreviewUrl" frameborder="0" class="w-full h-full rounded-lg border border-border-light"></iframe>
          </div>
        </div>
        
        <div class="flex justify-end mt-6 space-x-3">
          <a :href="currentOriginalUrl" target="_blank" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
            Download File Asli
          </a>
          <button @click="showPreviewModal = false" class="px-4 py-2 text-sm font-medium text-text-primary bg-background-tertiary border border-border-light rounded-md shadow-sm hover:bg-background-secondary focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
            Tutup
          </button>
        </div>
      </div>
    </modal-dialog>

    <!-- Konfirmasi Hapus Modal -->
    <modal-dialog :show="showDeleteModal" @close="showDeleteModal = false">
      <div class="p-6 bg-background-primary">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium leading-6 text-text-primary">
            Konfirmasi Hapus
          </h3>
          <button @click="showDeleteModal = false" class="text-text-tertiary hover:text-text-secondary transition-colors">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <p class="text-sm text-text-secondary">
          Apakah Anda yakin ingin menghapus dokumen <strong class="text-text-primary font-medium">"{{ document.file_name || document.name }}"</strong>? 
          Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="flex justify-end mt-6 space-x-3">
          <button @click="showDeleteModal = false" class="px-4 py-2 text-sm font-medium text-text-primary bg-background-tertiary border border-border-light rounded-md shadow-sm hover:bg-background-secondary focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
            Batal
          </button>
          <Link
            :href="route('admin.documents.destroy', document.id)"
            method="delete"
            as="button"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors"
            @click="showDeleteModal = false"
          >
            Hapus
          </Link>
        </div>
      </div>
    </modal-dialog>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useForm, Link, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ModalDialog from '@/Components/ModalDialog.vue';

const props = defineProps({
  document: Object,
  activities: Array,
});

const showPreviewModal = ref(false);
const showDeleteModal = ref(false);
const isLoading = ref(false);
const previewError = ref(null);
const previewType = ref(null);
const currentPreviewUrl = ref('');
const currentOriginalUrl = ref('');
const officeViewerUrl = ref('');
const activeViewer = ref('google');
const localEnvironment = ref(false);
const activityLimit = ref(5);
const expandActivities = ref(false);
const activityPage = ref(1);
const activityType = ref('all');

// Computed property untuk aktivitas yang difilter berdasarkan tipe
const filteredActivities = computed(() => {
  if (!props.activities || !props.activities.length) return [];
  
  return props.activities.filter(activity => {
    if (activityType.value === 'all') return true;
    return activity.description === activityType.value;
  });
});

// Computed property untuk total halaman setelah filter
const activityPages = computed(() => {
  if (!filteredActivities.value.length) return 1;
  if (activityLimit.value <= 0) return 1;
  return Math.ceil(filteredActivities.value.length / activityLimit.value);
});

// Computed property untuk aktivitas yang ditampilkan dengan pagination setelah filter
const displayedActivities = computed(() => {
  if (!filteredActivities.value.length) return [];
  
  // Jika limit 0, tampilkan semua
  if (activityLimit.value <= 0) return filteredActivities.value;
  
  // Hitung offset dan batas
  const start = (activityPage.value - 1) * activityLimit.value;
  const end = start + activityLimit.value;
  
  return filteredActivities.value.slice(start, end);
});

// Reset halaman ke 1 saat limit berubah
watch(activityLimit, () => {
  activityPage.value = 1;
});

// Reset halaman ke 1 saat filter berubah
watch(activityType, () => {
  activityPage.value = 1;
});

// Pastikan halaman tidak lebih dari total halaman yang ada
watch(activityPages, (newVal) => {
  if (activityPage.value > newVal) {
    activityPage.value = newVal;
  }
});

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  }).format(date);
};

const formatDateTime = (dateString) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(date);
};

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes';
  
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
  
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const statusLabel = (status) => {
  const labels = {
    'pending': 'Menunggu',
    'approved': 'Disetujui',
    'rejected': 'Ditolak'
  };
  
  return labels[status] || status;
};

const formatActivityDescription = (activity) => {
  switch (activity.description) {
    case 'created':
      return 'Membuat dokumen';
    case 'updated':
      return 'Mengupdate dokumen';
    case 'deleted document':
      return 'Menghapus dokumen';
    case 'downloaded document':
      return 'Mengunduh dokumen';
    default:
      return activity.description;
  }
};

// Fungsi untuk mendapatkan nama pengirim
const getPengirimName = (document) => {
  // Prioritaskan pengirim dari metadata
  if (document?.metadata?.pengirim) {
    return document.metadata.pengirim;
  }
  
  // Gunakan metadata.name jika tersedia
  if (document?.metadata?.name) {
    return document.metadata.name;
  }
  
  // Cek jika deskripsi berisi informasi pengirim (format lama)
  if (document?.description?.includes('Dari pengunjung:')) {
    return document.description.replace('Dari pengunjung:', '').trim();
  }

  // Cek jika deskripsi berisi nama pengirim dengan format lainnya
  if (document?.description?.includes('pengunjung:')) {
    return document.description.replace(/.*pengunjung:/, '').trim();
  }
  
  // Alternatif lain: cek dalam deskripsi jika ada format "dokumen dari [nama]"
  if (document?.description?.includes('dokumen dari')) {
    const match = document.description.match(/dokumen dari\s+([^\.]+)/i);
    if (match && match[1]) {
      return match[1].trim();
    }
  }
  
  // Sebagai fallback terakhir, gunakan nama uploader
  if (document?.user?.name && !document.user.name.includes('Admin')) {
    return document.user.name;
  }
  
  return 'Pengunjung';
};

// Fungsi untuk mendapatkan tipe file dari path jika tidak ada di properti file_type
const getFileTypeFromPath = (filePath) => {
  if (!filePath) return 'Tidak diketahui';
  
  const extension = filePath.split('.').pop().toLowerCase();
  const types = {
    'pdf': 'PDF Document',
    'doc': 'Word Document',
    'docx': 'Word Document',
    'xls': 'Excel Spreadsheet',
    'xlsx': 'Excel Spreadsheet',
    'ppt': 'PowerPoint Presentation',
    'pptx': 'PowerPoint Presentation',
    'txt': 'Text Document',
    'jpg': 'JPEG Image',
    'jpeg': 'JPEG Image',
    'png': 'PNG Image',
    'gif': 'GIF Image',
    'zip': 'ZIP Archive',
    'rar': 'RAR Archive'
  };
  
  return types[extension] || `${extension.toUpperCase()} File`;
};

const openPreviewModal = async () => {
  showPreviewModal.value = true;
  isLoading.value = true;
  previewError.value = null;
  previewType.value = null;
  currentPreviewUrl.value = '';
  officeViewerUrl.value = '';
  localEnvironment.value = false;
  
  try {
    // Tambahkan timestamp untuk mencegah cache
    const timestamp = new Date().getTime();
    const response = await fetch(`${route('documents.preview', props.document.id)}?t=${timestamp}`);
    const data = await response.json();
    
    console.log('Preview response:', data);
    
    if (response.ok && data.success) {
      previewType.value = data.type;
      
      if (data.type === 'pdf') {
        currentPreviewUrl.value = data.previewUrl;
      } else if (data.type === 'external') {
        currentPreviewUrl.value = data.googleViewerUrl || data.previewUrl;
        officeViewerUrl.value = data.officeViewerUrl || '';
        activeViewer.value = 'google';
        
        // Cek jika ada warning untuk local environment
        if (data.localEnvironment) {
          localEnvironment.value = true;
          console.warn(data.warning);
        }
      } else {
        currentPreviewUrl.value = data.previewUrl;
      }
      
      currentOriginalUrl.value = data.originalUrl || route('documents.download', props.document.id);
    } else {
      previewError.value = data.error || 'Tidak dapat memuat preview dokumen';
      currentOriginalUrl.value = route('documents.download', props.document.id);
    }
  } catch (error) {
    console.error('Preview error:', error);
    previewError.value = 'Terjadi kesalahan saat memuat preview dokumen. Silakan coba lagi nanti.';
    currentOriginalUrl.value = route('documents.download', props.document.id);
  } finally {
    isLoading.value = false;
  }
};

const switchViewer = (viewer) => {
  activeViewer.value = viewer;
  
  if (viewer === 'google') {
    currentPreviewUrl.value = previewType.value === 'external' ? 
      (currentPreviewUrl.value || route('documents.download', props.document.id)) : 
      currentPreviewUrl.value;
  } else if (viewer === 'office') {
    currentPreviewUrl.value = officeViewerUrl.value || route('documents.download', props.document.id);
  }
};

const confirmDelete = () => {
  showDeleteModal.value = true;
};

const getActivityTypeName = (type) => {
  const activityTypes = {
    'all': 'Semua Aktivitas',
    'downloaded document': 'Download',
    'updated': 'Update/Edit',
    'created': 'Pembuatan',
    'deleted document': 'Penghapusan'
  };
  
  return activityTypes[type] || type;
};

// Computed property untuk memeriksa apakah ada metadata tambahan
const hasAdditionalMetadata = computed(() => {
  if (!props.document?.metadata) return false;
  return Object.keys(filteredMetadata.value).length > 0;
});

// Computed property untuk filter metadata (tanpa metadata yang sudah ditampilkan tersendiri)
const filteredMetadata = computed(() => {
  if (!props.document?.metadata) return {};
  
  // Daftar kunci yang sudah ditampilkan di bagian atas
  const alreadyDisplayed = ['whatsapp', 'city', 'pengirim', 'name'];
  
  // Buat salinan objek metadata tanpa kunci yang sudah ditampilkan
  const filtered = {};
  for (const [key, value] of Object.entries(props.document.metadata)) {
    if (!alreadyDisplayed.includes(key)) {
      filtered[key] = value;
    }
  }
  
  return filtered;
});

// Fungsi untuk memformat kunci metadata menjadi teks yang lebih baik
const formatMetadataKey = (key) => {
  // Ubah snake_case menjadi Title Case
  return key
    .split('_')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
};

// Fungsi untuk memformat nilai metadata
const formatMetadataValue = (value) => {
  if (value === null || value === undefined) {
    return 'Tidak ada';
  }
  
  if (Array.isArray(value)) {
    return value.join(', ');
  }
  
  if (typeof value === 'object') {
    return JSON.stringify(value);
  }
  
  return value.toString();
};
</script> 