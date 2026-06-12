# Compila Taxpiya APK (debug) en Windows
$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

Write-Host "=== Taxpiya APK ===" -ForegroundColor Cyan

if (-not (Get-Command java -ErrorAction SilentlyContinue)) {
    Write-Host "ERROR: Java no encontrado. Instala Android Studio o:" -ForegroundColor Red
    Write-Host "  winget install Microsoft.OpenJDK.17" -ForegroundColor Yellow
    exit 1
}

if (-not (Test-Path "node_modules")) {
    Write-Host "npm install..."
    npm install
}

Write-Host "cap sync android..."
npx cap sync android

$gradle = Join-Path $root "android\gradlew.bat"
if (-not (Test-Path $gradle)) {
    Write-Host "ERROR: No existe android/gradlew.bat" -ForegroundColor Red
    exit 1
}

Write-Host "Gradle assembleDebug..."
Push-Location android
& .\gradlew.bat assembleDebug --no-daemon
Pop-Location

$apk = Join-Path $root "android\app\build\outputs\apk\debug\app-debug.apk"
if (Test-Path $apk) {
    $dest = Join-Path $root "Taxpiya-unified-debug.apk"
    Copy-Item $apk $dest -Force
    Write-Host ""
    Write-Host "OK - APK generada:" -ForegroundColor Green
    Write-Host "  $apk"
    Write-Host "  $dest"
} else {
    Write-Host "ERROR: No se genero la APK" -ForegroundColor Red
    exit 1
}
