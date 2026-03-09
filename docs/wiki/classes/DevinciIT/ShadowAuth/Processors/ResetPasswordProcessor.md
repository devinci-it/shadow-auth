# `DevinciIT\\ShadowAuth\\Processors\\ResetPasswordProcessor`

Processes reset-password submissions using a token.

## Responsibilities

- Validates POST + CSRF.
- Validates reset token and new password confirmation.
- Enforces minimum password length.
- Calls `Auth::resetPasswordWithToken()`.
- Redirects to login page on success.

## Constructor

- `__construct(string $loginRedirect)`

## Public Methods

- `handle(): void`
