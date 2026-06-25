# Crea las 2 cuentas Firebase canónicas (pasajero + conductor).
# Admin y empresa usan login Laravel, no Firebase.
$apiKey = if ($env:FIREBASE_API_KEY) { $env:FIREBASE_API_KEY } else { 'AIzaSyCIT1YV8eJRzmf7HSQbWe_7OzftDD1Vcnk' }
$password = if ($env:TAXPIYA_DEMO_PASSWORD) { $env:TAXPIYA_DEMO_PASSWORD } else { 'Taxpiya2026!' }

$accounts = @(
    @{ email = 'pasajero.demo1@taxpiya.com'; label = 'Pasajero' },
    @{ email = 'conductor.demo1@taxpiya.com'; label = 'Conductor' }
)

function Invoke-FirebaseSignUp {
    param([string]$Email, [string]$Password, [string]$ApiKey)
    $body = @{
        email             = $Email
        password          = $Password
        returnSecureToken = $true
    } | ConvertTo-Json

    try {
        return Invoke-RestMethod -Method Post `
            -Uri "https://identitytoolkit.googleapis.com/v1/accounts:signUp?key=$ApiKey" `
            -ContentType 'application/json' -Body $body -TimeoutSec 120
    } catch {
        $raw = $_.ErrorDetails.Message
        $err = $null
        try { $err = $raw | ConvertFrom-Json } catch {}
        if ($err.error.message -eq 'EMAIL_EXISTS') {
            $signInBody = @{
                email             = $Email
                password          = $Password
                returnSecureToken = $true
            } | ConvertTo-Json
            return Invoke-RestMethod -Method Post `
                -Uri "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=$ApiKey" `
                -ContentType 'application/json' -Body $signInBody -TimeoutSec 120
        }
        throw
    }
}

Write-Host "=== TaxPiya seed Firebase canonico (pasajero + conductor) ==="
Write-Host "Contraseña: $password"
Write-Host ""

$failed = 0
foreach ($acc in $accounts) {
    Write-Host "--- $($acc.label): $($acc.email) ---"
    try {
        $res = Invoke-FirebaseSignUp -Email $acc.email -Password $password -ApiKey $apiKey
        Write-Host "OK uid=$($res.localId)"
    } catch {
        Write-Host "FAIL: $($_.Exception.Message)"
        $failed++
    }
}

Write-Host ""
if ($failed -gt 0) {
    Write-Host "Completado con $failed error(es)."
    exit 1
}
Write-Host "OK - Firebase tiene pasajero + conductor demo."
exit 0
