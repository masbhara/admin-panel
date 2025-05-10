# Dokumentasi Fitur Notifikasi WhatsApp

Fitur notifikasi WhatsApp memungkinkan sistem untuk mengirim pesan WhatsApp otomatis ke pengguna saat mereka mengunggah dokumen atau saat status dokumen mereka berubah.

## Cara Menggunakan

### 1. Instalasi

Untuk menginstal fitur notifikasi WhatsApp, jalankan perintah berikut:

```bash
php artisan whatsapp:setup
php artisan whatsapp:permissions
```

Perintah ini akan:
- Menjalankan migrasi untuk membuat tabel `whatsapp_notifications`
- Menambahkan kolom `whatsapp_number` dan `notification_sent` ke tabel `documents`
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

### 4. Cara Kerja Notifikasi

1. Saat pengunjung mengunggah dokumen dan memasukkan nomor WhatsApp, sistem akan otomatis mengirimkan notifikasi WhatsApp menggunakan template "Dokumen Diunggah"
2. Saat admin mengubah status dokumen menjadi "approved" atau "rejected", sistem akan mengirimkan notifikasi WhatsApp sesuai dengan template yang sesuai

## Troubleshooting

### Notifikasi Tidak Terkirim

1. Pastikan fitur notifikasi WhatsApp diaktifkan di halaman pengaturan
2. Pastikan API Key dan Webhook URL Dripsender sudah benar
3. Pastikan nomor WhatsApp yang dimasukkan valid (format: 08xxx atau 628xxx)
4. Periksa log aplikasi untuk melihat error yang mungkin terjadi

### Template Tidak Berfungsi

1. Pastikan template notifikasi aktif (status "Aktif")
2. Pastikan variabel yang digunakan dalam template sesuai dengan format yang didukung
3. Pastikan template notifikasi memiliki tipe event yang sesuai (document_uploaded, document_approved, document_rejected)

## API Dripsender

Dokumentasi API Dripsender dapat dilihat di [https://docs.dripsender.id/integrasi-api](https://docs.dripsender.id/integrasi-api)

### Endpoint yang Digunakan

1. `POST https://api.dripsender.id/send` - Untuk mengirim pesan WhatsApp
2. `GET https://api.dripsender.id/lists/` - Untuk mendapatkan daftar list
3. `GET https://api.dripsender.id/lists/:id` - Untuk mendapatkan kontak dari list tertentu