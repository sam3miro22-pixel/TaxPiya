# E2E: referidor + referido + bono $5000 en produccion
$base = if ($env:TAXPIYA_BASE_URL) { $env:TAXPIYA_BASE_URL } else { 'https://taxpiya.onrender.com' }
$apiKey = if ($env:FIREBASE_API_KEY) { $env:FIREBASE_API_KEY } else { 'AIzaSyCIT1YV8eJRzmf7HSQbWe_7OzftDD1Vcnk' }
$password = 'TaxpiyaTest2026!'

function Get-Csrf($session) {
    $page = Invoke-WebRequest -Uri "$base/pasajero/registro" -WebSession $session -UseBasicParsing -TimeoutSec 120
    if ($page.Content -match 'csrf-token"\s+content="([^"]+)"') { return $Matches[1] }
    if ($page.Content -match 'name="_token"\s+value="([^"]+)"') { return $Matches[1] }
    throw 'No CSRF token'
}

function Firebase-SignUp($email) {
    $body = @{ email = $email; password = $password; returnSecureToken = $true } | ConvertTo-Json
    return Invoke-RestMethod -Method Post -Uri "https://identitytoolkit.googleapis.com/v1/accounts:signUp?key=$apiKey" `
        -ContentType 'application/json' -Body $body -TimeoutSec 120
}

function Sync-Pasajero($session, $csrf, $idToken, $extra) {
    $body = @{
        id_token = $idToken
        app      = 'pasajero'
        name     = 'Test E2E'
        is_register = $true
    }
    foreach ($k in $extra.Keys) { $body[$k] = $extra[$k] }
    $json = $body | ConvertTo-Json
    try {
        return Invoke-RestMethod -Method Post -Uri "$base/auth/firebase/sync" -WebSession $session `
            -ContentType 'application/json' -Headers @{
                'X-CSRF-TOKEN'     = $csrf
                'X-Requested-With' = 'XMLHttpRequest'
                'Accept'           = 'application/json'
            } -Body $json -TimeoutSec 120
    } catch {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        throw "Sync fail: $($reader.ReadToEnd())"
    }
}

Write-Host "=== E2E Referido + Bono ==="

$refEmail = "ref_a_$([guid]::NewGuid().ToString('N').Substring(0,8))@taxpiya-test.local"
$referredEmail = "ref_b_$([guid]::NewGuid().ToString('N').Substring(0,8))@taxpiya-test.local"

$session = New-Object Microsoft.PowerShell.Commands.WebRequestSession
$csrf = Get-Csrf $session

Write-Host "1) Crear referidor: $refEmail"
$refSignup = Firebase-SignUp $refEmail
$refSync = Sync-Pasajero $session $csrf $refSignup.idToken @{ telefono = "398$((Get-Random -Min 1000000 -Max 9999999))" }
$referrerId = [int]$refSync.user_id
$refCode = "TXP-P$referrerId"
Write-Host "   referrer_id=$referrerId code=$refCode sync=$($refSync | ConvertTo-Json -Compress)"

$valid = Invoke-RestMethod -Uri "$base/api/referral/validate?code=$refCode" -TimeoutSec 60
if (-not $valid.ok) { Write-Host "FAIL validate code"; exit 1 }
Write-Host "2) Codigo valido OK"

$session2 = New-Object Microsoft.PowerShell.Commands.WebRequestSession
$csrf2 = Get-Csrf $session2
Write-Host "3) Crear referido: $referredEmail con ref=$refCode"
$referredSignup = Firebase-SignUp $referredEmail
$referredSync = Sync-Pasajero $session2 $csrf2 $referredSignup.idToken @{
    telefono       = "397$((Get-Random -Min 1000000 -Max 9999999))"
    referral_code  = $refCode
}
Write-Host "   referred sync=$($referredSync | ConvertTo-Json -Compress)"

Start-Sleep -Seconds 2
$status = Invoke-RestMethod -Uri "$base/api/referral/user-status?email=$([uri]::EscapeDataString($refEmail))" -TimeoutSec 60
Write-Host "4) Estado referidor:"
$status | ConvertTo-Json -Depth 6

$saldo = [double]($status.wallet.saldo)
$activos = [int]($status.stats.activos)
if ($activos -lt 1) { Write-Host "FAIL: sin referidos activos"; exit 1 }
if ($saldo -lt 5000) { Write-Host "FAIL: saldo $saldo (esperado >= 5000)"; exit 1 }

Write-Host "`nOK - referido activo y bono acreditado ($saldo COP)"
exit 0
