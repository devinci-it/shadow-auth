# `DevinciIT\\ShadowAuth\\Processors\\BaseProcessor`

Abstract base class for HTTP form processors.

## Responsibilities

- Defines common request handling contract.
- Provides HTTP method check helper.
- Provides redirect helper with immediate termination.

## Methods

- `handle(): void` (abstract): Entry point implemented by concrete processors.
- `isPost(): bool`: True when request method is `POST`.
- `redirect(string $path): void`: Sends `Location` header and exits.
