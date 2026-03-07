<?php

declare(strict_types=1);
?>
<article class="tab-page">
<header class="tab-header">
    <h2 id="verify-overview"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> 2FA Verify Demo</h2>
    <p class="tab-actions">
        <a class="btn-outline-secondary" href="<?= htmlspecialchars($totpVerifyUrl, ENT_QUOTES, 'UTF-8') ?>">Open 2FA Verify Page</a>
        <a class="btn-outline-secondary" href="<?= htmlspecialchars($totpVerifyUrl . '&autologin=1', ENT_QUOTES, 'UTF-8') ?>">Auto-Login 2FA Demo User and Open Verification</a>
    </p>
    <p>Use <strong><?= htmlspecialchars($twoFaUser, ENT_QUOTES, 'UTF-8') ?></strong> / <strong><?= htmlspecialchars($twoFaPass, ENT_QUOTES, 'UTF-8') ?></strong> to trigger verification.</p>
    <p>Demo secret for authenticator app: <strong><?= htmlspecialchars($twoFaSecret, ENT_QUOTES, 'UTF-8') ?></strong></p>
</header>

<nav class="tab-outline" aria-label="2FA verify tab sections">
    <strong>On this tab</strong>
    <a href="#verify-notes">Wiki Notes</a>
    <a href="#verify-form">Form Preview</a>
    <a href="#verify-fields">Required Fields</a>
    <a href="#verify-source">Source Code</a>
</nav>

<section id="verify-notes" class="wiki-section">
<h3>Wiki Notes</h3>
<ul>
    <li>Purpose: verify pending login with a 6-digit TOTP code.</li>
    <li>Flow: submit code → TOTPProcessor::handle() → Auth::verifyPendingTotp().</li>
    <li>Guard: verify page is valid only when Auth::isTotpPending() is true.</li>
    <li>Shortcut: auto-login link starts pending login as <code>demo2fa</code>; flash prompts 2FA entry.</li>
</ul>
</section>

<section id="verify-form" class="wiki-section">
<h3>Form Preview</h3>
<form method="post" action="<?= htmlspecialchars($totpVerifyUrl, ENT_QUOTES, 'UTF-8') ?>">
    <?= $totpFormPreview ?>
    <button type="submit">Verify</button>
</form>
<details>
    <summary>View implementation snippet</summary>
    <?php
    $verifySnippet = <<<'PHP'
<?php
$form = new TotpForm();
echo $form->render();
PHP;
    ?>
    <pre><code><?= htmlspecialchars($verifySnippet, ENT_QUOTES, 'UTF-8') ?></code></pre>
</details>
</section>

<section id="verify-fields" class="wiki-section">
<h3>Required Fields</h3>
<p><strong>Required fields:</strong> totp_code, csrf_token</p>
<ul>
    <li>Form class: <code>DevinciIT\ShadowAuth\View\TotpForm</code></li>
    <li>POST processor: <code>DevinciIT\ShadowAuth\Processors\TOTPProcessor</code></li>
    <li>Verification: <code>DevinciIT\ShadowAuth\Facade\Auth::verifyPendingTotp()</code></li>
</ul>
</section>

<section id="verify-source" class="wiki-section">
<h3>Source Code</h3>
<p><strong>Explanation:</strong> Verify mode uses <code>TotpForm</code> and <code>TOTPProcessor</code> to validate pending-login code through <code>Auth::verifyPendingTotp()</code>.</p>
<p><strong>Caveats:</strong> this flow should only be reachable when <code>Auth::isTotpPending()</code> is true; otherwise redirect to login.</p>
<p><a href="<?= htmlspecialchars(shadow_auth_public_url('home') . '&tab=quick_reference', ENT_QUOTES, 'UTF-8') ?>">See Quick Reference Flow Guide</a></p>
<?= $renderSourcePanel('Page', 'views/setup_2fa.php') ?>
<?= $renderSourcePanel('Form class', 'src/View/TotpForm.php') ?>
<?= $renderSourcePanel('Processor class', 'src/Processors/TOTPProcessor.php') ?>
</section>
</article>
