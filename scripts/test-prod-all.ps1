# Verificación E2E producción Taxpiya
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
        $r = Invoke-WebRequest @params
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

Write-Host "Verificando $base`n"

# Diag
$diag = Invoke-RestMethod -Uri "$base/auth/firebase/diag" -TimeoutSec 120
Test-Step 'Firebase diag' ($diag.session_driver -eq 'database') "session_driver=$($diag.session_driver)"

# --- Admin login ---
$adminSess = New-Object Microsoft.PowerShell.Commands.WebRequestSession
$loginPage = Invoke-Txp -Method GET -Url "$base/index/login" -Session $adminSess
Test-Step 'Admin login page' ($loginPage.Code -eq 200) "HTTP $($loginPage.Code)"

$token = ''
if ($loginPage.Body -match 'name="_token"\s+value="([^"]+)"') { $token = $Matches[1] }
Test-Step 'Admin CSRF token' ($token.Length -gt 10) "len=$($token.Length)"

$body = "_token=$([uri]::EscapeDataString($token))&username=$([uri]::EscapeDataString('3001001001'))&password=$([uri]::EscapeDataString($password))&rememberme=true"
$loginA = Invoke-Txp -Method POST -Url "$base/auth/login" -Session $adminSess -Body $body -Headers @{ Referer = "$base/index/login" }
$aOk = ($loginA.Code -eq 302 -or $loginA.Code -eq 200) -and ($loginA.Body -notmatch 'no correctos|Page Expired|CSRF')
Test-Step 'Admin login POST' $aOk "HTTP $($loginA.Code)"

if ($aOk) {
    $homeA = Invoke-WebRequest -Uri "$base/home" -WebSession $adminSess -UseBasicParsing -TimeoutSec 120
    $homeOk = ($homeA.StatusCode -eq 200) -and ($homeA.Content -match 'Admin|admin')
    Test-Step 'Admin /home' $homeOk "HTTP $($homeA.StatusCode) len=$($homeA.Content.Length)"
    if ($homeA.StatusCode -ge 500) { Write-Host $homeA.Content.Substring(0, [Math]::Min(800, $homeA.Content.Length)) }
}

# --- Pasajero Laravel login ---
$pasSess = New-Object Microsoft.PowerShell.Commands.WebRequestSession
$pPage = Invoke-Txp -Method GET -Url "$base/pasajero/login" -Session $pasSess
if ($pPage.Body -match 'name="_token"\s+value="([^"]+)"') { $pt = $Matches[1] } else { $pt = '' }
$pbody = "_token=$([uri]::EscapeDataString($pt))&username=3009001001&password=$([uri]::EscapeDataString($password))&app=pasajero&rememberme=true"
$pLogin = Invoke-Txp -Method POST -Url "$base/auth/login" -Session $pasSess -Body $pbody -Headers @{ Referer = "$base/pasajero/login" }
Test-Step 'Pasajero login' (($pLogin.Code -eq 302) -and ($pLogin.Body -notmatch 'no correctos|Page Expired')) "HTTP $($pLogin.Code)"

# --- Firebase sync ---
$fbBody = @{ email='pasajero.demo1@taxpiya.com'; password=$password; returnSecureToken=$true } | ConvertTo-Json
$fb = Invoke-RestMethod -Method POST -Uri "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=$apiKey" -Body $fbBody -ContentType 'application/json' -TimeoutSec 60
Test-Step 'Firebase signIn' ($null -ne $fb.idToken) ($fb.error.message)

if ($fb.idToken) {
    $syncSess = New-Object Microsoft.PowerShell.Commands.WebRequestSession
    $sPage = Invoke-Txp -Method GET -Url "$base/pasajero/login" -Session $syncSess
    if ($sPage.Body -match 'name="_token"\s+value="([^"]+)"') { $st = $Matches[1] } else { $st = '' }
    $syncJson = @{ id_token = $fb.idToken; app = 'pasajero'; name = 'Pasajero Demo 1'; telefono = '3009001001' } | ConvertTo-Json
    try {
        $sync = Invoke-WebRequest -Method POST -Uri "$base/auth/firebase/sync" -WebSession $syncSess `
            -Body $syncJson -ContentType 'application/json' `
            -Headers @{ 'X-CSRF-TOKEN' = $st; Accept = 'application/json'; Referer = "$base/pasajero/login" } `
            -UseBasicParsing -TimeoutSec 120
        $syncData = $sync.Content | ConvertFrom-Json
        Test-Step 'Firebase sync' ($syncData.ok -eq $true) ($syncData.message)
    } catch {
        $code = [int]$_.Exception.Response.StatusCode
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $errBody = $reader.ReadToEnd()
        Test-Step 'Firebase sync' $false "HTTP $code - $errBody"
    }
}

# --- Empresa ---
$empSess = New-Object Microsoft.PowerShell.Commands.WebRequestSession
$ePage = Invoke-Txp -Method GET -Url "$base/empresa/login" -Session $empSess
if ($ePage.Body -match 'name="_token"\s+value="([^"]+)"') { $et = $Matches[1] } else { $et = '' }
$ebody = "_token=$([uri]::EscapeDataString($et))&username=3209002001&password=$([uri]::EscapeDataString($password))&app=empresa&rememberme=true"
$eLogin = Invoke-Txp -Method POST -Url "$base/auth/login" -Session $empSess -Body $ebody -Headers @{ Referer = "$base/empresa/login" }
Test-Step 'Empresa login' (($eLogin.Code -eq 302) -and ($eLogin.Body -notmatch 'no correctos|Page Expired')) "HTTP $($eLogin.Code)"

Write-Host ""
if ($failed -eq 0) { Write-Host "TODOS OK" -ForegroundColor Green; exit 0 }
else { Write-Host "$failed fallos" -ForegroundColor Red; exit 1 }
