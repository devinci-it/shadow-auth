<?php

declare(strict_types=1);

namespace DevinciIT\ShadowAuth\Processors;

use DevinciIT\ShadowAuth\Core\Flash;
use DevinciIT\ShadowAuth\Facade\Auth;
use DevinciIT\ShadowAuth\Utils\CSRF;

/**
 * Handles reset password form submission for token-based password changes.
 */
final class ResetPasswordProcessor extends BaseProcessor
{
    public function __construct(private readonly string $loginRedirect)
    {
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

        $token = trim((string) ($_POST['reset_token'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

        if ($token === '') {
            Flash::set('Reset token is required.');
            return;
        }

        if ($password === '' || $confirmPassword === '') {
            Flash::set('New password and confirmation are required.');
            return;
        }

        if ($password !== $confirmPassword) {
            Flash::set('Password confirmation does not match.');
            return;
        }

        if (strlen($password) < 8) {
            Flash::set('Password must be at least 8 characters.');
            return;
        }

        if (!Auth::resetPasswordWithToken($token, $password)) {
            Flash::set('Invalid or expired reset token.');
            return;
        }

        Flash::set('Password reset successful. You can login now.');
        $this->redirect($this->loginRedirect);
    }
}
