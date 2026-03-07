<?php

declare(strict_types=1);

namespace DevinciIT\ShadowAuth\Facade;

use DevinciIT\ShadowAuth\Core\AuthManager;
use DevinciIT\ShadowAuth\Core\Config;
use DevinciIT\ShadowAuth\Core\PasswordResetManager;
use DevinciIT\ShadowAuth\Core\RegistrationConstraintPolicy;
use DevinciIT\ShadowAuth\Core\RegistrationManager;
use DevinciIT\ShadowAuth\Providers\FileUserProvider;
use DevinciIT\ShadowAuth\Services\TwoFactorService;

final class Auth
{
    private static ?AuthManager $manager = null;

    public static function boot(): void
    {
        $provider = new FileUserProvider((string) Config::get('storage_path'));
        $provider->initialize();

        $registrationConstraints = (array) Config::get('registration_constraints', []);
        $registrationConstraintPolicy = new RegistrationConstraintPolicy($provider, $registrationConstraints);

        self::$manager = new AuthManager(
            $provider,
            new TwoFactorService(),
            new RegistrationManager($provider, $registrationConstraintPolicy),
            new PasswordResetManager($provider),
            (string) Config::get('session_key', 'shadow_auth_user')
        );
    }

    public static function register(string $username, string $password): bool
    {
        return self::manager()->register($username, $password);
    }

    public static function registerWithData(string $username, string $password, array $attributes): bool
    {
        return self::manager()->registerWithData($username, $password, $attributes);
    }

    public static function registrationError(): ?string
    {
        return self::manager()->registrationError();
    }

    public static function requestPasswordResetToken(string $identifier): ?string
    {
        return self::manager()->requestPasswordResetToken($identifier);
    }

    public static function hasValidPasswordResetToken(string $token): bool
    {
        return self::manager()->hasValidPasswordResetToken($token);
    }

    public static function resetPasswordWithToken(string $token, string $newPassword): bool
    {
        return self::manager()->resetPasswordWithToken($token, $newPassword);
    }

    public static function attempt(string $username, string $password, ?string $totp = null): bool
    {
        return self::manager()->attempt($username, $password, $totp);
    }

    public static function beginLogin(string $username, string $password): string
    {
        return self::manager()->beginLogin($username, $password);
    }

    public static function verifyPendingTotp(string $code): bool
    {
        return self::manager()->verifyPendingTotp($code);
    }

    public static function isTotpPending(): bool
    {
        return self::manager()->isTotpPending();
    }

    public static function pendingUsername(): ?string
    {
        return self::manager()->pendingUsername();
    }

    public static function check(): bool
    {
        return self::manager()->check();
    }

    public static function requireAuth(string $redirectTo = '/views/login.php'): void
    {
        if (!self::check()) {
            header('Location: ' . $redirectTo);
            exit;
        }
    }

    public static function user(): ?array
    {
        return self::manager()->user();
    }

    public static function logout(): void
    {
        self::manager()->logout();
    }

    public static function setupTotpSecret(string $username): ?string
    {
        return self::manager()->setupTotpSecret($username);
    }

    public static function confirmTotp(string $username, string $code): bool
    {
        return self::manager()->enableTotpForUser($username, $code);
    }

    public static function disableTotp(string $username): bool
    {
        return self::manager()->disableTotpForUser($username);
    }

    public static function enableTotp(): void
    {
        Config::set(['totp_enabled' => true]);
    }

    public static function disableTotpGlobally(): void
    {
        Config::set(['totp_enabled' => false]);
    }

    private static function manager(): AuthManager
    {
        if (self::$manager === null) {
            self::boot();
        }

        return self::$manager;
    }
}
