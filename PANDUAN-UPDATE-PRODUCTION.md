# Panduan Update Production dengan File Terbaru

Dokumen ini berisi panduan untuk melakukan update aplikasi di server production dengan hanya mengambil file terbaru dari GitHub.

## Keuntungan Menggunakan Script Update

Script `deploy-update-only.sh` memiliki beberapa keuntungan:

1. **Efisien** - Hanya mengambil file yang berubah dari repository
2. **Cerdas** - Menjalankan migrasi, seeder, dan update dependencies hanya jika diperlukan
3. **Aman** - Membuat backup database sebelum melakukan update
4. **Informatif** - Menampilkan file yang akan diupdate sebelum melakukan update
5. **Minimal Downtime** - Menggunakan maintenance mode hanya selama proses update

## Persiapan

1. Upload file `deploy-update-only.sh` ke server production
2. Berikan permission eksekusi:
   ```bash
   chmod +x deploy-update-only.sh
   ```
3. Pastikan repository Git sudah terkonfigurasi dengan benar di server production

## Cara Penggunaan

### 1. Melihat Preview Perubahan

Untuk melihat file apa saja yang akan diupdate tanpa melakukan update:

```bash
./deploy-update-only.sh preview
```

Output akan menampilkan daftar file yang berbeda antara versi lokal dan versi di GitHub:
- `A` - File baru yang ditambahkan
- `M` - File yang dimodifikasi
- `D` - File yang dihapus

### 2. Melakukan Update

Untuk melakukan update dengan mengambil file terbaru:

```bash
./deploy-update-only.sh update
```

Script akan:
1. Membuat backup database
2. Mengaktifkan maintenance mode
3. Mengambil perubahan terbaru dari GitHub
4. Menampilkan file yang akan diupdate dan meminta konfirmasi
5. Mengupdate dependencies jika composer.json atau package.json berubah
6. Menjalankan migrasi jika ada file migrasi baru
7. Menjalankan seeder jika ada file seeder yang berubah
8. Membersihkan dan membangun ulang cache
9. Mematikan maintenance mode

## Alur Kerja yang Direkomendasikan

1. **Development**:
   - Buat perubahan di environment development
   - Commit dan push ke GitHub

2. **Production**:
   - Jalankan `./deploy-update-only.sh preview` untuk melihat perubahan
   - Jalankan `./deploy-update-only.sh update` untuk menerapkan perubahan

## Kasus Khusus

### Update Permission Document Settings

Jika Anda mengalami masalah dengan permission document settings seperti yang dijelaskan di `PANDUAN-FIX-DOCUMENT-SETTINGS.md`, script update ini akan secara otomatis:

1. Mendeteksi perubahan pada file permission seeder
2. Menjalankan seeder yang diperlukan
3. Membersihkan cache permission

### Update Dependency

Jika Anda menambahkan package baru di composer.json atau package.json:

1. Script akan mendeteksi perubahan pada file tersebut
2. Secara otomatis menjalankan `composer install` atau `npm ci` dan `npm run build`

## Troubleshooting

### Jika Update Gagal

1. Periksa log error
2. Jalankan `git status` untuk melihat status repository
3. Coba jalankan langkah-langkah update secara manual:
   ```bash
   git fetch origin
   git diff --name-status HEAD origin/main
   git pull origin main
   composer install --no-dev --optimize-autoloader
   php artisan migrate --force
   php artisan db:seed --force
   php artisan cache:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan optimize
   ```

### Jika Maintenance Mode Tetap Aktif

Jika terjadi error dan aplikasi tetap dalam maintenance mode:

```bash
php artisan up
```

## Keamanan

1. Pastikan script ini hanya dapat diakses oleh pengguna yang berwenang
2. Pertimbangkan untuk menambahkan autentikasi tambahan sebelum menjalankan script
3. Selalu buat backup database sebelum melakukan update

## Pengembangan Lanjutan

Script ini dapat dikembangkan lebih lanjut dengan menambahkan:

1. Notifikasi email atau Slack setelah update berhasil/gagal
2. Logging yang lebih detail
3. Rollback otomatis jika update gagal
4. Integrasi dengan CI/CD pipeline 