# PulseOS

Proyecto PulseOS — deploy automático a FTP (Taller Boedo).

## Ramas y entornos

| Rama GitHub | Carpeta FTP (Taller Boedo) | Uso |
|-------------|----------------------------|-----|
| `prod` | `pulseOS/` | Producción |
| `pre-prod` | `PulseOS-prep/` | Pre-producción |

Cada **push** a la rama correspondiente dispara el deploy por GitHub Actions.

## Configuración inicial (una vez)

1. En GitHub: [pulseOs → Settings → Secrets and variables → Actions](https://github.com/mraviscioni-svg/pulseOs/settings/secrets/actions)
2. Crear estos secretos:

| Secreto | Descripción |
|---------|-------------|
| `FTP_SERVER` | Host FTP (sin `ftp://`), ej. del hosting Taller Boedo |
| `FTP_USERNAME` | Usuario FTP |
| `FTP_PASSWORD` | Contraseña FTP |
| `FTP_SERVER_DIR` | Carpeta base donde caés al conectar (terminar en `/`). Si al entrar ya estás en la raíz del sitio, usá `./`. Si entrás en una carpeta padre, ej. `public_html/` |

Las carpetas `pulseOS/` y `PulseOS-prep/` se crean automáticamente en el primer deploy si no existen.

3. En **Settings → General → Default branch**, elegí `prod` como rama por defecto (recomendado).

Detalle completo: ver [DEPLOY.md](DEPLOY.md).

## Flujo de trabajo

```powershell
cd "c:\Users\marcelo.raviscioni\Desktop\pulseOs"

# Desarrollo → pre-producción
git checkout pre-prod
git add .
git commit -m "Tu mensaje"
git push origin pre-prod

# Cuando esté listo → producción
git checkout prod
git merge pre-prod
git push origin prod
```

## Estado

Repositorio preparado para desarrollo. El código de la aplicación se agregará en los próximos commits.
