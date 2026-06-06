# TaxPiya — 100% GRATIS (sin db4free, sin cPanel)

## Arquitectura

| Componente | Servicio | Costo |
|--------------|----------|-------|
| Backend Laravel | **Render Free** | $0 |
| Base de datos | **SQLite incluida en el repo** | $0 |
| Usuarios / login | **Firebase Auth** (Google + correo) | $0 |
| Tiempo real viajes | **Firestore** (espejo de viajes) | $0 |
| Push | **Firebase FCM** | $0 |

No necesitas registrarte en ninguna web de base de datos.

---

## Render — variables de entorno (copiar tal cual)

```
APP_NAME=Taxpiya
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:CEbK0CwSiZfUxXtFFYUp2xbEtqRYfE5+CBHI2AxXOXQ=
APP_URL=https://TU-APP.onrender.com
FRONTEND_URL=https://TU-APP.onrender.com
LOG_CHANNEL=stderr

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/taxpiya.sqlite

JWT_SECRET=tUQE3gB0xYP6WwATu4slJGbDMy1SXCc2ahKRi8rI
JWT_DURATION=240
JWT_ALGORITHM=HS256

GOOGLE_MAPS_KEY=AIzaSyDOMjE-UJftefUXUZJSIvLbo5FdnxB8yL8

FIREBASE_PROJECT_ID=tax-piya
FIREBASE_API_KEY=AIzaSyCIT1YV8eJRzmf7HSQbWe_7OzftDD1Vcnk
FIREBASE_AUTH_DOMAIN=tax-piya.firebaseapp.com
FIREBASE_STORAGE_BUCKET=tax-piya.firebasestorage.app
FIREBASE_MESSAGING_SENDER_ID=902300007872
FIREBASE_APP_ID=1:902300007872:web:3872bd28d8a36b8dffdc25
TAXPIYA_USE_FIRESTORE=true
TAXPIYA_USE_FIREBASE_AUTH=true
FCM_SCOPE=prod
FIREBASE_CREDENTIALS_JSON={...service account JSON en una linea...}
```

---

## Pasos en Render

1. **New +** → **Web Service**
2. Repo: **sam3miro22-pixel/TaxPiya**
3. Runtime: **Docker** | Plan: **Free**
4. Pega las variables de arriba
5. **Create Web Service**
6. Cuando esté Live, actualiza `APP_URL` y `FRONTEND_URL` con tu URL real

---

## Firebase Console

Authentication → Authorized domains → agrega: `tu-app.onrender.com`

Habilita proveedores: **Email/Password** y **Google**

---

## Cómo iniciar sesión

### Opción 1 — Firebase (recomendado, usuarios nuevos)
- Clic en **Google** o **Correo (Firebase)** en login pasajero
- Se crea cuenta automáticamente

### Opción 2 — Usuarios del backup (SQL importado)
- Pasajero: `3017954934` / `Taxpiya123`
- Conductor: `3208254627` / `Taxpiya123`

---

## Probar

- Pasajero: `https://TU-URL.onrender.com/pasajero/login`
- Conductor: `https://TU-URL.onrender.com/conductor/login`

---

## Nota plan Free Render

Tras 15 min sin uso la app duerme; el primer acceso tarda ~1 minuto.
