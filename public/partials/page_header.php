<?php

declare(strict_types=1);

$pageActive = static function (string $name) use ($page): string {
    return $page === $name ? ' class="active"' : '';
};
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='%230969da' d='M8 0l6 2v4c0 4.4-2.9 8.3-6 10-3.1-1.7-6-5.6-6-10V2l6-2z'/%3E%3Cpath fill='white' d='M8 4a2 2 0 0 1 2 2v1h.5a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5h-5a.5.5 0 0 1-.5-.5v-3a.5.5 0 0 1 .5-.5H6V6a2 2 0 0 1 2-2zm-1 3h2V6a1 1 0 0 0-2 0v1z'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="https://unpkg.com/@primer/css@21.5.1/dist/primer.css">
    <link rel="stylesheet" href="https://unpkg.com/@primer/octicons@18.3.0/build/build.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/assets/css/reset.css">
    <link rel="stylesheet" href="/assets/css/typography.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Hubot+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap">
    <script src="/assets/js/index.js" defer></script>
</head>
<body>
<header class="wiki-header">
    <h1 class="wiki-title"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> <?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
    <p class="wiki-subtitle">Shadow Auth runtime pages</p>
</header>

<nav class="shadow-tabs" aria-label="Page navigation">
    <a href="<?= htmlspecialchars(shadow_auth_public_url('home'), ENT_QUOTES, 'UTF-8') ?>"><i class="fa-solid fa-house" aria-hidden="true"></i> Home</a>
    <a href="<?= htmlspecialchars(shadow_auth_public_url('overview'), ENT_QUOTES, 'UTF-8') ?>"<?= $pageActive('overview') ?>><i class="fa-solid fa-compass" aria-hidden="true"></i> Overview</a>
    <a href="<?= htmlspecialchars(shadow_auth_public_url('login'), ENT_QUOTES, 'UTF-8') ?>"<?= $pageActive('login') ?>><i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i> Login</a>
    <a href="<?= htmlspecialchars(shadow_auth_public_url('register'), ENT_QUOTES, 'UTF-8') ?>"<?= $pageActive('register') ?>><i class="fa-solid fa-user-plus" aria-hidden="true"></i> Register</a>
    <a href="<?= htmlspecialchars(shadow_auth_public_url('setup_2fa') . '&mode=verify', ENT_QUOTES, 'UTF-8') ?>"<?= $pageActive('setup_2fa') ?>><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> 2FA</a>
    <a href="<?= htmlspecialchars(shadow_auth_public_url('dashboard'), ENT_QUOTES, 'UTF-8') ?>"<?= $pageActive('dashboard') ?>><i class="fa-solid fa-gauge-high" aria-hidden="true"></i> Dashboard</a>
    <a href="<?= htmlspecialchars(shadow_auth_public_url('logout'), ENT_QUOTES, 'UTF-8') ?>"><i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i> Logout</a>
    <button id="theme-toggle" type="button" aria-label="Switch to dark theme" title="Switch to dark theme">
        <i class="fa-solid fa-moon" aria-hidden="true"></i>
        <span class="sr-only">Toggle theme</span>
    </button>
</nav>

<main class="shadow-shell">
