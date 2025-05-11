-- Script SQL untuk memperbaiki permission document settings
-- Untuk project Penulis

-- Tambahkan permission document settings jika belum ada
INSERT INTO permissions (name, guard_name, created_at, updated_at)
SELECT 'view document settings', 'web', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE name = 'view document settings');

INSERT INTO permissions (name, guard_name, created_at, updated_at)
SELECT 'create document settings', 'web', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE name = 'create document settings');

INSERT INTO permissions (name, guard_name, created_at, updated_at)
SELECT 'edit document settings', 'web', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE name = 'edit document settings');

INSERT INTO permissions (name, guard_name, created_at, updated_at)
SELECT 'delete document settings', 'web', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE name = 'delete document settings');

INSERT INTO permissions (name, guard_name, created_at, updated_at)
SELECT 'manage document settings', 'web', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE name = 'manage document settings');

-- Dapatkan ID role super admin atau admin
SET @role_id = (
    SELECT id FROM roles 
    WHERE name IN ('super admin', 'Super Admin', 'superadmin', 'admin') 
    ORDER BY CASE 
        WHEN name = 'super admin' THEN 1
        WHEN name = 'Super Admin' THEN 2
        WHEN name = 'superadmin' THEN 3
        WHEN name = 'admin' THEN 4
    END
    LIMIT 1
);

-- Dapatkan ID permission
SET @view_id = (SELECT id FROM permissions WHERE name = 'view document settings');
SET @create_id = (SELECT id FROM permissions WHERE name = 'create document settings');
SET @edit_id = (SELECT id FROM permissions WHERE name = 'edit document settings');
SET @delete_id = (SELECT id FROM permissions WHERE name = 'delete document settings');
SET @manage_id = (SELECT id FROM permissions WHERE name = 'manage document settings');

-- Berikan permission ke role super admin
INSERT INTO role_has_permissions (permission_id, role_id)
SELECT @view_id, @role_id
WHERE NOT EXISTS (SELECT 1 FROM role_has_permissions WHERE permission_id = @view_id AND role_id = @role_id);

INSERT INTO role_has_permissions (permission_id, role_id)
SELECT @create_id, @role_id
WHERE NOT EXISTS (SELECT 1 FROM role_has_permissions WHERE permission_id = @create_id AND role_id = @role_id);

INSERT INTO role_has_permissions (permission_id, role_id)
SELECT @edit_id, @role_id
WHERE NOT EXISTS (SELECT 1 FROM role_has_permissions WHERE permission_id = @edit_id AND role_id = @role_id);

INSERT INTO role_has_permissions (permission_id, role_id)
SELECT @delete_id, @role_id
WHERE NOT EXISTS (SELECT 1 FROM role_has_permissions WHERE permission_id = @delete_id AND role_id = @role_id);

INSERT INTO role_has_permissions (permission_id, role_id)
SELECT @manage_id, @role_id
WHERE NOT EXISTS (SELECT 1 FROM role_has_permissions WHERE permission_id = @manage_id AND role_id = @role_id);

-- Hapus cache permission
DELETE FROM cache WHERE key LIKE '%spatie.permission.cache%'; 