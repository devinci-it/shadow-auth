<?php

declare(strict_types=1);
?>
<article class="tab-page">
<header class="tab-header">
    <h2 id="login-overview"><i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i> Login Demo</h2>
    <p><a href="<?= htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') ?>">Open Login Page</a></p>
    <p>Use <strong><?= htmlspecialchars($standardUser, ENT_QUOTES, 'UTF-8') ?></strong> / <strong><?= htmlspecialchars($standardPass, ENT_QUOTES, 'UTF-8') ?></strong> for no-2FA flow.</p>
</header>

<nav class="tab-outline" aria-label="Login tab sections">
    <strong>On this tab</strong>
    <a href="#login-notes">Wiki Notes</a>
    <a href="#login-form">Form Preview</a>
    <a href="#login-fields">Required Fields</a>
    <a href="#login-source">Source Code</a>
</nav>

<section id="login-notes" class="wiki-section">
<h3>Wiki Notes</h3>
<ul>
    <li>Purpose: authenticate username/password and branch to dashboard or 2FA verify.</li>
    <li>Flow: submit form → LoginProcessor::handle() → Auth::beginLogin().</li>
    <li>Result states: authenticated, totp_required, failed.</li>
</ul>
</section>

<section id="login-form" class="wiki-section">
<h3>Form Preview</h3>
<form method="post" action="<?= htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') ?>">
    <?= $loginFormPreview ?>
    <button type="submit">Login</button>
</form>
<details>
    <summary>View implementation snippet</summary>
    <?php
    $loginSnippet = <<<'PHP'
<?php
$form = new LoginForm();
echo $form->render();
PHP;
    ?>
    <pre><code><?= htmlspecialchars($loginSnippet, ENT_QUOTES, 'UTF-8') ?></code></pre>
</details>
</section>

<section id="login-fields" class="wiki-section">
<h3>Required Fields</h3>
<p><strong>Required fields:</strong> username, password, csrf_token</p>
<ul>
    <li>Form class: <code>Devinci\ShadowAuth\View\LoginForm</code></li>
    <li>POST processor: <code>Devinci\ShadowAuth\Processors\LoginProcessor</code></li>
    <li>Auth logic: <code>Devinci\ShadowAuth\Facade\Auth::beginLogin()</code></li>
</ul>
</section>

<section id="login-source" class="wiki-section">
<h3>Source Code</h3>
<p><strong>Explanation:</strong> The page renders <code>LoginForm</code>, submits to <code>LoginProcessor</code>, and delegates final auth state handling to <code>Auth::beginLogin()</code>.</p>
<p><strong>Caveats:</strong> ensure <code>csrf_token</code> is present, preserve redirect URLs for dashboard/2FA verify, and avoid exposing detailed auth failure internals in production messages.</p>
<p><a href="<?= htmlspecialchars(shadow_auth_public_url('home') . '&tab=quick_reference', ENT_QUOTES, 'UTF-8') ?>">See Quick Reference Flow Guide</a></p>
<?= $renderSourcePanel('Page', 'views/login.php') ?>
<?= $renderSourcePanel('Form class', 'src/View/LoginForm.php') ?>
<?= $renderSourcePanel('Processor class', 'src/Processors/LoginProcessor.php') ?>
</section>
</article>
