<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Devinci\ShadowAuth\Core\Config;
use Devinci\ShadowAuth\Providers\FileUserProvider;
use Devinci\ShadowAuth\Utils\CSRF;

$isPublicMode = defined('SHADOW_AUTH_PUBLIC_MODE') && SHADOW_AUTH_PUBLIC_MODE;
$overviewUrl = $isPublicMode ? shadow_auth_public_url('overview') : '/views/overview.php';
$loginUrl = $isPublicMode ? shadow_auth_public_url('login') : '/views/login.php';
$registerUrl = $isPublicMode ? shadow_auth_public_url('register') : '/views/register.php';
$setup2faUrl = $isPublicMode ? shadow_auth_public_url('setup_2fa') . '&mode=setup' : '/views/setup_2fa.php?mode=setup';
$dashboardUrl = $isPublicMode ? shadow_auth_public_url('dashboard') : '/views/dashboard.php';

$provider = new FileUserProvider((string) Config::get('storage_path'));
$provider->initialize();

$error = null;
$success = null;

$standardUser = 'demo';
$standardPass = 'demo-password';
$twoFaUser = 'demo2fa';
$twoFaPass = 'demo-password';
$twoFaSecret = 'JBSWY3DPEHPK3PXP';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && (($_POST['action'] ?? '') === 'seed_demo_users')) {
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

$storagePath = (string) Config::get('storage_path');
$storageExists = is_file($storagePath);
$storagePerms = $storageExists ? substr(sprintf('%o', fileperms($storagePath) ?: 0), -4) : 'n/a';
$storageReadable = $storageExists && is_readable($storagePath);
$storageWritable = $storageExists && is_writable($storagePath);
$storageRaw = $storageExists ? (file_get_contents($storagePath) ?: '') : '';
$currentUsers = $provider->all();

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
?>
<h1>Shadow Auth Overview</h1>

<p>
    <a href="<?= htmlspecialchars($overviewUrl, ENT_QUOTES, 'UTF-8') ?>">Overview</a> |
    <a href="<?= htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') ?>">Login</a> |
    <a href="<?= htmlspecialchars($registerUrl, ENT_QUOTES, 'UTF-8') ?>">Register</a> |
    <a href="<?= htmlspecialchars($setup2faUrl, ENT_QUOTES, 'UTF-8') ?>">Setup 2FA</a> |
    <a href="<?= htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8') ?>">Dashboard</a>
</p>

<?php if ($error !== null): ?>
    <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>
<?php if ($success !== null): ?>
    <p><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<h2>Reset / Seed Demo Credentials</h2>
<p>Standard user: <strong><?= htmlspecialchars($standardUser, ENT_QUOTES, 'UTF-8') ?></strong> / <strong><?= htmlspecialchars($standardPass, ENT_QUOTES, 'UTF-8') ?></strong></p>
<p>2FA user: <strong><?= htmlspecialchars($twoFaUser, ENT_QUOTES, 'UTF-8') ?></strong> / <strong><?= htmlspecialchars($twoFaPass, ENT_QUOTES, 'UTF-8') ?></strong></p>
<form method="post" action="<?= htmlspecialchars($overviewUrl, ENT_QUOTES, 'UTF-8') ?>">
    <?= CSRF::input() ?>
    <input type="hidden" name="action" value="seed_demo_users">
    <button type="submit">Create / Reset Demo Users</button>
</form>

<h2>Current Shadow Storage Status</h2>
<ul>
    <li>Storage path: <code><?= htmlspecialchars($storagePath, ENT_QUOTES, 'UTF-8') ?></code></li>
    <li>File exists: <strong><?= $storageExists ? 'Yes' : 'No' ?></strong></li>
    <li>Permissions (octal): <strong><?= htmlspecialchars($storagePerms, ENT_QUOTES, 'UTF-8') ?></strong></li>
    <li>Readable: <strong><?= $storageReadable ? 'Yes' : 'No' ?></strong></li>
    <li>Writable: <strong><?= $storageWritable ? 'Yes' : 'No' ?></strong></li>
</ul>

<h3>Current Users (Parsed)</h3>
<?php if ($currentUsers === []): ?>
    <p>No users found.</p>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>Username</th>
            <th>2FA Enabled</th>
            <th>Has Secret</th>
            <th>Created At</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($currentUsers as $user): ?>
            <tr>
                <td><?= htmlspecialchars((string) ($user['username'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= !empty($user['totp_enabled']) ? 'Yes' : 'No' ?></td>
                <td><?= !empty($user['totp_secret']) ? 'Yes' : 'No' ?></td>
                <td><?= htmlspecialchars((string) ($user['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<h3>Current Shadow File Content (Raw)</h3>
<pre><code><?= htmlspecialchars($storageRaw, ENT_QUOTES, 'UTF-8') ?></code></pre>

<h2>Feature Overview</h2>
<ul>
    <li>File-based authentication with secure password hashing.</li>
    <li>Login, registration, and session management via facade + processors.</li>
    <li>TOTP 2FA setup and verification flow.</li>
    <li>CSRF protection for state-changing actions.</li>
    <li>Flash messaging for user feedback.</li>
</ul>

<h2>Important Files</h2>
<?= $renderSourcePanel('Bootstrap entry', 'bootstrap.php') ?>
<?= $renderSourcePanel('Public router', 'public/index.php') ?>
<?= $renderSourcePanel('Auth facade', 'src/Facade/Auth.php') ?>
<?= $renderSourcePanel('Auth manager', 'src/Core/AuthManager.php') ?>
<?= $renderSourcePanel('User provider', 'src/Providers/FileUserProvider.php') ?>
<?= $renderSourcePanel('2FA service', 'src/Services/TwoFactorService.php') ?>
