<?php

declare(strict_types=1);
?>
<article class="tab-page">
<header class="tab-header">
    <h2 id="quickref-overview"><i class="fa-solid fa-book" aria-hidden="true"></i> Quick Reference: How-To & Flow Guide</h2>
    <p class="wiki-muted">Use this as the short operational playbook for setup, login, 2FA, and protected pages.</p>
</header>

<nav class="tab-outline" aria-label="Quick reference sections">
    <strong>On this tab</strong>
    <a href="#quickref-seed">Seed Demo Users</a>
    <a href="#quickref-login">Login Flows</a>
    <a href="#quickref-2fa">2FA Setup/Verify</a>
    <a href="#quickref-protected">Protected Pages</a>
    <a href="#quickref-caveats">Caveats</a>
</nav>

<section id="quickref-seed" class="wiki-section">
    <h3>1) Seed Demo Users</h3>
    <ol>
        <li>Open Home and click <strong>Create / Reset Demo Users</strong>.</li>
        <li>Use credentials: <strong>demo / demo-password</strong> and <strong>demo2fa / demo-password</strong>.</li>
    </ol>
</section>

<section id="quickref-login" class="wiki-section">
    <h3>2) Login Flows</h3>
    <ul>
        <li><strong>No 2FA user:</strong> <code>login.php</code> → dashboard directly.</li>
        <li><strong>2FA user:</strong> <code>login.php</code> → pending TOTP → <code>setup_2fa.php?mode=verify</code>.</li>
    </ul>
</section>

<section id="quickref-2fa" class="wiki-section">
    <h3>3) 2FA Setup & Verify</h3>
    <ul>
        <li><strong>Setup:</strong> go to <code>setup_2fa.php?mode=setup</code>, generate secret, scan QR, confirm 6-digit code.</li>
        <li><strong>Verify:</strong> go to <code>setup_2fa.php?mode=verify</code> only when login has pending TOTP.</li>
    </ul>
</section>

<section id="quickref-protected" class="wiki-section">
    <h3>4) Protected Page Pattern</h3>
    <pre><code><?= htmlspecialchars("Auth::requireAuth('" . $loginUrl . "');\n// protected page content", ENT_QUOTES, 'UTF-8') ?></code></pre>
    <p>Current dashboard already uses this pattern.</p>
</section>

<section id="quickref-caveats" class="wiki-section">
    <h3>Caveats</h3>
    <ul>
        <li>Always include CSRF token in state-changing forms.</li>
        <li>2FA verify endpoint is only valid during pending TOTP login session.</li>
        <li>Keep <code>storage/shadow.php</code> permissions restricted (<code>0600</code> recommended).</li>
        <li>Use HTTPS in production to protect session and credentials.</li>
    </ul>
</section>
</article>
