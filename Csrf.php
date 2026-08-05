<?php
declare(strict_types=1);

namespace App;

final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validate(?string $submitted): bool
    {
        return !empty($_SESSION['csrf_token'])
            && !empty($submitted)
            && hash_equals($_SESSION['csrf_token'], $submitted);
    }

    public static function regenerate(): void
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}