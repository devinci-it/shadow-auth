<?php
declare(strict_types=1);

use Devinci\ShadowAuth\Core\Config;
use Devinci\ShadowAuth\Facade\Auth;

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

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    Auth::boot();
}