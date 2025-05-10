# Update Fitur Notifikasi WhatsApp

Fitur notifikasi WhatsApp telah diperbaiki untuk bekerja dengan Laravel 12. Berikut adalah perubahan yang dilakukan:

## Perubahan Utama

1. **WhatsappNotificationController**
   - Menghapus penggunaan `$this->middleware()` yang sudah tidak didukung di Laravel 12
   - Memindahkan definisi middleware ke routes

2. **Routes**
   - Menambahkan middleware permission langsung pada route definition
   - Memperbaiki urutan route (path statis didefinisikan sebelum route dengan parameter)
   - Mengelompokkan route berdasarkan permission

## Cara Menggunakan

### 1. Instalasi

Untuk menginstal fitur notifikasi WhatsApp, jalankan perintah berikut:

```bash
php artisan whatsapp:setup
php artisan whatsapp:permissions
```

Perintah ini akan:
- Menjalankan migrasi untuk tabel `whatsapp_notifications` jika belum ada
- Menambahkan kolom `whatsapp_number` dan `notification_sent` ke tabel `documents` jika belum ada
- Menambahkan pengaturan Dripsender ke tabel `settings`
- Menambahkan template notifikasi WhatsApp default
- Menambahkan permission untuk mengelola notifikasi WhatsApp

### 2. Konfigurasi Dripsender

1. Buka halaman admin di `/admin/whatsapp-notifications/settings`
2. Masukkan API Key dan Webhook URL dari akun Dripsender Anda
3. Aktifkan fitur notifikasi WhatsApp dengan mencentang checkbox "Aktifkan notifikasi WhatsApp"
4. Klik tombol "Test Koneksi" untuk memastikan koneksi ke Dripsender berfungsi dengan baik
5. Simpan pengaturan

### 3. Mengelola Template Notifikasi

1. Buka halaman admin di `/admin/whatsapp-notifications`
2. Anda dapat melihat, menambah, mengedit, dan menghapus template notifikasi WhatsApp
3. Template notifikasi mendukung variabel berikut:
   - `{{name}}` - Nama pengguna/pengunjung
   - `{{file_name}}` - Nama file dokumen
   - `{{status}}` - Status dokumen (pending/approved/rejected)
   - `{{uploaded_at}}` - Tanggal dan waktu unggah

### 4. Alur Kerja Notifikasi

1. Saat pengunjung mengunggah dokumen dan memasukkan nomor WhatsApp, sistem akan otomatis mengirimkan notifikasi WhatsApp menggunakan template "Dokumen Diunggah"
2. Saat admin mengubah status dokumen menjadi "approved" atau "rejected", sistem akan mengirimkan notifikasi WhatsApp sesuai dengan template yang sesuai

## Troubleshooting

### Middleware Error

Jika Anda melihat error `Call to undefined method App\Http\Controllers\Admin\WhatsappNotificationController::middleware()`, pastikan Anda telah menjalankan update ini dan menggunakan definisi middleware pada route.

### Permissions Error

Jika Anda mengalami masalah akses, pastikan:
1. Anda telah menjalankan `php artisan whatsapp:permissions`
2. User Anda memiliki role admin atau telah diberikan permission yang sesuai
3. Cek permission user dengan melakukan debugging di `/debug-permissions`

### API Dripsender Error

Jika notifikasi tidak terkirim, periksa:
1. API Key dan Webhook URL sudah benar
2. Format nomor WhatsApp valid (format: 08xxx atau 628xxx)
3. Cek log Laravel untuk error spesifik
