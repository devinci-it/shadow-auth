# `DevinciIT\\ShadowAuth\\Core\\Config`

Minimal static configuration container used across the library.

## Responsibilities

- Stores package configuration values in memory.
- Supports merge-based updates.
- Provides default fallbacks on read.

## Public Methods

- `set(array $values): void`: Merges values into current configuration.
- `get(string $key, $default = null)`: Reads value by key or default.
- `has(string $key): bool`: Checks key existence (including null values).

## Typical Keys

- `storage_path`
- `session_key`
- `totp_enabled`
- `registration_constraints`
