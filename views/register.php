<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Devinci\ShadowAuth\Core\Flash;
use Devinci\ShadowAuth\Processors\RegisterProcessor;
use Devinci\ShadowAuth\View\RegisterForm;

$isPublicMode = defined('SHADOW_AUTH_PUBLIC_MODE') && SHADOW_AUTH_PUBLIC_MODE;
$loginUrl = $isPublicMode ? shadow_auth_public_url('login') : '/views/login.php';

$extraFields = [
    [
        'name' => 'email',
        'label' => 'Email',
        'type' => 'email',
        'autocomplete' => 'email',
        'required' => true,
    ],
];

$processor = new RegisterProcessor($loginUrl, $extraFields);
$processor->handle();
$message = Flash::get();
$form = (new RegisterForm())->setExtraFields($extraFields);
?>
<h1>Register</h1>
<?php if ($message !== null): ?>
    <p class="flash flash-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>
<form method="post">
    <?= $form->render() ?>
    <button type="submit">Register</button>
</form>
<p><a href="<?= htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') ?>">Back to Login</a></p>
