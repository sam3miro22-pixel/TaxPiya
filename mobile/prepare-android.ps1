# Sincroniza plugins Capacitor antes de abrir Android Studio
$ErrorActionPreference = "Stop"
Set-Location $PSScriptRoot
Write-Host "Instalando dependencias npm..." -ForegroundColor Cyan
npm install --legacy-peer-deps
Write-Host "Sincronizando Android..." -ForegroundColor Cyan
npx cap sync android
Write-Host ""
Write-Host "Listo. Abre en Android Studio:" -ForegroundColor Green
Write-Host "  $PSScriptRoot\android"
Write-Host ""
Write-Host "Instrucciones completas: $PSScriptRoot\COMPILAR-APK.txt"
