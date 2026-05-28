-- Login por usuario (no email)

ALTER TABLE users
    ADD COLUMN username VARCHAR(80) NULL AFTER name;

UPDATE users SET username = LOWER(SUBSTRING_INDEX(email, '@', 1)) WHERE username IS NULL OR username = '';

UPDATE users SET username = CONCAT('user_', id) WHERE username IS NULL OR username = '';

ALTER TABLE users
    MODIFY username VARCHAR(80) NOT NULL,
    ADD UNIQUE KEY uq_users_username (username);

ALTER TABLE platform_admins
    ADD COLUMN username VARCHAR(80) NULL AFTER name;

UPDATE platform_admins SET username = 'admin' WHERE username IS NULL OR username = '';

ALTER TABLE platform_admins
    MODIFY username VARCHAR(80) NOT NULL,
    ADD UNIQUE KEY uq_platform_admins_username (username);

-- Super admin: usuario admin / clave password
UPDATE platform_admins SET username = 'admin' WHERE email = 'admin@pulseos.com' OR id = 1;
