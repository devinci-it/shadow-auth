# Devinci Shadow Auth

A lightweight, file-based PHP authentication library with optional TOTP 2FA, CSRF protection, and simple processor/form helpers for classic server-rendered apps.

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Architecture](#architecture)
- [Configuration](#configuration)
- [Auth Flows](#auth-flows)
- [Facade API](#facade-api)
- [Publishing Demo, Endpoints, and Wiki](#publishing-demo-endpoints-and-wiki)
- [Class Wiki](#class-wiki)
- [Security Notes](#security-notes)
- [Release and Tagging](#release-and-tagging)
- [License](#license)

## Requirements

- PHP `>=8.1`
- Composer (for package install and autoload)

## Installation

Install through Composer:

```bash
composer require devinci-it/shadow-auth
```

If you are developing this package locally in another app, use a Composer path repository:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "../devinci-it-shadow-auth",
      "options": {
        "symlink": true
      }
    }
  ],
  "require": {
    "devinci-it/shadow-auth": "*"
  }
}
```

Then update dependencies:

```bash
composer update devinci-it/shadow-auth
```

## Quick Start

### 1. Bootstrap

```php
<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use DevinciIT\ShadowAuth\Core\Config;
use DevinciIT\ShadowAuth\Facade\Auth;

Config::set([
    'storage_path' => __DIR__ . '/storage/shadow.php',
    'session_key' => 'shadow_auth_user',
    'totp_enabled' => true,
    'registration_constraints' => [
        'unique_fields' => ['username', 'email'],
        'case_insensitive_fields' => ['username', 'email'],
    ],
]);

Auth::boot();
```

### 2. Register

```php
$ok = Auth::registerWithData('alice', 'secure-password', [
    'email' => 'alice@example.com',
]);
```

### 3. Login

```php
$result = Auth::beginLogin('alice', 'secure-password');

if ($result === 'authenticated') {
    // Logged in
}

if ($result === 'totp_required') {
    // Show TOTP form and call Auth::verifyPendingTotp($code)
}
```

### 4. Route Guard

```php
Auth::requireAuth('/views/login.php');
```

## Architecture

Namespace root: `DevinciIT\ShadowAuth\`

Main components:

- `Core`: business logic managers and config.
- `Facade`: static API for app code.
- `Providers`: persistence layer (`FileUserProvider`).
- `Processors`: request handlers for login/register/reset/TOTP forms.
- `Services`: reusable services (`TwoFactorService`).
- `Utils`: utility helpers (`CSRF`).
- `View`: form builders with CSRF injection.
- `Publisher`: file publishing utilities for demo/endpoints/wiki.
- `Shadow\Facade\Auth`: compatibility alias facade.

## Configuration

Configure with `DevinciIT\ShadowAuth\Core\Config::set([...])`.

Supported keys:

- `storage_path` (`string`, required): path to PHP array storage file.
- `session_key` (`string`, optional): auth session key. Default: `shadow_auth_user`.
- `totp_enabled` (`bool`, optional): global TOTP toggle. Default: `true`.
- `registration_constraints` (`array`, optional): unique/case-insensitive field rules.

Example:

```php
Config::set([
    'storage_path' => __DIR__ . '/storage/shadow.php',
    'totp_enabled' => true,
    'registration_constraints' => [
        'unique_fields' => ['username', 'email'],
        'case_insensitive_fields' => ['username', 'email'],
    ],
]);
```

## Auth Flows

### Username/password only

1. `Auth::beginLogin($username, $password)`
2. Returns `authenticated` and session is established.

### Username/password + TOTP

1. `Auth::beginLogin(...)`
2. Returns `totp_required` and writes pending state in session.
3. `Auth::verifyPendingTotp($code)` finalizes login.

### Password reset

1. `Auth::requestPasswordResetToken($identifier)` returns token (demo mode usage).
2. Validate before submit with `Auth::hasValidPasswordResetToken($token)`.
3. Complete with `Auth::resetPasswordWithToken($token, $newPassword)`.

## Facade API

Available methods in `DevinciIT\ShadowAuth\Facade\Auth`:

- `boot(): void`
- `register(string $username, string $password): bool`
- `registerWithData(string $username, string $password, array $attributes): bool`
- `registrationError(): ?string`
- `attempt(string $username, string $password, ?string $totp = null): bool`
- `beginLogin(string $username, string $password): string`
- `verifyPendingTotp(string $code): bool`
- `isTotpPending(): bool`
- `pendingUsername(): ?string`
- `check(): bool`
- `requireAuth(string $redirectTo = '/views/login.php'): void`
- `user(): ?array`
- `logout(): void`
- `setupTotpSecret(string $username): ?string`
- `confirmTotp(string $username, string $code): bool`
- `disableTotp(string $username): bool`
- `enableTotp(): void`
- `disableTotpGlobally(): void`
- `requestPasswordResetToken(string $identifier): ?string`
- `hasValidPasswordResetToken(string $token): bool`
- `resetPasswordWithToken(string $token, string $newPassword): bool`

## Publishing Demo, Endpoints, and Wiki

This package includes `bin/shadow-auth-publish-demo` with multiple modes.

```bash
# Full demo scaffold into ./shadow-auth-demo
./vendor/bin/shadow-auth-publish-demo

# Overwrite existing files
./vendor/bin/shadow-auth-publish-demo --force

# Publish only public assets into host project ./public
./vendor/bin/shadow-auth-publish-demo --public

# Publish only views into host project ./views
./vendor/bin/shadow-auth-publish-demo --endpoints

# Publish only wiki scaffold into ./shadow-auth-wiki
./vendor/bin/shadow-auth-publish-demo --wiki
```

Composer script aliases:

```bash
composer publish-demo
composer publish-demo-force
composer publish-endpoints
composer publish-endpoints-force
```

## Class Wiki

Detailed per-class documentation is under `docs/wiki/`.

- Wiki index: `docs/wiki/README.md`
- Class pages: `docs/wiki/classes/`

## Security Notes

- Keep storage files outside the public web root where possible.
- Apply restrictive permissions (`0700` for directory, `0600` for file).
- Always use HTTPS in production.
- Regenerate session IDs after successful authentication.
- Validate CSRF tokens on all state-changing requests.
- Treat demo password reset tokens as sensitive and short-lived.

## Release and Tagging

Create an annotated tag with helper script:

```bash
./bin/shadow-auth-tag-release 0.0.3
./bin/shadow-auth-tag-release 0.0.3 --push
```

Composer aliases:

```bash
composer release-tag -- 0.0.3
composer release-tag-push -- 0.0.3
```

## License

MIT
