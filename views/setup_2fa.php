<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use DevinciIT\ShadowAuth\Core\Config;
use DevinciIT\ShadowAuth\Core\Flash;
use DevinciIT\ShadowAuth\Facade\Auth;
use DevinciIT\ShadowAuth\Processors\TOTPProcessor;
use DevinciIT\ShadowAuth\Providers\FileUserProvider;
use DevinciIT\ShadowAuth\Utils\CSRF;
use DevinciIT\ShadowAuth\View\TotpForm;

$isPublicMode = defined('SHADOW_AUTH_PUBLIC_MODE') && SHADOW_AUTH_PUBLIC_MODE;
$dashboardUrl = $isPublicMode ? shadow_auth_public_url('dashboard') : '/views/dashboard.php';
$loginUrl = $isPublicMode ? shadow_auth_public_url('login') : '/views/login.php';

$mode = isset($_GET['mode']) && is_string($_GET['mode']) ? $_GET['mode'] : 'verify';
if ($mode !== 'setup') {
    $mode = 'verify';
}

$setupUrl = $isPublicMode
    ? shadow_auth_public_url('setup_2fa') . '&mode=setup'
    : '/views/setup_2fa.php?mode=setup';
$verifyUrl = $isPublicMode
    ? shadow_auth_public_url('setup_2fa') . '&mode=verify'
    : '/views/setup_2fa.php?mode=verify';

$autoLoginRequested = isset($_GET['autologin']) && (string) $_GET['autologin'] === '1';

$issuer = 'ShadowAuth';
$setupMessage = null;
$setupError = null;
$secret = null;
$setupEnabled = false;
$username = '';

if ($mode === 'verify') {
    if (!Auth::isTotpPending() && $autoLoginRequested) {
        $autoLoginResult = Auth::beginLogin('demo2fa', 'demo-password');
        if ($autoLoginResult === 'totp_required') {
            Flash::set('Auto login started for demo2fa. Enter your 2FA code to continue.');
            header('Location: ' . $verifyUrl);
            exit;
        }

        if ($autoLoginResult === 'authenticated') {
            Flash::set('Auto login successful for demo2fa.');
            header('Location: ' . $dashboardUrl);
            exit;
        }

        Flash::set('Auto login failed for demo2fa.');
        header('Location: ' . $loginUrl);
        exit;
    }

    $processor = new TOTPProcessor($dashboardUrl, $loginUrl);
    $processor->handle();
    $message = Flash::get();
    $form = new TotpForm();

    if (!Auth::isTotpPending()) {
        header('Location: ' . $loginUrl);
        exit;
    }

    $username = (string) Auth::pendingUsername();
} else {
    if (!Auth::check() && $autoLoginRequested) {
        $autoLoginResult = Auth::beginLogin('demo', 'demo-password');
        if ($autoLoginResult === 'authenticated') {
            Flash::set('Auto login successful for demo.');
            header('Location: ' . $setupUrl);
            exit;
        }

        if ($autoLoginResult === 'totp_required') {
            Flash::set('Auto login for demo requires 2FA verification first.');
            header('Location: ' . $verifyUrl);
            exit;
        }

        Flash::set('Auto login failed for demo.');
        header('Location: ' . $loginUrl);
        exit;
    }

    if (!Auth::check()) {
        header('Location: ' . $loginUrl);
        exit;
    }

    $sessionUser = Auth::user();
    $username = (string) ($sessionUser['username'] ?? '');
    if ($username === '') {
        header('Location: ' . $loginUrl);
        exit;
    }

    $provider = new FileUserProvider((string) Config::get('storage_path'));
    $provider->initialize();

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        if (!CSRF::validate($_POST['csrf_token'] ?? null)) {
            $setupError = 'Invalid CSRF token.';
        } else {
            $action = isset($_POST['action']) && is_string($_POST['action']) ? $_POST['action'] : '';

            if ($action === 'generate') {
                $generatedSecret = Auth::setupTotpSecret($username);
                if ($generatedSecret === null) {
                    $setupError = 'Could not generate TOTP secret.';
                } else {
                    $setupMessage = 'TOTP secret generated. Scan the QR and confirm with a 6-digit code.';
                }
            }

            if ($action === 'confirm') {
                $code = preg_replace('/\s+/', '', (string) ($_POST['totp_code'] ?? ''));
                if (!is_string($code) || $code === '') {
                    $setupError = 'TOTP code is required.';
                } elseif (!Auth::confirmTotp($username, $code)) {
                    $setupError = 'Invalid TOTP code for setup confirmation.';
                } else {
                    $setupMessage = 'TOTP setup completed successfully.';
                }
            }
        }
    }

    $userRecord = $provider->findByUsername($username);
    if ($userRecord !== null) {
        $secret = (string) ($userRecord['totp_secret'] ?? '');
        if ($secret === '') {
            $secret = null;
        }

        $setupEnabled = (bool) ($userRecord['totp_enabled'] ?? false);
    }
}
?>
<h1><?= $mode === 'setup' ? 'Setup 2FA (TOTP)' : 'Verify 2FA (TOTP)' ?></h1>
<p>
    <a href="<?= htmlspecialchars($setupUrl, ENT_QUOTES, 'UTF-8') ?>">TOTP Setup</a>
    |
    <a href="<?= htmlspecialchars($verifyUrl, ENT_QUOTES, 'UTF-8') ?>">TOTP Verification</a>
