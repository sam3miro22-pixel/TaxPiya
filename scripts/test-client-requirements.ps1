# Pruebas requisitos cliente TaxPiya
$base = if ($env:TAXPIYA_BASE_URL) { $env:TAXPIYA_BASE_URL } else { 'https://taxpiya.onrender.com' }
$pw = 'Taxpiya2026!'
$failed = 0

function Test-Ok($name, $pass, $detail = '') {
    if ($pass) { Write-Host "[OK] $name $(if($detail){"- $detail"})" -ForegroundColor Green }
    else { Write-Host "[FAIL] $name $(if($detail){"- $detail"})" -ForegroundColor Red; $script:failed++ }
}

function Get-Csrf($html) {
    if ($html -match 'name="_token"\s+value="([^"]+)"') { return $Matches[1] }
    if ($html -match 'csrf-token"\s+content="([^"]+)"') { return $Matches[1] }
    return ''
}

function Login-Role($app, $phone) {
    $sess = New-Object Microsoft.PowerShell.Commands.WebRequestSession
    $loginUrl = switch ($app) {
        'admin' { "$base/admin/login" }
        'empresa' { "$base/empresa/login" }
        'conductor' { "$base/conductor/login" }
        default { "$base/pasajero/login" }
    }
    $page = Invoke-WebRequest -Uri $loginUrl -WebSession $sess -UseBasicParsing -TimeoutSec 120
    $token = Get-Csrf $page.Content
    $body = "_token=$([uri]::EscapeDataString($token))&username=$([uri]::EscapeDataString($phone))&password=$([uri]::EscapeDataString($pw))&rememberme=true"
    if ($app -ne 'admin') { $body += "&app=$([uri]::EscapeDataString($app))" }
    Invoke-WebRequest -Method POST -Uri "$base/auth/login" -WebSession $sess -Body $body -UseBasicParsing -MaximumRedirection 5 -TimeoutSec 120 | Out-Null
    return $sess
}

Write-Host "=== TaxPiya pruebas requisitos cliente ===`n"

# Diag asistente + tarifa km
try {
    $diag = Invoke-RestMethod -Uri "$base/assistant/diag" -TimeoutSec 60
    Test-Ok 'Assistant diag' ($diag.ok -eq $true) "groq=$($diag.groq)"
} catch { Test-Ok 'Assistant diag' $false $_.Exception.Message }

try {
    $tar = Invoke-RestMethod -Uri "$base/tarifa-fija?categoria=taxi&ciudad=Medellin&o_lat=6.24&o_lng=-75.56&d_lat=6.25&d_lng=-75.57" -TimeoutSec 60
    Test-Ok 'Tarifa por distancia API' ($tar.ok -eq $true -and $tar.monto -gt 0) "monto=$($tar.monto) km=$($tar.km)"
} catch { Test-Ok 'Tarifa por distancia API' $false $_.Exception.Message }

# Pasajero: home, menu, wallet, viaje activo
$pSess = Login-Role 'pasajero' '3009001001'
$homePage = Invoke-WebRequest -Uri "$base/home" -WebSession $pSess -UseBasicParsing -TimeoutSec 120
Test-Ok 'Pasajero home' ($homePage.StatusCode -eq 200)
Test-Ok 'Menu pasajero (hamburguesa)' ($homePage.Content -match 'id="qmToggle"')
Test-Ok 'Bootstrap viaje activo JS' ($homePage.Content -match '__txpActiveTrip')
Test-Ok 'Modal codigo llegada' ($homePage.Content -match 'txp-code-modal')

try {
    $activo = Invoke-WebRequest -Uri "$base/viaje/activo" -WebSession $pSess -UseBasicParsing -TimeoutSec 60
    $aj = $activo.Content | ConvertFrom-Json
    Test-Ok 'API viaje/activo pasajero' ($aj.ok -eq $true)
} catch { Test-Ok 'API viaje/activo pasajero' $false $_.Exception.Message }

$wallet = Invoke-WebRequest -Uri "$base/pasajero/wallet" -WebSession $pSess -UseBasicParsing -TimeoutSec 120
Test-Ok 'Pasajero billetera' ($wallet.StatusCode -eq 200)

# Conductor
$cSess = Login-Role 'conductor' '3109001001'
$cHome = Invoke-WebRequest -Uri "$base/home" -WebSession $cSess -UseBasicParsing -TimeoutSec 120
Test-Ok 'Conductor home' ($cHome.StatusCode -eq 200)
Test-Ok 'Conductor resume viaje JS' ($cHome.Content -match '__txpActiveTrip')
Test-Ok 'Conductor codigo input grande' ($cHome.Content -match 'txp-drv-code-input')

try {
    $activoC = Invoke-WebRequest -Uri "$base/viaje/activo" -WebSession $cSess -UseBasicParsing -TimeoutSec 60
    $cj = $activoC.Content | ConvertFrom-Json
    Test-Ok 'API viaje/activo conductor' ($cj.ok -eq $true)
} catch { Test-Ok 'API viaje/activo conductor' $false $_.Exception.Message }

# Admin logout + tarifas
$aSess = Login-Role 'admin' '3001001001'
$admin = Invoke-WebRequest -Uri "$base/home" -WebSession $aSess -UseBasicParsing -TimeoutSec 120
Test-Ok 'Admin dashboard' ($admin.StatusCode -eq 200)
Test-Ok 'Admin boton cerrar sesion' ($admin.Content -match 'Cerrar sesión')

$tarifas = Invoke-WebRequest -Uri "$base/tarifas" -WebSession $aSess -UseBasicParsing -TimeoutSec 120
Test-Ok 'Admin tarifas list' ($tarifas.StatusCode -eq 200)

# Empresa
$eSess = Login-Role 'empresa' '3209002001'
$emp = Invoke-WebRequest -Uri "$base/empresa" -WebSession $eSess -UseBasicParsing -TimeoutSec 120
Test-Ok 'Empresa dashboard' ($emp.StatusCode -eq 200)

# Assistant con sesion
$csrf = Get-Csrf $home.Content
$ab = "_token=$([uri]::EscapeDataString($csrf))&message=$([uri]::EscapeDataString('Como recargo mi billetera?'))"
$ar = Invoke-WebRequest -Method POST -Uri "$base/assistant/send" -WebSession $pSess -Body $ab -UseBasicParsing -TimeoutSec 120
$ap = $ar.Content | ConvertFrom-Json
Test-Ok 'Assistant responde' ($ap.ok -eq $true -and $ap.reply.Length -gt 8) ($ap.reply.Substring(0, [Math]::Min(60, $ap.reply.Length)))

Write-Host "`n=== Resultado: $failed fallos ==="
exit $failed
