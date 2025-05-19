<template>
  <AdminLayout :title="'Detail Form: ' + documentForm.title">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Detail Form: {{ documentForm.name || documentForm.title }}
        </h2>
        <div class="flex space-x-4">
          <Link
            :href="route('admin.document-forms.notifications.show', documentForm.id)"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            Pengaturan Notifikasi
          </Link>
          <Link
            :href="route('admin.document-forms.edit', documentForm.id)"
            class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            Edit
          </Link>
          <button
            @click="showPublicUrl"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            URL Publik
          </button>
        </div>
      </div>
    </template>

    <!-- Portal untuk dropdown menu -->
    <teleport to="body">
      <!-- Header dropdown portal -->
      <div 
        v-if="showHeaderDropdown" 
        class="fixed inset-0 z-[99999]" 
        @click="closeHeaderDropdown"
      >
        <div 
          ref="headerDropdownRef"
          class="absolute bg-white dark:bg-gray-800 rounded-md shadow-xl border border-gray-200 dark:border-gray-600 py-1 w-56"
          :style="{
            top: headerDropdownPosition.y + 'px',
            left: headerDropdownPosition.x + 'px',
          }"
          @click.stop
        >
          <div class="py-1 divide-y divide-gray-200 dark:divide-gray-600">
            <button 
              @click="showDocumentDetail(selectedHeaderDocument)"
              class="w-full flex items-center px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
              <svg class="mr-3 h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <span>Detail</span>
            </button>
            
            <button
              @click="openPreviewModal(selectedHeaderDocument)"
              class="w-full flex items-center px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
              <svg class="mr-3 h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
              <span>Preview</span>
            </button>
            
            <a 
              :href="selectedHeaderDocument ? route('documents.download', selectedHeaderDocument.id) : '#'"
              target="_blank" 
              class="flex items-center px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
              <svg class="mr-3 h-4 w-4 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
              <span>Download</span>
            </a>
          </div>
        </div>
      </div>
      
      <!-- Table dropdown portal -->
      <div 
        v-if="activeActionMenu" 
        class="fixed inset-0 z-[99999]" 
        @click="activeActionMenu = null"
      >
        <div 
          v-for="document in documents.data" 
          :key="document.id"
          v-show="activeActionMenu === document.id"
          class="absolute bg-white dark:bg-gray-800 rounded-md shadow-xl border border-gray-200 dark:border-gray-600 py-1 w-56"
          :style="{
            top: tableDropdownPosition.y + 'px',
            left: tableDropdownPosition.x + 'px',
          }"
          @click.stop
        >
          <div class="py-1 divide-y divide-gray-200 dark:divide-gray-600">
            <!-- Link to document detail -->
            <button 
              @click="showDocumentDetail(document)"
              class="w-full flex items-center px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
              <svg class="mr-3 h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <span>Detail</span>
            </button>
            
            <button
              @click="openPreviewModal(document)"
              class="w-full flex items-center px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
              <svg class="mr-3 h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
              <span>Preview</span>
            </button>
            
            <a 
              :href="route('documents.download', document.id)"
              target="_blank" 
              class="flex items-center px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
              <svg class="mr-3 h-4 w-4 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
              <span>Download</span>
            </a>
            
            <!-- Edit and delete links -->
            <Link 
              :href="route('admin.documents.edit', document.id)"
              class="flex items-center px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
              <svg class="mr-3 h-4 w-4 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              <span>Edit</span>
            </Link>
            
            <button 
              @click="confirmDelete(document)"
              class="w-full flex items-center px-4 py-2 text-xs text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
              <svg class="mr-3 h-4 w-4 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              <span>Hapus</span>
            </button>

            <!-- Tombol Status -->
            <div class="py-1 border-t border-gray-200 dark:border-gray-600">
              <button 
                @click="updateDocumentStatus(document, 'approved')" 
                class="w-full flex items-center px-4 py-2 text-xs text-green-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                :class="{ 'bg-green-50 dark:bg-green-900/20': document.status === 'approved' }"
                :disabled="processingStatus"
              >
                <svg v-if="processingStatus && processingDocumentId === document.id && processingStatusType === 'approved'" class="animate-spin mr-3 h-4 w-4 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="mr-3 h-4 w-4 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Disetujui</span>
              </button>
              
              <button 
                @click="updateDocumentStatus(document, 'rejected')" 
                class="w-full flex items-center px-4 py-2 text-xs text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                :class="{ 'bg-red-50 dark:bg-red-900/20': document.status === 'rejected' }"
                :disabled="processingStatus"
              >
                <svg v-if="processingStatus && processingDocumentId === document.id && processingStatusType === 'rejected'" class="animate-spin mr-3 h-4 w-4 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="mr-3 h-4 w-4 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>Ditolak</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </teleport>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Form Details -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-4">
            <div class="flex justify-between items-center">
              <div class="flex items-center gap-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                  {{ documentForm.title }}
                </h3>
                <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="{
                  'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': documentForm.is_active,
                  'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': !documentForm.is_active
                }">
                  {{ documentForm.is_active ? 'Aktif' : 'Tidak Aktif' }}
                </span>
                <!-- Toggle detail button -->
                <button @click="showFormDetails = !showFormDetails" class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                  <span class="mr-1">{{ showFormDetails ? 'Sembunyikan detail' : 'Lihat detail' }}</span>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :class="{'transform rotate-180': showFormDetails}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>
              </div>
              <div class="flex space-x-2">
                <Link
                  :href="route('admin.document-forms.notifications.show', documentForm.id)"
                  class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition"
                >
                  Pengaturan Notifikasi
                </Link>
                <Link :href="route('admin.document-forms.edit', documentForm.id)" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition">
                  Edit
                </Link>
                <button @click="showPublicUrl" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                  URL Publik
                </button>
              </div>
            </div>
            
            <!-- Detail panel (collapsible) -->
            <div v-show="showFormDetails" class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <div class="mb-4">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Judul</h4>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ documentForm.title }}</p>
                  </div>

                  <div class="mb-4">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Slug</h4>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ documentForm.slug }}</p>
                  </div>

                  <div class="mb-4">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</h4>
                    <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="{
                      'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': documentForm.is_active,
                      'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': !documentForm.is_active
                    }">
                      {{ documentForm.is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                  </div>

                  <div class="mb-4">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Dibuat Oleh</h4>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ documentForm.user.name }}</p>
                  </div>
                </div>

                <div>
                  <div class="mb-4">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Batas Waktu Pengumpulan</h4>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">
                      {{ documentForm.submission_deadline ? new Date(documentForm.submission_deadline).toLocaleString() : 'Tidak ada batas' }}
                    </p>
                  </div>

                  <div class="mb-4">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Pesan Ketika Ditutup</h4>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ documentForm.closed_message || 'Tidak ada pesan' }}</p>
                  </div>

                  <div class="mb-4">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Dibuat</h4>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ new Date(documentForm.created_at).toLocaleString() }}</p>
                  </div>

                  <div class="mb-4">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Terakhir Diperbarui</h4>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ new Date(documentForm.updated_at).toLocaleString() }}</p>
                  </div>
                </div>
              </div>

              <div v-if="documentForm.description" class="mt-4">
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi</h4>
                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ documentForm.description }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Documents Section -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">
              Dokumen yang Dikirim ({{ documents.total }})
            </h3>
            
            <!-- Flash Messages -->
            <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 border border-green-200 text-green-800 rounded-lg">
              {{ $page.props.flash.success }}
            </div>

            <!-- Filter dan pencarian -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
              <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                  <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                  </svg>
                </div>
                <input 
                  id="search" 
                  v-model="search" 
                  name="search" 
                  type="text" 
                  class="block w-full py-2 pl-10 pr-3 text-sm bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500" 
                  placeholder="Cari dokumen..." 
                  @keyup.enter="handleSearch"
                />
                <button 
                  v-if="search"
                  @click="resetSearch"
                  class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
                >
                  <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </button>
              </div>
              
              <div class="flex items-center gap-2">
                <div class="flex items-center gap-2">
                  <button 
                    @click="openImportModal" 
                    class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-white bg-emerald-600 border border-transparent rounded-lg shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors"
                  >
                    <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l-4-4m4 4l4-4" />
                    </svg>
                    Import
                  </button>
                  <button 
                    @click="exportToExcel" 
                    class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors"
                  >
                    <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Export
                  </button>
                </div>
              </div>
            </div>

            <!-- Statistik Dokumen -->
            <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              <div class="bg-white dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Dokumen</h3>
                <p class="text-2xl font-bold text-primary-600">{{ documents.total }}</p>
              </div>
              <div class="bg-white dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Dokumen Disetujui</h3>
                <p class="text-2xl font-bold text-green-600">{{ getDocumentCountByStatus('approved') }}</p>
              </div>
              <div class="bg-white dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Dokumen Pending</h3>
                <p class="text-2xl font-bold text-yellow-600">{{ getDocumentCountByStatus('pending') }}</p>
              </div>
              <div class="bg-white dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Dokumen Ditolak</h3>
                <p class="text-2xl font-bold text-red-600">{{ getDocumentCountByStatus('rejected') }}</p>
              </div>
            </div>

            <!-- Tabel dokumen -->
            <div v-if="documents.data.length > 0" class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 shadow-sm relative mt-4">
              <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                  <tr>
                    <th scope="col" class="px-4 py-4 border-b border-gray-200 dark:border-gray-600">
                      <div class="flex items-center gap-3">
                        <input
                          type="checkbox"
                          class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                          v-model="selectAll"
                          @change="toggleSelectAll"
                        />
                        <span v-if="selectedDocuments.length > 0" class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                          {{ selectedDocuments.length }} dari {{ documents.data.length }}
                        </span>
                      </div>
                    </th>
                    <th scope="col" class="px-6 py-4 font-bold text-left text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600">Nama Dokumen</th>
                    <th scope="col" class="px-6 py-4 font-bold text-left text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600">Pengirim</th>
                    <th scope="col" class="px-6 py-4 font-bold text-left text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600">WhatsApp</th>
                    <th scope="col" class="px-6 py-4 font-bold text-left text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600">Asal Kota</th>
                    <th scope="col" class="px-6 py-4 font-bold text-left text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600">Status</th>
                    <th scope="col" class="px-6 py-4 font-bold text-left text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600">Tanggal Upload</th>
                    <th scope="col" class="px-4 py-4 w-10 border-b border-gray-200 dark:border-gray-600">
                      <button
                        v-if="selectedDocuments.length > 0"
                        @click="confirmBulkDelete"
                        class="inline-flex items-center justify-center p-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors"
                        title="Hapus dokumen terpilih"
                      >
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="document in documents.data" :key="document.id" class="border-b border-gray-200 dark:border-gray-600 last:border-0 bg-white dark:bg-gray-700 hover:bg-primary-50 dark:hover:bg-gray-600/60 transition-colors group">
                    <td class="px-4 py-4">
                      <div class="flex items-center">
                        <input
                          type="checkbox"
                          class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                          v-model="selectedDocuments"
                          :value="document.id"
                          @change="handleCheckboxChange"
                        />
                      </div>
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100 max-w-[220px] break-words group-hover:text-primary-700 dark:group-hover:text-primary-400">{{ document?.file_name || document?.name || 'Tanpa Nama' }}</td>
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-300 break-words">{{ getPengirimName(document) }}</td>
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-300 break-words">{{ document?.whatsapp_number || document?.metadata?.whatsapp || '-' }}</td>
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-300 break-words max-w-[150px]">{{ document?.metadata?.city || '-' }}</td>
                    <td class="px-6 py-4">
                      <span 
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border border-transparent transition-colors"
                        :class="{
                          'bg-yellow-50 text-yellow-800 dark:bg-yellow-900/60 dark:text-yellow-200': document?.status === 'pending',
                          'bg-green-50 text-green-700 dark:bg-green-900/60 dark:text-green-200': document?.status === 'approved',
                          'bg-red-50 text-red-700 dark:bg-red-900/60 dark:text-red-200': document?.status === 'rejected',
                        }"
                      >
                        {{ statusLabel(document?.status || 'pending') }}
                      </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-300 whitespace-nowrap">{{ formatDate(document?.uploaded_at || document?.created_at) }}</td>
                    <td class="px-4 py-4 text-right relative">
                      <!-- Aksi -->
                      <div class="dropdown relative inline-block" :data-dropdown-id="document.id">
                        <button 
                          @click="toggleActionMenu(document.id, $event)" 
                          class="p-2 rounded-lg text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600/80 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                          </svg>
                        </button>
                        <!-- Dropdown aksi DIHAPUS - digantikan oleh portal -->
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else class="flex flex-col items-center justify-center py-12 bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600">
              <svg 
                class="w-16 h-16 text-gray-400" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24" 
                xmlns="http://www.w3.org/2000/svg"
              >
                <path 
                  stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                ></path>
              </svg>
              <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">Tidak ada dokumen</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Belum ada dokumen yang diupload untuk form ini.
              </p>
            </div>

            <!-- Pagination -->
            <div v-if="documents.data.length > 0" class="mt-4">
              <Pagination :links="documents.links" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
  
  <!-- Modals -->
  <!-- Preview Modal -->
  <Modal :show="showPreviewModal" max-width="4xl" @close="showPreviewModal = false" :closeable="true">
    <div class="p-6">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
          Preview Dokumen: {{ previewDocument?.name || previewDocument?.file_name || 'Dokumen' }}
        </h3>
        <button
          @click="showPreviewModal = false"
          class="rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <span class="sr-only">Close</span>
          <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <div v-if="isLoading" class="flex flex-col items-center justify-center py-12">
        <svg class="animate-spin h-10 w-10 text-primary-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-500 dark:text-gray-400">Memuat preview dokumen...</p>
      </div>
      
      <div v-else-if="previewError" class="flex flex-col items-center justify-center py-12 bg-red-50 dark:bg-red-900/20 rounded-lg">
        <svg class="h-16 w-16 text-red-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-2">Preview tidak tersedia</h3>
        <p class="text-center text-gray-500 dark:text-gray-400 mb-4">{{ previewError }}</p>
        <a 
          :href="currentOriginalUrl" 
          target="_blank" 
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
        >
          <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          Download dokumen
        </a>
      </div>
      
      <div v-else>
        <!-- Preview tools header -->
        <div v-if="previewType === 'external'" class="flex flex-wrap items-center justify-between gap-2 mb-4">
          <div class="flex items-center space-x-4">
            <button 
              @click="switchViewer('google')" 
              class="px-3 py-1 text-sm rounded-md transition-colors"
              :class="activeViewer === 'google' ? 'bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200 font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800'"
            >
              Google Viewer
            </button>
            <button 
              v-if="officeViewerUrl"
              @click="switchViewer('office')" 
              class="px-3 py-1 text-sm rounded-md transition-colors"
              :class="activeViewer === 'office' ? 'bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200 font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800'"
            >
              Office Viewer
            </button>
          </div>
          <a 
            :href="currentOriginalUrl" 
            target="_blank" 
            class="inline-flex items-center px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
          >
            <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Download
          </a>
        </div>
        
        <!-- PDF Preview -->
        <div v-if="previewType === 'pdf'" class="bg-gray-800 rounded-lg overflow-hidden h-[75vh]">
          <iframe 
            :src="currentPreviewUrl" 
            class="w-full h-full border-0"
            allowfullscreen
          ></iframe>
        </div>
        
        <!-- External Preview (Google/Office) -->
        <div v-else-if="previewType === 'external'" class="bg-gray-800 rounded-lg overflow-hidden h-[75vh]">
          <iframe 
            :src="currentPreviewUrl"
            class="w-full h-full border-0"
            allowfullscreen
          ></iframe>
        </div>
        
        <!-- Image Preview -->
        <div v-else class="bg-gray-800 rounded-lg overflow-hidden text-center p-4 h-[75vh]">
          <img :src="currentPreviewUrl" alt="Preview dokumen" class="max-h-full max-w-full inline-block object-contain" />
        </div>
      </div>
    </div>
  </Modal>

  <!-- Delete Confirmation Modal -->
  <ModalDialog :show="showDeleteModal" @close="showDeleteModal = false">
    <div class="p-6">
      <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mx-auto mb-4">
        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-center text-gray-900 dark:text-gray-100 mb-5">Konfirmasi Hapus</h3>
      <p class="text-sm text-center text-gray-500 dark:text-gray-400 mb-5">
        Apakah Anda yakin ingin menghapus dokumen ini? Dokumen yang dihapus tidak dapat dikembalikan.
      </p>
      <div class="flex justify-center gap-3 mt-6">
        <SecondaryButton @click="showDeleteModal = false" :disabled="processingDelete">
          Batal
        </SecondaryButton>
        <button
          @click="executeDelete"
          class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          :class="{'opacity-75 cursor-not-allowed': processingDelete}"
          :disabled="processingDelete"
        >
          <svg v-if="processingDelete" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ processingDelete ? 'Menghapus...' : 'Hapus' }}
        </button>
      </div>
    </div>
  </ModalDialog>

  <!-- Public URL Modal -->
  <ModalDialog :show="publicUrlModal" @close="closePublicUrlModal">
    <div class="p-6">
      <div class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 mx-auto mb-4">
        <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-center text-gray-900 dark:text-gray-100 mb-5">URL Publik Form</h3>
      <p class="text-sm text-center text-gray-500 dark:text-gray-400 mb-5">
        Bagikan URL berikut untuk mengizinkan pengunjung mengunggah dokumen ke form ini.
      </p>
      
      <div class="mt-4 relative">
        <input
          ref="publicUrlInput"
          type="text"
          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:text-gray-300"
          readonly
          :value="publicUrl"
        />
        <button 
          @click="copyToClipboard" 
          class="absolute right-2 top-1/2 transform -translate-y-1/2 px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded border border-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm"
        >
          {{ copied ? 'Disalin!' : 'Salin' }}
        </button>
      </div>
      
      <div class="flex justify-center mt-6">
        <button
          @click="closePublicUrlModal"
          class="inline-flex items-center justify-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
        >
          Tutup
        </button>
      </div>
    </div>
  </ModalDialog>

  <!-- Import Modal -->
  <ModalDialog :show="showImportModal" @close="showImportModal = false">
    <div class="p-6">
      <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-5">Import Dokumen</h3>
      
      <form @submit="submitImportForm">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            File Excel (.xlsx, .xls, .csv)
          </label>
          
          <div 
            @dragover="onDragOver"
            @dragleave="onDragLeave"
            @drop="onFileDrop"
            :class="['border-2 border-dashed rounded-lg p-6 text-center transition-colors', 
              isDragging ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-300 dark:border-gray-600'
            ]"
          >
            <div v-if="!importFile">
              <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
              </svg>
              <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                <span class="font-medium text-primary-600 dark:text-primary-400 hover:text-primary-500 cursor-pointer" @click="triggerFileInput">Pilih file</span> atau tarik dan letakkan file di sini.
              </p>
              <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Format yang didukung: .xlsx, .xls, .csv
              </p>
              <input 
                type="file" 
                class="hidden" 
                ref="importFileInput"
                @change="onFileInputChange"
                accept=".xlsx,.xls,.csv" 
              />
            </div>
            <div v-else class="flex items-center justify-between p-2 bg-gray-100 dark:bg-gray-700 rounded">
              <div class="flex items-center">
                <svg class="h-8 w-8 text-emerald-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <div class="text-left">
                  <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate max-w-xs">{{ importFile.name }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatFileSize(importFile.size) }}</p>
                </div>
              </div>
              <button 
                type="button"
                @click="importFile = null"
                class="p-1 rounded-full text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
              >
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
          
          <div v-if="importError" class="mt-2 text-sm text-red-600 dark:text-red-400">
            {{ importError }}
          </div>
        </div>
        
        <div class="flex justify-between items-center mt-6">
          <button 
            type="button"
            @click="downloadTemplate"
            class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-500 flex items-center"
          >
            <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Download Template
          </button>
          
          <div>
            <button 
              type="button"
              @click="showImportModal = false"
              class="inline-flex justify-center px-4 py-2 mr-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            >
              Batal
            </button>
            
            <button 
              type="submit"
              :disabled="!importFile || importProcessing"
              class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="importProcessing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ importProcessing ? 'Importing...' : 'Import' }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </ModalDialog>

  <!-- Bulk Delete Modal -->
  <ModalDialog :show="showBulkDeleteModal" @close="showBulkDeleteModal = false">
    <div class="p-6">
      <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mx-auto mb-4">
        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-center text-gray-900 dark:text-gray-100 mb-2">Hapus Dokumen Terpilih</h3>
      <p class="text-sm text-center text-gray-500 dark:text-gray-400 mb-5">
        Anda akan menghapus {{ selectedDocuments.length }} dokumen. Tindakan ini tidak dapat dibatalkan. Dokumen yang dihapus tidak akan dapat dipulihkan.
      </p>
      <div class="flex justify-center gap-3 mt-6">
        <SecondaryButton @click="showBulkDeleteModal = false" :disabled="bulkDeleteForm.processing">
          Batal
        </SecondaryButton>
        <button
          @click="executeBulkDelete"
          class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          :class="{'opacity-75 cursor-not-allowed': bulkDeleteForm.processing}"
          :disabled="bulkDeleteForm.processing"
        >
          <svg v-if="bulkDeleteForm.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ bulkDeleteForm.processing ? 'Menghapus...' : 'Hapus' }}
        </button>
      </div>
    </div>
  </ModalDialog>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount, computed } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import ModalDialog from '@/Components/ModalDialog.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import axios from 'axios';

