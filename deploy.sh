#!/bin/bash

# Script untuk deployment dan auto-update aplikasi

# Fungsi untuk logging
log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

# Fungsi untuk maintenance mode
toggle_maintenance() {
    if [ "$1" == "on" ]; then
        php artisan down
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
    php artisan optimize
}

# Fungsi untuk clear cache
clear_cache() {
    log "Membersihkan cache..."
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan cache:clear
}

# Fungsi untuk update dependencies
update_dependencies() {
    log "Mengupdate dependencies..."
    composer install --no-dev --optimize-autoloader
    npm ci
    npm run build
}

# Fungsi untuk migrasi database
run_migrations() {
    log "Menjalankan migrasi database..."
    php artisan migrate --force
}

# Fungsi utama deployment
deploy() {
    log "Memulai proses deployment..."
    
    # Backup database
    php artisan backup:run

    # Aktifkan maintenance mode
    toggle_maintenance on
    
    # Pull latest changes
    git pull origin main
    
    # Clear all cache
    clear_cache
    
    # Update dependencies
    update_dependencies
    
    # Run migrations
    run_migrations
    
    # Optimize cache
    optimize_cache
    
    # Restart queue workers
    php artisan queue:restart
    
    # Nonaktifkan maintenance mode
    toggle_maintenance off
    
    log "Deployment selesai!"
}

# Fungsi untuk auto-update
auto_update() {
    log "Memeriksa updates..."
    
    # Fetch latest changes
    git fetch origin main
    
    # Check if there are updates
    if git diff HEAD origin/main --quiet; then
        log "Tidak ada update baru."
        return 0
    fi
    
    log "Update ditemukan, memulai deployment..."
    deploy
}

# Main script execution
case "$1" in
    "deploy")
        deploy
        ;;
    "auto-update")
        auto_update
        ;;
    *)
        echo "Usage: $0 {deploy|auto-update}"
        exit 1
        ;;
esac 