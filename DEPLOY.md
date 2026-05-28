# Deploy PulseOS — GitHub y FTP Taller Boedo

## Arquitectura

```
GitHub (mraviscioni-svg/pulseOs)
├── rama pre-prod  ──push──►  FTP …/PulseOS-prep/
└── rama prod      ──push──►  FTP …/pulseOS/
```

El workflow ejecuta `composer install` antes de subir archivos (incluye `vendor/`).

## Secretos en GitHub

| Secreto | Uso |
|---------|-----|
| `FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD`, `FTP_SERVER_DIR` | Deploy FTP |
| `PROD_DB_HOST`, `PROD_DB_NAME`, `PROD_DB_USER`, `PROD_DB_PASSWORD` | MySQL (misma base en prod y prep) → `.env` en cada deploy |

Detalle: `docs/GITHUB_SECRETS.md`

## Base de datos en el servidor

Prod y pre-prod comparten los mismos secrets; el workflow genera `.env` en ambos (misma base MySQL).

En el panel del hosting (una sola base si querés datos compartidos entre prep y prod):

1. Crear base MySQL y usuario.
2. Importar:
   - `database/migrations/001_initial_schema.sql`
   - `database/migrations/002_seed_roles_permissions.sql`
3. Crear `.env` en la carpeta desplegada (`PulseOS-prep` o `pulseOS`):

```env
APP_NAME=PulseOS
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tallerboedo.com.ar/pulseOS/public

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=usuario_pulseos_prep
DB_USERNAME=usuario_mysql
DB_PASSWORD=***
DB_CHARSET=utf8mb4
```

Opcional: bases distintas en el panel; el deploy igual usa los mismos `PROD_DB_*` hasta que agregues otros secrets.

## Document root

Ideal: apuntar el dominio/subcarpeta a `public/`.

Si el hosting no permite cambiar el root, el `.htaccess` en la raíz del repo redirige a `public/`.

## Ramas

| Rama | Carpeta FTP |
|------|-------------|
| `prod` | `pulseOS` |
| `pre-prod` | `PulseOS-prep` |

## Qué no se sube

- `.env.example` (sí se genera y sube `.env` en deploy si hay secrets DB)
- `.git`, `.github`
- `README.md`, `DEPLOY.md` (documentación)

## Verificar

1. Actions en verde
2. `/register` o `/login` carga la UI
3. Crear empresa de prueba en pre-prod
