# Devinci Shadow Auth

A lightweight, file-based PHP authentication library focused on secure defaults and minimal integration overhead.

## Namespace

All library classes use:

```php
namespace DevinciIT\ShadowAuth;
```

Sub-namespaces follow the same root, for example:

- `DevinciIT\ShadowAuth\Core`
- `DevinciIT\ShadowAuth\Facade`
- `DevinciIT\ShadowAuth\Providers`
- `DevinciIT\ShadowAuth\Processors`
- `DevinciIT\ShadowAuth\Services`
- `DevinciIT\ShadowAuth\Utils`
- `DevinciIT\ShadowAuth\View`

## Features

- Secure password hashing (`password_hash`, `password_verify`)
- File-based user provider (no database required)
- Optional TOTP 2FA support
- CSRF token generation and validation
- Session flash messaging
- Simple static facade for common auth actions
- Processor classes for clean form handling

## Suggested Structure

```text
shadow-auth/
├── src/
│   ├── Core/
│   │   ├── AuthManager.php
│   │   ├── Config.php
│   │   ├── Flash.php
│   │   └── RegistrationManager.php
│   ├── Facade/
│   │   └── Auth.php
│   ├── Processors/
│   │   ├── BaseProcessor.php
│   │   ├── LoginProcessor.php
│   │   ├── RegisterProcessor.php
│   │   └── TOTPProcessor.php
│   ├── Providers/
│   │   └── FileUserProvider.php
│   ├── Services/
│   │   └── TwoFactorService.php
│   ├── Utils/
│   │   └── CSRF.php
│   └── View/
│       └── Forms.php
├── storage/
│   └── shadow.php
├── views/
│   ├── login.php
│   ├── register.php
│   ├── setup_2fa.php
│   ├── dashboard.php
│   └── logout.php
└── bootstrap.php
```

## Installation

If using Composer in your app:

```bash
composer require devinci-it/shadow-auth
```

If this package is local/private, add it as a path repository in your app `composer.json`.

### Local Path Package (Development)

In your consuming app, point Composer to this package folder:

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

Then run:

```bash
composer update devinci-it/shadow-auth
```

## Publish Demo Scaffold

After installing in a Composer project, publish an isolated ready-to-run demo scaffold with:

```bash
./vendor/bin/shadow-auth-publish-demo
```

This creates a self-contained folder:

- `shadow-auth-demo/composer.json`
- `shadow-auth-demo/bootstrap.php`
- `shadow-auth-demo/public/*`
- `shadow-auth-demo/views/*`

Install demo dependencies inside the published demo folder:

```bash
cd shadow-auth-demo
composer install
```

Note on versions: for `0.0.x` packages, Composer caret ranges like `^0.0.1` are very strict and do not auto-upgrade to `0.0.2`. Use a range like `>=0.0.1 <0.1.0` when you want updates across `0.0.x` tags.

To overwrite existing published files:

```bash
./vendor/bin/shadow-auth-publish-demo --force
```

You can also run the Composer script alias:

```bash
composer publish-demo
```

To overwrite existing files in that demo folder:

```bash
composer publish-demo-force
```

Publish only endpoints (`views/`) into the current project root:

```bash
composer publish-endpoints
composer publish-endpoints-force
```

Publish only demo public assets (`public/`) into the current project root:

```bash
./vendor/bin/shadow-auth-publish-demo --public
```

## Publish Commands Summary

- `composer publish-demo` → isolated `shadow-auth-demo/` scaffold
- `composer publish-demo-force` → overwrite inside `shadow-auth-demo/`
- `composer publish-endpoints` → copy package `views/` to host `views/`
- `composer publish-endpoints-force` → overwrite host `views/`

## Version Tagging

Use the built-in release helper to create semantic version tags.

Create a local tag:

```bash
./bin/shadow-auth-tag-release 0.0.3
```

Create and push a tag:

```bash
./bin/shadow-auth-tag-release 0.0.3 --push
```

Composer shortcuts:

```bash
composer release-tag -- 0.0.3
composer release-tag -- 0.0.3 --push
```

