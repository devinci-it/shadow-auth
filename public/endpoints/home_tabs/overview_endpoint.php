<?php

declare(strict_types=1);
?>
<article class="tab-page">
<header class="tab-header">
    <h2 id="overview-main"><i class="fa-solid fa-compass" aria-hidden="true"></i> Overview & Diagnostics</h2>
    <p class="wiki-muted">Current storage status, seeded users, and important project pointers for quick checks.</p>
</header>

<nav class="tab-outline" aria-label="Overview sections">
    <strong>On this tab</strong>
    <a href="#overview-storage">Storage Status</a>
    <a href="#overview-users">Current Users</a>
    <a href="#overview-raw">Raw Storage</a>
    <a href="#overview-features">Feature Summary</a>
</nav>

<section id="overview-storage" class="wiki-section">
    <h3>Storage Status</h3>
    <ul>
        <li>Path: <code><?= htmlspecialchars($storagePath, ENT_QUOTES, 'UTF-8') ?></code></li>
        <li>Exists: <strong><?= $storageExists ? 'Yes' : 'No' ?></strong></li>
        <li>Permissions: <strong><?= htmlspecialchars($storagePerms, ENT_QUOTES, 'UTF-8') ?></strong></li>
        <li>Readable: <strong><?= $storageReadable ? 'Yes' : 'No' ?></strong></li>
        <li>Writable: <strong><?= $storageWritable ? 'Yes' : 'No' ?></strong></li>
    </ul>
</section>

<section id="overview-users" class="wiki-section">
    <h3>Current Users (Parsed)</h3>
    <?php if ($currentUsers === []): ?>
        <p>No users found.</p>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>Username</th>
                <th>2FA Enabled</th>
                <th>Has Secret</th>
                <th>Created At</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($currentUsers as $user): ?>
                <tr>
                    <td><?= htmlspecialchars((string) ($user['username'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= !empty($user['totp_enabled']) ? 'Yes' : 'No' ?></td>
                    <td><?= !empty($user['totp_secret']) ? 'Yes' : 'No' ?></td>
                    <td><?= htmlspecialchars((string) ($user['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<section id="overview-raw" class="wiki-section">
    <h3>Raw Storage File</h3>
    <pre><code><?= htmlspecialchars($storageRaw, ENT_QUOTES, 'UTF-8') ?></code></pre>
</section>

<section id="overview-features" class="wiki-section">
    <h3>Feature Summary</h3>
    <ul>
        <li>File-based auth with secure password hashing.</li>
        <li>Register, login, and logout with processors + facade pattern.</li>
        <li>TOTP setup + verify split flow.</li>
        <li>CSRF protection on state-changing actions.</li>
    </ul>
</section>
</article>
