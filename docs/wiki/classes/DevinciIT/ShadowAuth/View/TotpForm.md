# `DevinciIT\\ShadowAuth\\View\\TotpForm`

Concrete form schema for 2FA verification.

## Required Fields

- `totp_code` (`text`, required, numeric input mode, one-time-code autocomplete)

Inherits rendering and CSRF behavior from `BaseForm`.
