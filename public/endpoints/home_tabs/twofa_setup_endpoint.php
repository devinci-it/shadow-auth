<?php

declare(strict_types=1);
//CSRF import for token generation and validation in the form
use Devinci\ShadowAuth\Utils\CSRF;

?>
<article class="tab-page">
<header class="tab-header">
    <h2 id="setup-overview"><i class="fa-solid fa-key" aria-hidden="true"></i> 2FA Setup Demo</h2>
    <p class="tab-actions">
        <a class="btn-outline-secondary" href="<?= htmlspecialchars($totpSetupUrl, ENT_QUOTES, 'UTF-8') ?>">Open 2FA Setup Page</a>
        <a class="btn-outline-secondary" href="<?= htmlspecialchars($totpSetupUrl . '&autologin=1', ENT_QUOTES, 'UTF-8') ?>">Auto-Login Demo User and Open Setup</a>
    </p>
    <p>Setup flow generates a TOTP secret, shows QR/provisioning details, then confirms with a 6-digit code.</p>
</header>

<nav class="tab-outline" aria-label="2FA setup tab sections">
    <strong>On this tab</strong>
    <a href="#setup-notes">Wiki Notes</a>
    <a href="#setup-form">Form Preview</a>
    <a href="#setup-uri">Provisioning URI</a>
    <a href="#setup-fields">Required Fields</a>
    <a href="#setup-source">Source Code</a>
</nav>

<section id="setup-notes" class="wiki-section">
<h3>Wiki Notes</h3>
<ul>
    <li>Purpose: enroll a logged-in user into TOTP 2FA.</li>
    <li>Flow: generate secret → scan QR in authenticator app → confirm code.</li>
    <li>Methods: Auth::setupTotpSecret() then Auth::confirmTotp().</li>
    <li>Shortcut: use auto-login link to sign in as <code>demo</code>; flash confirms auto-login success.</li>
</ul>
</section>

<section id="setup-form" class="wiki-section">
<h3>Form Preview</h3>
<form method="post" action="<?= htmlspecialchars($totpSetupUrl, ENT_QUOTES, 'UTF-8') ?>">
    <?= CSRF::input() ?>
    <input type="hidden" name="action" value="generate">
    <button type="submit">Generate TOTP Secret</button>
</form>

<form method="post" action="<?= htmlspecialchars($totpSetupUrl, ENT_QUOTES, 'UTF-8') ?>">
    <?= CSRF::input() ?>
    <input type="hidden" name="action" value="confirm">
    <label for="preview_totp_code">Enter current 6-digit code</label>
    <input id="preview_totp_code" name="totp_code" type="text" inputmode="numeric" pattern="[0-9]{6}" autocomplete="one-time-code" required>
    <button type="submit">Confirm and Enable 2FA</button>
</form>
<details>
    <summary>View implementation snippet</summary>
    <?php
    $setupSnippet = <<<'PHP'
<?php
// generate
Auth::setupTotpSecret($username);

// confirm
Auth::confirmTotp($username, $code);
PHP;
    ?>
    <pre><code><?= htmlspecialchars($setupSnippet, ENT_QUOTES, 'UTF-8') ?></code></pre>
</details>
</section>

<section id="setup-uri" class="wiki-section">
<p>Demo provisioning URI example:</p>
<p><code><?= htmlspecialchars($provisioningUri, ENT_QUOTES, 'UTF-8') ?></code></p>
</section>

<section id="setup-fields" class="wiki-section">
<h3>Required Fields</h3>
<p><strong>Generate step:</strong> csrf_token, action=generate</p>
<p><strong>Confirm step:</strong> totp_code, csrf_token, action=confirm</p>
<ul>
    <li>Setup methods: <code>Devinci\ShadowAuth\Facade\Auth::setupTotpSecret()</code> and <code>Devinci\ShadowAuth\Facade\Auth::confirmTotp()</code></li>
    <li>Backend methods: <code>Devinci\ShadowAuth\Core\AuthManager::setupTotpSecret()</code> and <code>enableTotpForUser()</code></li>
</ul>
</section>

<section id="setup-source" class="wiki-section">
<h3>Source Code</h3>
<p><strong>Explanation:</strong> Setup mode creates or rotates secret via <code>Auth::setupTotpSecret()</code>, then enables TOTP with <code>Auth::confirmTotp()</code> after a valid code.</p>
<p><strong>Caveats:</strong> generate/confirm actions require CSRF tokens and an authenticated session; secret handling should stay server-side.</p>
<p><a href="<?= htmlspecialchars(shadow_auth_public_url('home') . '&tab=quick_reference', ENT_QUOTES, 'UTF-8') ?>">See Quick Reference Flow Guide</a></p>
<?= $renderSourcePanel('Page (setup + verify modes)', 'views/setup_2fa.php') ?>
<?= $renderSourcePanel('Facade auth methods', 'src/Facade/Auth.php') ?>
<?= $renderSourcePanel('Auth manager methods', 'src/Core/AuthManager.php') ?>
</section>
</article>
