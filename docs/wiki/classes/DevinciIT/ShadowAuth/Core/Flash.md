# `DevinciIT\\ShadowAuth\\Core\\Flash`

Session-based one-time messaging helper for post/redirect/get flows.

## Responsibilities

- Writes a message into session.
- Returns and clears message on read.
- Lazily starts PHP session when needed.

## Public Methods

- `set(string $message): void`: Stores flash message under internal key.
- `get(): ?string`: Reads and removes message, returning `null` when absent.
