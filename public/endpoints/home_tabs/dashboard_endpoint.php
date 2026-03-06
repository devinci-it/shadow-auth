<?php

declare(strict_types=1);
?>
<article class="tab-page">
<header class="tab-header">
    <h2 id="dashboard-overview"><i class="fa-solid fa-gauge-high" aria-hidden="true"></i> Dashboard Auth Demo</h2>
    <p><a href="<?= htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8') ?>">Open Dashboard Page</a></p>
    <p>If user is not logged in, dashboard redirects to login page. If logged in, dashboard content is shown.</p>
</header>

<nav class="tab-outline" aria-label="Dashboard tab sections">
    <strong>On this tab</strong>
    <a href="#dashboard-notes">Wiki Notes</a>
    <a href="#dashboard-pattern">Protected Pattern</a>
    <a href="#dashboard-current">Current Enforcement</a>
    <a href="#dashboard-source">Source Code</a>
</nav>

<section id="dashboard-notes" class="wiki-section">
<h3>Wiki Notes</h3>
<ul>
    <li>Purpose: protected page example that requires an authenticated session.</li>
    <li>Flow: request dashboard → Auth::requireAuth($loginUrl) checks session.</li>
    <li>If unauthorized: immediate redirect to login; if authorized: render content.</li>
</ul>
</section>

<section id="dashboard-pattern" class="wiki-section">
<h3>Protected Page Pattern</h3>
<pre><code><?= htmlspecialchars("Auth::requireAuth('" . $loginUrl . "');\n// protected content below", ENT_QUOTES, 'UTF-8') ?></code></pre>
</section>

<section id="dashboard-current" class="wiki-section">
<h3>How Dashboard Currently Enforces Auth</h3>
<p><strong>Current behavior:</strong> uses <code>Auth::requireAuth($loginUrl)</code> to redirect unauthenticated users to login.</p>
</section>

<section id="dashboard-source" class="wiki-section">
<h3>Source Code</h3>
<p><strong>Explanation:</strong> Dashboard is a protected endpoint that calls <code>Auth::requireAuth($loginUrl)</code> before rendering any user data.</p>
<p><strong>Caveats:</strong> call auth guard before output, and keep redirect targets consistent with public-mode routing.</p>
<p><a href="<?= htmlspecialchars(shadow_auth_public_url('home') . '&tab=quick_reference', ENT_QUOTES, 'UTF-8') ?>">See Quick Reference flow guide</a></p>
<?= $renderSourcePanel('Dashboard page', 'views/dashboard.php') ?>
<?= $renderSourcePanel('Facade requireAuth/check', 'src/Facade/Auth.php') ?>
<?= $renderSourcePanel('Session auth checks', 'src/Core/AuthManager.php') ?>
</section>
</article>
