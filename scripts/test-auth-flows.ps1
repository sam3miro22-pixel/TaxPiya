# Pruebas completas: registros, logins y aprobación admin
$ErrorActionPreference = "Stop"
$base = if ($env:TAXPIYA_BASE_URL) { $env:TAXPIYA_BASE_URL.TrimEnd('/') } else { "https://taxpiya.onrender.com" }
$pw = "Taxpiya2026!"
$failed = 0

function Test-Ok($name, $pass, $detail = "") {
    if ($pass) { Write-Host "[OK] $name $(if($detail){"- $detail"})" -ForegroundColor Green }
    else { Write-Host "[FAIL] $name $(if($detail){"- $detail"})" -ForegroundColor Red; $script:failed++ }
}

function Get-Csrf($html) {
    if ($html -match 'name="_token"\s+value="([^"]+)"') { return $Matches[1] }
    if ($html -match 'csrf-token"\s+content="([^"]+)"') { return $Matches[1] }
    return ""
}

function Get-Page($url) {
    return Invoke-WebRequest -Uri "$base$url" -UseBasicParsing -TimeoutSec 120
}

function Login-Role($app, $phone) {
    $sess = New-Object Microsoft.PowerShell.Commands.WebRequestSession
    $loginUrl = switch ($app) {
        "admin" { "$base/index/login" }
        "empresa" { "$base/empresa/login" }
        "conductor" { "$base/conductor/login" }
        default { "$base/pasajero/login" }
    }
    $page = Invoke-WebRequest -Uri $loginUrl -WebSession $sess -UseBasicParsing -TimeoutSec 120
    $token = Get-Csrf $page.Content
    $body = "_token=$([uri]::EscapeDataString($token))&username=$([uri]::EscapeDataString($phone))&password=$([uri]::EscapeDataString($pw))&rememberme=true"
    if ($app -ne "admin") { $body += "&app=$([uri]::EscapeDataString($app))" }
    $resp = Invoke-WebRequest -Method POST -Uri "$base/auth/login" -WebSession $sess -Body $body -UseBasicParsing -MaximumRedirection 0 -ErrorAction SilentlyContinue
    if ($resp.StatusCode -ge 300 -and $resp.StatusCode -lt 400) {
        $loc = $resp.Headers.Location
        if ($loc -and $loc -notmatch '^https?://') { $loc = "$base$loc" }
        if ($loc) { Invoke-WebRequest -Uri $loc -WebSession $sess -UseBasicParsing -MaximumRedirection 5 -TimeoutSec 120 | Out-Null }
    }
    return $sess
}

function Login-ShouldFail($app, $phone) {
    $sess = New-Object Microsoft.PowerShell.Commands.WebRequestSession
    $loginUrl = switch ($app) {
        "conductor" { "$base/conductor/login" }
        "empresa" { "$base/empresa/login" }
        default { "$base/pasajero/login" }
    }
    $page = Invoke-WebRequest -Uri $loginUrl -WebSession $sess -UseBasicParsing -TimeoutSec 120
    $token = Get-Csrf $page.Content
    $body = "_token=$([uri]::EscapeDataString($token))&username=$([uri]::EscapeDataString($phone))&password=WrongPassword999!&app=$([uri]::EscapeDataString($app))"
    $resp = Invoke-WebRequest -Method POST -Uri "$base/auth/login" -WebSession $sess -Body $body -UseBasicParsing -MaximumRedirection 0 -ErrorAction SilentlyContinue
    return ($resp.StatusCode -ge 300 -and $resp.Headers.Location -match 'login')
}

Write-Host "=== TaxPiya pruebas auth/registro/admin ===`n" -ForegroundColor Cyan

# Páginas de login
foreach ($p in @(
    @{ u="/pasajero/login"; n="Login pasajero"; m="auth/login" },
    @{ u="/conductor/login"; n="Login conductor"; m="auth/login" },
    @{ u="/empresa/login"; n="Login empresa"; m="auth/login" },
    @{ u="/index/login"; n="Login admin"; m="auth/login" }
)) {
    try {
        $pg = Get-Page $p.u
        Test-Ok $p.n ($pg.StatusCode -eq 200 -and $pg.Content -match $p.m)
    } catch { Test-Ok $p.n $false $_.Exception.Message }
}

# Páginas de registro
foreach ($p in @(
    @{ u="/pasajero/registro"; n="Registro pasajero"; m="pasajero/registro" },
    @{ u="/conductor/aplicar"; n="Registro conductor (aplicar)"; m="conductor/aplicar" },
    @{ u="/conductor/registro"; n="Registro conductor (alias)"; m="conductor" },
    @{ u="/empresa/afiliarse"; n="Registro empresa (afiliarse)"; m="empresa/afiliarse" },
    @{ u="/empresa/registro"; n="Registro empresa (alias)"; m="empresa" }
)) {
    try {
        $pg = Get-Page $p.u
        Test-Ok $p.n ($pg.StatusCode -eq 200 -and $pg.Content -match $p.m)
    } catch { Test-Ok $p.n $false $_.Exception.Message }
}

# Páginas OK post-registro
foreach ($p in @(
    @{ u="/conductor/aplicar/ok"; n="Conductor solicitud enviada" },
    @{ u="/empresa/afiliarse/ok"; n="Empresa solicitud enviada" }
)) {
    try {
        $pg = Get-Page $p.u
        Test-Ok $p.n ($pg.StatusCode -eq 200)
    } catch { Test-Ok $p.n $false $_.Exception.Message }
}

