# `DevinciIT\\ShadowAuth\\Processors\\ForgotPasswordProcessor`

Processes forgot-password form submissions.

## Responsibilities

- Validates POST and CSRF token.
- Accepts username/email identifier.
- Requests password reset token through facade.
- Stores user-facing flash message.
- Builds reset URL (demo behavior) when token generated.

## Constructor

- `__construct(string $resetPageUrl)`

## Public Methods

- `handle(): void`

## Notes

In this package demo flow, successful token generation appends a direct reset URL to flash message instead of sending email.
