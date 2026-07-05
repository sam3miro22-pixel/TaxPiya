# =============================================================================
# TAXPIYA — PRUEBA COMPLETA 0 A 100 — Todos los roles y funciones
# =============================================================================
$base     = 'https://taxpiya.onrender.com'
$password = 'Taxpiya2026!'
$apiKey   = 'AIzaSyCIT1YV8eJRzmf7HSQbWe_7OzftDD1Vcnk'

$pass = 0; $fail = 0; $warn = 0
$results = [System.Collections.Generic.List[object]]::new()

$and = [char]38

function ReportResult($cat, $name, $ok, $detail = '') {
    $status = if ($ok -eq $true) { 'OK' } elseif ($ok -eq $null) { 'WARN' } else { 'FAIL' }
    $color  = if ($status -eq 'OK') { 'Green' } elseif ($status -eq 'WARN') { 'Yellow' } else { 'Red' }
    Write-Host "  [$status] $name$(if($detail){ ' - ' + $detail })" -ForegroundColor $color
    $script:results.Add([PSCustomObject]@{ Cat=$cat; Name=$name; Status=$status; Detail=$detail })
    if ($status -eq 'OK')   { $script:pass++ }
    elseif ($status -eq 'WARN') { $script:warn++ }
    else                    { $script:fail++ }
}

function WR($method, $url, $sess = $null, $body = $null, $ct = 'application/x-www-form-urlencoded', $json = $false) {
    $p = @{ Uri=$url; Method=$method; UseBasicParsing=$true; MaximumRedirection=5; ErrorAction='SilentlyContinue'; TimeoutSec=60 }
    if ($sess)  { $p.WebSession = $sess }
    if ($body)  { $p.Body = $body; $p.ContentType = $ct }
    if ($json)  { $p.Headers = @{ Accept='application/json'; 'X-Requested-With'='XMLHttpRequest' } }
    try {
        $r = Invoke-WebRequest @p
        return @{ Code=[int]$r.StatusCode; Body=$r.Content; Ok=($r.StatusCode -lt 400) }
    } catch {
        $resp = $_.Exception.Response
        if ($resp) {
            try { $bdy = (New-Object System.IO.StreamReader($resp.GetResponseStream())).ReadToEnd() } catch { $bdy = '' }
            return @{ Code=[int]$resp.StatusCode; Body=$bdy; Ok=$false }
        }
        return @{ Code=0; Body=$_.Exception.Message; Ok=$false }
    }
}

function CSRF($html) {
    if ($html -match 'name="([_]token)"\s+value="([^"]+)"') { return $Matches[2] }
    if ($html -match 'csrf-token"\s+content="([^"]+)"')     { return $Matches[1] }
    return ''
}

function LoginPhone($app, $phone) {
    $sess = New-Object Microsoft.PowerShell.Commands.WebRequestSession
    $loginUrl = switch ($app) {
        'admin'    { "$base/index/login" }
        'empresa'  { "$base/empresa/login" }
        'conductor'{ "$base/conductor/login" }
        default    { "$base/pasajero/login" }
    }
    $page  = WR GET $loginUrl $sess
    $token = CSRF $page.Body
    $bdy   = "_token=$([uri]::EscapeDataString($token))" + $script:and + "username=$([uri]::EscapeDataString($phone))" + $script:and + "password=$([uri]::EscapeDataString($password))" + $script:and + "rememberme=true"
    if ($app -ne 'admin') { $bdy += $script:and + "app=$([uri]::EscapeDataString($app))" }
    $r = WR POST "$base/auth/login" $sess $bdy
    return @{ Ok=($r.Code -eq 200 -and $r.Body -notmatch 'no correctos|Page Expired|CSRF'); Sess=$sess; Code=$r.Code; Body=$r.Body }
}

