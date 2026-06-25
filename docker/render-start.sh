#!/usr/bin/env bash
set -e
cd /var/www/html

export APP_TIMEZONE="${APP_TIMEZONE:-America/Bogota}"

if [ -n "${FIREBASE_CREDENTIALS_JSON:-}" ]; then
  mkdir -p storage/app/firebase
  printf '%s' "$FIREBASE_CREDENTIALS_JSON" > storage/app/firebase/service-account.json
  export FIREBASE_CREDENTIALS=/var/www/html/storage/app/firebase/service-account.json
fi

php artisan storage:link 2>/dev/null || true
mkdir -p storage/app/public/wallet
chown -R www-data:www-data storage/app/public 2>/dev/null || true
chmod -R 775 storage/app/public 2>/dev/null || true

fix_sqlite_perms() {
  if [ "${DB_CONNECTION:-sqlite}" != "sqlite" ]; then
    return 0
  fi
  mkdir -p database
  chown -R www-data:www-data database storage bootstrap/cache 2>/dev/null || true
  chmod -R 775 database storage bootstrap/cache 2>/dev/null || true
  if [ -f database/taxpiya.sqlite ]; then
    chown www-data:www-data database/taxpiya.sqlite 2>/dev/null || true
    chmod 664 database/taxpiya.sqlite 2>/dev/null || true
  fi
  for f in database/taxpiya.sqlite-wal database/taxpiya.sqlite-shm; do
    if [ -f "$f" ]; then
      chown www-data:www-data "$f" 2>/dev/null || true
      chmod 664 "$f" 2>/dev/null || true
    fi
  done
}

fix_sqlite_perms

# Restaurar SQLite desde GitHub ANTES de cachear config (Render redeploys borran el disco)
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ] && [ "${TAXPIYA_GITHUB_BACKUP:-true}" != "false" ]; then
  php artisan taxpiya:sqlite-restore --no-interaction 2>/dev/null || true
  fix_sqlite_perms
fi

php artisan config:cache
php artisan route:clear 2>/dev/null || true
php artisan view:cache

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
  php artisan migrate --force 2>/dev/null || true
  php artisan taxpiya:ensure-schema --no-interaction || true
  php artisan taxpiya:referral-pay-pending 2>/dev/null || true
  if [ "${TAXPIYA_PURGE_NON_DEMO:-false}" = "true" ]; then
    php artisan taxpiya:purge-non-demo --force --reseed --clean-transactions --no-firebase --once 2>/dev/null || true
    fix_sqlite_perms
  fi
  echo "[taxpiya] Comprobando reset one-shot de datos demo..."
  export TAXPIYA_SEED_NO_FIREBASE=1
  php artisan taxpiya:purge-non-demo --reset-once || true
  fix_sqlite_perms
  php artisan taxpiya:purge-non-demo --canonical-once --force || true
  php artisan taxpiya:firebase-cleanup --once --force || true
  unset TAXPIYA_SEED_NO_FIREBASE
  php artisan taxpiya:seed-demo --bootstrap-once --force || true
  fix_sqlite_perms
  php artisan taxpiya:sqlite-backup --no-interaction || true
  if [ "${TAXPIYA_SEED_DEMO:-false}" = "true" ]; then
    php artisan taxpiya:seed-demo --force 2>/dev/null || true
  fi
elif php artisan migrate:status >/dev/null 2>&1; then
  php artisan migrate --force || true
fi

# Primer respaldo tras arranque (asegura copia en nube si aún no existía)
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ] && [ "${TAXPIYA_GITHUB_BACKUP:-true}" != "false" ]; then
  php artisan taxpiya:sqlite-backup --no-interaction 2>/dev/null || true
fi

fix_sqlite_perms

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
