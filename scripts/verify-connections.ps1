# Prueba MySQL (si hay cliente) y FTP desde .env local
# Uso: cd repo; .\scripts\verify-connections.ps1

$ErrorActionPreference = "Continue"
$root = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
$envFile = Join-Path $root ".env"

if (-not (Test-Path $envFile)) {
    Write-Host "FALTA .env — copiá .env.example a .env y completá credenciales." -ForegroundColor Red
    exit 1
}

function Get-EnvValue([string]$key) {
    foreach ($line in Get-Content $envFile) {
        if ($line -match "^\s*$key\s*=\s*(.*)$") {
            return $Matches[1].Trim().Trim('"').Trim("'")
        }
    }
    return ""
}

function Get-DbEnv([string]$dbKey, [string]$prodKey, [string]$prepKey) {
    $v = Get-EnvValue $dbKey
    if ($v) { return $v }
    $v = Get-EnvValue $prodKey
    if ($v) { return $v }
    return Get-EnvValue $prepKey
}

$ok = $true
Write-Host "=== PulseOS — verificación (.env local) ===`n"

# FTP
$ftpServer = Get-EnvValue "FTP_SERVER"
$ftpUser = Get-EnvValue "FTP_USERNAME"
$ftpPass = Get-EnvValue "FTP_PASSWORD"
$ftpDir = Get-EnvValue "FTP_SERVER_DIR"
if (-not $ftpDir) { $ftpDir = "./" }

Write-Host "FTP..."
if (-not $ftpServer -or -not $ftpUser) {
    Write-Host "  [SKIP] Faltan FTP_SERVER o FTP_USERNAME" -ForegroundColor Yellow
    $ok = $false
} else {
    try {
        $uri = [Uri]("ftp://$ftpServer/")
        $req = [System.Net.FtpWebRequest]::Create($uri)
        $req.Method = [System.Net.WebRequestMethods+Ftp]::ListDirectory
        $req.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $req.UsePassive = $true
        $req.Timeout = 15000
        $res = $req.GetResponse()
        $reader = New-Object System.IO.StreamReader($res.GetResponseStream())
        $list = $reader.ReadToEnd()
        $reader.Close()
        $res.Close()
        $count = ($list -split "`n" | Where-Object { $_.Trim() }).Count
        Write-Host "  [OK] Login FTP en $ftpServer ($count ítems en raíz)" -ForegroundColor Green
        $prep = ($ftpDir.TrimEnd('/') + "/PulseOS-prep").Replace("//", "/")
        Write-Host "  Carpeta prep esperada: $prep/"
    } catch {
        Write-Host "  [FAIL] $($_.Exception.Message)" -ForegroundColor Red
        $ok = $false
    }
}

# MySQL — solo si mysql.exe está disponible
Write-Host "`nMySQL..."
$dbHost = Get-DbEnv "DB_HOST" "PROD_DB_HOST" "PREP_DB_HOST"
$dbName = Get-DbEnv "DB_DATABASE" "PROD_DB_NAME" "PREP_DB_NAME"
$dbUser = Get-DbEnv "DB_USERNAME" "PROD_DB_USER" "PREP_DB_USER"
$dbPass = Get-DbEnv "DB_PASSWORD" "PROD_DB_PASSWORD" "PREP_DB_PASSWORD"
$dbPort = Get-EnvValue "DB_PORT"
if (-not $dbPort) { $dbPort = "3306" }

if (-not $dbHost -or -not $dbName -or -not $dbUser) {
    Write-Host "  [SKIP] Faltan PROD_DB_* o DB_* en .env" -ForegroundColor Yellow
    $ok = $false
} else {
    $mysql = Get-Command mysql -ErrorAction SilentlyContinue
    if (-not $mysql) {
        Write-Host "  [SKIP] mysql.exe no está en PATH — usá php scripts/verify-connections.php o phpMyAdmin" -ForegroundColor Yellow
    } else {
        $env:MYSQL_PWD = $dbPass
        $out = & mysql -h $dbHost -P $dbPort -u $dbUser $dbName -e "SELECT VERSION() AS v;" 2>&1
        Remove-Item Env:MYSQL_PWD -ErrorAction SilentlyContinue
        if ($LASTEXITCODE -eq 0) {
            Write-Host "  [OK] Conectado a $dbHost/$dbName" -ForegroundColor Green
            Write-Host "  $out"
        } else {
            Write-Host "  [FAIL] $out" -ForegroundColor Red
            $ok = $false
        }
    }
}

Write-Host ""
if ($ok) { Write-Host "Resultado: TODO OK" -ForegroundColor Green; exit 0 }
Write-Host "Resultado: revisá nombres en .env (ver docs/GITHUB_SECRETS.md)" -ForegroundColor Red
exit 1
