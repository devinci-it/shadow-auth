<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use DevinciIT\ShadowAuth\Facade\Auth;

$isPublicMode = defined('SHADOW_AUTH_PUBLIC_MODE') && SHADOW_AUTH_PUBLIC_MODE;
$loginUrl = $isPublicMode ? shadow_auth_public_url('login') : '/views/login.php';
$registerUrl = $isPublicMode ? shadow_auth_public_url('register') : '/views/register.php';
$logoutUrl = $isPublicMode ? shadow_auth_public_url('logout') : '/views/logout.php';
$setupTotpUrl = $isPublicMode ? shadow_auth_public_url('setup_2fa') . '&mode=setup' : '/views/setup_2fa.php?mode=setup';

Auth::requireAuth($loginUrl);

$user = Auth::user();
$username = (string) ($user['username'] ?? '');
?>
<h1>Dashboard</h1>
<p>Welcome, <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></p>
<p><a href="<?= htmlspecialchars($registerUrl, ENT_QUOTES, 'UTF-8') ?>">Register User</a></p>
<p><a href="<?= htmlspecialchars($setupTotpUrl, ENT_QUOTES, 'UTF-8') ?>">Setup / Manage 2FA</a></p>
<p><a href="<?= htmlspecialchars($logoutUrl, ENT_QUOTES, 'UTF-8') ?>">Logout</a></p>
