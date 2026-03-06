<?php

declare(strict_types=1);

$active = static function (string $tab) use ($homeTab): string {
    return $homeTab === $tab ? ' class="active"' : '';
};

$tabIcon = static function (string $tab): string {
    return match ($tab) {
        'overview' => 'fa-solid fa-compass',
        'quick_reference' => 'fa-solid fa-book',
        'login' => 'fa-solid fa-right-to-bracket',
        'register' => 'fa-solid fa-user-plus',
        '2fa_verify' => 'fa-solid fa-shield-halved',
        '2fa_setup' => 'fa-solid fa-key',
        'dashboard' => 'fa-solid fa-gauge-high',
        'source_code' => 'fa-solid fa-code',
        default => 'fa-solid fa-circle',
    };
};
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Shadow Auth Home</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='%230969da' d='M8 0l6 2v4c0 4.4-2.9 8.3-6 10-3.1-1.7-6-5.6-6-10V2l6-2z'/%3E%3Cpath fill='white' d='M8 4a2 2 0 0 1 2 2v1h.5a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5h-5a.5.5 0 0 1-.5-.5v-3a.5.5 0 0 1 .5-.5H6V6a2 2 0 0 1 2-2zm-1 3h2V6a1 1 0 0 0-2 0v1z'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="https://unpkg.com/@primer/css@21.5.1/dist/primer.css">
    <link rel="stylesheet" href="https://unpkg.com/@primer/octicons@18.3.0/build/build.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/assets/css/reset.css">
    <link rel="stylesheet" href="/assets/css/typography.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <!-- Link HubotSans -->

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Hubot+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap">
    <script src="/assets/js/index.js" defer></script>
</head>
<body>
<header class="wiki-header">
    <h1 class="wiki-title"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Shadow Auth Wiki Demo</h1>
    <p class="wiki-subtitle">Reference + runnable examples for login, registration, TOTP setup/verify, and protected routes.</p>
</header>

<nav class="shadow-tabs" aria-label="Home tabs">
    <?php foreach ($homeTabs as $tab): ?>
        <?php
            $label = match ($tab) {
                'overview' => 'Overview',
                'quick_reference' => 'Quick Reference',
                '2fa_verify' => '2FA Verify',
                '2fa_setup' => '2FA Setup',
                'dashboard' => 'Dashboard',
                'source_code' => 'Source Code',
                default => ucfirst($tab),
            };
        ?>
        <a href="<?= htmlspecialchars(shadow_auth_public_url('home') . '&tab=' . $tab, ENT_QUOTES, 'UTF-8') ?>"<?= $active($tab) ?>><i class="<?= htmlspecialchars($tabIcon($tab), ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true"></i> <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a>
    <?php endforeach; ?>
    <a href="<?= htmlspecialchars($logoutUrl, ENT_QUOTES, 'UTF-8') ?>"><i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i> Logout</a>
    <button id="theme-toggle" type="button" aria-label="Switch to dark theme" title="Switch to dark theme">
        <i class="fa-solid fa-moon" aria-hidden="true"></i>
        <span class="sr-only">Toggle theme</span>
    </button>
</nav>

<main class="shadow-shell">
    <p class="wiki-jump"><a href="<?= htmlspecialchars(shadow_auth_public_url('home') . '&tab=overview', ENT_QUOTES, 'UTF-8') ?>">Open Overview Diagnostics Tab</a></p>
