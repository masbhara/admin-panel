#!/bin/bash

# Script untuk mengambil hanya file terbaru dari GitHub ke production
# Untuk project Penulis

# Fungsi untuk logging
log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

# Fungsi untuk maintenance mode
toggle_maintenance() {
    if [ "$1" == "on" ]; then
        php artisan down --render="maintenance" --retry=60
    else
        php artisan up
    fi
}

# Fungsi untuk optimasi cache
optimize_cache() {
    log "Mengoptimasi cache..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
    php artisan optimize
    
    # Pastikan direktori storage yang diperlukan ada
    php artisan storage:ensure-directories
}

# Fungsi untuk clear cache
clear_cache() {
    log "Membersihkan cache..."
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan cache:clear
    php artisan event:clear
}

# Fungsi untuk backup database
backup_database() {
    log "Membuat backup database..."
    php artisan backup:run --only-db
}

# Fungsi untuk menjalankan migrasi
run_migrations() {
    log "Menjalankan migrasi database..."
    php artisan migrate --force
}

# Fungsi untuk mengambil file terbaru
fetch_latest_changes() {
    log "Mengambil perubahan terbaru dari repository..."
    
    # Simpan hash commit sebelumnya
    PREV_COMMIT=$(git rev-parse HEAD)
    
    # Fetch perubahan terbaru
    git fetch origin
    
    # Tampilkan file yang berubah
    log "File yang akan diperbarui:"
    git diff --name-status $PREV_COMMIT origin/main
    
    # Konfirmasi update jika bukan auto-update
    if [ "$1" != "auto" ]; then
        read -p "Lanjutkan update? (y/n): " confirm
        if [[ $confirm != "y" && $confirm != "Y" ]]; then
            log "Update dibatalkan."
            exit 0
        fi
    fi
    
    # Pull perubahan terbaru
    git pull origin main
    
    # Tampilkan file yang telah diperbarui
    log "File yang telah diperbarui:"
    git diff --name-status $PREV_COMMIT HEAD
}

# Fungsi untuk update dependencies jika package.json atau composer.json berubah
update_dependencies_if_needed() {
    log "Memeriksa perubahan pada dependency..."
    
    # Cek apakah composer.json berubah
    if git diff --name-only $1 HEAD | grep -q "composer.json"; then
        log "composer.json berubah, mengupdate dependencies PHP..."
        composer install --no-dev --optimize-autoloader
    else
        log "composer.json tidak berubah, melewati update dependencies PHP."
    fi
    
    # Cek apakah package.json berubah
    if git diff --name-only $1 HEAD | grep -q "package.json"; then
        log "package.json berubah, mengupdate dependencies Node.js..."
        npm ci
        npm run build
    else
        log "package.json tidak berubah, melewati update dependencies Node.js."
    fi
}

# Fungsi untuk menjalankan seeder jika ada file seeder yang berubah
run_seeders_if_needed() {
    log "Memeriksa perubahan pada seeder..."
    
    # Cek apakah ada file seeder yang berubah
    if git diff --name-only $1 HEAD | grep -q "database/seeders/"; then
        log "File seeder berubah, menjalankan database seeder..."
        php artisan db:seed --force
    else
        log "Tidak ada file seeder yang berubah."
    fi
}

# Fungsi untuk membersihkan permission cache jika ada perubahan pada permission
reset_permission_cache_if_needed() {
    log "Memeriksa perubahan pada permission..."
    
    # Cek apakah ada file permission yang berubah
    if git diff --name-only $1 HEAD | grep -q "RolesAndPermissionsSeeder.php\|PermissionSeeder.php\|DocumentSettingsPermissionSeeder.php"; then
        log "File permission berubah, membersihkan cache permission..."
        php artisan permission:cache-reset
    else
        log "Tidak ada file permission yang berubah."
    fi
}

# Fungsi utama untuk update
update_production() {
    log "Memulai proses update production..."
    
    # Backup database
    backup_database
    
    # Simpan hash commit sebelumnya
    PREV_COMMIT=$(git rev-parse HEAD)
    
    # Aktifkan maintenance mode
    toggle_maintenance on
    
    # Ambil perubahan terbaru
    fetch_latest_changes $1
    
    # Update dependencies jika diperlukan
    update_dependencies_if_needed $PREV_COMMIT
    
    # Clear cache
    clear_cache
    
    # Jalankan migrasi jika ada
    if git diff --name-only $PREV_COMMIT HEAD | grep -q "database/migrations/"; then
        run_migrations
    else
        log "Tidak ada file migrasi yang berubah, melewati migrasi."
    fi
    
    # Jalankan seeder jika diperlukan
    run_seeders_if_needed $PREV_COMMIT
    
    # Reset permission cache jika diperlukan
    reset_permission_cache_if_needed $PREV_COMMIT
    
    # Optimize cache
    optimize_cache
    
    # Restart queue workers
    php artisan queue:restart
    
    # Nonaktifkan maintenance mode
    toggle_maintenance off
    
    log "Update production selesai!"
}

# Fungsi untuk melihat file yang akan diupdate tanpa melakukan update
preview_changes() {
    log "Preview perubahan dari repository..."
    
    # Fetch perubahan terbaru
    git fetch origin
    
    # Tampilkan file yang berubah
    log "File yang akan diperbarui:"
    git diff --name-status HEAD origin/main
}

# Main script execution
case "$1" in
    "preview")
        preview_changes
        ;;
    "update")
        update_production
        ;;
    "auto-update")
        update_production "auto"
        ;;
    *)
        echo "Usage: $0 {preview|update|auto-update}"
        echo "  preview     Tampilkan file yang akan diupdate tanpa melakukan update"
        echo "  update      Jalankan proses update production dengan konfirmasi"
        echo "  auto-update Jalankan proses update production tanpa konfirmasi (untuk webhook)"
        exit 1
        ;;
esac 