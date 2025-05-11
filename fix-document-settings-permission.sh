#!/bin/bash

# Script untuk memperbaiki permission document-settings
# Untuk project Penulis

echo "===== Fix Document Settings Permission ====="
echo "Memperbaiki permission untuk halaman document-settings..."

# Jalankan seeder permission
echo "Menjalankan seeder permission..."
php artisan db:seed --class=DocumentSettingsPermissionSeeder

# Clear cache
echo "Membersihkan cache..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan permission:cache-reset

# Rebuild cache
echo "Membangun ulang cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo "===== Selesai ====="
echo "Permission untuk document-settings telah diperbaiki."
echo "Super admin sekarang seharusnya bisa mengakses halaman document-settings." 