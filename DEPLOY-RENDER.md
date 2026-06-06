# TaxPiya en Render + GitHub (sin PC encendida)

Guía para tener backend **24/7 en internet**, probar desde el navegador y desde las APK en el móvil.

---

## ¿Qué vas a tener?

| Pieza | Dónde corre |
|-------|-------------|
| Backend Laravel (login, viajes, mapa, API) | **Render** (Web Service Docker) |
| Base de datos | **SQLite** (`database/taxpiya.sqlite`) — ver [SETUP-GRATIS.md](./SETUP-GRATIS.md) |
| Firebase (Auth, push, Firestore) | **Firebase** `tax-piya` (ya lo tienes) |
| Apps móviles | APK apuntando a la URL de Render |

> **Plan gratis Render:** el web duerme tras ~15 min sin uso; el primer acceso tarda ~30–60 s. Para pruebas en tiempo real sin pausa, usa plan de pago (~7 USD/mes) o acepta el “cold start”.

---

## PARTE 1 — Crear repositorio en GitHub

### 1.1 Cuenta y repo
1. Entra a [github.com](https://github.com) e inicia sesión.
2. **New repository**
3. Nombre sugerido: `taxpiya`
4. **Private** (recomendado: hay claves y dump de BD)
5. **No** marques “Add README” (subiremos el código existente)
6. Crear repositorio

### 1.2 Subir solo la carpeta Laravel (desde PowerShell)

```powershell
cd C:\Users\pc\Desktop\taxipiya\Taxpiya

git init
git add .
git commit -m "TaxPiya: backend Laravel listo para Render"

git branch -M main
git remote add origin https://github.com/TU_USUARIO/taxpiya.git
git push -u origin main
```

**No subas:** `.env`, `storage/app/firebase/service-account.json`, `node_modules/`, `vendor/` (ya están en `.gitignore`).

---

## PARTE 2 — MySQL en Render

Tienes el dump en: `database/sql/taxpiya48_718txps7.sql`

### Opción A — MySQL en Render (recomendada)
1. [Render Dashboard](https://dashboard.render.com) → **New +** → **Private Service**
2. Conecta el template: [render-examples/mysql](https://github.com/render-examples/mysql) (botón “Deploy to Render” en su README) **o** fork + Private Service Docker
3. Variables:
   - `MYSQL_DATABASE` = `taxpiya`
   - `MYSQL_USER` = `taxpiya`
   - `MYSQL_PASSWORD` = (genera una segura)
   - `MYSQL_ROOT_PASSWORD` = (otra segura)
4. **Disk** obligatorio: mount `/var/lib/mysql`, mínimo 10 GB
5. Anota el **hostname interno** (ej. `taxpiya-mysql:3306`) — solo visible desde otros servicios Render del mismo equipo

### Opción B — MySQL del hosting `taxpiya.com`
Si tienes cPanel/phpMyAdmin del hosting original:
- En “Remote MySQL” permite el acceso desde internet (o IP de Render)
- Usa `DB_HOST=taxpiya.com` en Render  
*(Antes falló desde tu PC por IP; Render tiene otra IP y puede funcionar si la autorizas.)*

---

## PARTE 3 — Web Service Laravel en Render

1. **New +** → **Web Service**
2. Conecta el repo `taxpiya` de GitHub
3. Configuración:

| Campo | Valor |
|-------|--------|
| **Root Directory** | *(vacío — el repo es la raíz Laravel)* |
| **Runtime** | **Docker** |
| **Branch** | `main` |
| **Instance type** | Free (pruebas) o Starter (sin sleep) |

4. **Environment Variables** (pestaña Environment):

```
APP_NAME=Taxpiya
APP_ENV=production
APP_DEBUG=false
APP_KEY=PEGA_AQUI_el_resultado_de_php_artisan_key_generate_show
APP_URL=https://TU-NOMBRE.onrender.com
FRONTEND_URL=https://TU-NOMBRE.onrender.com

DB_CONNECTION=mysql
DB_HOST=taxpiya-mysql
DB_PORT=3306
DB_DATABASE=taxpiya
DB_USERNAME=taxpiya
DB_PASSWORD=LA_MISMA_DEL_MYSQL

JWT_SECRET=genera_una_cadena_larga_aleatoria
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

FIREBASE_CREDENTIALS_JSON={...pega TODO el JSON del service-account en UNA linea...}
```

Generar `APP_KEY` en tu PC (una vez):
```powershell
cd C:\Users\pc\Desktop\taxipiya\Taxpiya
php artisan key:generate --show
```

5. **Create Web Service** → espera el primer deploy (5–15 min).

---

## PARTE 4 — Importar la base de datos

Cuando MySQL y el Web Service estén **Live**:

1. Render → Web Service → **Shell**
2. Instala cliente mysql si hace falta, o usa el shell del servicio MySQL
3. Desde el servicio **MySQL** Shell:

```bash
mysql -u taxpiya -p taxpiya < /path/to/dump.sql
```

**Más fácil:** phpMyAdmin del hosting (opción B) → Importar `database/sql/taxpiya48_718txps7.sql`

4. Columna Firebase (si no está en el dump):

```sql
ALTER TABLE users ADD COLUMN IF NOT EXISTS firebase_uid VARCHAR(128) NULL UNIQUE AFTER id;
```

5. **Manual Deploy** en Render para recargar la app.

Prueba en navegador:
- `https://TU-NOMBRE.onrender.com/pasajero/login`
- `https://TU-NOMBRE.onrender.com/conductor/login`

---

## PARTE 5 — Firebase (dominios autorizados)

Firebase Console → **Authentication** → **Settings** → **Authorized domains** → Add:
- `TU-NOMBRE.onrender.com`

*(Opcional)* Hosting custom: `app.taxpiya.com` → CNAME a Render.

---

## PARTE 6 — Apps móviles (APK)

Las APK deben cargar la URL de Render, no tu PC.

En `apppasajero/taxpiyapasajero/capacitor.config.json`:
```json
"server": {
  "url": "https://TU-NOMBRE.onrender.com/pasajero/login",
  "cleartext": false,
  "androidScheme": "https"
}
```

En `appconductor/taxpiyaconductor/capacitor.config.json`:
```json
"server": {
  "url": "https://TU-NOMBRE.onrender.com/conductor/login",
  "cleartext": false,
  "androidScheme": "https"
}
```

Luego recompilar APK (o pídemelo cuando tengas la URL final de Render).

**Prueba rápida sin recompilar:** abre las URLs en Chrome del móvil.

---

## PARTE 7 — Prueba en tiempo real (pasajero + conductor)

1. Dos dispositivos (o uno móvil + PC en incógnito)
2. Misma URL Render (no localhost)
3. Pasajero: solicita viaje
4. Conductor: activar GPS / disponible → debe recibir solicitud
5. Push FCM: requiere `FIREBASE_CREDENTIALS_JSON` correcto y permisos de notificación en el móvil

---

## Checklist antes de avisarme “está conectado”

- [ ] Repo GitHub creado y código subido
- [ ] Web Service Render en verde (Live)
- [ ] MySQL con dump importado
- [ ] Login pasajero/conductor funciona en navegador
- [ ] URL final de Render (ej. `https://taxpiya-xxxx.onrender.com`)

Cuando tengas la **URL de Render** y el repo en GitHub, avísame y:
1. Actualizo las APK con esa URL
2. Te paso credenciales de prueba
3. Verificamos viajes en tiempo real

---

## Costes orientativos Render

| Recurso | Free | Pago |
|---------|------|------|
| Web Service | Sí (duerme) | ~7 USD/mes siempre on |
| MySQL + disco 10GB | No gratis | ~7+ USD/mes |
| **Alternativa barata BD** | Usar MySQL del hosting `taxpiya.com` | 0 extra si ya lo pagas |

---

## Archivos de deploy incluidos en el repo

- `Dockerfile` — PHP 8.3 + Nginx + Laravel
- `docker/nginx.conf`, `docker/supervisord.conf`, `docker/render-start.sh`
- `.dockerignore`, `.env.example`
- `database/sql/taxpiya48_718txps7.sql` — backup para importar
