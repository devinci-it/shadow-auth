<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Devinci\ShadowAuth\Core\Flash;
use Devinci\ShadowAuth\Processors\LoginProcessor;
use Devinci\ShadowAuth\View\LoginForm;

$isPublicMode = defined('SHADOW_AUTH_PUBLIC_MODE') && SHADOW_AUTH_PUBLIC_MODE;
$dashboardUrl = $isPublicMode ? shadow_auth_public_url('dashboard') : '/views/dashboard.php';
$totpUrl = $isPublicMode ? shadow_auth_public_url('setup_2fa') : '/views/setup_2fa.php';
$registerUrl = $isPublicMode ? shadow_auth_public_url('register') : '/views/register.php';
$demoUrl = $isPublicMode ? shadow_auth_public_url('home') : '/views/demo.php';

$processor = new LoginProcessor($dashboardUrl, $totpUrl);
$processor->handle();
$message = Flash::get();
$form = new LoginForm();
?>
<h1>Login</h1>
<?php if ($message !== null): ?>
    <p class="flash flash-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>
<form method="post">
    <?= $form->render() ?>
    <button type="submit">Login</button>
</form>
<p><a href="<?= htmlspecialchars($registerUrl, ENT_QUOTES, 'UTF-8') ?>">Create Account</a></p>
<p><a href="<?= htmlspecialchars($demoUrl, ENT_QUOTES, 'UTF-8') ?>">Open Demo Credentials Page</a></p>
