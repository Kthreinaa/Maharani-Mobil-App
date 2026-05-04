param(
    [int]$Port = 8000,
    [switch]$NoBrowser
)

$ErrorActionPreference = "Stop"

# Always run from project root, even if script is called from another directory.
Set-Location -Path $PSScriptRoot

function Write-Step {
    param([string]$Message)
    Write-Host "==> $Message" -ForegroundColor Cyan
}

Write-Step "Menjalankan mode development Maharani Mobil"

# Basic preflight checks to avoid confusing runtime errors.
if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
    Write-Host "PHP tidak ditemukan di PATH. Install PHP atau tambahkan ke PATH terlebih dahulu." -ForegroundColor Red
    exit 1
}

if (-not (Test-Path ".\artisan")) {
    Write-Host "File artisan tidak ditemukan. Pastikan script dijalankan dari root project Laravel." -ForegroundColor Red
    exit 1
}

Write-Step "Membersihkan cache Laravel"
php artisan optimize:clear | Out-Host

Write-Step "Membangun cache view Blade"
php artisan view:cache | Out-Host

$url = "http://127.0.0.1:$Port"
Write-Step "Menjalankan server di $url"

if (-not $NoBrowser) {
    Write-Step "Membuka browser"
    Start-Process $url
}

php artisan serve --host=127.0.0.1 --port=$Port
