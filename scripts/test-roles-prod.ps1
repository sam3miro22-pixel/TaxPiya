# Verifica separación de roles y auth en producción
$base = if ($env:TAXPIYA_BASE_URL) { $env:TAXPIYA_BASE_URL } else { "https://taxpiya.onrender.com" }
$pass = "Taxpiya2026!"

Write-Host "=== TaxPiya roles + auth PROD ===" -ForegroundColor Cyan
Write-Host "Base: $base`n"

function Get-Session {
    param([string]$LoginPath, [string]$App)
    $r = Invoke-WebRequest -Uri "$base$LoginPath" -UseBasicParsing -SessionVariable sess -TimeoutSec 90
    if ($r.Content -notmatch 'name="_token" value="([^"]+)"') { return $null }
    $token = $Matches[1]
    $body = @{
        _token = $token
        username = if ($App -eq 'conductor') { '3109001001' } elseif ($App -eq 'empresa') { '3209002001' } else { '3009001001' }
        password = $pass
        app = $App
        rememberme = '1'
    }
    $login = Invoke-WebRequest -Uri "$base/auth/login" -Method POST -WebSession $sess -Body $body -UseBasicParsing -TimeoutSec 90 -MaximumRedirection 0 -ErrorAction SilentlyContinue
    $failed = $login.Content -match 'no correctos|exclusivo|solo para|inactiva|no está activa'
    return @{ Session = $sess; Failed = $failed; Code = $login.StatusCode; Body = $login.Content }
}

# Páginas login por rol
@('/pasajero/login','/conductor/login','/empresa/login') | ForEach-Object {
    try {
        $c = (Invoke-WebRequest -Uri "$base$_" -UseBasicParsing -TimeoutSec 90).StatusCode
        Write-Host "[OK] $_ HTTP $c"
    } catch { Write-Host "[FAIL] $_" }
}

# Pasajero demo → login OK
$pax = Get-Session '/pasajero/login' 'pasajero'
if ($pax -and -not $pax.Failed) { Write-Host "[OK] Pasajero demo login (celular)" }
else { Write-Host "[FAIL] Pasajero demo login" }

# Conductor demo en portal pasajero → debe fallar
try {
    $r = Invoke-WebRequest -Uri "$base/pasajero/login" -UseBasicParsing -SessionVariable s2 -TimeoutSec 90
    $t = if ($r.Content -match 'name="_token" value="([^"]+)"') { $Matches[1] } else { '' }
    $bad = Invoke-WebRequest -Uri "$base/auth/login" -Method POST -WebSession $s2 -Body @{
        _token=$t; username='3109001001'; password=$pass; app='pasajero'; rememberme='1'
    } -UseBasicParsing -MaximumRedirection 0 -ErrorAction SilentlyContinue
    $blocked = ($bad.Content -match 'solo para Pasajeros|exclusivo|no correctos')
        -or ($bad.BaseResponse.ResponseUri.AbsolutePath -match 'pasajero/login')
    if ($blocked) { Write-Host "[OK] Conductor bloqueado en login pasajero" }
    else { Write-Host "[FAIL] Conductor pudo entrar como pasajero (URL: $($bad.BaseResponse.ResponseUri))" }
} catch { Write-Host "[WARN] Test cruce conductor→pasajero: $($_.Exception.Message)" }

# Pasajero demo en portal conductor → debe fallar
try {
    $r = Invoke-WebRequest -Uri "$base/conductor/login" -UseBasicParsing -SessionVariable s3 -TimeoutSec 90
    $t = if ($r.Content -match 'name="_token" value="([^"]+)"') { $Matches[1] } else { '' }
    $bad = Invoke-WebRequest -Uri "$base/auth/login" -Method POST -WebSession $s3 -Body @{
        _token=$t; username='3009001001'; password=$pass; app='conductor'; rememberme='1'
    } -UseBasicParsing -MaximumRedirection 0 -ErrorAction SilentlyContinue
    $blocked = ($bad.Content -match 'exclusivo para Conductores|solo para Pasajeros|no correctos|no está activa')
        -or ($bad.BaseResponse.ResponseUri.AbsolutePath -match 'conductor/login')
    if ($blocked) { Write-Host "[OK] Pasajero bloqueado en login conductor" }
    else { Write-Host "[FAIL] Pasajero pudo entrar como conductor (URL: $($bad.BaseResponse.ResponseUri))" }
} catch { Write-Host "[WARN] Test cruce pasajero→conductor: $($_.Exception.Message)" }

# Conductor demo → login OK en portal conductor
$drv = Get-Session '/conductor/login' 'conductor'
if ($drv -and -not $drv.Failed) { Write-Host "[OK] Conductor demo login (celular)" }
else { Write-Host "[FAIL] Conductor demo login" }

# Referido código TXP-P33084 válido
try {
    $v = Invoke-WebRequest -Uri "$base/api/referral/validate?code=TXP-P33084" -UseBasicParsing -TimeoutSec 90
    if ($v.StatusCode -eq 200) { Write-Host "[OK] Código referido TXP-P33084 válido" }
    else { Write-Host "[INFO] TXP-P33084 HTTP $($v.StatusCode)" }
} catch {
    $code = $_.Exception.Response.StatusCode.value__
    Write-Host "[INFO] TXP-P33084 HTTP $code"
}

# Firebase UI en pasajero
try {
    $html = (Invoke-WebRequest -Uri "$base/pasajero/login" -UseBasicParsing -TimeoutSec 90).Content
    if ($html -match 'txp-firebase-auth') { Write-Host "[OK] Firebase auth UI en pasajero/login" }
    if ($html -match 'Celular') { Write-Host "[OK] Label celular separado de correo" }
} catch {}

Write-Host "`nListo."
