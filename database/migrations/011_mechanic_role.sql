-- Rol Mecánico (taller mecánico): órdenes de trabajo, clientes y consulta de repuestos

INSERT INTO roles (slug, name, description) VALUES
('mechanic', 'Mecánico', 'Órdenes de trabajo, clientes/vehículos y repuestos del taller')
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    description = VALUES(description);

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r
JOIN permissions p ON p.slug IN (
    'dashboard.view',
    'work_orders.manage',
    'customers.manage',
    'products.manage'
) WHERE r.slug = 'mechanic';
