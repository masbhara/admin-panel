#!/bin/bash

# Script untuk menerapkan konfigurasi production ke file .env
# Untuk project Penulis

echo "===== Setup Production Environment ====="
echo "Menerapkan konfigurasi production ke file .env..."

# Lokasi file
ENV_PROD="env.production"
ENV_FILE=".env"

# Memeriksa keberadaan file env.production
if [ ! -f "$ENV_PROD" ]; then
    echo "Error: File $ENV_PROD tidak ditemukan!"
    exit 1
fi

# Backup file .env yang ada (jika ada)
if [ -f "$ENV_FILE" ]; then
    echo "Membuat backup file .env yang ada..."
    cp "$ENV_FILE" "${ENV_FILE}.backup.$(date +%Y%m%d%H%M%S)"
    echo "Backup berhasil dibuat."
fi

# Menyalin file env.production ke .env
echo "Menyalin konfigurasi production ke file .env..."
cp "$ENV_PROD" "$ENV_FILE"

# Generate app key jika belum ada
if grep -q "APP_KEY=base64:GANTI_DENGAN_APP_KEY_ANDA" "$ENV_FILE"; then
    echo "Generating new APP_KEY..."
    php artisan key:generate
fi

# Mengatur permission file .env
echo "Mengatur permission file .env..."
chmod 600 "$ENV_FILE"

echo "Konfigurasi production berhasil diterapkan ke file .env!"
echo ""

# Optimasi cache
echo "Mengoptimasi cache Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo "===== Setup Selesai ====="
echo "Aplikasi Penulis siap dijalankan dalam mode production!"
echo "Pastikan untuk menyesuaikan nilai-nilai berikut di file .env:"
echo "- APP_URL"
echo "- DB_DATABASE, DB_USERNAME, DB_PASSWORD"
echo "- REDIS_PASSWORD"
echo "- MAIL_* (konfigurasi email)"
echo "- SESSION_DOMAIN" 