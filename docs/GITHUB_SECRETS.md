# Secretos GitHub — PulseOS

**Prod y pre-prod usan los mismos secrets.** La única diferencia es la carpeta FTP al desplegar.

| Rama | Carpeta remota |
|------|----------------|
| `pre-prod` | `PulseOS-prep/` |
| `prod` | `pulseOS/` |

---

## Secrets (9 + 1 opcional)

### FTP

| Secret | Uso |
|--------|-----|
| `FTP_SERVER` | Host FTP (sin `ftp://`) |
| `FTP_USERNAME` | Usuario FTP |
| `FTP_PASSWORD` | Contraseña FTP |
| `FTP_SERVER_DIR` | Carpeta base con `/` final, ej. `./` |

### MySQL (misma base para prod y prep)

| Secret | Se escribe en `.env` como |
|--------|---------------------------|
| `PROD_DB_HOST` | `DB_HOST` — en cPanel usar **`localhost`** (no el dominio) |
| `PROD_DB_NAME` | `DB_DATABASE` |
| `PROD_DB_USER` | `DB_USERNAME` |
| `PROD_DB_PASSWORD` | `DB_PASSWORD` |

El nombre `PROD_DB_*` es histórico; aplica a **ambos** entornos.

### Migraciones automáticas

| Secret | Descripción |
|--------|-------------|
| `MIGRATION_SECRET` | Cadena larga aleatoria (ej. `openssl rand -hex 32`). Protege `public/migrate-runner.php` en el servidor. **Recomendado.** |

Sin este secret, el deploy no ejecuta migraciones; podés correrlas a mano o con `.env` local.

### Opcional

| Secret | Descripción |
|--------|-------------|
| `PROD_APP_URL` | URL base; el workflow ajusta el path según carpeta (`/pulseOS` o `/PulseOS-prep`) si no la definís |

---

## Deploy

Cada push a `pre-prod` o `prod`:

1. `composer install`
2. Genera `.env` con `PROD_DB_*` y `MIGRATION_SECRET` (prep: `APP_DEBUG=true`, prod: `false`)
3. Sube por FTP a la carpeta correspondiente
4. POST a `public/migrate-runner.php` para aplicar migraciones SQL pendientes

---

## Local (Cursor)

Mismos nombres que en GitHub:

```env
FTP_SERVER=...
FTP_USERNAME=...
FTP_PASSWORD=...
FTP_SERVER_DIR=./

PROD_DB_HOST=...
PROD_DB_NAME=...
PROD_DB_USER=...
PROD_DB_PASSWORD=...
MIGRATION_SECRET=...   # mismo que en GitHub (solo si corrés migraciones en local)
```

```powershell
.\scripts\setup-local-env.ps1   # crea .env desde .env.example
.\scripts\verify-connections.ps1
php scripts/migrate.php --status
php scripts/migrate.php
```

---

## Workflows

| Action | Qué usa |
|--------|---------|
| **Verify connections** | `FTP_*` + `PROD_DB_*` |
| **Deploy to FTP** | Igual + carpeta según rama + migraciones remotas |
| **Run database migrations** | Solo `MIGRATION_SECRET` → ejecuta en prep y prod |
