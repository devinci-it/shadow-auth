<?php

declare(strict_types=1);

namespace Shadow\Facade;

use BadMethodCallException;

final class Auth
{
    public static function requireAuth(string $redirectTo = '/views/login.php'): void
    {
        \Devinci\ShadowAuth\Facade\Auth::requireAuth($redirectTo);
    }

    public static function __callStatic(string $name, array $arguments): mixed
    {
        $target = [\Devinci\ShadowAuth\Facade\Auth::class, $name];

        if (!is_callable($target)) {
            throw new BadMethodCallException(sprintf('Method %s::%s does not exist.', self::class, $name));
        }

        return $target(...$arguments);
    }
}
