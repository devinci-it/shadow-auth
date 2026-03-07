<?php

declare(strict_types=1);
?>
<article class="tab-page">
<header class="tab-header">
    <h2 id="register-overview"><i class="fa-solid fa-user-plus" aria-hidden="true"></i> Register Demo</h2>
    <p><a href="<?= htmlspecialchars($registerUrl, ENT_QUOTES, 'UTF-8') ?>">Open Register Page</a></p>
    <p>Register uses username + password and supports extra fields like email.</p>
</header>

<nav class="tab-outline" aria-label="Register tab sections">
    <strong>On this tab</strong>
    <a href="#register-notes">Wiki Notes</a>
    <a href="#register-form">Form Preview</a>
    <a href="#register-fields">Required Fields</a>
    <a href="#register-source">Source Code</a>
</nav>

<section id="register-notes" class="wiki-section">
<h3>Wiki Notes</h3>
<ul>
    <li>Purpose: create a new account record in file storage.</li>
    <li>Flow: submit form → RegisterProcessor::handle() → Auth::registerWithData().</li>
    <li>Validation: password confirmation + CSRF + optional extra field mapping.</li>
</ul>
</section>

<section id="register-form" class="wiki-section">
<h3>Form Preview</h3>
<form method="post" action="<?= htmlspecialchars($registerUrl, ENT_QUOTES, 'UTF-8') ?>">
    <?= $registerFormPreview ?>
    <button type="submit">Register</button>
</form>
<details>
    <summary>View implementation snippet</summary>
    <?php
    $registerSnippet = <<<'PHP'
<?php
$form = (new RegisterForm())->setExtraFields($extraFields);
echo $form->render();
PHP;
    ?>
    <pre><code><?= htmlspecialchars($registerSnippet, ENT_QUOTES, 'UTF-8') ?></code></pre>
</details>
</section>

<section id="register-fields" class="wiki-section">
<h3>Required Fields</h3>
<p><strong>Required fields:</strong> username, password, confirm_password, csrf_token</p>
<p><strong>Optional extra fields:</strong> via <code>setExtraFields()</code></p>
<ul>
    <li>Form class: <code>DevinciIT\ShadowAuth\View\RegisterForm</code></li>
    <li>POST processor: <code>DevinciIT\ShadowAuth\Processors\RegisterProcessor</code></li>
    <li>Persistence: <code>DevinciIT\ShadowAuth\Facade\Auth::registerWithData()</code></li>
</ul>
</section>

<section id="register-source" class="wiki-section">
<h3>Source Code</h3>
<p><strong>Explanation:</strong> Registration combines <code>RegisterForm</code> input rendering with <code>RegisterProcessor</code> validation and <code>Auth::registerWithData()</code> persistence.</p>
<p><strong>Caveats:</strong> keep password confirmation and CSRF validation mandatory, and validate extra fields consistently before storage.</p>
<p><a href="<?= htmlspecialchars(shadow_auth_public_url('home') . '&tab=quick_reference', ENT_QUOTES, 'UTF-8') ?>">See Quick Reference flow guide</a></p>
<?= $renderSourcePanel('Page', 'views/register.php') ?>
<?= $renderSourcePanel('Form class', 'src/View/RegisterForm.php') ?>
<?= $renderSourcePanel('Processor class', 'src/Processors/RegisterProcessor.php') ?>
</section>
</article>
