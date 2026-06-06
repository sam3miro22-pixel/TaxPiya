#!/usr/bin/env bash
set -e
cd /var/www/html

if [ -n "${FIREBASE_CREDENTIALS_JSON:-}" ]; then
  mkdir -p storage/app/firebase
  printf '%s' "$FIREBASE_CREDENTIALS_JSON" > storage/app/firebase/service-account.json
  export FIREBASE_CREDENTIALS=/var/www/html/storage/app/firebase/service-account.json
fi

php artisan storage:link 2>/dev/null || true
chmod -R 775 storage bootstrap/cache database 2>/dev/null || true

php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ "${DB_CONNECTION:-sqlite}" != "sqlite" ]; then
  if php artisan migrate:status >/dev/null 2>&1; then
    php artisan migrate --force || true
  fi
fi

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
