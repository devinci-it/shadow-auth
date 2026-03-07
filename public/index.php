<?php
declare(strict_types=1);

use Devinci\ShadowAuth\Core\Config;
use Devinci\ShadowAuth\Providers\FileUserProvider;
use Devinci\ShadowAuth\Utils\CSRF;

define('SHADOW_AUTH_PUBLIC_MODE', true);

if (!function_exists('shadow_auth_public_url')) {
    function shadow_auth_public_url(string $page): string
    {
        return '/index.php?page=' . rawurlencode($page);
    }
}

$page = isset($_GET['page']) && is_string($_GET['page']) ? $_GET['page'] : 'home';
$homeTab = isset($_GET['tab']) && is_string($_GET['tab']) ? $_GET['tab'] : 'login';
if ($page === 'demo') $page = 'home';
if ($page === 'overview') {
    $page = 'home';
    if (!isset($_GET['tab'])) {
        $homeTab = 'overview';
    }
}

$homeTabs = ['overview', 'quick_reference', 'login', 'password_reset', 'register', '2fa_verify', '2fa_setup', 'dashboard', 'source_code'];
if (!in_array($homeTab, $homeTabs, true)) $homeTab = 'login';

$routes = require __DIR__ . '/routes.php';

if (!array_key_exists($page, $routes)) {
    http_response_code(404);
    $page = 'home';
}

