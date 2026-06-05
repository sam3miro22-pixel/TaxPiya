# TaxPiya — 100% GRATIS (sin cPanel, sin hosting viejo)

Solo necesitas: **GitHub** + **Render Free** + **db4free.net** (MySQL gratis)

---

## PARTE A — Base de datos GRATIS (15 minutos)

### 1. Crear cuenta MySQL gratis
1. Abre: https://www.db4free.net/signup.php
2. Regístrate con **sam3miro22@gmail.com**
3. Confirma el correo que te envían

### 2. Crear la base de datos
1. Entra a: https://www.db4free.net/phpMyAdmin/
2. Login con tu usuario db4free
3. Clic en **New** (Nueva base de datos)
4. Nombre: `taxpiya` → Create

### 3. Importar los datos de TaxPiya
1. Selecciona la BD `taxpiya`
2. Pestaña **Import**
3. **Choose file** → sube el archivo:
   `database/sql/taxpiya48_718txps7.sql` (está en el repo de GitHub)
4. Clic **Go** / **Continuar** (puede tardar 1-2 min)

### 4. Anota estos datos (los necesitas en Render)

| Variable | Valor típico db4free |
|----------|----------------------|
| DB_HOST | `db4free.net` |
| DB_PORT | `3306` |
| DB_DATABASE | `taxpiya` |
| DB_USERNAME | tu usuario db4free |
| DB_PASSWORD | tu contraseña db4free |

---

## PARTE B — Render Web Service GRATIS

### 1. Crear servicio
1. https://dashboard.render.com
2. **New +** → **Web Service**
3. Conecta repo: **sam3miro22-pixel/TaxPiya**
4. Configuración:

| Campo | Valor |
|-------|--------|
| Name | `taxpiya` |
| Runtime | **Docker** |
| Instance Type | **Free** |
| Branch | `main` |

### 2. Variables de entorno (Environment)

Copia TODO esto y ajusta lo marcado con ⚠️:

```
APP_NAME=Taxpiya
APP_ENV=production
APP_DEBUG=false
APP_KEY=⚠️GENERA_CON_php_artisan_key_generate_show
APP_URL=https://taxpiya.onrender.com
FRONTEND_URL=https://taxpiya.onrender.com
LOG_CHANNEL=stderr

DB_CONNECTION=mysql
DB_HOST=db4free.net
DB_PORT=3306
DB_DATABASE=taxpiya
DB_USERNAME=⚠️TU_USUARIO_DB4FREE
DB_PASSWORD=⚠️TU_PASSWORD_DB4FREE

JWT_SECRET=⚠️CUALQUIER_CADENA_LARGA_ALEATORIA_32_CHARS
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
```

**Firebase service account** (Secret):
```
FIREBASE_CREDENTIALS_JSON=
```
Pega el JSON completo del archivo `service-account.json` en **una sola línea**.

### 3. Deploy
- **Create Web Service**
- Espera 5-15 min hasta **Live**
- Tu URL será algo como: `https://taxpiya-xxxx.onrender.com`
- Actualiza `APP_URL` y `FRONTEND_URL` con esa URL exacta → Save

---

## PARTE C — Firebase (gratis)

1. Firebase Console → proyecto **tax-piya**
2. Authentication → Settings → **Authorized domains**
3. Agrega: `taxpiya-xxxx.onrender.com` (tu URL Render)

---

## PARTE D — Probar

En el navegador del celular:

- Pasajero: `https://TU-URL.onrender.com/pasajero/login`
- Conductor: `https://TU-URL.onrender.com/conductor/login`

**Login de prueba** (contraseña `Taxpiya123` si importaste el SQL):
- Pasajero: `3017954934`
- Conductor: `3208254627`

---

## Limitaciones plan gratis

- Render **duerme** tras 15 min sin uso (primer acceso lento ~1 min)
- db4free **no es producción** (límite de espacio, puede ser lento)
- Suficiente para **probar todo en línea**

---

## Si falla el deploy

| Error | Solución |
|-------|----------|
| Can't connect to MySQL | Revisa usuario/password db4free |
| 500 error | Render → Logs → busca el error |
| Login no funciona | Confirma que el SQL se importó en db4free |
| Firebase error | Agrega dominio Render en Firebase Auth |
