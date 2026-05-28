# MySQL en hosting (Taller Boedo / cPanel)

## Error `MySQL server has gone away` al conectar

En **hosting compartido**, PHP y MySQL están en el **mismo servidor**. El host casi siempre es:

```text
localhost
```

**No uses** el dominio del sitio (`tallerboedo.com.ar`), la URL de cPanel (`mauca.com.ar:2083`) ni IPs externas en `DB_HOST`.

Ejemplo incorrecto (lo que muestra tu health ahora):

```text
PROD_DB_HOST=mauca.com.ar:2083   ← esto es el panel web, NO MySQL
```

Correcto:

```text
PROD_DB_HOST=localhost
```

### Si `host_probe` dice Access denied (1045) en localhost

MySQL **sí responde**; el problema es usuario/clave o permisos:

1. cPanel → **MySQL® Databases**
2. Creá la base y el usuario (o usá los existentes)
3. **Add User To Database** → usuario `mauritoc_tallerboedo` + base `mauritoc_tallerboedo` → **ALL PRIVILEGES**
4. Copiá la clave exacta al secret `PROD_DB_PASSWORD`
5. Redeploy o editá `.env` en `PulseOS-prep/`

---

## Qué corregir

### Opción A — GitHub Secrets (recomendado)

En **Settings → Secrets → Actions**, editá:

| Secret | Valor típico en cPanel |
|--------|-------------------------|
| `PROD_DB_HOST` | `localhost` |
| `PROD_DB_NAME` | nombre exacto de la base (ej. `tallerboedo_pulse`) |
| `PROD_DB_USER` | usuario MySQL del panel |
| `PROD_DB_PASSWORD` | clave del usuario MySQL |

Luego: **Actions → Deploy to FTP** (rama `pre-prod`) para regenerar `.env` en el servidor.

### Opción B — Editar `.env` en FTP

En `PulseOS-prep/.env`:

```env
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nombre_de_tu_base
DB_USERNAME=usuario_mysql
DB_PASSWORD=tu_clave
```

---

## Verificar

https://tallerboedo.com.ar/PulseOS-prep/public/health

Debe mostrar `"database":"ok"`. Si falla, mirá `host_probe` y `fix` en el JSON.

---

## Migraciones

Con la base conectada, importá en phpMyAdmin (en orden):

1. `001_initial_schema.sql`
2. `002_seed_roles_permissions.sql`
3. `003_platform_admins.sql`
4. `004_username_login.sql`
