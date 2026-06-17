# Verificación completa producción Taxpiya — roles, secciones admin, KPIs y API
$base = 'https://taxpiya.onrender.com'
$password = 'Taxpiya2026!'
$apiKey = 'AIzaSyCIT1YV8eJRzmf7HSQbWe_7OzftDD1Vcnk'
$failed = 0

function Test-Step($name, $pass, $detail = '') {
    if ($pass) { Write-Host "[OK] $name $(if($detail){'- ' + $detail})" -ForegroundColor Green }
    else { Write-Host "[FAIL] $name $(if($detail){'- ' + $detail})" -ForegroundColor Red; $script:failed++ }
}

function Invoke-Txp {
    param([string]$Method, [string]$Url, [hashtable]$Headers = @{}, [string]$Body = $null, [Microsoft.PowerShell.Commands.WebRequestSession]$Session = $null)
    $params = @{
        Uri = $Url; Method = $Method; UseBasicParsing = $true
        MaximumRedirection = 0; ErrorAction = 'SilentlyContinue'
    }
    if ($Session) { $params.WebSession = $Session }
    if ($Headers.Count) { $params.Headers = $Headers }
    if ($Body) { $params.Body = $Body; if (-not $params.ContentType) { $params.ContentType = 'application/x-www-form-urlencoded' } }
    try {
        $r = Invoke-WebRequest @params -TimeoutSec 120
        return @{ Code = [int]$r.StatusCode; Body = $r.Content; Headers = $r.Headers; Ok = $true }
    } catch {
        $resp = $_.Exception.Response
        if ($resp) {
            $reader = New-Object System.IO.StreamReader($resp.GetResponseStream())
            $body = $reader.ReadToEnd()
            return @{ Code = [int]$resp.StatusCode; Body = $body; Headers = $resp.Headers; Ok = $false }
        }
        return @{ Code = 0; Body = $_.Exception.Message; Ok = $false }
    }
}

function Get-Csrf($html) {
    if ($html -match 'name="_token"\s+value="([^"]+)"') { return $Matches[1] }
    return ''
}

function Do-Login($sess, $loginUrl, $username, $app = $null) {
    $page = Invoke-Txp -Method GET -Url $loginUrl -Session $sess
    $token = Get-Csrf $page.Body
    $body = "_token=$([uri]::EscapeDataString($token))&username=$([uri]::EscapeDataString($username))&password=$([uri]::EscapeDataString($password))&rememberme=true"
    if ($app) { $body += "&app=$([uri]::EscapeDataString($app))" }
    return Invoke-Txp -Method POST -Url "$base/auth/login" -Session $sess -Body $body -Headers @{ Referer = $loginUrl }
}

Write-Host "=== Verificación completa $base ===`n"

# Firebase diag
try {
    $diag = Invoke-RestMethod -Uri "$base/auth/firebase/diag" -TimeoutSec 120
    Test-Step 'Firebase diag' ($diag.session_driver -eq 'database') "session_driver=$($diag.session_driver)"
} catch {
    Test-Step 'Firebase diag' $false $_.Exception.Message
}

