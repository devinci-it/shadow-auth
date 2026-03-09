# `DevinciIT\\ShadowAuth\\Core\\AuthManager`

Central runtime service that orchestrates registration, login, TOTP, session state, and password reset operations.

## Responsibilities

- Delegates registration to `RegistrationManager`.
- Delegates password reset operations to `PasswordResetManager`.
- Verifies credentials against `FileUserProvider`.
- Handles 2FA pending state and final session authentication.
- Owns session key conventions for logged-in and pending users.

## Constructor Dependencies

- `FileUserProvider $provider`
- `TwoFactorService $twoFactorService`
- `RegistrationManager $registrationManager`
- `PasswordResetManager $passwordResetManager`
- `string $sessionKey`

## Public Methods

- `register(string $username, string $password): bool`: Shortcut to registration manager.
- `registerWithData(string $username, string $password, array $attributes): bool`: Registers with custom attributes.
- `registrationError(): ?string`: Returns last registration validation error.
- `requestPasswordResetToken(string $identifier): ?string`: Generates reset token when identifier exists.
- `hasValidPasswordResetToken(string $token): bool`: Checks if token matches a non-expired record.
- `resetPasswordWithToken(string $token, string $newPassword): bool`: Updates hash and clears token fields.
- `attempt(string $username, string $password, ?string $totpCode = null): bool`: One-call login flow (optionally with TOTP).
- `beginLogin(string $username, string $password): string`: Returns `authenticated`, `totp_required`, or `failed`.
- `verifyPendingTotp(string $code): bool`: Completes login for pending TOTP challenge.
- `isTotpPending(): bool`: Indicates whether pending TOTP state exists in session.
- `pendingUsername(): ?string`: Username from pending session state.
- `check(): bool`: True when authenticated session exists.
- `user(): ?array`: Session user payload (`username`, `logged_in_at`).
- `logout(): void`: Clears authenticated and pending session state.
- `setupTotpSecret(string $username): ?string`: Generates and stores TOTP secret for a user.
- `enableTotpForUser(string $username, string $code): bool`: Verifies code and enables TOTP.
- `disableTotpForUser(string $username): bool`: Disables and clears TOTP secret.

## Session Behavior

- Creates pending session key as `<session_key>_pending_totp`.
- Calls `session_regenerate_id(true)` after successful authentication.
- Starts session lazily via private `ensureSession()`.