const props = defineProps({
  documentForm: Object,
  documents: Object,
  filters: Object,
});

// State variables
const showPreviewModal = ref(false);
const showDeleteModal = ref(false);
const showBulkDeleteModal = ref(false);
const showImportModal = ref(false);
const publicUrlModal = ref(false);
const publicUrl = ref('');
const copied = ref(false);
const publicUrlInput = ref(null);
const activeActionMenu = ref(null);
const menuPosition = ref({ x: 0, y: 0 });
const selectAll = ref(false);
const selectedDocuments = ref([]);
const previewDocument = ref(null);
const documentToDelete = ref(null);
const processingDelete = ref(false);
const isLoading = ref(false);
const previewError = ref(null);
const previewType = ref(null);
const currentPreviewUrl = ref('');
const currentOriginalUrl = ref('');
const officeViewerUrl = ref('');
const activeViewer = ref('google');
const perPage = ref(props.documents.per_page || 10);
const processingStatus = ref(false);
const processingDocumentId = ref(null);
const processingStatusType = ref(null);
const search = ref(props.filters?.search || '');
const importFile = ref(null);
const importError = ref(null);
const importProcessing = ref(false);
const isDragging = ref(false);
const importFileInput = ref(null);
const showFormDetails = ref(false);
const headerDropdownRef = ref(null);
const showHeaderDropdown = ref(false);
const headerDropdownPosition = ref({ x: 0, y: 0 });
const tableDropdownPosition = ref({ x: 0, y: 0 });
const selectedHeaderDocument = ref(null);

