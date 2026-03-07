<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use DevinciIT\ShadowAuth\Core\Config;
use DevinciIT\ShadowAuth\Providers\FileUserProvider;
use DevinciIT\ShadowAuth\Utils\CSRF;

$isPublicMode = defined('SHADOW_AUTH_PUBLIC_MODE') && SHADOW_AUTH_PUBLIC_MODE;
$loginUrl = $isPublicMode ? shadow_auth_public_url('login') : '/views/login.php';
$registerUrl = $isPublicMode ? shadow_auth_public_url('register') : '/views/register.php';
$totpUrl = $isPublicMode ? shadow_auth_public_url('setup_2fa') : '/views/setup_2fa.php';

$provider = new FileUserProvider((string) Config::get('storage_path'));
$provider->initialize();

$error = null;
$success = null;

$standardUser = 'demo';
$standardPass = 'demo-password';

$twoFaUser = 'demo2fa';
$twoFaPass = 'demo-password';
$twoFaSecret = 'JBSWY3DPEHPK3PXP';
$issuer = 'ShadowAuth Demo';
$accountLabel = $issuer . ':' . $twoFaUser;
$provisioningUri = 'otpauth://totp/'
    . rawurlencode($accountLabel)
    . '?secret=' . rawurlencode($twoFaSecret)
    . '&issuer=' . rawurlencode($issuer)
    . '&algorithm=SHA1&digits=6&period=30';

$projectRoot = dirname(__DIR__);

