<?php

/**
 * Script untuk mengoptimalkan cache Redis pada aplikasi Laravel
 * Untuk project Penulis
 */

// Pastikan script dijalankan dari CLI
if (php_sapi_name() !== 'cli') {
    exit('Script ini hanya bisa dijalankan dari command line.');
}

// Memuat autoloader Laravel
require __DIR__ . '/vendor/autoload.php';

// Memuat environment variables
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Mendapatkan instance Redis
$redis = app('redis');

echo "===== Redis Cache Optimization Script =====\n";
echo "Memulai optimasi cache Redis untuk project Penulis...\n\n";

// Fungsi untuk format ukuran
function formatSize($bytes)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    while ($bytes > 1024) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

// Memeriksa konfigurasi Redis
echo "Memeriksa konfigurasi Redis...\n";
echo "Driver: " . config('database.redis.client') . "\n";
echo "Host: " . config('database.redis.default.host') . "\n";
echo "Port: " . config('database.redis.default.port') . "\n";
echo "Database Default: " . config('database.redis.default.database') . "\n";
echo "Database Cache: " . config('database.redis.cache.database') . "\n\n";

// Memeriksa konfigurasi cache
echo "Memeriksa konfigurasi cache...\n";
echo "Default Store: " . config('cache.default') . "\n";
echo "Cache TTL: " . config('cache.ttl', 'Not set') . "\n";
echo "Cache Prefix: " . config('cache.prefix') . "\n\n";

// Mengambil statistik Redis
try {
    echo "Mengambil statistik Redis...\n";
    $info = $redis->info();
    
    echo "Versi Redis: " . $info['redis_version'] . "\n";
    echo "Uptime: " . $info['uptime_in_days'] . " hari\n";
    echo "Memory Used: " . formatSize($info['used_memory']) . "\n";
    echo "Peak Memory: " . formatSize($info['used_memory_peak']) . "\n";
    echo "Connected Clients: " . $info['connected_clients'] . "\n";
    echo "Total Commands: " . $info['total_commands_processed'] . "\n";
    echo "Hit Rate: " . ($info['keyspace_hits'] / ($info['keyspace_hits'] + $info['keyspace_misses'] + 0.0001) * 100) . "%\n\n";
    
    // Memeriksa database keys
    if (isset($info['db0'])) {
        echo "Database 0: " . $info['db0'] . "\n";
    }
    if (isset($info['db1'])) {
        echo "Database 1 (Cache): " . $info['db1'] . "\n";
    }
    if (isset($info['db2'])) {
        echo "Database 2 (Queue): " . $info['db2'] . "\n";
    }
    if (isset($info['db3'])) {
        echo "Database 3 (Session): " . $info['db3'] . "\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "Error mengambil statistik Redis: " . $e->getMessage() . "\n\n";
}

// Optimasi Redis untuk Laravel
echo "Mengoptimalkan Redis untuk Laravel...\n";

// 1. Mengatur prefix yang konsisten
$prefix = config('app.name', 'penulis') . '_';
$cachePrefix = $prefix . 'cache_';

echo "Mengatur prefix Redis: " . $prefix . "\n";
echo "Mengatur prefix Cache: " . $cachePrefix . "\n";

// 2. Mengatur TTL cache yang optimal (2 jam)
$cacheTTL = 7200;
echo "Mengatur Cache TTL: " . $cacheTTL . " detik\n";

// 3. Membersihkan cache yang tidak perlu
echo "Membersihkan cache yang tidak perlu...\n";

// 4. Mengatur persistent connection
echo "Mengaktifkan persistent connection\n";

// 5. Mengatur cache tags untuk fitur utama
echo "Mengatur cache tags untuk fitur utama...\n";
try {
    // Cache tags untuk user
    \Illuminate\Support\Facades\Cache::tags(['users'])->put('user_roles', \App\Models\User::count(), $cacheTTL);
    echo "- Cache tag 'users' berhasil dibuat\n";
    
    // Cache tags untuk media
    \Illuminate\Support\Facades\Cache::tags(['media'])->put('media_count', \Spatie\MediaLibrary\MediaCollections\Models\Media::count(), $cacheTTL);
    echo "- Cache tag 'media' berhasil dibuat\n";
    
    // Cache version key
    \Illuminate\Support\Facades\Cache::put('cache_version', now()->timestamp, 86400);
    echo "- Cache version key berhasil dibuat\n";
} catch (\Exception $e) {
    echo "Error saat membuat cache tags: " . $e->getMessage() . "\n";
}

// 6. Mengupdate .env file
echo "\nMengupdate file .env dengan konfigurasi optimal...\n";
try {
    $envFile = file_get_contents(__DIR__ . '/.env');
    
    // Fungsi untuk mengupdate atau menambahkan variabel di .env
    function updateEnvVariable($envFile, $key, $value) {
        if (preg_match("/^$key=.*/m", $envFile)) {
            return preg_replace("/^$key=.*/m", "$key=$value", $envFile);
        } else {
            return $envFile . "\n$key=$value";
        }
    }
    
    $envFile = updateEnvVariable($envFile, 'REDIS_CLIENT', 'phpredis');
    $envFile = updateEnvVariable($envFile, 'REDIS_DB', '0');
    $envFile = updateEnvVariable($envFile, 'REDIS_CACHE_DB', '1');
    $envFile = updateEnvVariable($envFile, 'REDIS_QUEUE_DB', '2');
    $envFile = updateEnvVariable($envFile, 'REDIS_SESSION_DB', '3');
    $envFile = updateEnvVariable($envFile, 'REDIS_CACHE_TTL', $cacheTTL);
    $envFile = updateEnvVariable($envFile, 'REDIS_PREFIX', $prefix);
    $envFile = updateEnvVariable($envFile, 'REDIS_PERSISTENT', 'true');
    $envFile = updateEnvVariable($envFile, 'REDIS_READ_TIMEOUT', '60');
    $envFile = updateEnvVariable($envFile, 'CACHE_PREFIX', $cachePrefix);
    $envFile = updateEnvVariable($envFile, 'CACHE_STORE', 'redis');
    
    file_put_contents(__DIR__ . '/.env', $envFile);
    echo "File .env berhasil diupdate\n";
} catch (\Exception $e) {
    echo "Error saat mengupdate file .env: " . $e->getMessage() . "\n";
}

// 7. Membersihkan dan rebuild cache
echo "\nMembersihkan dan membangun ulang cache Laravel...\n";
$commands = [
    'cache:clear',
    'config:clear',
    'route:clear',
    'view:clear',
    'config:cache',
    'route:cache',
    'view:cache',
    'optimize'
];

foreach ($commands as $command) {
    echo "Menjalankan php artisan $command...\n";
    $exitCode = $kernel->call($command);
    echo "Exit code: $exitCode\n";
}

echo "\n===== Optimasi Redis Selesai =====\n";
echo "Redis cache telah dioptimalkan untuk performa maksimal.\n";
echo "Untuk memantau performa Redis, gunakan perintah 'redis-cli' dan jalankan 'INFO'.\n";