const deleteForm = useForm({});
const bulkDeleteForm = useForm({ document_ids: [] });

// Fungsi status label
const statusLabel = (status) => {
  const labels = {
    'pending': 'Menunggu',
    'approved': 'Disetujui',
    'rejected': 'Ditolak'
  };
  
  return labels[status] || status;
};

// Fungsi format tanggal
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(date);
};

// Mendapatkan jumlah dokumen berdasarkan status
const getDocumentCountByStatus = (status) => {
  return props.documents.data.filter(doc => doc.status === status).length;
};

// Toggle menu aksi
const toggleActionMenu = (documentId, event) => {
  // Mencegah event bubbling
  event.preventDefault();
  event.stopPropagation();
  
  console.log('Tombol aksi diklik untuk dokumen:', documentId);
  
  // Cari dokumen dari data
  const document = props.documents.data.find(doc => doc.id === documentId);
  if (!document) return;

  // Cek apakah tombol ini ada di header
  const button = event.target.closest('button');
  if (!button) return;
  
  const buttonRect = button.getBoundingClientRect();
  const isInHeader = buttonRect.top < 200; // Asumsi: header berada di atas 200px dari atas browser
  
  if (isInHeader) {
    console.log('Dropdown berada di header area, menggunakan portal');
    
    // Gunakan teleport untuk dropdown di header
    activeActionMenu.value = null;
    selectedHeaderDocument.value = document;
    
    // Letakkan dropdown di atas tombol alih-alih di bawah
    headerDropdownPosition.value = {
      x: buttonRect.left - 170,
      y: buttonRect.top - 150 // Posisikan 150px di atas tombol
    };
    
    // Tampilkan dropdown
    showHeaderDropdown.value = true;
  } else {
    console.log('Dropdown berada di area tabel, menggunakan portal');
    // Toggle menu di tabel reguler menggunakan portal
    if (activeActionMenu.value === documentId) {
      console.log('Menutup menu karena sudah aktif');
      activeActionMenu.value = null;
    } else {
      console.log('Membuka menu untuk dokumen:', documentId);
      // Tutup menu lain yang mungkin terbuka
      activeActionMenu.value = documentId;
      
      // Posisikan dropdown di atas tombol alih-alih di bawah
      tableDropdownPosition.value = {
        x: window.innerWidth - buttonRect.right > 250 ? buttonRect.right - 50 : buttonRect.left - 200,
        y: buttonRect.top - 220 // Posisikan 220px di atas tombol (tergantung tinggi dropdown)
      };
    }
  }
};

