# Devinci Shadow Auth

A lightweight, file-based PHP authentication library focused on secure defaults and minimal integration overhead.

## Namespace

All library classes use:

```php
namespace Devinci\ShadowAuth;
```

Sub-namespaces follow the same root, for example:

- `Devinci\ShadowAuth\Core`
- `Devinci\ShadowAuth\Facade`
- `Devinci\ShadowAuth\Providers`
- `Devinci\ShadowAuth\Processors`
- `Devinci\ShadowAuth\Services`
- `Devinci\ShadowAuth\Utils`
- `Devinci\ShadowAuth\View`

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
composer require devinci/shadow-auth
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
        "devinci/shadow-auth": "*"
    }
}
```

Then run:

```bash
composer update devinci/shadow-auth
```

## Publish Demo Scaffold

After installing in a Composer project, publish an isolated ready-to-run demo scaffold with:

```bash
./vendor/bin/shadow-auth-publish-demo
```

This creates a self-contained folder:

- `shadow-auth-demo/bootstrap.php`
- `shadow-auth-demo/public/*`
- `shadow-auth-demo/views/*`

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

use Devinci\ShadowAuth\Core\Config;
use Devinci\ShadowAuth\Facade\Auth;

Config::set([
        'storage_path' => __DIR__ . '/storage/shadow.php',
        'totp_enabled' => true,
]);

Auth::boot();
```

### 2) Register User

```php
use Devinci\ShadowAuth\Facade\Auth;

$ok = Auth::register('alice', 'secure-password');
```

### 3) Login User (single-call)

```php
use Devinci\ShadowAuth\Facade\Auth;

$ok = Auth::attempt('alice', 'secure-password');
```

### 4) Login User (processor two-step with required TOTP)

```php
use Devinci\ShadowAuth\Facade\Auth;

$result = Auth::beginLogin('alice', 'secure-password');

if ($result === 'totp_required') {
    // redirect to setup_2fa.php and validate with verifyPendingTotp()
}
```

### 5) Check Session / Logout

```php
use Devinci\ShadowAuth\Facade\Auth;

if (Auth::check()) {
        $user = Auth::user();
}

Auth::logout();
```

## Minimal View Example

```php
<?php
require __DIR__ . '/../bootstrap.php';

use Devinci\ShadowAuth\Processors\LoginProcessor;
use Devinci\ShadowAuth\View\Forms;
use Devinci\ShadowAuth\Core\Flash;

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
