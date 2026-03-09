# `DevinciIT\\ShadowAuth\\Core\\RegistrationConstraintPolicy`

Evaluates uniqueness constraints for registration attributes.

## Responsibilities

- Normalizes configured field names.
- Applies uniqueness checks against provider records.
- Supports case-sensitive or case-insensitive matching per field.
- Produces user-facing field-specific violation messages.

## Constructor Inputs

- `FileUserProvider $provider`
- `array $constraints`

Supported constraint keys:

- `unique_fields`
- `case_insensitive_fields`

## Public Methods

- `violationMessageFor(array $attributes): ?string`: Returns validation message or `null` when valid.

## Message Format

Returns messages like:

- `Username is already in use.`
- `Email is already in use.`
