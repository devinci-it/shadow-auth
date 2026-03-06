<?php

declare(strict_types=1);

namespace Devinci\ShadowAuth\Core;

use Devinci\ShadowAuth\Providers\FileUserProvider;

final class RegistrationManager
{
    private const RESERVED_KEYS = [
        'username',
        'password_hash',
        'totp_secret',
        'totp_enabled',
        'created_at',
    ];

    public function __construct(private readonly FileUserProvider $provider)
    {
    }

    public function register(string $username, string $password): bool
    {
        return $this->registerWithData($username, $password, []);
    }

    public function registerWithData(string $username, string $password, array $attributes): bool
    {
        $username = trim($username);

        if ($username === '' || $password === '') {
            return false;
        }

        if ($this->provider->findByUsername($username) !== null) {
            return false;
        }

        $customAttributes = $this->sanitizeCustomAttributes($attributes);

        $record = [
            'username' => $username,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'totp_secret' => null,
            'totp_enabled' => false,
            'created_at' => date(DATE_ATOM),
        ];

        $record = array_merge($record, $customAttributes);

        return $this->provider->create($record);
    }

    private function sanitizeCustomAttributes(array $attributes): array
    {
        $sanitized = [];

        foreach ($attributes as $key => $value) {
            if (!is_string($key) || in_array($key, self::RESERVED_KEYS, true)) {
                continue;
            }

            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $key)) {
                continue;
            }

            if (is_scalar($value) || $value === null) {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }
}
