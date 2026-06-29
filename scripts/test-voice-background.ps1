# Verifica integración micrófono + segundo plano (voz y burbuja)
$ErrorActionPreference = "Stop"
$root = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
$base = if ($env:TAXPIYA_URL) { $env:TAXPIYA_URL.TrimEnd('/') } else { "https://taxpiya.onrender.com" }

$fail = 0
function Assert($cond, $msg) {
    if (-not $cond) { Write-Host "FAIL: $msg" -ForegroundColor Red; $script:fail++ }
    else { Write-Host "OK: $msg" -ForegroundColor Green }
}

Write-Host "`n=== TaxPiya voice + background checks ===" -ForegroundColor Cyan

# Archivos locales
$voiceJs = Join-Path $root "public\js\taxpiya-voice.js"
$bgJs = Join-Path $root "public\js\taxpiya-background.js"
$pasajero = Join-Path $root "resources\views\pages\home\pasajero.blade.php"
$conductor = Join-Path $root "resources\views\pages\home\conductor.blade.php"

Assert (Test-Path $voiceJs) "taxpiya-voice.js existe"
Assert (Test-Path $bgJs) "taxpiya-background.js existe"

$voiceContent = Get-Content $voiceJs -Raw
Assert ($voiceContent -match "TaxpiyaVoice") "TaxpiyaVoice exportado"
Assert ($voiceContent -match "speechPlugin") "fallback Capacitor SpeechRecognition"

$bgContent = Get-Content $bgJs -Raw
Assert ($bgContent -match "syncConductor") "TxpBackground.syncConductor"
Assert ($bgContent -match "txp-bg-bubble") "UI burbuja in-app"

$pasContent = Get-Content $pasajero -Raw
Assert ($pasContent -match "taxpiya-voice\.js") "pasajero incluye taxpiya-voice.js"
Assert ($pasContent -match "taxpiya-background\.js") "pasajero incluye taxpiya-background.js"
Assert ($pasContent -match "TaxpiyaVoice\.bind") "pasajero usa TaxpiyaVoice.bind"
Assert ($pasContent -notmatch "function setupVoice") "setupVoice antigua eliminada"

$condContent = Get-Content $conductor -Raw
Assert ($condContent -match "taxpiya-background\.js") "conductor incluye taxpiya-background.js"
Assert ($condContent -notmatch "pagehide.*markDriverOfflineOnLeave") "conductor NO marca offline en pagehide"
Assert ($condContent -match "__txpConductorBgActive") "conductor expone flag BG watcher"
Assert ($condContent -match "TxpBackground\.init\('conductor'\)") "conductor inicializa TxpBackground"

$manifest = Join-Path $root "mobile\android\app\src\main\AndroidManifest.xml"
if (Test-Path $manifest) {
    $man = Get-Content $manifest -Raw
    Assert ($man -match "RECORD_AUDIO") "AndroidManifest RECORD_AUDIO"
}

# Producción (assets desplegados)
Write-Host "`n--- Producción: $base ---" -ForegroundColor Cyan

function Get-Csrf($html) {
    if ($html -match 'name="_token"\s+value="([^"]+)"') { return $Matches[1] }
    if ($html -match 'csrf-token"\s+content="([^"]+)"') { return $Matches[1] }
    return ''
}

function Login-Role($app, $phone) {
    $sess = New-Object Microsoft.PowerShell.Commands.WebRequestSession
    $loginUrl = switch ($app) {
        'conductor' { "$base/conductor/login" }
        default { "$base/pasajero/login" }
    }
    $page = Invoke-WebRequest -Uri $loginUrl -WebSession $sess -UseBasicParsing -TimeoutSec 120
    $token = Get-Csrf $page.Content
    $pw = 'Taxpiya2026!'
    $body = "_token=$([uri]::EscapeDataString($token))&username=$([uri]::EscapeDataString($phone))&password=$([uri]::EscapeDataString($pw))&rememberme=true&app=$([uri]::EscapeDataString($app))"
    Invoke-WebRequest -Method POST -Uri "$base/auth/login" -WebSession $sess -Body $body -UseBasicParsing -MaximumRedirection 5 -TimeoutSec 120 | Out-Null
    return $sess
}

try {
    $rVoice = Invoke-WebRequest -Uri "$base/js/taxpiya-voice.js?v=1" -UseBasicParsing -TimeoutSec 60
    Assert ($rVoice.StatusCode -eq 200) "prod taxpiya-voice.js HTTP 200"
    Assert ($rVoice.Content -match "TaxpiyaVoice") "prod voice JS válido"
} catch {
    Assert $false "prod taxpiya-voice.js: $($_.Exception.Message)"
}

try {
    $rBg = Invoke-WebRequest -Uri "$base/js/taxpiya-background.js?v=1" -UseBasicParsing -TimeoutSec 60
    Assert ($rBg.StatusCode -eq 200) "prod taxpiya-background.js HTTP 200"
    Assert ($rBg.Content -match "txp-bg-bubble") "prod background JS válido"
} catch {
    Assert $false "prod taxpiya-background.js: $($_.Exception.Message)"
}

try {
  $pSess = Login-Role 'pasajero' '3009001001'
  $pHome = Invoke-WebRequest -Uri "$base/home" -WebSession $pSess -UseBasicParsing -TimeoutSec 120
  Assert ($pHome.Content -match "taxpiya-voice\.js") "prod pasajero carga voice JS"
  Assert ($pHome.Content -match "TaxpiyaVoice\.bind") "prod pasajero usa TaxpiyaVoice.bind"
  Assert ($pHome.Content -notmatch "function setupVoice") "prod sin setupVoice vieja"
} catch {
  Assert $false "prod pasajero page: $($_.Exception.Message)"
}

try {
  $cSess = Login-Role 'conductor' '3109001001'
  $cond = Invoke-WebRequest -Uri "$base/home" -WebSession $cSess -UseBasicParsing -TimeoutSec 60
  Assert ($cond.Content -match "taxpiya-background\.js") "prod conductor carga background JS"
  Assert ($cond.Content -notmatch "addEventListener\('pagehide',\s*markDriverOfflineOnLeave\)") "prod conductor sin pagehide offline"
} catch {
  Assert $false "prod conductor page: $($_.Exception.Message)"
}

Write-Host "`nResultado: $($fail) fallo(s)" -ForegroundColor $(if ($fail -eq 0) { "Green" } else { "Red" })
if ($fail -gt 0) { exit 1 }
