# `DevinciIT\\ShadowAuth\\Processors\\TOTPProcessor`

Processes TOTP challenge submissions for pending login sessions.

## Responsibilities

- Requires pending TOTP session (`Auth::isTotpPending()`).
- Validates POST + CSRF.
- Normalizes and validates TOTP code input.
- Calls `Auth::verifyPendingTotp()`.
- Redirects to success route on verification.

## Constructor

- `__construct(string $successRedirect = '/dashboard.php', string $loginRedirect = '/views/login.php')`

## Public Methods

- `handle(): void`