# --- Admin ---
$adminSess = New-Object Microsoft.PowerShell.Commands.WebRequestSession
$loginA = Do-Login $adminSess "$base/index/login" '3001001001'
$aOk = ($loginA.Code -eq 302 -or $loginA.Code -eq 200) -and ($loginA.Body -notmatch 'no correctos|Page Expired|CSRF')
Test-Step 'Admin login' $aOk "HTTP $($loginA.Code)"

    if ($aOk) {
    $homeA = Invoke-WebRequest -Uri "$base/home" -WebSession $adminSess -UseBasicParsing -TimeoutSec 120
    Test-Step 'Admin /home' ($homeA.StatusCode -eq 200) "HTTP $($homeA.StatusCode)"

    $hasLeaflet = $homeA.Content -match 'leaflet|L\.map'
    $hasGoogleMaps = $homeA.Content -match 'maps\.googleapis\.com'
    Test-Step 'Mapa Leaflet (no Google Maps)' ($hasLeaflet -and -not $hasGoogleMaps)

    if ($homeA.Content -match 'Usuarios totales</div>\s*<div class="txp-kpi-value">(\d+)') { $usuarios = $Matches[1] } else { $usuarios = '?' }
    if ($homeA.Content -match 'Viajes</div>\s*<div class="txp-kpi-value">(\d+)') { $viajes = $Matches[1] } else { $viajes = '?' }
    Test-Step 'KPI usuarios <= 10' ([int]$usuarios -le 10) "usuarios=$usuarios"
    Test-Step 'KPI viajes = 0' ([int]$viajes -eq 0) "viajes=$viajes"

    $hasWalletApprove = $homeA.Content -match 'walletsolicitudes' -and $homeA.Content -match 'Aprobar dep'
    $hasMovLink = $homeA.Content -match 'walletmovimientos'
    $hasSaldoLink = $homeA.Content -match 'walletsaldos'
    Test-Step 'Dashboard enlace aprobar recargas' $hasWalletApprove
    Test-Step 'Dashboard enlace movimientos' $hasMovLink
    Test-Step 'Dashboard enlace saldos' $hasSaldoLink

    $api = Invoke-Txp -Method GET -Url "$base/api/admin/active-drivers" -Session $adminSess
    Test-Step 'API active-drivers' ($api.Code -eq 200) "HTTP $($api.Code)"

    $adminPaths = @(
        'users', 'conductores', 'empresas', 'referidos', 'viajes', 'walletsolicitudes',
        'walletmovimientos', 'walletsaldos', 'notificaciones'
    )
    foreach ($p in $adminPaths) {
        try {
            $r = Invoke-WebRequest -Uri "$base/$p" -WebSession $adminSess -UseBasicParsing -TimeoutSec 120 -MaximumRedirection 5
            $ok = $r.StatusCode -eq 200 -and $r.Content -notmatch 'Server Error|500 Internal'
            Test-Step "Admin /$p" $ok "HTTP $($r.StatusCode)"
        } catch {
            $code = if ($_.Exception.Response) { [int]$_.Exception.Response.StatusCode } else { 0 }
            Test-Step "Admin /$p" $false "HTTP $code"
        }
    }
}

# --- Pasajero ---
$pasSess = New-Object Microsoft.PowerShell.Commands.WebRequestSession
$pLogin = Do-Login $pasSess "$base/pasajero/login" '3009001001' 'pasajero'
Test-Step 'Pasajero login' (($pLogin.Code -eq 302) -and ($pLogin.Body -notmatch 'no correctos|Page Expired')) "HTTP $($pLogin.Code)"
if ($pLogin.Code -eq 302) {
    $pHome = Invoke-WebRequest -Uri "$base/home" -WebSession $pasSess -UseBasicParsing -TimeoutSec 120 -MaximumRedirection 5
    Test-Step 'Pasajero /home' ($pHome.StatusCode -eq 200) "HTTP $($pHome.StatusCode)"
    foreach ($p in @('pasajero/viajes', 'pasajero/wallet', 'pasajero/perfil')) {
        try {
            $r = Invoke-WebRequest -Uri "$base/$p" -WebSession $pasSess -UseBasicParsing -TimeoutSec 120 -MaximumRedirection 5
            Test-Step "Pasajero /$p" ($r.StatusCode -eq 200) "HTTP $($r.StatusCode)"
        } catch { Test-Step "Pasajero /$p" $false }
    }
}