// Function to close header dropdown
const closeHeaderDropdown = () => {
  showHeaderDropdown.value = false;
};

// Toggle select all documents
const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedDocuments.value = props.documents.data.map(doc => doc.id);
  } else {
    selectedDocuments.value = [];
  }
};

// Handle checkbox change
const handleCheckboxChange = () => {
  selectAll.value = selectedDocuments.value.length === props.documents.data.length;
};

// Clear selection
const clearSelection = () => {
  selectAll.value = false;
  selectedDocuments.value = [];
};

// Function to show public URL dialog
const showPublicUrl = async () => {
  publicUrlModal.value = true;
  
  try {
    const response = await axios.get(route('admin.document-forms.public-url', props.documentForm.id));
    publicUrl.value = response.data.publicUrl;
  } catch (error) {
    console.error('Error fetching public URL:', error);
    publicUrl.value = 'Error: Tidak dapat mengambil URL publik';
  }
};

// Function to close public URL dialog
const closePublicUrlModal = () => {
  publicUrlModal.value = false;
  publicUrl.value = '';
  copied.value = false;
};

// Function to copy URL to clipboard
const copyToClipboard = () => {
  if (publicUrlInput.value) {
    publicUrlInput.value.select();
    document.execCommand('copy');
    copied.value = true;
    
    setTimeout(() => {
      copied.value = false;
    }, 2000);
  }
};

