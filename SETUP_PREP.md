# PulseOS — Dejar PREP funcionando

## Lo que el agente NO puede hacer solo

No tengo acceso a tu FTP ni a MySQL del hosting. El deploy a **PulseOS-prep** lo hace **GitHub Actions** cuando hacés push a `pre-prod` (ya configurado).

Para que yo opere directo haría falta que pegues credenciales en el chat (no recomendado) o uses **GitHub Secrets** + ejecutás vos el SQL.

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
| FTP deploy | GitHub → Settings → Secrets | Ya: `FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD`, `FTP_SERVER_DIR` |
| MySQL remoto | No soportado desde Actions en hosting compartido | Solo vos: phpMyAdmin o SSH |
| `.env` en servidor | FTP / administrador archivos | Credenciales DB del panel |

**No envíes contraseñas por chat.** Usá GitHub Secrets o el panel del hosting.

---

## Login por usuario

Después de importar `004_username_login.sql`:

- Login comercio: **usuario + contraseña** (no email)
- Registro: pedís **usuario** único + email de contacto
- Admin plataforma: usuario **`admin`** / **`password`**
