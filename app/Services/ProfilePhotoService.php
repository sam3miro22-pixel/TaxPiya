<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

class ProfilePhotoService
{
    private const MAX_KB = 5120;

    private const ALLOWED = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public function store(UploadedFile $file): string
    {
        if (!$file->isValid()) {
            throw new RuntimeException('No se pudo leer la imagen.');
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        if (!in_array($ext, self::ALLOWED, true)) {
            throw new RuntimeException('Formato no permitido. Usa JPG, PNG, GIF o WEBP.');
        }

        if ($file->getSize() > self::MAX_KB * 1024) {
            throw new RuntimeException('La imagen supera el límite de 5 MB.');
        }

        $dir = public_path('uploads/files');
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $name = 'profile_' . date('Ymd_His') . '_' . Str::random(8) . '.' . $ext;
        $file->move($dir, $name);

        return 'uploads/files/' . $name;
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
        $absolute = public_path($relative);

        if (!is_file($absolute)) {
            return null;
        }

        return asset($relative);
    }
}
