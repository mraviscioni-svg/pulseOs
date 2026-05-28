# Deploy PulseOS — GitHub y FTP Taller Boedo

## Arquitectura

```
GitHub (mraviscioni-svg/pulseOs)
├── rama pre-prod  ──push──►  FTP …/PulseOS-prep/
└── rama prod      ──push──►  FTP …/pulseOS/
```

El workflow ejecuta `composer install` antes de subir archivos (incluye `vendor/`).

## Secretos en GitHub

| Secreto | Valor |
|---------|--------|
| `FTP_SERVER` | Host FTP |
| `FTP_USERNAME` | Usuario FTP |
| `FTP_PASSWORD` | Contraseña FTP |
| `FTP_SERVER_DIR` | Carpeta base al conectar, con `/` final |

## Base de datos en el servidor

**No va en GitHub Actions** (MySQL del hosting suele ser solo `localhost`).

Por entorno (prep y prod), en el panel del hosting:

1. Crear base MySQL y usuario.
2. Importar:
   - `database/migrations/001_initial_schema.sql`
   - `database/migrations/002_seed_roles_permissions.sql`
3. Crear `.env` en la carpeta desplegada (`PulseOS-prep` o `pulseOS`):

```env
APP_NAME=PulseOS
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com/pulseOS

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=usuario_pulseos_prep
DB_USERNAME=usuario_mysql
DB_PASSWORD=***
DB_CHARSET=utf8mb4
```

Usá **bases distintas** para pre-prod y producción.

## Document root

Ideal: apuntar el dominio/subcarpeta a `public/`.

Si el hosting no permite cambiar el root, el `.htaccess` en la raíz del repo redirige a `public/`.

## Ramas

| Rama | Carpeta FTP |
|------|-------------|
| `prod` | `pulseOS` |
| `pre-prod` | `PulseOS-prep` |

## Qué no se sube

- `.env`
- `.git`, `.github`
- `README.md`, `DEPLOY.md` (documentación)

## Verificar

1. Actions en verde
2. `/register` o `/login` carga la UI
3. Crear empresa de prueba en pre-prod
