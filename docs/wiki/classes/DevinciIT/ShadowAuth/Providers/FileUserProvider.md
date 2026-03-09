# `DevinciIT\\ShadowAuth\\Providers\\FileUserProvider`

Flat-file persistence adapter using a PHP file that returns an array of users.

## Responsibilities

- Initializes storage directory/file with secure permissions.
- Reads all users from configured storage file.
- Finds users by username or arbitrary field.
- Checks existence by field with optional case-insensitive matching.
- Creates and updates user records atomically via file write.

## Public Methods

- `initialize(): void`: Ensures storage path and permissions.
- `all(): array`: Returns all user records.
- `findByUsername(string $username): ?array`
- `findByField(string $field, mixed $value, bool $caseInsensitive = false): ?array`
- `existsByField(string $field, mixed $value, bool $caseInsensitive = false): bool`
- `create(array $user): bool`
- `updateByUsername(string $username, array $newData): bool`

## Security Notes

- Storage file created with strict `0600` permissions.
- Uses `LOCK_EX` for writes.
- Data persisted with `var_export` into executable PHP return file.
