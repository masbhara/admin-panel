#!/bin/bash

# Script untuk deployment ke production dengan optimasi Redis
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

# Fungsi untuk optimasi Redis
optimize_redis() {
    log "Mengoptimasi Redis..."
    
    # Memastikan Redis menggunakan driver phpredis
    grep -q "REDIS_CLIENT=phpredis" .env || echo "REDIS_CLIENT=phpredis" >> .env
    
    # Mengatur database Redis
    grep -q "REDIS_DB=0" .env || echo "REDIS_DB=0" >> .env
    grep -q "REDIS_CACHE_DB=1" .env || echo "REDIS_CACHE_DB=1" >> .env
    grep -q "REDIS_QUEUE_DB=2" .env || echo "REDIS_QUEUE_DB=2" >> .env
    grep -q "REDIS_SESSION_DB=3" .env || echo "REDIS_SESSION_DB=3" >> .env
    
    # Mengoptimalkan TTL dan koneksi
    grep -q "REDIS_CACHE_TTL=" .env || echo "REDIS_CACHE_TTL=7200" >> .env
    grep -q "REDIS_PREFIX=" .env || echo "REDIS_PREFIX=penulis_" >> .env
    grep -q "REDIS_PERSISTENT=" .env || echo "REDIS_PERSISTENT=true" >> .env
    grep -q "REDIS_READ_TIMEOUT=" .env || echo "REDIS_READ_TIMEOUT=60" >> .env
    
    # Mengoptimalkan cache Laravel
    grep -q "CACHE_PREFIX=" .env || echo "CACHE_PREFIX=penulis_cache_" >> .env
    grep -q "CACHE_STORE=redis" .env || echo "CACHE_STORE=redis" >> .env
    
    # Restart Redis jika ada akses
    if command -v systemctl &> /dev/null && systemctl is-active --quiet redis-server; then
        log "Merestart Redis server..."
        sudo systemctl restart redis-server
    else
        log "Redis server tidak terdeteksi atau tidak dapat direstart secara otomatis."
    fi
    
    # Membersihkan cache Redis
    php artisan cache:clear
}

# Fungsi untuk setup storage
setup_storage() {
    log "Mengatur storage dan permissions..."
    php artisan storage:link
    chmod -R 775 storage bootstrap/cache
    chown -R $(whoami):www-data storage bootstrap/cache
}

# Fungsi utama deployment
deploy() {
    log "Memulai proses deployment production..."
    
    # Backup database
    log "Membuat backup database..."
    php artisan backup:run
    
    # Aktifkan maintenance mode
    toggle_maintenance on
    
    # Pull latest changes
    log "Mengambil perubahan terbaru dari repository..."
    git pull origin main
    
    # Clear all cache
    clear_cache
    
    # Update dependencies
    update_dependencies
    
    # Run migrations
    run_migrations
    
    # Setup storage
    setup_storage
    
    # Optimize Redis
    optimize_redis
    
    # Optimize cache
    optimize_cache
    
    # Restart queue workers
    log "Merestart queue workers..."
    php artisan queue:restart
    
    # Nonaktifkan maintenance mode
    toggle_maintenance off
    
    log "Deployment production selesai!"
}

# Main script execution
if [ "$1" == "--help" ] || [ "$1" == "-h" ]; then
    echo "Usage: $0 [--redis-only]"
    echo "  --redis-only    Hanya optimasi Redis tanpa full deployment"
    exit 0
elif [ "$1" == "--redis-only" ]; then
    log "Menjalankan optimasi Redis saja..."
    optimize_redis
    log "Optimasi Redis selesai!"
else
    deploy
fi 