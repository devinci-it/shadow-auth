# `DevinciIT\\ShadowAuth\\Utils\\CSRF`

Session-backed CSRF token helper.

## Responsibilities

- Lazily creates token and stores it in session.
- Renders hidden input HTML field.
- Validates provided token using constant-time comparison.

## Public Methods

- `token(): string`: Returns stable session token.
- `input(): string`: Returns `<input type="hidden" ...>` markup.
- `validate(?string $token): bool`: Checks presence and equality with session token.
