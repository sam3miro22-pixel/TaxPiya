# Taxpiya — APK unificada (Android)

Una sola app para **Pasajero**, **Conductor** y **Empresa / Flota**.

Al abrirla carga la pantalla principal de Render (`https://taxpiya.onrender.com`) con los tres botones de entrada, igual que en la web.

## Ubicación del proyecto

```
Taxpiya/mobile/
├── capacitor.config.json   # URL de producción + plugins
├── www/                    # Pantalla local de respaldo
├── android/                # Proyecto Android (Gradle)
└── scripts/build-apk.ps1   # Compilar APK en Windows
```

## APK compilada

Tras compilar, la APK queda en:

```
Taxpiya/mobile/android/app/build/outputs/apk/debug/app-debug.apk
```

## Google Sign-In en la APK

Firebase pide la **huella SHA-1** (solo números y dos puntos), **no** el comando `keytool`.

### Huellas para pegar en Firebase Console

Proyecto **tax-piya** → Configuración → App Android `com.taxpiya.pasajero` → **Agregar huella digital**:

| Uso | SHA-1 |
|-----|-------|
| APK v1.0.1 (ya instalada) | `84:25:5C:7E:8B:A8:EC:2A:ED:71:2B:F3:18:98:29:E4:39:BA:09:93` |
| APK v1.0.2+ (keystore fijo) | `27:F5:AB:4F:A3:8B:03:6B:5B:DB:F8:B3:0D:61:8B:30:F8:37:E3:77` |

Agrega **las dos** si no estás seguro de qué APK tienes. Luego pulsa **Guardar**.

Para ver huellas de un APK local:

```powershell
powershell -File mobile\scripts\print-sha1.ps1
```

## Compilar en Windows (requiere Java 17+ y Android SDK)

### 1. Instalar dependencias

- [Android Studio](https://developer.android.com/studio) (incluye JDK y Android SDK)
- O JDK 17: `winget install Microsoft.OpenJDK.17`

### 2. Variables de entorno (si no usas Android Studio)

```powershell
$env:JAVA_HOME = "C:\Program Files\Microsoft\jdk-17.0.x"
$env:ANDROID_HOME = "$env:LOCALAPPDATA\Android\Sdk"
$env:PATH += ";$env:ANDROID_HOME\platform-tools;$env:ANDROID_HOME\tools"
```

### 3. Compilar

```powershell
cd Taxpiya\mobile
npm install
npx cap sync android
powershell -File scripts\build-apk.ps1
```

## Compilar con GitHub Actions

En el repo, ve a **Actions → Build Taxpiya APK → Run workflow**.  
Descarga el artefacto `taxpiya-unified-apk`.

## Instalar en el teléfono

1. Copia `app-debug.apk` al móvil.
2. Activa **Instalar apps desconocidas** para tu gestor de archivos.
3. Abre la APK e instala.
4. Si ya tenías instalada la APK antigua de pasajero (`com.taxpiya.pasajero`), se actualizará.

## Qué hace la app

| Rol | Ruta al elegir |
|-----|----------------|
| Pasajero | `/pasajero/login` |
| Conductor | `/conductor/login` |
| Empresa | `/empresa/login` |

Incluye permisos de GPS, notificaciones y cámara para mapas, viajes y perfil.

## Cambiar URL del servidor

Edita `mobile/capacitor.config.json`:

```json
"server": {
  "url": "https://taxpiya.onrender.com"
}
```

Luego: `npx cap sync android` y recompila.

## Notas

- Requiere internet (carga la web de Render).
- Push nativo FCM usa el proyecto Firebase `tax-piya` (package `com.taxpiya.pasajero`).
- Para APK firmada de Play Store, configura keystore en `android/keystore.properties`.
