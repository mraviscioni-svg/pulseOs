# PulseOS — Dejar PREP funcionando

## Cómo trabaja el agente (Cursor) con FTP/MySQL

Los **GitHub Secrets no son visibles** para el agente en tu PC. Para que pueda probar conexiones y ayudarte en caliente:

1. Copiá `.env.example` → **`.env`** (solo en tu máquina, nunca en Git)
2. Pegá ahí los mismos datos que en GitHub / el hosting
3. Ejecutá: `php scripts/verify-connections.php`

Lista de secretos y nombres correctos: **[docs/GITHUB_SECRETS.md](docs/GITHUB_SECRETS.md)**

En GitHub: Actions → **Verify connections** → Run workflow (prueba FTP/MySQL sin mostrar claves).

---

## Checklist PREP (orden)

### 1. Código en el servidor (automático)

- Push a rama `pre-prod` → Action **Deploy to FTP** → carpeta `PulseOS-prep/`
- Verificar: https://github.com/mraviscioni-svg/pulseOs/actions (run en verde)

### 2. Base de datos (vos en phpMyAdmin)

Base dedicada a prep. Importar **en este orden**:

| # | Archivo |
|---|---------|
| 1 | `database/migrations/001_initial_schema.sql` |
| 2 | `database/migrations/002_seed_roles_permissions.sql` |
| 3 | `database/migrations/003_platform_admins.sql` |
| 4 | `database/migrations/004_username_login.sql` |

### 3. Archivo `.env` en el FTP

En la carpeta desplegada (`PulseOS-prep/`), crear `.env` (no va por Git):

```env
APP_NAME=PulseOS
APP_ENV=production
APP_DEBUG=false
APP_URL=https://TU-DOMINIO/ruta-a-PulseOS-prep

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nombre_base_prep
DB_USERNAME=usuario_mysql
DB_PASSWORD=clave_mysql
DB_CHARSET=utf8mb4

MAIL_FROM=noreply@tudominio.com
```

### 4. Document root

El hosting debe servir la carpeta **`public/`**  
(o el `.htaccess` de la raíz del proyecto que redirige a `public/`).

### 5. Probar

| Acceso | URL | Usuario | Clave |
|--------|-----|---------|-------|
| Comercio | `/login` | el que creaste en `/register` | tu clave |
| Plataforma | `/admin/login` | `admin` | `password` |

---

## Si querés darme acceso (opcional, no por chat)

| Servicio | Dónde configurarlo | Qué secretos |
|----------|-------------------|--------------|
| FTP deploy | GitHub → Secrets | `FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD`, `FTP_SERVER_DIR` |
| MySQL (prod y prep) | GitHub → Secrets | `PROD_DB_HOST`, `PROD_DB_NAME`, `PROD_DB_USER`, `PROD_DB_PASSWORD` |

**No envíes contraseñas por chat.** Usá GitHub Secrets o el panel del hosting.

---

## Login por usuario

Después de importar `004_username_login.sql`:

- Login comercio: **usuario + contraseña** (no email)
- Registro: pedís **usuario** único + email de contacto
- Admin plataforma: usuario **`admin`** / **`password`**
