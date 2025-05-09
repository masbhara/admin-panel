# Struktur Aplikasi dan Konvensi Penamaan

## Struktur Direktori

### Direktori Controller

#### `/app/Http/Controllers`
- Base Controllers
- Terdapat Controllers utama yang diakses oleh pengguna umum
- Termasuk `UnifiedProfileController` yang menangani profil pengguna

#### `/app/Http/Controllers/Admin`
- Controllers untuk panel admin
- Diakses oleh pengguna dengan role 'admin'

#### `/app/Http/Controllers/Admin/Document`
- Controllers untuk mengelola dokumen pada panel admin
- Dibagi menjadi beberapa controller berdasarkan fungsionalitas:
  - `CrudController.php` - operasi CRUD
  - `ExportController.php` - ekspor data
  - `ImportController.php` - impor data

#### `/app/Http/Controllers/User`
- Controllers spesifik untuk panel dashboard pengguna
- Diakses oleh pengguna yang terautentikasi 

### Direktori View (Vue.js)

#### `/resources/js/Pages`
- Komponen Vue.js untuk halaman publik
- Termasuk direktori `Public/` yang berisi halaman untuk pengunjung umum

#### `/resources/js/Pages/Admin`
- Komponen Vue.js untuk panel admin
- Termasuk direktori berdasarkan fitur
  - `Documents/` - mengelola dokumen
  - `Users/` - mengelola pengguna
  - dll.

#### `/resources/js/Pages/User`
- Komponen Vue.js untuk panel dashboard pengguna
- Dashboard dan pengaturan pengguna

#### `/resources/js/Pages/Profile`
- Komponen Vue.js untuk halaman profil
- Mengikuti struktur CRUD dengan file `Index.vue`, `Show.vue`, `Edit.vue`, dan `Create.vue`

## Konvensi Penamaan

### Controller
- Penamaan singular, seperti `UserController`
- Namespace sesuai dengan struktur direktori, contoh: `App\Http\Controllers\Admin\Document\CrudController`
- Controller khusus dikategorikan di dalam subdirektori berdasarkan fungsionalitas, seperti `Admin/Document/`

### Route
- Route menggunakan kebab-case: `admin.users.update-status`
- Route untuk resource memiliki format `[prefix].[resource].[action]`
- Route dikelompokkan berdasarkan middleware dan prefix, seperti:
  - Admin: `/admin/users`
  - User Dashboard: `/dashboard`
  - Profile: `/profile`

### View (Vue.js)
- Penamaan file menggunakan PascalCase: `UserList.vue`
- Menggunakan konvensi CRUD untuk resource:
  - `Index.vue` - menampilkan daftar
  - `Show.vue` - menampilkan detail
  - `Create.vue` - membuat data baru
  - `Edit.vue` - mengedit data
- Semua view diorganisir dalam direktori berdasarkan pengguna dan fitur:
  - `/resources/js/Pages/Admin/` - untuk admin
  - `/resources/js/Pages/User/` - untuk user dashboard
  - `/resources/js/Pages/Profile/` - untuk halaman profil

## Media Handling

Pengelolaan media menggunakan spatie/laravel-medialibrary dengan konvensi:
- Avatar pengguna: koleksi `avatar`
- Setiap file dokumen disimpan di `public/documents`

## Standardisasi API Endpoint

Rute download dokumen distandarisasi menjadi dua rute utama:
- `/documents/{document}/preview` - preview dokumen
- `/documents/{document}/download` - download dokumen 