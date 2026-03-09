# `DevinciIT\\ShadowAuth\\Core\\RegistrationManager`

Handles registration input validation, custom attributes sanitization, and user record persistence.

## Responsibilities

- Validates required username/password fields.
- Applies configurable uniqueness constraints.
- Sanitizes custom attributes and blocks reserved keys.
- Writes normalized user record to `FileUserProvider`.
- Exposes last validation error for UI layer.

## Reserved Keys

Custom attributes cannot override internal keys:

- `username`
- `password_hash`
- `totp_secret`
- `totp_enabled`
- `created_at`

## Public Methods

- `register(string $username, string $password): bool`: Basic registration.
- `registerWithData(string $username, string $password, array $attributes): bool`: Registration with custom fields.
- `lastError(): ?string`: Last business-rule validation message.

## Record Shape

Default persisted fields include:

- `username`
- `password_hash`
- `totp_secret`
- `totp_enabled`
- `created_at`
