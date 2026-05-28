# Secretos GitHub — PulseOS

Configuración alineada con los secrets que tenés en el repo.

## Secrets actuales (los tuyos)

### FTP — obligatorios para deploy

| Secret | Uso |
|--------|-----|
| `FTP_SERVER` | Host FTP (sin `ftp://`) |
| `FTP_USERNAME` | Usuario FTP |
| `FTP_PASSWORD` | Contraseña FTP |
| `FTP_SERVER_DIR` | Carpeta base con `/` final, ej. `./` |

Deploy sube a: `{FTP_SERVER_DIR}PulseOS-prep/` (pre-prod) o `{FTP_SERVER_DIR}pulseOS/` (prod).

### MySQL producción — `PROD_DB_*`

| Secret | Se escribe en `.env` del servidor como |
|--------|----------------------------------------|
| `PROD_DB_HOST` | `DB_HOST` |
| `PROD_DB_NAME` | `DB_DATABASE` |
| `PROD_DB_USER` | `DB_USERNAME` |
| `PROD_DB_PASSWORD` | `DB_PASSWORD` |

En deploy a rama **prod**, el workflow genera `.env` automáticamente desde estos secrets.

### MySQL pre-prod — opcional `PREP_DB_*`

Si más adelante agregás base distinta para prep:

| Secret | Igual que prod pero para `PulseOS-prep/` |
|--------|------------------------------------------|
| `PREP_DB_HOST` | → `DB_HOST` |
| `PREP_DB_NAME` | → `DB_DATABASE` |
| `PREP_DB_USER` | → `DB_USERNAME` |
| `PREP_DB_PASSWORD` | → `DB_PASSWORD` |

Sin `PREP_DB_*`, el `.env` de **pre-prod** se crea manual en el FTP.

### Opcionales

| Secret | Descripción |
|--------|-------------|
| `PROD_APP_URL` | URL pública prod (default placeholder en workflow) |
| `PREP_APP_URL` | URL pública prep |

---

## Local (Cursor / agente)

Copiá `.env.example` → `.env` y usá **los mismos nombres que en GitHub** (`PROD_DB_*` y `FTP_*`). La app acepta `PROD_DB_*` o `DB_*`:

```env
FTP_SERVER=...
FTP_USERNAME=...
FTP_PASSWORD=...
FTP_SERVER_DIR=./

PROD_DB_HOST=localhost
PROD_DB_NAME=tu_base
PROD_DB_USER=tu_user
PROD_DB_PASSWORD=***
```

Verificar:

```powershell
.\scripts\verify-connections.ps1
```

---

## Workflows

| Action | Qué valida |
|--------|------------|
| **Verify connections** | FTP_* + PROD_DB_* |
| **Deploy to FTP** | FTP_*; genera `.env` en prod con PROD_DB_* |

---

## Errores frecuentes

- Poner credenciales en **Variables** en vez de **Secrets** → el workflow no las ve.
- `FTP_HOST` → debe ser **`FTP_SERVER`**.
- `DB_DATABASE` en GitHub → en tu repo usás **`PROD_DB_NAME`** (correcto).
- `FTP_SERVER_DIR` sin `/` al final.

---

## Qué NO hacer

- No commitear `.env`
- No pegar contraseñas en el chat
