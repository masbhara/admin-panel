#!/bin/bash

# Script untuk mengoptimalkan Redis di server production
# Untuk project Penulis

echo "===== Redis Optimization Script ====="
echo "Memulai optimasi Redis untuk project Penulis..."

# Backup konfigurasi Redis yang ada
echo "Membuat backup konfigurasi Redis..."
if [ -f "/etc/redis/redis.conf" ]; then
    sudo cp /etc/redis/redis.conf /etc/redis/redis.conf.backup
    echo "Backup berhasil dibuat di /etc/redis/redis.conf.backup"
else
    echo "File konfigurasi Redis tidak ditemukan di lokasi default."
    echo "Silakan sesuaikan path ke file konfigurasi Redis Anda."
    exit 1
fi

# Mengoptimalkan konfigurasi Redis
echo "Menerapkan konfigurasi optimal untuk Redis..."

# Konfigurasi dasar
sudo tee -a /etc/redis/redis.conf.optimized << EOF
# Redis Configuration for Penulis Production
# Optimized for Laravel application

# Network
bind 127.0.0.1
protected-mode yes
port 6379
tcp-backlog 511
timeout 0
tcp-keepalive 300

# General
daemonize yes
supervised systemd
pidfile /var/run/redis/redis-server.pid
loglevel notice
logfile /var/log/redis/redis-server.log

# Memory Management
maxmemory 512mb
maxmemory-policy allkeys-lru
maxmemory-samples 10

# Persistence
save 900 1
save 300 10
save 60 10000
stop-writes-on-bgsave-error yes
rdbcompression yes
rdbchecksum yes
dbfilename dump.rdb
dir /var/lib/redis

# Security
requirepass YOUR_STRONG_REDIS_PASSWORD

# Performance Tuning
databases 4
io-threads 4
io-threads-do-reads yes
lazyfree-lazy-eviction yes
lazyfree-lazy-expire yes
lazyfree-lazy-server-del yes
replica-lazy-flush yes
dynamic-hz yes
EOF

echo "File konfigurasi optimal telah dibuat di /etc/redis/redis.conf.optimized"
echo "Silakan tinjau file tersebut dan sesuaikan password Redis sebelum menerapkannya."
echo ""

# Petunjuk penerapan
echo "Untuk menerapkan konfigurasi baru, jalankan perintah berikut:"
echo "  1. sudo mv /etc/redis/redis.conf.optimized /etc/redis/redis.conf"
echo "  2. sudo systemctl restart redis-server"
echo ""

# Petunjuk optimasi Laravel
echo "===== Laravel Redis Optimization ====="
echo "Tambahkan konfigurasi berikut ke file .env production Anda:"
echo ""
echo "REDIS_CLIENT=phpredis"
echo "REDIS_DB=0"
echo "REDIS_CACHE_DB=1"
echo "REDIS_QUEUE_DB=2"
echo "REDIS_SESSION_DB=3"
echo "REDIS_CACHE_TTL=7200"
echo "REDIS_PREFIX=penulis_"
echo "REDIS_PERSISTENT=true"
echo "REDIS_READ_TIMEOUT=60"
echo ""

echo "Jalankan perintah berikut untuk mengoptimalkan Laravel:"
echo "  1. php artisan config:cache"
echo "  2. php artisan route:cache"
echo "  3. php artisan view:cache"
echo "  4. php artisan optimize"
echo ""

echo "===== Selesai =====" 