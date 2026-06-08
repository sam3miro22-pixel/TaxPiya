# Prueba registro Firebase + sync Laravel en producción
$base = if ($env:TAXPIYA_BASE_URL) { $env:TAXPIYA_BASE_URL } else { 'https://taxpiya.onrender.com' }
$apiKey = if ($env:FIREBASE_API_KEY) { $env:FIREBASE_API_KEY } else { 'AIzaSyCIT1YV8eJRzmf7HSQbWe_7OzftDD1Vcnk' }
$password = 'TaxpiyaTest2026!'
$email = "test_reg_{0}@taxpiya-test.local" -f ([guid]::NewGuid().ToString('N').Substring(0, 8))

Write-Host "=== Test registro Firebase + sync ==="
Write-Host "Email: $email"
Write-Host ""

$signupBody = @{
    email             = $email
    password          = $password
    returnSecureToken = $true
} | ConvertTo-Json

try {
    $signup = Invoke-RestMethod -Method Post -Uri "https://identitytoolkit.googleapis.com/v1/accounts:signUp?key=$apiKey" `
        -ContentType 'application/json' -Body $signupBody -TimeoutSec 120
} catch {
    $err = $_.ErrorDetails.Message | ConvertFrom-Json -ErrorAction SilentlyContinue
    Write-Host "FAIL Firebase signUp: $($err.error.message)"
    exit 1
}

$idToken = $signup.idToken
Write-Host "Firebase OK uid=$($signup.localId)"

$session = New-Object Microsoft.PowerShell.Commands.WebRequestSession
$page = Invoke-WebRequest -Uri "$base/pasajero/registro" -WebSession $session -TimeoutSec 120 -UseBasicParsing
$csrf = $null
if ($page.Content -match 'name="csrf-token"\s+content="([^"]+)"') {
    $csrf = $Matches[1]
} elseif ($page.Content -match 'name="_token"\s+value="([^"]+)"') {
    $csrf = $Matches[1]
}
if (-not $csrf) {
    Write-Host "FAIL: no CSRF token en pagina registro"
    exit 1
}

$syncBody = @{
    id_token    = $idToken
    app         = 'pasajero'
    name        = 'Test Registro'
    telefono    = "399$((Get-Random -Minimum 1000000 -Maximum 9999999))"
    is_register = $true
} | ConvertTo-Json

try {
    $sync = Invoke-RestMethod -Method Post -Uri "$base/auth/firebase/sync" -WebSession $session `
        -ContentType 'application/json' -Headers @{
            'X-CSRF-TOKEN'       = $csrf
            'X-Requested-With'   = 'XMLHttpRequest'
            'Accept'             = 'application/json'
            'Referer'            = "$base/pasajero/registro"
        } -Body $syncBody -TimeoutSec 120
    $ok = $sync.ok -eq $true
} catch {
    $resp = $_.Exception.Response
    $raw = ''
    if ($resp) {
        $reader = New-Object System.IO.StreamReader($resp.GetResponseStream())
        $raw = $reader.ReadToEnd()
    }
    if (-not $raw) { $raw = $_.ErrorDetails.Message }
    Write-Host "Sync Laravel FAIL (HTTP $($resp.StatusCode.value__)):"
    Write-Host $raw
    exit 1
}

$sync | ConvertTo-Json -Depth 5
if (-not $ok) {
    Write-Host "`nFAIL sync"
    exit 1
}

Write-Host "`nOK - registro completo"
exit 0
