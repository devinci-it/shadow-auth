<?php

declare(strict_types=1);

namespace DevinciIT\ShadowAuth\Processors;

use DevinciIT\ShadowAuth\Core\Flash;
use DevinciIT\ShadowAuth\Facade\Auth;
use DevinciIT\ShadowAuth\Utils\CSRF;

/**
 * Handles login submission and redirects by authentication outcome.
 */
final class LoginProcessor extends BaseProcessor
{
    public function __construct(
        private readonly string $successRedirect = '/dashboard.php',
        private readonly string $totpRedirect = '/views/setup_2fa.php'
    ) {
    }

    public function handle(): void
    {
        if (!$this->isPost()) {
            return;
        }

        if (!CSRF::validate($_POST['csrf_token'] ?? null)) {
            Flash::set('Invalid CSRF token.');
            return;
        }

        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            Flash::set('Username and password are required.');
            return;
        }

        $result = Auth::beginLogin($username, $password);

        if ($result === 'authenticated') {
            Flash::set('Login successful.');
            $this->redirect($this->successRedirect);
        }

        if ($result === 'totp_required') {
            Flash::set('Enter your 2FA code to continue.');
            $this->redirect($this->totpRedirect);
        }

        Flash::set('Invalid login credentials.');
    }
}