// Function to open preview modal
const openPreviewModal = async (document) => {
  previewDocument.value = document;
  showPreviewModal.value = true;
  isLoading.value = true;
  previewError.value = null;
  previewType.value = null;
  currentPreviewUrl.value = '';
  officeViewerUrl.value = '';
  
  try {
    const timestamp = new Date().getTime();
    const response = await fetch(`${route('documents.preview', document.id)}?t=${timestamp}`);
    const data = await response.json();
    
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
          console.warn(data.warning);
        }
      } else {
        currentPreviewUrl.value = data.previewUrl;
      }
      
      currentOriginalUrl.value = data.originalUrl || route('documents.download', document.id);
    } else {
      previewError.value = data.error || 'Tidak dapat memuat preview dokumen';
      currentOriginalUrl.value = route('documents.download', document.id);
    }
  } catch (error) {
    console.error('Preview error:', error);
    previewError.value = 'Terjadi kesalahan saat memuat preview dokumen. Silakan coba lagi nanti.';
    currentOriginalUrl.value = route('documents.download', document.id);
  } finally {
    isLoading.value = false;
  }
};

// Function to switch preview viewer
const switchViewer = (viewer) => {
  activeViewer.value = viewer;
  
  if (viewer === 'google') {
    currentPreviewUrl.value = previewType.value === 'external' ? 
      (currentPreviewUrl.value || route('documents.download', previewDocument.value.id)) : 
      currentPreviewUrl.value;
  } else if (viewer === 'office') {
    currentPreviewUrl.value = officeViewerUrl.value || route('documents.download', previewDocument.value.id);
  }
};

