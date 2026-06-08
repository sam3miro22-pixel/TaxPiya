# Verificación rápida de producción TaxPiya
$base = if ($env:TAXPIYA_BASE_URL) { $env:TAXPIYA_BASE_URL } else { "https://taxpiya.onrender.com" }
Write-Host "=== TaxPiya PROD check ===" -ForegroundColor Cyan
Write-Host "Base: $base`n"

$paths = @(
    "/pasajero/login",
    "/pasajero/registro",
    "/conductor/aplicar",
    "/conductor/registro",
    "/empresa/afiliarse",
    "/empresa/registro",
    "/api/referral/validate?code=TXP-P999999"
)

foreach ($p in $paths) {
    try {
        $r = Invoke-WebRequest -Uri "$base$p" -UseBasicParsing -TimeoutSec 90 -Method Get -ErrorAction Stop
        $ok = $r.StatusCode -ge 200 -and $r.StatusCode -lt 400
        $tag = if ($ok) { "[OK]" } else { "[FAIL]" }
        Write-Host "$tag $p HTTP $($r.StatusCode)"
    } catch {
        $code = $_.Exception.Response.StatusCode.value__
        if ($p -match "TXP-P999999" -and $code -eq 422) {
            Write-Host "[OK] $p HTTP 422 (código inválido esperado)"
        } else {
            Write-Host "[FAIL] $p HTTP $code"
        }
    }
}

try {
    $html = (Invoke-WebRequest -Uri "$base/conductor/aplicar" -UseBasicParsing -TimeoutSec 90).Content
    if ($html -match "taxpiya20@gmail.com") { Write-Host "[OK] Aviso documentación en conductor/aplicar" }
    else { Write-Host "[FAIL] Falta aviso documentación en conductor/aplicar" }
    if ($html -match "firebase-auth\.bundle\.js\?v=8") { Write-Host "[OK] Firebase bundle v8" }
    else { Write-Host "[WARN] Firebase bundle v8 no detectado (deploy pendiente?)" }
} catch {
    Write-Host "[FAIL] No se pudo leer conductor/aplicar"
}

try {
    $login = (Invoke-WebRequest -Uri "$base/pasajero/login" -UseBasicParsing -TimeoutSec 90).Content
    if ($login -match 'name="rememberme"' -and $login -match 'checked') { Write-Host "[OK] Recuérdame marcado por defecto" }
    else { Write-Host "[WARN] Recuérdame no detectado como checked" }
} catch {}

Write-Host "`nListo."
