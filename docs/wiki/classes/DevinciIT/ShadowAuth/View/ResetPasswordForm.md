# `DevinciIT\\ShadowAuth\\View\\ResetPasswordForm`

Concrete form schema for reset-password submissions.

## Required Fields

- `reset_token` (`hidden`)
- `password` (`password`, required)
- `confirm_password` (`password`, required)

## Public Methods

- `setToken(string $token): static`: Injects reset token into hidden field value.

Inherits rendering and CSRF behavior from `BaseForm`.
