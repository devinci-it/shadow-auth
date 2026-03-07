<?php

declare(strict_types=1);
?>
<article class="tab-page">
<header class="tab-header">
    <h2 id="source-overview"><i class="fa-solid fa-code" aria-hidden="true"></i> Source Code Reference</h2>
    <p class="wiki-muted">High-level summary of key files, classes, and method intent.</p>
</header>

<div class="source-layout">
    <aside class="source-side" aria-label="Source code side navigation">
        <h3>Files</h3>
        <a href="#source-bootstrap">Bootstrap</a>
        <a href="#source-facade">Facade/Auth</a>
        <a href="#source-manager">Core/AuthManager</a>
        <a href="#source-password-reset">Core/PasswordResetManager</a>
        <a href="#source-processors">Processors</a>
        <a href="#source-views">View Forms</a>
    </aside>

    <div class="source-main">
        <section id="source-bootstrap" class="wiki-section">
            <h3>bootstrap.php</h3>
            <p><strong>Intent:</strong> initialize autoloading, defaults, session, then call <code>Auth::boot()</code>.</p>
            <p><strong>Main actions:</strong> set <code>storage_path</code>, <code>totp_enabled</code>, <code>session_key</code>, start session, boot facade.</p>
            <?= $renderSourcePanel('Bootstrap', 'bootstrap.php') ?>
        </section>

        <section id="source-facade" class="wiki-section">
            <h3>src/Facade/Auth.php</h3>
            <p><strong>Intent:</strong> single static API boundary for app pages/processors.</p>
            <p><strong>Key methods:</strong> <code>beginLogin()</code>, <code>verifyPendingTotp()</code>, <code>setupTotpSecret()</code>, <code>confirmTotp()</code>, <code>requireAuth()</code>.</p>
            <?= $renderSourcePanel('Facade/Auth', 'src/Facade/Auth.php') ?>
        </section>

        <section id="source-manager" class="wiki-section">
            <h3>src/Core/AuthManager.php</h3>
            <p><strong>Intent:</strong> implement core auth state transitions and session ownership.</p>
            <p><strong>Key methods:</strong> <code>beginLogin()</code>, <code>verifyPendingTotp()</code>, <code>check()</code>, <code>logout()</code>, <code>setupTotpSecret()</code>, <code>enableTotpForUser()</code>.</p>
            <?= $renderSourcePanel('Core/AuthManager', 'src/Core/AuthManager.php') ?>
        </section>

        <section id="source-password-reset" class="wiki-section">
            <h3>src/Core/PasswordResetManager.php</h3>
            <p><strong>Intent:</strong> manage reset token creation, validation, expiry, and password hash update.</p>
            <p><strong>Key methods:</strong> <code>requestResetToken()</code>, <code>hasValidToken()</code>, <code>resetPasswordWithToken()</code>.</p>
            <?= $renderSourcePanel('Core/PasswordResetManager', 'src/Core/PasswordResetManager.php') ?>
        </section>

        <section id="source-processors" class="wiki-section">
            <h3>src/Processors/*</h3>
            <p><strong>Intent:</strong> request handling/validation layer for form submissions.</p>
            <ul>
                <li><code>LoginProcessor</code>: username/password -> auth branch.</li>
                <li><code>RegisterProcessor</code>: validate + persist new user.</li>
                <li><code>TOTPProcessor</code>: pending-login TOTP verification.</li>
                <li><code>ForgotPasswordProcessor</code>: request and publish reset URL for demo flow.</li>
                <li><code>ResetPasswordProcessor</code>: token + password confirmation reset handling.</li>
            </ul>
            <?= $renderSourcePanel('LoginProcessor', 'src/Processors/LoginProcessor.php') ?>
            <?= $renderSourcePanel('RegisterProcessor', 'src/Processors/RegisterProcessor.php') ?>
            <?= $renderSourcePanel('TOTPProcessor', 'src/Processors/TOTPProcessor.php') ?>
            <?= $renderSourcePanel('ForgotPasswordProcessor', 'src/Processors/ForgotPasswordProcessor.php') ?>
            <?= $renderSourcePanel('ResetPasswordProcessor', 'src/Processors/ResetPasswordProcessor.php') ?>
        </section>

        <section id="source-views" class="wiki-section">
            <h3>src/View/*Form.php</h3>
            <p><strong>Intent:</strong> form field rendering abstraction for login/register/totp views.</p>
            <p><strong>Use:</strong> instantiate form class, render fields, wrap in page form with submit button.</p>
            <?= $renderSourcePanel('LoginForm', 'src/View/LoginForm.php') ?>
            <?= $renderSourcePanel('RegisterForm', 'src/View/RegisterForm.php') ?>
            <?= $renderSourcePanel('TotpForm', 'src/View/TotpForm.php') ?>
            <?= $renderSourcePanel('ForgotPasswordForm', 'src/View/ForgotPasswordForm.php') ?>
            <?= $renderSourcePanel('ResetPasswordForm', 'src/View/ResetPasswordForm.php') ?>
        </section>
    </div>
</div>
</article>
