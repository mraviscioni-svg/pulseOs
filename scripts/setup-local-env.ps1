# Crea .env local para que el agente/CLI pueda correr migraciones.
# Completá PROD_DB_* con los mismos valores que en GitHub Secrets.

$root = Split-Path -Parent $PSScriptRoot
$envFile = Join-Path $root ".env"
$example = Join-Path $root ".env.example"

if (Test-Path $envFile) {
    Write-Host ".env ya existe en $envFile"
    exit 0
}

Copy-Item $example $envFile
Write-Host "Creado: $envFile"
Write-Host ""
Write-Host "Editá el archivo y pegá:"
Write-Host "  PROD_DB_HOST=localhost"
Write-Host "  PROD_DB_NAME=..."
Write-Host "  PROD_DB_USER=..."
Write-Host "  PROD_DB_PASSWORD=..."
Write-Host ""
Write-Host "Luego: php scripts/verify-connections.php"
Write-Host "       php scripts/migrate.php --status"
Write-Host "       php scripts/migrate.php"
