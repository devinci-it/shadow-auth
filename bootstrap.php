<?php
declare(strict_types=1);

use DevinciIT\ShadowAuth\Core\Config;
use DevinciIT\ShadowAuth\Facade\Auth;

if (!defined('SHADOW_AUTH_BOOTSTRAPPED')) {
    define('SHADOW_AUTH_BOOTSTRAPPED', true);

    require_once __DIR__ . '/vendor/autoload.php';

    // Default values ONLY if not already set
    if (!Config::has('storage_path')) {
        Config::set([
            'storage_path' => __DIR__ . '/storage/shadow.php'
        ]);
    }

    if (!Config::has('totp_enabled')) {
        Config::set([
            'totp_enabled' => true
        ]);
    }

    if (!Config::has('session_key')) {
        Config::set([
            'session_key' => 'shadow_auth_user'
        ]);
    }

    if (!Config::has('registration_constraints')) {
        Config::set([
            'registration_constraints' => [
                // Add fields here to enforce uniqueness during registration.
                'unique_fields' => ['username'],
                // Fields listed here are compared in case-insensitive mode.
                'case_insensitive_fields' => ['username', 'email'],
            ],
        ]);
    }

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    Auth::boot();
}