Notes:

- Tags are created as annotated tags: `v<version>`.
- The script validates SemVer and blocks duplicate local/remote tags.

## Composer Autoload

In the package `composer.json`, map the namespace root:

```json
{
    "autoload": {
        "psr-4": {
            "Devinci\\ShadowAuth\\": "src/"
        }
    }
}
```

Then run:

```bash
composer dump-autoload
```

## Quick Start

### 1) Bootstrap

```php
<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use DevinciIT\ShadowAuth\Core\Config;
use DevinciIT\ShadowAuth\Facade\Auth;

Config::set([
        'storage_path' => __DIR__ . '/storage/shadow.php',
        'totp_enabled' => true,
    'registration_constraints' => [
        // Enforce uniqueness for any attributes persisted by registration.
        'unique_fields' => ['username', 'email'],
        // Compare selected fields case-insensitively.
        'case_insensitive_fields' => ['username', 'email'],
    ],
]);

Auth::boot();
```

### Registration Constraints

`registration_constraints` lets developers define uniqueness rules without changing processor logic.

```php
Config::set([
    'registration_constraints' => [
        'unique_fields' => ['username', 'email'],
        'case_insensitive_fields' => ['username', 'email'],
    ],
]);
```

If a constraint fails, registration now returns a field-specific message like `Email is already in use.`.

### 2) Register User

```php
use DevinciIT\ShadowAuth\Facade\Auth;

$ok = Auth::register('alice', 'secure-password');
```

### 3) Login User (single-call)

```php
use DevinciIT\ShadowAuth\Facade\Auth;

$ok = Auth::attempt('alice', 'secure-password');
```

### 4) Login User (processor two-step with required TOTP)

```php
use DevinciIT\ShadowAuth\Facade\Auth;

$result = Auth::beginLogin('alice', 'secure-password');

if ($result === 'totp_required') {
    // redirect to setup_2fa.php and validate with verifyPendingTotp()
}
```

### 5) Check Session / Logout

```php
use DevinciIT\ShadowAuth\Facade\Auth;

if (Auth::check()) {
        $user = Auth::user();
}

Auth::logout();
```

## Minimal View Example

```php
<?php
require __DIR__ . '/../bootstrap.php';

use DevinciIT\ShadowAuth\Processors\LoginProcessor;
use DevinciIT\ShadowAuth\View\Forms;
use DevinciIT\ShadowAuth\Core\Flash;

$processor = new LoginProcessor();
$processor->handle();
?>

<form method="post">
        <?= Forms::renderInputs('login') ?>
        <button type="submit">Login</button>
</form>

<p><?= Flash::get() ?></p>
```

## Security Notes

- Keep `storage/shadow.php` outside the public web root
- Apply restrictive permissions (recommended `0600`)
- Always use HTTPS in production
- Regenerate sessions after successful login
- Validate CSRF tokens on all state-changing requests

## API (Facade)

Implemented facade methods:

- `Auth::boot()`
- `Auth::register(string $username, string $password): bool`
- `Auth::attempt(string $username, string $password, ?string $totp = null): bool`
- `Auth::beginLogin(string $username, string $password): string` (`authenticated|totp_required|failed`)
- `Auth::verifyPendingTotp(string $code): bool`
- `Auth::isTotpPending(): bool`
- `Auth::pendingUsername(): ?string`
- `Auth::check(): bool`
- `Auth::user(): ?array`
- `Auth::logout(): void`
- `Auth::enableTotp(): void`
- `Auth::disableTotpGlobally(): void`
- `Auth::confirmTotp(string $username, string $code): bool`
- `Auth::disableTotp(string $username): bool`

## VCS Release Guide (v0.0.1)

Use this when preparing your first public release.

1. Ensure repository is clean and ready:

```bash
git status
```

2. Commit changes:

```bash
git add .
git commit -m "release: prepare v0.0.1"
```

3. Tag release:

```bash
git tag v0.0.1
```

4. Push branch and tags:

```bash
git push origin main
git push origin v0.0.1
```

## License

MIT
