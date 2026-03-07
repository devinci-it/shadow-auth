<?php

declare(strict_types=1);

namespace Devinci\ShadowAuth\Core;

use Devinci\ShadowAuth\Providers\FileUserProvider;

final class PasswordResetManager
{
    private const TOKEN_TTL_SECONDS = 1800;

    public function __construct(private readonly FileUserProvider $provider)
    {
    }

    public function requestResetToken(string $identifier): ?string
    {
        $user = $this->findByIdentifier($identifier);
        if ($user === null) {
            return null;
        }

        $username = (string) ($user['username'] ?? '');
        if ($username === '') {
            return null;
        }

        $token = bin2hex(random_bytes(32));
        $expiresAt = date(DATE_ATOM, time() + self::TOKEN_TTL_SECONDS);

        $updated = $this->provider->updateByUsername($username, [
            'password_reset_token_hash' => hash('sha256', $token),
            'password_reset_requested_at' => date(DATE_ATOM),
            'password_reset_expires_at' => $expiresAt,
        ]);

        return $updated ? $token : null;
    }

    public function resetPasswordWithToken(string $token, string $newPassword): bool
    {
        $token = trim($token);
        if ($token === '' || $newPassword === '') {
            return false;
        }

        $user = $this->findByValidToken($token);
        if ($user === null) {
            return false;
        }

        $username = (string) ($user['username'] ?? '');
        if ($username === '') {
            return false;
        }

        return $this->provider->updateByUsername($username, [
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
            'password_reset_token_hash' => null,
            'password_reset_requested_at' => null,
            'password_reset_expires_at' => null,
        ]);
    }

    public function hasValidToken(string $token): bool
    {
        return $this->findByValidToken($token) !== null;
    }

    private function findByIdentifier(string $identifier): ?array
    {
        $identifier = trim($identifier);
        if ($identifier === '') {
            return null;
        }

        $users = $this->provider->all();
        $needle = strtolower($identifier);

        foreach ($users as $user) {
            if (!is_array($user)) {
                continue;
            }

            $username = strtolower((string) ($user['username'] ?? ''));
            $email = strtolower((string) ($user['email'] ?? ''));

            if ($needle === $username || ($email !== '' && $needle === $email)) {
                return $user;
            }
        }

        return null;
    }

    private function findByValidToken(string $token): ?array
    {
        $tokenHash = hash('sha256', $token);
        $users = $this->provider->all();
        $now = time();

        foreach ($users as $user) {
            if (!is_array($user)) {
                continue;
            }

            $storedHash = (string) ($user['password_reset_token_hash'] ?? '');
            if ($storedHash === '' || !hash_equals($storedHash, $tokenHash)) {
                continue;
            }

            $expiresAtRaw = (string) ($user['password_reset_expires_at'] ?? '');
            $expiresAt = strtotime($expiresAtRaw);
            if ($expiresAt === false || $expiresAt < $now) {
                continue;
            }

            return $user;
        }

        return null;
    }
}
