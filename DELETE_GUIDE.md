# Panduan Implementasi Delete Data di API Penulis

## Pendahuluan
Dokumen ini berisi panduan lengkap untuk mengimplementasikan fungsi hapus data di aplikasi API Penulis. Panduan ini mencakup implementasi di sisi frontend (Vue.js) dan backend (Laravel).

## Implementasi Frontend (Vue.js)

### 1. Komponen Modal Konfirmasi
```vue
<!-- Delete Confirmation Modal -->
<Modal :show="deleteModal" @close="closeDeleteModal">
  <div class="p-6">
    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
      Konfirmasi Hapus Data
    </h2>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
      Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.
    </p>
    <!-- Error Message Display -->
    <div v-if="errorMessage" class="mt-4 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded relative">
      <span class="block sm:inline">{{ errorMessage }}</span>
    </div>
    <div class="mt-6 flex justify-end space-x-4">
      <SecondaryButton @click="closeDeleteModal">Batal</SecondaryButton>
      <DangerButton @click="deleteData" :class="{ 'opacity-25': processing }" :disabled="processing">
        {{ processing ? 'Menghapus...' : 'Hapus' }}
      </DangerButton>
    </div>
  </div>
</Modal>
```

### 2. State Management
```javascript
// Definisikan state yang diperlukan
const deleteModal = ref(false);
const itemToDelete = ref(null);
const processing = ref(false);
const errorMessage = ref('');
```

### 3. Method untuk Menampilkan Modal
```javascript
const confirmDelete = (item) => {
  itemToDelete.value = item;
  deleteModal.value = true;
};
```

### 4. Method untuk Menutup Modal
```javascript
const closeDeleteModal = () => {
  deleteModal.value = false;
  errorMessage.value = '';
  setTimeout(() => {
    itemToDelete.value = null;
  }, 200);
};
```

### 5. Method untuk Menghapus Data
```javascript
const deleteData = () => {
  if (!itemToDelete.value || !itemToDelete.value.id) {
    errorMessage.value = 'ID tidak valid';
    return;
  }

  processing.value = true;
  errorMessage.value = '';
  
  // Pastikan ID valid
  const itemId = parseInt(itemToDelete.value.id);
  if (isNaN(itemId) || itemId <= 0) {
    errorMessage.value = 'ID tidak valid';
    processing.value = false;
    return;
  }
  
  // Gunakan axios.post dengan method spoofing DELETE
  axios.post(route('admin.resource.destroy', itemId), {
    _method: 'DELETE'
  })
  .then(response => {
    if (response.data && response.data.success) {
      deleteModal.value = false;
      itemToDelete.value = null;
      window.location.reload();
    } else {
      errorMessage.value = response.data?.message || 'Terjadi kesalahan saat menghapus data.';
    }
  })
  .catch(error => {
    console.error('Delete error:', error);
    if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message;
    } else if (error.response?.status === 403) {
      errorMessage.value = 'Anda tidak memiliki izin untuk menghapus data ini.';
    } else if (error.response?.status === 404) {
      errorMessage.value = 'Data tidak ditemukan.';
    } else {
      errorMessage.value = 'Terjadi kesalahan saat menghapus data.';
    }
  })
  .finally(() => {
    processing.value = false;
  });
};
```

## Implementasi Backend (Laravel)

### 1. Controller Method
```php
public function destroy(Model $model)
{
    try {
        DB::beginTransaction();
        
        // Periksa relasi jika diperlukan
        if ($model->hasRelatedData()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak dapat dihapus karena masih memiliki data terkait.'
            ], 400);
        }
        
        // Log aktivitas sebelum menghapus
        activity()
            ->performedOn($model)
            ->causedBy(auth()->user())
            ->log('deleted data');
        
        // Hapus data
        $model->delete();
        
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus.'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error deleting data: ' . $e->getMessage(), [
            'user_id' => auth()->id(),
            'model_id' => $model->id,
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
        ], 500);
    }
}
```

### 2. Route Definition
```php
Route::delete('resource/{model}', [Controller::class, 'destroy'])
    ->name('resource.destroy')
    ->middleware(['auth', 'verified']);
```

## Catatan Penting

1. **CSRF Token**
   - Pastikan CSRF token tersedia di header request
   - Axios akan menangani ini secara otomatis jika sudah dikonfigurasi dengan benar

2. **Authorization**
   - Gunakan middleware untuk mengontrol akses
   - Implementasikan policy jika diperlukan
   - Periksa permission user sebelum menghapus data

3. **Validasi**
   - Selalu validasi ID data yang akan dihapus
   - Periksa relasi data sebelum menghapus
   - Gunakan soft deletes jika diperlukan

4. **Error Handling**
   - Selalu gunakan try-catch untuk menangani error
   - Berikan pesan error yang informatif
   - Log error untuk keperluan debugging

5. **Response Format**
   - Gunakan format response yang konsisten
   - Sertakan flag success/error
   - Sertakan pesan yang informatif
   - Gunakan HTTP status code yang sesuai

## Contoh Penggunaan

```vue
<template>
  <button @click="confirmDelete(item)" class="text-red-600 hover:text-red-900">
    Hapus
  </button>
  
  <!-- Include Delete Confirmation Modal -->
</template>

<script setup>
// Import dan setup seperti panduan di atas
</script>
```

## Troubleshooting

1. **Method Not Allowed (405)**
   - Pastikan route terdaftar dengan benar
   - Periksa method spoofing (`_method: 'DELETE'`)
   - Periksa middleware yang digunakan

2. **CSRF Token Mismatch**
   - Pastikan CSRF token ada di meta tag
   - Periksa konfigurasi Axios

3. **Unauthorized (403)**
   - Periksa middleware auth
   - Periksa permission user
   - Pastikan user sudah login

4. **Server Error (500)**
   - Periksa log Laravel untuk detail error
   - Pastikan semua relasi ditangani dengan benar
   - Periksa transaksi database 