$renderSourcePanel = static function (string $title, string $relativePath) use ($projectRoot): string {
    $fullPath = $projectRoot . '/' . ltrim($relativePath, '/');

    if (!is_file($fullPath)) {
        return '<details><summary>'
            . htmlspecialchars($title . ' (' . $relativePath . ')', ENT_QUOTES, 'UTF-8')
            . '</summary><p>File not found.</p></details>';
    }

    $code = file_get_contents($fullPath);
    if ($code === false) {
        return '<details><summary>'
            . htmlspecialchars($title . ' (' . $relativePath . ')', ENT_QUOTES, 'UTF-8')
            . '</summary><p>Could not read file.</p></details>';
    }

    return '<details><summary>'
        . htmlspecialchars($title . ' (' . $relativePath . ')', ENT_QUOTES, 'UTF-8')
        . '</summary><pre><code>'
        . htmlspecialchars($code, ENT_QUOTES, 'UTF-8')
        . '</code></pre></details>';
};

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!CSRF::validate($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid CSRF token.';
    } else {
        $users = $provider->all();
        $filtered = [];

        foreach ($users as $user) {
            $username = (string) ($user['username'] ?? '');
            if ($username === $standardUser || $username === $twoFaUser) {
                continue;
            }

            $filtered[] = $user;
        }

        $filtered[] = [
            'username' => $standardUser,
            'password_hash' => password_hash($standardPass, PASSWORD_DEFAULT),
            'totp_secret' => null,
            'totp_enabled' => false,
            'created_at' => date(DATE_ATOM),
        ];

        $filtered[] = [
            'username' => $twoFaUser,
            'password_hash' => password_hash($twoFaPass, PASSWORD_DEFAULT),
            'totp_secret' => $twoFaSecret,
            'totp_enabled' => true,
            'created_at' => date(DATE_ATOM),
        ];

        $export = var_export($filtered, true);
        $payload = "<?php\n\ndeclare(strict_types=1);\n\nreturn {$export};\n";
        $storagePath = (string) Config::get('storage_path');

        if (file_put_contents($storagePath, $payload, LOCK_EX) === false) {
            $error = 'Could not seed demo users.';
        } else {
            @chmod($storagePath, 0600);
            $success = 'Demo users seeded successfully.';
        }
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Shadow Auth Demo</title>
</head>
<body>
<h1>Shadow Auth Demo</h1>

<?php if ($error !== null): ?>
    <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if ($success !== null): ?>
    <p><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<h2>Seed Sample Users</h2>
<form method="post">
    <?= CSRF::input() ?>
    <button type="submit">Create / Reset Demo Users</button>
</form>

<h2>Sample Login (No 2FA)</h2>
<p>Username: <strong><?= htmlspecialchars($standardUser, ENT_QUOTES, 'UTF-8') ?></strong></p>
<p>Password: <strong><?= htmlspecialchars($standardPass, ENT_QUOTES, 'UTF-8') ?></strong></p>

<h2>Sample Login (With 2FA)</h2>
<p>Username: <strong><?= htmlspecialchars($twoFaUser, ENT_QUOTES, 'UTF-8') ?></strong></p>
<p>Password: <strong><?= htmlspecialchars($twoFaPass, ENT_QUOTES, 'UTF-8') ?></strong></p>
<p>TOTP Secret: <strong><?= htmlspecialchars($twoFaSecret, ENT_QUOTES, 'UTF-8') ?></strong></p>
<p>Issuer: <strong><?= htmlspecialchars($issuer, ENT_QUOTES, 'UTF-8') ?></strong></p>
<p>Account label: <strong><?= htmlspecialchars($accountLabel, ENT_QUOTES, 'UTF-8') ?></strong></p>

<h3>2FA Setup</h3>
<ol>
    <li>Open your authenticator app (Google Authenticator, Authy, 1Password, etc).</li>
    <li>Add account manually and enter the secret above, or use the provisioning URI below.</li>
    <li>Go to login and sign in with <strong><?= htmlspecialchars($twoFaUser, ENT_QUOTES, 'UTF-8') ?></strong>.</li>
    <li>You will be redirected to the 2FA verification page.</li>
    <li>Enter the current 6-digit code from your authenticator app.</li>
</ol>

<p>Provisioning URI (copy into apps/tools that support otpauth):</p>
<p><code><?= htmlspecialchars($provisioningUri, ENT_QUOTES, 'UTF-8') ?></code></p>

<h2>Required Fields & Implementation Map</h2>

<h3>Login Form</h3>
<p><strong>Required fields</strong>: <code>username</code>, <code>password</code>, <code>csrf_token</code></p>
<ul>
    <li>Form fields implemented in <code>DevinciIT\ShadowAuth\View\LoginForm</code>.</li>
    <li>POST processing implemented in <code>DevinciIT\ShadowAuth\Processors\LoginProcessor</code>.</li>
    <li>Auth logic implemented in <code>DevinciIT\ShadowAuth\Facade\Auth::beginLogin()</code>.</li>
</ul>

<h3>Register Form</h3>
<p><strong>Required fields</strong>: <code>username</code>, <code>password</code>, <code>confirm_password</code>, <code>csrf_token</code></p>
<p><strong>Optional extra fields</strong>: declared in the page (example: <code>email</code>) and passed to both the form and processor.</p>
<ul>
    <li>Form fields implemented in <code>DevinciIT\ShadowAuth\View\RegisterForm</code> via <code>setExtraFields()</code>.</li>
    <li>POST processing and validation implemented in <code>DevinciIT\ShadowAuth\Processors\RegisterProcessor</code>.</li>
    <li>Persistence implemented in <code>DevinciIT\ShadowAuth\Facade\Auth::registerWithData()</code>.</li>
</ul>

<h3>2FA Verify Form</h3>
<p><strong>Required fields</strong>: <code>totp_code</code>, <code>csrf_token</code></p>
<ul>
    <li>Form fields implemented in <code>DevinciIT\ShadowAuth\View\TotpForm</code>.</li>
    <li>POST processing implemented in <code>DevinciIT\ShadowAuth\Processors\TOTPProcessor</code>.</li>
    <li>Verification implemented in <code>DevinciIT\ShadowAuth\Facade\Auth::verifyPendingTotp()</code>.</li>
</ul>

<h2>Exact Files & Code by Page</h2>

<h3>Login Page</h3>
<?= $renderSourcePanel('Page', 'views/login.php') ?>
<?= $renderSourcePanel('Form class', 'src/View/LoginForm.php') ?>
<?= $renderSourcePanel('Processor class', 'src/Processors/LoginProcessor.php') ?>

<h3>Register Page</h3>
<?= $renderSourcePanel('Page', 'views/register.php') ?>
<?= $renderSourcePanel('Form class', 'src/View/RegisterForm.php') ?>
<?= $renderSourcePanel('Processor class', 'src/Processors/RegisterProcessor.php') ?>

<h3>2FA Verification Page</h3>
<?= $renderSourcePanel('Page', 'views/setup_2fa.php') ?>
<?= $renderSourcePanel('Form class', 'src/View/TotpForm.php') ?>
<?= $renderSourcePanel('Processor class', 'src/Processors/TOTPProcessor.php') ?>

<h2>Try It</h2>
<p><a href="<?= htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') ?>">Go to Login</a></p>
<p><a href="<?= htmlspecialchars($totpUrl, ENT_QUOTES, 'UTF-8') ?>">Go to 2FA Verification Page</a></p>
<p><a href="<?= htmlspecialchars($registerUrl, ENT_QUOTES, 'UTF-8') ?>">Go to Register</a></p>
</body>
</html>
