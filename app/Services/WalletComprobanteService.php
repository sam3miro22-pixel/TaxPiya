<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

class WalletComprobanteService
{
    private const MAX_KB = 5120;

    private const ALLOWED = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    /** Ruta relativa dentro de storage/app/public (servida vía /storage/...) */
    private const STORAGE_PREFIX = 'wallet';

    public function store(UploadedFile $file): string
    {
        if (!$file->isValid()) {
            throw new RuntimeException('No se pudo leer la imagen del comprobante.');
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        if (!in_array($ext, self::ALLOWED, true)) {
            throw new RuntimeException('Formato no permitido. Usa JPG, PNG, GIF o WEBP.');
        }

        if ($file->getSize() > self::MAX_KB * 1024) {
            throw new RuntimeException('La imagen supera el límite de 5 MB.');
        }

        $dir = storage_path('app/public/' . self::STORAGE_PREFIX);
        $this->ensureWritableDirectory($dir);

        $name = 'comprobante_' . date('Ymd_His') . '_' . Str::random(8) . '.' . $ext;
        $file->move($dir, $name);

        return self::STORAGE_PREFIX . '/' . $name;
    }

    private function ensureWritableDirectory(string $dir): void
    {
        if (File::isDirectory($dir)) {
            if (!is_writable($dir)) {
                throw new RuntimeException('El servidor no puede escribir comprobantes. Contacta soporte Taxpiya.');
            }

            return;
        }

        try {
            File::makeDirectory($dir, 0775, true);
        } catch (\Throwable $e) {
            report($e);
            throw new RuntimeException('No se pudo preparar el almacén de comprobantes en el servidor.');
        }

        if (!is_writable($dir)) {
            throw new RuntimeException('El servidor no puede escribir comprobantes. Contacta soporte Taxpiya.');
        }
    }

    public function publicUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $relative = ltrim($path, '/');

        if (str_starts_with($relative, 'uploads/')) {
            return asset($relative);
        }

        if (str_starts_with($relative, 'storage/')) {
            return asset($relative);
        }

        return asset('storage/' . $relative);
    }

    public function absolutePath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $relative = ltrim($path, '/');

        if (str_starts_with($relative, 'uploads/')) {
            return public_path($relative);
        }

        if (str_starts_with($relative, 'storage/')) {
            $relative = substr($relative, strlen('storage/'));
        }

        return storage_path('app/public/' . $relative);
    }
}
