# Panduan Webhook Auto-Update

Dokumen ini berisi panduan untuk mengkonfigurasi auto-update dari GitHub ke server production menggunakan webhook.

## Gambaran Umum

Sistem auto-update ini terdiri dari dua komponen utama:

1. **webhook-auto-update.php** - Script PHP yang menerima webhook dari GitHub
2. **deploy-update-only.sh** - Script Bash yang melakukan update file dari GitHub

Dengan sistem ini, setiap kali ada push ke branch main di GitHub, server production akan otomatis mengambil perubahan terbaru dan menerapkannya.

## Keuntungan

1. **Otomatisasi** - Tidak perlu login ke server untuk update
2. **Efisiensi** - Hanya mengambil file yang berubah
3. **Keamanan** - Verifikasi signature untuk memastikan webhook berasal dari GitHub
4. **Logging** - Mencatat semua aktivitas update

## Persiapan

### 1. Upload Script ke Server

Upload kedua script berikut ke server production:
- `webhook-auto-update.php` - Letakkan di direktori yang bisa diakses dari web (misalnya di `/var/www/html/webhook.php`)
- `deploy-update-only.sh` - Letakkan di direktori root project

### 2. Konfigurasi Script

1. Edit `webhook-auto-update.php` dan sesuaikan konfigurasi:
   ```php
   $secret = 'GANTI_DENGAN_SECRET_WEBHOOK_ANDA'; // Secret untuk verifikasi
   $branch = 'main'; // Branch yang ingin di-update
   $logFile = __DIR__ . '/webhook.log'; // Path ke file log
   $deployScript = __DIR__ . '/deploy-update-only.sh'; // Path ke script deploy
   $autoUpdate = true; // Set false jika hanya ingin mencatat tanpa auto-update
   ```

2. Berikan permission eksekusi untuk script deploy:
   ```bash
   chmod +x deploy-update-only.sh
   ```

3. Pastikan user web server (misalnya `www-data`) memiliki akses ke repository Git:
   ```bash
   # Berikan akses ke direktori project
   chown -R www-data:www-data /path/to/project
   
   # Atau tambahkan user web server ke grup yang memiliki akses
   usermod -a -G git www-data
   ```

### 3. Konfigurasi GitHub Webhook

1. Buka repository GitHub Anda
2. Klik **Settings** > **Webhooks** > **Add webhook**
3. Isi form webhook:
   - **Payload URL**: URL ke script webhook (misalnya `https://domain-anda.com/webhook.php`)
   - **Content type**: `application/json`
   - **Secret**: Masukkan secret yang sama dengan di script PHP
   - **Which events would you like to trigger this webhook?**: Pilih "Just the push event"
   - **Active**: Centang untuk mengaktifkan webhook

## Cara Kerja

1. Ketika ada push ke branch main di GitHub, GitHub akan mengirim webhook ke server
2. Script `webhook-auto-update.php` menerima webhook dan memverifikasi signature
3. Jika valid, script akan menjalankan `deploy-update-only.sh` dengan mode `auto-update`
4. Script `deploy-update-only.sh` akan mengambil perubahan terbaru dan menerapkannya
5. Semua aktivitas dicatat di file log

## Monitoring dan Troubleshooting

### Memeriksa Log

Untuk memeriksa log webhook:
```bash
tail -f /path/to/webhook.log
```

### Pengujian Webhook

Untuk menguji webhook tanpa melakukan push ke GitHub:
1. Buka repository GitHub Anda
2. Klik **Settings** > **Webhooks** > klik webhook Anda
3. Scroll ke bawah dan klik **Recent Deliveries** untuk melihat riwayat webhook
4. Klik **Redeliver** untuk mengirim ulang webhook terakhir

### Troubleshooting

1. **Webhook tidak diterima**:
   - Periksa URL webhook di GitHub
   - Pastikan server dapat diakses dari internet
   - Periksa firewall server

2. **Signature tidak valid**:
   - Pastikan secret di GitHub dan di script PHP sama persis
   - Periksa Content-Type di webhook GitHub (harus `application/json`)

3. **Update gagal**:
   - Periksa log webhook
   - Periksa permission file dan direktori
   - Jalankan script deploy secara manual untuk melihat error

## Keamanan

1. **Gunakan HTTPS** - Pastikan URL webhook menggunakan HTTPS untuk enkripsi
2. **Secret yang Kuat** - Gunakan secret yang panjang dan kompleks
3. **Batasi Akses** - Batasi akses ke script webhook dengan .htaccess atau konfigurasi server
4. **Validasi IP** - Pertimbangkan untuk menambahkan validasi IP GitHub

## Pengembangan Lanjutan

Script webhook ini dapat dikembangkan lebih lanjut dengan:

1. **Notifikasi** - Kirim notifikasi ke Slack, Telegram, atau email setelah update
2. **Rollback Otomatis** - Tambahkan fitur rollback jika update gagal
3. **Dashboard** - Buat dashboard untuk melihat riwayat update
4. **Multi-Branch** - Dukung update dari multiple branch (misalnya staging dan production) 