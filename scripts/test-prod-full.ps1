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
    if ($html -match 'csrf-token"\s+content="([^"]+)"') { return $Matches[1] }
    return ''
}

function Do-Login($sess, $loginUrl, $username, $app = $null) {
    $page = Invoke-Txp -Method GET -Url $loginUrl -Session $sess
    $token = Get-Csrf $page.Body
    $body = "_token=$([uri]::EscapeDataString($token))&username=$([uri]::EscapeDataString($username))&password=$([uri]::EscapeDataString($password))&rememberme=true"
    if ($app) { $body += "&app=$([uri]::EscapeDataString($app))" }
    return Invoke-Txp -Method POST -Url "$base/auth/login" -Session $sess -Body $body -Headers @{ Referer = $loginUrl }
}

function Do-FirebaseLogin($sess, $loginUrl, $email, $app, $telefono = $null) {
    $fbBody = @{ email = $email; password = $script:password; returnSecureToken = $true } | ConvertTo-Json
    try {
        $fb = Invoke-RestMethod -Method POST -Uri "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=$script:apiKey" -Body $fbBody -ContentType 'application/json' -TimeoutSec 60
    } catch {
        return @{ Code = 0; Ok = $false; Detail = $_.Exception.Message }
    }
    if (-not $fb.idToken) {
        return @{ Code = 0; Ok = $false; Detail = 'sin idToken' }
    }
    $sPage = Invoke-Txp -Method GET -Url $loginUrl -Session $sess
    $st = Get-Csrf $sPage.Body
    $payload = @{ id_token = $fb.idToken; app = $app }
    if ($telefono) { $payload.telefono = $telefono }
    $syncJson = $payload | ConvertTo-Json
    try {
        $sync = Invoke-WebRequest -Method POST -Uri "$script:base/auth/firebase/sync" -WebSession $sess `
            -Body $syncJson -ContentType 'application/json' `
            -Headers @{ 'X-CSRF-TOKEN' = $st; Accept = 'application/json'; Referer = $loginUrl; 'X-Requested-With' = 'XMLHttpRequest' } `
            -UseBasicParsing -TimeoutSec 120
        $data = $sync.Content | ConvertFrom-Json
        return @{ Code = [int]$sync.StatusCode; Ok = ($data.ok -eq $true); Detail = $data.message; Body = $sync.Content }
    } catch {
        $resp = $_.Exception.Response
        if ($resp) {
            $reader = New-Object System.IO.StreamReader($resp.GetResponseStream())
            $body = $reader.ReadToEnd()
            return @{ Code = [int]$resp.StatusCode; Ok = $false; Detail = $body; Body = $body }
        }
        return @{ Code = 0; Ok = $false; Detail = $_.Exception.Message }
    }
}

Write-Host "=== Verificación completa $base ===`n"

# Firebase diag
try {
    $diag = Invoke-RestMethod -Uri "$base/auth/firebase/diag" -TimeoutSec 120
    Test-Step 'Firebase diag' ($diag.session_driver -eq 'database') "session_driver=$($diag.session_driver)"
    Test-Step 'Firebase diag version' ($diag.login_flow_version -eq 'firebase-sync-v12-closure') $diag.login_flow_version
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

# --- Pasajero (Firebase) ---
$pasSess = New-Object Microsoft.PowerShell.Commands.WebRequestSession
$pLogin = Do-FirebaseLogin $pasSess "$base/pasajero/login" 'pasajero.demo1@taxpiya.com' 'pasajero' '3009001001'
Test-Step 'Pasajero Firebase login' $pLogin.Ok "HTTP $($pLogin.Code) $($pLogin.Detail)"
if ($pLogin.Ok) {
    $pHome = Invoke-WebRequest -Uri "$base/home" -WebSession $pasSess -UseBasicParsing -TimeoutSec 120 -MaximumRedirection 5
    Test-Step 'Pasajero /home' ($pHome.StatusCode -eq 200) "HTTP $($pHome.StatusCode)"
    foreach ($p in @('pasajero/viajes', 'pasajero/wallet', 'pasajero/perfil')) {
        try {
            $r = Invoke-WebRequest -Uri "$base/$p" -WebSession $pasSess -UseBasicParsing -TimeoutSec 120 -MaximumRedirection 5
            Test-Step "Pasajero /$p" ($r.StatusCode -eq 200) "HTTP $($r.StatusCode)"
        } catch { Test-Step "Pasajero /$p" $false }
    }
}

# --- Conductor (Firebase) ---
$drvSess = New-Object Microsoft.PowerShell.Commands.WebRequestSession
$dLogin = Do-FirebaseLogin $drvSess "$base/conductor/login" 'conductor.demo1@taxpiya.com' 'conductor' '3109001001'
Test-Step 'Conductor Firebase login' $dLogin.Ok "HTTP $($dLogin.Code) $($dLogin.Detail)"
if ($dLogin.Ok) {
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
    foreach ($p in @('empresa/flota', 'empresa/wallet', 'empresa/cuenta', 'empresa/contabilidad')) {
        try {
            $r = Invoke-WebRequest -Uri "$base/$p" -WebSession $empSess -UseBasicParsing -TimeoutSec 120 -MaximumRedirection 5
            Test-Step "Empresa /$p" ($r.StatusCode -eq 200) "HTTP $($r.StatusCode)"
        } catch { Test-Step "Empresa /$p" $false }
    }
}

# --- Firebase sync (endpoint directo) ---
try {
    $fbBody = @{ email='pasajero.demo1@taxpiya.com'; password=$password; returnSecureToken=$true } | ConvertTo-Json
    $fb = Invoke-RestMethod -Method POST -Uri "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=$apiKey" -Body $fbBody -ContentType 'application/json' -TimeoutSec 60
    Test-Step 'Firebase signIn API' ($null -ne $fb.idToken)
    if ($fb.idToken) {
        $syncSess = New-Object Microsoft.PowerShell.Commands.WebRequestSession
        $syncResult = Do-FirebaseLogin $syncSess "$base/pasajero/login" 'pasajero.demo1@taxpiya.com' 'pasajero' '3009001001'
        Test-Step 'Firebase sync endpoint' $syncResult.Ok ($syncResult.Detail)
    }
} catch {
    Test-Step 'Firebase sync endpoint' $false $_.Exception.Message
}

# --- UI / Auth policy ---
try {
    $pasPage = Invoke-WebRequest -Uri "$base/pasajero/login" -UseBasicParsing -TimeoutSec 120
    Test-Step 'Pasajero login page' ($pasPage.StatusCode -eq 200) "HTTP $($pasPage.StatusCode)"
    Test-Step 'Pasajero Firebase UI (Google)' ($pasPage.Content -match 'Continuar con Google')
    Test-Step 'Pasajero form celular' ($pasPage.Content -match '<form[^>]*name="loginForm"')
    Test-Step 'Pasajero sin olvidé contraseña' ($pasPage.Content -notmatch 'Olvidaste|olvidaste|forgotpassword')
} catch { Test-Step 'Pasajero login page' $false }

try {
    $drvPage = Invoke-WebRequest -Uri "$base/conductor/login" -UseBasicParsing -TimeoutSec 120
    Test-Step 'Conductor login page' ($drvPage.StatusCode -eq 200) "HTTP $($drvPage.StatusCode)"
    Test-Step 'Conductor Firebase UI' ($drvPage.Content -match 'Continuar con Google')
    Test-Step 'Conductor form celular' ($drvPage.Content -match '<form[^>]*name="loginForm"')
} catch { Test-Step 'Conductor login page' $false }

try {
    $forgot = Invoke-Txp -Method GET -Url "$base/auth/password/forgotpassword"
    $forgotOk = ($forgot.Code -eq 302 -or $forgot.Code -eq 200) -and ($forgot.Body -notmatch 'Recuperar contraseña|Olvidaste')
    Test-Step 'Recuperar contraseña deshabilitado' $forgotOk "HTTP $($forgot.Code)"
} catch { Test-Step 'Recuperar contraseña deshabilitado' $false }

try {
    $guia = Invoke-WebRequest -Uri "$base/info/guia-roles" -UseBasicParsing -TimeoutSec 120
    Test-Step 'Guía de roles' ($guia.StatusCode -eq 200 -and $guia.Content -match 'guia-roles|roles</span>|Guía de') "HTTP $($guia.StatusCode)"
} catch { Test-Step 'Guía de roles' $false }

Write-Host ""
if ($failed -eq 0) { Write-Host "TODOS OK ($failed fallos)" -ForegroundColor Green; exit 0 }
else { Write-Host "$failed fallos" -ForegroundColor Red; exit 1 }
