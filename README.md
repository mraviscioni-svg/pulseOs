# PulseOS

SaaS multitenant para gestión comercial: stock, ventas (POS), proveedores, compras y caja.

**Stack:** PHP 8.2+, MySQL/MariaDB, Tailwind CSS, Alpine.js, Composer.

## Requisitos

- PHP 8.1+ con extensiones `pdo_mysql`, `mbstring`, `json`
- MySQL 8+ o MariaDB 10.4+
- Apache con `mod_rewrite` (o Nginx equivalente)
- Composer (local o en CI)

## Instalación local

```powershell
cd "c:\Users\marcelo.raviscioni\Desktop\pulseOs"
composer install
copy .env.example .env
```

1. Crear base de datos `pulseos` en MySQL.
2. Editar `.env` con credenciales DB.
3. Importar migraciones en orden:
   - `database/migrations/001_initial_schema.sql`
   - `database/migrations/002_seed_roles_permissions.sql`
4. Servir la carpeta `public/`:

```powershell
php -S localhost:8080 -t public
```

5. Abrir http://localhost:8080/register y crear tu empresa.

## Estructura

```
app/           Controladores, modelos, servicios, vistas, middleware
config/        App, DB, rubros de negocio
database/      Migraciones SQL
public/        Document root (index.php, assets)
routes/        Rutas web y API interna
```

## MVP incluido

| Módulo | Estado |
|--------|--------|
| Registro de empresa (tenant) | ✓ |
| Login / logout / roles | ✓ |
| Dashboard | ✓ |
| Productos + stock | ✓ |
| POS + código de barras | ✓ |
| Caja | ✓ |
| Proveedores y compras | ✓ |
| Usuarios | ✓ |
| API interna (`/api/products/*`) | ✓ |

## Multitenancy

Cada tabla de negocio lleva `tenant_id`. La sesión fija el tenant; los modelos filtran por tenant. Ningún usuario accede a datos de otro negocio.

## Deploy (FTP)

Ver [DEPLOY.md](DEPLOY.md). En el servidor:

1. Subir código (GitHub Actions incluye `vendor/` vía `composer install` en CI).
2. Crear `.env` en la carpeta del sitio (no se sube por Git).
3. Importar SQL en la base del hosting.
4. Document root → carpeta `public/` (o usar `.htaccess` en la raíz del proyecto).

## Próximos pasos (arquitectura preparada)

- Facturación electrónica
- Mercado Pago
- Reportes avanzados
- Multi-sucursal
- API pública / app mobile
