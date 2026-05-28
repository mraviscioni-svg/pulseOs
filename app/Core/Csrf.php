<?php

declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    public static function token(): string
    {
        if (!Session::get('_csrf')) {
            Session::set('_csrf', bin2hex(random_bytes(32)));
        }

        return (string) Session::get('_csrf');
    }

    public static function validate(?string $token): bool
    {
        $stored = Session::get('_csrf');

        return is_string($stored) && is_string($token) && hash_equals($stored, $token);
    }
}
