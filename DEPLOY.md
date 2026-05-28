# Deploy PulseOS — GitHub y FTP Taller Boedo

## Arquitectura

```
GitHub (mraviscioni-svg/pulseOs)
├── rama pre-prod  ──push──►  FTP …/PulseOS-prep/
└── rama prod      ──push──►  FTP …/pulseOS/
```

El workflow está en `.github/workflows/deploy-ftp.yml`.

## Secretos en GitHub

Repo: https://github.com/mraviscioni-svg/pulseOs/settings/secrets/actions

| Secreto | Valor |
|---------|--------|
| `FTP_SERVER` | Servidor FTP del hosting (ej. `ftp.tudominio.com`) |
| `FTP_USERNAME` | Usuario FTP Taller Boedo |
| `FTP_PASSWORD` | Contraseña FTP |
| `FTP_SERVER_DIR` | Directorio base al conectar, **con barra final**. Ejemplos: `./` si los archivos van en la raíz del login; o `public_html/` si el hosting abre ahí |

El action concatena la subcarpeta del entorno:

- **prod:** `{FTP_SERVER_DIR}pulseOS/`
- **pre-prod:** `{FTP_SERVER_DIR}PulseOS-prep/`

No hace falta crear las carpetas a mano en el FTP: el primer deploy las crea al subir archivos.

## Ramas

| Rama | Deploy automático | Carpeta FTP |
|------|-------------------|-------------|
| `prod` | Sí (push) | `pulseOS` |
| `pre-prod` | Sí (push) | `PulseOS-prep` |
| `main` | No | — |

Recomendación: usar **`prod`** como rama por defecto en GitHub (Settings → General).

## Comandos habituales

```powershell
cd "c:\Users\marcelo.raviscioni\Desktop\pulseOs"

git checkout pre-prod
# … cambios …
git add .
git commit -m "feat: descripción"
git push origin pre-prod

git checkout prod
git merge pre-prod
git push origin prod
```

## Deploy manual (sin push)

1. GitHub → **Actions** → **Deploy to FTP**
2. **Run workflow**
3. Elegir `pre-prod` o `prod`

## Qué se sube al FTP

Todo el contenido del repo **excepto**:

- `.git`, `.github`
- `README.md`, `DEPLOY.md`, `.gitignore`
- `.env` y archivos de entorno
- `node_modules/`

Cuando agregues la app (PHP, HTML, assets), esos archivos sí se despliegan.

## FTPS

Si el hosting exige FTP seguro, en `deploy-ftp.yml` cambiá `protocol: ftp` por `protocol: ftps`.

## Verificar el deploy

1. **Actions** → último run en verde
2. En el FTP, revisar `pulseOS/` o `PulseOS-prep/` según la rama
