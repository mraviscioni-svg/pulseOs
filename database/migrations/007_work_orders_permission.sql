-- Permiso órdenes de trabajo

INSERT INTO permissions (slug, name, module) VALUES
('work_orders.manage', 'Gestionar órdenes de trabajo', 'work_orders')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r
JOIN permissions p ON p.slug = 'work_orders.manage'
WHERE r.slug IN ('owner', 'manager');

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r
JOIN permissions p ON p.slug = 'work_orders.manage'
WHERE r.slug IN ('seller', 'warehouse');
