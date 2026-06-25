<?php

namespace App\Services;

class DemoAccountCatalog
{
    /** Una cuenta canónica por rol (SQLite). */
    /** @return list<string> */
    public static function keepEmails(): array
    {
        return [
            'admin.demo@taxpiya.com',
            'pasajero.demo1@taxpiya.com',
            'conductor.demo1@taxpiya.com',
            'empresa.demo@taxpiya.com',
        ];
    }

    /** Solo pasajero y conductor usan Firebase Auth. */
    /** @return list<string> */
    public static function keepFirebaseEmails(): array
    {
        return [
            'pasajero.demo1@taxpiya.com',
            'conductor.demo1@taxpiya.com',
        ];
    }

    /** @return list<string> */
    public static function keepPhones(): array
    {
        return [
            '3001001001',
            '3009001001',
            '3109001001',
            '3209002001',
        ];
    }

    public static function isDemoEmail(?string $email): bool
    {
        if ($email === null || $email === '') {
            return false;
        }

        return in_array(strtolower(trim($email)), array_map('strtolower', self::keepEmails()), true);
    }

    public static function isDemoPhone(?string $phone): bool
    {
        if ($phone === null || $phone === '') {
            return false;
        }

        $digits = preg_replace('/\D+/', '', $phone);

        return in_array($digits, self::keepPhones(), true);
    }
}