// Function to confirm delete
const confirmDelete = (document) => {
  documentToDelete.value = document;
  showDeleteModal.value = true;
  activeActionMenu.value = null;
};

// Function to show document detail
const showDocumentDetail = (document) => {
  activeActionMenu.value = null; // Tutup dropdown
  
  // Gunakan URL langsung alih-alih route()
  window.location.href = `/admin/documents/${document.id}`;
};

// Functions related to document deletion
const executeDelete = () => {
  if (!documentToDelete.value) return;
  
  processingDelete.value = true;
  
  // Gunakan axios alih-alih router.delete
  axios.delete(`/admin/documents/${documentToDelete.value.id}`)
    .then(response => {
      showDeleteModal.value = false;
      processingDelete.value = false;
      documentToDelete.value = null;
      
      // Refresh halaman
      router.reload();
    })
    .catch(error => {
      console.error('Error deleting document:', error);
      processingDelete.value = false;
    })
    .finally(() => {
      processingDelete.value = false;
    });
};

// Function to update document status
const updateDocumentStatus = (document, status) => {
  if (processingStatus.value) return;
  
  processingStatus.value = true;
  processingDocumentId.value = document.id;
  processingStatusType.value = status;
  
  // Gunakan axios alih-alih router.put
  axios.put(`/admin/documents/${document.id}/status`, {
    status: status
  })
    .then(response => {
      processingStatus.value = false;
      activeActionMenu.value = null;
      
      // Update dokumen status secara lokal
      const index = props.documents.data.findIndex(d => d.id === document.id);
      if (index !== -1) {
        props.documents.data[index].status = status;
      }
    })
    .catch(error => {
      console.error('Error updating document status:', error);
      processingStatus.value = false;
    })
    .finally(() => {
      setTimeout(() => {
        processingStatus.value = false;
        processingDocumentId.value = null;
        processingStatusType.value = null;
      }, 500);
    });
};

