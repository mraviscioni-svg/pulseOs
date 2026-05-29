-- Usuario único por comercio (mismo username permitido en distintos tenants)

ALTER TABLE users DROP INDEX uq_users_username;

ALTER TABLE users ADD UNIQUE KEY uq_users_tenant_username (tenant_id, username);
