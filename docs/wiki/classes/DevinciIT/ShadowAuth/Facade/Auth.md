# `DevinciIT\\ShadowAuth\\Facade\\Auth`

Primary static facade for application code. Delegates to singleton `AuthManager` and lazily boots dependencies.

## Responsibilities

- Bootstraps provider, managers, and services from `Config`.
- Exposes static API for auth, registration, TOTP, and password reset.
- Provides route guard helper (`requireAuth`).
- Toggles global TOTP setting through `Config`.

## Boot Process

`boot()` builds and stores one `AuthManager` using:

- `FileUserProvider`
- `RegistrationConstraintPolicy`
- `RegistrationManager`
- `PasswordResetManager`
- `TwoFactorService`

## Public Methods

- `boot(): void`
- `register(string $username, string $password): bool`
- `registerWithData(string $username, string $password, array $attributes): bool`
- `registrationError(): ?string`
- `requestPasswordResetToken(string $identifier): ?string`
- `hasValidPasswordResetToken(string $token): bool`
- `resetPasswordWithToken(string $token, string $newPassword): bool`
- `attempt(string $username, string $password, ?string $totp = null): bool`
- `beginLogin(string $username, string $password): string`
- `verifyPendingTotp(string $code): bool`
- `isTotpPending(): bool`
- `pendingUsername(): ?string`
- `check(): bool`
- `requireAuth(string $redirectTo = '/views/login.php'): void`
- `user(): ?array`
- `logout(): void`
- `setupTotpSecret(string $username): ?string`
- `confirmTotp(string $username, string $code): bool`
- `disableTotp(string $username): bool`
- `enableTotp(): void`
- `disableTotpGlobally(): void`
