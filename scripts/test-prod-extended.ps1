# Pruebas extendidas TaxPiya
$base = if ($env:TAXPIYA_BASE_URL) { $env:TAXPIYA_BASE_URL } else { 'https://taxpiya.onrender.com' }
$password = 'Taxpiya2026!'
$apiKey = if ($env:FIREBASE_API_KEY) { $env:FIREBASE_API_KEY } else { 'AIzaSyCIT1YV8eJRzmf7HSQbWe_7OzftDD1Vcnk' }
$failed = 0

function Test-Step($name, $pass, $detail = '') {
    if ($pass) { Write-Host "[OK] $name $(if($detail){'- ' + $detail})" -ForegroundColor Green }
    else { Write-Host "[FAIL] $name $(if($detail){'- ' + $detail})" -ForegroundColor Red; $script:failed++ }
}

function Get-Csrf($html) {
    if ($html -match 'name="_token"\s+value="([^"]+)"') { return $Matches[1] }
    if ($html -match 'csrf-token"\s+content="([^"]+)"') { return $Matches[1] }
    return ''
}

function Do-PhoneLogin($sess, $loginUrl, $phone, $app) {
    $page = Invoke-WebRequest -Uri $loginUrl -WebSession $sess -UseBasicParsing -TimeoutSec 120
    $token = Get-Csrf $page.Content
    $body = "_token=$([uri]::EscapeDataString($token))&username=$([uri]::EscapeDataString($phone))&password=$([uri]::EscapeDataString($script:password))&rememberme=true"
    if ($app) { $body += "&app=$([uri]::EscapeDataString($app))" }
    $r = Invoke-WebRequest -Method POST -Uri "$script:base/auth/login" -WebSession $sess -Body $body -UseBasicParsing -MaximumRedirection 5 -TimeoutSec 120
    $ok = ($r.StatusCode -eq 200) -and ($r.Content -notmatch 'no correctos|auth_error|Nombre de usuario')
    return @{ Ok = $ok; Code = $r.StatusCode }
}

Write-Host "=== TaxPiya pruebas extendidas $base ===`n"

try {
    $diag = Invoke-RestMethod -Uri "$base/assistant/diag" -TimeoutSec 60
    Test-Step 'Assistant diag' ($diag.ok -eq $true -and $diag.version -eq 'assistant-v5') "v=$($diag.version) groq=$($diag.groq) curl=$($diag.curl)"
} catch {
    Test-Step 'Assistant diag' $false $_.Exception.Message
}

foreach ($pair in @(
    @{ Url = "$base/pasajero/login"; Name = 'Pasajero login form' },
    @{ Url = "$base/conductor/login"; Name = 'Conductor login form' },
    @{ Url = "$base/empresa/login"; Name = 'Empresa login form' }
)) {
    $p = Invoke-WebRequest -Uri $pair.Url -UseBasicParsing -TimeoutSec 120
    Test-Step $pair.Name (($p.Content -match 'name="username"')) "HTTP $($p.StatusCode)"
}

$roles = @(
    @{ App = 'pasajero'; Phone = '3009001001'; Login = "$base/pasajero/login"; Dest = "$base/home" },
    @{ App = 'conductor'; Phone = '3109001001'; Login = "$base/conductor/login"; Dest = "$base/home" },
    @{ App = 'empresa'; Phone = '3209002001'; Login = "$base/empresa/login"; Dest = "$base/empresa" }
)

foreach ($r in $roles) {
    $label = $r.App
    $sess = New-Object Microsoft.PowerShell.Commands.WebRequestSession
    $res = Do-PhoneLogin $sess $r.Login $r.Phone $r.App
    Test-Step "Phone login $label" $res.Ok "HTTP $($res.Code)"
    if ($res.Ok) {
        $destPage = Invoke-WebRequest -Uri $r.Dest -WebSession $sess -UseBasicParsing -TimeoutSec 120
        Test-Step "Home $label" ($destPage.StatusCode -eq 200) "HTTP $($destPage.StatusCode)"
        Test-Step "Assistant widget $label" ($destPage.Content -match 'txp-assistant-fab') 'fab'
        $csrf = Get-Csrf $destPage.Content
        $body = "_token=$([uri]::EscapeDataString($csrf))&message=$([uri]::EscapeDataString('Hola, necesito ayuda con un viaje'))"
        $ar = Invoke-WebRequest -Method POST -Uri "$base/assistant/send" -WebSession $sess -Body $body -UseBasicParsing -TimeoutSec 120
        $parsed = $ar.Content | ConvertFrom-Json
        $preview = if ($parsed.reply.Length -gt 50) { $parsed.reply.Substring(0, 50) + '...' } else { $parsed.reply }
        Test-Step "Assistant reply $label" ($parsed.ok -eq $true -and $parsed.reply.Length -gt 5) $preview
    }
}

Write-Host "`n=== Resultado: $failed fallos ==="
exit $failed