# --- Conductor ---
$drvSess = New-Object Microsoft.PowerShell.Commands.WebRequestSession
$dLogin = Do-Login $drvSess "$base/conductor/login" '3109001001' 'conductor'
Test-Step 'Conductor login' (($dLogin.Code -eq 302) -and ($dLogin.Body -notmatch 'no correctos|Page Expired')) "HTTP $($dLogin.Code)"
if ($dLogin.Code -eq 302) {
    $dHome = Invoke-WebRequest -Uri "$base/home" -WebSession $drvSess -UseBasicParsing -TimeoutSec 120 -MaximumRedirection 5
    Test-Step 'Conductor /home' ($dHome.StatusCode -eq 200) "HTTP $($dHome.StatusCode)"
    foreach ($p in @('conductor/viajes', 'conductor/wallet', 'conductor/cuenta')) {
        try {
            $r = Invoke-WebRequest -Uri "$base/$p" -WebSession $drvSess -UseBasicParsing -TimeoutSec 120 -MaximumRedirection 5
            Test-Step "Conductor /$p" ($r.StatusCode -eq 200) "HTTP $($r.StatusCode)"
        } catch { Test-Step "Conductor /$p" $false }
    }
}

# --- Empresa ---
$empSess = New-Object Microsoft.PowerShell.Commands.WebRequestSession
$eLogin = Do-Login $empSess "$base/empresa/login" '3209002001' 'empresa'
Test-Step 'Empresa login' (($eLogin.Code -eq 302) -and ($eLogin.Body -notmatch 'no correctos|Page Expired')) "HTTP $($eLogin.Code)"
if ($eLogin.Code -eq 302) {
    $eHome = Invoke-WebRequest -Uri "$base/empresa" -WebSession $empSess -UseBasicParsing -TimeoutSec 120 -MaximumRedirection 5
    Test-Step 'Empresa /dashboard' ($eHome.StatusCode -eq 200) "HTTP $($eHome.StatusCode)"
    foreach ($p in @('empresa/flota', 'empresa/wallet', 'empresa/cuenta')) {
        try {
            $r = Invoke-WebRequest -Uri "$base/$p" -WebSession $empSess -UseBasicParsing -TimeoutSec 120 -MaximumRedirection 5
            Test-Step "Empresa /$p" ($r.StatusCode -eq 200) "HTTP $($r.StatusCode)"
        } catch { Test-Step "Empresa /$p" $false }
    }
}

# --- Firebase sync ---
try {
    $fbBody = @{ email='pasajero.demo1@taxpiya.com'; password=$password; returnSecureToken=$true } | ConvertTo-Json
    $fb = Invoke-RestMethod -Method POST -Uri "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=$apiKey" -Body $fbBody -ContentType 'application/json' -TimeoutSec 60
    Test-Step 'Firebase signIn' ($null -ne $fb.idToken)
    if ($fb.idToken) {
        $syncSess = New-Object Microsoft.PowerShell.Commands.WebRequestSession
        $sPage = Invoke-Txp -Method GET -Url "$base/pasajero/login" -Session $syncSess
        $st = Get-Csrf $sPage.Body
        $syncJson = @{ id_token = $fb.idToken; app = 'pasajero'; name = 'Pasajero Demo 1'; telefono = '3009001001' } | ConvertTo-Json
        $sync = Invoke-WebRequest -Method POST -Uri "$base/auth/firebase/sync" -WebSession $syncSess `
            -Body $syncJson -ContentType 'application/json' `
            -Headers @{ 'X-CSRF-TOKEN' = $st; Accept = 'application/json'; Referer = "$base/pasajero/login" } `
            -UseBasicParsing -TimeoutSec 120
        $syncData = $sync.Content | ConvertFrom-Json
        Test-Step 'Firebase sync' ($syncData.ok -eq $true) ($syncData.message)
    }
} catch {
    Test-Step 'Firebase sync' $false $_.Exception.Message
}

Write-Host ""
if ($failed -eq 0) { Write-Host "TODOS OK ($failed fallos)" -ForegroundColor Green; exit 0 }
else { Write-Host "$failed fallos" -ForegroundColor Red; exit 1 }
