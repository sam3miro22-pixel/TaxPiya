# Persistencia de datos en Render (100% gratis)

Render Free **borra el disco** en cada redeploy. TaxPiya usa **GitHub** (gratis, sin tarjeta, sin Firebase Storage).

---

## Arquitectura

| Pieza | Qué hace |
|-------|----------|
| **GitHub Actions** (cada 10 min) | Descarga SQLite de Render y la sube a `taxpiya-db-backup` |
| **Arranque Render** | Restaura SQLite desde GitHub (URL pública) |
| **Respaldo en Render** (opcional) | Si pones `GITHUB_BACKUP_TOKEN`, también respalda desde el servidor |

Repositorio de respaldos: [taxpiya-db-backup](https://github.com/sam3miro22-pixel/taxpiya-db-backup) (público, solo el archivo `.sqlite`).

---

## Variables en Render (mínimas)

```
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/taxpiya.sqlite
TAXPIYA_GITHUB_BACKUP=true
```

**No hace falta** `GITHUB_BACKUP_TOKEN` si GitHub Actions está activo (ya configurado en el repo).

Opcional en Render:

```
GITHUB_BACKUP_TOKEN=ghp_...   ← respaldo extra desde el servidor
```

---

## Secret en GitHub (repo TaxPiya)

Ya configurado automáticamente:

| Secret | Uso |
|--------|-----|
| `TAXPIYA_APP_KEY` | Descargar dump seguro desde Render |
| `TAXPIYA_GH_PAT` | Subir backup al repo `taxpiya-db-backup` |

Workflow: `.github/workflows/sqlite-backup.yml`

---

## Comandos (Shell Render)

```bash
php artisan taxpiya:sqlite-status
php artisan taxpiya:sqlite-backup
php artisan taxpiya:sqlite-restore
```

---

## Desarrollo local

```
TAXPIYA_GITHUB_BACKUP=false
```

---

## Qué queda protegido

Usuarios, viajes, wallet, SOS, conductores — **toda la SQLite**.

---

## Nota de seguridad

El repo `taxpiya-db-backup` es **público** para que Render pueda restaurar sin tokens. Contiene datos de demo; en producción real conviene repo privado + `GITHUB_BACKUP_TOKEN` en Render.
