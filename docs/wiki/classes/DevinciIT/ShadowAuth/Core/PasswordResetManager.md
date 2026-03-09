# `DevinciIT\\ShadowAuth\\Core\\PasswordResetManager`

Manages password reset token generation, validation, and password updates.

## Responsibilities

- Finds users by username or email identifier.
- Issues random reset token and stores hash only.
- Enforces token expiration (`1800` seconds by default).
- Resets password and clears reset metadata after success.

## Public Methods

- `requestResetToken(string $identifier): ?string`: Returns plain token for delivery if account exists.
- `resetPasswordWithToken(string $token, string $newPassword): bool`: Resets hash when token is valid and active.
- `hasValidToken(string $token): bool`: Fast validity check.

## Security Notes

- Raw token is never persisted, only `sha256` hash.
- Token comparison uses `hash_equals`.
- Expired tokens are rejected before update.