# Login incorrecto rechazado
Test-Ok "Login rechaza password incorrecto" (Login-ShouldFail "pasajero" "3009001001")

# Logins demo activos
$pSess = Login-Role "pasajero" "3009001001"
$cSess = Login-Role "conductor" "3109001001"
$eSess = Login-Role "empresa" "3209002001"
$aSess = Login-Role "admin" "3001001001"

try {
    $ph = Invoke-WebRequest -Uri "$base/home" -WebSession $pSess -UseBasicParsing -TimeoutSec 120
    Test-Ok "Pasajero accede /home" ($ph.StatusCode -eq 200 -and $ph.Content -match 'pasajero|Taxpiya|map')
} catch { Test-Ok "Pasajero accede /home" $false $_.Exception.Message }

try {
    $ch = Invoke-WebRequest -Uri "$base/home" -WebSession $cSess -UseBasicParsing -TimeoutSec 120
    Test-Ok "Conductor accede /home" ($ch.StatusCode -eq 200 -and $ch.Content -match 'conductor|driver-online|Taxpiya')
} catch { Test-Ok "Conductor accede /home" $false $_.Exception.Message }

try {
    $eh = Invoke-WebRequest -Uri "$base/empresa" -WebSession $eSess -UseBasicParsing -TimeoutSec 120
    Test-Ok "Empresa accede /empresa" ($eh.StatusCode -eq 200 -and $eh.Content -match 'empresa|Empresa|flota')
} catch { Test-Ok "Empresa accede /empresa" $false $_.Exception.Message }

try {
    $ah = Invoke-WebRequest -Uri "$base/home" -WebSession $aSess -UseBasicParsing -TimeoutSec 120
    Test-Ok "Admin accede dashboard" ($ah.StatusCode -eq 200)
    Test-Ok "Admin boton cerrar sesion" ($ah.Content -match 'auth/logout' -and $ah.Content -match 'Cerrar')
} catch { Test-Ok "Admin accede dashboard" $false $_.Exception.Message }

# Panel admin: listas y pendientes
try {
    $cond = Invoke-WebRequest -Uri "$base/conductores" -WebSession $aSess -UseBasicParsing -TimeoutSec 120
    Test-Ok "Admin lista conductores" ($cond.StatusCode -eq 200 -and $cond.Content -match 'Conductores')
    Test-Ok "Admin filtro conductores pendientes" ($cond.Content -match 'estado_operitivo/0' -and $cond.Content -match 'Pendientes')
} catch { Test-Ok "Admin lista conductores" $false $_.Exception.Message }

try {
    $condP = Invoke-WebRequest -Uri "$base/conductores/index/estado_operitivo/0" -WebSession $aSess -UseBasicParsing -TimeoutSec 120
    Test-Ok "Admin conductores pendientes URL" ($condP.StatusCode -eq 200)
} catch { Test-Ok "Admin conductores pendientes URL" $false $_.Exception.Message }

try {
    $emp = Invoke-WebRequest -Uri "$base/empresas" -WebSession $aSess -UseBasicParsing -TimeoutSec 120
    Test-Ok "Admin lista empresas" ($emp.StatusCode -eq 200 -and $emp.Content -match 'Empresas')
    Test-Ok "Admin filtro empresas pendientes" ($emp.Content -match 'estado/pendiente' -and $emp.Content -match 'Pendientes')
} catch { Test-Ok "Admin lista empresas" $false $_.Exception.Message }

try {
    $empP = Invoke-WebRequest -Uri "$base/empresas/index/estado/pendiente" -WebSession $aSess -UseBasicParsing -TimeoutSec 120
    Test-Ok "Admin empresas pendientes URL" ($empP.StatusCode -eq 200)
} catch { Test-Ok "Admin empresas pendientes URL" $false $_.Exception.Message }

# Cross-portal: pasajero no entra como conductor
try {
    $sess = New-Object Microsoft.PowerShell.Commands.WebRequestSession
    $page = Invoke-WebRequest -Uri "$base/conductor/login" -WebSession $sess -UseBasicParsing -TimeoutSec 120
    $token = Get-Csrf $page.Content
    $body = "_token=$([uri]::EscapeDataString($token))&username=3009001001&password=$([uri]::EscapeDataString($pw))&app=conductor"
    $resp = Invoke-WebRequest -Method POST -Uri "$base/auth/login" -WebSession $sess -Body $body -UseBasicParsing -MaximumRedirection 0 -ErrorAction SilentlyContinue
    $blocked = ($resp.Headers.Location -match 'conductor/login')
    Test-Ok "Pasajero no entra portal conductor" $blocked
} catch { Test-Ok "Pasajero no entra portal conductor" $false $_.Exception.Message }

# Firebase diag
try {
    $fb = Invoke-RestMethod -Uri "$base/auth/firebase/diag" -TimeoutSec 60
    Test-Ok "Firebase diag endpoint" ($fb.firebase_auth -eq $true -and $fb.sessions_table -eq $true)
} catch { Test-Ok "Firebase diag endpoint" $false $_.Exception.Message }

Write-Host "`n=== Resultado: $failed fallos ===" -ForegroundColor $(if ($failed -eq 0) { "Green" } else { "Red" })
exit $failed
