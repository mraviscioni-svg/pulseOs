-- Permiso clientes

INSERT INTO permissions (slug, name, module) VALUES
('customers.manage', 'Gestionar clientes', 'customers')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r
JOIN permissions p ON p.slug = 'customers.manage'
WHERE r.slug IN ('owner', 'manager', 'seller');
