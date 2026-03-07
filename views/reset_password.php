<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use DevinciIT\ShadowAuth\Core\Flash;
use DevinciIT\ShadowAuth\Facade\Auth;
use DevinciIT\ShadowAuth\Processors\ResetPasswordProcessor;
use DevinciIT\ShadowAuth\View\ResetPasswordForm;

$isPublicMode = defined('SHADOW_AUTH_PUBLIC_MODE') && SHADOW_AUTH_PUBLIC_MODE;
$loginUrl = $isPublicMode ? shadow_auth_public_url('login') : '/views/login.php';
$forgotPasswordUrl = $isPublicMode ? shadow_auth_public_url('forgot_password') : '/views/forgot_password.php';

$processor = new ResetPasswordProcessor($loginUrl);
$processor->handle();
$message = Flash::get();

$token = trim((string) ($_GET['token'] ?? $_POST['reset_token'] ?? ''));
$form = (new ResetPasswordForm())->setToken($token);
$tokenProvided = $token !== '';
$tokenValid = $tokenProvided && Auth::hasValidPasswordResetToken($token);
?>
<h1>Reset Password</h1>
<?php if ($message !== null): ?>
    <p class="flash flash-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!$tokenProvided): ?>
    <p>No reset token was provided. Request a fresh link first.</p>
    <p><a href="<?= htmlspecialchars($forgotPasswordUrl, ENT_QUOTES, 'UTF-8') ?>">Go to Forgot Password</a></p>
<?php elseif (!$tokenValid): ?>
    <p>This reset token is invalid or expired. Request a fresh link.</p>
    <p><a href="<?= htmlspecialchars($forgotPasswordUrl, ENT_QUOTES, 'UTF-8') ?>">Request New Reset Link</a></p>
<?php else: ?>
    <p>Set your new password below.</p>
    <form method="post">
        <?= $form->render() ?>
        <button type="submit">Reset Password</button>
    </form>
<?php endif; ?>

<p><a href="<?= htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') ?>">Back to Login</a></p>
