# Secretos GitHub — PulseOS

## Importante: Secrets vs Variables

| Tipo en GitHub | ¿Lo usa el deploy? | ¿Lo ve Cursor/agente local? |
|----------------|-------------------|----------------------------|
| **Secrets** (Actions) | Sí | **No** |
| **Variables** (repo) | Solo si el workflow las referencia | No |
| Archivo **`.env` en tu PC** | No (no se sube) | **Sí** |

Para trabajar dinámico con el agente: copiá los valores a **`.env` local** (gitignored).

---

## Nombres exactos para el deploy FTP

Creá en **Settings → Secrets and variables → Actions → Secrets** (no Variables):

| Secret | Ejemplo | Notas |
|--------|---------|--------|
| `FTP_SERVER` | `ftp.midominio.com` | Sin `ftp://` |
| `FTP_USERNAME` | usuario del panel | |
| `FTP_PASSWORD` | •••••• | |
| `FTP_SERVER_DIR` | `./` o `public_html/` | **Debe terminar en `/`** |

El workflow concatena: `{FTP_SERVER_DIR}PulseOS-prep/` (pre-prod) o `pulseOS/` (prod).

### Errores frecuentes

- `FTP_HOST` → incorrecto, debe ser **`FTP_SERVER`**
- `FTP_SERVER_DIR` sin barra final → puede fallar el path
- Poner credenciales en **Variables** en lugar de **Secrets** → el workflow no las lee como `secrets.*`

---

## Secretos opcionales para MySQL (workflow Verify connections)

Solo para el action manual **Verify connections**. El deploy FTP **no** ejecuta migraciones.

| Secret | Ejemplo |
|--------|---------|
| `DB_HOST` | `localhost` (desde GitHub casi siempre **falla** si el hosting solo permite localhost) |
| `DB_PORT` | `3306` |
| `DB_DATABASE` | base prep |
| `DB_USERNAME` | user mysql |
| `DB_PASSWORD` | •••• |

En hosting compartido, MySQL suele ser **solo desde el servidor**. Las migraciones hacelas en **phpMyAdmin** o con `.env` si tenés túnel SSH.

---

## Cómo verificar

### 1. GitHub (sin exponer claves)

1. Actions → **Verify connections** → Run workflow
2. Revisá si falla FTP o MySQL
3. Deploy: Actions → **Deploy to FTP** (último run verde)

### 2. En tu PC (para el agente Cursor)

**Sin `.env` local el agente no puede conectarse** aunque los secrets en GitHub estén bien.

```powershell
cd "C:\Users\marcelo.raviscioni\Desktop\pulseOs"
copy .env.example .env
# Editá .env con FTP + MySQL del hosting (mismos valores que en GitHub, sin subir el archivo)
.\scripts\verify-connections.ps1
# o, si tenés PHP: composer install && php scripts/verify-connections.php
```

Checklist de nombres en `.env` (deben coincidir **exactamente**):

| En `.env` | En GitHub Secrets (Actions) |
|-----------|----------------------------|
| `FTP_SERVER` | `FTP_SERVER` |
| `FTP_USERNAME` | `FTP_USERNAME` |
| `FTP_PASSWORD` | `FTP_PASSWORD` |
| `FTP_SERVER_DIR` | `FTP_SERVER_DIR` |
| `DB_HOST` | `DB_HOST` (opcional) |
| `DB_DATABASE` | `DB_DATABASE` — **no** `DB_NAME` |
| `DB_USERNAME` | `DB_USERNAME` — **no** `DB_USER` |
| `DB_PASSWORD` | `DB_PASSWORD` |

Si el script da OK, el agente puede usar `.env` para deploy manual, SQL, etc.

---

## Qué NO hacer

- No commitear `.env`
- No pegar contraseñas en issues o chat
- No usar el mismo secret name con typos
