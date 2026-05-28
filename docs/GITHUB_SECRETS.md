# Secretos GitHub — PulseOS

**Prod y pre-prod usan los mismos secrets.** La única diferencia es la carpeta FTP al desplegar.

| Rama | Carpeta remota |
|------|----------------|
| `pre-prod` | `PulseOS-prep/` |
| `prod` | `pulseOS/` |

---

## Secrets (8 + 1 opcional)

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
| `PROD_DB_HOST` | `DB_HOST` |
| `PROD_DB_NAME` | `DB_DATABASE` |
| `PROD_DB_USER` | `DB_USERNAME` |
| `PROD_DB_PASSWORD` | `DB_PASSWORD` |

El nombre `PROD_DB_*` es histórico; aplica a **ambos** entornos.

### Opcional

| Secret | Descripción |
|--------|-------------|
| `PROD_APP_URL` | URL base; el workflow ajusta el path según carpeta (`/pulseOS` o `/PulseOS-prep`) si no la definís |

---

## Deploy

Cada push a `pre-prod` o `prod`:

1. `composer install`
2. Genera `.env` con `PROD_DB_*` (prep: `APP_DEBUG=true`, prod: `false`)
3. Sube por FTP a la carpeta correspondiente

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
```

```powershell
.\scripts\verify-connections.ps1
```

---

## Workflows

| Action | Qué usa |
|--------|---------|
| **Verify connections** | `FTP_*` + `PROD_DB_*` |
| **Deploy to FTP** | Igual + carpeta según rama |
