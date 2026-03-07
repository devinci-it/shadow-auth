<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use DevinciIT\ShadowAuth\Core\Flash;
use DevinciIT\ShadowAuth\Processors\ForgotPasswordProcessor;
use DevinciIT\ShadowAuth\View\ForgotPasswordForm;

$isPublicMode = defined('SHADOW_AUTH_PUBLIC_MODE') && SHADOW_AUTH_PUBLIC_MODE;
$loginUrl = $isPublicMode ? shadow_auth_public_url('login') : '/views/login.php';
$resetUrl = $isPublicMode ? shadow_auth_public_url('reset_password') : '/views/reset_password.php';

$processor = new ForgotPasswordProcessor($resetUrl);
$processor->handle();
$message = Flash::get();
$form = new ForgotPasswordForm();
?>
<h1>Forgot Password</h1>
<p>Enter your username or email to request a password reset.</p>
<?php if ($message !== null): ?>
    <p class="flash flash-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>
<form method="post">
    <?= $form->render() ?>
    <button type="submit">Generate Reset Link</button>
</form>
<p><a href="<?= htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') ?>">Back to Login</a></p>