</p>

<?php if ($mode === 'verify'): ?>
    <?php if ($message !== null): ?>
        <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <p>This page is only for login-time verification when TOTP is required.</p>
    <p>Username: <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></p>
    <form method="post">
        <?= $form->render() ?>
        <button type="submit">Verify</button>
    </form>
<?php else: ?>
    <?php if ($setupError !== null): ?>
        <p><?= htmlspecialchars($setupError, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <?php if ($setupMessage !== null): ?>
        <p><?= htmlspecialchars($setupMessage, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <p>Username: <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></p>
    <p>Status: <strong><?= $setupEnabled ? 'Enabled' : 'Not enabled' ?></strong></p>

    <form method="post">
        <?= CSRF::input() ?>
        <input type="hidden" name="action" value="generate">
        <button type="submit"><?= $secret === null ? 'Generate TOTP Secret' : 'Regenerate TOTP Secret' ?></button>
    </form>

    <?php if ($secret !== null): ?>
        <?php
            $accountLabel = $issuer . ':' . $username;
            $provisioningUri = 'otpauth://totp/'
                . rawurlencode($accountLabel)
                . '?secret=' . rawurlencode($secret)
                . '&issuer=' . rawurlencode($issuer)
                . '&algorithm=SHA1&digits=6&period=30';
            $qrUrl = 'https://quickchart.io/qr?size=220&text=' . rawurlencode($provisioningUri);
        ?>
        <h2>Authenticator Setup</h2>
        <p>Scan this QR code in Google Authenticator, Authy, 1Password, or another TOTP app.</p>
        <p><img src="<?= htmlspecialchars($qrUrl, ENT_QUOTES, 'UTF-8') ?>" alt="TOTP QR code"></p>
        <p>Secret: <code><?= htmlspecialchars($secret, ENT_QUOTES, 'UTF-8') ?></code></p>
        <p>Provisioning URI: <code><?= htmlspecialchars($provisioningUri, ENT_QUOTES, 'UTF-8') ?></code></p>

        <h2>Confirm Setup</h2>
        <form method="post">
            <?= CSRF::input() ?>
            <input type="hidden" name="action" value="confirm">
            <label for="totp_code">Enter current 6-digit code</label>
            <input id="totp_code" name="totp_code" type="text" inputmode="numeric" pattern="[0-9]{6}" autocomplete="one-time-code" required>
            <button type="submit">Confirm and Enable 2FA</button>
        </form>
    <?php endif; ?>

    <p><a href="<?= htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8') ?>">Back to Dashboard</a></p>
<?php endif; ?>
