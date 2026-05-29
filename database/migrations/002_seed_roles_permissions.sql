-- Roles y permisos base PulseOS

INSERT INTO roles (slug, name, description) VALUES
('owner', 'Owner / Admin', 'Acceso total al negocio'),
('manager', 'Encargado', 'Gestión operativa'),
('seller', 'Vendedor', 'Ventas y POS'),
('warehouse', 'Depósito', 'Stock y compras'),
('accountant', 'Contador', 'Reportes y caja')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO permissions (slug, name, module) VALUES
('dashboard.view', 'Ver dashboard', 'dashboard'),
('products.manage', 'Gestionar productos', 'products'),
('stock.manage', 'Gestionar stock', 'stock'),
('pos.sell', 'Punto de venta', 'pos'),
('suppliers.manage', 'Gestionar proveedores', 'suppliers'),
('purchases.manage', 'Gestionar compras', 'purchases'),
('cash.manage', 'Gestionar caja', 'cash'),
('reports.view', 'Ver reportes', 'reports'),
('users.manage', 'Gestionar usuarios', 'users'),
('settings.manage', 'Configuración', 'settings'),
('work_orders.manage', 'Gestionar órdenes de trabajo', 'work_orders'),
('customers.manage', 'Gestionar clientes', 'customers')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Owner: todos los permisos
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p WHERE r.slug = 'owner';

-- Manager
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r
JOIN permissions p ON p.slug IN (
    'dashboard.view','products.manage','stock.manage','pos.sell',
    'suppliers.manage','purchases.manage','cash.manage','reports.view','work_orders.manage','customers.manage'
) WHERE r.slug = 'manager';

-- Vendedor
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r
JOIN permissions p ON p.slug IN ('dashboard.view','pos.sell','products.manage','customers.manage')
WHERE r.slug = 'seller';

-- Depósito
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r
JOIN permissions p ON p.slug IN (
    'dashboard.view','products.manage','stock.manage','suppliers.manage','purchases.manage'
) WHERE r.slug = 'warehouse';

-- Contador
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r
JOIN permissions p ON p.slug IN ('dashboard.view','reports.view','cash.manage')
WHERE r.slug = 'accountant';
