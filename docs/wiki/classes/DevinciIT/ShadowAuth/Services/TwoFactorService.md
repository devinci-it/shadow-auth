# `DevinciIT\\ShadowAuth\\Services\\TwoFactorService`

Implements TOTP secret generation and code verification (30-second window).

## Responsibilities

- Generates Base32-style shared secrets.
- Verifies 6-digit TOTP codes with configurable time drift window.
- Implements RFC-style dynamic truncation flow (HMAC-SHA1).

## Public Methods

- `generateSecret(int $length = 32): string`: Creates random secret using `A-Z2-7` alphabet.
- `verifyCode(string $secret, string $code, int $window = 1): bool`: Validates code in current +/- window.

## Internal Steps

1. Decode Base32 secret to binary key.
2. Build time counter (`floor(time()/30)`).
3. HMAC-SHA1 and dynamic truncation.
4. Compare candidate code with `hash_equals`.