function FBLogin($app, $email, $phone) {
    $sess = New-Object Microsoft.PowerShell.Commands.WebRequestSession
    $loginUrl = "$base/$app/login"
    $fbBody = @{ email=$email; password=$password; returnSecureToken=$true } | ConvertTo-Json
    try {
        $fb = Invoke-RestMethod -Method POST -Uri "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=$apiKey" -Body $fbBody -ContentType 'application/json' -TimeoutSec 60
    } catch { return @{ Ok=$false; Detail=$_.Exception.Message; Sess=$sess } }
    if (-not $fb.idToken) { return @{ Ok=$false; Detail='no idToken'; Sess=$sess } }
    $page  = WR GET $loginUrl $sess
    $token = CSRF $page.Body
    $payload = (@{ id_token=$fb.idToken; app=$app; telefono=$phone } | ConvertTo-Json)
    try {
        $sync = Invoke-WebRequest -Method POST -Uri "$base/auth/firebase/sync" -WebSession $sess `
            -Body $payload -ContentType 'application/json' `
            -Headers @{ 'X-CSRF-TOKEN'=$token; Accept='application/json'; 'X-Requested-With'='XMLHttpRequest' } `
            -UseBasicParsing -TimeoutSec 60
        $data = $sync.Content | ConvertFrom-Json
        return @{ Ok=($data.ok -eq $true); Detail=$data.message; Sess=$sess; Code=[int]$sync.StatusCode }
    } catch {
        return @{ Ok=$false; Detail=$_.Exception.Message; Sess=$sess }
    }
}

# ============================================================
Write-Host "`n=========================================" -ForegroundColor Cyan
Write-Host "  TAXPIYA - PRUEBA COMPLETA 0 A 100" -ForegroundColor Cyan
Write-Host "  $base" -ForegroundColor Cyan
Write-Host "=========================================`n" -ForegroundColor Cyan

# ============================================================
Write-Host "=== [1] INFRAESTRUCTURA Y SERVIDOR ===" -ForegroundColor Magenta

$root = WR GET $base
ReportResult 'Infra' 'Servidor responde (200)' ($root.Code -eq 200) "HTTP $($root.Code)"
ReportResult 'Infra' 'Redirige a login/home' ($root.Body -match 'login|Taxpiya|taxpi') 'contenido OK'

$diag = $null
try { $diag = Invoke-RestMethod -Uri "$base/auth/firebase/diag" -TimeoutSec 60; ReportResult 'Infra' 'Firebase diag endpoint' ($diag.ok -eq $true -or $diag.firebase_auth -ne $null) "session=$($diag.session_driver)" } catch { ReportResult 'Infra' 'Firebase diag endpoint' $false $_.Exception.Message }

$tarifa = $null
try {
    $qParams = @('categoria=taxi', 'ciudad=Medellin', 'o_lat=6.24', 'o_lng=-75.56', 'd_lat=6.25', 'd_lng=-75.57') -join $and
    $tarifa = Invoke-RestMethod -Uri "$base/tarifa-fija?$qParams" -TimeoutSec 60
    ReportResult 'Infra' 'API Tarifa por distancia' ($tarifa.ok -eq $true -and $tarifa.monto -gt 0) "monto=$($tarifa.monto) km=$($tarifa.km)"
} catch { ReportResult 'Infra' 'API Tarifa por distancia' $false $_.Exception.Message }

# ============================================================
Write-Host "`n=== [2] PAGINAS PUBLICAS ===" -ForegroundColor Magenta

@(
    @{ Url="$base/pasajero/login"; Name='Pasajero login page'; Check='username' },
    @{ Url="$base/conductor/login"; Name='Conductor login page'; Check='username' },
    @{ Url="$base/empresa/login"; Name='Empresa login page'; Check='username' },
    @{ Url="$base/index/login"; Name='Admin login page'; Check='username' },
    @{ Url="$base/info/guia-roles"; Name='Guia de roles'; Check='rol' }
) | ForEach-Object {
    $r = WR GET $_.Url
    ReportResult 'Public' $_.Name ($r.Code -eq 200 -and $r.Body -match $_.Check) "HTTP $($r.Code)"
}

$forgotR = WR GET "$base/auth/password/forgotpassword"
ReportResult 'Public' 'Recuperar contrasena desactivado' ($forgotR.Code -ne 200 -or $forgotR.Body -notmatch 'Recuperar contrasena|password.reset') "HTTP $($forgotR.Code)"

# ============================================================
Write-Host "`n=== [3] ADMIN - Login, Dashboard, Secciones ===" -ForegroundColor Magenta

$aLogin = LoginPhone 'admin' '3001001001'
ReportResult 'Admin' 'Admin login (celular/contrasena)' $aLogin.Ok "HTTP $($aLogin.Code)"
$aSess = $aLogin.Sess

if ($aLogin.Ok) {
    $aHome = WR GET "$base/home" $aSess
    ReportResult 'Admin' 'Admin /home (dashboard)' ($aHome.Code -eq 200) "HTTP $($aHome.Code)"
    ReportResult 'Admin' 'Dashboard: KPI Usuarios' ($aHome.Body -match 'Usuarios\s*totales|kpi-value') ''
    ReportResult 'Admin' 'Dashboard: Mapa conductores activos' ($aHome.Body -match 'leaflet|initMap|map') ''
    ReportResult 'Admin' 'Dashboard: Boton cerrar sesion' ($aHome.Body -match 'auth/logout|Cerrar sesion') ''
    ReportResult 'Admin' 'Dashboard: Widget Asistente' ($aHome.Body -match 'txp-assistant-fab') ''

    @('users','conductores','empresas','viajes','referidos',
      'walletsolicitudes','walletmovimientos','walletsaldos',
      'notificaciones','tarifas','sosincidentes') | ForEach-Object {
        $r = WR GET "$base/$_" $aSess
        ReportResult 'Admin' "Admin /$_" ($r.Code -eq 200 -and $r.Body -notmatch '500|Server Error') "HTTP $($r.Code)"
    }

    # API Admin endpoints
    $apiAD = WR GET "$base/api/admin/active-drivers" $aSess
    ReportResult 'Admin' 'API /api/admin/active-drivers' ($apiAD.Code -eq 200) "HTTP $($apiAD.Code)"

    # Tarifa admin
    $tarAdmin = WR GET "$base/tarifas" $aSess
    ReportResult 'Admin' 'Admin /tarifas list' ($tarAdmin.Code -eq 200) "HTTP $($tarAdmin.Code)"
}

# ============================================================
Write-Host "`n=== [4] PASAJERO - Login, Home, Billetera, Viajes, SOS ===" -ForegroundColor Magenta

$pLogin = FBLogin 'pasajero' 'pasajero.demo1@taxpiya.com' '3009001001'
ReportResult 'Pasajero' 'Pasajero Firebase login (Google/email)' $pLogin.Ok "HTTP $($pLogin.Code) $($pLogin.Detail)"
$pSess = $pLogin.Sess

if ($pLogin.Ok) {
    $pHome = WR GET "$base/home" $pSess
    ReportResult 'Pasajero' 'Pasajero /home (mapa)' ($pHome.Code -eq 200) "HTTP $($pHome.Code)"
    ReportResult 'Pasajero' 'Pasajero: menu hamburguesa (qmToggle)' ($pHome.Body -match 'id="qmToggle"') ''
    ReportResult 'Pasajero' 'Pasajero: input direccion origen' ($pHome.Body -match 'origen|destino|autocomplete') ''
    ReportResult 'Pasajero' 'Pasajero: boton Solicitar Viaje' ($pHome.Body -match 'solicitar-btn|Solicitar Viaje') ''
    ReportResult 'Pasajero' 'Pasajero: boton SOS visible' ($pHome.Body -match 'txp-sos-btn|txp-sos-fab') ''
    ReportResult 'Pasajero' 'Pasajero: z-index SOS (10005)' ($pHome.Body -match '10005') ''
    ReportResult 'Pasajero' 'Pasajero: microfono (TaxpiyaVoice)' ($pHome.Body -match 'TaxpiyaVoice|taxpiya-voice') ''
    ReportResult 'Pasajero' 'Pasajero: background JS' ($pHome.Body -match 'taxpiya-background\.js') ''
    ReportResult 'Pasajero' 'Pasajero: modal codigo llegada' ($pHome.Body -match 'txp-code-modal') ''
    ReportResult 'Pasajero' 'Pasajero: widget Asistente' ($pHome.Body -match 'txp-assistant-fab') ''

    @('pasajero/viajes','pasajero/wallet','pasajero/perfil','pasajero/referidos') | ForEach-Object {
        $r = WR GET "$base/$_" $pSess
        ReportResult 'Pasajero' "Pasajero /$_" ($r.Code -eq 200) "HTTP $($r.Code)"
    }

    # API: viaje activo
    try {
        $av = Invoke-RestMethod -Uri "$base/viaje/activo" -WebSession $pSess -TimeoutSec 60 -Headers @{ Accept='application/json' }
        ReportResult 'Pasajero' 'API /viaje/activo pasajero' ($av.ok -eq $true) "estado=$($av.estado)"
    } catch { ReportResult 'Pasajero' 'API /viaje/activo pasajero' $false $_.Exception.Message }

    # API: solicitar tarifa
    try {
        $cs = CSRF $pHome.Body
        $sBody = "_token=$([uri]::EscapeDataString($cs))" + $script:and + "origen_lat=6.2442" + $script:and + "origen_lng=-75.5733" + $script:and + "destino_lat=6.2519" + $script:and + "destino_lng=-75.5635" + $script:and + "origen_texto=El+Centro" + $script:and + "destino_texto=El+Poblado" + $script:and + "categoria=taxi"
        $sr = WR POST "$base/viaje/solicitar" $pSess $sBody
        $sJson = $sr.Body | ConvertFrom-Json -ErrorAction SilentlyContinue
        ReportResult 'Pasajero' 'API /viaje/solicitar (pasajero sin saldo)' ($sr.Code -eq 402 -or $sr.Code -eq 200 -or $sr.Code -eq 201 -or ($sJson -and ($sJson.ok -eq $true -or $sJson.message))) "HTTP $($sr.Code)"
    } catch { ReportResult 'Pasajero' 'API /viaje/solicitar' $null 'Error en request' }

    # SOS endpoint
    try {
        $cs2 = CSRF $pHome.Body
        $sosBody = "_token=$([uri]::EscapeDataString($cs2))" + $script:and + "lat=6.244" + $script:and + "lng=-75.573" + $script:and + "descripcion=Test+SOS"
        $sosR = WR POST "$base/api/sos/reportar" $pSess $sosBody
        ReportResult 'Pasajero' 'API /api/sos/reportar pasajero' ($sosR.Code -eq 200 -or $sosR.Code -eq 201) "HTTP $($sosR.Code)"
    } catch { ReportResult 'Pasajero' 'API /api/sos/reportar' $false $_.Exception.Message }

    # Wallet deposit request
    $wPage = WR GET "$base/pasajero/wallet" $pSess
    ReportResult 'Pasajero' 'Wallet: formulario de recarga/deposito' ($wPage.Body -match 'deposito|recargar|billetera') "HTTP $($wPage.Code)"
    ReportResult 'Pasajero' 'Wallet: saldo visible' ($wPage.Body -match 'saldo|balance|COP|\$') ''

    # Referido
    $refPage = WR GET "$base/pasajero/referidos" $pSess
    ReportResult 'Pasajero' 'Referidos: codigo visible' ($refPage.Body -match 'TXP-P|codigo|referido') "HTTP $($refPage.Code)"
}

# Test login con celular directo (sin Firebase)
$pLogin2 = LoginPhone 'pasajero' '3009001001'
ReportResult 'Pasajero' 'Pasajero login (celular/contrasena directo)' $pLogin2.Ok "HTTP $($pLogin2.Code)"

# ============================================================
Write-Host "`n=== [5] CONDUCTOR - Login, Home, Aceptar, Wallet, SOS ===" -ForegroundColor Magenta

$dLogin = FBLogin 'conductor' 'conductor.demo1@taxpiya.com' '3109001001'
ReportResult 'Conductor' 'Conductor Firebase login' $dLogin.Ok "HTTP $($dLogin.Code) $($dLogin.Detail)"
$dSess = $dLogin.Sess

if ($dLogin.Ok) {
    $dHome = WR GET "$base/home" $dSess
    ReportResult 'Conductor' 'Conductor /home (mapa)' ($dHome.Code -eq 200) "HTTP $($dHome.Code)"
    ReportResult 'Conductor' 'Conductor: boton DISPONIBLE/toggle' ($dHome.Body -match 'driver-online-toggle|DISPONIBLE') ''
    ReportResult 'Conductor' 'Conductor: boton SOS (z-index 10005)' ($dHome.Body -match '10005|txp-sos-btn') ''
    ReportResult 'Conductor' 'Conductor: sheet solicitud Aceptar/Rechazar' ($dHome.Body -match 'drv-btn-aceptar|drv-btn-rechazar') ''
    ReportResult 'Conductor' 'Conductor: padding safe-area sheet' ($dHome.Body -match 'safe-area-inset-bottom') ''
    ReportResult 'Conductor' 'Conductor: input codigo llegada' ($dHome.Body -match 'txp-drv-code-input|drv-codigo-llegada') ''
    ReportResult 'Conductor' 'Conductor: background JS' ($dHome.Body -match 'taxpiya-background\.js') ''
    ReportResult 'Conductor' 'Conductor: ruta a pasajero (Google/Waze)' ($dHome.Body -match 'txd-open-google|txd-open-waze') ''
    ReportResult 'Conductor' 'Conductor: widget Asistente' ($dHome.Body -match 'txp-assistant-fab') ''

    @('conductor/viajes','conductor/wallet','conductor/cuenta') | ForEach-Object {
        $r = WR GET "$base/$_" $dSess
        ReportResult 'Conductor' "Conductor /$_" ($r.Code -eq 200) "HTTP $($r.Code)"
    }

    # API viaje activo conductor
    try {
        $avc = Invoke-RestMethod -Uri "$base/viaje/activo" -WebSession $dSess -TimeoutSec 60 -Headers @{ Accept='application/json' }
        ReportResult 'Conductor' 'API /viaje/activo conductor' ($avc.ok -eq $true) "estado=$($avc.estado)"
    } catch { ReportResult 'Conductor' 'API /viaje/activo conductor' $false $_.Exception.Message }

    # API disponible (el fix de WalletService)
    $cs = CSRF $dHome.Body
    $dispJson  = '{"disponible":true}'
    try {
        $dispR = Invoke-WebRequest -Method POST -Uri "$base/conductor/disponible" -WebSession $dSess -Body $dispJson -ContentType 'application/json' -Headers @{ 'X-CSRF-TOKEN'=$cs; Accept='application/json'; 'X-Requested-With'='XMLHttpRequest' } -UseBasicParsing -TimeoutSec 60
        $dispData = $dispR.Content | ConvertFrom-Json
        ReportResult 'Conductor' 'API /conductor/disponible (poner en linea - FIX WalletService)' ($dispData.ok -eq $true -or $dispR.StatusCode -eq 200) "ok=$($dispData.ok) msg=$($dispData.message)"
    } catch { ReportResult 'Conductor' 'API /conductor/disponible' $false $_.Exception.Message }

    # API solicitud de viaje (poll)
    try {
        $solR = Invoke-WebRequest -Uri "$base/conductor/solicitud" -WebSession $dSess -Headers @{ Accept='application/json'; 'X-Requested-With'='XMLHttpRequest' } -UseBasicParsing -TimeoutSec 60
        ReportResult 'Conductor' 'API /conductor/solicitud (poll viajes)' ($solR.StatusCode -eq 200 -or $solR.StatusCode -eq 204 -or $solR.StatusCode -eq 404) "HTTP $($solR.StatusCode)"
    } catch { ReportResult 'Conductor' 'API /conductor/solicitud' $false $_.Exception.Message }

    # SOS conductor
    try {
        $cs2 = CSRF $dHome.Body
        $sosBody = "_token=$([uri]::EscapeDataString($cs2))" + $script:and + "lat=6.244" + $script:and + "lng=-75.573" + $script:and + "descripcion=Test+SOS+conductor"
        $sosR = WR POST "$base/api/sos/reportar" $dSess $sosBody
        ReportResult 'Conductor' 'API /api/sos/reportar conductor' ($sosR.Code -eq 200 -or $sosR.Code -eq 201) "HTTP $($sosR.Code)"
    } catch { ReportResult 'Conductor' 'API /api/sos/reportar conductor' $false $_.Exception.Message }

    # Wallet conductor
    $wdPage = WR GET "$base/conductor/wallet" $dSess
    ReportResult 'Conductor' 'Wallet conductor: saldo/movimientos' ($wdPage.Body -match 'saldo|balance|billetera') "HTTP $($wdPage.Code)"

    # Cuenta conductor
    $ccPage = WR GET "$base/conductor/cuenta" $dSess
    ReportResult 'Conductor' 'Cuenta conductor: datos personales' ($ccPage.Body -match 'nombre|telefono|cuenta') "HTTP $($ccPage.Code)"
}

# Test login celular conductor
$dLogin2 = LoginPhone 'conductor' '3109001001'
ReportResult 'Conductor' 'Conductor login (celular/contrasena directo)' $dLogin2.Ok "HTTP $($dLogin2.Code)"

# ============================================================
Write-Host "`n=== [6] EMPRESA - Login, Dashboard, Flota, Wallet, Contabilidad ===" -ForegroundColor Magenta

$eLogin = LoginPhone 'empresa' '3209002001'
ReportResult 'Empresa' 'Empresa login (celular)' $eLogin.Ok "HTTP $($eLogin.Code)"
$eSess = $eLogin.Sess

if ($eLogin.Ok) {
    $eHome = WR GET "$base/empresa" $eSess
    ReportResult 'Empresa' 'Empresa /dashboard' ($eHome.Code -eq 200) "HTTP $($eHome.Code)"
    ReportResult 'Empresa' 'Dashboard: KPIs empresa' ($eHome.Body -match 'flota|conductores|viajes|saldo') ''

    @('empresa/flota','empresa/wallet','empresa/cuenta','empresa/contabilidad') | ForEach-Object {
        $r = WR GET "$base/$_" $eSess
        ReportResult 'Empresa' "Empresa /$_" ($r.Code -eq 200) "HTTP $($r.Code)"
    }

    # API empresa: agregar conductor a flota
    $eF = WR GET "$base/empresa/flota" $eSess
    ReportResult 'Empresa' 'Flota: lista de conductores' ($eF.Body -match 'conductor|flota|celular') "HTTP $($eF.Code)"

    $eW = WR GET "$base/empresa/wallet" $eSess
    ReportResult 'Empresa' 'Wallet empresa: saldo/movimientos' ($eW.Body -match 'saldo|balance|billetera') "HTTP $($eW.Code)"

    $eCont = WR GET "$base/empresa/contabilidad" $eSess
    ReportResult 'Empresa' 'Contabilidad: ingresos/egresos' ($eCont.Body -match 'ingreso|egreso|total|comision') "HTTP $($eCont.Code)"
}

# ============================================================
Write-Host "`n=== [7] WALLET ADMIN - Depositos, Retiros, Saldos ===" -ForegroundColor Magenta

if ($aLogin.Ok) {
    $wSol = WR GET "$base/walletsolicitudes" $aSess
    ReportResult 'Wallet' 'Admin: solicitudes de deposito/retiro' ($wSol.Code -eq 200) "HTTP $($wSol.Code)"
    ReportResult 'Wallet' 'Admin: boton Aprobar deposito' ($wSol.Body -match 'Aprobar|aprobar|btn.*apro') ''

    $wMov = WR GET "$base/walletmovimientos" $aSess
    ReportResult 'Wallet' 'Admin: movimientos de billetera' ($wMov.Code -eq 200) "HTTP $($wMov.Code)"

    $wSal = WR GET "$base/walletsaldos" $aSess
    ReportResult 'Wallet' 'Admin: saldos por usuario' ($wSal.Code -eq 200) "HTTP $($wSal.Code)"
    ReportResult 'Wallet' 'Admin: filtro tipo usuario en saldos' ($wSal.Body -match 'conductor|pasajero|empresa') ''
}

# ============================================================
Write-Host "`n=== [8] ASISTENTE IA - Todos los roles ===" -ForegroundColor Magenta

try {
    $assistDiag = Invoke-RestMethod -Uri "$base/assistant/diag" -TimeoutSec 60
    ReportResult 'Asistente' 'Assistant /diag' ($assistDiag.ok -eq $true) "groq=$($assistDiag.groq)"
} catch { ReportResult 'Asistente' 'Assistant /diag' $false $_.Exception.Message }

if ($pLogin.Ok) {
    $cs = CSRF $pHome.Body
    $ab = "_token=$([uri]::EscapeDataString($cs))" + $script:and + "message=$([uri]::EscapeDataString('Como recargo mi billetera?'))"
    try {
        $ar = Invoke-WebRequest -Method POST -Uri "$base/assistant/send" -WebSession $pSess -Body $ab -UseBasicParsing -TimeoutSec 60
        $ap = $ar.Content | ConvertFrom-Json
        ReportResult 'Asistente' 'Asistente responde (pasajero)' ($ap.ok -eq $true -and $ap.reply.Length -gt 5) ($ap.reply.Substring(0, [Math]::Min(60, $ap.reply.Length)))
    } catch { ReportResult 'Asistente' 'Asistente responde (pasajero)' $false $_.Exception.Message }
}

if ($dLogin.Ok) {
    $cs = CSRF $dHome.Body
    $ab = "_token=$([uri]::EscapeDataString($cs))" + $script:and + "message=$([uri]::EscapeDataString('Como cobro un viaje?'))"
    try {
        $ar = Invoke-WebRequest -Method POST -Uri "$base/assistant/send" -WebSession $dSess -Body $ab -UseBasicParsing -TimeoutSec 60
        $ap = $ar.Content | ConvertFrom-Json
        ReportResult 'Asistente' 'Asistente responde (conductor)' ($ap.ok -eq $true -and $ap.reply.Length -gt 5) ($ap.reply.Substring(0, [Math]::Min(60, $ap.reply.Length)))
    } catch { ReportResult 'Asistente' 'Asistente responde (conductor)' $false $_.Exception.Message }
}

# ============================================================
Write-Host "`n=== [9] APIs CORE - Conductor posicion, estado viaje ===" -ForegroundColor Magenta

if ($dLogin.Ok) {
    $csD = CSRF $dHome.Body
    # Enviar posicion GPS
    $posJson = '{"lat":6.2442,"lng":-75.5733,"heading":90,"velocidad_kmh":30}'
    try {
        $posR = Invoke-WebRequest -Method POST -Uri "$base/conductor/posicion" -WebSession $dSess `
            -Body $posJson -ContentType 'application/json' `
            -Headers @{ 'X-CSRF-TOKEN'=$csD; Accept='application/json'; 'X-Requested-With'='XMLHttpRequest' } `
            -UseBasicParsing -TimeoutSec 30
        ReportResult 'API' 'API /conductor/posicion (GPS update)' ($posR.StatusCode -eq 200) "HTTP $($posR.StatusCode)"
    } catch { ReportResult 'API' 'API /conductor/posicion' $false $_.Exception.Message }
}

# Verificar referrals API
try {
    $refV = Invoke-RestMethod -Uri "$base/api/referral/validate?code=TXP-P1" -TimeoutSec 30
    ReportResult 'API' 'API /api/referral/validate' ($refV -ne $null) "ok=$($refV.ok)"
} catch { ReportResult 'API' 'API /api/referral/validate' $false $_.Exception.Message }

# ============================================================
Write-Host "`n=== [10] SEGURIDAD - Acceso sin sesion ===" -ForegroundColor Magenta

@(
    @{ Url="$base/home";              Name='Protegido: /home sin login' },
    @{ Url="$base/users";             Name='Protegido: /users sin login' },
    @{ Url="$base/conductor/wallet";  Name='Protegido: conductor wallet sin login' },
    @{ Url="$base/empresa";           Name='Protegido: empresa sin login' },
    @{ Url="$base/pasajero/wallet";   Name='Protegido: pasajero wallet sin login' }
) | ForEach-Object {
    $r = WR GET $_.Url
    $protected = ($r.Code -eq 302 -or $r.Code -eq 401 -or ($r.Code -eq 200 -and $r.Body -match 'login|Iniciar sesion'))
    ReportResult 'Seguridad' $_.Name $protected "HTTP $($r.Code)"
}

# ============================================================
# RESUMEN FINAL
# ============================================================
Write-Host "`n`n=========================================" -ForegroundColor Cyan
Write-Host "  RESUMEN FINAL DE PRUEBAS" -ForegroundColor Cyan
Write-Host "=========================================`n" -ForegroundColor Cyan

$cats = $results | Select-Object -ExpandProperty Cat -Unique
foreach ($cat in $cats) {
    $catR = $results | Where-Object { $_.Cat -eq $cat }
    $catOk   = ($catR | Where-Object { $_.Status -eq 'OK'   }).Count
    $catFail = ($catR | Where-Object { $_.Status -eq 'FAIL' }).Count
    $catWarn = ($catR | Where-Object { $_.Status -eq 'WARN' }).Count
    $total   = $catR.Count
    $color = if ($catFail -eq 0) { 'Green' } elseif ($catFail -le 2) { 'Yellow' } else { 'Red' }
    Write-Host ("  {0,-15} OK:{1,3}  FAIL:{2,3}  WARN:{3,3}  Total:{4,3}" -f $cat, $catOk, $catFail, $catWarn, $total) -ForegroundColor $color
}

Write-Host ""
Write-Host ("  TOTAL: {0} OK  |  {1} FAIL  |  {2} WARN" -f $pass, $fail, $warn) -ForegroundColor $(if ($fail -eq 0) { 'Green' } elseif ($fail -le 5) { 'Yellow' } else { 'Red' })
Write-Host ""

if ($fail -gt 0) {
    Write-Host "--- FALLOS ---" -ForegroundColor Red
    $results | Where-Object { $_.Status -eq 'FAIL' } | ForEach-Object {
        Write-Host ("  [{0}] {1} - {2}" -f $_.Cat, $_.Name, $_.Detail) -ForegroundColor Red
    }
}

Write-Host ""
exit $fail
