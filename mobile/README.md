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

Si Google muestra **Error 403: disallowed_useragent**, instala la **última APK** (v1.0.1+). Usa inicio nativo con Google, no WebView.

En [Firebase Console](https://console.firebase.google.com/) → proyecto **tax-piya** → Configuración → Tus apps → Android `com.taxpiya.pasajero`:

1. Agrega la huella **SHA-1** del keystore debug (para pruebas):
   ```powershell
   keytool -list -v -keystore $env:USERPROFILE\.android\debug.keystore -alias androiddebugkey -storepass android
   ```
2. Descarga de nuevo `google-services.json` si Firebase lo pide y cópialo a `mobile/android/app/`.

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