ob_start();
if ($page === 'home') {
    require dirname(__DIR__) . '/bootstrap.php';

    $isPublicMode = defined('SHADOW_AUTH_PUBLIC_MODE') && SHADOW_AUTH_PUBLIC_MODE;
    $loginUrl = $isPublicMode ? shadow_auth_public_url('login') : '/views/login.php';
    $registerUrl = $isPublicMode ? shadow_auth_public_url('register') : '/views/register.php';
    $forgotPasswordUrl = $isPublicMode ? shadow_auth_public_url('forgot_password') : '/views/forgot_password.php';
    $resetPasswordUrl = $isPublicMode ? shadow_auth_public_url('reset_password') : '/views/reset_password.php';
    $totpUrl = $isPublicMode ? shadow_auth_public_url('setup_2fa') : '/views/setup_2fa.php';
    $totpSetupUrl = $isPublicMode ? shadow_auth_public_url('setup_2fa') . '&mode=setup' : '/views/setup_2fa.php?mode=setup';
    $totpVerifyUrl = $isPublicMode ? shadow_auth_public_url('setup_2fa') . '&mode=verify' : '/views/setup_2fa.php?mode=verify';
    $dashboardUrl = $isPublicMode ? shadow_auth_public_url('dashboard') : '/views/dashboard.php';
    $overviewUrl = $isPublicMode ? shadow_auth_public_url('overview') : '/views/overview.php';
    $logoutUrl = $isPublicMode ? shadow_auth_public_url('logout') : '/views/logout.php';

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
    $provisioningUri = 'otpauth://totp/' . rawurlencode($accountLabel)
        . '?secret=' . rawurlencode($twoFaSecret)
        . '&issuer=' . rawurlencode($issuer)
        . '&algorithm=SHA1&digits=6&period=30';

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
        if (!is_file($fullPath)) return "<details><summary>{$title} ({$relativePath})</summary><p>File not found.</p></details>";
        $code = file_get_contents($fullPath);
        if ($code === false) return "<details><summary>{$title} ({$relativePath})</summary><p>Could not read file.</p></details>";
        return '<details><summary>' . htmlspecialchars($title . ' (' . $relativePath . ')', ENT_QUOTES, 'UTF-8') . '</summary><pre><code>'
            . htmlspecialchars($code, ENT_QUOTES, 'UTF-8')
            . '</code></pre></details>';
    };

    $registerExtraFields = [
        [
            'name' => 'email',
            'label' => 'Email',
            'type' => 'email',
            'autocomplete' => 'email',
            'required' => true,
        ],
    ];

    $loginFormPreview = (new Devinci\ShadowAuth\View\LoginForm())->render();
    $forgotPasswordFormPreview = (new Devinci\ShadowAuth\View\ForgotPasswordForm())->render();
    $resetPasswordFormPreview = (new Devinci\ShadowAuth\View\ResetPasswordForm())->setToken('demo-token-placeholder')->render();
    $registerFormPreview = (new Devinci\ShadowAuth\View\RegisterForm())->setExtraFields($registerExtraFields)->render();
    $totpFormPreview = (new Devinci\ShadowAuth\View\TotpForm())->render();

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && (($_POST['action'] ?? '') === 'seed_demo_users')) {
        if (!CSRF::validate($_POST['csrf_token'] ?? null)) {
            $error = 'Invalid CSRF token.';
        } else {
            $users = $provider->all();
            $filtered = [];
            foreach ($users as $user) {
                $username = (string) ($user['username'] ?? '');
                if ($username === $standardUser || $username === $twoFaUser) continue;
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
            if (file_put_contents($storagePath, $payload, LOCK_EX) === false) {
                $error = 'Could not seed demo users.';
            } else {
                @chmod($storagePath, 0600);
                $success = 'Demo users seeded successfully.';
            }
        }
    }

    include __DIR__ . '/partials/home_header.php';

    if ($error) echo '<p class="flash flash-error">'.htmlspecialchars($error, ENT_QUOTES).'</p>';
    if ($success) echo '<p class="flash flash-success">'.htmlspecialchars($success, ENT_QUOTES).'</p>';

    if ($homeTab === 'overview') {
        echo '<section class="wiki-card">';
        echo '<h2>Demo Users</h2>';
        echo '<p class="wiki-muted">Seed quick credentials for demo flows.</p>';
        echo "<p><strong>Standard:</strong> {$standardUser} / {$standardPass}</p>";
        echo "<p><strong>2FA User:</strong> {$twoFaUser} / {$twoFaPass}</p>";
        echo '<form method="post">';
        echo CSRF::input();
        echo '<input type="hidden" name="action" value="seed_demo_users">';
        echo '<button type="submit">Create / Reset Demo Users</button>';
        echo '</form>';
        echo '</section>';
    }

    $tabEndpoints = [
        'overview' => __DIR__ . '/endpoints/home_tabs/overview_endpoint.php',
        'quick_reference' => __DIR__ . '/endpoints/home_tabs/quick_reference_endpoint.php',
        'login' => __DIR__ . '/endpoints/home_tabs/login_endpoint.php',
        'password_reset' => __DIR__ . '/endpoints/home_tabs/password_reset_endpoint.php',
        'register' => __DIR__ . '/endpoints/home_tabs/register_endpoint.php',
        '2fa_verify' => __DIR__ . '/endpoints/home_tabs/twofa_verify_endpoint.php',
        '2fa_setup' => __DIR__ . '/endpoints/home_tabs/twofa_setup_endpoint.php',
        'dashboard' => __DIR__ . '/endpoints/home_tabs/dashboard_endpoint.php',
        'source_code' => __DIR__ . '/endpoints/home_tabs/source_code_endpoint.php',
    ];

    if (isset($tabEndpoints[$homeTab]) && is_file($tabEndpoints[$homeTab])) {
        include $tabEndpoints[$homeTab];
    } else {
        echo '<p>Tab endpoint not found.</p>';
    }

    include __DIR__ . '/partials/home_footer.php';

} else {
    $pageTitle = match ($page) {
        'overview' => 'Overview',
        'login' => 'Login',
        'forgot_password' => 'Forgot Password',
        'reset_password' => 'Reset Password',
        'register' => 'Register',
        'setup_2fa' => 'Two-Factor Authentication',
        'dashboard' => 'Dashboard',
        'logout' => 'Logout',
        default => 'Shadow Auth',
    };

    include __DIR__ . '/partials/page_header.php';
    include $routes[$page];
    include __DIR__ . '/partials/page_footer.php';
}

$output = ob_get_clean();
echo $output;