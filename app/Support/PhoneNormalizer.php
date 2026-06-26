<?php

namespace App\Support;

class PhoneNormalizer
{
    public static function digits(?string $phone): string
    {
        if ($phone === null || $phone === '') {
            return '';
        }

        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (strlen($digits) === 12 && str_starts_with($digits, '57')) {
            $digits = substr($digits, 2);
        }

        return $digits;
    }

    public static function matches(?string $stored, ?string $input): bool
    {
        $a = self::digits($stored);
        $b = self::digits($input);

        if ($a === '' || $b === '') {
            return false;
        }

        return $a === $b || str_ends_with($a, $b) || str_ends_with($b, $a);
    }
}
