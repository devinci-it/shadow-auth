<?php

declare(strict_types=1);

namespace Devinci\ShadowAuth\Core;

use Devinci\ShadowAuth\Providers\FileUserProvider;
use Devinci\ShadowAuth\Services\TwoFactorService;

final class AuthManager
{
    private string $pendingSessionKey;

    public function __construct(
        private readonly FileUserProvider $provider,
        private readonly TwoFactorService $twoFactorService,
        private readonly RegistrationManager $registrationManager,
        private readonly PasswordResetManager $passwordResetManager,
        private readonly string $sessionKey
    ) {
        $this->pendingSessionKey = $this->sessionKey . '_pending_totp';
    }

    public function register(string $username, string $password): bool
    {
        return $this->registrationManager->register($username, $password);
    }

    public function registerWithData(string $username, string $password, array $attributes): bool
    {
        return $this->registrationManager->registerWithData($username, $password, $attributes);
    }

    public function registrationError(): ?string
    {
        return $this->registrationManager->lastError();
    }

    public function requestPasswordResetToken(string $identifier): ?string
    {
        return $this->passwordResetManager->requestResetToken($identifier);
    }

    public function hasValidPasswordResetToken(string $token): bool
    {
        return $this->passwordResetManager->hasValidToken($token);
    }

    public function resetPasswordWithToken(string $token, string $newPassword): bool
    {
        return $this->passwordResetManager->resetPasswordWithToken($token, $newPassword);
    }

    public function attempt(string $username, string $password, ?string $totpCode = null): bool
    {
        $result = $this->beginLogin($username, $password);
        if ($result === 'failed') {
            return false;
        }

        if ($result === 'totp_required') {
            if ($totpCode === null) {
                return false;
            }

            return $this->verifyPendingTotp($totpCode);
        }

        return true;
    }

    public function beginLogin(string $username, string $password): string
    {
        $this->ensureSession();

        $user = $this->provider->findByUsername(trim($username));
        if ($user === null) {
            return 'failed';
        }

        $passwordHash = (string) ($user['password_hash'] ?? '');
        if ($passwordHash === '' || !password_verify($password, $passwordHash)) {
            return 'failed';
        }

        $totpEnabled = (bool) ($user['totp_enabled'] ?? false);
        $globalTotpEnabled = (bool) Config::get('totp_enabled', true);

        if ($globalTotpEnabled && $totpEnabled) {
            $_SESSION[$this->pendingSessionKey] = [
                'username' => $user['username'],
                'created_at' => time(),
            ];

            return 'totp_required';
        }

        unset($_SESSION[$this->pendingSessionKey]);
        $_SESSION[$this->sessionKey] = [
            'username' => $user['username'],
            'logged_in_at' => time(),
        ];
        session_regenerate_id(true);

        return 'authenticated';
    }

    public function verifyPendingTotp(string $code): bool
    {
        $this->ensureSession();

        $pending = $_SESSION[$this->pendingSessionKey] ?? null;
        if (!is_array($pending) || !isset($pending['username'])) {
            return false;
        }

        $username = (string) $pending['username'];
        $user = $this->provider->findByUsername($username);
        if ($user === null) {
            return false;
        }

        $secret = (string) ($user['totp_secret'] ?? '');
        if ($secret === '' || !$this->twoFactorService->verifyCode($secret, $code)) {
            return false;
        }

        unset($_SESSION[$this->pendingSessionKey]);
        $_SESSION[$this->sessionKey] = [
            'username' => $user['username'],
            'logged_in_at' => time(),
        ];
        session_regenerate_id(true);

        return true;
    }

    public function isTotpPending(): bool
    {
        $this->ensureSession();

        return isset($_SESSION[$this->pendingSessionKey]['username']);
    }

    public function pendingUsername(): ?string
    {
        $this->ensureSession();

        if (!$this->isTotpPending()) {
            return null;
        }

        return (string) $_SESSION[$this->pendingSessionKey]['username'];
    }

    public function check(): bool
    {
        $this->ensureSession();

        return isset($_SESSION[$this->sessionKey]['username']);
    }

    public function user(): ?array
    {
        $this->ensureSession();

        return $_SESSION[$this->sessionKey] ?? null;
    }

    public function logout(): void
    {
        $this->ensureSession();
        unset($_SESSION[$this->sessionKey]);
        unset($_SESSION[$this->pendingSessionKey]);
    }

    public function setupTotpSecret(string $username): ?string
    {
        $user = $this->provider->findByUsername($username);
        if ($user === null) {
            return null;
        }

        $secret = $this->twoFactorService->generateSecret();
        $updated = $this->provider->updateByUsername($username, [
            'totp_secret' => $secret,
            'totp_enabled' => false,
        ]);

        return $updated ? $secret : null;
    }

    public function enableTotpForUser(string $username, string $code): bool
    {
        $user = $this->provider->findByUsername($username);
        if ($user === null) {
            return false;
        }

        $secret = (string) ($user['totp_secret'] ?? '');
        if ($secret === '' || !$this->twoFactorService->verifyCode($secret, $code)) {
            return false;
        }

        return $this->provider->updateByUsername($username, ['totp_enabled' => true]);
    }

    public function disableTotpForUser(string $username): bool
    {
        return $this->provider->updateByUsername($username, [
            'totp_enabled' => false,
            'totp_secret' => null,
        ]);
    }

    private function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
}
