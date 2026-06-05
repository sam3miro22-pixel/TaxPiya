-- Ejecutar en MySQL si no puedes correr php artisan migrate
ALTER TABLE `users`
  ADD COLUMN `firebase_uid` VARCHAR(128) NULL UNIQUE AFTER `id`;
