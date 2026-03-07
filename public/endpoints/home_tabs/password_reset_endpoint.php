<?php

declare(strict_types=1);
?>
<article class="tab-page">
<header class="tab-header">
    <h2 id="password-reset-overview"><i class="fa-solid fa-key" aria-hidden="true"></i> Password Reset Demo</h2>
    <p><a href="<?= htmlspecialchars($forgotPasswordUrl, ENT_QUOTES, 'UTF-8') ?>">Open Forgot Password Page</a></p>
    <p><a href="<?= htmlspecialchars($resetPasswordUrl, ENT_QUOTES, 'UTF-8') ?>">Open Reset Password Page</a></p>
</header>

<nav class="tab-outline" aria-label="Password reset tab sections">
    <strong>On this tab</strong>
    <a href="#password-reset-notes">Wiki Notes</a>
    <a href="#password-reset-form">Form Preview</a>
    <a href="#password-reset-fields">Required Fields</a>
    <a href="#password-reset-source">Source Code</a>
</nav>

<section id="password-reset-notes" class="wiki-section">
<h3>Wiki Notes</h3>
<ul>
    <li>Purpose: issue a short-lived password reset token and update a user password with that token.</li>
    <li>Flow: forgot form -> ForgotPasswordProcessor -> Auth::requestPasswordResetToken().</li>
    <li>Flow: reset form -> ResetPasswordProcessor -> Auth::resetPasswordWithToken().</li>
    <li>Single responsibility: token lifecycle is handled by PasswordResetManager only.</li>
</ul>
</section>

<section id="password-reset-form" class="wiki-section">
<h3>Form Preview</h3>
<h4>Forgot Password</h4>
<form method="post" action="<?= htmlspecialchars($forgotPasswordUrl, ENT_QUOTES, 'UTF-8') ?>">
    <?= $forgotPasswordFormPreview ?>
    <button type="submit">Generate Reset Link</button>
</form>

<h4>Reset Password</h4>
<form method="post" action="<?= htmlspecialchars($resetPasswordUrl . '&token=demo-token-placeholder', ENT_QUOTES, 'UTF-8') ?>">
    <?= $resetPasswordFormPreview ?>
    <button type="submit">Reset Password</button>
</form>
</section>

<section id="password-reset-fields" class="wiki-section">
<h3>Required Fields</h3>
<p><strong>Forgot page:</strong> login_identifier, csrf_token</p>
<p><strong>Reset page:</strong> reset_token, password, confirm_password, csrf_token</p>
<ul>
    <li>Form classes: <code>ForgotPasswordForm</code>, <code>ResetPasswordForm</code></li>
    <li>Processors: <code>ForgotPasswordProcessor</code>, <code>ResetPasswordProcessor</code></li>
    <li>Core manager: <code>PasswordResetManager</code> via <code>Auth</code> facade</li>
</ul>
</section>

<section id="password-reset-source" class="wiki-section">
<h3>Source Code</h3>
<p><strong>Explanation:</strong> the forgot-password endpoint generates a token, and reset-password validates token + password confirmation before updating the hash.</p>
<p><strong>Caveats:</strong> tokens are time-limited and cleared after successful reset.</p>
<?= $renderSourcePanel('Forgot Password Page', 'views/forgot_password.php') ?>
<?= $renderSourcePanel('Reset Password Page', 'views/reset_password.php') ?>
<?= $renderSourcePanel('PasswordResetManager', 'src/Core/PasswordResetManager.php') ?>
<?= $renderSourcePanel('ForgotPasswordProcessor', 'src/Processors/ForgotPasswordProcessor.php') ?>
<?= $renderSourcePanel('ResetPasswordProcessor', 'src/Processors/ResetPasswordProcessor.php') ?>
</section>
</article>