// Function to change per page
const changePerPage = () => {
  router.get(route('admin.document-forms.show', props.documentForm.id), { per_page: perPage.value }, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
};

// Function to get sender name
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

// Function to handle search
const handleSearch = () => {
  router.get(route('admin.document-forms.show', props.documentForm.id), 
    { search: search.value, per_page: perPage.value }, 
    {
      preserveState: true,
      replace: true,
    }
  );
};

// Function to reset search
const resetSearch = () => {
  search.value = '';
  router.get(route('admin.document-forms.show', props.documentForm.id), 
    { per_page: perPage.value }, 
    {
      preserveState: true,
      replace: true,
    }
  );
};

// Function to export to Excel - removed

// Function to open import modal
const openImportModal = () => {
  showImportModal.value = true;
  importFile.value = null;
  importError.value = null;
  importProcessing.value = false;
  isDragging.value = false;
};

// Function to handle file drag over
const onDragOver = (e) => {
  e.preventDefault();
  isDragging.value = true;
};

// Function to handle file drag leave
const onDragLeave = () => {
  isDragging.value = false;
};

// Function to handle file drop
const onFileDrop = (e) => {
  e.preventDefault();
  isDragging.value = false;
  
  if (e.dataTransfer.files.length > 0) {
    importFile.value = e.dataTransfer.files[0];
  }
};

// Function to handle file input change
const onFileInputChange = (e) => {
  if (e.target.files.length > 0) {
    importFile.value = e.target.files[0];
  }
};

// Function to trigger file input click
const triggerFileInput = () => {
  if (importFileInput.value) {
    importFileInput.value.click();
  }
};

// Function to submit import form
const submitImportForm = async (e) => {
  e.preventDefault();
  
  if (!importFile.value) {
    importError.value = 'Silakan pilih file terlebih dahulu.';
    return;
  }
  
  importProcessing.value = true;
  importError.value = null;
  
  const formData = new FormData();
  formData.append('file', importFile.value);
  formData.append('document_form_id', props.documentForm.id);
  
  try {
    // Gunakan URL langsung alih-alih route
    const response = await axios.post('/admin/documents/import', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    
    if (response.data.success) {
      showImportModal.value = false;
      
      // Refresh halaman setelah impor berhasil
      router.reload();
    } else {
      importError.value = response.data.message || 'Terjadi kesalahan saat mengimpor dokumen.';
    }
  } catch (error) {
    console.error('Import error:', error);
    importError.value = error.response?.data?.message || 'Terjadi kesalahan saat mengimpor dokumen.';
  } finally {
    importProcessing.value = false;
  }
};

// Function to download template
const downloadTemplate = () => {
  // Gunakan URL langsung alih-alih route
  window.open('/admin/documents/template/download', '_blank');
};

// Function to format file size
const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes';
  
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
  
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

// Function to confirm bulk delete
const confirmBulkDelete = () => {
  if (selectedDocuments.value.length > 0) {
    showBulkDeleteModal.value = true;
  }
};

// Function to execute bulk delete
const executeBulkDelete = () => {
  if (selectedDocuments.value.length === 0) return;
  
  bulkDeleteForm.document_ids = selectedDocuments.value;
  
  bulkDeleteForm.delete(route('admin.documents.bulk-delete'), {
    preserveScroll: true,
    onSuccess: () => {
      showBulkDeleteModal.value = false;
      clearSelection();
    }
  });
};

// Membersihkan event listener saat komponen unmounted
onBeforeUnmount(() => {
  console.log('Event listener dibersihkan');
});
</script>

<style scoped>
.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-menu {
  position: absolute;
  right: 0;
  top: 100%;
  min-width: 200px;
  z-index: 9999;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  overflow: visible;
}

/* Untuk dropdown yang berada di bagian atas halaman (header) */
.header-dropdown .dropdown-menu {
  position: fixed !important; /* Gunakan posisi fixed */
  z-index: 99999;
  top: auto;
  right: auto; /* Jangan gunakan right otomatis */
  bottom: auto;
  transform: translateY(0);
  margin-top: 8px;
  margin-bottom: 0;
}

.header-dropdown .dropdown-menu:before {
  top: -8px;
  bottom: auto;
  transform: rotate(0);
  border-color: transparent transparent white transparent;
}

.dropdown-menu:before {
  content: "";
  position: absolute;
  bottom: -8px; /* Ubah dari top menjadi bottom */
  right: 9px;
  border-width: 8px 8px 0 8px; /* Ubah arah panah */
  border-style: solid;
  border-color: var(--tw-ring-color) transparent transparent transparent; /* Ubah warna panah */
  filter: drop-shadow(0 1px 0px rgba(0, 0, 0, 0.1));
}

/* Panah untuk dropdown yang muncul di atas tombol */
.header-dropdown .dropdown-menu:before {
  bottom: -8px;
  top: auto;
  transform: rotate(0);
  border-color: white transparent transparent transparent;
}
</style> 
