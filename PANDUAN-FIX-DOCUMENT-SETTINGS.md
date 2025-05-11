# Panduan Memperbaiki Akses Document Settings

Dokumen ini berisi langkah-langkah untuk memperbaiki masalah akses ke halaman document-settings di aplikasi Penulis.

## Masalah

Pengguna dengan role super admin tidak dapat mengakses halaman `/admin/document-settings` dan mendapatkan pesan error:

```
User does not have the right permissions.
```

## Penyebab

Masalah ini terjadi karena route document-settings menggunakan middleware permission `manage document settings`, tetapi permission tersebut belum diberikan ke role super admin.

## Solusi

Ada beberapa cara untuk memperbaiki masalah ini:

### Solusi 1: Menggunakan Seeder (Rekomendasi)

1. Jalankan seeder yang telah dibuat untuk menambahkan permission document-settings:

```bash
# Pastikan berada di root folder project
chmod +x fix-document-settings-permission.sh
./fix-document-settings-permission.sh
```

Script ini akan:
- Menambahkan permission document-settings ke database
- Memberikan permission tersebut ke role super admin
- Membersihkan cache permission
- Membangun ulang cache Laravel

### Solusi 2: Menggunakan SQL Manual

Jika Anda lebih suka menggunakan SQL langsung:

1. Masuk ke MySQL:
```bash
mysql -u username -p database_name
```

2. Jalankan script SQL yang telah dibuat:
```sql
source fix-document-settings-permission.sql
```

Atau Anda bisa menggunakan phpMyAdmin atau tool database lainnya untuk mengeksekusi SQL tersebut.

### Solusi 3: Menggunakan Artisan Tinker

1. Jalankan Artisan Tinker:
```bash
php artisan tinker
```

2. Tambahkan permission dan berikan ke role super admin:
```php
// Tambahkan permission
$permissions = [
    'view document settings',
    'create document settings',
    'edit document settings',
    'delete document settings',
    'manage document settings',
];

foreach ($permissions as $permission) {
    Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
}

// Cari role super admin
$role = Spatie\Permission\Models\Role::where('name', 'super admin')
    ->orWhere('name', 'Super Admin')
    ->orWhere('name', 'superadmin')
    ->orWhere('name', 'admin')
    ->first();

// Berikan permission ke role
$role->givePermissionTo($permissions);

// Hapus cache permission
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
```

## Verifikasi

Setelah menjalankan salah satu solusi di atas:

1. Logout dari aplikasi
2. Login kembali sebagai super admin
3. Coba akses halaman `/admin/document-settings`

Anda seharusnya sekarang bisa mengakses halaman document-settings tanpa mendapatkan pesan error.

## Pencegahan Masalah di Masa Depan

Untuk mencegah masalah serupa di masa depan, pastikan:

1. Setiap kali menambahkan route baru dengan middleware permission, tambahkan juga permission tersebut ke seeder `RolesAndPermissionsSeeder.php`
2. Jalankan `php artisan db:seed --class=RolesAndPermissionsSeeder` setelah deploy ke production
3. Jika menambahkan permission baru, pastikan untuk memberikannya ke role yang sesuai

## Troubleshooting

Jika masalah masih terjadi setelah menjalankan solusi di atas:

1. Pastikan cache permission sudah dibersihkan:
```bash
php artisan permission:cache-reset
```

2. Bersihkan cache Laravel:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

3. Rebuild cache Laravel:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

4. Restart web server:
```bash
sudo systemctl restart nginx
sudo systemctl restart php-fpm
```

5. Periksa permission di database:
```sql
SELECT * FROM permissions WHERE name LIKE '%document settings%';
SELECT p.name FROM permissions p
JOIN role_has_permissions rhp ON p.id = rhp.permission_id
JOIN roles r ON rhp.role_id = r.id
WHERE r.name IN ('super admin', 'Super Admin', 'superadmin', 'admin');
``` 