<?php

declare(strict_types=1);

namespace DevinciIT\ShadowAuth\Core;

/**
 * Session-backed one-time message helper for redirect flows.
 */
final class Flash
{
    private const KEY = '_shadow_flash';

    public static function set(string $message): void
    {
        self::ensureSession();
        $_SESSION[self::KEY] = $message;
    }

    public static function get(): ?string
    {
        self::ensureSession();

        if (!isset($_SESSION[self::KEY])) {
            return null;
        }

        $message = (string) $_SESSION[self::KEY];
        unset($_SESSION[self::KEY]);

        return $message;
    }

    private static function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
}
