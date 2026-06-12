# Muestra las huellas SHA para pegar en Firebase Console (sin comandos, solo el hash).
$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent (Split-Path -Parent $PSScriptRoot)
$mobile = Join-Path $root 'mobile'
$apk = Join-Path $mobile 'Taxpiya-unified-debug.apk'
$ks = Join-Path $mobile 'android\app\taxpiya-debug.keystore'

$javaHome = $env:JAVA_HOME
if (-not $javaHome) {
    $javaHome = (Get-ChildItem 'C:\Program Files\Microsoft\jdk-*' -Directory -ErrorAction SilentlyContinue |
        Sort-Object Name -Descending | Select-Object -First 1).FullName
}
if (-not $javaHome) { throw 'Instala Java (JDK 17+) o define JAVA_HOME' }
$keytool = Join-Path $javaHome 'bin\keytool.exe'

Write-Host ''
Write-Host '=== PEGA ESTO EN FIREBASE (campo Huella digital) ===' -ForegroundColor Cyan
Write-Host 'Firebase Console -> tax-piya -> Configuracion -> App Android com.taxpiya.pasajero' -ForegroundColor Gray
Write-Host ''

if (Test-Path $apk) {
    $out = & $keytool -printcert -jarfile $apk 2>&1 | Out-String
    if ($out -match 'SHA1:\s*([0-9A-F:]+)') {
        Write-Host 'SHA-1 (APK actual):' -ForegroundColor Yellow
        Write-Host $Matches[1] -ForegroundColor Green
        Write-Host ''
    }
    if ($out -match 'SHA256:\s*([0-9A-F:]+)') {
        Write-Host 'SHA-256 (APK actual, opcional):' -ForegroundColor Yellow
        Write-Host $Matches[1] -ForegroundColor Green
        Write-Host ''
    }
}

if (Test-Path $ks) {
    $out2 = & $keytool -list -v -keystore $ks -alias taxpiyaDebug -storepass taxpiya2026 2>&1 | Out-String
    if ($out2 -match 'SHA1:\s*([0-9A-F:]+)') {
        Write-Host 'SHA-1 (keystore fijo futuras APK):' -ForegroundColor Yellow
        Write-Host $Matches[1] -ForegroundColor Green
        Write-Host ''
    }
}

Write-Host 'No pegues comandos keytool en Firebase. Solo la linea con numeros y dos puntos.' -ForegroundColor Magenta
