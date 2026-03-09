<?php

namespace DevinciIT\ShadowAuth\Core;

/**
 * Lightweight static configuration container used by library services.
 */
class Config
{
    protected static array $config = [];

    public static function set(array $values): void
    {
        self::$config = array_merge(self::$config, $values);
    }

    /**
     * Returns a config value, or a fallback when the key is missing.
     */
    public static function get(string $key, $default = null)
    {
        return self::$config[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, self::$config);
    }
}