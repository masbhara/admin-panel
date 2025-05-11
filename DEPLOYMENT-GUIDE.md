# Panduan Deployment Penulis ke Production

Dokumen ini berisi panduan lengkap untuk melakukan deployment aplikasi Penulis ke environment production dengan optimasi Redis.

## Daftar Isi
- [Persiapan Server](#persiapan-server)
- [Konfigurasi Environment](#konfigurasi-environment)
- [Optimasi Redis](#optimasi-redis)
- [Proses Deployment](#proses-deployment)
- [Konfigurasi Supervisor](#konfigurasi-supervisor)
- [Pemeliharaan](#pemeliharaan)
- [Troubleshooting](#troubleshooting)

## Persiapan Server

### Kebutuhan Sistem
```bash
PHP >= 8.2
MySQL >= 8.0
Redis >= 6.2
Node.js >= 18.0
Nginx/Apache
Supervisor
```

### Ekstensi PHP yang Diperlukan
```bash
php-fpm
php-mysql
php-redis
php-curl
php-zip
php-gd
php-mbstring
php-xml
php-bcmath
```

### Instalasi Redis
```bash
sudo apt update
sudo apt install redis-server
```

## Konfigurasi Environment

### File .env Production

Buat file `.env` untuk production dengan konfigurasi berikut:

```env
# Application
APP_NAME=Penulis
APP_ENV=production
APP_KEY=base64:GANTI_DENGAN_APP_KEY_ANDA
APP_DEBUG=false
APP_URL=https://domain-anda.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=penulis_prod
DB_USERNAME=penulis_prod_user
DB_PASSWORD=password_database_yang_kuat

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
BROADCAST_DRIVER=redis
FILESYSTEM_DISK=public

# Redis Configuration (Optimized)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=password_redis_yang_kuat
REDIS_PORT=6379
REDIS_CLIENT=phpredis
REDIS_DB=0
REDIS_CACHE_DB=1
REDIS_QUEUE_DB=2
REDIS_SESSION_DB=3
REDIS_CACHE_TTL=7200
REDIS_PREFIX=penulis_
REDIS_PERSISTENT=true
REDIS_READ_TIMEOUT=60
REDIS_CACHE_LOCK_CONNECTION=default

# Cache Optimization
CACHE_PREFIX=penulis_cache_
CACHE_STORE=redis

# Media Library
MEDIA_DISK=public
MEDIA_PATH=media
MEDIA_CONVERSIONS_PATH=media-conversions
QUEUE_MEDIA_CONVERSIONS=true

# Security
SECURE_HEADERS_ENABLED=true
SECURE_HEADERS_STRICT_TRANSPORT=true
SECURE_HEADERS_CONTENT_SECURITY_POLICY="default-src 'self'"

# Backup System
BACKUP_ARCHIVE_PASSWORD=password_backup_yang_kuat
BACKUP_NOTIFICATION_EMAIL=admin@domain-anda.com
BACKUP_DISK=s3
BACKUP_DESTINATION_PATH=penulis-backups

# Performance
LOG_CHANNEL=stack
LOG_LEVEL=warning
OPCACHE_ENABLE=1
OPCACHE_MEMORY_CONSUMPTION=256
TELESCOPE_ENABLED=false

# Queue Optimization
REDIS_QUEUE=default
REDIS_QUEUE_RETRY_AFTER=90
QUEUE_FAILED_DRIVER=database
```

## Optimasi Redis

### Konfigurasi Redis Server

Edit file konfigurasi Redis di `/etc/redis/redis.conf` dengan pengaturan berikut:

```
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
requirepass password_redis_yang_kuat

# Performance Tuning
databases 4
io-threads 4
io-threads-do-reads yes
lazyfree-lazy-eviction yes
lazyfree-lazy-expire yes
lazyfree-lazy-server-del yes
replica-lazy-flush yes
dynamic-hz yes
```

Setelah mengubah konfigurasi, restart Redis:

```bash
sudo systemctl restart redis-server
```

### Optimasi Redis di Laravel

Pastikan pengaturan Redis di Laravel sudah optimal dengan menjalankan script `redis-optimize.sh` yang telah dibuat:

```bash
chmod +x redis-optimize.sh
./redis-optimize.sh
```

## Proses Deployment

Gunakan script `deploy-production.sh` yang telah dibuat untuk melakukan deployment:

```bash
chmod +x deploy-production.sh
./deploy-production.sh
```

Jika hanya ingin mengoptimalkan Redis tanpa melakukan full deployment:

```bash
./deploy-production.sh --redis-only
```

### Langkah-langkah Manual Deployment

1. **Clone Repository**
```bash
git clone https://github.com/your-repo/penulis.git
cd penulis
```

2. **Konfigurasi Environment**
```bash
cp .env.example .env
# Edit file .env sesuai panduan di atas
php artisan key:generate
```

3. **Install Dependencies**
```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
```

4. **Migrasi Database**
```bash
php artisan migrate --force
```

5. **Setup Storage**
```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

6. **Optimasi Cache**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan optimize
```

## Konfigurasi Supervisor

Buat file konfigurasi Supervisor untuk mengelola queue worker:

```bash
sudo nano /etc/supervisor/conf.d/penulis-worker.conf
```

Isi dengan konfigurasi berikut:

```ini
[program:penulis-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/penulis/artisan queue:work redis --sleep=3 --tries=3
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/penulis/storage/logs/worker.log
stopwaitsecs=3600
```

Kemudian reload Supervisor dan mulai worker:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start penulis-worker:*
```

## Pemeliharaan

### Cron Jobs

Tambahkan cron jobs berikut untuk pemeliharaan rutin:

```bash
# Edit crontab
crontab -e
```

```
# Laravel scheduler (setiap menit)
* * * * * cd /path/to/penulis && php artisan schedule:run >> /dev/null 2>&1

# Backup database (setiap hari jam 1 pagi)
0 1 * * * cd /path/to/penulis && php artisan backup:run

# Cleanup backup lama (setiap hari jam 2 pagi)
0 2 * * * cd /path/to/penulis && php artisan backup:clean

# Health check (setiap 5 menit)
*/5 * * * * curl -s https://domain-anda.com/health >> /var/log/penulis/health.log
```

### Monitoring Redis

Untuk memonitor penggunaan Redis:

```bash
redis-cli -a password_redis_yang_kuat
```

Perintah Redis yang berguna:
```
# Melihat statistik penggunaan memori
INFO memory

# Melihat statistik cache hits/misses
INFO stats

# Melihat daftar key dengan pattern tertentu
KEYS penulis_*

# Melihat database stats
INFO keyspace
```

## Troubleshooting

### Redis Connection Failed
```bash
# Check Redis service
sudo systemctl status redis-server

# Test koneksi
redis-cli ping
```

### Queue Not Processing
```bash
# Check supervisor status
sudo supervisorctl status

# Restart workers
sudo supervisorctl restart penulis-worker:*
```

### Permission Issues
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Cache Issues
```bash
# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

Untuk bantuan lebih lanjut, silakan merujuk ke dokumentasi resmi Laravel dan Redis. 