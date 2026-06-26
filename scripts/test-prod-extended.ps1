# Pruebas extendidas: login telefono, assistant, roles
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
    $page = Invoke-WebRequest -Uri $loginUrl -WebSession $sess -UseBasicParsing -TimeoutSec 120 -ErrorAction SilentlyContinue
    $token = Get-Csrf $page.Content
    $body = "_token=$([uri]::EscapeDataString($token))&username=$([uri]::EscapeDataString($phone))&password=$([uri]::EscapeDataString($script:password))&rememberme=true&app=$([uri]::EscapeDataString($app))"
    try {
        $r = Invoke-WebRequest -Method POST -Uri "$script:base/auth/login" -WebSession $sess -Body $body -UseBasicParsing -MaximumRedirection 0 -TimeoutSec 120 -ErrorAction Stop
        return @{ Ok = ($r.StatusCode -eq 302); Code = $r.StatusCode; Location = $r.Headers['Location'] }
    } catch {
        $resp = $_.Exception.Response
        if ($resp -and [int]$resp.StatusCode -eq 302) {
            return @{ Ok = $true; Code = 302; Location = $resp.Headers['Location'] }
        }
        return @{ Ok = $false; Code = 0; Detail = $_.Exception.Message }
    }
}

Write-Host "=== TaxPiya pruebas extendidas $base ===`n"

# Login pages show phone form
foreach ($pair in @(
    @{ Url = "$base/pasajero/login"; Name = 'Pasajero login form' },
    @{ Url = "$base/conductor/login"; Name = 'Conductor login form' },
    @{ Url = "$base/empresa/login"; Name = 'Empresa login form' },
    @{ Url = "$base/index/login"; Name = 'Admin login form' }
)) {
    try {
        $p = Invoke-WebRequest -Uri $pair.Url -UseBasicParsing -TimeoutSec 120
        Test-Step $pair.Name (($p.Content -match 'name="username"') -and ($p.Content -notmatch 'fbOnly')) "HTTP $($p.StatusCode)"
    } catch {
        Test-Step $pair.Name $false $_.Exception.Message
    }
}

# Phone login all demo roles
$roles = @(
    @{ App = 'pasajero'; Phone = '3009001001'; Login = "$base/pasajero/login"; Home = "$base/home" },
    @{ App = 'conductor'; Phone = '3109001001'; Login = "$base/conductor/login"; Home = "$base/home" },
    @{ App = 'empresa'; Phone = '3209002001'; Login = "$base/empresa/login"; Home = "$base/empresa" },
    @{ App = $null; Phone = '3001001001'; Login = "$base/index/login"; Home = "$base/home" }
)

foreach ($r in $roles) {
    $sess = New-Object Microsoft.PowerShell.Commands.WebRequestSession
    $res = Do-PhoneLogin $sess $r.Login $r.Phone $r.App
    Test-Step "Phone login $($r.App ?? 'admin')" $res.Ok "HTTP $($res.Code)"
    if ($res.Ok) {
        try {
            $home = Invoke-WebRequest -Uri $r.Home -WebSession $sess -UseBasicParsing -TimeoutSec 120
            Test-Step "Home $($r.App ?? 'admin')" ($home.StatusCode -eq 200) "HTTP $($home.StatusCode)"
            Test-Step "Assistant widget $($r.App ?? 'admin')" ($home.Content -match 'txp-assistant-fab') 'fab present'
            if ($home.Content -match 'csrf-token') {
                $csrf = Get-Csrf $home.Content
                $assistBody = '{"message":"Hola, necesito ayuda con un viaje"}'
                try {
                    $ar = Invoke-RestMethod -Method POST -Uri "$base/assistant/send" -WebSession $sess `
                        -ContentType 'application/json' -Body $assistBody `
                        -Headers @{ 'X-CSRF-TOKEN' = $csrf; Accept = 'application/json'; 'X-Requested-With' = 'XMLHttpRequest' } -TimeoutSec 120
                    Test-Step "Assistant reply $($r.App ?? 'admin')" ($ar.ok -eq $true -and $ar.reply.Length -gt 5) ($ar.reply.Substring(0, [Math]::Min(60, $ar.reply.Length)) + '...')
                } catch {
                    Test-Step "Assistant reply $($r.App ?? 'admin')" $false $_.Exception.Message
                }
            }
        } catch {
            Test-Step "Home $($r.App ?? 'admin')" $false $_.Exception.Message
        }
    }
}

# Firebase pasajero still works
$fbSess = New-Object Microsoft.PowerShell.Commands.WebRequestSession
try {
    $fb = Invoke-RestMethod -Method POST -Uri "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=$apiKey" `
        -Body (@{ email = 'pasajero.demo1@taxpiya.com'; password = $password; returnSecureToken = $true } | ConvertTo-Json) `
        -ContentType 'application/json' -TimeoutSec 60
    $sPage = Invoke-WebRequest -Uri "$base/pasajero/login" -WebSession $fbSess -UseBasicParsing -TimeoutSec 120
    $st = Get-Csrf $sPage.Content
    $sync = Invoke-RestMethod -Method POST -Uri "$base/auth/firebase/sync" -WebSession $fbSess `
        -Body (@{ id_token = $fb.idToken; app = 'pasajero' } | ConvertTo-Json) -ContentType 'application/json' `
        -Headers @{ 'X-CSRF-TOKEN' = $st; Accept = 'application/json'; 'X-Requested-With' = 'XMLHttpRequest' } -TimeoutSec 120
    Test-Step 'Firebase pasajero sync' ($sync.ok -eq $true) $sync.message
} catch {
    Test-Step 'Firebase pasajero sync' $false $_.Exception.Message
}

Write-Host "`n=== Resultado: $($failed) fallos ==="
exit $failed
