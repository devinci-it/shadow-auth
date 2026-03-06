<?php

declare(strict_types=1);

namespace Devinci\ShadowAuth\Utils;

final class CSRF
{
    private const SESSION_KEY = '_shadow_csrf';

    public static function token(): string
    {
        self::ensureSession();

        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return (string) $_SESSION[self::SESSION_KEY];
    }

    public static function input(): string
    {
        $token = htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8');

        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    public static function validate(?string $token): bool
    {
        self::ensureSession();

        if (!is_string($token) || $token === '' || !isset($_SESSION[self::SESSION_KEY])) {
            return false;
        }

        return hash_equals((string) $_SESSION[self::SESSION_KEY], $token);
    }

    private static function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
}
