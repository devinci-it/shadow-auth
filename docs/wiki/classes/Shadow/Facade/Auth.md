# `Shadow\\Facade\\Auth`

Compatibility alias facade that forwards static calls to `DevinciIT\\ShadowAuth\\Facade\\Auth`.

## Responsibilities

- Preserves legacy namespace usage (`Shadow\\Facade\\Auth`).
- Explicitly exposes `requireAuth()`.
- Forwards all other static calls via `__callStatic`.

## Public Methods

- `requireAuth(string $redirectTo = '/views/login.php'): void`
- `__callStatic(string $name, array $arguments): mixed`

## Error Handling

`__callStatic` throws `BadMethodCallException` when the target static method does not exist.
